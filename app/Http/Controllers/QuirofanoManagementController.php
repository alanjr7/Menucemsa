<?php

namespace App\Http\Controllers;

use App\Models\Quirofano;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class QuirofanoManagementController extends Controller
{
    public function index(): View
    {
        $quirofanos = Quirofano::orderBy('id')->get();
        return view('quirofano.management.index', compact('quirofanos'));
    }

    public function create(): View
    {
        return view('quirofano.management.create');
    }

    public function getNextNumber(): JsonResponse
    {
        try {
            // Usar el ID más alto como referencia para el siguiente número
            $ultimoNumero = Quirofano::max('id') ?? 0;
            $siguienteNumero = $ultimoNumero + 1;

            return response()->json([
                'nextNumber' => $siguienteNumero
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'No se pudo obtener el siguiente número',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'tipo' => 'required|string|in:General,Especializado,Urgencias,Pediatrico,Cardiologia,Neurologia,Oftalmologia,Ginecologia,Urologia',
            'estado' => 'required|string|in:disponible,ocupado,mantenimiento',
        ]);

        $quirofano = Quirofano::create($request->only(['tipo', 'estado']));

        return response()->json([
            'success' => true,
            'message' => 'Quirófano creado exitosamente',
            'quirofano' => $quirofano
        ]);
    }

    public function show(Quirofano $quirofano): View
    {
        return view('quirofano.management.show', compact('quirofano'));
    }

    public function edit(Quirofano $quirofano): View
    {
        return view('quirofano.management.edit', compact('quirofano'));
    }

    public function update(Request $request, Quirofano $quirofano): JsonResponse
    {
        $request->validate([
            'tipo' => 'required|string|in:General,Especializado,Urgencias,Pediatrico,Cardiologia,Neurologia,Oftalmologia,Ginecologia,Urologia',
            'estado' => 'required|string|in:disponible,ocupado,mantenimiento',
        ]);

        $quirofano->update($request->only(['tipo', 'estado']));

        return response()->json([
            'success' => true,
            'message' => 'Quirófano actualizado exitosamente',
            'quirofano' => $quirofano->fresh()
        ]);
    }

    public function destroy(Quirofano $quirofano): JsonResponse
    {
        // Verificar si hay citas programadas
        $citasCount = $quirofano->citasQuirurgicas()->count();
        
        if ($citasCount > 0) {
            return response()->json([
                'success' => false,
                'message' => "No se puede eliminar el quirófano porque tiene {$citasCount} citas programadas"
            ], 422);
        }

        $quirofano->delete();

        return response()->json([
            'success' => true,
            'message' => 'Quirófano eliminado exitosamente'
        ]);
    }

    public function cambiarEstado(Request $request, Quirofano $quirofano): JsonResponse
    {
        $request->validate([
            'estado' => 'required|string|in:disponible,ocupado,mantenimiento'
        ]);

        $quirofano->estado = $request->estado;
        $quirofano->save();

        return response()->json([
            'success' => true,
            'message' => 'Estado del quirófano actualizado exitosamente',
            'quirofano' => $quirofano->fresh()
        ]);
    }
}
