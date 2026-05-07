<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Emergency;
use App\Models\Paciente;
use App\Models\Quirofano;
use App\Models\Hospitalizacion;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Models\AlmacenStock;
use App\Services\CuentaCobroService;
use App\Services\ActivityLogService;

class EmergencyStaffController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:emergencia|enfermera-emergencia|admin|dirmedico|administrador')->except(['apiEmergenciasTemporales']);
    }

    public function index(Request $request): View
    {
        $fecha = $request->filled('fecha')
            ? \Carbon\Carbon::parse($request->fecha)->toDateString()
            : today()->toDateString();

        $evaluaciones = \App\Models\Evaluacion::with(['paciente', 'user', 'items'])
            ->where('area', 'emergencia')
            ->whereDate('created_at', $fecha)
            ->orderBy('created_at')
            ->get();

        $camillaRegistradas = \App\Models\CuentaCobroDetalle::with(['cuentaCobro.paciente', 'user'])
            ->where('area_origen', 'emergencia')
            ->whereDate('created_at', $fecha)
            ->orderBy('created_at')
            ->get();

        return view('emergency-staff.dashboard', compact('fecha', 'evaluaciones', 'camillaRegistradas'));
    }

    /**
     * API: Get emergencies for dashboard
     */
    public function apiEmergencias(Request $request): JsonResponse
    {
        $query = Emergency::with(['paciente'])
            ->where('ubicacion_actual', 'emergencia')
            ->whereIn('status', ['recibido', 'en_evaluacion', 'estabilizado'])
            ->orderBy('created_at', 'desc');

        if ($request->has('estado') && $request->estado !== 'todos') {
            $query->where('status', $request->estado);
        }

        $emergencias = $query->get()->map(function($emg) {
            return [
                'id' => $emg->id,
                'code' => $emg->code,
                'paciente_id' => $emg->patient_id,
                'temp_id' => $emg->temp_id,
                'paciente_nombre' => $emg->is_temp_id ? 'Paciente Temporal' : ($emg->paciente?->nombre ?? 'Desconocido'),
                'is_temp_id' => $emg->is_temp_id,
                'tipo_ingreso' => $emg->tipo_ingreso,
                'tipo_ingreso_label' => $emg->tipo_ingreso_label,
                'destino_inicial' => $emg->destino_inicial,
                'hora_ingreso' => $emg->admission_date?->format('H:i') ?? $emg->created_at->format('H:i'),
                'status' => $emg->status,
                'status_label' => $this->getStatusLabel($emg->status),
            ];
        });

        $stats = [
            'activos' => Emergency::where('ubicacion_actual', 'emergencia')->whereIn('status', ['recibido', 'en_evaluacion', 'estabilizado'])->count(),
            'espera' => Emergency::where('status', 'recibido')->where('ubicacion_actual', 'emergencia')->count(),
            'atencion' => Emergency::where('status', 'en_evaluacion')->where('ubicacion_actual', 'emergencia')->count(),
            'hoy' => Emergency::whereDate('created_at', today())->count(),
        ];

        return response()->json([
            'success' => true,
            'emergencias' => $emergencias,
            'stats' => $stats
        ]);
    }

    /**
     * API: Get statistics
     */
    public function apiEstadisticas(): JsonResponse
    {
        $stats = [
            'activos' => Emergency::where('ubicacion_actual', 'emergencia')->whereIn('status', ['recibido', 'en_evaluacion', 'estabilizado'])->count(),
            'espera' => Emergency::where('status', 'recibido')->where('ubicacion_actual', 'emergencia')->count(),
            'atencion' => Emergency::where('status', 'en_evaluacion')->where('ubicacion_actual', 'emergencia')->count(),
            'hoy' => Emergency::whereDate('created_at', today())->count(),
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }

    /**
     * API: Update status
     */
    public function updateStatus(Request $request, Emergency $emergency): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:recibido,en_evaluacion,estabilizado,uti,cirugia,alta,fallecido',
        ]);

        $estadoAnterior = $emergency->status;
        $emergency->update(['status' => $validated['status']]);

        // Registrar en flujo historial
        $emergency->registrarMovimiento('emergencia', 'emergencia', 'Cambio de estado: ' . $estadoAnterior . ' → ' . $validated['status']);

        // Registrar en auditoría de actividades
        ActivityLogService::log(
            'cambio_estado_paciente',
            'Paciente ' . ($emergency->paciente?->nombre ?? 'Temporal') . ' - Cambio de estado: ' . $estadoAnterior . ' → ' . $validated['status'],
            $emergency,
            ['status' => $estadoAnterior],
            ['status' => $validated['status']]
        );

        return response()->json([
            'success' => true,
            'message' => 'Estado actualizado correctamente',
            'status' => $emergency->status,
            'status_color' => $emergency->status_color,
        ]);
    }

    /**
     * API: Derivar a otro módulo
     */
    public function derivar(Request $request, Emergency $emergency): JsonResponse
    {
        try {
            $validated = $request->validate([
                'destino' => 'required|in:cirugia,hospitalizacion',
                'forzar' => 'nullable|boolean',
            ]);

            $destino = $validated['destino'];
            $forzar = $request->boolean('forzar');

            // Validar disponibilidad de recursos
            if (!$forzar) {
                $validacion = $this->validarRecursos($destino);
                if (!$validacion['disponible']) {
                    return response()->json([
                        'success' => false,
                        'requiere_confirmacion' => true,
                        'message' => $validacion['mensaje'],
                    ]);
                }
            }

            $ubicacionAnterior = $emergency->ubicacion_actual;

            // Actualizar según destino
            switch ($destino) {
                case 'cirugia':
                    $emergency->update([
                        'status' => 'cirugia',
                        'ubicacion_actual' => 'cirugia',
                        'nro_cirugia' => $this->generarNroCirugia($emergency),
                    ]);
                    break;
                case 'hospitalizacion':
                    $nroHosp = $this->generarNroHospitalizacion($emergency);
                    $emergency->update([
                        'status'             => 'hospitalizacion',
                        'ubicacion_actual'   => 'hospitalizacion',
                        'nro_hospitalizacion'=> $nroHosp,
                    ]);

                    // Crear registro en hospitalizaciones
                    $ciPaciente = $emergency->is_temp_id ? null : $emergency->patient_id;
                    Hospitalizacion::create([
                        'id'           => $nroHosp,
                        'ci_paciente'  => $ciPaciente,
                        'fecha_ingreso'=> now(),
                        'estado'       => 'activo',
                        'diagnostico'  => $emergency->initial_assessment,
                        'nro_emergencia'=> $emergency->id,
                    ]);

                    // Vincular a cuenta maestra (reutiliza la de emergencia si existe)
                    if ($ciPaciente) {
                        \App\Services\CuentaCobroService::obtenerOCrearCuentaMaestra(
                            $ciPaciente,
                            'internacion'
                        );
                    }
                    break;

            }

            // Registrar movimiento
            $emergency->registrarMovimiento($ubicacionAnterior, $destino, 'Derivación desde emergencia');

            // Registrar en auditoría
            ActivityLogService::log(
                'derivar_paciente',
                'Paciente ' . ($emergency->paciente?->nombre ?? 'Temporal') . ' derivado de ' . $ubicacionAnterior . ' a ' . $destino,
                $emergency,
                ['ubicacion_actual' => $ubicacionAnterior, 'status' => $emergency->status],
                ['ubicacion_actual' => $destino, 'status' => $destino]
            );

            return response()->json([
                'success' => true,
                'message' => 'Paciente derivado correctamente a ' . $destino,
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación: ' . $e->getMessage(),
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Error al derivar paciente: ' . $e->getMessage(), [
                'emergency_id' => $emergency->id ?? 'unknown',
                'destino' => $request->input('destino'),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al derivar paciente: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * API: Dar de alta
     */
    public function darAlta(Emergency $emergency): JsonResponse
    {
        $ubicacionAnterior = $emergency->ubicacion_actual;

        $emergency->update([
            'status' => 'alta',
            'ubicacion_actual' => 'alta',
            'discharge_date' => now(),
        ]);

        $emergency->registrarMovimiento($ubicacionAnterior, 'alta', 'Paciente dado de alta');

        // Registrar en auditoría
        ActivityLogService::log(
            'dar_alta_paciente',
            'Paciente ' . ($emergency->paciente?->nombre ?? 'Temporal') . ' dado de alta desde ' . $ubicacionAnterior,
            $emergency,
            ['status' => $emergency->getOriginal('status'), 'ubicacion_actual' => $ubicacionAnterior],
            ['status' => 'alta', 'ubicacion_actual' => 'alta']
        );

        return response()->json([
            'success' => true,
            'message' => 'Paciente dado de alta correctamente',
        ]);
    }

    /**
     * Validar disponibilidad de recursos
     */
    private function validarRecursos(string $destino): array
    {
        switch ($destino) {
            case 'cirugia':
                $quirofanosDisponibles = Quirofano::where('estado', 'disponible')->count();
                if ($quirofanosDisponibles === 0) {
                    return [
                        'disponible' => false,
                        'mensaje' => 'No hay quirófanos disponibles en este momento.'
                    ];
                }
                return ['disponible' => true];

            case 'hospitalizacion':
                $camasHosp = DB::table('camas')->where('disponibilidad', 'disponible')->count();
                if ($camasHosp === 0) {
                    return [
                        'disponible' => false,
                        'mensaje' => 'No hay camas disponibles en hospitalización.'
                    ];
                }
                return ['disponible' => true];

            default:
                return ['disponible' => true];
        }
    }

    /**
     * Generar número de cirugía
     */
    private function generarNroCirugia(Emergency $emergency): string
    {
        return 'CIR-' . now()->format('Ymd') . '-' . str_pad($emergency->id, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Generar número de hospitalización
     */
    private function generarNroHospitalizacion(Emergency $emergency): string
    {
        return 'HOSP-' . now()->format('Ymd') . '-' . str_pad($emergency->id, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get status label
     */
    private function getStatusLabel(string $status): string
    {
        return match($status) {
            'recibido' => 'Recibido',
            'en_evaluacion' => 'En Evaluación',
            'estabilizado' => 'Estabilizado',
            'cirugia' => 'En Cirugía',
            'uti' => 'En UTI',
            'alta' => 'Dado de Alta',
            'fallecido' => 'Fallecido',
            default => $status,
        };
    }

    public function create(): View
    {
        return view('emergency-staff.create');
    }

    public function show(Emergency $emergency): View
    {
        return view('emergency-staff.show', compact('emergency'));
    }

    public function edit(Emergency $emergency): View
    {
        return view('emergency-staff.edit', compact('emergency'));
    }

    /**
     * API: Obtener medicamentos disponibles en almacén de emergencia
     */
    public function apiMedicamentosDisponibles(Request $request): JsonResponse
    {
        $query = AlmacenStock::porUbicacion('emergencia')
            ->where('cantidad_actual', '>', 0)
            ->whereHas('lote.catalogo', fn($q) => $q->activos())
            ->with('lote.catalogo');

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->whereHas('lote.catalogo', function($q) use ($buscar) {
                $q->where('nombre', 'like', '%' . $buscar . '%')
                  ->orWhere('descripcion', 'like', '%' . $buscar . '%');
            });
        }

        $medicamentos = $query->limit(20)->get()
            ->map(function($stock) {
                $c = $stock->lote->catalogo;
                return [
                    'id'                 => $stock->id,
                    'nombre'             => $c->nombre,
                    'descripcion'        => $c->descripcion,
                    'tipo'               => $c->tipo,
                    'tipo_label'         => ucfirst($c->tipo),
                    'cantidad_disponible' => $stock->cantidad_actual,
                    'unidad_medida'      => $c->unidad_medida,
                    'precio'             => $stock->lote->precio_venta,
                    'stock_minimo'       => $stock->stock_minimo,
                    'esta_bajo_stock'    => $stock->cantidad_actual <= $stock->stock_minimo,
                ];
            });

        return response()->json([
            'success' => true,
            'medicamentos' => $medicamentos,
            'total' => $medicamentos->count(),
        ]);
    }

    /**
     * API: Obtener emergencias con ID temporal
     */
    public function apiEmergenciasTemporales(): JsonResponse
    {
        $emergencias = Emergency::where('is_temp_id', true)
            ->whereNotIn('status', ['alta', 'fallecido'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($emg) {
                return [
                    'id' => $emg->id,
                    'code' => $emg->code,
                    'temp_id' => $emg->temp_id,
                    'tipo_ingreso' => $emg->tipo_ingreso,
                    'tipo_ingreso_label' => $emg->tipo_ingreso_label,
                    'status' => $emg->status,
                    'status_label' => $this->getStatusLabel($emg->status),
                    'ubicacion_actual' => $emg->ubicacion_actual,
                    'hora_ingreso' => $emg->admission_date?->format('H:i') ?? $emg->created_at->format('H:i'),
                    'fecha_ingreso' => $emg->admission_date?->format('d/m/Y') ?? $emg->created_at->format('d/m/Y'),
                ];
            });

        return response()->json([
            'success' => true,
            'emergencias' => $emergencias,
            'total' => $emergencias->count(),
        ]);
    }

    /**
     * Mostrar historial completo de evaluaciones de una emergencia
     */
    public function historial(Emergency $emergency): View
    {
        // Cargar relaciones necesarias
        $emergency->load(['paciente', 'user']);

        // Decodificar datos almacenados
        $detalleCostos = $emergency->detalle_costos ?? [];
        $flujoHistorial = $emergency->flujo_historial ?? [];
        $vitalSigns = [];

        if ($emergency->vital_signs) {
            $vitalSigns = is_string($emergency->vital_signs)
                ? json_decode($emergency->vital_signs, true)
                : $emergency->vital_signs;
        }

        // Obtener cuenta de cobro asociada si existe
        $cuenta = \App\Models\CuentaCobro::where('referencia_id', $emergency->id)
            ->where('referencia_type', Emergency::class)
            ->with('detalles')
            ->first();

        // Cargar nombres de usuarios que aplicaron medicamentos
        $usuariosMedicamentos = [];
        $userIds = [];
        foreach ($detalleCostos as $evaluacion) {
            if (isset($evaluacion['usuario_id'])) {
                $userIds[] = $evaluacion['usuario_id'];
            }
        }
        $userIds = array_unique($userIds);
        if (!empty($userIds)) {
            $usuarios = \App\Models\User::whereIn('id', $userIds)->pluck('name', 'id');
            $usuariosMedicamentos = $usuarios->toArray();
        }

        return view('emergency-staff.historial', compact(
            'emergency',
            'detalleCostos',
            'flujoHistorial',
            'vitalSigns',
            'cuenta',
            'usuariosMedicamentos'
        ));
    }

    /**
     * API: Get current nurse permissions
     */
    public function apiPermisos(): JsonResponse
    {
        $user = auth()->user();

        // If user is enfermera-emergencia, get their individual permissions
        if ($user->isEnfermeraEmergencia()) {
            $enfermera = \App\Models\Enfermera::where('user_id', $user->id)->first();

            if ($enfermera) {
                $permissions = $enfermera->getPermissionKeys();
            } else {
                $permissions = [];
            }

            return response()->json([
                'success' => true,
                'role' => 'enfermera-emergencia',
                'permissions' => $permissions,
                'all_permissions' => array_keys(\App\Models\EnfermeraPermission::AVAILABLE_PERMISSIONS),
            ]);
        }

        // For emergencia, admin, dirmedico - they have all permissions implicitly
        return response()->json([
            'success' => true,
            'role' => $user->role,
            'permissions' => array_keys(\App\Models\EnfermeraPermission::AVAILABLE_PERMISSIONS), // All permissions
            'all_permissions' => array_keys(\App\Models\EnfermeraPermission::AVAILABLE_PERMISSIONS),
        ]);
    }

    /**
     * Mostrar historial general de todas las emergencias
     */
    public function historialGeneral(Request $request): View
    {
        $query = Emergency::with(['paciente']);

        // Filtro por fecha desde
        if ($request->filled('fecha_desde')) {
            $query->whereDate('admission_date', '>=', $request->fecha_desde);
        }

        // Filtro por fecha hasta
        if ($request->filled('fecha_hasta')) {
            $query->whereDate('admission_date', '<=', $request->fecha_hasta);
        }

        // Filtro por estado
        if ($request->filled('estado') && $request->estado !== 'todos') {
            $query->where('status', $request->estado);
        }

        $emergencias = $query->orderBy('admission_date', 'desc')
            ->paginate(25)
            ->withQueryString();

        // Estadísticas
        $stats = [
            'total' => Emergency::count(),
            'hoy' => Emergency::whereDate('created_at', today())->count(),
            'activos' => Emergency::whereIn('status', ['recibido', 'en_evaluacion', 'estabilizado'])->count(),
            'alta' => Emergency::where('status', 'alta')->count(),
            'cirugia' => Emergency::where('status', 'cirugia')->count(),
            'uti' => Emergency::where('status', 'uti')->count(),
        ];

        return view('emergency-staff.historial-general', compact('emergencias', 'stats'));
    }

    /**
     * Exportar historial de emergencias a Excel (CSV)
     */
    public function exportHistorialGeneral(Request $request)
    {
        $query = Emergency::with(['paciente']);

        // Aplicar mismos filtros que en historialGeneral
        if ($request->filled('fecha_desde')) {
            $query->whereDate('admission_date', '>=', $request->fecha_desde);
        }
        if ($request->filled('fecha_hasta')) {
            $query->whereDate('admission_date', '<=', $request->fecha_hasta);
        }
        if ($request->filled('estado') && $request->estado !== 'todos') {
            $query->where('status', $request->estado);
        }

        $emergencias = $query->orderBy('admission_date', 'desc')->get();

        // Generar nombre de archivo
        $filename = 'historial_emergencias_' . now()->format('Y-m-d_H-i-s') . '.csv';

        // Headers para descarga
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $file = fopen('php://output', 'w');

        // BOM para Excel
        fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

        // Título
        fputcsv($file, ['HISTORIAL DE EMERGENCIAS'], ';');
        fputcsv($file, [], ';');

        // Encabezados
        fputcsv($file, [
            'Código',
            'Paciente',
            'CI',
            'Fecha Ingreso',
            'Hora',
            'Tipo Ingreso',
            'Estado',
            'Destino',
            'Costo Total',
            'Ubicación Actual'
        ], ';');

        // Datos
        foreach ($emergencias as $emg) {
            fputcsv($file, [
                $emg->code,
                $emg->is_temp_id ? 'Paciente Temporal' : ($emg->paciente?->nombre ?? 'Desconocido'),
                $emg->patient_id,
                $emg->admission_date?->format('d/m/Y') ?? $emg->created_at->format('d/m/Y'),
                $emg->admission_date?->format('H:i') ?? $emg->created_at->format('H:i'),
                $emg->tipo_ingreso_label,
                $this->getStatusLabel($emg->status),
                $emg->destino_inicial ?? 'Pendiente',
                'Bs. ' . number_format($emg->cost ?? 0, 2),
                $emg->ubicacion_actual ?? 'emergencia'
            ], ';');
        }

        fclose($file);
        exit;
    }
}
