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
use App\Services\AplicarSeguroService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CajaOperativaController extends Controller
{
    use \App\Traits\AuditLoggable;

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
        // Solo muestra cuentas que deben pagar (sin seguro, rechazado, o autorizado con copago)
        $cuentasPendientes = CuentaCobro::with(['paciente', 'detalles', 'seguro'])
            ->whereIn('estado', ['pendiente', 'parcial'])
            ->where(function ($query) {
                $query->whereNull('seguro_estado')
                      ->orWhere('seguro_estado', 'rechazado')
                      ->orWhere(function ($q) {
                          $q->where('seguro_estado', 'autorizado')
                            ->where('estado', 'parcial');
                      });
            })
            ->orderBy('created_at', 'desc')
            ->get();

        // Estadísticas del día
        $hoy = now()->toDateString();
        
        // Cuentas pendientes que son visibles en caja (no tienen seguro pendiente)
        $cuentasVisiblesQuery = CuentaCobro::where(function ($query) {
            $query->whereNull('seguro_estado')
                  ->orWhere('seguro_estado', 'rechazado');
        });
        
        $estadisticas = [
            'total_cobrado' => PagoCuenta::delDia($hoy)->sum('monto'),
            'transacciones' => PagoCuenta::delDia($hoy)->count(),
            'pendientes' => (clone $cuentasVisiblesQuery)->pendiente()->count(),
            'parciales' => CuentaCobro::parcial()->where('seguro_estado', 'autorizado')->count() 
                         + (clone $cuentasVisiblesQuery)->parcial()->count(),
            'pendientes_seguro' => CuentaCobro::pendientesSeguro()->count(),
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

            // Registrar en bitácora
            $this->logActivity(
                'abrir_caja',
                'Caja abierta - Monto inicial: Bs. ' . number_format($request->monto_inicial, 2),
                $caja
            );

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

            // Calcular totales esperados (excluir apertura/cierre para no doblar monto_inicial)
            $cobros = $caja->movimientos()->where('tipo', 'ingreso')->where('concepto', 'like', 'Cobro%')->sum('monto');
            $egresos = $caja->movimientos()->where('tipo', 'egreso')->where('concepto', '!=', 'Cierre de caja')->sum('monto');
            $totalIngresos = $cobros;
            $totalEgresos = $egresos;
            $totalEsperado = (float) $caja->monto_inicial + $cobros - $egresos;
            $diferencia = round($request->monto_final - $totalEsperado, 2);

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

            // Registrar en bitácora
            $this->logActivity(
                'cerrar_caja',
                'Caja cerrada - Monto final: Bs. ' . number_format($request->monto_final, 2) .
                ' - Diferencia: Bs. ' . number_format($diferencia, 2),
                $caja
            );

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
     * Solo muestra cuentas que deben pagar:
     * - Sin seguro (seguro_estado = null)
     * - Seguro rechazado (seguro_estado = 'rechazado')
     * - Seguro autorizado con copago pendiente (estado = 'parcial')
     * Excluye:
     * - Seguro pendiente de autorización (seguro_estado = 'pendiente_autorizacion')
     * - Seguro autorizado pagado completo (estado = 'pagado')
     */
    public function getPacientesPendientes(): JsonResponse
    {
        try {
            $cuentas = CuentaCobro::with(['paciente', 'referencia', 'detalles', 'pagos', 'seguro'])
                ->whereIn('estado', ['pendiente', 'parcial'])
                ->where(function ($query) {
                    $query->whereNull('seguro_estado')
                          ->orWhere('seguro_estado', 'rechazado')
                          ->orWhere(function ($q) {
                              $q->where('seguro_estado', 'autorizado')
                                ->where('estado', 'parcial');
                          });
                })
                ->orderBy('created_at', 'desc')
                ->get();

            // Agrupar por paciente_ci para consolidar múltiples cuentas en una sola fila
            $porPaciente = $cuentas->groupBy('paciente_ci');

            $resultado = $porPaciente->map(function ($cuentasPaciente) {
                // La más reciente es la principal (para cobrar primero)
                $principal = $cuentasPaciente->first();

                $pacienteNombre = 'N/A';
                $pacienteCi = $principal->paciente_ci;

                if ($principal->paciente) {
                    $pacienteNombre = $principal->paciente->nombre;
                } elseif ($principal->es_emergencia && $principal->referencia) {
                    $emergency = $principal->referencia;
                    if ($emergency->patient_id) {
                        $paciente = \App\Models\Paciente::find($emergency->patient_id);
                        if ($paciente) {
                            $pacienteNombre = $paciente->nombre;
                            $pacienteCi = $paciente->ci;
                        } else {
                            $pacienteNombre = 'Paciente Emergencia #' . $emergency->id;
                            $pacienteCi = $emergency->patient_id;
                        }
                    }
                }

                $totalCalculado = $cuentasPaciente->sum('total_calculado');
                $totalPagado    = $cuentasPaciente->sum('total_pagado');
                $saldoPendiente = $totalCalculado - $totalPagado;
                $itemsCount     = $cuentasPaciente->sum(fn($c) => $c->detalles->count());

                // Tipos de atención combinados
                $tipos = $cuentasPaciente->pluck('tipo_atencion')->unique()->implode(', ');

                // Seguro: usar el de la cuenta principal si tiene
                $infoSeguro = null;
                if ($principal->seguro_estado === 'autorizado' && $principal->seguro) {
                    $infoSeguro = [
                        'nombre'         => $principal->seguro->nombre_empresa,
                        'monto_cubierto' => $principal->seguro_monto_cobertura,
                        'monto_paciente' => $principal->seguro_monto_paciente,
                    ];
                }

                $esEmergencia = $cuentasPaciente->contains('es_emergencia', true);
                $estado = $saldoPendiente <= 0 ? 'pagado' : ($totalPagado > 0 ? 'parcial' : 'pendiente');

                return [
                    'id'              => $principal->id,
                    'cuenta_ids'      => $cuentasPaciente->pluck('id')->values(),
                    'paciente_ci'     => $pacienteCi,
                    'paciente_nombre' => $pacienteNombre,
                    'tipo_atencion'   => $tipos,
                    'total_calculado' => $totalCalculado,
                    'total_pagado'    => $totalPagado,
                    'saldo_pendiente' => $saldoPendiente,
                    'estado'          => $estado,
                    'estado_color'    => $estado === 'pendiente' ? 'yellow' : ($estado === 'parcial' ? 'orange' : 'green'),
                    'estado_label'    => ucfirst($estado),
                    'es_emergencia'   => $esEmergencia,
                    'es_post_pago'    => $principal->es_post_pago,
                    'seguro_estado'   => $principal->seguro_estado,
                    'info_seguro'     => $infoSeguro,
                    'created_at'      => $principal->created_at->format('d/m/Y H:i'),
                    'items_count'     => $itemsCount,
                ];
            })->values();

            return response()->json([
                'success' => true,
                'cuentas' => $resultado
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar pacientes: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener detalle consolidado de todas las cuentas pendientes de un paciente.
     * El ID pasado es la cuenta principal; se agrupan todas las cuentas del mismo paciente_ci.
     */
    public function getDetalleCuenta(string $id): JsonResponse
    {
        try {
            $cuentaPrincipal = CuentaCobro::with(['paciente', 'referencia', 'detalles.tarifa', 'pagos.user'])
                ->findOrFail($id);

            // Todas las cuentas pendientes del mismo paciente
            $todasCuentas = CuentaCobro::with(['detalles.tarifa', 'pagos.user', 'seguro'])
                ->where('paciente_ci', $cuentaPrincipal->paciente_ci)
                ->whereIn('estado', ['pendiente', 'parcial'])
                ->orderBy('created_at', 'asc')
                ->get();

            // Si solo hay una cuenta, usarla directamente
            if ($todasCuentas->count() === 1) {
                $todasCuentas = collect([$cuentaPrincipal]);
            }

            // Datos del paciente
            $pacienteNombre = 'N/A';
            $pacienteCi = $cuentaPrincipal->paciente_ci;
            $pacienteTelefono = 'N/A';

            if ($cuentaPrincipal->paciente) {
                $pacienteNombre  = $cuentaPrincipal->paciente->nombre;
                $pacienteTelefono = $cuentaPrincipal->paciente->telefono ?? 'N/A';
            } elseif ($cuentaPrincipal->es_emergencia && $cuentaPrincipal->referencia) {
                $emergency = $cuentaPrincipal->referencia;
                if ($emergency->patient_id) {
                    $paciente = \App\Models\Paciente::find($emergency->patient_id);
                    if ($paciente) {
                        $pacienteNombre  = $paciente->nombre;
                        $pacienteCi      = $paciente->ci;
                        $pacienteTelefono = $paciente->telefono ?? 'N/A';
                    } else {
                        $pacienteNombre = 'Paciente Emergencia #' . $emergency->id;
                        $pacienteCi     = $emergency->patient_id;
                    }
                }
            }

            // Consolidar detalles de todas las cuentas
            $detallesConsolidados = $todasCuentas->flatMap(function ($cuenta) {
                return $cuenta->detalles->map(function ($detalle) use ($cuenta) {
                    $origen = 'General';
                    if ($detalle->area_origen) {
                        $origen = ucfirst($detalle->area_origen);
                    } elseif ($detalle->origen_type === \App\Models\Emergency::class) {
                        $origen = 'Emergencia';
                    } elseif ($detalle->origen_type === \App\Models\Hospitalizacion::class) {
                        $origen = 'Internación';
                    } elseif ($detalle->origen_type === \App\Models\CitaQuirurgica::class) {
                        $origen = 'Cirugía';
                    } elseif ($cuenta->tipo_atencion) {
                        $origen = ucfirst($cuenta->tipo_atencion);
                    }

                    return [
                        'id'             => $detalle->id,
                        'tipo_item'      => $detalle->tipo_item_label,
                        'descripcion'    => '[' . $origen . '] ' . $detalle->descripcion,
                        'cantidad'       => $detalle->cantidad,
                        'precio_unitario' => $detalle->precio_unitario,
                        'subtotal'       => $detalle->subtotal,
                        'cuenta_id'      => $cuenta->id,
                    ];
                });
            });

            // Consolidar pagos de todas las cuentas
            $pagosConsolidados = $todasCuentas->flatMap(function ($cuenta) {
                return $cuenta->pagos->map(function ($pago) {
                    return [
                        'id'          => $pago->id,
                        'monto'       => $pago->monto,
                        'metodo_pago' => $pago->metodo_pago_label,
                        'referencia'  => $pago->referencia,
                        'usuario'     => $pago->user->name ?? 'N/A',
                        'fecha'       => $pago->created_at->format('d/m/Y H:i'),
                    ];
                });
            });

            $totalCalculado = $todasCuentas->sum('total_calculado');
            $totalPagado    = $todasCuentas->sum('total_pagado');
            $saldoPendiente = $totalCalculado - $totalPagado;

            $estado = $saldoPendiente <= 0 ? 'pagado' : ($totalPagado > 0 ? 'parcial' : 'pendiente');

            return response()->json([
                'success' => true,
                'cuenta' => [
                    'id'               => $cuentaPrincipal->id,
                    'cuenta_ids'       => $todasCuentas->pluck('id')->values(),
                    'paciente' => [
                        'ci'       => $pacienteCi,
                        'nombre'   => $pacienteNombre,
                        'telefono' => $pacienteTelefono,
                    ],
                    'tipo_atencion'      => $todasCuentas->pluck('tipo_atencion')->unique()->implode(', '),
                    'es_emergencia'      => $todasCuentas->contains('es_emergencia', true),
                    'es_post_pago'       => $cuentaPrincipal->es_post_pago,
                    'estado'             => $estado,
                    'estado_label'       => ucfirst($estado),
                    'total_calculado'    => $totalCalculado,
                    'total_pagado'       => $totalPagado,
                    'saldo_pendiente'    => $saldoPendiente,
                    'seguro'             => AplicarSeguroService::calcularProyeccion($cuentaPrincipal),
                    'ci_nit_facturacion' => $cuentaPrincipal->ci_nit_facturacion,
                    'razon_social'       => $cuentaPrincipal->razon_social,
                    'detalles'           => $detallesConsolidados->values(),
                    'pagos'              => $pagosConsolidados->values(),
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
            // Sanitizar monto: reemplazar coma por punto para soportar locales con coma decimal
            if ($request->has('monto')) {
                $request->merge([
                    'monto' => str_replace(',', '.', (string) $request->monto)
                ]);
            }

            $request->validate([
                'cuenta_cobro_id' => 'required|string|exists:cuenta_cobros,id',
                'cuenta_ids'      => 'nullable|array',
                'cuenta_ids.*'    => 'string|exists:cuenta_cobros,id',
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

            // Determinar qué cuentas cobrar: si vienen cuenta_ids, todas; si no, solo la principal
            $cuentaIds = $request->filled('cuenta_ids') ? $request->cuenta_ids : [$request->cuenta_cobro_id];
            $cuentas = CuentaCobro::with('paciente.seguro')
                ->whereIn('id', $cuentaIds)
                ->orderBy('created_at', 'asc')
                ->get();

            $cuenta = $cuentas->firstWhere('id', $request->cuenta_cobro_id) ?? $cuentas->first();

            // Calcular saldo total consolidado
            $saldoTotalPendiente = round($cuentas->sum(fn($c) => $c->saldo_pendiente), 2);

            // Aplicar seguro en la cuenta principal si corresponde
            $infoSeguro = AplicarSeguroService::aplicarSiCorresponde($cuenta);
            if ($infoSeguro['aplicado']) {
                $cuenta->refresh();
                $saldoTotalPendiente = round($cuentas->fresh()->sum(fn($c) => $c->saldo_pendiente), 2);
            }

            if ($saldoTotalPendiente <= 0) {
                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => $infoSeguro['aplicado']
                        ? 'Cobro completado. El seguro cubrio el total.'
                        : 'Cuenta ya saldada.',
                    'cuenta' => [
                        'id' => $cuenta->id,
                        'estado' => $cuenta->estado,
                        'total_pagado' => $cuenta->total_pagado,
                        'saldo_pendiente' => 0,
                    ]
                ]);
            }

            $montoPagar = $request->es_pago_total
                ? $saldoTotalPendiente
                : (float) $request->monto;

            if (round($montoPagar, 2) > $saldoTotalPendiente) {
                return response()->json([
                    'success' => false,
                    'message' => 'El monto a pagar no puede ser mayor al saldo pendiente'
                ], 400);
            }

            // Datos de facturación en la cuenta principal
            if ($request->ci_nit_facturacion) {
                $cuenta->ci_nit_facturacion = $request->ci_nit_facturacion;
            }
            if ($request->razon_social) {
                $cuenta->razon_social = $request->razon_social;
            }
            $cuenta->caja_session_id = $cajaAbierta->id;
            $cuenta->save();

            // Distribuir el pago entre todas las cuentas (de más antigua a más reciente)
            $montoRestante = $montoPagar;
            $ultimaCuentaPagada = $cuenta;
            foreach ($cuentas as $cuentaItem) {
                if ($montoRestante <= 0) break;
                $saldoItem = round($cuentaItem->saldo_pendiente, 2);
                if ($saldoItem <= 0) continue;

                $montoEstaCuenta = min($montoRestante, $saldoItem);
                $cuentaItem->caja_session_id = $cajaAbierta->id;
                $cuentaItem->save();
                $cuentaItem->registrarPago($montoEstaCuenta, $request->metodo_pago, $request->referencia, Auth::id());
                $cuentaItem->refresh();
                $montoRestante = round($montoRestante - $montoEstaCuenta, 2);
                $ultimaCuentaPagada = $cuentaItem;
            }
            $cuenta->refresh();

            // Actualizar Caja y Consulta si alguna cuenta quedó pagada
            $ultimaFactura = \App\Models\Caja::max('nro_factura') ?? 0;
            $nroFactura = $ultimaFactura + 1;
            $facturaGenerada = false;

            foreach ($cuentas as $cuentaItem) {
                $cuentaItem->refresh();
                if ($cuentaItem->estado === 'pagado') {
                    if (!$facturaGenerada) {
                        $caja = \App\Models\Caja::whereHas('consulta', function($q) use ($cuentaItem) {
                            $q->where('ci_paciente', $cuentaItem->paciente_ci);
                        })->whereNull('nro_factura')->first();
                        if ($caja) {
                            $caja->update(['nro_factura' => $nroFactura]);
                            $facturaGenerada = true;
                        }
                    }

                    $consulta = \App\Models\Consulta::where('ci_paciente', $cuentaItem->paciente_ci)
                        ->where('estado_pago', false)
                        ->whereDate('fecha', today())
                        ->first();
                    if ($consulta) {
                        $consulta->update(['estado_pago' => true]);
                    }

                    if ($cuentaItem->es_emergencia && $cuentaItem->es_post_pago) {
                        $emergency = Emergency::find($cuentaItem->referencia_id);
                        if ($emergency) {
                            $emergency->update(['paid' => true]);
                        }
                    }
                }
            }

            // Obtener nombre del paciente
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
                'concepto' => 'Cobro consolidado - Paciente: ' . $nombrePaciente,
                'monto' => $montoPagar,
                'metodo_pago' => $request->metodo_pago,
                'referencia' => $request->referencia,
                'movable_type' => 'App\Models\PagoCuenta',
                'movable_id' => 0,
            ]);

            DB::commit();

            $saldoRestante = round($cuentas->fresh()->sum(fn($c) => $c->saldo_pendiente), 2);
            $todosPagado   = $saldoRestante <= 0;

            $tipoPago = $todosPagado ? 'total' : 'parcial';
            $this->logActivity(
                'procesar_cobro_' . $tipoPago,
                'Cobro ' . $tipoPago . ' procesado - Paciente: ' . $nombrePaciente .
                ' - Monto: Bs. ' . number_format($montoPagar, 2) .
                ' - Método: ' . $request->metodo_pago,
                $cuenta
            );

            if ($todosPagado) {
                NotificationService::notifyAdmins('pago', 'Pago Completado', "Paciente: {$nombrePaciente} - Monto: Bs." . number_format($montoPagar, 2), route('caja.gestion.index'));
            }

            return response()->json([
                'success' => true,
                'message' => $todosPagado
                    ? 'Cuenta pagada completamente'
                    : 'Pago parcial registrado',
                'print_url' => route('caja.operativa.comprobante', $cuenta->id),
                'cuenta' => [
                    'id' => $cuenta->id,
                    'estado' => $todosPagado ? 'pagado' : 'parcial',
                    'estado_label' => $todosPagado ? 'Pagado' : 'Parcial',
                    'total_pagado' => $cuentas->sum('total_pagado'),
                    'saldo_pendiente' => $saldoRestante,
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
