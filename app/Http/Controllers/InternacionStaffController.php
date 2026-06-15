<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Hospitalizacion;
use App\Models\Paciente;
use App\Models\Cama;
use App\Models\CuentaCobro;
use App\Models\CuentaCobroDetalle;
use App\Models\HospCatering;
use App\Models\CateringPrecio;
use App\Models\Emergency;
use App\Models\Quirofano;
use Carbon\Carbon;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Services\EpisodioService;

class InternacionStaffController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:internacion|admin|dirmedico|enfermera-internacion|administrador');
    }

    public function index(Request $request): View
    {
        $fecha = $request->filled('fecha')
            ? Carbon::parse($request->fecha)->toDateString()
            : today()->toDateString();

        // Fuente 1: evaluaciones de internación (medicamentos, insumos, procedimientos)
        $evaluaciones = \App\Models\Evaluacion::with(['paciente', 'user', 'items'])
            ->where('area', 'internacion')
            ->whereDate('created_at', $fecha)
            ->orderBy('created_at')
            ->get();

        $medicamentos    = $evaluaciones->flatMap(fn($ev) => $ev->items->where('tipo', 'medicamento')->map(fn($it) => (object)['paciente' => $ev->paciente, 'user' => $ev->user, 'item' => $it, 'hora' => $ev->created_at]));
        $insumos         = $evaluaciones->flatMap(fn($ev) => $ev->items->where('tipo', 'insumo')->map(fn($it) => (object)['paciente' => $ev->paciente, 'user' => $ev->user, 'item' => $it, 'hora' => $ev->created_at]));
        $procedimientos  = $evaluaciones->flatMap(fn($ev) => $ev->items->where('tipo', 'procedimiento')->map(fn($it) => (object)['paciente' => $ev->paciente, 'user' => $ev->user, 'item' => $it, 'hora' => $ev->created_at]));

        // Fuente 2: habitaciones registradas (registro-uso)
        $habitacionesRegistradas = CuentaCobroDetalle::with(['cuentaCobro.paciente', 'user'])
            ->where('tipo_item', 'estadia')
            ->whereDate('created_at', $fecha)
            ->orderBy('created_at')
            ->get();

        return view('internacion-staff.dashboard', compact(
            'fecha',
            'evaluaciones',
            'habitacionesRegistradas'
        ));
    }

    /**
     * API: Get internaciones for dashboard
     */
    public function apiInternaciones(Request $request): JsonResponse
    {
        $query = Hospitalizacion::with(['paciente', 'medico.user', 'medico.especialidad'])
            ->whereNull('fecha_alta')
            ->where('estado', '!=', 'trasladado')
            ->orderBy('fecha_ingreso', 'desc');

        if ($request->has('estado') && $request->estado !== 'todos') {
            $query->where('estado', $request->estado);
        }

        $internaciones = $query->get()->map(function($hosp) {
            return [
                'id' => $hosp->id,
                'codigo' => $hosp->id,
                'paciente_id' => $hosp->paciente_id,
                'paciente_nombre' => $hosp->paciente?->nombre ?? 'Desconocido',
                'tipo' => strtolower($hosp->tipo),
                'servicio' => $hosp->servicio,
                'habitacion' => $hosp->habitacion_id ?? 'Por asignar',
                'hora_ingreso' => $hosp->fecha_ingreso?->format('H:i') ?? 'N/A',
                'fecha_ingreso' => $hosp->fecha_ingreso?->format('d/m/Y') ?? 'N/A',
                'estado' => $hosp->estado ?? 'activo',
                'medico' => $hosp->medico?->user?->name ?? 'No asignado',
                'diagnostico' => $hosp->diagnostico,
            ];
        });

        $stats = [
            'activos' => Hospitalizacion::whereNull('fecha_alta')->where('estado', '!=', 'trasladado')->count(),
            'espera' => Hospitalizacion::whereNull('fecha_alta')->where('estado', '!=', 'trasladado')->whereNull('habitacion_id')->count(),
            'atencion' => Hospitalizacion::whereNull('fecha_alta')->where('estado', '!=', 'trasladado')->whereNotNull('habitacion_id')->count(),
            'hoy' => Hospitalizacion::whereDate('fecha_ingreso', today())->where('estado', '!=', 'trasladado')->count(),
        ];

        return response()->json([
            'success' => true,
            'internaciones' => $internaciones,
            'stats' => $stats
        ]);
    }

    /**
     * API: Get statistics
     */
    public function apiEstadisticas(): JsonResponse
    {
        $stats = [
            'activos' => Hospitalizacion::whereNull('fecha_alta')->where('estado', '!=', 'trasladado')->count(),
            'espera' => Hospitalizacion::whereNull('fecha_alta')->where('estado', '!=', 'trasladado')->whereNull('habitacion_id')->count(),
            'atencion' => Hospitalizacion::whereNull('fecha_alta')->where('estado', '!=', 'trasladado')->whereNotNull('habitacion_id')->count(),
            'hoy' => Hospitalizacion::whereDate('fecha_ingreso', today())->where('estado', '!=', 'trasladado')->count(),
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }

    /**
     * API: Update status
     */
    public function updateStatus(Request $request, $id): JsonResponse
    {
        $hospitalizacion = Hospitalizacion::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:activo,en_observacion,estable,crítico,alta,trasladado',
        ]);

        $hospitalizacion->update(['estado' => $validated['status']]);

        return response()->json([
            'success' => true,
            'message' => 'Estado actualizado correctamente',
            'status' => $hospitalizacion->estado,
        ]);
    }

    /**
     * API: Derivar a Quirófano
     */
    public function derivarAQuirofano(Request $request, $id): JsonResponse
    {
        try {
            DB::beginTransaction();

            $hospitalizacion = Hospitalizacion::with('paciente')->findOrFail($id);

            // Verificar si ya está en quirófano (buscar en emergencias)
            $cirugiaActiva = Emergency::where('paciente_id', $hospitalizacion->paciente_id)
                ->where('ubicacion_actual', 'cirugia')
                ->whereIn('status', ['cirugia', 'en_evaluacion', 'estabilizado'])
                ->first();

            if ($cirugiaActiva) {
                return response()->json([
                    'success' => false,
                    'message' => 'El paciente ya tiene una cirugía activa',
                ], 422);
            }

            // Buscar quirófano (opcional - si existe alguno disponible, se usa para info)
            $quirofano = Quirofano::where('estado', '!=', 'mantenimiento')->first();

            // Generar número de cirugía
            $nroCirugia = 'CIR-' . now()->format('Ymd') . '-' . str_pad($hospitalizacion->id, 4, '0', STR_PAD_LEFT);

            // Crear registro en emergencias como paciente en cirugía
            $emergency = Emergency::crearConCodigo([
                'paciente_id' => $hospitalizacion->paciente_id,
                'user_id' => auth()->id(),
                'status' => 'cirugia',
                'ubicacion_actual' => 'cirugia',
                'nro_cirugia' => $nroCirugia,
                'symptoms' => 'Derivación desde internación para cirugía',
                'initial_assessment' => $hospitalizacion->diagnostico,
                'admission_date' => now(),
                'tipo_ingreso' => 'general',
                'destino_inicial' => 'cirugia',
                'flujo_historial' => [
                    [
                        'fecha' => now()->toDateTimeString(),
                        'desde' => 'internacion',
                        'hasta' => 'cirugia',
                        'usuario_id' => auth()->id(),
                        'notas' => 'Derivación desde internación a quirófano',
                    ]
                ],
            ]);

            // Actualizar hospitalización
            $hospitalizacion->update([
                'estado' => 'trasladado',
                'observaciones' => ($hospitalizacion->observaciones ? $hospitalizacion->observaciones . "\n" : '') .
                    'Trasladado a Quirófano: ' . now()->format('d/m/Y H:i'),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Paciente derivado a Quirófano correctamente',
                'cirugia' => [
                    'id' => $emergency->id,
                    'nro_cirugia' => $nroCirugia,
                    'quirofano' => $quirofano ? ($quirofano->tipo ?? 'Q' . $quirofano->id) : 'Sin asignar',
                ],
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Error al derivar a quirófano: ' . $e->getMessage(), [
                'hospitalizacion_id' => $id,
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error al derivar a Quirófano: ' . $e->getMessage(),
            ], 500);
        }
    }

    // El alta de pacientes se realiza exclusivamente en /patients-dar-de-alta
    // (PatientsController::darDeAlta), que registra AltaPaciente y cierra el episodio.
    // El antiguo darAlta() que marcaba hospitalizaciones.estado/fecha_alta y cobraba
    // estancia sobre la hospitalización fue eliminado (método legacy).

    /**
     * API: Get catering del paciente
     */
    public function apiCatering($id): JsonResponse
    {
        $hospitalizacion = Hospitalizacion::findOrFail($id);

        // Obtener o inicializar registros de hoy
        $fecha = now()->toDateString();
        $tiposComida = ['desayuno', 'almuerzo', 'merienda', 'cena'];
        $catering = [];

        foreach ($tiposComida as $tipo) {
            $registro = HospCatering::where('hospitalizacion_id', $id)
                ->where('fecha', $fecha)
                ->where('tipo_comida', $tipo)
                ->first();

            if ($registro) {
                $catering[] = [
                    'id' => $registro->id,
                    'tipo_comida' => $tipo,
                    'tipo_label' => $registro->tipo_comida_label,
                    'estado' => $registro->estado,
                    'estado_label' => $registro->estado_label,
                    'estado_color' => $registro->estado_color,
                    'hora_registro' => $registro->hora_registro?->format('H:i'),
                    'observaciones' => $registro->observaciones,
                    'precio' => $registro->precio,
                    'cargo_generado' => $registro->cargo_generado,
                ];
            } else {
                $precios = CateringPrecio::getPreciosArray();
                $catering[] = [
                    'id' => null,
                    'tipo_comida' => $tipo,
                    'tipo_label' => ucfirst($tipo),
                    'estado' => 'no_dado',
                    'estado_label' => 'No Dado',
                    'estado_color' => 'red',
                    'hora_registro' => null,
                    'observaciones' => null,
                    'precio' => $precios[$tipo] ?? 0,
                    'cargo_generado' => false,
                ];
            }
        }

        return response()->json([
            'success' => true,
            'fecha' => $fecha,
            'catering' => $catering
        ]);
    }

    /**
     * Helper: Anular cargo
     */
    private function anularCargo($detalleId): void
    {
        $detalle = CuentaCobroDetalle::find($detalleId);
        if ($detalle) {
            $cuenta = $detalle->cuentaCobro;
            $detalle->delete();
            if ($cuenta) {
                $cuenta->recalcularTotales();
            }
        }
    }

    /**
     * API: Get precios de catering
     */
    public function apiCateringPrecios(): JsonResponse
    {
        $precios = CateringPrecio::getPreciosArray();

        return response()->json([
            'success' => true,
            'precios' => $precios
        ]);
    }

    /**
     * API: Actualizar precios de catering
     */
    public function actualizarCateringPrecios(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $rolesPermitidos = ['admin', 'administrador', 'dirmedico'];
            $tienePermiso = false;

            foreach ($rolesPermitidos as $rol) {
                if ($user->hasRole($rol)) {
                    $tienePermiso = true;
                    break;
                }
            }

            if (!$tienePermiso) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tiene permisos para actualizar los precios'
                ], 403);
            }

            $validated = $request->validate([
                'desayuno' => 'required|numeric|decimal:0,2|min:0',
                'almuerzo' => 'required|numeric|decimal:0,2|min:0',
                'merienda' => 'required|numeric|decimal:0,2|min:0',
                'cena' => 'required|numeric|decimal:0,2|min:0',
            ]);

            foreach ($validated as $tipo => $precio) {
                CateringPrecio::actualizarPrecio($tipo, floatval($precio));
            }

            return response()->json([
                'success' => true,
                'message' => 'Precios de catering actualizados correctamente',
                'precios' => CateringPrecio::getPreciosArray()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar precios: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Vista de Gestión de Precios de Catering (Admin)
     */
    public function gestionCatering(): View
    {
        $precios = CateringPrecio::getPreciosArray();

        return view('internacion-staff.catering.gestion', compact('precios'));
    }

    /**
     * Vista de Catering Masivo - Todos los pacientes del sistema
     */
    public function cateringIndex(Request $request): View
    {
        $fecha = now()->toDateString();

        // Query base: TODOS los pacientes con registro (como en PatientsController)
        $query = Paciente::with([
                'seguro',
                'registro',
                'consultas' => fn($q) => $q->orderBy('created_at', 'desc')->limit(1),
                'hospitalizaciones' => fn($q) => $q->orderBy('created_at', 'desc')->limit(1),
                'emergencias' => fn($q) => $q->orderBy('created_at', 'desc')->limit(1),
            ])
            ->whereHas('registro');

        // Filtro de búsqueda
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'LIKE', "%{$search}%")
                    ->orWhere('ci', 'LIKE', "%{$search}%")
                    ->orWhereHas('registro', fn($rq) => $rq->where('codigo', 'LIKE', "%{$search}%"));
            });
        }

        $pacientes = $query->orderBy('created_at', 'desc')->paginate(15);

        // Obtener pacientes temporales de emergencias
        $emergencyQuery = Emergency::with('paciente')
            ->whereHas('paciente', fn($q) => $q->where('is_temp', true))
            ->whereIn('status', ['recibido', 'en_evaluacion', 'estabilizado']);

        if ($request->filled('search')) {
            $search = $request->search;
            $emergencyQuery->where(fn($q) => $q->where('code', 'LIKE', "%{$search}%")
                ->orWhereHas('paciente', fn($q2) => $q2->where('temp_code', 'LIKE', "%{$search}%")));
        }

        $pacientesTemporales = $emergencyQuery->orderBy('created_at', 'desc')->get()
            ->map(fn($emergency) => (object)[
                'id'               => $emergency->paciente_id,
                'ci'               => null,
                'temp_code'        => $emergency->paciente?->temp_code,
                'nombre'           => 'Paciente Temporal - Emergencia',
                'is_temp'          => true,
                'emergency_id'     => $emergency->id,
                'emergency_code'   => $emergency->code,
                'emergency_status' => $emergency->status,
                'created_at'       => $emergency->created_at,
                'seguro'           => null,
                'area_actual'      => 'emergencia',
            ]);

        // Determinar área actual para cada paciente
        $pacientes->getCollection()->transform(function ($paciente) {
            $paciente->area_actual = $this->determinarAreaActualPaciente($paciente);
            return $paciente;
        });

        // Combinar pacientes regulares y temporales
        $todosPacientes = collect($pacientes->items())->merge($pacientesTemporales)
            ->sortByDesc('created_at')
            ->values();

        $pacienteIds = $todosPacientes->pluck('id')->filter()->unique();
        $cateringHoy = HospCatering::where('fecha', $fecha)
            ->whereIn('paciente_id', $pacienteIds)
            ->get()
            ->groupBy('paciente_id');

        // Obtener precios actuales
        $precios = CateringPrecio::getPreciosArray();

        // Stats
        $stats = [
            'total' => Paciente::whereHas('registro')->count(),
            'hospitalizados' => Hospitalizacion::whereNull('fecha_alta')->where('estado', '!=', 'trasladado')->count(),
            'emergencias' => Emergency::whereIn('status', ['recibido', 'en_evaluacion', 'estabilizado'])->count(),
        ];

        return view('internacion-staff.catering.index', compact(
            'pacientes',
            'pacientesTemporales',
            'todosPacientes',
            'cateringHoy',
            'precios',
            'fecha',
            'stats'
        ));
    }

    /**
     * API: Estados de catering de una fecha para un conjunto de pacientes.
     * Permite al frontend refrescar los indicadores al cambiar la fecha sin recargar.
     */
    public function cateringPorFecha(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'fecha'  => 'required|date',
            'ids'    => 'array',
            'ids.*'  => 'integer',
        ]);

        $fecha = \Carbon\Carbon::parse($validated['fecha'])->toDateString();
        $ids   = $validated['ids'] ?? [];

        $estados = HospCatering::where('fecha', $fecha)
            ->whereIn('paciente_id', $ids)
            ->get(['paciente_id', 'tipo_comida', 'estado'])
            ->groupBy('paciente_id')
            ->map(fn($registros) => [
                'desayuno' => $registros->firstWhere('tipo_comida', 'desayuno')->estado ?? 'no_dado',
                'almuerzo' => $registros->firstWhere('tipo_comida', 'almuerzo')->estado ?? 'no_dado',
                'merienda' => $registros->firstWhere('tipo_comida', 'merienda')->estado ?? 'no_dado',
                'cena'     => $registros->firstWhere('tipo_comida', 'cena')->estado ?? 'no_dado',
            ]);

        return response()->json($estados);
    }

    /**
     * Determinar el área actual donde está el paciente
     */
    private function determinarAreaActualPaciente(Paciente $paciente): string
    {
        // Verificar si está en emergencia activa
        $emergenciaActiva = $paciente->emergencias
            ->whereIn('status', ['recibido', 'en_evaluacion', 'estabilizado'])
            ->first();
        if ($emergenciaActiva) {
            return 'emergencia';
        }

        // Verificar si está internado
        $hospitalizacionActiva = $paciente->hospitalizaciones
            ->whereNull('fecha_alta')
            ->where('estado', '!=', 'trasladado')
            ->first();
        if ($hospitalizacionActiva) {
            return 'internacion';
        }

        // Verificar consulta externa reciente (últimas 24 horas)
        $consultaReciente = $paciente->consultas
            ->where('created_at', '>=', now()->subDay())
            ->first();
        if ($consultaReciente) {
            return 'consulta';
        }

        return 'registrado';
    }

    /**
     * API: Registrar catering masivo (múltiples pacientes por CI)
     */
    public function cateringRegistrar(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $validated = $request->validate([
                'fecha' => 'nullable|date',
                'registros' => 'required|array',
                'registros.*.paciente_id' => 'required|integer|exists:pacientes,id',
                'registros.*.tipo_comida' => 'required|in:desayuno,almuerzo,merienda,cena',
                'registros.*.estado' => 'required|in:dado,no_dado,no_aplica',
                'registros.*.observaciones' => 'nullable|string|max:255',
            ]);

            // Fecha objetivo: la enviada por el usuario (para registrar días olvidados) o hoy por defecto.
            $fecha = !empty($validated['fecha'])
                ? \Carbon\Carbon::parse($validated['fecha'])->toDateString()
                : now()->toDateString();
            $registrosGuardados = 0;
            $cargosGenerados = 0;
            $errores = [];

            foreach ($validated['registros'] as $registroData) {
                try {
                    $pacienteId = (int) $registroData['paciente_id'];

                    $hospitalizacion = Hospitalizacion::where('paciente_id', $pacienteId)
                        ->whereNull('fecha_alta')
                        ->where('estado', '!=', 'trasladado')
                        ->first();

                    $registro = HospCatering::where('fecha', $fecha)
                        ->where('tipo_comida', $registroData['tipo_comida'])
                        ->where(function ($q) use ($pacienteId, $hospitalizacion) {
                            $q->where('paciente_id', $pacienteId);
                            if ($hospitalizacion) {
                                $q->orWhere('hospitalizacion_id', $hospitalizacion->id);
                            }
                        })
                        ->first();

                    $precio = 0;
                    $cuentaCobroDetalleId = null;
                    $cargoGenerado = false;

                    if ($registroData['estado'] === 'dado' && $pacienteId) {
                        $precio = CateringPrecio::getPrecio($registroData['tipo_comida']);

                        // Si ya tenía un cargo anterior, no generar otro
                        if ($registro && $registro->cargo_generado && $registro->estado === 'dado') {
                            $cuentaCobroDetalleId = $registro->cuenta_cobro_detalle_id;
                            $cargoGenerado = true;
                        } elseif ($precio > 0) {
                            // Generar cargo usando paciente_ci directamente
                            $cuentaCobroDetalleId = $this->generarCargoPorPaciente(
                                $pacienteId,
                                'Catering: ' . ucfirst($registroData['tipo_comida']),
                                $precio,
                                1,
                                'servicio'
                            );
                            $cargoGenerado = true;
                            $cargosGenerados++;
                        }
                    }

                    $data = [
                        'paciente_id' => $pacienteId,
                        'hospitalizacion_id' => $hospitalizacion?->id,
                        'registered_by' => Auth::id(),
                        'fecha' => $fecha,
                        'tipo_comida' => $registroData['tipo_comida'],
                        'estado' => $registroData['estado'],
                        'hora_registro' => $registroData['estado'] === 'dado' ? now() : null,
                        'observaciones' => $registroData['observaciones'] ?? null,
                        'precio' => $precio,
                        'cargo_generado' => $cargoGenerado,
                        'cuenta_cobro_detalle_id' => $cuentaCobroDetalleId,
                    ];

                    if ($registro) {
                        // Si cambia de 'dado' a otro estado, anular el cargo anterior
                        if ($registro->estado === 'dado' && $registroData['estado'] !== 'dado' && $registro->cuenta_cobro_detalle_id) {
                            $this->anularCargo($registro->cuenta_cobro_detalle_id);
                            $data['cargo_generado'] = false;
                            $data['cuenta_cobro_detalle_id'] = null;
                            $data['precio'] = 0;
                        }
                        $registro->update($data);
                    } else {
                        HospCatering::create($data);
                    }

                    $registrosGuardados++;

                } catch (\Exception $e) {
                    $errores[] = [
                        'paciente_id' => $registroData['paciente_id'],
                        'error' => $e->getMessage(),
                    ];
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Catering registrado: {$registrosGuardados} registros, {$cargosGenerados} cargos generados",
                'registros_guardados' => $registrosGuardados,
                'cargos_generados' => $cargosGenerados,
                'errores' => $errores
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar catering: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper: Generar cargo en cuenta de cobro por CI de paciente
     */
    private function generarCargoPorPaciente(int $pacienteId, string $concepto, float $precio, float $cantidad = 1, string $tipoItem = 'servicio'): ?int
    {
        $cuenta = \App\Services\CuentaCobroService::obtenerCuentaPostPagoActiva($pacienteId);

        if (!$cuenta) {
            $cuenta = \App\Services\CuentaCobroService::obtenerOCrearCuentaMaestra(
                $pacienteId,
                'general',
                null
            );
        }

        $detalle = $cuenta->detalles()->create([
            'tipo_item' => $tipoItem,
            'descripcion' => $concepto,
            'cantidad' => $cantidad,
            'precio_unitario' => $precio,
            'subtotal' => $precio * $cantidad,
            'area_origen' => 'internacion',
            'user_id' => Auth::id(),
        ]);

        $cuenta->recalcularTotales();

        return $detalle->id;
    }

    public function procedimientos(): \Illuminate\View\View
    {
        $procedimientos = \App\Models\Procedimiento::where('area', 'internacion')
            ->where('activo', true)
            ->orderBy('nombre')
            ->paginate(50);

        return view('internacion-staff.procedimientos', compact('procedimientos'));
    }
}
