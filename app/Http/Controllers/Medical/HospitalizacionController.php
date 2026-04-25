<?php

namespace App\Http\Controllers\Medical;

use App\Http\Controllers\Controller;
use App\Models\Hospitalizacion;
use App\Models\AlmacenMedicamento;
use App\Services\CuentaCobroService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class HospitalizacionController extends Controller
{
    public function detalle($id)
    {
        $hospitalizacion = Hospitalizacion::with([
            'paciente', 'medico.user', 'habitacion', 'cama',
            'medicamentosAdministrados.medicamento',
        ])->findOrFail($id);

        return view('medical.internacion-detalle', compact('hospitalizacion'));
    }

    /**
     * Guardar evolución de internación con medicamentos y equipos médicos
     */
    public function guardarEvolucion(Request $request, Hospitalizacion $hospitalizacion): JsonResponse
    {
        $validated = $request->validate([
            'diagnostico' => 'nullable|string',
            'tratamiento' => 'nullable|string',
            'medicamentos' => 'nullable|array',
            'medicamentos.*.id' => 'required_with:medicamentos|exists:almacen_medicamentos,id',
            'medicamentos.*.cantidad' => 'required_with:medicamentos|integer|min:1',
            'equipos_medicos' => 'nullable|array',
            'equipos_medicos.*.nombre' => 'required_with:equipos_medicos|string|max:255',
            'equipos_medicos.*.precio' => 'required_with:equipos_medicos|numeric|min:0',
            'equipos_medicos.*.cantidad' => 'required_with:equipos_medicos|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            // 1. Actualizar datos de la hospitalización
            $hospitalizacion->update([
                'diagnostico' => $validated['diagnostico'] ?? $hospitalizacion->diagnostico,
                'tratamiento' => $validated['tratamiento'] ?? $hospitalizacion->tratamiento,
            ]);

            // 2. Procesar medicamentos seleccionados
            $medicamentosAplicados = [];
            $totalMedicamentos = 0;

            if (!empty($validated['medicamentos'])) {
                foreach ($validated['medicamentos'] as $med) {
                    $medicamento = AlmacenMedicamento::find($med['id']);

                    if ($medicamento && $medicamento->cantidad >= $med['cantidad']) {
                        // Descontar del inventario
                        $medicamento->cantidad -= $med['cantidad'];
                        $medicamento->save();

                        // Registrar uso
                        $medicamentosAplicados[] = [
                            'id' => $medicamento->id,
                            'nombre' => $medicamento->nombre,
                            'cantidad' => $med['cantidad'],
                            'precio_unitario' => $medicamento->precio ?? 0,
                            'subtotal' => ($medicamento->precio ?? 0) * $med['cantidad'],
                            'unidad_medida' => $medicamento->unidad_medida,
                        ];

                        $totalMedicamentos += ($medicamento->precio ?? 0) * $med['cantidad'];
                    }
                }
            }

            // 3. Procesar equipos médicos agregados
            $equiposMedicosAplicados = [];
            $totalEquiposMedicos = 0;

            if (!empty($validated['equipos_medicos'])) {
                foreach ($validated['equipos_medicos'] as $equipo) {
                    $subtotal = $equipo['precio'] * $equipo['cantidad'];
                    $equiposMedicosAplicados[] = [
                        'nombre' => $equipo['nombre'],
                        'precio_unitario' => $equipo['precio'],
                        'cantidad' => $equipo['cantidad'],
                        'subtotal' => $subtotal,
                    ];
                    $totalEquiposMedicos += $subtotal;
                }
            }

            // 4. Obtener o crear cuenta de cobro (sin agregar tarifa base si ya existe)
            $pacienteCi = $hospitalizacion->ci_paciente;
            if ($pacienteCi) {
                $cuenta = CuentaCobroService::obtenerOCrearCuentaInternacion(
                    (string) $pacienteCi,
                    $hospitalizacion->id,
                    $hospitalizacion->paciente?->seguro?->id
                );

                // Agregar cargos por medicamentos
                if (!empty($medicamentosAplicados)) {
                    foreach ($medicamentosAplicados as $med) {
                        if ($med['subtotal'] > 0) {
                            CuentaCobroService::agregarCargo(
                                $cuenta->id,
                                'medicamento',
                                'Internación - ' . $med['nombre'] . ' (' . $med['cantidad'] . ' ' . $med['unidad_medida'] . ')',
                                $med['precio_unitario'],
                                $med['cantidad'],
                                null,
                                AlmacenMedicamento::class,
                                $med['id']
                            );
                        }
                    }
                }

                // Agregar cargos por equipos médicos
                if (!empty($equiposMedicosAplicados)) {
                    foreach ($equiposMedicosAplicados as $equipo) {
                        if ($equipo['subtotal'] > 0) {
                            CuentaCobroService::agregarCargo(
                                $cuenta->id,
                                'equipo_medico',
                                'Internación - Equipo/Procedimiento: ' . $equipo['nombre'],
                                $equipo['precio_unitario'],
                                $equipo['cantidad'],
                                null,
                                Hospitalizacion::class,
                                $hospitalizacion->id
                            );
                        }
                    }
                }
            }

            // 5. Actualizar detalle_costos de la hospitalización
            $detalleCostos = $hospitalizacion->equipos_medicos ?? [];
            $detalleCostos[] = [
                'tipo' => 'evolucion',
                'fecha' => now()->toDateTimeString(),
                'diagnostico' => $validated['diagnostico'] ?? null,
                'tratamiento' => $validated['tratamiento'] ?? null,
                'medicamentos' => $medicamentosAplicados,
                'total_medicamentos' => $totalMedicamentos,
                'equipos_medicos' => $equiposMedicosAplicados,
                'total_equipos_medicos' => $totalEquiposMedicos,
                'usuario_id' => auth()->id(),
            ];

            $hospitalizacion->update([
                'equipos_medicos' => $detalleCostos,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Evolución guardada correctamente',
                'medicamentos_aplicados' => count($medicamentosAplicados),
                'total_medicamentos' => $totalMedicamentos,
                'equipos_medicos_aplicados' => count($equiposMedicosAplicados),
                'total_equipos_medicos' => $totalEquiposMedicos,
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Error al guardar evolución de internación: ' . $e->getMessage(), [
                'hospitalizacion_id' => $hospitalizacion->id,
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al guardar la evolución: ' . $e->getMessage(),
            ], 500);
        }
    }
}
