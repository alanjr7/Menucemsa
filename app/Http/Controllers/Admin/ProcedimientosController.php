<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Procedimiento;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProcedimientosController extends Controller
{
    public function index(): View
    {
        $procedimientos = Procedimiento::orderBy('area')->orderBy('nombre')->paginate(20);
        return view('admin.procedimientos.index', compact('procedimientos'));
    }

    public function create(): View
    {
        return view('admin.procedimientos.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nombre'      => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string'],
            'area'        => ['required', 'in:emergencia,uti,internacion,cirugia,hospitalizacion,neonato'],
            'precio'      => ['required', 'numeric', 'min:0'],
            'activo'      => ['boolean'],
        ]);

        $data['activo'] = $request->boolean('activo', true);

        Procedimiento::create($data);

        return redirect()->route('admin.procedimientos.index')
            ->with('success', 'Procedimiento creado correctamente.');
    }

    public function edit(Procedimiento $procedimiento): View
    {
        return view('admin.procedimientos.edit', compact('procedimiento'));
    }

    public function update(Request $request, Procedimiento $procedimiento): RedirectResponse
    {
        $data = $request->validate([
            'nombre'      => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string'],
            'area'        => ['required', 'in:emergencia,uti,internacion,cirugia,hospitalizacion,neonato'],
            'precio'      => ['required', 'numeric', 'min:0'],
        ]);

        $data['activo'] = $request->boolean('activo', false);

        $procedimiento->update($data);

        return redirect()->route('admin.procedimientos.index')
            ->with('success', 'Procedimiento actualizado correctamente.');
    }

    public function destroy(Procedimiento $procedimiento): RedirectResponse
    {
        $procedimiento->delete();

        return redirect()->route('admin.procedimientos.index')
            ->with('success', 'Procedimiento eliminado correctamente.');
    }
}
