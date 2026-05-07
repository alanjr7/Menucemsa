<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Camilla;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CamillaController extends Controller
{
    public function index(): View
    {
        $camillas = Camilla::withCount('usos')
            ->orderBy('area')
            ->orderBy('nombre')
            ->paginate(20);

        return view('admin.camillas.index', compact('camillas'));
    }

    public function create(): View
    {
        return view('admin.camillas.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nombre'          => ['required', 'string', 'max:100'],
            'codigo'          => ['required', 'string', 'max:30', 'unique:camillas,codigo'],
            'precio_por_hora' => ['required', 'numeric', 'min:0'],
            'area'            => ['required', 'in:uti,emergencia'],
        ]);

        $data['codigo'] = strtoupper($data['codigo']);
        $data['activa'] = $request->boolean('activa', true);

        Camilla::create($data);

        return redirect()->route('admin.camillas.index')
            ->with('success', 'Camilla creada correctamente.');
    }

    public function edit(Camilla $camilla): View
    {
        return view('admin.camillas.edit', compact('camilla'));
    }

    public function update(Request $request, Camilla $camilla): RedirectResponse
    {
        $data = $request->validate([
            'nombre'          => ['required', 'string', 'max:100'],
            'codigo'          => ['required', 'string', 'max:30', 'unique:camillas,codigo,' . $camilla->id],
            'precio_por_hora' => ['required', 'numeric', 'min:0'],
            'area'            => ['required', 'in:uti,emergencia'],
        ]);

        $data['codigo'] = strtoupper($data['codigo']);
        $data['activa'] = $request->boolean('activa', true);

        $camilla->update($data);

        return redirect()->route('admin.camillas.index')
            ->with('success', 'Camilla actualizada correctamente.');
    }

    public function destroy(Camilla $camilla): RedirectResponse
    {
        if ($camilla->usos()->exists()) {
            return redirect()->route('admin.camillas.index')
                ->with('error', 'No se puede eliminar: la camilla tiene usos registrados.');
        }

        $camilla->delete();

        return redirect()->route('admin.camillas.index')
            ->with('success', 'Camilla eliminada correctamente.');
    }
}
