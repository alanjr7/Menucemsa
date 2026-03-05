<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tarifa;
use Illuminate\Http\Request;

class TarifarioController extends Controller
{
    public function index()
    {
        // Obtener estadísticas
        $stats = [
            'total' => Tarifa::where('activo', true)->count(),
            'servicios' => Tarifa::porCategoria('SERVICIO')->count(),
            'procedimientos' => Tarifa::porCategoria('PROCEDIMIENTO')->count(),
            'cirugias' => Tarifa::porCategoria('CIRUGIA')->count(),
        ];

        // Obtener datos para cada categoría
        $servicios = Tarifa::porCategoria('SERVICIO')->orderBy('codigo')->get();
        $procedimientos = Tarifa::porCategoria('PROCEDIMIENTO')->orderBy('codigo')->get();
        $cirugias = Tarifa::porCategoria('CIRUGIA')->orderBy('codigo')->get();

        return view('admin.tarifarios', compact('stats', 'servicios', 'procedimientos', 'cirugias'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'codigo' => 'required|string|max:20|unique:tarifas',
            'descripcion' => 'required|string|max:200',
            'categoria' => 'required|in:SERVICIO,PROCEDIMIENTO,CIRUGIA',
            'precio_particular' => 'required|numeric|min:0',
            'precio_sis' => 'nullable|numeric|min:0',
            'precio_eps' => 'nullable|numeric|min:0',
            'tipo_convenio_sis' => 'nullable|in:CONVENIO,TARIFARIO',
            'tipo_convenio_eps' => 'nullable|in:CONVENIO,TARIFARIO',
        ]);

        Tarifa::create($validated);

        return redirect()->route('admin.tarifarios')->with('success', 'Tarifa creada exitosamente.');
    }

    public function update(Request $request, Tarifa $tarifa)
    {
        $validated = $request->validate([
            'codigo' => 'required|string|max:20|unique:tarifas,codigo,' . $tarifa->id,
            'descripcion' => 'required|string|max:200',
            'categoria' => 'required|in:SERVICIO,PROCEDIMIENTO,CIRUGIA',
            'precio_particular' => 'required|numeric|min:0',
            'precio_sis' => 'nullable|numeric|min:0',
            'precio_eps' => 'nullable|numeric|min:0',
            'tipo_convenio_sis' => 'nullable|in:CONVENIO,TARIFARIO',
            'tipo_convenio_eps' => 'nullable|in:CONVENIO,TARIFARIO',
        ]);

        $tarifa->update($validated);

        return redirect()->route('admin.tarifarios')->with('success', 'Tarifa actualizada exitosamente.');
    }

    public function destroy(Tarifa $tarifa)
    {
        $tarifa->update(['activo' => false]);
        return redirect()->route('admin.tarifarios')->with('success', 'Tarifa eliminada exitosamente.');
    }

    // API endpoints
    public function apiIndex(Request $request)
    {
        $query = Tarifa::where('activo', true);

        if ($request->has('categoria')) {
            $query->porCategoria($request->categoria);
        }

        if ($request->has('buscar')) {
            $query->buscar($request->buscar);
        }

        return response()->json($query->orderBy('codigo')->get());
    }

    public function apiShow(Tarifa $tarifa)
    {
        return response()->json($tarifa);
    }
}
