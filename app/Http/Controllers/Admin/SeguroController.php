<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Seguro;
use App\Models\Paciente;
use App\Models\CuentaCobro;
use Illuminate\Http\JsonResponse;

class SeguroController extends Controller
{
    /**
     * Vista principal de gestión de seguros
     */
    public function index()
    {
        $seguros = Seguro::all();
        $totalAfiliados = Paciente::select('seguro_id')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('seguro_id')
            ->pluck('total', 'seguro_id');

        // Pre-autorizaciones pendientes (cuentas por cobrar con seguro)
        $preautorizaciones = CuentaCobro::with(['paciente', 'paciente.seguro'])
            ->whereHas('paciente', function($q) {
                $q->whereNotNull('seguro_id');
            })
            ->whereIn('estado', ['pendiente', 'parcial'])
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get()
            ->map(function($cuenta) {
                return [
                    'id' => $cuenta->id,
                    'numero' => $cuenta->id,
                    'fecha' => $cuenta->created_at->format('Y-m-d'),
                    'paciente' => $cuenta->paciente?->nombre ?? 'N/A',
                    'seguro' => $cuenta->paciente?->seguro?->nombre_empresa ?? 'Sin seguro',
                    'servicio' => $cuenta->tipo_atencion_label,
                    'monto' => $cuenta->total_calculado,
                    'estado' => $cuenta->estado,
                ];
            });

        // Estadísticas
        $stats = [
            'pendientes' => $preautorizaciones->where('estado', 'pendiente')->count(),
            'en_proceso' => $preautorizaciones->where('estado', 'parcial')->count(),
            'aprobadas' => CuentaCobro::where('estado', 'pagado')
                ->whereHas('paciente', function($q) {
                    $q->whereNotNull('seguro_id');
                })->count(),
            'monto_total' => $preautorizaciones->sum('monto'),
        ];

        return view('admin.seguros', compact('seguros', 'totalAfiliados', 'preautorizaciones', 'stats'));
    }

    /**
     * API: Listar todos los seguros
     */
    public function apiIndex(): JsonResponse
    {
        $seguros = Seguro::all()->map(function($seguro) {
            return [
                'id' => $seguro->id,
                'nombre_empresa' => $seguro->nombre_empresa,
                'tipo' => $seguro->tipo,
                'cobertura' => $seguro->cobertura,
                'telefono' => $seguro->telefono,
                'formulario' => $seguro->formulario,
                'estado' => $seguro->estado,
                'total_afiliados' => Paciente::where('seguro_id', $seguro->id)->count(),
            ];
        });

        return response()->json([
            'success' => true,
            'seguros' => $seguros,
        ]);
    }

    /**
     * API: Crear nuevo seguro
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'nombre_empresa' => 'required|string|max:255|unique:seguros,nombre_empresa',
                'tipo' => 'required|string|max:100',
                'cobertura' => 'nullable|string|max:255',
                'telefono' => 'nullable|string|max:50',
                'formulario' => 'nullable|string|max:100',
            ]);

            $validated['estado'] = 'activo';

            $seguro = Seguro::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Seguro creado exitosamente',
                'seguro' => $seguro,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear seguro: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * API: Actualizar seguro
     */
    public function update(Request $request, Seguro $seguro): JsonResponse
    {
        try {
            $validated = $request->validate([
                'nombre_empresa' => 'required|string|max:255|unique:seguros,nombre_empresa,' . $seguro->id,
                'tipo' => 'required|string|max:100',
                'cobertura' => 'nullable|string|max:255',
                'telefono' => 'nullable|string|max:50',
                'formulario' => 'nullable|string|max:100',
                'estado' => 'nullable|in:activo,inactivo',
            ]);

            $seguro->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Seguro actualizado exitosamente',
                'seguro' => $seguro,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar seguro: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * API: Eliminar seguro
     */
    public function destroy(Seguro $seguro): JsonResponse
    {
        try {
            // Verificar si hay pacientes afiliados
            $afiliados = Paciente::where('seguro_id', $seguro->id)->count();
            if ($afiliados > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "No se puede eliminar. Tiene {$afiliados} pacientes afiliados.",
                ], 422);
            }

            $seguro->delete();

            return response()->json([
                'success' => true,
                'message' => 'Seguro eliminado exitosamente',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar seguro: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * API: Obtener detalle de seguro con afiliados
     */
    public function show(Seguro $seguro): JsonResponse
    {
        $pacientes = Paciente::where('seguro_id', $seguro->id)
            ->limit(50)
            ->get(['ci', 'nombre', 'telefono']);

        return response()->json([
            'success' => true,
            'seguro' => $seguro,
            'total_afiliados' => Paciente::where('seguro_id', $seguro->id)->count(),
            'pacientes' => $pacientes,
        ]);
    }

    /**
     * API: Obtener pre-autorizaciones
     */
    public function getPreautorizaciones(): JsonResponse
    {
        $preautorizaciones = CuentaCobro::with(['paciente', 'paciente.seguro'])
            ->whereHas('paciente', function($q) {
                $q->whereNotNull('seguro_id');
            })
            ->whereIn('estado', ['pendiente', 'parcial'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($cuenta) {
                return [
                    'id' => $cuenta->id,
                    'numero' => $cuenta->id,
                    'fecha' => $cuenta->created_at->format('Y-m-d'),
                    'paciente' => $cuenta->paciente?->nombre ?? 'N/A',
                    'paciente_ci' => $cuenta->paciente_ci,
                    'seguro' => $cuenta->paciente?->seguro?->nombre_empresa ?? 'Sin seguro',
                    'seguro_id' => $cuenta->paciente?->seguro_id,
                    'servicio' => $cuenta->tipo_atencion_label,
                    'monto' => $cuenta->total_calculado,
                    'saldo' => $cuenta->saldo_pendiente,
                    'estado' => $cuenta->estado,
                    'estado_label' => $cuenta->estado_label,
                ];
            });

        return response()->json([
            'success' => true,
            'preautorizaciones' => $preautorizaciones,
        ]);
    }

    /**
     * API: Cambiar estado de pre-autorización
     */
    public function cambiarEstadoPreautorizacion(Request $request, string $cuentaId): JsonResponse
    {
        try {
            $validated = $request->validate([
                'estado' => 'required|in:aprobado,rechazado,en_proceso',
            ]);

            $cuenta = CuentaCobro::findOrFail($cuentaId);
            
            // Aquí podrías agregar lógica adicional según el estado
            if ($validated['estado'] === 'aprobado') {
                // Marcar como pagado por el seguro
                $cuenta->update([
                    'estado' => 'pagado',
                    'total_pagado' => $cuenta->total_calculado,
                    'saldo_pendiente' => 0,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Estado actualizado exitosamente',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cambiar estado: ' . $e->getMessage(),
            ], 500);
        }
    }
}
