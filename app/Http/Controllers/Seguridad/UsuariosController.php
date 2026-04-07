<?php

namespace App\Http\Controllers\Seguridad;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Medico;
use App\Models\Especialidad;

class UsuariosController extends Controller
{
    public function index()
    {
        $usuarios = User::orderBy('name')->get();
        
        return view('seguridad.usuarios', compact('usuarios'));
    }

    public function create()
    {
        $especialidades = Especialidad::orderBy('nombre')->get();

        return view('seguridad.usuarios-create', compact('especialidades'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,reception,dirmedico,doctor,emergencia,caja,user,farmacia'
        ]);

        // Normalizar roles: si selecciona 'doctor', convertirlo a 'dirmedico'
        $role = $request->role === 'doctor' ? 'dirmedico' : $request->role;

        if ($role === 'dirmedico') {
            $request->validate([
                'ci' => 'required|integer',
                'telefono' => 'nullable|integer',
                'codigo_especialidad' => 'required|string|exists:especialidades,codigo',
            ]);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $role,
        ]);

        if ($role === 'dirmedico') {
            Medico::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'ci' => $request->ci,
                    'telefono' => $request->telefono,
                    'estado' => 'Activo',
                    'id_asistente' => null,
                    'codigo_especialidad' => $request->codigo_especialidad,
                ]
            );
        }

        return redirect()->route('seguridad.usuarios.index')
            ->with('success', 'Usuario creado exitosamente.');
    }

    public function edit(User $user)
    {
        $especialidades = Especialidad::orderBy('nombre')->get();
        $medico = Medico::where('user_id', $user->id)->first();

        return view('seguridad.usuarios-edit', compact('user', 'especialidades', 'medico'));
    }

    public function update(Request $request, User $user)
    {
        // Prevenir que un usuario edite su propio rol
        if ($user->id === auth()->id()) {
            return redirect()->route('seguridad.usuarios.index')
                ->with('error', 'No puedes modificar tu propio rol. Contacta a otro administrador.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,reception,dirmedico,doctor,emergencia,caja,farmacia'
        ]);

        // Normalizar roles: si selecciona 'doctor', convertirlo a 'dirmedico'
        $role = $request->role === 'doctor' ? 'dirmedico' : $request->role;

        if ($role === 'dirmedico') {
            $request->validate([
                'ci' => 'required|integer',
                'telefono' => 'nullable|integer',
                'codigo_especialidad' => 'required|string|exists:especialidades,codigo',
            ]);
        }

        try {
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'role' => $role,
            ]);

            if ($role === 'dirmedico') {
                Medico::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'ci' => $request->ci,
                        'telefono' => $request->telefono,
                        'estado' => 'Activo',
                        'id_asistente' => null,
                        'codigo_especialidad' => $request->codigo_especialidad,
                    ]
                );
            }

            if ($request->filled('password')) {
                $request->validate([
                    'password' => 'required|string|min:8|confirmed',
                ]);
                $user->update([
                    'password' => bcrypt($request->password),
                ]);
            }

            return redirect()->route('seguridad.usuarios.index')
                ->with('success', 'Usuario actualizado exitosamente.');
                
        } catch (\Illuminate\Database\QueryException $e) {
            // Manejar error de constraint de la base de datos
            if (strpos($e->getMessage(), 'CHECK constraint failed') !== false) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'El rol seleccionado no es válido. Por favor selecciona un rol válido.');
            }
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al actualizar el usuario: ' . $e->getMessage());
        }
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('seguridad.usuarios.index')
                ->with('error', 'No puedes eliminar tu propio usuario.');
        }

        $user->delete();

        return redirect()->route('seguridad.usuarios.index')
            ->with('success', 'Usuario eliminado exitosamente.');
    }
}
