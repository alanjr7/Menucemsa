<?php

namespace App\Http\Controllers;

use App\Models\CitaQuirurgica;
use App\Models\TipoCirugia;
use App\Models\Paciente;
use App\Models\Medico;
use App\Models\Quirofano;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class QuirofanoController extends Controller
{
    public function index(): View
    {
        $quirofanos = Quirofano::all();
        
        // Obtener la fecha actual y el rango de la semana
        $startOfWeek = now()->startOfWeek();
        $endOfWeek = now()->endOfWeek();
        
        // Obtener todas las citas de la semana
        $citasSemana = CitaQuirurgica::with(['paciente', 'cirujano.usuario', 'quirofano'])
            ->whereBetween('fecha', [$startOfWeek, $endOfWeek])
            ->orderBy('fecha')
            ->orderBy('hora_inicio_estimada')
            ->get();

        // Agrupar citas por día y hora
        $citasPorDiaHora = [];
        foreach ($citasSemana as $cita) {
            $dia = $cita->fecha->format('Y-m-d');
            $hora = $cita->hora_inicio_estimada->format('H:00');
            $citasPorDiaHora[$dia][$hora][$cita->nro_quirofano][] = $cita;
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
        ];
        
        return view('quirofano.index', compact('quirofanos', 'diasSemana', 'horasDia', 'citasPorDiaHora', 'stats'));
    }

    public function create(): View
    {
        try {
            $pacientes = Paciente::all();
            $medicos = Medico::with('usuario')->get();
            $quirofanos = Quirofano::all();
            $tiposCirugia = TipoCirugia::all();

            return view('quirofano.create', compact('pacientes', 'medicos', 'quirofanos', 'tiposCirugia'));

        } catch (\Exception $e) {
            // Log the error and return a simple error view
            \Log::error('Error en QuirofanoController@create: ' . $e->getMessage());
            abort(500, 'Error al cargar el formulario de creación: ' . $e->getMessage());
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
                'nro_quirofano' => 'required|exists:quirofanos,nro',
                'tipo_cirugia' => 'required|in:menor,mediana,mayor,ambulatoria',
                'fecha' => 'required|date|after_or_equal:today',
                'hora_inicio_estimada' => 'required|date_format:H:i',
            ]);

            // Crear cita sin validación de disponibilidad por ahora
            $cita = new CitaQuirurgica();
            $cita->ci_paciente = $validated['ci_paciente'];
            $cita->ci_cirujano = $validated['ci_cirujano'];
            $cita->nro_quirofano = $validated['nro_quirofano'];
            $cita->tipo_cirugia = $validated['tipo_cirugia'];
            $cita->fecha = $validated['fecha'];
            $cita->hora_inicio_estimada = $validated['hora_inicio_estimada'];
            
            // Campos opcionales
            $cita->ci_instrumentista = $request->input('ci_instrumentista');
            $cita->ci_anestesiologo = $request->input('ci_anestesiologo');
            $cita->descripcion_cirugia = $request->input('descripcion_cirugia');
            $cita->observaciones = $request->input('observaciones');
            
            // Establecer valores por defecto
            $cita->estado = 'programada';
            $cita->id_usuario_registro = auth()->id();

            // Obtener tipo de cirugía
            $tipoCirugia = TipoCirugia::where('nombre', $validated['tipo_cirugia'])->first();
            if (!$tipoCirugia) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tipo de cirugía no encontrado.'
                ], 422);
            }

            $cita->costo_base = $tipoCirugia->costo_base;

            // Validar disponibilidad del quirófano
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

            // Guardar cita
            $cita->save();

            return response()->json([
                'success' => true,
                'message' => 'Cita quirúrgica programada exitosamente.',
                'cita' => $cita->fresh()
            ]);

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

    public function show(CitaQuirurgica $cita): View
    {
        $cita->load([
            'paciente.seguro',
            'cirujano.usuario',
            'instrumentista.usuario',
            'anestesiologo.usuario',
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
        $medicos = Medico::with('usuario')->get();
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
            'nro_quirofano' => 'required|exists:quirofanos,nro',
            'tipo_cirugia' => 'required|in:menor,mediana,mayor,ambulatoria',
            'fecha' => 'required|date|after_or_equal:today',
            'hora_inicio_estimada' => 'required|date_format:H:i',
            'descripcion_cirugia' => 'nullable|string|max:500',
            'observaciones' => 'nullable|string|max:1000',
        ]);

        // Validar disponibilidad del quirófano
        $cita->fill($request->except(['id_usuario_registro']));
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

        $cita->update($request->except(['id_usuario_registro']));

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

        $cita->finalizarCirugia();

        return response()->json([
            'success' => true,
            'message' => 'Cirugía finalizada exitosamente.',
            'cita' => $cita->fresh()
        ]);
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
            'nro_quirofano' => 'required|exists:quirofanos,nro',
            'fecha' => 'required|date',
            'hora_inicio' => 'required|date_format:H:i',
            'tipo_cirugia' => 'required|in:menor,mediana,mayor,ambulatoria'
        ]);

        try {
            $tipoCirugia = TipoCirugia::where('nombre', $request->tipo_cirugia)->first();
            $horaInicio = Carbon::createFromFormat('H:i', $request->hora_inicio);
            $horaFin = $horaInicio->copy()->addMinutes($tipoCirugia->duracion_minutos);

            // Buscar citas existentes
            $citasExistentes = CitaQuirurgica::where('nro_quirofano', $request->nro_quirofano)
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
                    'nro_quirofano' => $request->nro_quirofano,
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
            'quirofano' => 'nullable|exists:quirofanos,nro',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio'
        ]);

        $query = CitaQuirurgica::with(['paciente', 'cirujano.usuario', 'quirofano'])
            ->whereBetween('fecha', [$request->fecha_inicio, $request->fecha_fin]);

        if ($request->quirofano) {
            $query->where('nro_quirofano', $request->quirofano);
        }

        $citas = $query->get()->map(function($cita) {
            return [
                'id' => $cita->id,
                'title' => $cita->paciente->nombre . ' - ' . $cita->tipo_cirugia,
                'start' => $cita->fecha->format('Y-m-d') . ' ' . $cita->hora_inicio_estimada->format('H:i:s'),
                'end' => $cita->fecha->format('Y-m-d') . ' ' . $cita->hora_fin_estimada->format('H:i:s'),
                'backgroundColor' => $this->getColorPorEstado($cita->estado),
                'borderColor' => $this->getColorPorEstado($cita->estado),
                'extendedProps' => [
                    'paciente' => $cita->paciente->nombre,
                    'cirujano' => $cita->cirujano->usuario->name,
                    'quirofano' => $cita->quirofano->nro,
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
}
