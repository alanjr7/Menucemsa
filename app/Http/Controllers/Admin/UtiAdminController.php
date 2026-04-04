<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use App\Models\UtiBed;
use App\Models\UtiAdmission;
use App\Models\UtiTarifario;
use App\Models\UtiMedication;
use App\Models\UtiSupply;
use App\Models\UtiCatering;
use App\Services\CuentaCobroService;

class UtiAdminController extends Controller
{
    protected $cuentaCobroService;

    public function __construct(CuentaCobroService $cuentaCobroService)
    {
        $this->cuentaCobroService = $cuentaCobroService;
        $this->middleware(['auth', 'role:admin']);
    }

    /**
     * Dashboard administrativo UTI
     */
    public function index(): View
    {
        return view('uti.admin.dashboard');
    }

    /**
     * Vista de gestión de camas
     */
    public function camas(): View
    {
        return view('uti.admin.dashboard');
    }

    /**
     * Vista de control financiero
     */
    public function controlFinanciero(): View
    {
        return view('uti.admin.dashboard');
    }

    /**
     * Vista de tarifario UTI
     */
    public function tarifario(): View
    {
        return view('uti.admin.dashboard');
    }

    /**
     * API: Obtener estadísticas del dashboard
     */
    public function getEstadisticas(): JsonResponse
    {
        $totalCamas = UtiBed::where('activa', true)->count();
        $camasOcupadas = UtiBed::where('status', 'ocupada')->where('activa', true)->count();
        $camasDisponibles = UtiBed::where('status', 'disponible')->where('activa', true)->count();
        $camasMantenimiento = UtiBed::where('status', 'mantenimiento')->where('activa', true)->count();

        $pacientesActivos = UtiAdmission::where('estado', 'activo')->count();
        $pacientesAltaClinica = UtiAdmission::where('estado', 'alta_clinica')->count();

        // Estadías prolongadas (>7 días)
        $estanciasProlongadas = UtiAdmission::where('estado', 'activo')
            ->whereRaw('DATEDIFF(NOW(), fecha_ingreso) > 7')
            ->count();

        // Pacientes sin preautorización
        $sinPreautorizacion = UtiAdmission::where('estado', 'activo')
            ->where('tipo_pago', 'seguro')
            ->whereNull('nro_autorizacion')
            ->count();

        // Días sin registro clínico
        $hoy = today()->toDateString();
        $admisionesActivas = UtiAdmission::where('estado', 'activo')->pluck('id');
        $conRegistroHoy = DB::table('uti_daily_records')
            ->where('fecha', $hoy)
            ->whereIn('uti_admission_id', $admisionesActivas)
            ->pluck('uti_admission_id');
        $sinRegistroHoy = $admisionesActivas->diff($conRegistroHoy)->count();

        return response()->json([
            'success' => true,
            'camas' => [
                'total' => $totalCamas,
                'ocupadas' => $camasOcupadas,
                'disponibles' => $camasDisponibles,
                'mantenimiento' => $camasMantenimiento,
                'ocupacion_porcentaje' => $totalCamas > 0 ? round(($camasOcupadas / $totalCamas) * 100, 1) : 0,
            ],
            'pacientes' => [
                'activos' => $pacientesActivos,
                'alta_clinica' => $pacientesAltaClinica,
                'total_ingresados' => $pacientesActivos + $pacientesAltaClinica,
            ],
            'alertas' => [
                'estancias_prolongadas' => $estanciasProlongadas,
                'sin_preautorizacion' => $sinPreautorizacion,
                'sin_registro_hoy' => $sinRegistroHoy,
            ],
        ]);
    }

    /**
     * API: Obtener grid de camas
     */
    public function getCamasGrid(): JsonResponse
    {
        $camas = UtiBed::where('activa', true)
            ->with(['admission.paciente'])
            ->orderBy('bed_number')
            ->get()
            ->map(function($cama) {
                $admission = $cama->admission;
                return [
                    'id' => $cama->id,
                    'numero' => $cama->bed_number,
                    'tipo' => $cama->tipo,
                    'status' => $cama->status,
                    'status_color' => $cama->status_color,
                    'status_label' => $cama->status_label,
                    'precio_dia' => $cama->precio_dia,
                    'paciente' => $admission ? [
                        'id' => $admission->id,
                        'ci' => $admission->paciente?->ci,
                        'nombre' => $admission->paciente?->nombre,
                        'estado_clinico' => $admission->estado_clinico,
                        'estado_clinico_color' => $admission->estado_clinico_color,
                        'dias_en_uti' => $admission->dias_en_uti,
                        'diagnostico' => $admission->diagnostico_principal,
                    ] : null,
                ];
            });

        return response()->json([
            'success' => true,
            'camas' => $camas,
        ]);
    }

    /**
     * API: Obtener lista de pacientes con filtros
     */
    public function getPacientes(Request $request): JsonResponse
    {
        $query = UtiAdmission::with(['paciente', 'bed', 'medico', 'seguro'])
            ->whereIn('estado', ['activo', 'alta_clinica']);

        // Filtros
        if ($request->has('estado_clinico') && $request->estado_clinico !== 'todos') {
            $query->where('estado_clinico', $request->estado_clinico);
        }

        if ($request->has('dias_estancia') && $request->dias_estancia !== 'todos') {
            $dias = (int) $request->dias_estancia;
            $query->whereRaw('DATEDIFF(NOW(), fecha_ingreso) >= ?', [$dias]);
        }

        if ($request->has('tipo_pago') && $request->tipo_pago !== 'todos') {
            $query->where('tipo_pago', $request->tipo_pago);
        }

        if ($request->has('estado') && $request->estado !== 'todos') {
            $query->where('estado', $request->estado);
        }

        $pacientes = $query->orderBy('fecha_ingreso', 'desc')
            ->get()
            ->map(function($adm) {
                return [
                    'id' => $adm->id,
                    'nro_ingreso' => $adm->nro_ingreso,
                    'paciente' => [
                        'ci' => $adm->paciente?->ci,
                        'nombre' => $adm->paciente?->nombre,
                        'telefono' => $adm->paciente?->telefono,
                    ],
                    'cama' => $adm->bed?->bed_number ?? 'Sin cama',
                    'estado_clinico' => $adm->estado_clinico,
                    'estado_clinico_color' => $adm->estado_clinico_color,
                    'tipo_pago' => $adm->tipo_pago,
                    'seguro' => $adm->seguro?->nombre ?? 'Particular',
                    'nro_autorizacion' => $adm->nro_autorizacion,
                    'dias_en_uti' => $adm->dias_en_uti,
                    'estado' => $adm->estado,
                    'medico' => $adm->medico?->nombre ?? 'No asignado',
                    'fecha_ingreso' => $adm->fecha_ingreso?->format('d/m/Y H:i'),
                ];
            });

        return response()->json([
            'success' => true,
            'pacientes' => $pacientes,
        ]);
    }

    /**
     * API: Obtener costos por paciente
     */
    public function getCostosPaciente($admissionId): JsonResponse
    {
        $admission = UtiAdmission::with([
            'paciente',
            'bed',
            'medicaments.medicamento',
            'supplies.insumo',
            'catering',
        ])->findOrFail($admissionId);

        // Calcular costo por estadía
        $diasEstadia = $admission->dias_en_uti;
        $precioDia = $admission->bed?->precio_dia ?? 0;
        $costoEstadia = $diasEstadia * $precioDia;

        // Costo de medicamentos
        $medicamentos = $admission->medicaments->map(function($med) {
            $precioUnitario = $med->medicamento?->precio ?? 0;
            return [
                'nombre' => $med->medicamento?->nombre,
                'dosis' => $med->dosis . ' ' . $med->unidad,
                'fecha' => $med->fecha->format('d/m/Y'),
                'costo' => $precioUnitario * $med->dosis,
            ];
        });
        $costoMedicamentos = $medicamentos->sum('costo');

        // Costo de insumos
        $insumos = $admission->supplies->map(function($sup) {
            $precioUnitario = $sup->insumo?->precio ?? 0;
            return [
                'nombre' => $sup->insumo?->nombre,
                'cantidad' => $sup->cantidad,
                'fecha' => $sup->fecha->format('d/m/Y'),
                'costo' => $precioUnitario * $sup->cantidad,
            ];
        });
        $costoInsumos = $insumos->sum('costo');

        // Costo de alimentación
        $tarifaAlimentacion = UtiTarifario::where('tipo', 'alimentacion')
            ->where('activo', true)
            ->first();
        $precioComida = $tarifaAlimentacion?->precio ?? 0;
        $comidasDadas = $admission->catering->where('estado', 'dado')->count();
        $costoAlimentacion = $comidasDadas * $precioComida;

        $total = $costoEstadia + $costoMedicamentos + $costoInsumos + $costoAlimentacion;

        return response()->json([
            'success' => true,
            'paciente' => [
                'nombre' => $admission->paciente?->nombre,
                'ci' => $admission->paciente?->ci,
            ],
            'costos' => [
                'estadia' => [
                    'dias' => $diasEstadia,
                    'precio_dia' => $precioDia,
                    'total' => $costoEstadia,
                ],
                'medicamentos' => [
                    'items' => $medicamentos,
                    'total' => $costoMedicamentos,
                ],
                'insumos' => [
                    'items' => $insumos,
                    'total' => $costoInsumos,
                ],
                'alimentacion' => [
                    'comidas' => $comidasDadas,
                    'precio_comida' => $precioComida,
                    'total' => $costoAlimentacion,
                ],
            ],
            'total_general' => $total,
        ]);
    }

    /**
     * API: Crear nueva cama
     */
    public function crearCama(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'bed_number' => 'required|string|max:10|unique:uti_beds,bed_number',
            'tipo' => 'required|string|max:50',
            'equipamiento' => 'nullable|string|max:500',
            'precio_dia' => 'required|numeric|min:0',
        ]);

        $validated['status'] = 'disponible';
        $validated['activa'] = true;

        $cama = UtiBed::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Cama creada correctamente',
            'cama' => $cama,
        ]);
    }

    /**
     * API: Actualizar cama
     */
    public function actualizarCama(Request $request, $id): JsonResponse
    {
        $cama = UtiBed::findOrFail($id);

        $validated = $request->validate([
            'tipo' => 'required|string|max:50',
            'equipamiento' => 'nullable|string|max:500',
            'precio_dia' => 'required|numeric|min:0',
            'activa' => 'boolean',
        ]);

        $cama->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Cama actualizada correctamente',
            'cama' => $cama,
        ]);
    }

    /**
     * API: Cambiar estado de cama
     */
    public function cambiarEstadoCama(Request $request, $id): JsonResponse
    {
        $cama = UtiBed::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:disponible,mantenimiento,reservada',
        ]);

        if ($cama->status === 'ocupada') {
            return response()->json([
                'success' => false,
                'message' => 'No se puede cambiar el estado de una cama ocupada',
            ], 422);
        }

        $cama->update(['status' => $validated['status']]);

        return response()->json([
            'success' => true,
            'message' => 'Estado de cama actualizado correctamente',
        ]);
    }

    /**
     * API: Obtener tarifario UTI
     */
    public function getTarifario(): JsonResponse
    {
        $tarifas = UtiTarifario::orderBy('tipo')
            ->orderBy('concepto')
            ->get();

        return response()->json([
            'success' => true,
            'tarifas' => $tarifas,
        ]);
    }

    /**
     * API: Crear tarifa
     */
    public function crearTarifa(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'concepto' => 'required|string|max:100',
            'tipo' => 'required|in:estadia,alimentacion,procedimiento,insumo,medicamento',
            'precio' => 'required|numeric|min:0',
            'unidad' => 'required|string|max:20',
            'descripcion' => 'nullable|string|max:500',
        ]);

        $validated['activo'] = true;

        $tarifa = UtiTarifario::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Tarifa creada correctamente',
            'tarifa' => $tarifa,
        ]);
    }

    /**
     * API: Actualizar tarifa
     */
    public function actualizarTarifa(Request $request, $id): JsonResponse
    {
        $tarifa = UtiTarifario::findOrFail($id);

        $validated = $request->validate([
            'concepto' => 'required|string|max:100',
            'tipo' => 'required|in:estadia,alimentacion,procedimiento,insumo,medicamento',
            'precio' => 'required|numeric|min:0',
            'unidad' => 'required|string|max:20',
            'descripcion' => 'nullable|string|max:500',
            'activo' => 'boolean',
        ]);

        $tarifa->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Tarifa actualizada correctamente',
            'tarifa' => $tarifa,
        ]);
    }

    /**
     * API: Obtener alertas
     */
    public function getAlertas(): JsonResponse
    {
        $alertas = [];

        // Estancias prolongadas (>10 días)
        $estanciasProlongadas = UtiAdmission::where('estado', 'activo')
            ->whereRaw('DATEDIFF(NOW(), fecha_ingreso) > 10')
            ->with('paciente')
            ->get();

        foreach ($estanciasProlongadas as $adm) {
            $alertas[] = [
                'tipo' => 'estancia_prolongada',
                'nivel' => 'warning',
                'paciente' => $adm->paciente?->nombre,
                'mensaje' => "Estancia prolongada: {$adm->dias_en_uti} días en UTI",
                'admission_id' => $adm->id,
            ];
        }

        // Preautorización por vencer (seguros que llevan más de 5 días sin renovar)
        $sinPreautorizacion = UtiAdmission::where('estado', 'activo')
            ->where('tipo_pago', 'seguro')
            ->whereNull('nro_autorizacion')
            ->with('paciente')
            ->get();

        foreach ($sinPreautorizacion as $adm) {
            $alertas[] = [
                'tipo' => 'sin_preautorizacion',
                'nivel' => 'danger',
                'paciente' => $adm->paciente?->nombre,
                'mensaje' => 'Paciente con seguro sin número de autorización',
                'admission_id' => $adm->id,
            ];
        }

        // Días sin registro clínico
        $hoy = today()->toDateString();
        $admisionesActivas = UtiAdmission::where('estado', 'activo')->get();

        foreach ($admisionesActivas as $adm) {
            $tieneRegistroHoy = DB::table('uti_daily_records')
                ->where('uti_admission_id', $adm->id)
                ->where('fecha', $hoy)
                ->exists();

            if (!$tieneRegistroHoy) {
                $alertas[] = [
                    'tipo' => 'sin_registro',
                    'nivel' => 'warning',
                    'paciente' => $adm->paciente?->nombre,
                    'mensaje' => 'Sin registro clínico del día',
                    'admission_id' => $adm->id,
                ];
            }
        }

        // Pacientes sin cargos (más de 24 horas sin cargos generados)
        $pacientesSinCargos = UtiAdmission::where('estado', 'activo')
            ->whereRaw('DATEDIFF(NOW(), fecha_ingreso) > 0')
            ->doesntHave('medications')
            ->doesntHave('supplies')
            ->get();

        foreach ($pacientesSinCargos as $adm) {
            $alertas[] = [
                'tipo' => 'sin_cargos',
                'nivel' => 'info',
                'paciente' => $adm->paciente?->nombre,
                'mensaje' => 'Paciente sin consumos registrados',
                'admission_id' => $adm->id,
            ];
        }

        return response()->json([
            'success' => true,
            'alertas' => $alertas,
            'total' => count($alertas),
        ]);
    }

    /**
     * API: Actualizar preautorización
     */
    public function actualizarPreautorizacion(Request $request, $admissionId): JsonResponse
    {
        $admission = UtiAdmission::findOrFail($admissionId);

        $validated = $request->validate([
            'nro_autorizacion' => 'required|string|max:50',
        ]);

        $admission->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Preautorización actualizada correctamente',
        ]);
    }
}
