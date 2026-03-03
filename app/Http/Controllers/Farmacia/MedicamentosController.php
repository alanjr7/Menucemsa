<?php

namespace App\Http\Controllers\Farmacia;

use App\Http\Controllers\Controller;
use App\Models\Medicamentos;
use App\Models\Inventario;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class MedicamentosController extends Controller
{
    public function index(): JsonResponse
    {
        $medicamentos = Medicamentos::all();
        return response()->json($medicamentos);
    }

    public function store(Request $request): JsonResponse
    {
        try {
            \Log::info('Medicamentos store request data:', $request->all());
            
            $validated = $request->validate([
                'CODIGO' => 'required|string|max:15|unique:MEDICAMENTOS,CODIGO',
                'DESCRIPCION' => 'required|string|max:80',
                'PRECIO' => 'required|numeric|min:0'
            ]);
            
            \Log::info('Medicamentos validated data:', $validated);

            $medicamento = Medicamentos::create($validated);
            \Log::info('Medicamento created:', $medicamento->toArray());

            // Automatically create inventory record
            try {
                Inventario::create([
                    'ID' => $validated['CODIGO'],
                    'ID_FARMACIA' => 'F1', // Default pharmacy, can be made dynamic
                    'TIPO_ITEM' => 'Medicamento',
                    'STOCK_MINIMO' => '10',
                    'STOCK_DISPONIBLE' => '0',
                    'REPOSICION' => 'No especificado',
                    'FECHA_INGRESO' => now()->format('Y-m-d')
                ]);
                \Log::info('Inventario record created for medicamento');
            } catch (\Exception $e) {
                \Log::error('Error creating inventario record: ' . $e->getMessage());
                // Continue even if inventory creation fails
            }

            return response()->json($medicamento, 201);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error in medicamentos store:', $e->errors());
            return response()->json([
                'error' => 'Validation failed',
                'messages' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            \Log::error('Error in medicamentos store: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to create medicamento',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function show(string $codigo): JsonResponse
    {
        $medicamento = Medicamentos::findOrFail($codigo);
        return response()->json($medicamento);
    }

    public function update(Request $request, string $codigo): JsonResponse
    {
        $medicamento = Medicamentos::findOrFail($codigo);
        
        $validated = $request->validate([
            'DESCRIPCION' => 'required|string|max:80',
            'PRECIO' => 'required|numeric|min:0'
        ]);

        $medicamento->update($validated);
        return response()->json($medicamento);
    }

    public function destroy(string $codigo): JsonResponse
    {
        try {
            $medicamento = Medicamentos::findOrFail($codigo);
            
            // Eliminar registros relacionados primero
            $medicamento->detalleMedicamentos()->delete();
            $medicamento->detalleRecetas()->delete();
            
            // Eliminar registro de inventario si existe
            try {
                Inventario::where('ID', $codigo)->delete();
            } catch (\Exception $e) {
                \Log::warning('No se pudo eliminar inventario: ' . $e->getMessage());
            }
            
            // Eliminar el medicamento
            $medicamento->delete();
            
            return response()->json(null, 204);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Medicamento no encontrado',
                'message' => "No se encontró ningún medicamento con el código {$codigo}"
            ], 404);
            
        } catch (\Exception $e) {
            \Log::error('Error eliminando medicamento: ' . $e->getMessage());
            return response()->json([
                'error' => 'Error eliminando medicamento',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
