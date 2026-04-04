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
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CajaOperativaController extends Controller
{
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
                'usuario_id' => Auth::id(),
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
                'observaciones' => 'Monto final al cierre'
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
            $cuentas = CuentaCobro::with(['paciente', 'detalles.tarifa', 'pagos'])
                ->whereIn('estado', ['pendiente', 'parcial'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($cuenta) {
                    return [
                        'id' => $cuenta->id,
                        'paciente_ci' => $cuenta->paciente_ci,
                        'paciente_nombre' => $cuenta->paciente->nombre ?? 'N/A',
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
            $cuenta = CuentaCobro::with(['paciente', 'detalles.tarifa', 'pagos.usuario'])
                ->findOrFail($id);

            // Determinar referencia (consulta, emergencia, etc.)
            $referencia = null;
            if ($cuenta->referencia_type) {
                $referencia = $cuenta->referencia;
            }

            return response()->json([
                'success' => true,
                'cuenta' => [
                    'id' => $cuenta->id,
                    'paciente' => [
                        'ci' => $cuenta->paciente_ci,
                        'nombre' => $cuenta->paciente->nombre ?? 'N/A',
                        'telefono' => $cuenta->paciente->telefono ?? 'N/A',
                    ],
                    'tipo_atencion' => $cuenta->tipo_atencion_label,
                    'es_emergencia' => $cuenta->es_emergencia,
                    'es_post_pago' => $cuenta->es_post_pago,
                    'estado' => $cuenta->estado,
                    'estado_label' => $cuenta->estado_label,
                    'total_calculado' => $cuenta->total_calculado,
                    'total_pagado' => $cuenta->total_pagado,
                    'saldo_pendiente' => $cuenta->saldo_pendiente,
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
                            'usuario' => $pago->usuario->name ?? 'N/A',
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

            // Si es pago total, usar el saldo pendiente
            $montoPagar = $request->es_pago_total 
                ? $cuenta->saldo_pendiente 
                : $request->monto;

            // Validar que no pague más de lo pendiente
            if ($montoPagar > $cuenta->saldo_pendiente) {
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
            $cuenta->usuario_caja_id = Auth::id();
            $cuenta->save();

            // Registrar el pago
            $pago = PagoCuenta::create([
                'cuenta_cobro_id' => $cuenta->id,
                'monto' => $montoPagar,
                'metodo_pago' => $request->metodo_pago,
                'referencia' => $request->referencia,
                'usuario_id' => Auth::id(),
                'caja_session_id' => $cajaAbierta->id,
            ]);

            // Actualizar totales de la cuenta
            $cuenta->total_pagado += $montoPagar;
            $cuenta->saldo_pendiente = $cuenta->total_calculado - $cuenta->total_pagado;

            if ($cuenta->saldo_pendiente <= 0) {
                $cuenta->estado = 'pagado';
                $cuenta->saldo_pendiente = 0;
            } else {
                $cuenta->estado = 'parcial';
            }
            $cuenta->save();

            // Registrar movimiento en caja
            MovimientoCaja::create([
                'caja_session_id' => $cajaAbierta->id,
                'tipo' => 'ingreso',
                'concepto' => 'Cobro ' . $cuenta->tipo_atencion_label . ' - Paciente: ' . $cuenta->paciente->nombre,
                'monto' => $montoPagar,
                'metodo_pago' => $request->metodo_pago,
                'referencia' => $request->referencia,
                'movable_type' => PagoCuenta::class,
                'movable_id' => $pago->id,
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

            return response()->json([
                'success' => true,
                'message' => $cuenta->estado === 'pagado' 
                    ? 'Cuenta pagada completamente' 
                    : 'Pago parcial registrado',
                'pago_id' => $pago->id,
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
}
