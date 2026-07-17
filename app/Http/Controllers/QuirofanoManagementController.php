<?php

namespace App\Http\Controllers;

use App\Models\Quirofano;
use App\Models\TipoCirugia;
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

    // =========================================================================
    // Tipos de cirugía: configuración del precio y duración POR DEFECTO.
    // Estos valores sólo precargan el formulario de programar cirugía; cada cita
    // guarda su propio costo_base (snapshot), por lo que editar aquí NO afecta
    // cirugías ya programadas o cobradas. No se permite crear/borrar tipos
    // (el nombre es la clave del enum tipo_cirugia en citas_quirurgicas).
    // =========================================================================

    public function tiposIndex(): View
    {
        $tipos = TipoCirugia::orderBy('costo_base')->get();
        return view('quirofano.management.tipos.index', compact('tipos'));
    }

    public function tiposEdit(TipoCirugia $tipo): View
    {
        return view('quirofano.management.tipos.edit', compact('tipo'));
    }

    public function tiposUpdate(Request $request, TipoCirugia $tipo): JsonResponse
    {
        $validated = $request->validate([
            'duracion_minutos' => 'required|integer|min:1',
            'costo_base'       => 'required|numeric|decimal:0,2|min:0',
            'descripcion'      => 'nullable|string|max:255',
            'activo'           => 'boolean',
        ]);

        $tipo->update($validated);

        return response()->json([
            'success' => true,
            'message' => "Tipo de cirugía «{$tipo->nombre}» actualizado exitosamente",
            'tipo'    => $tipo->fresh(),
        ]);
    }
}
