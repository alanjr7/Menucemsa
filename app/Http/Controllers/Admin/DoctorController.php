<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Medico;
use App\Models\Especialidad;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class DoctorController extends Controller
{
    public function index(): View
    {
        $doctors = User::where('role', 'doctor')
            ->with('medico.especialidad')
            ->paginate(15);

        return view('admin.doctors.index', compact('doctors'));
    }

    public function create(): View
    {
        $especialidades = Especialidad::where('estado', 'activo')
            ->orderBy('nombre')
            ->get();

        return view('admin.doctors.create', compact('especialidades'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'ci' => ['required', 'string', 'unique:medicos,ci'],
            'telefono' => ['nullable', 'string'],
            'codigo_especialidad' => ['required', 'exists:especialidades,codigo'],
        ]);

        try {
            \DB::beginTransaction();

            // Crear usuario con rol doctor
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => \Hash::make($data['password']),
                'role' => 'doctor',
                'is_active' => true,
            ]);

            // Crear registro médico
            Medico::create([
                'id_usuario' => $user->id,
                'ci' => $data['ci'],
                'telefono' => $data['telefono'] ?? null,
                'estado' => 'Activo',
                'codigo_especialidad' => $data['codigo_especialidad'],
            ]);

            \DB::commit();

            return redirect()
                ->route('admin.doctors.index')
                ->with('success', 'Doctor creado correctamente.');

        } catch (\Exception $e) {
            \DB::rollBack();
            
            return redirect()
                ->back()
                ->with('error', 'Error al crear el doctor: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function edit(User $doctor): View
    {
        $doctor->load('medico.especialidad');
        $especialidades = Especialidad::where('estado', 'activo')
            ->orderBy('nombre')
            ->get();

        return view('admin.doctors.edit', compact('doctor', 'especialidades'));
    }

    public function update(Request $request, User $doctor): RedirectResponse
    {
        if ($doctor->role !== 'doctor') {
            return redirect()
                ->route('admin.doctors.index')
                ->with('error', 'El usuario seleccionado no es un doctor.');
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $doctor->id],
            'telefono' => ['nullable', 'string'],
            'codigo_especialidad' => ['required', 'exists:especialidades,codigo'],
            'estado' => ['required', 'in:Activo,Inactivo'],
        ]);

        try {
            \DB::beginTransaction();

            // Actualizar usuario
            $doctor->update([
                'name' => $data['name'],
                'email' => $data['email'],
            ]);

            // Actualizar registro médico
            if ($doctor->medico) {
                $doctor->medico->update([
                    'telefono' => $data['telefono'],
                    'estado' => $data['estado'],
                    'codigo_especialidad' => $data['codigo_especialidad'],
                ]);
            }

            \DB::commit();

            return redirect()
                ->route('admin.doctors.index')
                ->with('success', 'Doctor actualizado correctamente.');

        } catch (\Exception $e) {
            \DB::rollBack();
            
            return redirect()
                ->back()
                ->with('error', 'Error al actualizar el doctor: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(User $doctor): RedirectResponse
    {
        if ($doctor->role !== 'doctor') {
            return redirect()
                ->route('admin.doctors.index')
                ->with('error', 'El usuario seleccionado no es un doctor.');
        }

        try {
            \DB::beginTransaction();

            // Eliminar registro médico
            if ($doctor->medico) {
                $doctor->medico->delete();
            }

            // Eliminar usuario
            $doctor->delete();

            \DB::commit();

            return redirect()
                ->route('admin.doctors.index')
                ->with('success', 'Doctor eliminado correctamente.');

        } catch (\Exception $e) {
            \DB::rollBack();
            
            return redirect()
                ->route('admin.doctors.index')
                ->with('error', 'Error al eliminar el doctor: ' . $e->getMessage());
        }
    }

    /**
     * API para obtener médicos por especialidad
     */
    public function getMedicosByEspecialidad(Request $request)
    {
        try {
            $request->validate([
                'especialidad' => ['required', 'exists:especialidades,codigo']
            ]);

            $especialidad = $request->especialidad;
            
            $medicosQuery = Medico::with(['usuario', 'especialidad'])
                ->where('codigo_especialidad', $especialidad)
                ->where('estado', 'Activo');
            
            $medicos = $medicosQuery->get();
            
            $medicosFormateados = $medicos->map(function ($medico) {
                return [
                    'ci' => $medico->ci,
                    'nombre' => $medico->usuario ? $medico->usuario->name : 'Sin usuario',
                    'especialidad' => $medico->especialidad ? $medico->especialidad->nombre : 'Sin especialidad',
                ];
            });

            return response()->json([
                'success' => true,
                'medicos' => $medicosFormateados
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cargar médicos: ' . $e->getMessage()
            ], 500);
        }
    }
}
