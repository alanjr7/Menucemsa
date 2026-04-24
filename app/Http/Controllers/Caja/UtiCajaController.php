<?php

namespace App\Http\Controllers\Caja;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use App\Models\UtiAdmission;
use App\Models\UtiMedication;
use App\Models\UtiSupply;
use App\Models\UtiCatering;
use App\Models\UtiTarifario;
use App\Services\CuentaCobroService;

class UtiCajaController extends Controller
{
    protected $cuentaCobroService;

    public function __construct(CuentaCobroService $cuentaCobroService)
    {
        $this->cuentaCobroService = $cuentaCobroService;
        $this->middleware(['auth', 'role:admin|caja']);
    }

    /**
     * Vista principal de caja UTI
     */
    public function index(): View
    {
        return view('caja.uti.index');
    }

    /**
     * API: Obtener lista de pacientes UTI para cobro
     */
    public function getPacientesUti(Request $request): JsonResponse
    {
        $query = UtiAdmission::with(['paciente', 'bed', 'seguro'])
            ->whereIn('estado', ['activo', 'alta_clinica']);

        if ($request->has('estado') && $request->estado !== 'todos') {
            $query->where('estado', $request->estado);
        }

        $pacientes = $query->orderBy('fecha_ingreso', 'desc')
            ->get()
            ->map(function($adm) {
                // Calcular cuenta parcial
                $cuenta = $this->calcularCuenta($adm);

                return [
                    'id' => $adm->id,
                    'nro_ingreso' => $adm->nro_ingreso,
                    'paciente' => [
                        'ci' => $adm->paciente?->ci,
                        'nombre' => $adm->paciente?->nombre,
                        'telefono' => $adm->paciente?->telefono,
                    ],
                    'cama' => $adm->bed?->bed_number ?? 'Sin cama',
                    'estado' => $adm->estado,
                    'estado_label' => $this->getEstadoLabel($adm->estado),
                    'estado_color' => $this->getEstadoColor($adm->estado),
                    'tipo_pago' => $adm->tipo_pago,
                    'seguro' => $adm->seguro?->nombre ?? 'Particular',
                    'dias_en_uti' => $adm->dias_en_uti,
                    'fecha_ingreso' => $adm->fecha_ingreso?->format('d/m/Y H:i'),
                    'fecha_alta_clinica' => $adm->fecha_alta_clinica?->format('d/m/Y H:i'),
                    'listo_para_cobro' => $adm->estado === 'alta_clinica',
                    'cuenta' => $cuenta,
                ];
            });

        return response()->json([
            'success' => true,
            'pacientes' => $pacientes,
        ]);
    }

    /**
     * API: Obtener detalle de cuenta para cobro
     */
    public function getDetalleCuenta($admissionId): JsonResponse
    {
        $admission = UtiAdmission::with([
            'paciente.seguro',
            'bed',
            'medicaments.medicamento',
            'supplies.insumo',
            'catering',
            'medico',
        ])->findOrFail($admissionId);

        $cuenta = $this->calcularCuentaDetallada($admission);

        return response()->json([
            'success' => true,
            'paciente' => [
                'ci' => $admission->paciente?->ci,
                'nombre' => $admission->paciente?->nombre,
                'direccion' => $admission->paciente?->direccion,
                'telefono' => $admission->paciente?->telefono,
                'seguro' => $admission->paciente?->seguro?->nombre,
            ],
            'ingreso' => [
                'nro_ingreso' => $admission->nro_ingreso,
                'fecha_ingreso' => $admission->fecha_ingreso?->format('d/m/Y H:i'),
                'fecha_alta_clinica' => $admission->fecha_alta_clinica?->format('d/m/Y H:i'),
                'dias_en_uti' => $admission->dias_en_uti,
                'cama' => $admission->bed?->bed_number,
                'diagnostico' => $admission->diagnostico_principal,
                'medico' => $admission->medico?->nombre,
            ],
            'cuenta' => $cuenta,
        ]);
    }

    /**
     * API: Procesar cobro
     */
    public function procesarCobro(Request $request, $admissionId): JsonResponse
    {
        $admission = UtiAdmission::findOrFail($admissionId);

        $validated = $request->validate([
            'monto_efectivo' => 'nullable|numeric|min:0',
            'monto_tarjeta' => 'nullable|numeric|min:0',
            'monto_transferencia' => 'nullable|numeric|min:0',
            'monto_deposito' => 'nullable|numeric|min:0',
            'es_cobro_total' => 'required|boolean',
            'observaciones' => 'nullable|string|max:500',
        ]);

        // Validar que tenga alta clínica si es cobro total
        if ($validated['es_cobro_total'] && $admission->estado !== 'alta_clinica') {
            return response()->json([
                'success' => false,
                'message' => 'No se puede realizar el cobro total sin alta clínica',
            ], 422);
        }

        $cuenta = $this->calcularCuenta($admission);
        $totalPagar = $cuenta['total'];

        $totalRecibido = ($validated['monto_efectivo'] ?? 0) +
                         ($validated['monto_tarjeta'] ?? 0) +
                         ($validated['monto_transferencia'] ?? 0) +
                         ($validated['monto_deposito'] ?? 0);

        if ($totalRecibido < $totalPagar) {
            return response()->json([
                'success' => false,
                'message' => 'El monto recibido es menor al total a pagar',
                'faltante' => $totalPagar - $totalRecibido,
            ], 422);
        }

        // Crear registro de pago usando el servicio
        try {
            $pago = $this->cuentaCobroService->registrarPagoUti(
                $admission,
                $validated,
                $cuenta
            );

            // Si es cobro total, dar alta administrativa
            if ($validated['es_cobro_total']) {
                $admission->update([
                    'estado' => 'alta_administrativa',
                    'fecha_alta_administrativa' => now(),
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => $validated['es_cobro_total'] ? 'Cobro total procesado correctamente' : 'Cobro parcial procesado correctamente',
                'pago' => $pago,
                'vuelto' => $totalRecibido - $totalPagar,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar el pago: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * API: Registrar depósito (cobro parcial)
     */
    public function registrarDeposito(Request $request, $admissionId): JsonResponse
    {
        $admission = UtiAdmission::findOrFail($admissionId);

        $validated = $request->validate([
            'monto' => 'required|numeric|min:0.01',
            'metodo_pago' => 'required|in:efectivo,tarjeta,transferencia,deposito',
            'observaciones' => 'nullable|string|max:500',
        ]);

        try {
            $deposito = $this->cuentaCobroService->registrarDepositoUti(
                $admission,
                $validated['monto'],
                $validated['metodo_pago'],
                $validated['observaciones'] ?? null
            );

            return response()->json([
                'success' => true,
                'message' => 'Depósito registrado correctamente',
                'deposito' => $deposito,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar depósito: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Calcular cuenta resumida
     */
    private function calcularCuenta($admission): array
    {
        // Estadía
        $diasEstadia = $admission->dias_en_uti;
        $precioDia = $admission->bed?->precio_dia ??
            UtiTarifario::where('tipo', 'estadia')->where('activo', true)->first()?->precio ?? 0;
        $costoEstadia = $diasEstadia * $precioDia;

        // Medicamentos
        $costoMedicamentos = UtiMedication::where('uti_admission_id', $admission->id)
            ->where('cargo_generado', true)
            ->join('medicamentos', 'uti_medications.medicamento_id', '=', 'medicamentos.id')
            ->sum(DB::raw('uti_medications.dosis * medicamentos.precio'));

        // Insumos
        $costoInsumos = UtiSupply::where('uti_admission_id', $admission->id)
            ->where('cargo_generado', true)
            ->join('insumos', 'uti_supplies.insumo_id', '=', 'insumos.id')
            ->sum(DB::raw('uti_supplies.cantidad * insumos.precio'));

        // Alimentación
        $tarifaAlimentacion = UtiTarifario::where('tipo', 'alimentacion')
            ->where('activo', true)
            ->first();
        $precioComida = $tarifaAlimentacion?->precio ?? 0;
        $comidasDadas = UtiCatering::where('uti_admission_id', $admission->id)
            ->where('estado', 'dado')
            ->where('cargo_generado', true)
            ->count();
        $costoAlimentacion = $comidasDadas * $precioComida;

        // Descuento por seguro
        $descuento = 0;
        if ($admission->tipo_pago === 'seguro' && $admission->seguro) {
            $montoServicios = $costoEstadia + $costoMedicamentos + $costoInsumos;
            $calculo = $admission->seguro->calcularCobertura($montoServicios);
            $descuento = $calculo['monto_cubierto'];
        }

        $subtotal = $costoEstadia + $costoMedicamentos + $costoInsumos + $costoAlimentacion;
        $total = $subtotal - $descuento;

        // Depósitos ya realizados
        $depositosRealizados = $this->cuentaCobroService->getDepositosUti($admission->id);
        $totalDepositos = collect($depositosRealizados)->sum('monto');

        return [
            'estadia' => [
                'dias' => $diasEstadia,
                'precio_dia' => $precioDia,
                'subtotal' => $costoEstadia,
            ],
            'medicamentos' => $costoMedicamentos,
            'insumos' => $costoInsumos,
            'alimentacion' => [
                'comidas' => $comidasDadas,
                'precio' => $precioComida,
                'subtotal' => $costoAlimentacion,
            ],
            'subtotal' => $subtotal,
            'descuento_seguro' => $descuento,
            'total' => $total,
            'depositos_realizados' => $totalDepositos,
            'saldo_pendiente' => max(0, $total - $totalDepositos),
        ];
    }

    /**
     * Calcular cuenta detallada
     */
    private function calcularCuentaDetallada($admission): array
    {
        $cuentaResumen = $this->calcularCuenta($admission);

        // Detalle de medicamentos
        $medicamentos = UtiMedication::where('uti_admission_id', $admission->id)
            ->where('cargo_generado', true)
            ->with('medicamento')
            ->get()
            ->map(function($med) {
                $precioUnitario = $med->medicamento?->precio ?? 0;
                return [
                    'nombre' => $med->medicamento?->nombre ?? 'Desconocido',
                    'dosis' => $med->dosis . ' ' . $med->unidad,
                    'via' => $med->via_administracion,
                    'fecha' => $med->fecha->format('d/m/Y'),
                    'precio_unitario' => $precioUnitario,
                    'cantidad' => $med->dosis,
                    'total' => $precioUnitario * $med->dosis,
                ];
            });

        // Detalle de insumos
        $insumos = UtiSupply::where('uti_admission_id', $admission->id)
            ->where('cargo_generado', true)
            ->with('insumo')
            ->get()
            ->map(function($sup) {
                $precioUnitario = $sup->insumo?->precio ?? 0;
                return [
                    'nombre' => $sup->insumo?->nombre ?? 'Desconocido',
                    'cantidad' => $sup->cantidad,
                    'fecha' => $sup->fecha->format('d/m/Y'),
                    'precio_unitario' => $precioUnitario,
                    'total' => $precioUnitario * $sup->cantidad,
                ];
            });

        // Detalle de alimentación
        $alimentacion = UtiCatering::where('uti_admission_id', $admission->id)
            ->where('estado', 'dado')
            ->where('cargo_generado', true)
            ->get()
            ->map(function($cat) use ($cuentaResumen) {
                return [
                    'tipo_comida' => $cat->tipo_comida,
                    'fecha' => $cat->fecha->format('d/m/Y'),
                    'precio' => $cuentaResumen['alimentacion']['precio'],
                ];
            });

        return array_merge($cuentaResumen, [
            'detalle_medicamentos' => $medicamentos,
            'detalle_insumos' => $insumos,
            'detalle_alimentacion' => $alimentacion,
        ]);
    }

    private function getEstadoLabel($estado): string
    {
        return match($estado) {
            'activo' => 'En UTI',
            'alta_clinica' => 'Listo para Alta',
            'alta_administrativa' => 'Alta Completada',
            default => $estado,
        };
    }

    private function getEstadoColor($estado): string
    {
        return match($estado) {
            'activo' => 'blue',
            'alta_clinica' => 'green',
            'alta_administrativa' => 'gray',
            default => 'gray',
        };
    }
}
