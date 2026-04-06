<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Caja;
use App\Models\CajaSession;
use App\Models\Paciente;
use App\Models\Consulta;
use App\Models\Tarifario;
use App\Models\Emergency;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class CajaController extends Controller
{
    public function index(): View
    {
        $fecha = now()->startOfDay();

        $movimientos = Caja::query()
            ->whereDate('fecha', $fecha)
            ->orderByDesc('fecha')
            ->limit(20)
            ->get();

        // Cajas abiertas desde el rol caja (caja_sessions)
        $cajasAbiertas = CajaSession::with('usuario')
            ->where('estado', 'abierta')
            ->orderByDesc('fecha_apertura')
            ->get();

        $ingresos = $movimientos->where('total_dia', '>=', 0);
        $egresos = $movimientos->where('total_dia', '<', 0);

        // Calcular pendientes (consultas pagadas pero no atendidas)
        $pendientesCount = Consulta::whereDate('created_at', $fecha)
            ->where('estado_pago', true)
            ->where('estado', 'pendiente')
            ->count();

        $metodosPago = [
            'EFECTIVO' => 0,
            'TARJETA' => 0,
            'TRANSFERENCIA' => 0,
        ];

        // Debug: mostrar información
        \Log::info('Caja Index - Fecha: ' . $fecha->format('Y-m-d'));
        \Log::info('Total movimientos: ' . $movimientos->count());
        \Log::info('Cajas abiertas (sessions): ' . $cajasAbiertas->count());
        \Log::info('Ingresos: ' . $ingresos->sum('total_dia'));
        \Log::info('Egresos: ' . abs($egresos->sum('total_dia')));

        return view('admin.caja', [
            'fecha' => $fecha,
            'movimientos' => $movimientos,
            'cajasAbiertas' => $cajasAbiertas,
            'resumen' => [
                'ingresos' => $ingresos->sum('total_dia'),
                'ingresos_count' => $ingresos->count(),
                'egresos' => abs($egresos->sum('total_dia')),
                'egresos_count' => $egresos->count(),
                'saldo' => $movimientos->sum('total_dia'),
                'pendientes_monto' => 0, // Por ahora 0, se puede calcular si hay montos asociados
                'pendientes_count' => $pendientesCount,
            ],
            'metodosPago' => $metodosPago,
        ]);
    }

    public function nuevoCobro(): View
    {
        $fecha = now()->startOfDay();

        $movimientos = Caja::query()
            ->whereDate('fecha', $fecha)
            ->orderByDesc('fecha')
            ->limit(20)
            ->get();

        $ingresos = $movimientos->where('total_dia', '>=', 0);
        $egresos = $movimientos->where('total_dia', '<', 0);

        // Calcular pendientes (consultas pagadas pero no atendidas)
        $pendientesCount = Consulta::whereDate('created_at', $fecha)
            ->where('estado_pago', true)
            ->where('estado', 'pendiente')
            ->count();

        $metodosPago = [
            'EFECTIVO' => 0,
            'TARJETA' => 0,
            'TRANSFERENCIA' => 0,
        ];

        return view('admin.caja', [
            'fecha' => $fecha,
            'movimientos' => $movimientos,
            'resumen' => [
                'ingresos' => $ingresos->sum('total_dia'),
                'ingresos_count' => $ingresos->count(),
                'egresos' => abs($egresos->sum('total_dia')),
                'egresos_count' => $egresos->count(),
                'saldo' => $movimientos->sum('total_dia'),
                'pendientes_monto' => 0, // Por ahora 0, se puede calcular si hay montos asociados
                'pendientes_count' => $pendientesCount,
            ],
            'metodosPago' => $metodosPago,
        ]);
    }

    public function getPacientesRegistrados(): JsonResponse
    {
        try {
            $pacientes = Paciente::select('ci', 'nombre', 'telefono', 'sexo')
                ->orderBy('nombre')
                ->limit(50)
                ->get();

            return response()->json([
                'success' => true,
                'pacientes' => $pacientes
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar pacientes: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getPacientesPendientes(): JsonResponse
    {
        try {
            // Pacientes con consultas pendientes de pago
            $consultasPendientes = Consulta::with('paciente')
                ->where('estado_pago', 'pendiente')
                ->whereDate('created_at', today())
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($consulta) {
                    return [
                        'id' => $consulta->id,
                        'tipo' => 'consulta',
                        'paciente_ci' => $consulta->ci_paciente,
                        'paciente_nombre' => $consulta->paciente->nombre,
                        'concepto' => 'Consulta Externa',
                        'monto' => 150.00, // Valor por defecto
                        'fecha' => $consulta->created_at->format('Y-m-d H:i:s'),
                        'estado' => 'pendiente'
                    ];
                });

            // Emergencias no pagadas
            $emergenciesPendientes = Emergency::with('patient')
                ->where('paid', false)
                ->whereDate('created_at', today())
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($emergency) {
                    return [
                        'id' => $emergency->id,
                        'tipo' => 'emergencia',
                        'paciente_ci' => $emergency->patient->dni ?? 'N/A',
                        'paciente_nombre' => $emergency->patient->name,
                        'concepto' => 'Emergencia - ' . $emergency->code,
                        'monto' => $emergency->cost,
                        'fecha' => $emergency->created_at->format('Y-m-d H:i:s'),
                        'estado' => 'pendiente'
                    ];
                });

            // Combinar ambos resultados
            $pendientes = $consultasPendientes->concat($emergenciesPendientes)
                ->sortByDesc('fecha')
                ->values();

            return response()->json([
                'success' => true,
                'pendientes' => $pendientes
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar pendientes: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getServiciosDisponibles(): JsonResponse
    {
        try {
            $servicios = Tarifario::select('id', 'descripcion', 'precio', 'categoria')
                ->where('activo', true)
                ->orderBy('categoria')
                ->orderBy('descripcion')
                ->get()
                ->groupBy('categoria');

            return response()->json([
                'success' => true,
                'servicios' => $servicios
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar servicios: ' . $e->getMessage()
            ], 500);
        }
    }

    public function procesarCobro(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'paciente_ci' => 'required|string',
                'paciente_nombre' => 'required|string',
                'concepto' => 'required|string',
                'monto' => 'required|numeric|min:0',
                'metodo_pago' => 'required|in:EFECTIVO,TARJETA,TRANSFERENCIA',
                'medico_ci' => 'required|string',
                'servicio_id' => 'nullable|integer',
                'tipo_servicio' => 'required|in:consulta,emergencia'
            ]);

            // Crear movimiento en caja central con solo las columnas existentes
            $movimiento = Caja::create([
                'fecha' => now(),
                'total_dia' => $request->monto,
                'tipo' => $request->concepto . ' - ' . $request->metodo_pago,
                'nro_factura' => $this->generarNumeroFactura(),
                'id_farmacia' => null, // No aplica para caja central
            ]);

            // Procesar según tipo de servicio
            if ($request->tipo_servicio === 'emergencia') {
                // Marcar emergencia como pagada
                $emergency = Emergency::find($request->servicio_id);
                if ($emergency) {
                    $emergency->update(['paid' => true]);
                    
                    \Log::info('Pago de emergencia procesado:', [
                        'emergency_id' => $emergency->id,
                        'emergency_code' => $emergency->code,
                        'movimiento_id' => $movimiento->id,
                        'monto' => $request->monto
                    ]);
                }
            } else {
                // Crear consulta médica asociada
                $consulta = Consulta::create([
                    'codigo' => 'CONS-' . date('YmdHis') . '-' . rand(100, 999),
                    'fecha' => now()->toDateString(),
                    'hora' => now()->toTimeString(),
                    'motivo' => $request->concepto,
                    'observaciones' => 'Cobro realizado en caja central - Médico: ' . $request->medico_ci,
                    'codigo_especialidad' => 'ESP001', // Especialidad por defecto (Medicina General)
                    'ci_paciente' => $request->paciente_ci,
                    'ci_medico' => $request->medico_ci,
                    'estado_pago' => true, // Pagado
                    'caja_id' => $movimiento->id,
                    'estado' => 'pendiente' // Pendiente de atención médica
                ]);

                // Si hay un servicio_id, actualizar estado de pago
                if ($request->servicio_id) {
                    $consultaExistente = Consulta::find($request->servicio_id);
                    if ($consultaExistente) {
                        $consultaExistente->update(['estado_pago' => true]);
                    }
                }

                // Debug: registrar en log
                \Log::info('Cobro de consulta procesado:', [
                    'movimiento_id' => $movimiento->id,
                    'consulta_nro' => $consulta->nro,
                    'monto' => $request->monto,
                    'paciente_ci' => $request->paciente_ci,
                    'medico_ci' => $request->medico_ci
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Cobro y consulta registrados correctamente',
                    'movimiento' => $movimiento,
                    'consulta' => $consulta
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Pago procesado correctamente',
                'movimiento' => $movimiento
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar cobro: ' . $e->getMessage()
            ], 500);
        }
    }

    public function imprimirCierre(Request $request): JsonResponse
    {
        try {
            $fecha = $request->get('fecha', now()->format('Y-m-d'));
            
            $movimientos = Caja::whereDate('fecha', $fecha)->get();
            
            $ingresos = $movimientos->where('total_dia', '>=', 0);
            $egresos = $movimientos->where('total_dia', '<', 0);
            
            $resumen = [
                'fecha' => $fecha,
                'ingresos' => $ingresos->sum('total_dia'),
                'egresos' => abs($egresos->sum('total_dia')),
                'saldo' => $movimientos->sum('total_dia'),
                'total_transacciones' => $movimientos->count(),
                'metodos_pago' => $movimientos->groupBy('tipo')->map(function ($grupo) {
                    return $grupo->sum('total_dia');
                })
            ];

            return response()->json([
                'success' => true,
                'resumen' => $resumen,
                'movimientos' => $movimientos
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al generar cierre: ' . $e->getMessage()
            ], 500);
        }
    }

    private function generarNumeroFactura()
    {
        // Obtener el último número de factura y generar el siguiente
        $ultimaFactura = Caja::whereNotNull('nro_factura')->max('nro_factura') ?? 0;
        return $ultimaFactura + 1;
    }
}
