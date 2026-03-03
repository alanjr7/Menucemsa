<?php

namespace App\Http\Controllers\Farmacia;

use App\Http\Controllers\Controller;
use App\Models\CajaDiaria;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CajaDiariaController extends Controller
{
    public function index()
    {
        return view('farmacia.caja-diaria');
    }

    /**
     * Obtener estado de la caja de hoy - Ruta específica que debe ir PRIMERO
     */
    public function apiEstadoHoy(): JsonResponse
    {
        try {
            Log::info('=== API ESTADO HOY ===');
            
            $hoy = Carbon::now()->format('Y-m-d');
            
            // Buscar caja abierta de hoy
            $caja = CajaDiaria::whereDate('fecha', $hoy)
                ->where('estado', 'abierta')
                ->first();
            
            if ($caja) {
                return response()->json([
                    'success' => true,
                    'hay_caja' => true,
                    'caja' => $caja
                ]);
            }
            
            return response()->json([
                'success' => true,
                'hay_caja' => false,
                'caja' => null,
                'mensaje' => 'No hay caja abierta para hoy'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error en apiEstadoHoy: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'hay_caja' => false,
                'caja' => null,
                'error' => 'Error interno: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mostrar una caja específica por ID
     */
    public function apiShow(int $id): JsonResponse
    {
        try {
            $caja = CajaDiaria::with('usuario')->findOrFail($id);
            return response()->json($caja);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Caja no encontrada'
            ], 404);
        }
    }

    /**
     * Abrir nueva caja
     */
    public function apiStore(Request $request): JsonResponse
    {
        try {
            Log::info('=== API STORE ===');
            
            $validated = $request->validate([
                'monto_inicial' => 'required|numeric|min:0',
                'observaciones' => 'nullable|string|max:500'
            ]);

            $hoy = Carbon::now()->format('Y-m-d');
            
            // Verificar si ya existe una caja ABIERTA hoy
            $cajaAbiertaExistente = CajaDiaria::whereDate('fecha', $hoy)
                ->where('estado', 'abierta')
                ->first();
            
            if ($cajaAbiertaExistente) {
                return response()->json([
                    'success' => false,
                    'error' => 'Ya existe una caja abierta para hoy'
                ], 422);
            }

            // Verificar si existe una caja CERRADA hoy para reabrir
            $cajaCerradaHoy = CajaDiaria::whereDate('fecha', $hoy)
                ->where('estado', 'cerrada')
                ->first();

            if ($cajaCerradaHoy) {
                // Reabrir la caja existente
                $cajaCerradaHoy->update([
                    'monto_inicial' => $validated['monto_inicial'],
                    'monto_final' => $validated['monto_inicial'],
                    'ventas_efectivo' => 0,
                    'ventas_qr' => 0,
                    'ventas_transferencia' => 0,
                    'ventas_tarjeta' => 0,
                    'total_ventas' => 0,
                    'estado' => 'abierta',
                    'usuario_id' => auth()->id() ?? 1,
                    'observaciones' => $validated['observaciones'] ?? null,
                    'hora_apertura' => now(),
                    'hora_cierre' => null
                ]);

                Log::info('Caja reabierta con ID: ' . $cajaCerradaHoy->id);

                return response()->json($cajaCerradaHoy, 200);
            }

            // Obtener usuario actual o usar ID 1 como fallback
            $usuarioId = auth()->id() ?? 1;

            // Crear nueva caja
            $caja = CajaDiaria::create([
                'fecha' => $hoy,
                'monto_inicial' => $validated['monto_inicial'],
                'monto_final' => $validated['monto_inicial'],
                'ventas_efectivo' => 0,
                'ventas_qr' => 0,
                'ventas_transferencia' => 0,
                'ventas_tarjeta' => 0,
                'total_ventas' => 0,
                'estado' => 'abierta',
                'usuario_id' => $usuarioId,
                'observaciones' => $validated['observaciones'] ?? null,
                'hora_apertura' => now()
            ]);

            Log::info('Caja creada con ID: ' . $caja->id);

            return response()->json($caja, 201);
            
        } catch (\Exception $e) {
            Log::error('Error en apiStore: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Error abriendo caja: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cerrar caja
     */
    public function apiCerrar(Request $request, int $id): JsonResponse
    {
        try {
            Log::info('=== API CERRAR === ID: ' . $id);
            
            $caja = CajaDiaria::findOrFail($id);
            
            if ($caja->estado === 'cerrada') {
                return response()->json([
                    'success' => false,
                    'error' => 'Caja ya cerrada'
                ], 422);
            }

            $validated = $request->validate([
                'observaciones' => 'nullable|string|max:500'
            ]);

            // Aquí podrías calcular los totales si es necesario
            $caja->estado = 'cerrada';
            $caja->hora_cierre = now();
            
            if (!empty($validated['observaciones'])) {
                $caja->observaciones = $caja->observaciones 
                    ? $caja->observaciones . ' | Cierre: ' . $validated['observaciones']
                    : 'Cierre: ' . $validated['observaciones'];
            }
            
            $caja->save();

            return response()->json($caja);
            
        } catch (\Exception $e) {
            Log::error('Error en apiCerrar: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Error cerrando caja: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Listar cajas (adicional)
     */
    public function apiIndex(): JsonResponse
    {
        try {
            $cajas = CajaDiaria::with('usuario')
                ->orderBy('fecha', 'desc')
                ->take(30)
                ->get();
            
            return response()->json($cajas);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error obteniendo cajas',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}