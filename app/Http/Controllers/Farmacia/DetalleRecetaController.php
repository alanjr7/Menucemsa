<?php

namespace App\Http\Controllers\Farmacia;

use App\Http\Controllers\Controller;
use App\Models\DetalleReceta;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DetalleRecetaController extends Controller
{
    public function index(): JsonResponse
    {
        $detalles = DetalleReceta::with(['farmacia', 'medicamento'])->get();
        return response()->json($detalles);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ID_FARMACIA' => 'required|string|max:15|exists:FARMACIA,ID',
            'CODIGO_MEDICAMENTOS' => 'required|string|max:15|exists:MEDICAMENTOS,CODIGO',
            'DOSIS' => 'nullable|string|max:80',
            'SUBTOTAL' => 'nullable|numeric|min:0'
        ]);

        $detalle = DetalleReceta::create($validated);
        $detalle->load(['farmacia', 'medicamento']);
        return response()->json($detalle, 201);
    }

    public function show(string $id_farmacia, string $codigo_medicamentos): JsonResponse
    {
        $detalle = DetalleReceta::with(['farmacia', 'medicamento'])
            ->where('ID_FARMACIA', $id_farmacia)
            ->where('CODIGO_MEDICAMENTOS', $codigo_medicamentos)
            ->firstOrFail();
        return response()->json($detalle);
    }

    public function update(Request $request, string $id_farmacia, string $codigo_medicamentos): JsonResponse
    {
        $detalle = DetalleReceta::where('ID_FARMACIA', $id_farmacia)
            ->where('CODIGO_MEDICAMENTOS', $codigo_medicamentos)
            ->firstOrFail();
        
        $validated = $request->validate([
            'DOSIS' => 'nullable|string|max:80',
            'SUBTOTAL' => 'nullable|numeric|min:0'
        ]);

        $detalle->update($validated);
        $detalle->load(['farmacia', 'medicamento']);
        return response()->json($detalle);
    }

    public function destroy(string $id_farmacia, string $codigo_medicamentos): JsonResponse
    {
        $detalle = DetalleReceta::where('ID_FARMACIA', $id_farmacia)
            ->where('CODIGO_MEDICAMENTOS', $codigo_medicamentos)
            ->firstOrFail();
        $detalle->delete();
        return response()->json(null, 204);
    }
}
