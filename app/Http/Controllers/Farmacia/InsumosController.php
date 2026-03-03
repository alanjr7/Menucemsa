<?php

namespace App\Http\Controllers\Farmacia;

use App\Http\Controllers\Controller;
use App\Models\Insumos;
use App\Models\Inventario;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class InsumosController extends Controller
{
    public function index(): JsonResponse
    {
        $insumos = Insumos::all();
        return response()->json($insumos);
    }

    public function store(Request $request): JsonResponse
    {
        try {
            \Log::info('Insumos store request data:', $request->all());
            
            $validated = $request->validate([
                'CODIGO' => 'required|string|max:15|unique:INSUMOS,CODIGO',
                'NOMBRE' => 'required|string|max:80',
                'DESCRIPCION' => 'nullable|string|max:80',
                'PRECIO' => 'nullable|numeric|min:0'
            ]);
            
            \Log::info('Insumos validated data:', $validated);

            $insumo = Insumos::create($validated);
            \Log::info('Insumo created:', $insumo->toArray());

            // Automatically create inventory record
            try {
                Inventario::create([
                    'ID' => $validated['CODIGO'],
                    'ID_FARMACIA' => 'F1', // Default pharmacy, can be made dynamic
                    'TIPO_ITEM' => 'Insumo',
                    'STOCK_MINIMO' => '10',
                    'STOCK_DISPONIBLE' => '0',
                    'REPOSICION' => 'No especificado',
                    'FECHA_INGRESO' => now()->format('Y-m-d')
                ]);
                \Log::info('Inventario record created for insumo');
            } catch (\Exception $e) {
                \Log::error('Error creating inventario record: ' . $e->getMessage());
                // Continue even if inventory creation fails
            }

            return response()->json($insumo, 201);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error in insumos store:', $e->errors());
            return response()->json([
                'error' => 'Validation failed',
                'messages' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            \Log::error('Error in insumos store: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to create insumo',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function show(string $codigo): JsonResponse
    {
        $insumo = Insumos::findOrFail($codigo);
        return response()->json($insumo);
    }

    public function update(Request $request, string $codigo): JsonResponse
    {
        $insumo = Insumos::findOrFail($codigo);
        
        $validated = $request->validate([
            'NOMBRE' => 'required|string|max:80',
            'DESCRIPCION' => 'nullable|string|max:80',
            'PRECIO' => 'nullable|numeric|min:0'
        ]);

        $insumo->update($validated);
        return response()->json($insumo);
    }

    public function destroy(string $codigo): JsonResponse
    {
        \Log::info("Intentando eliminar insumo con código: {$codigo}");
        
        try {
            $insumo = Insumos::findOrFail($codigo);
            \Log::info("Insumo encontrado:", $insumo->toArray());
            
            // Eliminar registros relacionados primero
            $insumo->detalleInsumos()->delete();
            
            // Eliminar registro de inventario si existe
            try {
                Inventario::where('ID', $codigo)->delete();
                \Log::info("Registro de inventario eliminado para insumo {$codigo}");
            } catch (\Exception $e) {
                \Log::warning('No se pudo eliminar inventario: ' . $e->getMessage());
            }
            
            // Eliminar el insumo
            $insumo->delete();
            \Log::info("Insumo eliminado exitosamente");
            
            return response()->json(null, 204);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            \Log::error("Insumo no encontrado con código: {$codigo}");
            return response()->json([
                'error' => 'Insumo no encontrado',
                'message' => "No se encontró ningún insumo con el código {$codigo}",
                'codigo' => $codigo
            ], 404);
            
        } catch (\Exception $e) {
            \Log::error("Error eliminando insumo: " . $e->getMessage());
            return response()->json([
                'error' => 'Error eliminando insumo',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
