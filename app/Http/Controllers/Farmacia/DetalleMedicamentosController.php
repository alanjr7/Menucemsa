<?php

namespace App\Http\Controllers\Farmacia;

use App\Http\Controllers\Controller;
use App\Models\DetalleMedicamentos;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DetalleMedicamentosController extends Controller
{
    public function index(): JsonResponse
    {
        $detalles = DetalleMedicamentos::with(['farmacia', 'medicamento'])->get();
        return response()->json($detalles);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ID_FARMACIA' => 'required|string|max:15|exists:FARMACIA,ID',
            'CODIGO_MEDICAMENTOS' => 'required|string|max:15|exists:MEDICAMENTOS,CODIGO',
            'LABORATORIO' => 'nullable|string|max:80',
            'FECHA_VENCIMIENTO' => 'required|date',
            'TIPO' => 'nullable|string|max:80',
            'REQUERIMIENTO' => 'nullable|string|max:80'
        ]);

        $detalle = DetalleMedicamentos::create($validated);
        $detalle->load(['farmacia', 'medicamento']);
        return response()->json($detalle, 201);
    }

    public function show(string $id_farmacia, string $codigo_medicamentos): JsonResponse
    {
        $detalle = DetalleMedicamentos::with(['farmacia', 'medicamento'])
            ->where('ID_FARMACIA', $id_farmacia)
            ->where('CODIGO_MEDICAMENTOS', $codigo_medicamentos)
            ->firstOrFail();
        return response()->json($detalle);
    }

    public function update(Request $request, string $id_farmacia, string $codigo_medicamentos): JsonResponse
    {
        $detalle = DetalleMedicamentos::where('ID_FARMACIA', $id_farmacia)
            ->where('CODIGO_MEDICAMENTOS', $codigo_medicamentos)
            ->firstOrFail();
        
        $validated = $request->validate([
            'LABORATORIO' => 'nullable|string|max:80',
            'FECHA_VENCIMIENTO' => 'required|date',
            'TIPO' => 'nullable|string|max:80',
            'REQUERIMIENTO' => 'nullable|string|max:80'
        ]);

        $detalle->update($validated);
        $detalle->load(['farmacia', 'medicamento']);
        return response()->json($detalle);
    }

    public function destroy(string $id_farmacia, string $codigo_medicamentos): JsonResponse
    {
        $detalle = DetalleMedicamentos::where('ID_FARMACIA', $id_farmacia)
            ->where('CODIGO_MEDICAMENTOS', $codigo_medicamentos)
            ->firstOrFail();
        $detalle->delete();
        return response()->json(null, 204);
    }
}
