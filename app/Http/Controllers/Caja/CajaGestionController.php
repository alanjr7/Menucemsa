<?php

namespace App\Http\Controllers\Caja;

use App\Http\Controllers\Controller;
use App\Models\CajaSession;
use App\Models\CuentaCobro;
use App\Models\CuentaCobroDetalle;
use App\Models\CuentaCobroDetalleEliminado;
use App\Models\PagoCuenta;
use App\Models\MovimientoCaja;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class CajaGestionController extends Controller
{
    /**
     * Vista principal de gestión de caja (Admin)
     */
    public function index(): View
    {
        // Estadísticas generales - usar MovimientoCaja para reflejar movimientos reales
        $hoy = now()->toDateString();
        $inicioDia = now()->startOfDay();
        $finDia = now()->endOfDay();
        
        // Obtener todos los ingresos de hoy (excluyendo aperturas de caja)
        $ingresosHoy = MovimientoCaja::whereDate('created_at', $hoy)
            ->where('tipo', 'ingreso')
            ->where('concepto', 'like', 'Cobro%')
            ->get();
        
        // Calcular totales manualmente - usar el valor casteado del modelo
        $totalRecaudado = 0;
        $transacciones = $ingresosHoy->count();
        $metodosPagoHoy = ['efectivo' => 0, 'transferencia' => 0, 'tarjeta' => 0, 'qr' => 0];

        foreach ($ingresosHoy as $mov) {
            $monto = (float) $mov->monto;
            $totalRecaudado += $monto;
            $metodo = $mov->metodo_pago ?? 'efectivo';
            if (isset($metodosPagoHoy[$metodo])) {
                $metodosPagoHoy[$metodo] += $monto;
            }
        }
        
        $estadisticas = [
            'total_recaudado_hoy' => $totalRecaudado,
            'transacciones_hoy' => $transacciones,
            'cajas_abiertas' => CajaSession::abierta()->count(),
            'cajas_cerradas_hoy' => CajaSession::cerrada()->whereBetween('fecha_cierre', [$inicioDia, $finDia])->count(),
            'cuentas_pendientes' => CuentaCobro::pendiente()->count(),
            'emergencias_pendientes' => CuentaCobro::emergencias()->postPago()->whereIn('estado', ['pendiente', 'parcial'])->count(),
        ];

        // Cajas abiertas actualmente
        $cajasAbiertas = CajaSession::abierta()
            ->with(['user', 'movimientos'])
            ->get()
            ->map(function ($caja) {
                return [
                    'id' => $caja->id,
                    'usuario' => $caja->user->name ?? 'N/A',
                    'fecha_apertura' => $caja->fecha_apertura->format('d/m/Y H:i'),
                    'monto_inicial' => $caja->monto_inicial,
                    'total_ingresos' => $caja->ingresos()->sum('monto'),
                    'total_egresos' => $caja->egresos()->sum('monto'),
                    'duracion' => $caja->duracion,
                ];
            });

        // Transacciones recientes
        $transaccionesRecientes = PagoCuenta::with(['cuentaCobro.paciente', 'user'])
            ->delDia($hoy)
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        return view('caja.gestion', compact(
            'estadisticas',
            'metodosPagoHoy',
            'cajasAbiertas',
            'transaccionesRecientes'
        ));
    }

    /**
     * API: Obtener listado de transacciones con filtros
     */
    public function getTransacciones(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'fecha_inicio' => 'nullable|date',
                'fecha_fin' => 'nullable|date',
                'estado' => 'nullable|in:pagado,parcial,pendiente,todos',
                'tipo_flujo' => 'nullable|in:normal,emergencia,todos',
                'metodo_pago' => 'nullable|in:efectivo,transferencia,tarjeta,qr,todos',
            ]);

            $query = CuentaCobro::with(['paciente', 'referencia', 'pagos.user', 'cajaSession.user']);

            // Filtro por fecha
            if ($request->filled('fecha_inicio') && $request->filled('fecha_fin')) {
                // Se usa el final del día para la fecha fin
                $fechaFin = \Carbon\Carbon::parse($request->fecha_fin)->endOfDay();
                $query->whereBetween('created_at', [$request->fecha_inicio, $fechaFin]);
            } elseif ($request->filled('fecha_inicio')) {
                $query->whereDate('created_at', $request->fecha_inicio);
            } elseif (!$request->has('fecha_inicio')) {
                // Si no se envía el parámetro (carga inicial), por defecto hoy
                $query->whereDate('created_at', now()->toDateString());
            }

            // Filtro por estado
            if ($request->estado && $request->estado !== 'todos') {
                $query->where('estado', $request->estado);
            }

            // Filtro por tipo de flujo
            if ($request->tipo_flujo === 'emergencia') {
                $query->where('es_emergencia', true);
            } elseif ($request->tipo_flujo === 'normal') {
                $query->where('es_emergencia', false);
            }

            // Filtro por método de pago
            if ($request->metodo_pago && $request->metodo_pago !== 'todos') {
                $query->whereHas('pagos', function ($q) use ($request) {
                    $q->where('metodo_pago', $request->metodo_pago);
                });
            }

            $transacciones = $query->orderBy('created_at', 'desc')
                ->paginate(25)
                ->through(function ($cuenta) {
                    // Obtener nombre del paciente (desde relación o desde emergencia)
                    $nombrePaciente = $cuenta->paciente?->nombre;
                    if (empty($nombrePaciente) && $cuenta->es_emergencia && $cuenta->referencia) {
                        $emergency = $cuenta->referencia;
                        if ($emergency->patient_id) {
                            $paciente = \App\Models\Paciente::find($emergency->patient_id);
                            $nombrePaciente = $paciente?->nombre ?? 'Paciente Emergencia #' . $emergency->id;
                        } else {
                            $nombrePaciente = 'Paciente Emergencia #' . $emergency->id;
                        }
                    }

                    return [
                        'id' => $cuenta->id,
                        'paciente' => [
                            'ci' => $cuenta->paciente_ci,
                            'nombre' => $nombrePaciente ?? 'N/A',
                        ],
                        'tipo_flujo' => $cuenta->es_emergencia ? 'emergencia' : 'normal',
                        'es_post_pago' => $cuenta->es_post_pago,
                        'tipo_atencion' => $cuenta->tipo_atencion_label,
                        'monto' => $cuenta->total_calculado,
                        'total_pagado' => $cuenta->total_pagado,
                        'saldo_pendiente' => $cuenta->saldo_pendiente,
                        'estado' => $cuenta->estado,
                        'estado_label' => $cuenta->estado_label,
                        'estado_color' => $cuenta->estado_color,
                        'metodos_pago' => $cuenta->pagos->pluck('metodo_pago')->unique()->values(),
                        'usuario_caja' => $cuenta->cajaSession?->user?->name ?? 'N/A',
                        'fecha' => $cuenta->created_at->format('d/m/Y H:i'),
                        'fecha_pago' => $cuenta->pagos->last()?->created_at?->format('d/m/Y H:i'),
                    ];
                });

            return response()->json([
                'success' => true,
                'transacciones' => $transacciones
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar transacciones: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Obtener detalle completo de una transacción
     */
    public function getDetalleTransaccion(string $id): JsonResponse
    {
        try {
            $cuenta = CuentaCobro::with([
                'paciente',
                'detalles.tarifa',
                'pagos.user',
                'cajaSession.user',
                'referencia'
            ])->findOrFail($id);

            // Obtener referencia específica según tipo
            $referenciaDetalle = null;
            if ($cuenta->referencia) {
                $referencia = $cuenta->referencia;
                $referenciaDetalle = [
                    'id' => $referencia->id ?? null,
                    'codigo' => $referencia->code ?? $referencia->nro ?? $referencia->id ?? null,
                    'fecha' => $referencia->created_at?->format('d/m/Y H:i') ?? null,
                    'estado' => $referencia->status ?? $referencia->estado ?? null,
                ];

                // Datos específicos según el tipo
                if ($cuenta->tipo_atencion === 'emergencia' && $referencia) {
                    $referenciaDetalle['tipo_ingreso'] = $referencia->tipo_ingreso ?? null;
                    $referenciaDetalle['destino'] = $referencia->destination ?? null;
                    $referenciaDetalle['flujo_historial'] = $referencia->flujo_historial ?? [];
                }
            }

            return response()->json([
                'success' => true,
                'transaccion' => [
                    'id' => $cuenta->id,
                    'paciente' => [
                        'ci' => $cuenta->paciente_ci,
                        'nombre' => $cuenta->paciente->nombre ?? 'N/A',
                        'telefono' => $cuenta->paciente->telefono ?? 'N/A',
                    ],
                    'tipo_atencion' => $cuenta->tipo_atencion_label,
                    'tipo_flujo' => $cuenta->es_emergencia ? 'emergencia' : 'normal',
                    'es_post_pago' => $cuenta->es_post_pago,
                    'pre_pago' => !$cuenta->es_post_pago && !$cuenta->es_emergencia,
                    'estado' => $cuenta->estado,
                    'estado_label' => $cuenta->estado_label,
                    'total_calculado' => $cuenta->total_calculado,
                    'total_pagado' => $cuenta->total_pagado,
                    'saldo_pendiente' => $cuenta->saldo_pendiente,
                    'ci_nit_facturacion' => $cuenta->ci_nit_facturacion,
                    'razon_social' => $cuenta->razon_social,
                    'fecha_creacion' => $cuenta->created_at->format('d/m/Y H:i'),
                    'usuario_creacion' => $cuenta->cajaSession?->user?->name ?? 'Sistema',
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
                            'metodo_pago_icon' => $pago->metodo_pago_icon,
                            'referencia' => $pago->referencia,
                            'usuario' => $pago->user->name ?? 'N/A',
                            'fecha' => $pago->created_at->format('d/m/Y H:i'),
                        ];
                    }),
                    'referencia' => $referenciaDetalle,
                    'observaciones' => $cuenta->observaciones,
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
     * API: Obtener control de cajas (aperturas/cierres)
     */
    public function getControlCajas(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'fecha_inicio' => 'nullable|date',
                'fecha_fin' => 'nullable|date',
                'estado' => 'nullable|in:abierta,cerrada,todas',
                'user_id' => 'nullable|integer|exists:users,id',
            ]);

            $query = CajaSession::with(['user', 'movimientos']);

            // Filtros de fecha
            if ($request->filled('fecha_inicio') && $request->filled('fecha_fin')) {
                $fechaFin = \Carbon\Carbon::parse($request->fecha_fin)->endOfDay();
                $query->whereBetween('fecha_apertura', [$request->fecha_inicio, $fechaFin]);
            } elseif ($request->filled('fecha_inicio')) {
                $query->whereDate('fecha_apertura', $request->fecha_inicio);
            }

            // Filtro por estado
            if ($request->estado && $request->estado !== 'todas') {
                $query->where('estado', $request->estado);
            }

            // Filtro por usuario
            if ($request->user_id) {
                $query->where('user_id', $request->user_id);
            }

            $cajas = $query->orderBy('fecha_apertura', 'desc')
                ->paginate(20)
                ->through(function ($caja) {
                    $totalIngresos = $caja->ingresos()->sum('monto');
                    $totalEgresos = $caja->egresos()->sum('monto');
                    $totalEsperado = $caja->monto_inicial + $totalIngresos - $totalEgresos;
                    $diferencia = $caja->monto_final !== null 
                        ? $caja->monto_final - $totalEsperado 
                        : null;
                    
                    return [
                        'id' => $caja->id,
                        'user' => [
                            'id' => $caja->user_id,
                            'nombre' => $caja->user->name ?? 'N/A',
                        ],
                        'fecha_apertura' => $caja->fecha_apertura ? $caja->fecha_apertura->format('d/m/Y H:i') : 'N/A',
                        'monto_inicial' => $caja->monto_inicial,
                        'monto_final' => $caja->monto_final,
                        'total_ingresos' => $totalIngresos,
                        'total_egresos' => $totalEgresos,
                        'total_esperado' => $totalEsperado,
                        'diferencia' => $diferencia,
                        'transacciones_count' => $caja->movimientos()->count(),
                        'observaciones' => $caja->observaciones,
                    ];
                });

            return response()->json([
                'success' => true,
                'cajas' => $cajas
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar cajas: ' . $e->getMessage() . ' en ' . $e->getFile() . ':' . $e->getLine()
            ], 500);
        }
    }

    /**
     * API: Obtener resumen financiero por rango de fechas
     */
    public function getResumenFinanciero(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'fecha_inicio' => 'required|date',
                'fecha_fin' => 'required|date',
            ]);

            $fechaInicio = \Carbon\Carbon::parse($request->fecha_inicio)->startOfDay();
            $fechaFin = \Carbon\Carbon::parse($request->fecha_fin)->endOfDay();

            // Totales generales
            $totalesGenerales = [
                'total_recaudado' => (float) PagoCuenta::whereBetween('created_at', [$fechaInicio, $fechaFin])->sum('monto'),
                'total_transacciones' => PagoCuenta::whereBetween('created_at', [$fechaInicio, $fechaFin])->count(),
            ];

            // Desglose por método de pago
            $porMetodoPago = [
                'efectivo' => (float) PagoCuenta::whereBetween('created_at', [$fechaInicio, $fechaFin])->efectivo()->sum('monto'),
                'transferencia' => (float) PagoCuenta::whereBetween('created_at', [$fechaInicio, $fechaFin])->transferencia()->sum('monto'),
                'tarjeta' => (float) PagoCuenta::whereBetween('created_at', [$fechaInicio, $fechaFin])->tarjeta()->sum('monto'),
                'qr' => (float) PagoCuenta::whereBetween('created_at', [$fechaInicio, $fechaFin])->qr()->sum('monto'),
            ];

            // Debug: obtener pagos individuales para verificar
            $pagosHoy = PagoCuenta::whereBetween('created_at', [$fechaInicio, $fechaFin])
                ->get(['id', 'monto', 'metodo_pago', 'cuenta_cobro_id', 'created_at']);
            \Log::info('Fecha inicio: ' . $fechaInicio . ' | Fin: ' . $fechaFin);
            \Log::info('Total pagos encontrados: ' . $pagosHoy->count());
            \Log::info('Pagos del día: ' . $pagosHoy->toJson());
            \Log::info('Sumas por método: E=' . $porMetodoPago['efectivo'] . ' Q=' . $porMetodoPago['qr']);

            // Desglose por tipo de atención
            $porTipoAtencion = CuentaCobro::whereBetween('created_at', [$fechaInicio, $fechaFin])
                ->where('estado', 'pagado')
                ->selectRaw('tipo_atencion, COUNT(*) as cantidad, SUM(total_calculado) as total')
                ->groupBy('tipo_atencion')
                ->get()
                ->map(function ($item) {
                    return [
                        'tipo' => $item->tipo_atencion,
                        'cantidad' => $item->cantidad,
                        'total' => $item->total,
                    ];
                });

            // Flujos: normal vs emergencia
            $flujos = [
                'normal' => [
                    'cantidad' => CuentaCobro::whereBetween('created_at', [$fechaInicio, $fechaFin])
                        ->where('es_emergencia', false)
                        ->where('estado', 'pagado')
                        ->count(),
                    'monto' => CuentaCobro::whereBetween('created_at', [$fechaInicio, $fechaFin])
                        ->where('es_emergencia', false)
                        ->where('estado', 'pagado')
                        ->sum('total_calculado'),
                ],
                'emergencia' => [
                    'cantidad' => CuentaCobro::whereBetween('created_at', [$fechaInicio, $fechaFin])
                        ->where('es_emergencia', true)
                        ->where('estado', 'pagado')
                        ->count(),
                    'monto' => CuentaCobro::whereBetween('created_at', [$fechaInicio, $fechaFin])
                        ->where('es_emergencia', true)
                        ->where('estado', 'pagado')
                        ->sum('total_calculado'),
                    'post_pago' => CuentaCobro::whereBetween('created_at', [$fechaInicio, $fechaFin])
                        ->where('es_emergencia', true)
                        ->where('es_post_pago', true)
                        ->where('estado', 'pagado')
                        ->count(),
                ],
            ];

            // Cuentas pendientes
            $cuentasPendientes = CuentaCobro::pendiente()->get();
            $montoPendiente = $cuentasPendientes->sum(function($cuenta) {
                return $cuenta->saldo_pendiente;
            });
            
            $pendientes = [
                'total' => $cuentasPendientes->count(),
                'monto' => $montoPendiente,
                'emergencias' => CuentaCobro::emergencias()->postPago()->pendiente()->count(),
            ];

            return response()->json([
                'success' => true,
                'resumen' => [
                    'totales_generales' => $totalesGenerales,
                    'por_metodo_pago' => $porMetodoPago,
                    'por_tipo_atencion' => $porTipoAtencion,
                    'flujos' => $flujos,
                    'pendientes' => $pendientes,
                    'rango_fechas' => [
                        'inicio' => $fechaInicio,
                        'fin' => $fechaFin,
                    ],
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al generar resumen: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Obtener auditoría de movimientos
     */
    public function getAuditoria(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'fecha_inicio' => 'nullable|date',
                'fecha_fin' => 'nullable|date',
                'user_id' => 'nullable|integer|exists:users,id',
                'tipo_accion' => 'nullable|in:apertura,cierre,pago,ingreso,egreso,todos',
            ]);

            $query = MovimientoCaja::with(['cajaSession.user', 'movable']);

            // Filtros de fecha
            if ($request->filled('fecha_inicio') && $request->filled('fecha_fin')) {
                $fechaFin = \Carbon\Carbon::parse($request->fecha_fin)->endOfDay();
                $query->whereBetween('created_at', [$request->fecha_inicio, $fechaFin]);
            } elseif ($request->filled('fecha_inicio')) {
                $query->whereDate('created_at', $request->fecha_inicio);
            }

            // Filtro por usuario (a través de caja session)
            if ($request->user_id) {
                $query->whereHas('cajaSession', function ($q) use ($request) {
                    $q->where('user_id', $request->user_id);
                });
            }

            // Filtro por tipo de acción
            if ($request->tipo_accion && $request->tipo_accion !== 'todos') {
                $query->where('tipo', $request->tipo_accion);
            }

            $movimientos = $query->orderBy('created_at', 'desc')
                ->paginate(30)
                ->through(function ($mov) {
                    return [
                        'id' => $mov->id,
                        'fecha' => $mov->created_at->format('d/m/Y H:i'),
                        'usuario' => $mov->cajaSession?->user?->name ?? 'N/A',
                        'tipo' => $mov->tipo,
                        'tipo_label' => ucfirst($mov->tipo),
                        'concepto' => $mov->concepto,
                        'monto' => $mov->monto,
                        'monto_formateado' => ($mov->tipo === 'egreso' ? '-' : '+') . ' S/ ' . number_format($mov->monto, 2),
                        'metodo_pago' => $mov->metodo_pago,
                        'referencia' => $mov->referencia,
                        'caja_session_id' => $mov->caja_session_id,
                    ];
                });

            // Lista de usuarios para filtro
            $usuarios = User::whereHas('cajaSessions')->select('id', 'name')->get();

            return response()->json([
                'success' => true,
                'movimientos' => $movimientos,
                'usuarios' => $usuarios,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar auditoría: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Obtener datos de facturación pendientes
     */
    public function getDatosFacturacion(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'estado' => 'nullable|in:completo,incompleto,todos',
            ]);

            $query = CuentaCobro::with('paciente')
                ->where('estado', 'pagado')
                ->where(function ($q) {
                    $q->whereNull('ci_nit_facturacion')
                      ->orWhereNull('razon_social')
                      ->orWhere('ci_nit_facturacion', '')
                      ->orWhere('razon_social', '');
                });

            if ($request->estado === 'completo') {
                $query = CuentaCobro::with('paciente')
                    ->where('estado', 'pagado')
                    ->whereNotNull('ci_nit_facturacion')
                    ->whereNotNull('razon_social')
                    ->where('ci_nit_facturacion', '!=', '')
                    ->where('razon_social', '!=', '');
            }

            $cuentas = $query->orderBy('created_at', 'desc')
                ->paginate(25)
                ->through(function ($cuenta) {
                    $datosCompletos = !empty($cuenta->ci_nit_facturacion) && !empty($cuenta->razon_social);
                    
                    return [
                        'id' => $cuenta->id,
                        'paciente' => [
                            'ci' => $cuenta->paciente_ci,
                            'nombre' => $cuenta->paciente->nombre ?? 'N/A',
                        ],
                        'total' => $cuenta->total_calculado,
                        'fecha_pago' => $cuenta->updated_at->format('d/m/Y H:i'),
                        'ci_nit_facturacion' => $cuenta->ci_nit_facturacion,
                        'razon_social' => $cuenta->razon_social,
                        'datos_completos' => $datosCompletos,
                        'datos_faltantes' => $datosCompletos ? [] : array_filter([
                            empty($cuenta->ci_nit_facturacion) ? 'CI/NIT' : null,
                            empty($cuenta->razon_social) ? 'Razón Social' : null,
                        ]),
                    ];
                });

            return response()->json([
                'success' => true,
                'cuentas' => $cuentas
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar datos de facturación: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Listar usuarios con caja
     */
    public function getUsuariosCaja(): JsonResponse
    {
        try {
            $usuarios = User::whereHas('cajaSessions')
                ->select('id', 'name', 'email')
                ->get();

            return response()->json([
                'success' => true,
                'usuarios' => $usuarios
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar usuarios: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar un detalle de cuenta y guardar en historial
     */
    public function eliminarDetalle(Request $request, string $detalleId): JsonResponse
    {
        $request->validate(['motivo' => 'required|string|max:500']);

        $user = auth()->user();
        if (!$user->hasRole('admin') && !$user->hasRole('administrador')) {
            return response()->json(['success' => false, 'message' => 'No autorizado.'], 403);
        }

        DB::beginTransaction();
        try {
            $detalle = CuentaCobroDetalle::with('cuentaCobro')->findOrFail($detalleId);
            $cuenta = $detalle->cuentaCobro;

            if ($cuenta->estado === 'pagado') {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar ítems de una cuenta ya pagada.'
                ], 422);
            }

            CuentaCobroDetalleEliminado::create([
                'cuenta_cobro_id'        => $detalle->cuenta_cobro_id,
                'tipo_item'              => $detalle->tipo_item,
                'tarifa_id'              => $detalle->tarifa_id,
                'descripcion'            => $detalle->descripcion,
                'cantidad'               => $detalle->cantidad,
                'precio_unitario'        => $detalle->precio_unitario,
                'subtotal'               => $detalle->subtotal,
                'origen_type'            => $detalle->origen_type,
                'origen_id'              => $detalle->origen_id,
                'area_origen'            => $detalle->area_origen,
                'observaciones'          => $detalle->observaciones,
                'usuario_eliminacion_id' => $user->id,
                'motivo_eliminacion'     => $request->motivo,
                'eliminado_en'           => now(),
            ]);

            $nuevoTotal = max(0, (float) $cuenta->total_calculado - (float) $detalle->subtotal);
            $estadoNuevo = $cuenta->estado;
            if ($nuevoTotal <= 0) {
                $estadoNuevo = 'pendiente';
            } elseif ($cuenta->total_pagado > 0 && $nuevoTotal <= $cuenta->total_pagado) {
                $estadoNuevo = 'pagado';
            } elseif ($cuenta->total_pagado > 0 && $nuevoTotal > $cuenta->total_pagado) {
                $estadoNuevo = 'parcial';
            }

            $cuenta->update([
                'total_calculado' => $nuevoTotal,
                'estado'          => $estadoNuevo,
            ]);

            $detalle->delete();

            \Log::info('Detalle de cuenta eliminado', [
                'detalle_id'   => $detalleId,
                'cuenta_id'    => $cuenta->id,
                'motivo'       => $request->motivo,
                'user_id'      => $user->id,
                'subtotal'     => $detalle->subtotal,
            ]);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Ítem eliminado y registrado en historial.']);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error al eliminar detalle: ' . $e->getMessage(), ['user_id' => $user->id ?? null]);
            return response()->json(['success' => false, 'message' => 'Error al eliminar el ítem.'], 500);
        }
    }

    /**
     * Obtener listado de detalles eliminados con filtros
     */
    public function getDetallesEliminados(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'fecha_inicio' => 'nullable|date',
                'fecha_fin' => 'nullable|date',
                'cuenta_cobro_id' => 'nullable|string',
            ]);

            $query = CuentaCobroDetalleEliminado::with(['cuentaCobro.paciente', 'usuarioEliminacion']);

            if ($request->filled('fecha_inicio') && $request->filled('fecha_fin')) {
                $fechaFin = \Carbon\Carbon::parse($request->fecha_fin)->endOfDay();
                $query->whereBetween('eliminado_en', [$request->fecha_inicio, $fechaFin]);
            } elseif ($request->filled('fecha_inicio')) {
                $query->whereDate('eliminado_en', $request->fecha_inicio);
            }

            if ($request->filled('cuenta_cobro_id')) {
                $query->where('cuenta_cobro_id', $request->cuenta_cobro_id);
            }

            $eliminados = $query->orderBy('eliminado_en', 'desc')
                ->paginate(25)
                ->through(function ($item) {
                    return [
                        'id' => $item->id,
                        'cuenta_cobro_id' => $item->cuenta_cobro_id,
                        'paciente' => [
                            'ci' => $item->cuentaCobro?->paciente_ci ?? 'N/A',
                            'nombre' => $item->cuentaCobro?->paciente?->nombre ?? 'N/A',
                        ],
                        'tipo_item' => $item->tipo_item_label,
                        'descripcion' => $item->descripcion,
                        'cantidad' => $item->cantidad,
                        'precio_unitario' => $item->precio_unitario,
                        'subtotal' => $item->subtotal,
                        'motivo_eliminacion' => $item->motivo_eliminacion,
                        'usuario' => $item->usuarioEliminacion?->name ?? 'N/A',
                        'eliminado_en' => $item->eliminado_en->format('d/m/Y H:i'),
                    ];
                });

            return response()->json([
                'success' => true,
                'eliminados' => $eliminados,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar ítems eliminados: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Anular una cuenta de cobro pendiente
     */
    public function anularCuenta(Request $request, string $id): JsonResponse
    {
        $request->validate(['motivo' => 'required|string|max:500']);

        DB::beginTransaction();
        try {
            $cuenta = CuentaCobro::findOrFail($id);

            if ($cuenta->estado === 'pagado' && $cuenta->total_pagado > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede anular una cuenta ya pagada. Use devolución parcial.'
                ], 422);
            }

            $cuenta->update([
                'estado'       => 'anulado',
                'observaciones'=> ($cuenta->observaciones ? $cuenta->observaciones . ' | ' : '') .
                                 'ANULADO: ' . $request->motivo . ' por ' . auth()->user()->name .
                                 ' el ' . now()->format('d/m/Y H:i'),
            ]);

            \Log::info('Cuenta anulada', [
                'cuenta_id' => $id,
                'motivo'    => $request->motivo,
                'user_id'   => auth()->id(),
            ]);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Cuenta anulada correctamente']);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error al anular cuenta: ' . $e->getMessage(), ['user_id' => auth()->id()]);
            return response()->json(['success' => false, 'message' => 'Error al anular la cuenta.'], 500);
        }
    }
}
