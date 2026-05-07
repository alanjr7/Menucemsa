<?php

namespace App\Http\Controllers\Farmacia;

use App\Http\Controllers\Controller;
use App\Models\Medicamentos;
use App\Models\InventarioFarmacia;
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
            $validated = $request->validate([
                'CODIGO' => 'required|string|max:15|unique:MEDICAMENTOS,CODIGO',
                'DESCRIPCION' => 'required|string|max:80',
                'PRECIO' => 'required|numeric|min:0'
            ]);

            $medicamento = Medicamentos::create($validated);

            return response()->json($medicamento, 201);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Validation failed',
                'messages' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            \Log::error('Error in medicamentos store');
            return response()->json([
                'error' => 'Failed to create medicamento',
                'message' => 'Ocurrió un error. Por favor contacte al administrador.'
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
            
            $medicamento->detalleRecetas()->delete();

            InventarioFarmacia::where('codigo_item', $codigo)->delete();

            // Eliminar el medicamento
            $medicamento->delete();

            return response()->json(null, 204);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'error' => 'Medicamento no encontrado',
                'message' => "No se encontró ningún medicamento con el código {$codigo}"
            ], 404);

        } catch (\Exception $e) {
            \Log::error('Error eliminando medicamento');
            return response()->json([
                'error' => 'Error eliminando medicamento',
                'message' => 'Ocurrió un error. Por favor contacte al administrador.'
            ], 500);
        }
    }
}
