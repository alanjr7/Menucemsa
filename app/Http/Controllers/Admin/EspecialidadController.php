<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Especialidad;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EspecialidadController extends Controller
{
    public function index(): View
    {
        $especialidades = Especialidad::withCount(['medicos', 'consultas'])
            ->orderBy('nombre')
            ->paginate(15);

        return view('admin.especialidades.index', compact('especialidades'));
    }

    public function create(): View
    {
        return view('admin.especialidades.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'codigo' => ['nullable', 'string', 'max:15', 'unique:especialidades,codigo'],
            'nombre' => ['required', 'string', 'max:80', 'unique:especialidades,nombre'],
            'descripcion' => ['nullable', 'string', 'max:255'],
            'estado' => ['required', 'in:activo,inactivo'],
        ]);

        if (! isset($data['codigo']) || blank($data['codigo'])) {
            unset($data['codigo']);
        } else {
            $data['codigo'] = strtoupper($data['codigo']);
        }

        Especialidad::create($data);

        return redirect()
            ->route('admin.especialidades.index')
            ->with('success', 'Especialidad creada correctamente.');
    }

    public function edit(Especialidad $especialidad): View
    {
        return view('admin.especialidades.edit', compact('especialidad'));
    }

    public function update(Request $request, Especialidad $especialidad): RedirectResponse
    {
        $data = $request->validate([
            'nombre' => ['required', 'string', 'max:80', 'unique:especialidades,nombre,' . $especialidad->codigo . ',codigo'],
            'descripcion' => ['nullable', 'string', 'max:255'],
            'estado' => ['required', 'in:activo,inactivo'],
        ]);

        $especialidad->update($data);

        return redirect()
            ->route('admin.especialidades.index')
            ->with('success', 'Especialidad actualizada correctamente.');
    }

    public function destroy(Especialidad $especialidad): RedirectResponse
    {
        if ($especialidad->medicos()->exists() || $especialidad->consultas()->exists()) {
            return redirect()
                ->route('admin.especialidades.index')
                ->with('error', 'No se puede eliminar la especialidad porque tiene médicos o consultas asociadas.');
        }

        $especialidad->delete();

        return redirect()
            ->route('admin.especialidades.index')
            ->with('success', 'Especialidad eliminada correctamente.');
    }
}
