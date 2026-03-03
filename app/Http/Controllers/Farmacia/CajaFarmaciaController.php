<?php

namespace App\Http\Controllers\Farmacia;

use App\Http\Controllers\Controller;
use App\Models\CajaFarmacia;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CajaFarmaciaController extends Controller
{
    public function index(): JsonResponse
    {
        $cajas = CajaFarmacia::with('caja')->get();
        return response()->json($cajas);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'CODIGO' => 'required|string|max:15|unique:CAJA_FARMACIA,CODIGO',
            'DETALLE' => 'nullable|string|max:80',
            'TOTAL' => 'nullable|numeric|min:0',
            'ID_CAJA' => 'nullable|string|max:15|exists:CAJA,ID'
        ]);

        $caja = CajaFarmacia::create($validated);
        $caja->load('caja');
        return response()->json($caja, 201);
    }

    public function show(string $codigo): JsonResponse
    {
        $caja = CajaFarmacia::with('caja')->findOrFail($codigo);
        return response()->json($caja);
    }

    public function update(Request $request, string $codigo): JsonResponse
    {
        $caja = CajaFarmacia::findOrFail($codigo);
        
        $validated = $request->validate([
            'DETALLE' => 'nullable|string|max:80',
            'TOTAL' => 'nullable|numeric|min:0',
            'ID_CAJA' => 'nullable|string|max:15|exists:CAJA,ID'
        ]);

        $caja->update($validated);
        $caja->load('caja');
        return response()->json($caja);
    }

    public function destroy(string $codigo): JsonResponse
    {
        $caja = CajaFarmacia::findOrFail($codigo);
        $caja->delete();
        return response()->json(null, 204);
    }
}
