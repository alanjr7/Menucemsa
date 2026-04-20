<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Hospitalizacion;
use App\Models\Paciente;
use App\Models\UtiAdmission;
use App\Models\UtiBed;
use App\Models\Cama;
use App\Models\CuentaCobro;
use App\Models\CuentaCobroDetalle;
use App\Models\AlmacenMedicamento;
use App\Models\HospMedicamentoAdministrado;
use App\Models\HospCatering;
use App\Models\HospDrenaje;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class InternacionStaffController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:internacion|admin|dirmedico');
    }

    public function index(): View
    {
        return view('internacion-staff.dashboard');
    }

    /**
     * Mostrar página de evaluación del paciente
     */
    public function evaluar($id): View
    {
        $hospitalizacion = Hospitalizacion::with(['paciente', 'medico.user', 'medico.especialidad'])->findOrFail($id);
        return view('internacion-staff.evaluar', compact('hospitalizacion'));
    }

    /**
     * API: Get internaciones for dashboard
     */
    public function apiInternaciones(Request $request): JsonResponse
    {
        $query = Hospitalizacion::with(['paciente', 'medico.user', 'medico.especialidad'])
            ->whereNull('fecha_alta')
            ->orderBy('fecha_ingreso', 'desc');

        if ($request->has('estado') && $request->estado !== 'todos') {
            $query->where('estado', $request->estado);
        }

        $internaciones = $query->get()->map(function($hosp) {
            return [
                'id' => $hosp->id,
                'codigo' => $hosp->id,
                'paciente_id' => $hosp->ci_paciente,
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
            'activos' => Hospitalizacion::whereNull('fecha_alta')->count(),
            'espera' => Hospitalizacion::whereNull('fecha_alta')->whereNull('habitacion_id')->count(),
            'atencion' => Hospitalizacion::whereNull('fecha_alta')->whereNotNull('habitacion_id')->count(),
            'hoy' => Hospitalizacion::whereDate('fecha_ingreso', today())->count(),
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
            'activos' => Hospitalizacion::whereNull('fecha_alta')->count(),
            'espera' => Hospitalizacion::whereNull('fecha_alta')->whereNull('habitacion_id')->count(),
            'atencion' => Hospitalizacion::whereNull('fecha_alta')->whereNotNull('habitacion_id')->count(),
            'hoy' => Hospitalizacion::whereDate('fecha_ingreso', today())->count(),
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
     * API: Derivar a UTI
     */
    public function derivarAUti(Request $request, $id): JsonResponse
    {
        try {
            DB::beginTransaction();

            $hospitalizacion = Hospitalizacion::with('paciente')->findOrFail($id);

            // Verificar si ya está en UTI
            $utiActivo = UtiAdmission::where('patient_id', $hospitalizacion->ci_paciente)
                ->whereIn('estado', ['activo', 'alta_clinica'])
                ->first();

            if ($utiActivo) {
                return response()->json([
                    'success' => false,
                    'message' => 'El paciente ya tiene un ingreso activo en UTI',
                ], 422);
            }

            // Buscar cama disponible
            $cama = UtiBed::where('status', 'disponible')
                ->where('activa', true)
                ->first();

            if (!$cama) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay camas disponibles en UTI',
                ], 422);
            }

            // Generar número de ingreso UTI
            $prefijo = 'UTI';
            $anio = date('Y');
            $mes = date('m');
            $ultimo = UtiAdmission::whereYear('fecha_ingreso', date('Y'))
                ->whereMonth('fecha_ingreso', date('m'))
                ->count();
            $secuencia = str_pad($ultimo + 1, 4, '0', STR_PAD_LEFT);
            $nroIngreso = "{$prefijo}-{$anio}{$mes}-{$secuencia}";

            // Crear ingreso UTI
            $admission = UtiAdmission::create([
                'patient_id' => $hospitalizacion->ci_paciente,
                'bed_id' => $cama->id,
                'nro_ingreso' => $nroIngreso,
                'hospitalization_id' => $hospitalizacion->id,
                'estado_clinico' => 'estable',
                'diagnostico_principal' => $hospitalizacion->diagnostico,
                'tipo_ingreso' => 'derivacion_interna',
                'tipo_pago' => 'particular',
                'fecha_ingreso' => now(),
                'estado' => 'activo',
                'medico_responsable_id' => $hospitalizacion->ci_medico,
            ]);

            // Actualizar cama a ocupada
            $cama->update(['status' => 'ocupada']);

            // Actualizar hospitalización
            $hospitalizacion->update([
                'estado' => 'trasladado',
                'observaciones' => ($hospitalizacion->observaciones ? $hospitalizacion->observaciones . "\n" : '') .
                    'Trasladado a UTI: ' . now()->format('d/m/Y H:i'),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Paciente derivado a UTI correctamente',
                'admission' => [
                    'id' => $admission->id,
                    'nro_ingreso' => $admission->nro_ingreso,
                    'cama' => $cama->bed_number,
                ],
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error al derivar a UTI: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * API: Dar alta
     */
    public function darAlta(Request $request, $id): JsonResponse
    {
        try {
            DB::beginTransaction();

            $hospitalizacion = Hospitalizacion::findOrFail($id);

            if ($hospitalizacion->fecha_alta) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este paciente ya fue dado de alta'
                ], 422);
            }

            $validated = $request->validate([
                'motivo_alta' => 'nullable|string|max:500',
            ]);

            // 1. Si tiene cama asignada, liberarla y calcular estancia
            if ($hospitalizacion->cama_id) {
                $cama = Cama::find($hospitalizacion->cama_id);

                if ($cama) {
                    // Calcular días y costo
                    $dias = $hospitalizacion->getDiasEstancia();
                    $costoTotal = $hospitalizacion->getCostoEstancia();

                    // Actualizar detalle de cuenta de cobro
                    if ($hospitalizacion->cuenta_cobro_detalle_id) {
                        $detalle = CuentaCobroDetalle::find($hospitalizacion->cuenta_cobro_detalle_id);
                        if ($detalle) {
                            $detalle->update([
                                'cantidad' => $dias,
                                'subtotal' => $costoTotal,
                                'descripcion' => "Estancia Internación - {$dias} días (Alta médica)",
                            ]);

                            // Recalcular total de cuenta
                            $cuenta = $detalle->cuentaCobro;
                            if ($cuenta) {
                                $cuenta->total_calculado = $cuenta->detalles->sum('subtotal');
                                $cuenta->save();
                            }
                        }
                    }

                    // Guardar totales en hospitalización
                    $hospitalizacion->update([
                        'total_estancia' => $costoTotal,
                    ]);

                    // Liberar cama
                    $cama->update(['disponibilidad' => 'disponible']);

                    // Verificar si habitación quedó vacía
                    $habitacion = $cama->habitacion;
                    $camasOcupadas = $habitacion->camas()->where('disponibilidad', 'ocupada')->count();
                    if ($camasOcupadas === 0 && $habitacion->estado === 'ocupada') {
                        $habitacion->update(['estado' => 'disponible']);
                    }
                }
            }

            // 2. Marcar fecha de alta
            $hospitalizacion->update([
                'fecha_alta' => now(),
                'motivo_alta' => $validated['motivo_alta'] ?? 'Alta médica',
                'estado' => 'alta',
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Paciente dado de alta exitosamente' . ($hospitalizacion->total_estancia > 0
                    ? '. Estancia: ' . $hospitalizacion->getDiasEstancia() . ' días, Total: Bs. ' . number_format($hospitalizacion->total_estancia, 2)
                    : ''),
                'hospitalizacion' => $hospitalizacion
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al dar de alta: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Get medicamentos disponibles del inventario de internacion
     */
    public function apiMedicamentosDisponibles(): JsonResponse
    {
        $medicamentos = AlmacenMedicamento::porArea('internacion')
            ->where('cantidad', '>', 0)
            ->activos()
            ->orderBy('nombre')
            ->get(['id', 'nombre', 'tipo', 'unidad_medida', 'precio', 'cantidad']);

        return response()->json([
            'success' => true,
            'medicamentos' => $medicamentos
        ]);
    }

    /**
     * API: Buscar medicamentos por nombre
     */
    public function buscarMedicamentos(Request $request): JsonResponse
    {
        $query = $request->get('q', '');

        $medicamentos = AlmacenMedicamento::porArea('internacion')
            ->where('cantidad', '>', 0)
            ->activos()
            ->where('nombre', 'like', '%' . $query . '%')
            ->limit(10)
            ->get(['id', 'nombre', 'tipo', 'unidad_medida', 'precio', 'cantidad']);

        return response()->json([
            'success' => true,
            'medicamentos' => $medicamentos
        ]);
    }

    /**
     * API: Get medicamentos administrados a un paciente
     */
    public function apiMedicamentos($id): JsonResponse
    {
        $medicamentos = HospMedicamentoAdministrado::with(['medicamento', 'administeredBy'])
            ->where('hospitalizacion_id', $id)
            ->orderBy('fecha', 'desc')
            ->orderBy('hora', 'desc')
            ->get()
            ->map(function($med) {
                return [
                    'id' => $med->id,
                    'medicamento' => $med->medicamento?->nombre ?? 'Desconocido',
                    'tipo' => $med->medicamento?->tipo ?? '-',
                    'cantidad' => $med->cantidad,
                    'unidad' => $med->unidad,
                    'via_administracion' => $med->via_administracion,
                    'fecha' => $med->fecha?->format('d/m/Y'),
                    'hora' => $med->hora?->format('H:i'),
                    'administrado_por' => $med->administeredBy?->name ?? 'Desconocido',
                    'observaciones' => $med->observaciones,
                    'cargo_generado' => $med->cargo_generado,
                ];
            });

        return response()->json([
            'success' => true,
            'medicamentos' => $medicamentos
        ]);
    }

    /**
     * API: Registrar medicamento administrado
     */
    public function storeMedicamento(Request $request, $id): JsonResponse
    {
        try {
            DB::beginTransaction();

            $hospitalizacion = Hospitalizacion::with('paciente')->findOrFail($id);
            $medicamento = AlmacenMedicamento::findOrFail($request->medicamento_id);

            $validated = $request->validate([
                'medicamento_id' => 'required|exists:almacen_medicamentos,id',
                'cantidad' => 'required|numeric|min:0.01',
                'unidad' => 'required|string|max:20',
                'via_administracion' => 'nullable|string|max:50',
                'observaciones' => 'nullable|string',
            ]);

            // Verificar stock suficiente
            if ($medicamento->cantidad < $validated['cantidad']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stock insuficiente. Disponible: ' . $medicamento->cantidad . ' ' . $medicamento->unidad_medida
                ], 422);
            }

            // Descontar del inventario
            $medicamento->decrement('cantidad', $validated['cantidad']);

            // Calcular precio y generar cargo
            $cuentaCobroDetalleId = null;
            $cargoGenerado = false;
            $precio = $medicamento->precio ?? 0;
            $subtotal = $precio * $validated['cantidad'];

            if ($subtotal > 0) {
                $cuentaCobroDetalleId = $this->generarCargo(
                    $hospitalizacion,
                    'Medicamento: ' . $medicamento->nombre,
                    $subtotal,
                    1,
                    'medicamento',
                    $medicamento->id
                );
                $cargoGenerado = true;
            }

            // Crear registro
            $registro = HospMedicamentoAdministrado::create([
                'hospitalizacion_id' => $id,
                'medicamento_id' => $validated['medicamento_id'],
                'administered_by' => Auth::id(),
                'fecha' => now(),
                'hora' => now(),
                'cantidad' => $validated['cantidad'],
                'unidad' => $validated['unidad'],
                'via_administracion' => $validated['via_administracion'],
                'observaciones' => $validated['observaciones'],
                'cargo_generado' => $cargoGenerado,
                'cuenta_cobro_detalle_id' => $cuentaCobroDetalleId,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Medicamento registrado correctamente' . ($cargoGenerado ? ' y cargo generado' : ''),
                'registro' => $registro
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar medicamento: ' . $e->getMessage()
            ], 500);
        }
    }

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
                $precios = config('hospitalizacion.catering.precios', []);
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
     * API: Registrar/actualizar catering
     */
    public function storeCatering(Request $request, $id): JsonResponse
    {
        try {
            DB::beginTransaction();

            $hospitalizacion = Hospitalizacion::with('paciente')->findOrFail($id);

            $validated = $request->validate([
                'tipo_comida' => 'required|in:desayuno,almuerzo,merienda,cena',
                'estado' => 'required|in:dado,no_dado,no_aplica',
                'observaciones' => 'nullable|string',
            ]);

            $fecha = now()->toDateString();

            // Buscar registro existente
            $registro = HospCatering::where('hospitalizacion_id', $id)
                ->where('fecha', $fecha)
                ->where('tipo_comida', $validated['tipo_comida'])
                ->first();

            $precio = 0;
            $cuentaCobroDetalleId = null;
            $cargoGenerado = false;

            if ($validated['estado'] === 'dado') {
                $precios = config('hospitalizacion.catering.precios', []);
                $precio = $precios[$validated['tipo_comida']] ?? 0;

                // Si ya tenía un cargo anterior, no generar otro
                if ($registro && $registro->cargo_generado && $registro->estado === 'dado') {
                    $cuentaCobroDetalleId = $registro->cuenta_cobro_detalle_id;
                    $cargoGenerado = true;
                } elseif ($precio > 0) {
                    $cuentaCobroDetalleId = $this->generarCargo(
                        $hospitalizacion,
                        'Catering: ' . ucfirst($validated['tipo_comida']),
                        $precio,
                        1,
                        'servicio'
                    );
                    $cargoGenerado = true;
                }
            }

            $data = [
                'hospitalizacion_id' => $id,
                'registered_by' => Auth::id(),
                'fecha' => $fecha,
                'tipo_comida' => $validated['tipo_comida'],
                'estado' => $validated['estado'],
                'hora_registro' => $validated['estado'] === 'dado' ? now() : null,
                'observaciones' => $validated['observaciones'] ?? null,
                'precio' => $precio,
                'cargo_generado' => $cargoGenerado,
                'cuenta_cobro_detalle_id' => $cuentaCobroDetalleId,
            ];

            if ($registro) {
                // Si cambia de 'dado' a otro estado, anular el cargo anterior
                if ($registro->estado === 'dado' && $validated['estado'] !== 'dado' && $registro->cuenta_cobro_detalle_id) {
                    $this->anularCargo($registro->cuenta_cobro_detalle_id);
                    $data['cargo_generado'] = false;
                    $data['cuenta_cobro_detalle_id'] = null;
                    $data['precio'] = 0;
                }
                $registro->update($data);
            } else {
                $registro = HospCatering::create($data);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Catering registrado correctamente' . ($cargoGenerado ? ' y cargo generado' : ''),
                'registro' => $registro
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
     * API: Get drenajes del paciente
     */
    public function apiDrenajes($id): JsonResponse
    {
        $drenajes = HospDrenaje::with('registeredBy')
            ->where('hospitalizacion_id', $id)
            ->orderBy('fecha', 'desc')
            ->orderBy('hora', 'desc')
            ->get()
            ->map(function($drenaje) {
                return [
                    'id' => $drenaje->id,
                    'fecha' => $drenaje->fecha?->format('d/m/Y'),
                    'hora' => $drenaje->hora?->format('H:i'),
                    'tipo_drenaje' => $drenaje->tipo_drenaje,
                    'realizado' => $drenaje->realizado,
                    'observaciones' => $drenaje->observaciones,
                    'precio' => $drenaje->precio,
                    'cargo_generado' => $drenaje->cargo_generado,
                    'registrado_por' => $drenaje->registeredBy?->name ?? 'Desconocido',
                ];
            });

        return response()->json([
            'success' => true,
            'drenajes' => $drenajes
        ]);
    }

    /**
     * API: Registrar drenaje
     */
    public function storeDrenaje(Request $request, $id): JsonResponse
    {
        try {
            DB::beginTransaction();

            $hospitalizacion = Hospitalizacion::with('paciente')->findOrFail($id);

            $validated = $request->validate([
                'tipo_drenaje' => 'nullable|string|max:50',
                'realizado' => 'required|boolean',
                'observaciones' => 'nullable|string',
            ]);

            $precio = 0;
            $cuentaCobroDetalleId = null;
            $cargoGenerado = false;

            if ($validated['realizado']) {
                $precios = config('hospitalizacion.drenajes.precios', []);
                $precio = $precios[$validated['tipo_drenaje']] ?? config('hospitalizacion.drenajes.precio_default', 40);

                if ($precio > 0) {
                    $cuentaCobroDetalleId = $this->generarCargo(
                        $hospitalizacion,
                        'Drenaje: ' . ($validated['tipo_drenaje'] ?? 'General'),
                        $precio,
                        1,
                        'procedimiento'
                    );
                    $cargoGenerado = true;
                }
            }

            $registro = HospDrenaje::create([
                'hospitalizacion_id' => $id,
                'registered_by' => Auth::id(),
                'fecha' => now(),
                'hora' => now(),
                'tipo_drenaje' => $validated['tipo_drenaje'],
                'realizado' => $validated['realizado'],
                'observaciones' => $validated['observaciones'],
                'precio' => $precio,
                'cargo_generado' => $cargoGenerado,
                'cuenta_cobro_detalle_id' => $cuentaCobroDetalleId,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Drenaje registrado correctamente' . ($cargoGenerado ? ' y cargo generado' : ''),
                'registro' => $registro
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar drenaje: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper: Generar cargo en cuenta de cobro
     */
    private function generarCargo($hospitalizacion, $concepto, $precio, $cantidad = 1, $tipoItem = 'servicio', $origenId = null): ?int
    {
        // Buscar cuenta de cobro existente
        $cuenta = CuentaCobro::where('paciente_ci', $hospitalizacion->ci_paciente)
            ->where('tipo_atencion', 'hospitalizacion')
            ->where('referencia_id', $hospitalizacion->id)
            ->whereIn('estado', ['pendiente', 'parcial'])
            ->first();

        // Si no existe, crear nueva
        if (!$cuenta) {
            $cuenta = CuentaCobro::create([
                'paciente_ci' => $hospitalizacion->ci_paciente,
                'tipo_atencion' => 'hospitalizacion',
                'referencia_id' => $hospitalizacion->id,
                'referencia_type' => Hospitalizacion::class,
                'estado' => 'pendiente',
                'total_calculado' => 0,
                'total_pagado' => 0,
            ]);
        }

        $detalleData = [
            'cuenta_cobro_id' => $cuenta->id,
            'tipo_item' => $tipoItem,
            'descripcion' => $concepto,
            'cantidad' => $cantidad,
            'precio_unitario' => $precio,
            'subtotal' => $precio * $cantidad,
        ];

        if ($origenId) {
            $detalleData['origen_type'] = AlmacenMedicamento::class;
            $detalleData['origen_id'] = $origenId;
        }

        $detalle = CuentaCobroDetalle::create($detalleData);

        // Recalcular total
        $cuenta->recalcularTotales();

        return $detalle->id;
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
}
