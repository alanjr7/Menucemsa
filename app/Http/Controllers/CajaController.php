<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Caja;
use App\Models\Consulta;
use App\Models\Paciente;
use Illuminate\Support\Facades\DB;

class CajaController extends Controller
{
    public function index()
    {
        // Obtener consultas pendientes de pago (sin factura)
        $consultasPendientes = Caja::with([
            'consulta.paciente',
            'consulta.medico.usuario',
            'consulta.especialidad'
        ])
        ->where('tipo', 'CONSULTA_EXTERNA')
        ->whereNull('nro_factura') // Pendiente = sin factura
        ->whereDate('fecha', today())
        ->orderBy('created_at', 'desc')
        ->get();

        // Obtener pagos del día (con factura)
        $pagosDelDia = Caja::with([
            'consulta.paciente',
            'consulta.medico.usuario',
            'consulta.especialidad'
        ])
        ->where('tipo', 'CONSULTA_EXTERNA')
        ->whereNotNull('nro_factura') // Pagado = con factura
        ->whereDate('fecha', today())
        ->orderBy('updated_at', 'desc')
        ->get();

        // Estadísticas
        $totalPendiente = $consultasPendientes->sum('total_dia');
        $totalPagado = $pagosDelDia->sum('total_dia');
        $cantidadPendientes = $consultasPendientes->count();
        $cantidadPagadas = $pagosDelDia->count();

        // Resumen por forma de pago
        $resumenFormasPago = Caja::whereDate('fecha', today())
            ->whereNotNull('nro_factura')
            ->whereNotNull('metodo_pago')
            ->selectRaw('metodo_pago, SUM(total_dia) as total')
            ->groupBy('metodo_pago')
            ->pluck('total', 'metodo_pago')
            ->toArray();

        return view('admin.caja', compact(
            'consultasPendientes',
            'pagosDelDia',
            'totalPendiente',
            'totalPagado',
            'cantidadPendientes',
            'cantidadPagadas',
            'resumenFormasPago'
        ));
    }

    public function procesarPago(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $caja = Caja::with(['consulta.paciente', 'consulta.medico.usuario', 'consulta.especialidad'])->findOrFail($id);

            if ($caja->nro_factura) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este pago ya ha sido procesado'
                ]);
            }

            // Generar número de factura
            $caja->nro_factura = $this->generarNumeroFactura();
            $caja->estado = 'pagado';
            $caja->save();

            // Actualizar estado de la consulta (marcar como pagada)
            if ($caja->consulta) {
                $caja->consulta->estado_pago = true;
                $caja->consulta->save();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pago procesado exitosamente',
                'factura' => $caja->nro_factura,
                'paciente_nombre' => $caja->consulta->paciente->nombre ?? 'N/A',
                'servicio' => $caja->tipo,
                'monto' => $caja->total_dia
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar el pago: ' . $e->getMessage()
            ], 500);
        }
    }

    private function generarNumeroFactura()
    {
        // Obtener el último número de factura y generar el siguiente
        $ultimaFactura = Caja::whereNotNull('nro_factura')->max('nro_factura') ?? 0;
        return $ultimaFactura + 1;
    }

    public function verDetalles($id)
    {
        $caja = Caja::with([
            'consulta.paciente',
            'consulta.medico.usuario',
            'consulta.especialidad'
        ])->findOrFail($id);

        return view('caja.detalles', compact('caja'));
    }

    public function getPacientesPendientes()
    {
        // Get patients with pending payments from reception (exclude already paid)
        $pacientesPendientes = Caja::with(['consulta.paciente'])
            ->whereNull('nro_factura') // Pendientes de pago (sin factura)
            ->whereDate('fecha', today())
            ->whereDoesntHave('consulta', function ($query) {
                $query->where('estado_pago', true); // Excluir consultas ya pagadas
            })
            ->get()
            ->map(function ($caja) {
                return [
                    'id' => $caja->id,
                    'ci' => $caja->consulta->ci_paciente,
                    'nombre' => $caja->consulta->paciente->nombre,
                    'telefono' => $caja->consulta->paciente->telefono ?? 'N/A',
                    'sexo' => $caja->consulta->paciente->sexo ?? 'N/A',
                    'tipo_servicio' => $caja->tipo,
                    'monto' => $caja->total_dia,
                    'motivo' => $caja->consulta->motivo,
                    'fecha_registro' => $caja->created_at->format('H:i')
                ];
            });

        return response()->json([
            'success' => true,
            'pacientes' => $pacientesPendientes
        ]);
    }

    public function getServiciosDisponibles()
    {
        $servicios = \App\Models\Servicio::where('activo', true)
            ->orderBy('nombre')
            ->get(['codigo', 'nombre', 'descripcion', 'precio', 'tipo']);

        return response()->json([
            'success' => true,
            'servicios' => $servicios
        ]);
    }

    public function getPacientesRegistrados()
    {
        $pacientes = Paciente::select('ci', 'nombre', 'telefono', 'sexo')
            ->orderBy('nombre')
            ->get();

        return response()->json([
            'success' => true,
            'pacientes' => $pacientes
        ]);
    }

    public function procesarNuevoCobro(Request $request)
    {
        try {
            DB::beginTransaction();

            // Validate request data
            $request->validate([
                'ci_paciente' => 'required|string',
                'tipo_servicio' => 'required|string',
                'metodo_pago' => 'required|string|in:EFECTIVO,TARJETA,QR,TRANSFERENCIA',
                'referencia' => 'nullable|string'
            ]);

            // Get patient
            $paciente = Paciente::findOrFail($request->ci_paciente);

            // Check if there are ANY pending payments for this patient and service type today
            $registrosPendientes = Caja::where('tipo', $request->tipo_servicio)
                ->whereDate('fecha', today())
                ->whereNull('nro_factura') // Pendiente de pago
                ->get();

            // Find the first pending record that belongs to this patient
            $registroPendiente = null;
            foreach ($registrosPendientes as $registro) {
                if ($registro->consulta && $registro->consulta->ci_paciente == $request->ci_paciente) {
                    $registroPendiente = $registro;
                    break;
                }
            }

            if ($registroPendiente) {
                // Process existing pending payment instead of creating a new one
                $registroPendiente->nro_factura = $this->generarNumeroFactura();
                $registroPendiente->metodo_pago = $request->metodo_pago;
                $registroPendiente->referencia = $request->referencia;
                $registroPendiente->estado = 'pagado';
                $registroPendiente->save();

                // Update consultation payment status
                if ($registroPendiente->consulta) {
                    $registroPendiente->consulta->estado_pago = true;
                    $registroPendiente->consulta->save();
                }

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Pago procesado exitosamente (registro existente)',
                    'factura' => $registroPendiente->nro_factura,
                    'caja_id' => $registroPendiente->id
                ]);
            }

            // Log for debugging: check if there are other pending records that don't belong to this patient
            if ($registrosPendientes->count() > 0) {
                \Log::info('Found pending records but none for patient ' . $request->ci_paciente . '. Total pending: ' . $registrosPendientes->count());
            }

            // Get service price from database
            $servicio = \App\Models\Servicio::getServicioPorTipo($request->tipo_servicio);
            if (!$servicio) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tipo de servicio no encontrado o no disponible'
                ], 400);
            }

            // Create caja record with automatic price
            $caja = Caja::create([
                'fecha' => now(),
                'total_dia' => $servicio->precio,
                'tipo' => $request->tipo_servicio,
                'nro_factura' => $this->generarNumeroFactura(),
                'id_farmacia' => null,
                'metodo_pago' => $request->metodo_pago,
                'referencia' => $request->referencia,
                'estado' => 'pagado'
            ]);

            // Only create consultation record if it's a medical service AND there are doctors available
            if (in_array($request->tipo_servicio, ['CONSULTA_EXTERNA', 'EMERGENCIA'])) {
                // Check if there are any doctors available
                $doctorCount = \App\Models\Medico::count();
                
                if ($doctorCount > 0) {
                    // Get first available doctor as default
                    $doctor = \App\Models\Medico::first();
                    
                    Consulta::create([
                        'fecha' => now()->toDateString(),
                        'hora' => now()->toTimeString(),
                        'motivo' => 'Cobro directo en caja - ' . $servicio->nombre,
                        'observaciones' => 'Método de pago: ' . $request->metodo_pago . ($request->referencia ? ' - Ref: ' . $request->referencia : ''),
                        'codigo_especialidad' => 'GENERAL',
                        'ci_paciente' => $request->ci_paciente,
                        'ci_medico' => $doctor->ci,
                        'estado_pago' => true,
                        'id_caja' => $caja->id,
                    ]);
                }
                // If no doctors available, we skip consultation creation
                // The payment is still registered in caja
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Cobro procesado exitosamente',
                'factura' => $caja->nro_factura,
                'caja_id' => $caja->id,
                'servicio' => $servicio->nombre,
                'monto' => $servicio->precio
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar el cobro: ' . $e->getMessage()
            ], 500);
        }
    }

    public function reporteDiario()
    {
        $pagosDelDia = Caja::with([
            'consulta.paciente',
            'consulta.medico.usuario',
            'consulta.especialidad'
        ])
        ->where('tipo', 'CONSULTA_EXTERNA')
        ->where('estado', 'pagado')
        ->whereDate('fecha', today())
        ->orderBy('updated_at', 'desc')
        ->get();

        $totalDelDia = $pagosDelDia->sum('monto_pagado');
        $cantidadTotal = $pagosDelDia->count();

        return view('caja.reporte', compact(
            'pagosDelDia',
            'totalDelDia',
            'cantidadTotal'
        ));
    }
}
