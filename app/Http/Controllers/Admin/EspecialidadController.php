<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Especialidad;
use Illuminate\Support\Facades\Validator;

class EspecialidadController extends Controller
{
    public function index()
    {
        $especialidades = Especialidad::withCount(['medicos', 'consultas'])->orderBy('nombre')->get();
        return view('admin.especialidades', compact('especialidades'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'codigo' => 'required|string|max:15|unique:especialidades,codigo',
            'nombre' => 'required|string|max:80|unique:especialidades,nombre',
            'descripcion' => 'required|string|max:80',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $especialidad = Especialidad::create([
                'codigo' => strtoupper($request->codigo),
                'nombre' => ucwords(strtolower($request->nombre)),
                'descripcion' => ucfirst($request->descripcion),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Especialidad creada exitosamente',
                'especialidad' => $especialidad
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear especialidad: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $codigo)
    {
        $especialidad = Especialidad::findOrFail($codigo);

        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:80|unique:especialidades,nombre,' . $codigo . ',codigo',
            'descripcion' => 'required|string|max:80',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $especialidad->update([
                'nombre' => ucwords(strtolower($request->nombre)),
                'descripcion' => ucfirst($request->descripcion),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Especialidad actualizada exitosamente',
                'especialidad' => $especialidad
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar especialidad: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($codigo)
    {
        try {
            $especialidad = Especialidad::findOrFail($codigo);
            
            // Verificar si hay médicos asociados
            if ($especialidad->medicos()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar la especialidad porque tiene médicos asociados'
                ], 422);
            }

            // Verificar si hay consultas asociadas
            if ($especialidad->consultas()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar la especialidad porque tiene consultas asociadas'
                ], 422);
            }

            $especialidad->delete();

            return response()->json([
                'success' => true,
                'message' => 'Especialidad eliminada exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar especialidad: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($codigo)
    {
        try {
            $especialidad = Especialidad::withCount(['medicos', 'consultas'])->findOrFail($codigo);
            
            return response()->json([
                'success' => true,
                'especialidad' => $especialidad
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener especialidad: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getStats()
    {
        try {
            $stats = [
                'total' => Especialidad::count(),
                'con_medicos' => Especialidad::has('medicos')->count(),
                'sin_medicos' => Especialidad::doesntHave('medicos')->count(),
                'mas_usadas' => Especialidad::withCount('consultas')
                    ->orderBy('consultas_count', 'desc')
                    ->limit(5)
                    ->get()
            ];

            return response()->json([
                'success' => true,
                'stats' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener estadísticas: ' . $e->getMessage()
            ], 500);
        }
    }
}
