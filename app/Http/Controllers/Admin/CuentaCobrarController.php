<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CuentaCobro;
use App\Models\CuentaCobroDetalle;
use App\Models\PagoCuenta;
use App\Models\Paciente;
use App\Models\Emergency;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class CuentaCobrarController extends Controller
{
    /**
     * Vista principal de cuentas por cobrar
     */
    public function index(Request $request)
    {
        $cuentaId = $request->get('cuenta');
        $cuentaSeleccionada = null;
        
        if ($cuentaId) {
            $cuentaSeleccionada = CuentaCobro::with(['paciente', 'detalles', 'pagos', 'referencia'])
                ->find($cuentaId);
        }

        // Todas las cuentas pendientes y parciales
        $cuentas = CuentaCobro::with(['paciente', 'referencia', 'detalles'])
            ->whereIn('estado', ['pendiente', 'parcial'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($cuenta) {
                $emergency = null;
                if ($cuenta->es_emergencia && $cuenta->referencia_type === Emergency::class) {
                    $emergency = Emergency::find($cuenta->referencia_id);
                }
                
                // Obtener nombre del paciente
                $pacienteNombre = 'N/A';
                if ($cuenta->paciente) {
                    $pacienteNombre = $cuenta->paciente->nombre;
                } elseif ($emergency && $emergency->patient_id) {
                    $paciente = Paciente::find($emergency->patient_id);
                    $pacienteNombre = $paciente?->nombre ?? 'Paciente #' . $emergency->patient_id;
                }
                
                return [
                    'id' => $cuenta->id,
                    'paciente_nombre' => $pacienteNombre,
                    'paciente_ci' => $cuenta->paciente_ci,
                    'tipo_atencion' => $cuenta->tipo_atencion_label,
                    'total_calculado' => $cuenta->total_calculado,
                    'total_pagado' => $cuenta->total_pagado,
                    'saldo_pendiente' => $cuenta->saldo_pendiente,
                    'estado' => $cuenta->estado,
                    'estado_label' => $cuenta->estado_label,
                    'estado_color' => $cuenta->estado_color,
                    'created_at' => $cuenta->created_at,
                    'es_emergencia' => $cuenta->es_emergencia,
                    'referencia_id' => $cuenta->referencia_id,
                    'referencia_type' => $cuenta->referencia_type,
                    'detalles' => $cuenta->detalles,
                ];
            });

        // Cuentas de emergencias activas
        $cuentasEmergencias = CuentaCobro::with(['paciente', 'referencia'])
            ->where('es_emergencia', true)
            ->whereIn('estado', ['pendiente', 'parcial'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($cuenta) {
                $emergency = null;
                if ($cuenta->referencia_type === Emergency::class) {
                    $emergency = Emergency::find($cuenta->referencia_id);
                }
                
                // Obtener nombre del paciente
                $pacienteNombre = 'N/A';
                if ($cuenta->paciente) {
                    $pacienteNombre = $cuenta->paciente->nombre;
                } elseif ($emergency && $emergency->patient_id) {
                    // Buscar paciente por patient_id de la emergencia
                    $paciente = Paciente::find($emergency->patient_id);
                    $pacienteNombre = $paciente?->nombre ?? 'Paciente #' . $emergency->patient_id;
                }
                
                return [
                    'id' => $cuenta->id,
                    'paciente' => $pacienteNombre,
                    'paciente_ci' => $cuenta->paciente_ci,
                    'emergency_code' => $emergency?->code,
                    'ubicacion_actual' => $emergency?->ubicacion_actual,
                    'tipo_atencion' => $cuenta->tipo_atencion_label,
                    'total' => $cuenta->total_calculado,
                    'pagado' => $cuenta->total_pagado,
                    'saldo' => $cuenta->saldo_pendiente,
                    'estado' => $cuenta->estado,
                    'fecha' => $cuenta->created_at->format('Y-m-d'),
                    'es_temporal' => $emergency?->is_temp_id ?? false,
                ];
            });

        // Estadísticas
        $stats = [
            'total_cobrar' => $cuentas->sum('saldo_pendiente'),
            'vencidas' => $cuentas->where('created_at', '<', Carbon::now()->subDays(30))->sum('saldo_pendiente'),
            'cuentas_activas' => $cuentas->count(),
            'morosidad' => $cuentas->count() > 0 
                ? round(($cuentas->where('created_at', '<', Carbon::now()->subDays(30))->count() / $cuentas->count()) * 100, 1)
                : 0,
        ];

        return view('admin.cuentas', compact('cuentas', 'cuentasEmergencias', 'stats', 'cuentaSeleccionada'));
    }

    /**
     * API: Listar todas las cuentas
     */
    public function apiIndex(): JsonResponse
    {
        $cuentas = CuentaCobro::with(['paciente', 'detalles.tarifa', 'pagos'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($cuenta) {
                return [
                    'id' => $cuenta->id,
                    'paciente' => $cuenta->paciente?->nombre ?? 'N/A',
                    'paciente_ci' => $cuenta->paciente_ci,
                    'tipo_atencion' => $cuenta->tipo_atencion_label,
                    'estado' => $cuenta->estado,
                    'estado_label' => $cuenta->estado_label,
                    'estado_color' => $cuenta->estado_color,
                    'total_calculado' => $cuenta->total_calculado,
                    'total_pagado' => $cuenta->total_pagado,
                    'saldo_pendiente' => $cuenta->saldo_pendiente,
                    'es_emergencia' => $cuenta->es_emergencia,
                    'es_post_pago' => $cuenta->es_post_pago,
                    'fecha' => $cuenta->created_at->format('Y-m-d'),
                    'detalles_count' => $cuenta->detalles->count(),
                    'pagos_count' => $cuenta->pagos->count(),
                ];
            });

        return response()->json([
            'success' => true,
            'cuentas' => $cuentas,
        ]);
    }

    /**
     * API: Obtener detalle de cuenta
     */
    public function show(string $id): JsonResponse
    {
        $cuenta = CuentaCobro::with(['paciente', 'detalles', 'pagos', 'referencia'])->findOrFail($id);

        // Si es emergencia, cargar datos adicionales
        $emergency = null;
        if ($cuenta->referencia_type === Emergency::class) {
            $emergency = Emergency::with(['paciente'])->find($cuenta->referencia_id);
        }

        return response()->json([
            'success' => true,
            'cuenta' => [
                'id' => $cuenta->id,
                'paciente' => [
                    'ci' => $cuenta->paciente_ci,
                    'nombre' => $cuenta->paciente?->nombre ?? 'N/A',
                    'telefono' => $cuenta->paciente?->telefono,
                ],
                'tipo_atencion' => $cuenta->tipo_atencion_label,
                'estado' => $cuenta->estado,
                'total_calculado' => $cuenta->total_calculado,
                'total_pagado' => $cuenta->total_pagado,
                'saldo_pendiente' => $cuenta->saldo_pendiente,
                'es_emergencia' => $cuenta->es_emergencia,
                'observaciones' => $cuenta->observaciones,
                'fecha' => $cuenta->created_at->format('Y-m-d H:i'),
                'detalles' => $cuenta->detalles->map(function($d) {
                    return [
                        'tipo' => $d->tipo_item_label,
                        'descripcion' => $d->descripcion,
                        'cantidad' => $d->cantidad,
                        'precio_unitario' => $d->precio_unitario,
                        'subtotal' => $d->subtotal,
                    ];
                }),
                'pagos' => $cuenta->pagos->map(function($p) {
                    return [
                        'monto' => $p->monto,
                        'metodo' => $p->metodo_pago,
                        'referencia' => $p->referencia,
                        'fecha' => $p->created_at->format('Y-m-d H:i'),
                    ];
                }),
                'emergency' => $emergency ? [
                    'code' => $emergency->code,
                    'status' => $emergency->status,
                    'ubicacion_actual' => $emergency->ubicacion_actual,
                    'is_temp_id' => $emergency->is_temp_id,
                ] : null,
            ],
        ]);
    }

    /**
     * API: Registrar pago
     */
    public function registrarPago(Request $request, string $id): JsonResponse
    {
        try {
            $validated = $request->validate([
                'monto' => 'required|numeric|min:0.01',
                'metodo_pago' => 'required|in:efectivo,transferencia,tarjeta,qr,cheque',
                'referencia' => 'nullable|string|max:255',
            ]);

            $cuenta = CuentaCobro::findOrFail($id);

            // Verificar que no exceda el saldo
            if ($validated['monto'] > $cuenta->saldo_pendiente) {
                return response()->json([
                    'success' => false,
                    'message' => 'El monto excede el saldo pendiente',
                ], 422);
            }

            // Registrar pago
            $pago = PagoCuenta::create([
                'cuenta_cobro_id' => $id,
                'monto' => $validated['monto'],
                'metodo_pago' => $validated['metodo_pago'],
                'referencia' => $validated['referencia'],
                'usuario_id' => auth()->id(),
            ]);

            // Actualizar cuenta
            $cuenta->total_pagado += $validated['monto'];
            
            // Calcular saldo para determinar estado
            $saldoPendiente = (float) $cuenta->total_calculado - (float) $cuenta->total_pagado;
            
            if ($saldoPendiente <= 0) {
                $cuenta->estado = 'pagado';
            } else {
                $cuenta->estado = $cuenta->total_pagado > 0 ? 'parcial' : 'pendiente';
            }
            
            $cuenta->save();

            // Si es emergencia, marcar como pagada
            if ($cuenta->es_emergencia && $cuenta->estado === 'pagado') {
                $emergency = Emergency::find($cuenta->referencia_id);
                if ($emergency) {
                    $emergency->update(['paid' => true]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Pago registrado exitosamente',
                'pago' => $pago,
                'cuenta' => [
                    'id' => $cuenta->id,
                    'estado' => $cuenta->estado,
                    'total_pagado' => $cuenta->total_pagado,
                    'saldo_pendiente' => $cuenta->saldo_pendiente,
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar pago: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * API: Obtener cuentas de emergencias activas
     */
    public function getCuentasEmergencias(): JsonResponse
    {
        $cuentas = CuentaCobro::with(['paciente', 'referencia'])
            ->where('es_emergencia', true)
            ->whereIn('estado', ['pendiente', 'parcial'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($cuenta) {
                $emergency = null;
                if ($cuenta->referencia_type === Emergency::class) {
                    $emergency = Emergency::find($cuenta->referencia_id);
                }

                return [
                    'id' => $cuenta->id,
                    'paciente' => $cuenta->paciente?->nombre ?? 'N/A',
                    'paciente_ci' => $cuenta->paciente_ci,
                    'emergency_code' => $emergency?->code,
                    'emergency_status' => $emergency?->status,
                    'ubicacion_actual' => $emergency?->ubicacion_actual,
                    'ubicacion_label' => $emergency?->ubicacion_label ?? 'N/A',
                    'total' => $cuenta->total_calculado,
                    'pagado' => $cuenta->total_pagado,
                    'saldo' => $cuenta->saldo_pendiente,
                    'estado' => $cuenta->estado,
                    'fecha' => $cuenta->created_at->format('Y-m-d H:i'),
                    'es_temporal' => $emergency?->is_temp_id ?? false,
                ];
            });

        return response()->json([
            'success' => true,
            'cuentas' => $cuentas,
        ]);
    }

    /**
     * API: Generar reporte de morosidad
     */
    public function getReporteMorosidad(): JsonResponse
    {
        $cuentasVencidas = CuentaCobro::with(['paciente'])
            ->where('created_at', '<', Carbon::now()->subDays(30))
            ->whereIn('estado', ['pendiente', 'parcial'])
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'cuentas' => $cuentasVencidas->map(function($c) {
                return [
                    'id' => $c->id,
                    'paciente' => $c->paciente?->nombre ?? 'N/A',
                    'dias_vencida' => $c->created_at->diffInDays(Carbon::now()),
                    'saldo' => $c->saldo_pendiente,
                ];
            }),
        ]);
    }
}
