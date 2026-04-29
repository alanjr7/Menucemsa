<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\IngresoPrecio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IngresoPrecioController extends Controller
{
    public function index()
    {
        $precios = IngresoPrecio::with('user')
            ->orderBy('tipo_ingreso')
            ->get()
            ->keyBy('tipo_ingreso');

        $tipos = IngresoPrecio::TIPOS_INGRESO;

        foreach ($tipos as $tipo => $label) {
            if (!$precios->has($tipo)) {
                $precios->put($tipo, new IngresoPrecio([
                    'tipo_ingreso' => $tipo,
                    'precio' => 0,
                    'activo' => true,
                ]));
            }
        }

        return view('admin.ingreso-precios.index', compact('precios', 'tipos'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'precios' => 'required|array',
            'precios.*' => 'nullable|numeric|min:0',
        ]);

        foreach ($validated['precios'] as $tipo => $precio) {
            IngresoPrecio::updateOrCreate(
                ['tipo_ingreso' => $tipo],
                [
                    'precio' => $precio ?? 0,
                    'activo' => true,
                    'user_id' => Auth::id(),
                ]
            );
        }

        return redirect()
            ->route('admin.ingreso-precios.index')
            ->with('success', 'Precios actualizados correctamente');
    }
}
