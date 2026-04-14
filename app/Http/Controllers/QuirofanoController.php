<?php

namespace App\Http\Controllers;

use App\Models\CitaQuirurgica;
use App\Models\TipoCirugia;
use App\Models\Paciente;
use App\Models\Medico;
use App\Models\Quirofano;
use App\Models\Caja;
use App\Models\AlmacenMedicamento;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class QuirofanoController extends Controller
{
    public function index(): View
    {
        $quirofanos = Quirofano::all();

        // Obtener la fecha actual y el rango de la semana
        $startOfWeek = now()->startOfWeek()->startOfDay();
        $endOfWeek = now()->endOfWeek()->endOfDay();

        \Log::info('Date range', [
            'start' => $startOfWeek->format('Y-m-d H:i:s'),
            'end' => $endOfWeek->format('Y-m-d H:i:s'),
            'today' => now()->format('Y-m-d H:i:s')
        ]);

        // Obtener todas las citas de la semana
        $citasSemana = CitaQuirurgica::with(['paciente', 'cirujano.user', 'quirofano'])
            ->whereBetween('fecha', [$startOfWeek, $endOfWeek])
            ->orderBy('fecha')
            ->orderBy('hora_inicio_estimada')
            ->get();

        // Obtener emergencias que están actualmente en quirófano
        $emergenciasEnQuirofano = \App\Models\Emergency::with(['paciente'])
            ->where('ubicacion_actual', 'cirugia')
            ->whereIn('status', ['cirugia', 'en_evaluacion', 'estabilizado'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($emg) {
                return [
                    'id' => $emg->id,
                    'code' => $emg->code,
                    'nro_cirugia' => $emg->nro_cirugia,
                    'paciente_nombre' => $emg->is_temp_id ? 'Paciente Temporal' : ($emg->paciente?->nombre ?? 'Desconocido'),
                    'paciente_ci' => $emg->patient_id,
                    'status' => $emg->status,
                    'status_label' => $emg->status === 'cirugia' ? 'En Cirugía' : $emg->status,
                    'hora_ingreso' => $emg->admission_date?->format('H:i') ?? $emg->created_at->format('H:i'),
                    'tipo_ingreso' => $emg->tipo_ingreso_label,
                    'is_emergency' => true,
                ];
            });

        \Log::info('Citas loaded', ['count' => $citasSemana->count()]);
        \Log::info('Emergencias en quirofano', ['count' => $emergenciasEnQuirofano->count()]);

        // Agrupar citas por día y hora
        $citasPorDiaHora = [];
        foreach ($citasSemana as $cita) {
            $dia = $cita->fecha->format('Y-m-d');
            // Handle time safely - it could be null, Carbon, or string
            $horaStr = $cita->hora_inicio_estimada;
            if ($horaStr instanceof \Carbon\Carbon) {
                $hora = $horaStr->format('H:00');
            } elseif (is_string($horaStr)) {
                $hora = substr($horaStr, 0, 2) . ':00';
            } else {
                $hora = '00:00';
            }
            $citasPorDiaHora[$dia][$hora][$cita->quirofano_id][] = $cita;
        }

        // Generar los días de la semana
        $diasSemana = [];
        for ($date = $startOfWeek; $date <= $endOfWeek; $date->addDay()) {
            $diasSemana[] = [
                'fecha' => $date->copy(),
                'nombre' => $date->locale('es')->dayName,
                'dia_mes' => $date->format('d/m'),
                'fecha_key' => $date->format('Y-m-d')
            ];
        }

        // Generar horas del día (6:00 - 22:00)
        $horasDia = [];
        for ($hora = 6; $hora <= 22; $hora++) {
            $horasDia[] = sprintf('%02d:00', $hora);
        }

        // Estadísticas
        $stats = [
            'total_semana' => $citasSemana->count(),
            'hoy' => CitaQuirurgica::whereDate('fecha', today())->count(),
            'en_curso' => CitaQuirurgica::where('estado', 'en_curso')->count(),
            'finalizadas' => CitaQuirurgica::whereDate('fecha', today())->where('estado', 'finalizada')->count(),
            'emergencias' => $emergenciasEnQuirofano->count(),
        ];

        return view('quirofano.index', compact('quirofanos', 'diasSemana', 'horasDia', 'citasPorDiaHora', 'stats', 'emergenciasEnQuirofano'));
    }

    public function create(): View
    {
        return view('quirofano.cita-create');
    }

    public function getQuirofanosDisponibles(): JsonResponse
    {
        try {
            // Mostrar todos los quirófanos excepto los en mantenimiento
            $quirofanos = Quirofano::where('estado', '!=', 'mantenimiento')->get();
            return response()->json([
                'success' => true,
                'quirofanos' => $quirofanos
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar quirófanos: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getPaciente($ci): JsonResponse
    {
        try {
            $paciente = Paciente::find($ci);
            if (!$paciente) {
                return response()->json([
                    'success' => false,
                    'message' => 'Paciente no encontrado'
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'paciente' => $paciente
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al buscar paciente: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getMedico($ci): JsonResponse
    {
        try {
            $medico = Medico::with('user')->find($ci);
            if (!$medico) {
                return response()->json([
                    'success' => false,
                    'message' => 'Médico no encontrado'
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'medico' => $medico
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al buscar médico: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            // Log the incoming request data for debugging
            \Log::info('Store request data:', $request->all());
            
            // Validación básica primero
            $validated = $request->validate([
                'ci_paciente' => 'required|exists:pacientes,ci',
                'ci_cirujano' => 'required|exists:medicos,ci',
                'nro_quirofano' => 'required|exists:quirofanos,id',
                'tipo_cirugia' => 'required|in:menor,mediana,mayor,ambulatoria',
                'fecha' => 'required|date|after_or_equal:today',
                'hora_inicio_estimada' => 'required|date_format:H:i',
                'costo_base' => 'required|numeric|min:0',
            ]);

            // Crear cita sin validación de disponibilidad por ahora
            $cita = new CitaQuirurgica();
            $cita->ci_paciente = $validated['ci_paciente'];
            $cita->ci_cirujano = $validated['ci_cirujano'];
            $cita->quirofano_id = $validated['nro_quirofano'];
            $cita->tipo_cirugia = $validated['tipo_cirugia'];
            $cita->fecha = $validated['fecha'];
            $cita->hora_inicio_estimada = $validated['hora_inicio_estimada'];
            
            // Campos opcionales
            $cita->nombre_instrumentista = $request->input('nombre_instrumentista');
            $cita->nombre_anestesiologo = $request->input('nombre_anestesiologo');
            $cita->descripcion_cirugia = $request->input('descripcion_cirugia');
            $cita->observaciones = $request->input('observaciones');
            
            // Establecer valores por defecto
            $cita->estado = 'programada';
            $cita->user_registro_id = auth()->id();

            // Usar el precio ingresado por el administrador
            $cita->costo_base = $validated['costo_base'];

            // Validar disponibilidad del quirófano (temporalmente desactivada para pruebas)
            /*
            try {
                if ($cita->validarDisponibilidadQuirofano()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'El quirófano no está disponible en este horario. Ya existe una cirugía programada.'
                    ], 422);
                }
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al validar disponibilidad: ' . $e->getMessage()
                ], 500);
            }
            */

            // Guardar cita y crear registro en caja dentro de una transacción
            DB::beginTransaction();
            
            try {
                $cita->save();
                
                // Crear registro en caja inmediatamente
                $cuentaCobro = $this->crearRegistroCajaCirugia($cita);
                
                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Cita quirúrgica programada exitosamente. Se generó la cuenta de cobro en caja.',
                    'cita' => $cita->fresh(),
                    'cuenta_cobro_id' => $cuentaCobro->id
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                \Log::error('Error al crear cita o registro en caja: ' . $e->getMessage());
                throw $e;
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Log detailed validation errors
            \Log::error('Validation failed:', [
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);
            
            // Also return the errors in a more visible way for debugging
            return response()->json([
                'success' => false,
                'message' => 'Error de validación - ' . implode(', ', array_keys($e->errors())),
                'errors' => $e->errors(),
                'debug' => 'Failed fields: ' . implode(', ', array_keys($e->errors()))
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mostrar formulario para programar cirugía desde emergencia
     */
    public function programarEmergencia(int $emergency_id): View
    {
        $emergencia = \App\Models\Emergency::with(['paciente'])->findOrFail($emergency_id);

        // Verificar que la emergencia esté en quirófano
        if ($emergencia->ubicacion_actual !== 'cirugia') {
            abort(403, 'La emergencia no está en quirófano.');
        }

        $quirofanos = Quirofano::where('estado', '!=', 'mantenimiento')->get();
        $tiposCirugia = TipoCirugia::all();
        $medicos = Medico::with('user')->get();

        return view('quirofano.programar-emergencia', compact('emergencia', 'quirofanos', 'tiposCirugia', 'medicos'));
    }

    /**
     * Guardar cirugía programada desde emergencia
     */
    public function storeEmergencia(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'emergency_id' => 'required|exists:emergencies,id',
                'nro_quirofano' => 'required|exists:quirofanos,id',
                'ci_cirujano' => 'required|exists:medicos,ci',
                'ci_instrumentista' => 'nullable|exists:medicos,ci',
                'ci_anestesiologo' => 'nullable|exists:medicos,ci',
                'tipo_cirugia' => 'required|in:menor,mediana,mayor,ambulatoria',
                'fecha' => 'required|date|after_or_equal:today',
                'hora_inicio_estimada' => 'required|date_format:H:i',
                'descripcion_cirugia' => 'nullable|string|max:500',
            ]);

            $emergencia = \App\Models\Emergency::findOrFail($validated['emergency_id']);

            // Crear cita quirúrgica
            $cita = new CitaQuirurgica();
            // Para pacientes temporales, usar el ID de emergencia como identificador numérico
            $cita->ci_paciente = $emergencia->is_temp_id 
                ? (int) $emergencia->id  // Usar ID de emergencia como identificador numérico
                : (int) $emergencia->patient_id;
            $cita->ci_cirujano = $validated['ci_cirujano'];
            $cita->ci_instrumentista = $validated['ci_instrumentista'];
            $cita->ci_anestesiologo = $validated['ci_anestesiologo'];
            $cita->quirofano_id = $validated['nro_quirofano'];
            $cita->tipo_cirugia = $validated['tipo_cirugia'];
            $cita->fecha = $validated['fecha'];
            $cita->hora_inicio_estimada = $validated['hora_inicio_estimada'];
            $cita->descripcion_cirugia = $validated['descripcion_cirugia'] ?? 'Cirugía derivada desde emergencia ' . $emergencia->code;
            $cita->estado = 'programada';
            $cita->user_registro_id = auth()->id();

            // Calcular costo base según tipo
            $tipoCirugia = TipoCirugia::where('nombre', $validated['tipo_cirugia'])->first();
            if ($tipoCirugia) {
                $cita->costo_base = $tipoCirugia->costo_base;
            }

            // Validar disponibilidad
            if ($cita->validarDisponibilidadQuirofano()) {
                return response()->json([
                    'success' => false,
                    'message' => 'El quirófano no está disponible en ese horario'
                ], 422);
            }

            $cita->save();

            // Actualizar emergencia con referencia a la cita
            $emergencia->update([
                'nro_cirugia' => 'CIR-' . $cita->id,
                'status' => 'cirugia_programada'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Cirugía programada exitosamente',
                'cita' => $cita->fresh(),
                'redirect' => route('quirofano.index')
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Iniciar cirugía de emergencia inmediatamente (sin programar)
     */
    public function iniciarEmergencia(int $emergency_id): JsonResponse
    {
        try {
            $emergencia = \App\Models\Emergency::findOrFail($emergency_id);

            if ($emergencia->ubicacion_actual !== 'cirugia') {
                return response()->json([
                    'success' => false,
                    'message' => 'La emergencia no está en quirófano'
                ], 400);
            }

            // Buscar quirófano disponible
            $quirofano = Quirofano::where('estado', 'disponible')->first();
            if (!$quirofano) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay quirófanos disponibles'
                ], 422);
            }

            // Crear cita quirúrgica para ahora
            $cita = new CitaQuirurgica();
            // Para pacientes temporales, usar el ID de emergencia como identificador numérico
            $cita->ci_paciente = $emergencia->is_temp_id 
                ? (int) $emergencia->id  // Usar ID de emergencia como identificador numérico
                : (int) $emergencia->patient_id;
            $cita->ci_cirujano = auth()->user()->medico?->ci ?? null;
            $cita->quirofano_id = $quirofano->id;
            $cita->tipo_cirugia = 'mayor';
            $cita->fecha = now()->toDateString();
            $cita->hora_inicio_estimada = now()->format('H:i');
            $cita->descripcion_cirugia = 'Cirugía de emergencia - ' . $emergencia->code;
            $cita->estado = 'en_curso';
            $cita->timestamp_inicio = now();
            $cita->user_registro_id = auth()->id();
            $cita->save();

            // Marcar quirófano como ocupado
            $quirofano->update(['estado' => 'ocupado']);

            // Actualizar emergencia
            $emergencia->update([
                'nro_cirugia' => 'CIR-URG-' . $cita->id,
                'status' => 'cirugia_en_curso'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Cirugía iniciada en ' . $quirofano->nombre,
                'cita_id' => $cita->id,
                'redirect' => route('quirofano.show', $cita)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(CitaQuirurgica $cita): View
    {
        $cita->load([
            'paciente.seguro',
            'cirujano.user',
            'instrumentista.user',
            'anestesiologo.user',
            'quirofano',
            'usuarioRegistro'
        ]);

        return view('quirofano.show', compact('cita'));
    }

    public function edit(CitaQuirurgica $cita): View
    {
        if ($cita->estado === 'en_curso' || $cita->estado === 'finalizada') {
            abort(403, 'No se puede modificar una cirugía en curso o finalizada.');
        }

        $pacientes = Paciente::all();
        $medicos = Medico::with('user')->get();
        $quirofanos = Quirofano::where('estado', 'disponible')->get();
        $tiposCirugia = TipoCirugia::activos()->get();

        return view('quirofano.edit', compact('cita', 'pacientes', 'medicos', 'quirofanos', 'tiposCirugia'));
    }

    public function update(Request $request, CitaQuirurgica $cita): JsonResponse
    {
        if ($cita->estado === 'en_curso' || $cita->estado === 'finalizada') {
            return response()->json([
                'success' => false,
                'message' => 'No se puede modificar una cirugía en curso o finalizada.'
            ], 422);
        }

        $request->validate([
            'ci_cirujano' => 'required|exists:medicos,ci',
            'ci_instrumentista' => 'nullable|exists:medicos,ci',
            'ci_anestesiologo' => 'nullable|exists:medicos,ci',
            'nro_quirofano' => 'required|exists:quirofanos,id',
            'tipo_cirugia' => 'required|in:menor,mediana,mayor,ambulatoria',
            'fecha' => 'required|date|after_or_equal:today',
            'hora_inicio_estimada' => 'required|date_format:H:i',
            'descripcion_cirugia' => 'nullable|string|max:500',
            'observaciones' => 'nullable|string|max:1000',
        ]);

        // Validar disponibilidad del quirófano
        $cita->fill($request->except(['user_registro_id']));
        try {
            if ($cita->validarDisponibilidadQuirofano()) {
                return response()->json([
                    'success' => false,
                    'message' => 'El quirófano no está disponible en este horario. Ya existe una cirugía programada.'
                ], 422);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al validar disponibilidad: ' . $e->getMessage()
            ], 500);
        }

        $cita->update($request->except(['user_registro_id']));

        return response()->json([
            'success' => true,
            'message' => 'Cita quirúrgica actualizada exitosamente.'
        ]);
    }

    public function iniciarCirugia(CitaQuirurgica $cita): JsonResponse
    {
        if ($cita->estado !== 'programada') {
            return response()->json([
                'success' => false,
                'message' => 'Solo se pueden iniciar cirugías programadas.'
            ], 422);
        }

        $cita->iniciarCirugia();

        return response()->json([
            'success' => true,
            'message' => 'Cirugía iniciada exitosamente.',
            'timestamp_inicio' => $cita->timestamp_inicio
        ]);
    }

    public function finalizarCirugia(CitaQuirurgica $cita): JsonResponse
    {
        if ($cita->estado !== 'en_curso') {
            return response()->json([
                'success' => false,
                'message' => 'Solo se pueden finalizar cirugías en curso.'
            ], 422);
        }

        try {
            DB::beginTransaction();
            
            // Finalizar la cirugía
            $cita->finalizarCirugia();
            
            // Crear registro automático en caja
            $this->crearRegistroCajaCirugia($cita);
            
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Cirugía finalizada exitosamente. Se ha generado el cobro automático en caja.',
                'cita' => $cita->fresh(),
                'cobro_generado' => true
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al finalizar cirugía y generar cobro: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error al finalizar cirugía: ' . $e->getMessage()
            ], 500);
        }
    }

    private function crearRegistroCajaCirugia(CitaQuirurgica $cita)
    {
        // Usar costo_base ya que costo_final solo se calcula al finalizar la cirugía
        $monto = $cita->costo_base ?? 0;
        
        \Log::info('Verificando cuenta de cobro para cirugía', [
            'cita_id' => $cita->id,
            'monto' => $monto,
            'paciente' => $cita->ci_paciente
        ]);
        
        try {
            // Buscar si ya existe una cuenta de cobro para esta cirugía
            $cuentaCobro = \App\Models\CuentaCobro::where('referencia_id', $cita->id)
                ->where(function($q) {
                    $q->where('referencia_type', CitaQuirurgica::class)
                      ->orWhere('referencia_type', 'like', '%CitaQuirurgica%');
                })
                ->where('estado', 'pendiente')
                ->first();
            
            // Si ya existe, no crear duplicado - solo retornar la existente
            if ($cuentaCobro) {
                \Log::info('Cuenta de cobro ya existe, usando existente', [
                    'cita_id' => $cita->id,
                    'cuenta_cobro_id' => $cuentaCobro->id
                ]);
                return $cuentaCobro;
            }
            
            // Crear cuenta de cobro para que aparezca en caja operativa
            $cuentaCobro = \App\Models\CuentaCobro::create([
                'paciente_ci' => $cita->ci_paciente,
                'tipo_atencion' => 'CIRUGIA',
                'referencia_id' => $cita->id,
                'referencia_type' => CitaQuirurgica::class,
                'estado' => 'pendiente',
                'total_calculado' => $monto,
                'total_pagado' => 0,
                'es_emergencia' => false,
                'es_post_pago' => true,
                'observaciones' => 'Cirugía programada - Quirófano: ' . $cita->quirofano_id,
            ]);

            // Crear detalle de la cuenta de cobro
            \App\Models\CuentaCobroDetalle::create([
                'cuenta_cobro_id' => $cuentaCobro->id,
                'tipo_item' => 'procedimiento',
                'descripcion' => 'Cirugía ' . $cita->tipo_cirugia . ' - ' . ($cita->descripcion_cirugia ?? 'Sin descripción'),
                'cantidad' => 1,
                'precio_unitario' => $monto,
                'subtotal' => $monto,
            ]);

            // Relacionar la cita con la cuenta de cobro
            $cita->observaciones = ($cita->observaciones ?? '') . ' | Cuenta Cobro: ' . $cuentaCobro->id;
            $cita->save();
            
            \Log::info('Cuenta de cobro creada exitosamente', [
                'cita_id' => $cita->id,
                'cuenta_cobro_id' => $cuentaCobro->id,
                'monto' => $monto
            ]);
            
            return $cuentaCobro;
        } catch (\Exception $e) {
            \Log::error('Error al crear cuenta de cobro: ' . $e->getMessage(), [
                'cita_id' => $cita->id,
                'monto' => $monto,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function cancelar(Request $request, CitaQuirurgica $cita): JsonResponse
    {
        if ($cita->estado === 'finalizada') {
            return response()->json([
                'success' => false,
                'message' => 'No se puede cancelar una cirugía finalizada.'
            ], 422);
        }

        $request->validate([
            'motivo_cancelacion' => 'required|string|max:500'
        ]);

        $cita->estado = 'cancelada';
        $cita->motivo_cancelacion = $request->motivo_cancelacion;
        $cita->save();

        return response()->json([
            'success' => true,
            'message' => 'Cita quirúrgica cancelada exitosamente.'
        ]);
    }

    public function disponibilidad(Request $request): JsonResponse
    {
        $request->validate([
            'nro_quirofano' => 'required|exists:quirofanos,id',
            'fecha' => 'required|date',
            'hora_inicio' => 'required|date_format:H:i',
            'tipo_cirugia' => 'required|in:menor,mediana,mayor,ambulatoria'
        ]);

        try {
            $tipoCirugia = TipoCirugia::where('nombre', $request->tipo_cirugia)->first();
            $horaInicio = Carbon::createFromFormat('H:i', $request->hora_inicio);
            $horaFin = $horaInicio->copy()->addMinutes($tipoCirugia->duracion_minutos);

            // Buscar citas existentes
            $citasExistentes = CitaQuirurgica::where('quirofano_id', $request->nro_quirofano)
                ->where('fecha', $request->fecha)
                ->where('estado', '!=', 'cancelada')
                ->get();

            $conflictos = [];
            foreach ($citasExistentes as $cita) {
                $citaInicio = Carbon::createFromFormat('H:i:s', $cita->hora_inicio_estimada);
                $citaFin = $citaInicio->copy()->addMinutes($cita->duracion_estimada);

                if ($horaInicio < $citaFin && $horaFin > $citaInicio) {
                    $conflictos[] = [
                        'paciente' => $cita->paciente->nombre ?? 'N/A',
                        'inicio' => $citaInicio->format('H:i'),
                        'fin' => $citaFin->format('H:i'),
                        'estado' => $cita->estado
                    ];
                }
            }

            return response()->json([
                'disponible' => empty($conflictos),
                'conflictos' => $conflictos,
                'hora_fin_estimada' => $horaFin->format('H:i'),
                'debug_info' => [
                    'quirofano_id' => $request->nro_quirofano,
                    'fecha' => $request->fecha,
                    'hora_inicio' => $request->hora_inicio,
                    'hora_fin' => $horaFin->format('H:i'),
                    'tipo_cirugia' => $request->tipo_cirugia,
                    'duracion' => $tipoCirugia->duracion_minutos,
                    'citas_existentes_count' => $citasExistentes->count()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    public function calendario(Request $request): JsonResponse
    {
        $request->validate([
            'quirofano' => 'nullable|exists:quirofanos,id',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio'
        ]);

        $query = CitaQuirurgica::with(['paciente', 'cirujano.user', 'quirofano'])
            ->whereBetween('fecha', [$request->fecha_inicio, $request->fecha_fin]);

        if ($request->quirofano) {
            $query->where('quirofano_id', $request->quirofano);
        }

        $citas = $query->get()->map(function($cita) {
            return [
                'id' => $cita->id,
                'title' => $cita->paciente->nombre . ' - ' . $cita->tipo_cirugia,
                'start' => $cita->fecha->format('Y-m-d') . ' ' . $cita->hora_inicio_estimada,
                'end' => $cita->fecha->format('Y-m-d') . ' ' . $cita->hora_fin_estimada,
                'backgroundColor' => $this->getColorPorEstado($cita->estado),
                'borderColor' => $this->getColorPorEstado($cita->estado),
                'extendedProps' => [
                    'paciente' => $cita->paciente->nombre,
                    'cirujano' => $cita->cirujano->user->name,
                    'quirofano' => $cita->quirofano->id,
                    'tipo_cirugia' => $cita->tipo_cirugia,
                    'estado' => $cita->estado,
                    'duracion_real' => $cita->duracion_real,
                    'costo_final' => $cita->costo_final
                ]
            ];
        });

        return response()->json($citas);
    }

    
    private function getColorPorEstado($estado): string
    {
        $colores = [
            'programada' => '#3b82f6', // blue
            'en_curso' => '#f59e0b', // amber
            'finalizada' => '#10b981', // emerald
            'cancelada' => '#ef4444', // red
        ];

        return $colores[$estado] ?? '#6b7280'; // gray
    }

    public function historial(): View
    {
        $citas = CitaQuirurgica::with(['paciente', 'cirujano.user', 'quirofano'])
            ->orderBy('fecha', 'desc')
            ->orderBy('hora_inicio_estimada', 'desc')
            ->paginate(20);

        $stats = [
            'total' => CitaQuirurgica::count(),
            'programadas' => CitaQuirurgica::where('estado', 'programada')->count(),
            'en_curso' => CitaQuirurgica::where('estado', 'en_curso')->count(),
            'finalizadas' => CitaQuirurgica::where('estado', 'finalizada')->count(),
            'canceladas' => CitaQuirurgica::where('estado', 'cancelada')->count(),
        ];

        return view('quirofano.historial', compact('citas', 'stats'));
    }

    // Métodos API para búsqueda en formulario de quirófano
    public function getListaPacientes(): JsonResponse
    {
        try {
            $pacientes = Paciente::select('ci', 'nombre', 'telefono')
                ->orderBy('nombre')
                ->get();

            return response()->json([
                'success' => true,
                'pacientes' => $pacientes
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar pacientes: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getListaMedicos(): JsonResponse
    {
        try {
            $medicos = Medico::with('user', 'especialidad')
                ->whereHas('user', function($q) {
                    $q->where('role', 'doctor');
                })
                ->get()
                ->map(function($medico) {
                    return [
                        'ci' => $medico->ci,
                        'nombre' => $medico->user->name ?? 'Sin nombre',
                        'especialidad' => $medico->especialidad->nombre ?? 'Sin especialidad'
                    ];
                });

            return response()->json([
                'success' => true,
                'medicos' => $medicos
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar médicos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener medicamentos disponibles en quirófano para una cirugía
     */
    public function getMedicamentosDisponibles(CitaQuirurgica $cita): JsonResponse
    {
        try {
            \Log::info('getMedicamentosDisponibles llamado', ['cita_id' => $cita->id, 'estado' => $cita->estado]);

            // Verificar que la cirugía esté programada o en curso
            if (!in_array($cita->estado, ['programada', 'en_curso'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'La cirugía debe estar programada o en curso'
                ], 422);
            }

            \Log::info('Buscando medicamentos de cirugia...');

            $query = AlmacenMedicamento::porArea('cirugia')
                ->activos()
                ->where('cantidad', '>', 0)
                ->orderBy('nombre');

            \Log::info('Query SQL: ' . $query->toSql());

            $medicamentos = $query->get(['id', 'nombre', 'tipo', 'cantidad', 'unidad_medida', 'precio']);

            \Log::info('Medicamentos encontrados: ' . $medicamentos->count());

            return response()->json([
                'success' => true,
                'medicamentos' => $medicamentos
            ]);
        } catch (\Exception $e) {
            \Log::error('Error en getMedicamentosDisponibles: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar medicamentos: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    /**
     * Obtener medicamentos ya usados en una cirugía
     */
    public function getMedicamentosUsados(CitaQuirurgica $cita): JsonResponse
    {
        try {
            // Buscar la cuenta de cobro relacionada a esta cita
            $cuentaCobro = \App\Models\CuentaCobro::where('referencia_type', CitaQuirurgica::class)
                ->where('referencia_id', $cita->id)
                ->first();

            if (!$cuentaCobro) {
                return response()->json([
                    'success' => true,
                    'medicamentos' => []
                ]);
            }

            // Obtener detalles de tipo medicamento
            $medicamentos = $cuentaCobro->detalles()
                ->where('tipo_item', 'medicamento')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'medicamentos' => $medicamentos
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar medicamentos usados: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Agregar medicamento a una cirugía en curso
     */
    public function agregarMedicamento(Request $request, CitaQuirurgica $cita): JsonResponse
    {
        try {
            // Verificar que la cirugía esté programada o en curso
            if (!in_array($cita->estado, ['programada', 'en_curso'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'La cirugía debe estar programada o en curso'
                ], 422);
            }

            // Validar input
            $validated = $request->validate([
                'almacen_medicamento_id' => 'required|exists:almacen_medicamentos,id',
                'cantidad' => 'required|integer|min:1'
            ]);

            $medicamento = AlmacenMedicamento::findOrFail($validated['almacen_medicamento_id']);

            // Verificar que el medicamento sea de cirugía
            if ($medicamento->area !== 'cirugia') {
                return response()->json([
                    'success' => false,
                    'message' => 'El medicamento no pertenece al inventario de quirófano'
                ], 422);
            }

            // Verificar stock suficiente
            if ($medicamento->cantidad < $validated['cantidad']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stock insuficiente. Disponible: ' . $medicamento->cantidad . ' ' . $medicamento->unidad_medida
                ], 422);
            }

            DB::beginTransaction();

            try {
                // Descontar stock
                $cantidadAnterior = $medicamento->cantidad;
                $medicamento->cantidad -= $validated['cantidad'];
                $medicamento->save();

                // Buscar cuenta de cobro existente - método más flexible
                $refType = 'App\Models\CitaQuirurgica';
                \Log::info('Buscando cuenta de cobro', [
                    'referencia_type' => $refType,
                    'referencia_id' => $cita->id,
                    'paciente_ci' => $cita->ci_paciente
                ]);
                
                $cuentaCobro = \App\Models\CuentaCobro::where('referencia_id', $cita->id)
                    ->where(function($q) use ($refType) {
                        $q->where('referencia_type', $refType)
                          ->orWhere('referencia_type', 'like', '%CitaQuirurgica%');
                    })
                    ->where('estado', 'pendiente')
                    ->first();
                
                // Si no existe por referencia, buscar por paciente y tipo
                if (!$cuentaCobro) {
                    $cuentaCobro = \App\Models\CuentaCobro::where('paciente_ci', $cita->ci_paciente)
                        ->where('tipo_atencion', 'CIRUGIA')
                        ->where('estado', 'pendiente')
                        ->orderBy('created_at', 'desc')
                        ->first();
                    
                    if ($cuentaCobro) {
                        \Log::info('Cuenta encontrada por paciente/tipo', [
                            'cuenta_cobro_id' => $cuentaCobro->id
                        ]);
                        // Actualizar la referencia para futuras búsquedas
                        $cuentaCobro->referencia_id = $cita->id;
                        $cuentaCobro->referencia_type = $refType;
                        $cuentaCobro->save();
                    }
                } else {
                    \Log::info('Cuenta encontrada por referencia', [
                        'cuenta_cobro_id' => $cuentaCobro->id
                    ]);
                }
                
                // Si aún no existe, crear nueva (no debería pasar si la cita fue creada correctamente)
                if (!$cuentaCobro) {
                    \Log::error('No se encontró cuenta de cobro existente para agregar medicamento', [
                        'cita_id' => $cita->id,
                        'paciente_ci' => $cita->ci_paciente
                    ]);
                    return response()->json([
                        'success' => false,
                        'message' => 'No se encontró la cuenta de cobro de la cirugía. Por favor contacte al administrador.'
                    ], 422);
                }

                $precioUnitario = $medicamento->precio ?? 0;
                $subtotal = $precioUnitario * $validated['cantidad'];

                // Crear detalle en cuenta de cobro
                $detalle = $cuentaCobro->detalles()->create([
                    'tipo_item' => 'medicamento',
                    'descripcion' => $medicamento->nombre . ' (' . $medicamento->tipo . ')',
                    'cantidad' => $validated['cantidad'],
                    'precio_unitario' => $precioUnitario,
                    'subtotal' => $subtotal,
                ]);

                // Actualizar total de cuenta de cobro
                $cuentaCobro->total_calculado = $cuentaCobro->detalles()->sum('subtotal');
                $cuentaCobro->save();

                // Registrar en ActivityLog para historial
                \App\Models\ActivityLog::create([
                    'user_id' => auth()->id(),
                    'action' => 'medicamento_cirugia_agregado',
                    'model_type' => CitaQuirurgica::class,
                    'model_id' => $cita->id,
                    'description' => 'Medicamento agregado a cirugía: ' . $medicamento->nombre,
                    'new_values' => json_encode([
                        'almacen_medicamento_id' => $medicamento->id,
                        'nombre' => $medicamento->nombre,
                        'cantidad' => $validated['cantidad'],
                        'precio_unitario' => $precioUnitario,
                        'subtotal' => $subtotal,
                        'stock_anterior' => $cantidadAnterior,
                        'stock_nuevo' => $medicamento->cantidad,
                        'cuenta_cobro_id' => $cuentaCobro->id,
                        'detalle_id' => $detalle->id
                    ]),
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent()
                ]);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Medicamento agregado exitosamente',
                    'medicamento' => [
                        'nombre' => $medicamento->nombre,
                        'cantidad' => $validated['cantidad'],
                        'precio_unitario' => $precioUnitario,
                        'subtotal' => $subtotal
                    ],
                    'stock_restante' => $medicamento->cantidad
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al agregar medicamento: ' . $e->getMessage()
            ], 500);
        }
    }
}
