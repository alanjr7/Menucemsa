<?php

namespace App\Http\Controllers;

use App\Models\CitaQuirurgica;
use App\Models\TipoCirugia;
use App\Models\Paciente;
use App\Models\Medico;
use App\Models\Quirofano;
use App\Models\Caja;
use App\Models\AlmacenCatalogo;
use App\Models\AlmacenStock;
use App\Models\Registro;
use App\Models\Seguro;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class QuirofanoController extends Controller
{
    use \App\Traits\AuditLoggable;

    public function index(Request $request): View
    {
        \Log::info('Index llamado', [
            'all_params' => $request->all(),
            'fecha_param' => $request->input('fecha'),
            'url' => $request->fullUrl()
        ]);

        $quirofanos = Quirofano::all();

        // Obtener fecha seleccionada o usar hoy
        $fechaSeleccionada = $request->filled('fecha') 
            ? \Carbon\Carbon::parse($request->input('fecha'))
            : now();

        // Obtener la fecha y el rango de la semana
        $startOfWeek = $fechaSeleccionada->copy()->startOfWeek()->startOfDay();
        $endOfWeek = $fechaSeleccionada->copy()->endOfWeek()->endOfDay();

        \Log::info('Date range', [
            'start' => $startOfWeek->format('Y-m-d H:i:s'),
            'end' => $endOfWeek->format('Y-m-d H:i:s'),
            'today' => now()->format('Y-m-d H:i:s'),
            'fecha_seleccionada' => $fechaSeleccionada->format('Y-m-d'),
            'used_request_date' => $request->filled('fecha')
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
                // Determinar origen basado en flujo_historial
                $origen = $emg->tipo_ingreso_label;
                if (!empty($emg->flujo_historial) && is_array($emg->flujo_historial) && count($emg->flujo_historial) > 0) {
                    $primerMovimiento = $emg->flujo_historial[0];
                    $desde = $primerMovimiento['desde'] ?? '';
                    $origen = match($desde) {
                        'internacion' => 'Derivado desde Internación',
                        'recepcion' => 'Ingreso desde Recepción',
                        'emergencia' => 'Derivado desde Emergencia',
                        'uti' => 'Derivado desde UTI',
                        default => $emg->tipo_ingreso_label,
                    };
                }

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
                    'origen_label' => $origen,
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

    public function apiDashboard(Request $request): JsonResponse
    {
        $quirofanos = Quirofano::all();

        // Obtener fecha seleccionada o usar hoy (para auto-refresh respetar fecha)
        $fechaSeleccionada = $request->filled('fecha')
            ? \Carbon\Carbon::parse($request->input('fecha'))
            : now();

        $startOfWeek = $fechaSeleccionada->copy()->startOfWeek()->startOfDay();
        $endOfWeek = $fechaSeleccionada->copy()->endOfWeek()->endOfDay();

        $citasSemana = CitaQuirurgica::with(['paciente', 'cirujano.user', 'quirofano'])
            ->whereBetween('fecha', [$startOfWeek, $endOfWeek])
            ->orderBy('fecha')
            ->orderBy('hora_inicio_estimada')
            ->get();

        $emergenciasEnQuirofano = \App\Models\Emergency::with(['paciente'])
            ->where('ubicacion_actual', 'cirugia')
            ->whereIn('status', ['cirugia', 'en_evaluacion', 'estabilizado'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($emg) {
                // Determinar origen basado en flujo_historial
                $origen = $emg->tipo_ingreso_label;
                if (!empty($emg->flujo_historial) && is_array($emg->flujo_historial) && count($emg->flujo_historial) > 0) {
                    $primerMovimiento = $emg->flujo_historial[0];
                    $desde = $primerMovimiento['desde'] ?? '';
                    $origen = match($desde) {
                        'internacion' => 'Derivado desde Internación',
                        'recepcion' => 'Ingreso desde Recepción',
                        'emergencia' => 'Derivado desde Emergencia',
                        'uti' => 'Derivado desde UTI',
                        default => $emg->tipo_ingreso_label,
                    };
                }

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
                    'origen_label' => $origen,
                    'is_emergency' => true,
                ];
            });

        $citasPorDiaHora = [];
        foreach ($citasSemana as $cita) {
            $dia = $cita->fecha->format('Y-m-d');
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

        $diasSemana = [];
        for ($date = $startOfWeek; $date <= $endOfWeek; $date->addDay()) {
            $diasSemana[] = [
                'fecha' => $date->format('Y-m-d'),
                'nombre' => $date->locale('es')->dayName,
                'dia_mes' => $date->format('d/m'),
                'fecha_key' => $date->format('Y-m-d'),
                'is_today' => $date->isToday()
            ];
        }

        $horasDia = [];
        for ($hora = 6; $hora <= 22; $hora++) {
            $horasDia[] = sprintf('%02d:00', $hora);
        }

        $stats = [
            'total_semana' => $citasSemana->count(),
            'hoy' => CitaQuirurgica::whereDate('fecha', today())->count(),
            'en_curso' => CitaQuirurgica::where('estado', 'en_curso')->count(),
            'finalizadas' => CitaQuirurgica::whereDate('fecha', today())->where('estado', 'finalizada')->count(),
            'emergencias' => $emergenciasEnQuirofano->count(),
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats,
            'emergencias' => $emergenciasEnQuirofano,
            'citasPorDiaHora' => $citasPorDiaHora,
            'diasSemana' => $diasSemana,
            'horasDia' => $horasDia,
            'quirofanos' => $quirofanos
        ]);
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

                // Notificar a cirujano y administración sobre cirugía programada
                $paciente = Paciente::find($cita->ci_paciente);
                $cirujano = Medico::find($cita->ci_cirujano);

                // Registrar en bitácora
                $this->logActivity(
                    'programar_cirugia',
                    'Cirugía programada - Paciente: ' . ($paciente ? $paciente->nombre : 'N/A') .
                    ' - Cirujano: ' . ($cirujano && $cirujano->user ? $cirujano->user->name : 'N/A') .
                    ' - Fecha: ' . $cita->fecha . ' ' . $cita->hora_inicio_estimada,
                    $cita
                );

                NotificationService::notify($cirujano->user_id, 'cirugia', 'Cirugía Programada', "Paciente: {$paciente->nombre} - Fecha: {$cita->fecha} {$cita->hora_inicio_estimada}", route('quirofano.index'), ['cita_id' => $cita->id]);
                NotificationService::notifyAdmins('cirugia', 'Cirugía Programada', "Paciente: {$paciente->nombre} - Cirujano: {$cirujano->user->name}", route('quirofano.index'));

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

        // Obtener quirófanos - todos excepto mantenimiento, ordenados por disponibilidad
        $quirofanos = Quirofano::where('estado', '!=', 'mantenimiento')
            ->orderByRaw("CASE WHEN estado = 'disponible' THEN 0 ELSE 1 END")
            ->orderBy('tipo')
            ->get();
        
        \Log::info('Quirofanos cargados para programar emergencia', [
            'count' => $quirofanos->count(),
            'emergency_id' => $emergency_id
        ]);
        
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
                'nombre_instrumentista' => 'nullable|string|max:255',
                'nombre_anestesiologo' => 'nullable|string|max:255',
                'tipo_cirugia' => 'required|exists:tipos_cirugia,nombre',
                'fecha' => 'required|date|after_or_equal:today',
                'hora_inicio_estimada' => 'required|date_format:H:i',
                'descripcion_cirugia' => 'nullable|string|max:500',
                'costo_base' => 'required|numeric|min:0',
            ]);

            $emergencia = \App\Models\Emergency::findOrFail($validated['emergency_id']);

            // Si es paciente temporal, crear un paciente real primero
            if ($emergencia->is_temp_id) {
                $paciente = $this->crearPacienteDesdeTemporal($emergencia);
                $ciPaciente = $paciente->ci;
            } else {
                $ciPaciente = $emergencia->patient_id;
            }

            // Crear cita quirúrgica
            $cita = new CitaQuirurgica();
            $cita->ci_paciente = (int) $ciPaciente;
            $cita->ci_cirujano = $validated['ci_cirujano'];
            $cita->ci_instrumentista = null;
            $cita->ci_anestesiologo = null;
            $cita->nombre_instrumentista = $validated['nombre_instrumentista'];
            $cita->nombre_anestesiologo = $validated['nombre_anestesiologo'];
            $cita->quirofano_id = $validated['nro_quirofano'];
            $cita->tipo_cirugia = $validated['tipo_cirugia'];
            $cita->fecha = $validated['fecha'];
            $cita->hora_inicio_estimada = $validated['hora_inicio_estimada'];
            $cita->descripcion_cirugia = $validated['descripcion_cirugia'] ?? 'Cirugía derivada desde emergencia ' . $emergencia->code;
            $cita->estado = 'programada';
            $cita->user_registro_id = auth()->id();
            $cita->costo_base = $validated['costo_base'];

            // Validar disponibilidad (usa accessor duracion_estimada que calcula desde tipo_cirugia)
            if ($cita->validarDisponibilidadQuirofano()) {
                return response()->json([
                    'success' => false,
                    'message' => 'El quirófano no está disponible en ese horario'
                ], 422);
            }

            $cita->save();

            // Crear cuenta de cobro para la cirugía (igual que en store regular)
            $cuentaCobro = $this->crearRegistroCajaCirugia($cita);

            // Actualizar emergencia con referencia a la cita
            $emergencia->update([
                'nro_cirugia' => 'CIR-' . $cita->id,
                'status' => 'cirugia'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Cirugía programada exitosamente',
                'cita' => $cita->fresh(),
                'redirect' => route('quirofano.index')
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Error de validación en storeEmergencia', [
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);
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
     * Obtener médicos disponibles para selección en cirugía de emergencia
     */
    public function getMedicosDisponibles(): JsonResponse
    {
        try {
            \Log::info('Cargando médicos disponibles');
            
            // Obtener todos los médicos activos sin cargar relaciones para evitar errores
            $medicosQuery = Medico::where('estado', 'activo')
                ->orderBy('nombre');
            
            \Log::info('SQL: ' . $medicosQuery->toSql());
            
            $medicos = $medicosQuery->get();
            
            \Log::info('Médicos encontrados: ' . $medicos->count());
            
            $resultado = $medicos->map(function($medico) {
                // Obtener nombre del médico o del usuario relacionado
                $nombre = $medico->nombre;
                if (empty($nombre) && $medico->user_id) {
                    $user = \App\Models\User::find($medico->user_id);
                    $nombre = $user?->name ?? 'Sin nombre';
                }
                if (empty($nombre)) {
                    $nombre = 'Médico CI: ' . $medico->ci;
                }
                
                // Obtener especialidad
                $especialidad = 'Sin especialidad';
                if ($medico->codigo_especialidad) {
                    $esp = \App\Models\Especialidad::where('codigo', $medico->codigo_especialidad)->first();
                    $especialidad = $esp?->nombre ?? 'Sin especialidad';
                }
                
                return [
                    'ci' => $medico->ci,
                    'nombre' => $nombre,
                    'especialidad' => $especialidad,
                    'disponible' => true
                ];
            });

            return response()->json([
                'success' => true,
                'medicos' => $resultado
            ]);
        } catch (\Exception $e) {
            \Log::error('Error en getMedicosDisponibles: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar médicos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Iniciar cirugía de emergencia inmediatamente (sin programar)
     */
    public function iniciarEmergencia(Request $request, int $emergency_id): JsonResponse
    {
        try {
            $validated = $request->validate([
                'ci_cirujano' => 'required|exists:medicos,ci',
            ]);

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
            $cita->ci_cirujano = $validated['ci_cirujano'];
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
    /**
 * Mostrar detalles de una cirugía finalizada (solo lectura)
 */
public function showDetails(CitaQuirurgica $cita): View
{
    $cita->load([
        'paciente.seguro',
        'cirujano.user',
        'instrumentista.user',
        'anestesiologo.user',
        'quirofano',
        'usuarioRegistro'
    ]);

    // Buscar la cuenta a partir de los detalles vinculados a esta cita
    $detallePivot = \App\Models\CuentaCobroDetalle::where('origen_type', CitaQuirurgica::class)
        ->where('origen_id', (string) $cita->id)
        ->first();

    $cuentaCobro = $detallePivot?->cuentaCobro;

    // Filtrar detalles de esta cita específica (la cuenta puede ser compartida/maestra)
    $medicamentosUsados = collect();
    $equiposUsados      = collect();

    if ($cuentaCobro) {
        $medicamentosUsados = \App\Models\CuentaCobroDetalle::where('cuenta_cobro_id', $cuentaCobro->id)
            ->where('origen_type', CitaQuirurgica::class)
            ->where('origen_id', (string) $cita->id)
            ->where('tipo_item', 'medicamento')
            ->get();

        $equiposUsados = \App\Models\CuentaCobroDetalle::where('cuenta_cobro_id', $cuentaCobro->id)
            ->where('origen_type', CitaQuirurgica::class)
            ->where('origen_id', (string) $cita->id)
            ->where('tipo_item', 'equipo_medico')
            ->get();
    }

    $tiposCirugia = TipoCirugia::activos()->get();
    $medicamentos = AlmacenCatalogo::where('tipo', 'medicamento')
        ->where('activo', true)
        ->orderBy('nombre')
        ->get();

    return view('quirofano.show-details', compact(
        'cita', 
        'tiposCirugia', 
        'medicamentos',
        'medicamentosUsados',
        'equiposUsados',
        'cuentaCobro'
    ));
}
    public function show(CitaQuirurgica $cita): \Illuminate\View\View|\Illuminate\Http\RedirectResponse
    {
        // Si la cita ya está finalizada o cancelada, redirigir al historial
        if (in_array($cita->estado, ['finalizada', 'cancelada'])) {
            return redirect()->route('quirofano.historial')
                ->with('info', 'Esta cirugía ya ha sido ' . $cita->estado . '. Ver el historial.');
        }

        $cita->load([
            'paciente.seguro',
            'cirujano.user',
            'instrumentista.user',
            'anestesiologo.user',
            'quirofano',
            'usuarioRegistro'
        ]);

        $tiposCirugia = TipoCirugia::activos()->get();
        $medicamentos = AlmacenCatalogo::where('tipo', 'medicamento')
            ->where('activo', true)
            ->orderBy('nombre')
            ->get();

        return view('quirofano.ejecutar', compact('cita', 'tiposCirugia', 'medicamentos'));
    }

    public function ejecutar(Request $request, CitaQuirurgica $cita): JsonResponse
    {
        if ($cita->estado === 'finalizada') {
            return response()->json(['success' => false, 'message' => 'La cirugía ya está finalizada.'], 422);
        }

        $validated = $request->validate([
            'fecha'              => 'required|date',
            'hora_inicio'        => 'required|date_format:H:i',
            'hora_fin'           => 'required|date_format:H:i',
            'tipo_cirugia'       => 'required|exists:tipos_cirugia,nombre',
            'descripcion_cirugia'=> 'nullable|string|max:1000',
            'observaciones'      => 'nullable|string|max:1000',
            'medicamentos'       => 'nullable|array',
            'medicamentos.*.id'  => 'required_with:medicamentos|exists:almacen_catalogo,id',
            'medicamentos.*.cantidad' => 'required_with:medicamentos|integer|min:1',
            'equipos'            => 'nullable|array',
            'equipos.*.nombre'   => 'required_with:equipos|string|max:255',
            'equipos.*.precio'   => 'required_with:equipos|numeric|min:0',
            'equipos.*.cantidad' => 'required_with:equipos|integer|min:1',
        ]);

        DB::beginTransaction();

        try {
            $fechaBase = Carbon::parse($validated['fecha']);
            $tsInicio  = $fechaBase->copy()->setTimeFromTimeString($validated['hora_inicio'] . ':00');
            $tsFin     = $fechaBase->copy()->setTimeFromTimeString($validated['hora_fin']    . ':00');

            if ($tsFin <= $tsInicio) {
                $tsFin->addDay();
            }

            $duracion = (int) $tsInicio->diffInMinutes($tsFin);

            if ($duracion <= 45)      $tipoFinal = 'ambulatoria';
            elseif ($duracion <= 60)  $tipoFinal = 'menor';
            elseif ($duracion <= 90)  $tipoFinal = 'mediana';
            else                      $tipoFinal = 'mayor';

            $tipoCirugia      = TipoCirugia::where('nombre', $tipoFinal)->first();
            $tipoCirugiaBase  = TipoCirugia::where('nombre', $cita->tipo_cirugia)->first();
            $costoBaseStr     = (string) $cita->costo_base;
            $duracionStr      = (string) $duracion;
            // Duración de referencia = tipo originalmente programado (base del cobro)
            $duracionBaseStr  = (string) ($tipoCirugiaBase ? $tipoCirugiaBase->duracion_minutos : $duracion);

            // Regla de 3: costo_total = (costo_base * duracion_real) / duracion_base
            $costoTotalBc = bcdiv(bcmul($costoBaseStr, $duracionStr, 10), $duracionBaseStr, 2);
            $costoExtraBc = bccomp($costoTotalBc, $costoBaseStr, 2) > 0
                ? bcsub($costoTotalBc, $costoBaseStr, 2)
                : '0.00';
            $costoExtra   = (float) $costoExtraBc;
            // Costo por minuto efectivo (base / duracion_base) para auditoría
            $costoMinuto  = bccomp($duracionBaseStr, '0', 0) > 0
                ? (float) bcdiv($costoBaseStr, $duracionBaseStr, 4)
                : 0;

            // Costo final = regla de 3 proporcional a la duración real
            $costoCirugia = (float) $costoTotalBc;

            // Buscar el detalle de cirugía en CUALQUIER cuenta del paciente (no solo la más reciente).
            // Buscar por cuenta scoped causaba doble cargo cuando una nueva cuenta pendiente
            // era creada por otro módulo entre la programación y la ejecución.
            $detalleCirugia = \App\Models\CuentaCobroDetalle::where('tipo_item', 'procedimiento')
                ->where('origen_type', CitaQuirurgica::class)
                ->where('origen_id', (string) $cita->id)
                ->first();

            if ($detalleCirugia) {
                // Reutilizar la cuenta donde vive el detalle y actualizar con el costo real
                $cuenta = $detalleCirugia->cuentaCobro;
                $detalleCirugia->descripcion    = 'Cirugía ' . $tipoFinal . ' - ' . $duracion . ' min (Cita #' . $cita->id . ')';
                $detalleCirugia->precio_unitario = $costoCirugia;
                $detalleCirugia->subtotal        = $costoCirugia;
                $detalleCirugia->save();
            } else {
                // No existe detalle previo (cirugía de emergencia sin pre-programación):
                // obtener/crear cuenta maestra y usar agregarCargoConDeduplicacion para seguridad
                $cuenta = \App\Services\CuentaCobroService::obtenerOCrearCuentaMaestra(
                    $cita->ci_paciente,
                    'quirofano'
                );
                \App\Services\CuentaCobroService::agregarCargoConDeduplicacion(
                    $cuenta->id,
                    'procedimiento',
                    'Cirugía ' . $tipoFinal . ' - ' . $duracion . ' min (Cita #' . $cita->id . ')',
                    $costoCirugia,
                    1,
                    'quirofano',
                    CitaQuirurgica::class,
                    (string) $cita->id
                );
                $cuenta->refresh();
            }

            // Procesar medicamentos recibidos desde el formulario
            $costoMedicamentos = 0;
            $medicamentosUsados = [];
            if (!empty($validated['medicamentos'])) {
                foreach ($validated['medicamentos'] as $med) {
                    $medicamento = AlmacenCatalogo::find($med['id']);
                    if ($medicamento) {
                        // Obtener precio_venta del lote más reciente no vencido
                        $lote = \App\Models\AlmacenLote::where('catalogo_id', $medicamento->id)
                            ->where(function($q) {
                                $q->whereNull('fecha_vencimiento')
                                  ->orWhere('fecha_vencimiento', '>', now());
                            })
                            ->orderByDesc('created_at')
                            ->first();

                        $precioUnitario = (float) ($lote->precio_venta ?? 0);
                        $subtotal = $precioUnitario * $med['cantidad'];
                        $costoMedicamentos += $subtotal;

                        // Descontar del stock de cirugía/quirófano
                        $stock = AlmacenStock::whereHas('lote', function($q) use ($medicamento) {
                                $q->where('catalogo_id', $medicamento->id)
                                  ->where(function($sub) {
                                      $sub->whereNull('fecha_vencimiento')
                                          ->orWhere('fecha_vencimiento', '>', now());
                                  });
                            })
                            ->whereIn('ubicacion', ['cirugia', 'quirofano'])
                            ->where('cantidad_actual', '>=', $med['cantidad'])
                            ->first();
                        
                        if ($stock) {
                            $stock->cantidad_actual -= $med['cantidad'];
                            $stock->save();
                        } else {
                            \Log::warning('No hay stock suficiente para medicamento: ' . $medicamento->nombre . ' - Cantidad solicitada: ' . $med['cantidad']);
                        }

                        // Verificar que no se haya agregado ya desde el módulo de medicamentos en tiempo real
                        $yaAgregado = $cuenta->detalles()
                            ->where('tipo_item', 'medicamento')
                            ->where('origen_type', CitaQuirurgica::class)
                            ->where('origen_id', (string) $cita->id)
                            ->where('descripcion', 'like', '%' . $medicamento->nombre . '%')
                            ->exists();

                        if (!$yaAgregado) {
                            $cuenta->detalles()->create([
                                'tipo_item'       => 'medicamento',
                                'descripcion'     => $medicamento->nombre,
                                'cantidad'        => $med['cantidad'],
                                'precio_unitario' => $precioUnitario,
                                'subtotal'        => $subtotal,
                                'area_origen'     => 'quirofano',
                                'origen_type'     => CitaQuirurgica::class,
                                'origen_id'       => (string) $cita->id,
                            ]);
                        }

                        $medicamentosUsados[] = [
                            'id'             => $medicamento->id,
                            'nombre'         => $medicamento->nombre,
                            'cantidad'       => $med['cantidad'],
                            'precio_unitario'=> $precioUnitario,
                            'subtotal'       => $subtotal,
                        ];
                    }
                }
            }

            // Procesar equipos médicos recibidos desde el formulario
            $costoEquipos = 0;
            $equiposUsados = [];
            if (!empty($validated['equipos'])) {
                foreach ($validated['equipos'] as $equipo) {
                    $subtotal = $equipo['precio'] * $equipo['cantidad'];
                    $costoEquipos += $subtotal;

                    $cuenta->detalles()->create([
                        'tipo_item'       => 'equipo_medico',
                        'descripcion'     => $equipo['nombre'],
                        'cantidad'        => $equipo['cantidad'],
                        'precio_unitario' => $equipo['precio'],
                        'subtotal'        => $subtotal,
                        'area_origen'     => 'quirofano',
                        'origen_type'     => CitaQuirurgica::class,
                        'origen_id'       => (string) $cita->id,
                    ]);

                    $equiposUsados[] = [
                        'nombre'         => $equipo['nombre'],
                        'cantidad'       => $equipo['cantidad'],
                        'precio_unitario'=> $equipo['precio'],
                        'subtotal'       => $subtotal,
                    ];
                }
            }

            // Recalcular total de la cuenta desde la suma real de sus detalles
            $costoTotal = $costoCirugia + $costoMedicamentos + $costoEquipos;
            $cuenta->total_calculado = $cuenta->detalles()->sum('subtotal');
            $cuenta->save();

            // Actualizar cita quirúrgica
            $cita->tipo_cirugia       = $validated['tipo_cirugia'];
            $cita->descripcion_cirugia= $validated['descripcion_cirugia'];
            $cita->observaciones      = $validated['observaciones'];
            $cita->fecha              = $validated['fecha'];
            $cita->hora_inicio_real   = $validated['hora_inicio'];
            $cita->hora_fin_real      = $validated['hora_fin'];
            $cita->timestamp_inicio   = $tsInicio;
            $cita->timestamp_fin      = $tsFin;
            $cita->estado             = 'finalizada';
            $cita->tipo_final         = $tipoFinal;
            $cita->costo_final        = $costoTotal;
            $cita->costo_minuto_extra = $costoMinuto;
            $cita->save();

            if ($cita->quirofano_id) {
                Quirofano::where('id', $cita->quirofano_id)->update(['estado' => 'disponible']);
            }

            // La cuenta maestra ya existe (se usó arriba)
            \App\Services\CuentaCobroService::obtenerOCrearCuentaMaestra($cita->ci_paciente, 'quirofano');

            DB::commit();

            $this->logActivity(
                'registrar_cirugia',
                'Cirugía registrada - Paciente: ' . ($cita->paciente?->nombre ?? 'N/A') .
                ' - Tipo: ' . $tipoFinal . ' - Duración: ' . $duracion . ' min',
                $cita
            );

            return response()->json([
                'success'  => true,
                'message'  => 'Cirugía registrada exitosamente.',
                'redirect' => route('quirofano.historial')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al registrar cirugía: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
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

        // Registrar en bitácora
        $this->logActivity(
            'iniciar_cirugia',
            'Cirugía iniciada - Paciente: ' . ($cita->paciente ? $cita->paciente->nombre : 'N/A') .
            ' - Tipo: ' . $cita->tipo_cirugia,
            $cita
        );

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

            // Liberar el quirófano
            if ($cita->quirofano_id) {
                Quirofano::where('id', $cita->quirofano_id)
                    ->update(['estado' => 'disponible']);
            }

            // NO crear cuenta aquí: ya fue creada al programar la cirugía.
            // Solo verificar si existe y actualizar el total si tiene detalles adicionales.
            $cuentaCobro = \App\Models\CuentaCobro::where('referencia_id', $cita->id)
                ->where(function($q) {
                    $q->where('referencia_type', CitaQuirurgica::class)
                      ->orWhere('referencia_type', 'like', '%CitaQuirurgica%');
                })
                ->where('estado', 'pendiente')
                ->first();

            if (!$cuentaCobro) {
                // Solo crear si genuinamente no existe (ej. cirugía de emergencia inmediata sin programar)
                $this->crearRegistroCajaCirugia($cita);
            }

            DB::commit();

            // Registrar en bitácora
            $this->logActivity(
                'finalizar_cirugia',
                'Cirugía finalizada - Paciente: ' . ($cita->paciente ? $cita->paciente->nombre : 'N/A') .
                ' - Tipo: ' . $cita->tipo_cirugia .
                ' - Duración: ' . ($cita->duracion_real_minutos ? $cita->duracion_real_minutos . ' min' : 'N/A'),
                $cita
            );

            return response()->json([
                'success' => true,
                'message' => 'Cirugía finalizada exitosamente.',
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
        $cita->refresh();
        $monto = $cita->costo_final ?? $cita->costo_base ?? 0;

        \Log::info('[CuentaMaestra] Registrando cargo de cirugía', [
            'cita_id'   => $cita->id,
            'monto'     => $monto,
            'paciente'  => $cita->ci_paciente,
        ]);

        try {
            // Obtener o crear la cuenta maestra del paciente (nunca duplica)
            $cuenta = \App\Services\CuentaCobroService::obtenerOCrearCuentaMaestra(
                $cita->ci_paciente,
                'quirofano'
            );

            // Agregar el cargo de cirugía con deduplicación automática
            \App\Services\CuentaCobroService::agregarCargoConDeduplicacion(
                $cuenta->id,
                'procedimiento',
                'Cirugía ' . $cita->tipo_cirugia . ' (Cita #' . $cita->id . ')',
                (float) $monto,
                1,
                'quirofano',
                CitaQuirurgica::class,
                $cita->id
            );

            return $cuenta;

        } catch (\Exception $e) {
            \Log::error('[CuentaMaestra] Error al registrar cargo de cirugía: ' . $e->getMessage(), [
                'cita_id' => $cita->id,
                'monto'   => $monto,
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

        // Registrar en bitácora
        $this->logActivity(
            'cancelar_cirugia',
            'Cirugía cancelada - Paciente: ' . ($cita->paciente ? $cita->paciente->nombre : 'N/A') .
            ' - Motivo: ' . $request->motivo_cancelacion,
            $cita
        );

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
            $parts = explode(':', $request->hora_inicio);
            $horaInicio = Carbon::createFromTime((int) $parts[0], (int) ($parts[1] ?? 0));
            $horaFin = $horaInicio->copy()->addMinutes($tipoCirugia->duracion_minutos);

            // Buscar citas existentes
            $citasExistentes = CitaQuirurgica::where('quirofano_id', $request->nro_quirofano)
                ->where('fecha', $request->fecha)
                ->where('estado', '!=', 'cancelada')
                ->get();

            $conflictos = [];
            foreach ($citasExistentes as $cita) {
                // Handle both Carbon and string formats for hora_inicio_estimada
                if ($cita->hora_inicio_estimada instanceof \Carbon\Carbon) {
                    $citaInicio = $cita->hora_inicio_estimada->copy();
                } else {
                    $citaParts = explode(':', (string) $cita->hora_inicio_estimada);
                    $citaInicio = Carbon::createFromTime((int) $citaParts[0], (int) ($citaParts[1] ?? 0));
                }
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

    public function historial(Request $request): View
    {
        $query = CitaQuirurgica::with(['paciente', 'cirujano.user', 'quirofano']);

        // Aplicar filtro de estado
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        // Aplicar filtro de rango de fechas
        if ($request->filled('fecha_desde') && $request->filled('fecha_hasta')) {
            $query->whereBetween('fecha', [$request->fecha_desde, $request->fecha_hasta]);
        } elseif ($request->filled('fecha_desde')) {
            $query->whereDate('fecha', '>=', $request->fecha_desde);
        } elseif ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha', '<=', $request->fecha_hasta);
        }

        $citas = $query->orderBy('created_at', 'desc')
            ->paginate(10);

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
            // Médicos con registro en tabla medicos (doctores y cirujanos)
            $medicos = Medico::with('user', 'especialidad')
                ->whereHas('user', function($q) {
                    $q->whereIn('role', ['doctor', 'cirujano']);
                })
                ->get()
                ->map(function($medico) {
                    return [
                        'ci' => $medico->ci,
                        'nombre' => $medico->user->name ?? 'Sin nombre',
                        'especialidad' => $medico->especialidad->nombre ?? 'Sin especialidad'
                    ];
                });

            $todos = $medicos->values();

            return response()->json([
                'success' => true,
                'medicos' => $todos
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar médicos: ' . $e->getMessage()
            ], 500);
        }
    }

    
    public function buscarProcedimientos(Request $request): JsonResponse
    {
        $q = trim($request->get('q', ''));
        $procedimientos = \App\Models\Procedimiento::activos()
            ->porArea('cirugia')
            ->when($q !== '', fn($query) => $query->where('nombre', 'like', "%{$q}%"))
            ->orderBy('nombre')
            ->get(['id', 'nombre', 'precio', 'descripcion']);

        return response()->json(['success' => true, 'procedimientos' => $procedimientos]);
    }

   /**
 * Obtener medicamentos disponibles en quirófano para una cirugía
 */
public function getMedicamentosDisponibles(CitaQuirurgica $cita): JsonResponse
{
    try {
        // Verificar que la cirugía esté programada o en curso
        if (!in_array($cita->estado, ['programada', 'en_curso'])) {
            return response()->json([
                'success' => false,
                'message' => 'La cirugía debe estar programada o en curso'
            ], 422);
        }

        // Obtener medicamentos del área 'quirófano' o 'cirugia' con stock > 0
        $medicamentos = \App\Models\AlmacenStock::where('cantidad_actual', '>', 0)
            ->whereIn('ubicacion', ['quirófano', 'cirugia'])
            ->whereHas('lote.catalogo', function($q) {
                $q->where('tipo', 'medicamento')
                  ->where('activo', true);
            })
            ->with(['lote.catalogo', 'lote'])
            ->get()
            ->map(function($stock) {
                return [
                    'id' => $stock->lote->catalogo->id,
                    'stock_id' => $stock->id,
                    'nombre' => $stock->lote->catalogo->nombre,
                    'presentacion' => $stock->lote->catalogo->presentacion ?? '',
                    'concentracion' => $stock->lote->catalogo->concentracion ?? '',
                    'cantidad' => $stock->cantidad_actual,
                    'precio' => (float) ($stock->lote->precio_venta ?? $stock->lote->catalogo->precio ?? 0),
                    'unidad_medida' => $stock->lote->catalogo->unidad_medida ?? 'unidad',
                ];
            });

        return response()->json([
            'success' => true,
            'medicamentos' => $medicamentos
        ]);
    } catch (\Exception $e) {
        \Log::error('Error en getMedicamentosDisponibles: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Error al cargar medicamentos: ' . $e->getMessage()
        ], 500);
    }
}

    /**
     * Obtener medicamentos ya usados en una cirugía
     */
    public function getMedicamentosUsados(CitaQuirurgica $cita): JsonResponse
    {
        try {
            // Obtener detalles de tipo medicamento buscando por origen o por la cuenta vinculada
            $medicamentos = \App\Models\CuentaCobroDetalle::where('tipo_item', 'medicamento')
                ->where(function ($q) use ($cita) {
                    // Si el detalle tiene origen_type específico a esta cirugía
                    $q->where(function ($sub) use ($cita) {
                        $sub->where('origen_type', CitaQuirurgica::class)
                            ->orWhere('origen_type', 'like', '%CitaQuirurgica%');
                    })->where('origen_id', $cita->id);
                    
                    // O si pertenece a una cuenta no unificada que referencia directamente a la cirugía
                    $q->orWhereHas('cuentaCobro', function ($qCuenta) use ($cita) {
                        $qCuenta->where(function ($qRef) {
                            $qRef->where('referencia_type', CitaQuirurgica::class)
                                 ->orWhere('referencia_type', 'like', '%CitaQuirurgica%');
                        })->where('referencia_id', $cita->id);
                    });
                })
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
                'almacen_medicamento_id' => 'required|exists:almacen_stocks,id',
                'cantidad' => 'required|integer|min:1'
            ]);

            $stock = AlmacenStock::with('lote.catalogo')->findOrFail($validated['almacen_medicamento_id']);

            if ($stock->cantidad_actual < $validated['cantidad']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stock insuficiente. Disponible: ' . $stock->cantidad_actual
                ], 422);
            }

            DB::beginTransaction();

            try {
                $cantidadAnterior = $stock->cantidad_actual;
                $stock->decrement('cantidad_actual', $validated['cantidad']);
                $medicamento = $stock->lote->catalogo;

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
                        ->where('tipo_atencion', 'cirugia')
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
                
                // Si aún no existe, crearla automáticamente (para cirugías existentes antes del fix)
                if (!$cuentaCobro) {
                    \Log::info('Creando cuenta de cobro automáticamente para cita existente', [
                        'cita_id' => $cita->id,
                        'paciente_ci' => $cita->ci_paciente
                    ]);
                    $cuentaCobro = $this->crearRegistroCajaCirugia($cita);
                }

                $precioUnitario = $stock->lote->precio_venta ?? 0;
                $subtotal = $precioUnitario * $validated['cantidad'];

                $detalle = $cuentaCobro->detalles()->create([
                    'tipo_item' => 'medicamento',
                    'descripcion' => $medicamento->nombre . ' (' . $medicamento->tipo . ')',
                    'cantidad' => $validated['cantidad'],
                    'precio_unitario' => $precioUnitario,
                    'subtotal' => $subtotal,
                    'origen_type' => CitaQuirurgica::class,
                    'origen_id' => $cita->id,
                ]);

                $cuentaCobro->total_calculado = $cuentaCobro->detalles()->sum('subtotal');
                $cuentaCobro->save();

                \App\Models\ActivityLog::create([
                    'user_id' => auth()->id(),
                    'action' => 'medicamento_cirugia_agregado',
                    'model_type' => CitaQuirurgica::class,
                    'model_id' => $cita->id,
                    'description' => 'Medicamento agregado a cirugía: ' . $medicamento->nombre,
                    'new_values' => json_encode([
                        'almacen_stock_id' => $stock->id,
                        'nombre' => $medicamento->nombre,
                        'cantidad' => $validated['cantidad'],
                        'precio_unitario' => $precioUnitario,
                        'subtotal' => $subtotal,
                        'stock_anterior' => $cantidadAnterior,
                        'stock_nuevo' => $stock->cantidad_actual,
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

    /**
     * Agregar equipo médico a una cirugía en curso
     */
    public function agregarEquipoMedico(Request $request, CitaQuirurgica $cita): JsonResponse
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
                'nombre' => 'required|string|max:255',
                'precio' => 'required|numeric|min:0',
                'cantidad' => 'required|integer|min:1'
            ]);

            DB::beginTransaction();

            try {
                // Buscar cuenta de cobro existente
                $refType = 'App\Models\CitaQuirurgica';
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
                        ->where('tipo_atencion', 'cirugia')
                        ->where('estado', 'pendiente')
                        ->orderBy('created_at', 'desc')
                        ->first();

                    if ($cuentaCobro) {
                        $cuentaCobro->referencia_id = $cita->id;
                        $cuentaCobro->referencia_type = $refType;
                        $cuentaCobro->save();
                    }
                }

                // Si aún no existe, crearla automáticamente
                if (!$cuentaCobro) {
                    $cuentaCobro = $this->crearRegistroCajaCirugia($cita);
                }

                $precioUnitario = $validated['precio'];
                $cantidad = $validated['cantidad'];
                $subtotal = $precioUnitario * $cantidad;

                // Crear detalle en cuenta de cobro
                $detalle = $cuentaCobro->detalles()->create([
                    'tipo_item' => 'equipo_medico',
                    'descripcion' => 'Cirugía - Equipo/Procedimiento: ' . $validated['nombre'],
                    'cantidad' => $cantidad,
                    'precio_unitario' => $precioUnitario,
                    'subtotal' => $subtotal,
                    'origen_type' => CitaQuirurgica::class,
                    'origen_id' => $cita->id,
                ]);

                // Actualizar total de cuenta de cobro
                $cuentaCobro->total_calculado = $cuentaCobro->detalles()->sum('subtotal');
                $cuentaCobro->save();

                // Actualizar equipos_medicos en la cita (cirugía)
                $equiposMedicos = $cita->equipos_medicos ?? [];
                $equiposMedicos[] = [
                    'nombre' => $validated['nombre'],
                    'precio_unitario' => $precioUnitario,
                    'cantidad' => $cantidad,
                    'subtotal' => $subtotal,
                    'fecha' => now()->toDateTimeString(),
                    'usuario_id' => auth()->id(),
                ];
                $cita->update(['equipos_medicos' => $equiposMedicos]);

                // Registrar en ActivityLog para historial
                \App\Models\ActivityLog::create([
                    'user_id' => auth()->id(),
                    'action' => 'equipo_medico_cirugia_agregado',
                    'model_type' => CitaQuirurgica::class,
                    'model_id' => $cita->id,
                    'description' => 'Equipo médico agregado a cirugía: ' . $validated['nombre'],
                    'new_values' => json_encode([
                        'nombre' => $validated['nombre'],
                        'cantidad' => $cantidad,
                        'precio_unitario' => $precioUnitario,
                        'subtotal' => $subtotal,
                        'cuenta_cobro_id' => $cuentaCobro->id,
                        'detalle_id' => $detalle->id
                    ]),
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent()
                ]);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Equipo médico agregado exitosamente',
                    'equipo' => [
                        'nombre' => $validated['nombre'],
                        'cantidad' => $cantidad,
                        'precio_unitario' => $precioUnitario,
                        'subtotal' => $subtotal
                    ]
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
                'message' => 'Error al agregar equipo médico: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener equipos médicos agregados a una cirugía
     */
    public function getEquiposMedicos(CitaQuirurgica $cita): JsonResponse
    {
        try {
            $equiposMedicos = $cita->equipos_medicos ?? [];

            // También buscar en detalles de cuentas de cobro que pertenezcan a esta cirugía
            $detallesEquipos = \App\Models\CuentaCobroDetalle::where('tipo_item', 'equipo_medico')
                ->where(function ($q) use ($cita) {
                    // Detalles con origen explícito
                    $q->where(function ($sub) use ($cita) {
                        $sub->where('origen_type', CitaQuirurgica::class)
                            ->orWhere('origen_type', 'like', '%CitaQuirurgica%');
                    })->where('origen_id', $cita->id);
                    
                    // Detalles de cuenta específica para esta cirugía
                    $q->orWhereHas('cuentaCobro', function ($qCuenta) use ($cita) {
                        $qCuenta->where(function ($qRef) {
                            $qRef->where('referencia_type', CitaQuirurgica::class)
                                 ->orWhere('referencia_type', 'like', '%CitaQuirurgica%');
                        })->where('referencia_id', $cita->id);
                    });
                })
                ->get();

            $equiposDesdeCuenta = [];
            foreach ($detallesEquipos as $detalle) {
                $equiposDesdeCuenta[] = [
                    'nombre' => str_replace('Cirugía - Equipo/Procedimiento: ', '', $detalle->descripcion),
                    'precio_unitario' => $detalle->precio_unitario,
                    'cantidad' => $detalle->cantidad,
                    'subtotal' => $detalle->subtotal,
                    'fecha' => $detalle->created_at->toDateTimeString(),
                ];
            }

            // Combinar ambas fuentes (priorizar los de la cita)
            $equipos = !empty($equiposMedicos) ? $equiposMedicos : $equiposDesdeCuenta;

            return response()->json([
                'success' => true,
                'equipos' => $equipos,
                'total' => count($equipos),
                'total_monto' => array_sum(array_column($equipos, 'subtotal'))
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener equipos médicos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crear un paciente real desde datos de emergencia temporal
     */
    private function crearPacienteDesdeTemporal($emergencia): Paciente
    {
        // Generar un CI numérico único basado en el ID de emergencia (columna ci es integer)
        $ci = (int)('9' . $emergencia->id . now()->format('is'));

        // Crear registro
        $registroCodigo = 'REG-' . date('Y') . '-' . str_pad(Registro::count() + 1, 6, '0', STR_PAD_LEFT);
        Registro::create([
            'codigo' => $registroCodigo,
            'fecha' => now()->toDateString(),
            'hora' => now()->toTimeString(),
            'motivo' => 'Paciente temporal creado desde programación de cirugía de emergencia',
            'user_id' => auth()->id()
        ]);

        // Obtener o crear seguro particular
        $seguro = Seguro::firstOrCreate(
            ['nombre_empresa' => 'Particular'],
            [
                'tipo' => 'Particular',
                'telefono' => null,
                'formulario' => 'PARTICULAR',
                'estado' => 'activo'
            ]
        );

        // Crear el paciente
        $paciente = Paciente::create([
            'ci' => $ci,
            'nombre' => 'Paciente Temporal EMG-' . $emergencia->id,
            'sexo' => 'M',
            'direccion' => 'Sin especificar',
            'telefono' => 0,
            'correo' => 'sin@email.com',
            'seguro_id' => $seguro->id,
            'registro_codigo' => $registroCodigo,
        ]);

        // Actualizar la emergencia para referenciar al paciente creado
        $emergencia->update([
            'patient_id' => $ci,
            'is_temp_id' => false,
            'temp_id' => null,
        ]);

        return $paciente;
    }

    /**
     * Exportar historial de cirugías a Excel (CSV format)
     */
    public function exportHistorial(Request $request)
    {
        $query = CitaQuirurgica::with(['paciente', 'cirujano.user', 'quirofano']);

        // Aplicar filtros igual que en historial()
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }
        if ($request->filled('fecha_desde') && $request->filled('fecha_hasta')) {
            $query->whereBetween('fecha', [$request->fecha_desde, $request->fecha_hasta]);
        } elseif ($request->filled('fecha_desde')) {
            $query->whereDate('fecha', '>=', $request->fecha_desde);
        } elseif ($request->filled('fecha_hasta')) {
            $query->whereDate('fecha', '<=', $request->fecha_hasta);
        }

        $citas = $query->orderBy('fecha', 'desc')
            ->orderBy('hora_inicio_estimada', 'desc')
            ->get();

        // Construir nombre del archivo
        $fechaDesde = $request->filled('fecha_desde')
            ? Carbon::parse($request->fecha_desde)->format('d-m-Y')
            : 'inicio';
        $fechaHasta = $request->filled('fecha_hasta')
            ? Carbon::parse($request->fecha_hasta)->format('d-m-Y')
            : 'hoy';

        $nombreArchivo = "cirugias de {$fechaDesde} a {$fechaHasta}.csv";

        // Generar CSV
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$nombreArchivo}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
        ];

        $callback = function() use ($citas) {
            $file = fopen('php://output', 'w');

            // BOM para UTF-8 (Excel reconoce correctamente caracteres especiales)
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Título
            fputcsv($file, ['HISTORIAL DE CIRUGIAS'], ';');
            fputcsv($file, [], ';');

            // Encabezados de columnas
            $encabezados = [
                'Fecha',
                'Hora',
                'Paciente',
                'CI',
                'Cirujano',
                'Quirofano',
                'Tipo',
                'Estado',
                'Duracion'
            ];

            if (auth()->user()->role !== 'cirujano') {
                $encabezados[] = 'Costo';
            }

            fputcsv($file, $encabezados, ';');

            // Datos
            foreach ($citas as $cita) {
                $duracion = $cita->duracion_real
                    ? ($this->formatearDuracionExport($cita->duracion_real))
                    : '-';
                
                $fila = [
                    $cita->fecha->format('d/m/Y'),
                    $cita->hora_inicio_estimada->format('H:i'),
                    $cita->paciente->nombre,
                    $cita->paciente->ci,
                    optional($cita->cirujano->user)->name ?? 'N/A',
                    'Q' . $cita->quirofano->id,
                    $cita->tipo_cirugia,
                    ucfirst($cita->estado),
                    $duracion
                ];

                if (auth()->user()->role !== 'cirujano') {
                    $fila[] = $cita->costo_final ? '$' . number_format($cita->costo_final, 2) : '-';
                }

                fputcsv($file, $fila, ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Formatear duración para exportación
     */
    private function formatearDuracionExport($minutos)
    {
        $total = round($minutos);
        $horas = floor($total / 60);
        $mins = $total % 60;
        if ($horas > 0) {
            return "{$horas}h {$mins}min";
        }
        return "{$mins}min";
    }

}
