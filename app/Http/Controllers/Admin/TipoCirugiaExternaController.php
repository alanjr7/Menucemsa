<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TipoCirugiaExterna;
use App\Support\Money;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Precios y duración del catálogo de cirugías externas (alquiler de quirófano).
 * Edición inline por el admin; separado del tarifario clínico interno.
 */
class TipoCirugiaExternaController extends Controller
{
    public function index(): View
    {
        $tipos = TipoCirugiaExterna::orderByDesc('precio')->get();

        return view('admin.tipos-cirugia-externa.index', compact('tipos'));
    }

    public function update(Request $request, TipoCirugiaExterna $tipo): RedirectResponse|JsonResponse
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:120',
            'precio' => Money::rules(),
            'duracion_minutos' => 'required|integer|min:1|max:1440',
            'activo' => 'nullable|boolean',
        ]);

        $tipo->update([
            'nombre' => $data['nombre'],
            'precio' => Money::format($data['precio']),
            'duracion_minutos' => $data['duracion_minutos'],
            'activo' => $request->boolean('activo'),
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['ok' => true, 'precio' => $tipo->precio, 'duracion_minutos' => $tipo->duracion_minutos]);
        }

        return redirect()
            ->route('admin.tipos-cirugia-externa.index')
            ->with('success', "«{$tipo->nombre}» actualizado correctamente.");
    }
}
