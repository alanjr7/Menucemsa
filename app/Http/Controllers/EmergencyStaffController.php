<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Emergency;
use App\Models\Paciente;
use App\Models\Quirofano;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Models\AlmacenMedicamento;
use App\Services\CuentaCobroService;

class EmergencyStaffController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:emergencia|admin|dirmedico')->except(['apiEmergenciasTemporales']);
    }

    public function index(): View
    {
        return view('emergency-staff.dashboard');
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
        $validated = $request->validate([
            'destino' => 'required|in:cirugia,uti,hospitalizacion,observacion,alta',
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
        $statusAnterior = $emergency->status;

        // Actualizar según destino
        switch ($destino) {
            case 'cirugia':
                $emergency->update([
                    'status' => 'cirugia',
                    'ubicacion_actual' => 'cirugia',
                    'nro_cirugia' => $this->generarNroCirugia($emergency),
                ]);
                break;
            case 'uti':
                $emergency->update([
                    'status' => 'uti',
                    'ubicacion_actual' => 'uti',
                    'nro_uti' => $this->generarNroUti($emergency),
                ]);
                break;
            case 'hospitalizacion':
                $emergency->update([
                    'status' => 'hospitalizacion',
                    'ubicacion_actual' => 'hospitalizacion',
                    'nro_hospitalizacion' => $this->generarNroHospitalizacion($emergency),
                ]);
                break;
            case 'observacion':
                $emergency->update([
                    'status' => 'estabilizado',
                    'ubicacion_actual' => 'observacion',
                ]);
                break;
            case 'alta':
                $emergency->update([
                    'status' => 'alta',
                    'ubicacion_actual' => 'alta',
                    'discharge_date' => now(),
                ]);
                break;
        }

        // Registrar movimiento
        $emergency->registrarMovimiento($ubicacionAnterior, $destino, 'Derivación desde emergencia');

        return response()->json([
            'success' => true,
            'message' => 'Paciente derivado correctamente a ' . $destino,
        ]);
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

            case 'uti':
                // Verificar camas UTI disponibles
                $camasUti = DB::table('camas')->where('area', 'UTI')->where('estado', 'disponible')->count();
                if ($camasUti === 0) {
                    return [
                        'disponible' => false,
                        'mensaje' => 'No hay camas disponibles en UTI.'
                    ];
                }
                return ['disponible' => true];

            case 'hospitalizacion':
                $camasHosp = DB::table('camas')->where('area', 'hospitalizacion')->where('estado', 'disponible')->count();
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
     * Generar número de UTI
     */
    private function generarNroUti(Emergency $emergency): string
    {
        return 'UTI-' . now()->format('Ymd') . '-' . str_pad($emergency->id, 4, '0', STR_PAD_LEFT);
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

    public function pending(): View
    {
        return view('emergency-staff.pending');
    }

    /**
     * Mostrar formulario de evaluación de emergencia
     */
    public function evaluacion(Emergency $emergency): View
    {
        // Cargar medicamentos disponibles del área de emergencia
        $medicamentos = AlmacenMedicamento::activos()
            ->porArea('emergencia')
            ->where('cantidad', '>', 0)
            ->orderBy('nombre')
            ->get();

        // Decodificar signos vitales actuales si existen
        $vitalSigns = [];
        if ($emergency->vital_signs) {
            $vitalSigns = is_string($emergency->vital_signs)
                ? json_decode($emergency->vital_signs, true)
                : $emergency->vital_signs;
        }

        return view('emergency-staff.evaluacion', compact('emergency', 'medicamentos', 'vitalSigns'));
    }

    /**
     * Guardar evaluación de emergencia con signos vitales, gravedad y medicamentos
     */
    public function guardarEvaluacion(Request $request, Emergency $emergency): JsonResponse
    {
        $validated = $request->validate([
            'presion_arterial' => 'nullable|string|max:20',
            'frecuencia_cardiaca' => 'nullable|string|max:20',
            'frecuencia_respiratoria' => 'nullable|string|max:20',
            'temperatura' => 'nullable|string|max:20',
            'saturacion_o2' => 'nullable|string|max:20',
            'glucosa' => 'nullable|string|max:20',
            'nivel_gravedad' => 'required|in:leve,moderado,grave,critico',
            'motivo_consulta' => 'nullable|string',
            'observaciones' => 'nullable|string',
            'medicamentos' => 'nullable|array',
            'medicamentos.*.id' => 'required_with:medicamentos|exists:almacen_medicamentos,id',
            'medicamentos.*.cantidad' => 'required_with:medicamentos|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            // 1. Actualizar signos vitales
            $vitalSigns = [
                'presion_arterial' => $validated['presion_arterial'] ?? null,
                'frecuencia_cardiaca' => $validated['frecuencia_cardiaca'] ?? null,
                'frecuencia_respiratoria' => $validated['frecuencia_respiratoria'] ?? null,
                'temperatura' => $validated['temperatura'] ?? null,
                'saturacion_o2' => $validated['saturacion_o2'] ?? null,
                'glucosa' => $validated['glucosa'] ?? null,
                'fecha_registro' => now()->toDateTimeString(),
            ];

            // 2. Actualizar estado a "en_evaluacion"
            $estadoAnterior = $emergency->status;
            $emergency->update([
                'status' => 'en_evaluacion',
                'vital_signs' => $vitalSigns,
                'initial_assessment' => $validated['motivo_consulta'] ?? $emergency->initial_assessment,
                'observations' => $validated['observaciones'] ?? $emergency->observations,
            ]);

            // Registrar en flujo historial
            $emergency->registrarMovimiento('emergencia', 'emergencia',
                'Inicio de evaluación médica. Gravedad: ' . strtoupper($validated['nivel_gravedad']));

            // 3. Procesar medicamentos seleccionados
            $medicamentosAplicados = [];
            $totalMedicamentos = 0;

            if (!empty($validated['medicamentos'])) {
                foreach ($validated['medicamentos'] as $med) {
                    $medicamento = AlmacenMedicamento::find($med['id']);

                    if ($medicamento && $medicamento->cantidad >= $med['cantidad']) {
                        // Descontar del inventario
                        $cantidadAnterior = $medicamento->cantidad;
                        $medicamento->cantidad -= $med['cantidad'];
                        $medicamento->save();

                        // Registrar uso
                        $medicamentosAplicados[] = [
                            'id' => $medicamento->id,
                            'nombre' => $medicamento->nombre,
                            'cantidad' => $med['cantidad'],
                            'precio_unitario' => $medicamento->precio ?? 0,
                            'subtotal' => ($medicamento->precio ?? 0) * $med['cantidad'],
                            'unidad_medida' => $medicamento->unidad_medida,
                        ];

                        $totalMedicamentos += ($medicamento->precio ?? 0) * $med['cantidad'];

                        // Log del consumo
                        \Log::info('Medicamento aplicado en emergencia', [
                            'emergency_id' => $emergency->id,
                            'medicamento_id' => $medicamento->id,
                            'medicamento_nombre' => $medicamento->nombre,
                            'cantidad' => $med['cantidad'],
                            'stock_anterior' => $cantidadAnterior,
                            'stock_nuevo' => $medicamento->cantidad,
                            'usuario_id' => auth()->id(),
                        ]);
                    }
                }
            }

            // 4. Crear o actualizar cuenta de cobro para la emergencia
            // Para pacientes temporales, usar el ID de emergencia como identificador numérico
            // ya que paciente_ci en BD es integer y temp_id es string
            $pacienteCi = $emergency->is_temp_id
                ? (int) $emergency->id  // Usar ID de emergencia como identificador numérico
                : (int) $emergency->patient_id;

            $cuenta = CuentaCobroService::obtenerOCrearCuentaEmergencia(
                $pacienteCi,
                $emergency->id
            );

            // Agregar cargos por medicamentos a la cuenta
            if (!empty($medicamentosAplicados)) {
                foreach ($medicamentosAplicados as $med) {
                    if ($med['subtotal'] > 0) {
                        CuentaCobroService::agregarCargo(
                            $cuenta->id,
                            'medicamento',
                            'Emergencia - ' . $med['nombre'] . ' (' . $med['cantidad'] . ' ' . $med['unidad_medida'] . ')',
                            $med['precio_unitario'],
                            $med['cantidad'],
                            null,
                            AlmacenMedicamento::class,
                            $med['id']
                        );
                    }
                }
            }

            // 5. Actualizar costo total de la emergencia
            $emergency->update([
                'cost' => $cuenta->total_calculado,
                'detalle_costos' => array_merge($emergency->detalle_costos ?? [], [
                    [
                        'tipo' => 'evaluacion',
                        'fecha' => now()->toDateTimeString(),
                        'nivel_gravedad' => $validated['nivel_gravedad'],
                        'medicamentos' => $medicamentosAplicados,
                        'total_medicamentos' => $totalMedicamentos,
                        'usuario_id' => auth()->id(),
                    ]
                ]),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Evaluación guardada correctamente',
                'emergency_id' => $emergency->id,
                'status' => $emergency->status,
                'medicamentos_aplicados' => count($medicamentosAplicados),
                'total_medicamentos' => $totalMedicamentos,
                'redirect' => route('emergency-staff.dashboard'),
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Error al guardar evaluación de emergencia: ' . $e->getMessage(), [
                'emergency_id' => $emergency->id,
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al guardar la evaluación: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * API: Obtener medicamentos disponibles en almacén de emergencia
     */
    public function apiMedicamentosDisponibles(): JsonResponse
    {
        $medicamentos = AlmacenMedicamento::activos()
            ->porArea('emergencia')
            ->where('cantidad', '>', 0)
            ->orderBy('nombre')
            ->get()
            ->map(function($med) {
                return [
                    'id' => $med->id,
                    'nombre' => $med->nombre,
                    'descripcion' => $med->descripcion,
                    'tipo' => $med->tipo,
                    'tipo_label' => $med->tipo_label,
                    'cantidad_disponible' => $med->cantidad,
                    'unidad_medida' => $med->unidad_medida,
                    'precio' => $med->precio,
                    'stock_minimo' => $med->stock_minimo,
                    'esta_bajo_stock' => $med->estaBajoStock(),
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

        return view('emergency-staff.historial', compact(
            'emergency',
            'detalleCostos',
            'flujoHistorial',
            'vitalSigns',
            'cuenta'
        ));
    }
}
