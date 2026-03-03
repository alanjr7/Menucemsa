<?php

namespace App\Http\Controllers\Farmacia;

use App\Http\Controllers\Controller;
use App\Models\DetalleInsumos;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DetalleInsumosController extends Controller
{
    public function index(): JsonResponse
    {
        $detalles = DetalleInsumos::with(['farmacia', 'insumo'])->get();
        return response()->json($detalles);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ID_FARMACIA' => 'required|string|max:15|exists:FARMACIA,ID',
            'CODIGO_INSUMOS' => 'required|string|max:15|exists:INSUMOS,CODIGO',
            'LABORATORIO' => 'nullable|string|max:80',
            'FECHA_VENCIMIENTO' => 'required|date',
            'DESCRIPCION' => 'nullable|string|max:80'
        ]);

        $detalle = DetalleInsumos::create($validated);
        $detalle->load(['farmacia', 'insumo']);
        return response()->json($detalle, 201);
    }

    public function show(string $id_farmacia, string $codigo_insumos): JsonResponse
    {
        $detalle = DetalleInsumos::with(['farmacia', 'insumo'])
            ->where('ID_FARMACIA', $id_farmacia)
            ->where('CODIGO_INSUMOS', $codigo_insumos)
            ->firstOrFail();
        return response()->json($detalle);
    }

    public function update(Request $request, string $id_farmacia, string $codigo_insumos): JsonResponse
    {
        $detalle = DetalleInsumos::where('ID_FARMACIA', $id_farmacia)
            ->where('CODIGO_INSUMOS', $codigo_insumos)
            ->firstOrFail();
        
        $validated = $request->validate([
            'LABORATORIO' => 'nullable|string|max:80',
            'FECHA_VENCIMIENTO' => 'required|date',
            'DESCRIPCION' => 'nullable|string|max:80'
        ]);

        $detalle->update($validated);
        $detalle->load(['farmacia', 'insumo']);
        return response()->json($detalle);
    }

    public function destroy(string $id_farmacia, string $codigo_insumos): JsonResponse
    {
        $detalle = DetalleInsumos::where('ID_FARMACIA', $id_farmacia)
            ->where('CODIGO_INSUMOS', $codigo_insumos)
            ->firstOrFail();
        $detalle->delete();
        return response()->json(null, 204);
    }
}
