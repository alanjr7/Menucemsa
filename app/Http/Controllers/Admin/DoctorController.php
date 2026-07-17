<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Medico;
use App\Models\Especialidad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
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
                'user_id' => $user->id,
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

        $currentCi = $doctor->medico?->ci;

        $data = $request->validate([
            'name'               => ['required', 'string', 'max:255'],
            'email'              => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $doctor->id],
            'ci'                 => ['required', 'integer', Rule::unique('medicos', 'ci')->ignore($currentCi, 'ci')],
            'telefono'           => ['nullable', 'string'],
            'codigo_especialidad'=> ['required', 'exists:especialidades,codigo'],
            'estado'             => ['required', 'in:Activo,Inactivo'],
        ]);

        try {
            DB::beginTransaction();

            $doctor->update([
                'name'  => $data['name'],
                'email' => $data['email'],
            ]);

            if ($doctor->medico) {
                $newCi = (int) $data['ci'];

                if ($currentCi !== $newCi) {
                    // CI cambia: actualizar en cascada deshabilitando FK checks temporalmente
                    DB::statement('SET FOREIGN_KEY_CHECKS=0');

                    DB::table('medicos')->where('ci', $currentCi)->update(['ci' => $newCi]);

                    DB::table('citas')->where('ci_medico', $currentCi)->update(['ci_medico' => $newCi]);
                    DB::table('consultas')->where('ci_medico', $currentCi)->update(['ci_medico' => $newCi]);
                    DB::table('hospitalizaciones')->where('ci_medico', $currentCi)->update(['ci_medico' => $newCi]);
                    DB::table('citas_quirurgicas')->where('ci_cirujano', $currentCi)->update(['ci_cirujano' => $newCi]);
                    DB::table('citas_quirurgicas')->where('ci_instrumentista', $currentCi)->update(['ci_instrumentista' => $newCi]);
                    DB::table('citas_quirurgicas')->where('ci_anestesiologo', $currentCi)->update(['ci_anestesiologo' => $newCi]);

                    DB::statement('SET FOREIGN_KEY_CHECKS=1');

                    // Refrescar el modelo para que apunte al nuevo ci
                    $doctor->medico->ci = $newCi;
                }

                $doctor->medico->update([
                    'telefono'            => $data['telefono'],
                    'estado'              => $data['estado'],
                    'codigo_especialidad' => $data['codigo_especialidad'],
                ]);
            }

            DB::commit();

            return redirect()
                ->route('admin.doctors.index')
                ->with('success', 'Doctor actualizado correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            DB::statement('SET FOREIGN_KEY_CHECKS=1');

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
