<?php

namespace App\Http\Controllers\Caja;

use App\Http\Controllers\Controller;
use App\Models\CajaSession;
use App\Models\CuentaCobro;
use App\Models\CuentaCobroDetalle;
use App\Models\PagoCuenta;
use App\Models\Paciente;
use App\Models\Emergency;
use App\Models\Consulta;
use App\Models\Hospitalizacion;
use App\Models\Tarifa;
use App\Models\MovimientoCaja;
use App\Models\UtiAdmission;
use App\Models\UtiMedication;
use App\Models\UtiSupply;
use App\Models\UtiCatering;
use App\Models\UtiTarifario;
use App\Services\CuentaCobroService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CajaOperativaController extends Controller
{
    protected $cuentaCobroService;

    public function __construct(CuentaCobroService $cuentaCobroService)
    {
        $this->cuentaCobroService = $cuentaCobroService;
    }

    /**
     * Vista principal de caja operativa
     */
    public function index(): View
    {
        // Verificar si hay caja abierta
        $cajaAbierta = CajaSession::delUsuario(Auth::id())
            ->abierta()
            ->first();

        if (!$cajaAbierta) {
            return view('caja.apertura');
        }

        // Obtener pacientes con cuenta pendiente o parcial
        $cuentasPendientes = CuentaCobro::with(['paciente', 'detalles'])
            ->whereIn('estado', ['pendiente', 'parcial'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Estadísticas del día
        $hoy = now()->toDateString();
        $estadisticas = [
            'total_cobrado' => PagoCuenta::delDia($hoy)->sum('monto'),
            'transacciones' => PagoCuenta::delDia($hoy)->count(),
            'pendientes' => CuentaCobro::pendiente()->count(),
            'parciales' => CuentaCobro::parcial()->count(),
        ];

        // Desglose por método de pago
        $metodosPago = [
            'efectivo' => PagoCuenta::delDia($hoy)->efectivo()->sum('monto'),
            'transferencia' => PagoCuenta::delDia($hoy)->transferencia()->sum('monto'),
            'tarjeta' => PagoCuenta::delDia($hoy)->tarjeta()->sum('monto'),
            'qr' => PagoCuenta::delDia($hoy)->qr()->sum('monto'),
        ];

        return view('caja.operativa', compact(
            'cajaAbierta',
            'cuentasPendientes',
            'estadisticas',
            'metodosPago'
        ));
    }

    /**
     * Apertura de caja
     */
    public function abrirCaja(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'monto_inicial' => 'required|numeric|min:0',
                'observaciones' => 'nullable|string|max:500'
            ]);

            // Verificar que no haya caja abierta
            $cajaAbierta = CajaSession::delUsuario(Auth::id())
                ->abierta()
                ->first();

            if ($cajaAbierta) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ya existe una caja abierta para este usuario'
                ], 400);
            }

            $caja = CajaSession::create([
                'user_id' => Auth::id(),
                'fecha_apertura' => now(),
                'monto_inicial' => $request->monto_inicial,
                'estado' => 'abierta',
                'observaciones' => $request->observaciones
            ]);

            // Registrar movimiento de apertura
            MovimientoCaja::create([
                'caja_session_id' => $caja->id,
                'tipo' => 'ingreso',
                'concepto' => 'Apertura de caja',
                'monto' => $request->monto_inicial,
                'metodo_pago' => 'efectivo',
                'movable_type' => 'App\Models\CajaSession',
                'movable_id' => $caja->id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Caja abierta correctamente',
                'caja' => $caja
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al abrir caja: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cierre de caja
     */
    public function cerrarCaja(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'monto_final' => 'required|numeric|min:0',
                'observaciones' => 'nullable|string|max:500'
            ]);

            $caja = CajaSession::delUsuario(Auth::id())
                ->abierta()
                ->firstOrFail();

            // Calcular totales esperados
            $totalIngresos = $caja->ingresos()->sum('monto');
            $totalEgresos = $caja->egresos()->sum('monto');
            $totalEsperado = $caja->monto_inicial + $totalIngresos - $totalEgresos;
            $diferencia = $request->monto_final - $totalEsperado;

            DB::beginTransaction();

            // Actualizar caja
            $caja->update([
                'fecha_cierre' => now(),
                'monto_final' => $request->monto_final,
                'estado' => 'cerrada',
                'observaciones' => $request->observaciones . ($diferencia != 0 ? " | Diferencia: $diferencia" : '')
            ]);

            // Registrar movimiento de cierre
            MovimientoCaja::create([
                'caja_session_id' => $caja->id,
                'tipo' => 'egreso',
                'concepto' => 'Cierre de caja',
                'monto' => $request->monto_final,
                'metodo_pago' => 'efectivo',
                'observaciones' => 'Monto final al cierre',
                'movable_type' => 'App\Models\CajaSession',
                'movable_id' => $caja->id,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Caja cerrada correctamente',
                'resumen' => [
                    'monto_inicial' => $caja->monto_inicial,
                    'total_ingresos' => $totalIngresos,
                    'total_egresos' => $totalEgresos,
                    'total_esperado' => $totalEsperado,
                    'monto_final' => $request->monto_final,
                    'diferencia' => $diferencia,
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error al cerrar caja: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener lista de pacientes con cuenta pendiente
     */
    public function getPacientesPendientes(): JsonResponse
    {
        try {
            $cuentas = CuentaCobro::with(['paciente', 'referencia', 'detalles.tarifa', 'pagos'])
                ->whereIn('estado', ['pendiente', 'parcial'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($cuenta) {
                    // Obtener nombre y CI del paciente
                    $pacienteNombre = 'N/A';
                    $pacienteCi = $cuenta->paciente_ci;
                    
                    if ($cuenta->paciente) {
                        $pacienteNombre = $cuenta->paciente->nombre;
                    } elseif ($cuenta->es_emergencia && $cuenta->referencia) {
                        // Para emergencias, obtener datos desde la emergencia
                        $emergency = $cuenta->referencia;
                        if ($emergency->patient_id) {
                            // Buscar paciente por patient_id de la emergencia
                            $paciente = \App\Models\Paciente::find($emergency->patient_id);
                            if ($paciente) {
                                $pacienteNombre = $paciente->nombre;
                                $pacienteCi = $paciente->ci; // Usar CI real del paciente
                            } else {
                                $pacienteNombre = 'Paciente Emergencia #' . $emergency->id;
                                $pacienteCi = $emergency->patient_id; // Al menos mostrar el patient_id
                            }
                        }
                    }

                    return [
                        'id' => $cuenta->id,
                        'paciente_ci' => $pacienteCi,
                        'paciente_nombre' => $pacienteNombre,
                        'tipo_atencion' => $cuenta->tipo_atencion_label,
                        'total_calculado' => $cuenta->total_calculado,
                        'total_pagado' => $cuenta->total_pagado,
                        'saldo_pendiente' => $cuenta->saldo_pendiente,
                        'estado' => $cuenta->estado,
                        'estado_color' => $cuenta->estado_color,
                        'estado_label' => $cuenta->estado_label,
                        'es_emergencia' => $cuenta->es_emergencia,
                        'es_post_pago' => $cuenta->es_post_pago,
                        'created_at' => $cuenta->created_at->format('d/m/Y H:i'),
                        'items_count' => $cuenta->detalles->count(),
                    ];
                });

            return response()->json([
                'success' => true,
                'cuentas' => $cuentas
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar pacientes: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener detalle de una cuenta por cobrar
     */
    public function getDetalleCuenta(string $id): JsonResponse
    {
        try {
            $cuenta = CuentaCobro::with(['paciente', 'referencia', 'detalles.tarifa', 'pagos.user'])
                ->findOrFail($id);

            // Determinar referencia (consulta, emergencia, etc.)
            $referencia = null;
            if ($cuenta->referencia_type) {
                $referencia = $cuenta->referencia;
            }

            // Obtener nombre y datos del paciente
            $pacienteNombre = 'N/A';
            $pacienteCi = $cuenta->paciente_ci;
            $pacienteTelefono = 'N/A';
            
            if ($cuenta->paciente) {
                $pacienteNombre = $cuenta->paciente->nombre;
                $pacienteTelefono = $cuenta->paciente->telefono ?? 'N/A';
            } elseif ($cuenta->es_emergencia && $referencia) {
                $emergency = $referencia;
                if ($emergency->patient_id) {
                    $paciente = \App\Models\Paciente::find($emergency->patient_id);
                    if ($paciente) {
                        $pacienteNombre = $paciente->nombre;
                        $pacienteCi = $paciente->ci;
                        $pacienteTelefono = $paciente->telefono ?? 'N/A';
                    } else {
                        $pacienteNombre = 'Paciente Emergencia #' . $emergency->id;
                        $pacienteCi = $emergency->patient_id;
                    }
                }
            }

            return response()->json([
                'success' => true,
                'cuenta' => [
                    'id' => $cuenta->id,
                    'paciente' => [
                        'ci' => $pacienteCi,
                        'nombre' => $pacienteNombre,
                        'telefono' => $pacienteTelefono,
                    ],
                    'tipo_atencion' => $cuenta->tipo_atencion_label,
                    'es_emergencia' => $cuenta->es_emergencia,
                    'es_post_pago' => $cuenta->es_post_pago,
                    'estado' => $cuenta->estado,
                    'estado_label' => $cuenta->estado_label,
                    'total_calculado' => $cuenta->total_calculado,
                    'total_pagado' => $cuenta->total_pagado,
                    'saldo_pendiente' => $cuenta->total_calculado - $cuenta->total_pagado,
                    'ci_nit_facturacion' => $cuenta->ci_nit_facturacion,
                    'razon_social' => $cuenta->razon_social,
                    'detalles' => $cuenta->detalles->map(function ($detalle) {
                        return [
                            'id' => $detalle->id,
                            'tipo_item' => $detalle->tipo_item_label,
                            'descripcion' => $detalle->descripcion,
                            'cantidad' => $detalle->cantidad,
                            'precio_unitario' => $detalle->precio_unitario,
                            'subtotal' => $detalle->subtotal,
                        ];
                    }),
                    'pagos' => $cuenta->pagos->map(function ($pago) {
                        return [
                            'id' => $pago->id,
                            'monto' => $pago->monto,
                            'metodo_pago' => $pago->metodo_pago_label,
                            'referencia' => $pago->referencia,
                            'usuario' => $pago->user->name ?? 'N/A',
                            'fecha' => $pago->created_at->format('d/m/Y H:i'),
                        ];
                    }),
                    'referencia' => $referencia ? [
                        'id' => $referencia->id ?? null,
                        'codigo' => $referencia->code ?? $referencia->nro ?? null,
                        'fecha' => $referencia->created_at?->format('d/m/Y') ?? null,
                    ] : null,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar detalle: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Procesar un cobro (pago total o parcial)
     */
    public function procesarCobro(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'cuenta_cobro_id' => 'required|string|exists:cuenta_cobros,id',
                'monto' => 'required|numeric|min:0.01',
                'metodo_pago' => 'required|in:efectivo,transferencia,tarjeta,qr',
                'referencia' => 'nullable|string|max:100',
                'ci_nit_facturacion' => 'nullable|string|max:20',
                'razon_social' => 'nullable|string|max:100',
                'es_pago_total' => 'nullable|boolean',
            ]);

            $cajaAbierta = CajaSession::delUsuario(Auth::id())
                ->abierta()
                ->first();

            if (!$cajaAbierta) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay caja abierta. Debe abrir caja primero.'
                ], 400);
            }

            DB::beginTransaction();

            $cuenta = CuentaCobro::findOrFail($request->cuenta_cobro_id);

            // Si es pago total, usar el saldo pendiente calculado
            $saldoPendiente = $cuenta->total_calculado - $cuenta->total_pagado;
            $montoPagar = $request->es_pago_total 
                ? $saldoPendiente 
                : $request->monto;

            // Validar que no pague más de lo pendiente
            if ($montoPagar > $saldoPendiente) {
                return response()->json([
                    'success' => false,
                    'message' => 'El monto a pagar no puede ser mayor al saldo pendiente'
                ], 400);
            }

            // Actualizar datos de facturación si se proporcionan
            if ($request->ci_nit_facturacion) {
                $cuenta->ci_nit_facturacion = $request->ci_nit_facturacion;
            }
            if ($request->razon_social) {
                $cuenta->razon_social = $request->razon_social;
            }
            $cuenta->caja_session_id = $cajaAbierta->id;
            $cuenta->save();

            // Registrar el pago usando el método del modelo
            $cuenta->registrarPago($montoPagar, $request->metodo_pago, $request->referencia, Auth::id());
            $cuenta->refresh();

            // Si está pagado completamente, actualizar Caja y Consulta
            if ($cuenta->estado === 'pagado') {
                // Generar número de factura
                $ultimaFactura = \App\Models\Caja::max('nro_factura') ?? 0;
                $nroFactura = $ultimaFactura + 1;

                // Buscar y actualizar la Caja asociada
                $caja = \App\Models\Caja::whereHas('consulta', function($q) use ($cuenta) {
                    $q->where('ci_paciente', $cuenta->paciente_ci);
                })->whereNull('nro_factura')->first();

                if ($caja) {
                    $caja->update(['nro_factura' => $nroFactura]);
                }

                // Actualizar la Consulta - marcar como pagada
                $consulta = \App\Models\Consulta::where('ci_paciente', $cuenta->paciente_ci)
                    ->where('estado_pago', false)
                    ->whereDate('fecha', today())
                    ->first();

                if ($consulta) {
                    $consulta->update(['estado_pago' => true]);
                }
            }

            // Registrar movimiento en caja
            // Obtener nombre del paciente (desde relación o desde emergencia)
            $nombrePaciente = 'N/A';
            if ($cuenta->paciente) {
                $nombrePaciente = $cuenta->paciente->nombre;
            } elseif ($cuenta->es_emergencia && $cuenta->referencia) {
                $emergency = $cuenta->referencia;
                if ($emergency->patient_id) {
                    $paciente = \App\Models\Paciente::find($emergency->patient_id);
                    $nombrePaciente = $paciente?->nombre ?? 'Paciente Emergencia #' . $emergency->id;
                }
            }

            MovimientoCaja::create([
                'caja_session_id' => $cajaAbierta->id,
                'tipo' => 'ingreso',
                'concepto' => 'Cobro ' . $cuenta->tipo_atencion_label . ' - Paciente: ' . $nombrePaciente,
                'monto' => $montoPagar,
                'metodo_pago' => $request->metodo_pago,
                'referencia' => $request->referencia,
                'movable_type' => 'App\Models\PagoCuenta',
                'movable_id' => 0,
            ]);

            // Si es emergencia post-pago y ya está pagado completamente,
            // actualizar la emergencia como pagada
            if ($cuenta->es_emergencia && $cuenta->es_post_pago && $cuenta->estado === 'pagado') {
                $emergency = Emergency::find($cuenta->referencia_id);
                if ($emergency) {
                    $emergency->update(['paid' => true]);
                }
            }

            DB::commit();

            // Notificar cuando el pago está completo
            if ($cuenta->estado === 'pagado') {
                NotificationService::notifyAdmins('pago', 'Pago Completado', "Paciente: {$nombrePaciente} - Monto: $" . number_format($cuenta->total_pagado, 2), route('caja.gestion.index'));
            }

            return response()->json([
                'success' => true,
                'message' => $cuenta->estado === 'pagado'
                    ? 'Cuenta pagada completamente'
                    : 'Pago parcial registrado',
                'cuenta' => [
                    'id' => $cuenta->id,
                    'estado' => $cuenta->estado,
                    'estado_label' => $cuenta->estado_label,
                    'total_pagado' => $cuenta->total_pagado,
                    'saldo_pendiente' => $cuenta->saldo_pendiente,
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar cobro: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Buscar paciente por CI o nombre
     */
    public function buscarPaciente(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'termino' => 'required|string|min:2'
            ]);

            $termino = $request->termino;

            $pacientes = Paciente::where(function ($q) use ($termino) {
                    $q->where('ci', 'like', "%{$termino}%")
                      ->orWhere('nombre', 'like', "%{$termino}%");
                })
                ->limit(10)
                ->get(['ci', 'nombre', 'telefono', 'sexo']);

            return response()->json([
                'success' => true,
                'pacientes' => $pacientes
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al buscar paciente: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener resumen del día para el cajero
     */
    public function getResumenDia(): JsonResponse
    {
        try {
            $hoy = now()->toDateString();
            $cajaAbierta = CajaSession::delUsuario(Auth::id())
                ->abierta()
                ->first();

            if (!$cajaAbierta) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay caja abierta'
                ], 400);
            }

            $resumen = [
                'monto_inicial' => $cajaAbierta->monto_inicial,
                'apertura' => $cajaAbierta->fecha_apertura->format('d/m/Y H:i'),
                'duracion' => $cajaAbierta->duracion,
                
                'totales' => [
                    'efectivo' => PagoCuenta::delDia($hoy)->efectivo()->sum('monto'),
                    'transferencia' => PagoCuenta::delDia($hoy)->transferencia()->sum('monto'),
                    'tarjeta' => PagoCuenta::delDia($hoy)->tarjeta()->sum('monto'),
                    'qr' => PagoCuenta::delDia($hoy)->qr()->sum('monto'),
                ],
                
                'transacciones' => [
                    'total' => PagoCuenta::delDia($hoy)->count(),
                    'efectivo' => PagoCuenta::delDia($hoy)->efectivo()->count(),
                    'transferencia' => PagoCuenta::delDia($hoy)->transferencia()->count(),
                    'tarjeta' => PagoCuenta::delDia($hoy)->tarjeta()->count(),
                    'qr' => PagoCuenta::delDia($hoy)->qr()->count(),
                ],

                'cuentas' => [
                    'pendientes' => CuentaCobro::pendiente()->count(),
                    'parciales' => CuentaCobro::parcial()->count(),
                    'pagadas_hoy' => CuentaCobro::pagado()->delDia($hoy)->count(),
                ]
            ];

            $resumen['totales']['general'] = array_sum($resumen['totales']);

            return response()->json([
                'success' => true,
                'resumen' => $resumen
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener resumen: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener tarifas disponibles para mostrar precios
     */
    public function getTarifas(): JsonResponse
    {
        try {
            $tarifas = Tarifa::where('activo', true)
                ->orderBy('categoria')
                ->orderBy('descripcion')
                ->get()
                ->groupBy('categoria');

            return response()->json([
                'success' => true,
                'tarifas' => $tarifas
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar tarifas: ' . $e->getMessage()
            ], 500);
        }
    }

    // ==================== MÉTODOS UTI INTEGRADOS ====================

    /**
     * API: Obtener lista de pacientes UTI para cobro
     */
    public function getPacientesUti(Request $request): JsonResponse
    {
        try {
            $query = UtiAdmission::with(['paciente', 'bed', 'seguro'])
                ->whereIn('estado', ['activo', 'alta_clinica']);

            if ($request->has('estado') && $request->estado !== 'todos') {
                $query->where('estado', $request->estado);
            }

            $pacientes = $query->orderBy('fecha_ingreso', 'desc')
                ->get()
                ->map(function($adm) {
                    $cuenta = $this->calcularCuentaUti($adm);

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
                        'estado_label' => $this->getEstadoLabelUti($adm->estado),
                        'estado_color' => $this->getEstadoColorUti($adm->estado),
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
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener pacientes UTI: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Obtener detalle de cuenta UTI para cobro
     */
    public function getDetalleCuentaUti($admissionId): JsonResponse
    {
        try {
            $admission = UtiAdmission::with([
                'paciente.seguro',
                'bed',
                'medications.medicamento',
                'supplies.insumo',
                'catering',
                'medico',
            ])->findOrFail($admissionId);

            $cuenta = $this->calcularCuentaDetalladaUti($admission);

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
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener detalle UTI: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Procesar cobro UTI
     */
    public function procesarCobroUti(Request $request, $admissionId): JsonResponse
    {
        try {
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

            $cuenta = $this->calcularCuentaUti($admission);
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
                'message' => 'Error al procesar cobro UTI: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Registrar depósito UTI
     */
    public function registrarDepositoUti(Request $request, $admissionId): JsonResponse
    {
        try {
            $validated = $request->validate([
                'monto' => 'required|numeric|min:0.01',
                'metodo_pago' => 'required|in:efectivo,transferencia,tarjeta,deposito',
                'referencia' => 'nullable|string|max:100',
                'observaciones' => 'nullable|string|max:500',
            ]);

            $admission = UtiAdmission::findOrFail($admissionId);

            // Usar el servicio para registrar el depósito
            $deposito = $this->cuentaCobroService->registrarDepositoUti(
                $admission,
                $validated['monto'],
                $validated['metodo_pago'],
                $validated['referencia'] ?? null,
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
                'message' => 'Error al registrar depósito: ' . $e->getMessage()
            ], 500);
        }
    }

    // ==================== MÉTODOS AUXILIARES UTI ====================

    private function calcularCuentaUti($admission): array
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
            $cobertura = $admission->seguro->cobertura ?? 0;
            $descuento = ($costoEstadia + $costoMedicamentos + $costoInsumos) * ($cobertura / 100);
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

    private function calcularCuentaDetalladaUti($admission): array
    {
        $cuenta = $this->calcularCuentaUti($admission);

        // Detalle de medicamentos
        $medicamentos = UtiMedication::where('uti_admission_id', $admission->id)
            ->where('cargo_generado', true)
            ->with('medicamento')
            ->get()
            ->map(fn($m) => [
                'nombre' => $m->medicamento?->nombre,
                'cantidad' => $m->dosis,
                'unidad' => $m->unidad,
                'precio_unitario' => $m->medicamento?->precio ?? 0,
                'subtotal' => $m->dosis * ($m->medicamento?->precio ?? 0),
                'fecha' => $m->fecha?->format('d/m/Y'),
            ]);

        // Detalle de insumos
        $insumos = UtiSupply::where('uti_admission_id', $admission->id)
            ->where('cargo_generado', true)
            ->with('insumo')
            ->get()
            ->map(fn($i) => [
                'nombre' => $i->insumo?->nombre,
                'cantidad' => $i->cantidad,
                'precio_unitario' => $i->insumo?->precio ?? 0,
                'subtotal' => $i->cantidad * ($i->insumo?->precio ?? 0),
                'fecha' => $i->fecha?->format('d/m/Y'),
            ]);

        // Detalle de alimentación
        $alimentacion = UtiCatering::where('uti_admission_id', $admission->id)
            ->where('estado', 'dado')
            ->where('cargo_generado', true)
            ->get()
            ->groupBy('tipo_comida')
            ->map(fn($grupo, $tipo) => [
                'tipo' => $tipo,
                'cantidad' => $grupo->count(),
                'precio_unitario' => $cuenta['alimentacion']['precio'],
                'subtotal' => $grupo->count() * $cuenta['alimentacion']['precio'],
            ])
            ->values();

        // Historial de depósitos
        $depositos = $this->cuentaCobroService->getDepositosUti($admission->id);

        return array_merge($cuenta, [
            'detalle_medicamentos' => $medicamentos,
            'detalle_insumos' => $insumos,
            'detalle_alimentacion' => $alimentacion,
            'historial_depositos' => $depositos,
        ]);
    }

    private function getEstadoLabelUti($estado): string
    {
        return match($estado) {
            'activo' => 'Activo en UTI',
            'alta_clinica' => 'Alta Clínica - Pendiente Cobro',
            'alta_administrativa' => 'Alta Administrativa',
            'trasladado' => 'Trasladado',
            'fallecido' => 'Fallecido',
            default => $estado,
        };
    }

    private function getEstadoColorUti($estado): string
    {
        return match($estado) {
            'activo' => 'green',
            'alta_clinica' => 'orange',
            'alta_administrativa' => 'blue',
            'trasladado' => 'purple',
            'fallecido' => 'red',
            default => 'gray',
        };
    }

    /**
     * Vista de comprobante de pago imprimible
     */
    public function comprobante(string $cuentaId): View
    {
        $cuenta = CuentaCobro::with([
            'paciente', 'detalles', 'pagos.user', 'cajaSession.user'
        ])->findOrFail($cuentaId);

        return view('caja.comprobante', compact('cuenta'));
    }
}
