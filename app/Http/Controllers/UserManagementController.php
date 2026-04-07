<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Password;

class UserManagementController extends Controller
{
    public function index()
    {
        $users = User::orderBy('name')->get();
        return view('user-management.index', compact('users'));
    }

    public function create()
    {
        $roles = ['admin', 'reception', 'dirmedico', 'emergencia', 'caja', 'gerente', 'doctor', 'farmacia'];
        return view('user-management.create', compact('roles'));
    }

    public function store(Request $request)
    {
        try {
            // Validación básica siempre requerida
            $rules = [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => ['required', 'confirmed', Password::defaults()],
                'role' => 'required|in:admin,reception,dirmedico,emergencia,caja,gerente,doctor,farmacia',
            ];

            // Validaciones adicionales solo para roles que las necesitan
            if (in_array($request->role, ['doctor', 'dirmedico'])) {
                $rules['ci'] = 'required|string';
                $rules['codigo_especialidad'] = 'required|exists:especialidades,codigo';
                $rules['telefono'] = 'nullable|string';
            }

            $validated = $request->validate($rules);

            // Verificar si el CI ya existe (solo para roles médicos)
            if (in_array($request->role, ['doctor', 'dirmedico']) && isset($validated['ci'])) {
                $existingMedico = \App\Models\Medico::where('ci', $validated['ci'])->first();
                if ($existingMedico) {
                    return redirect()->back()
                        ->with('error', 'El CI ' . $validated['ci'] . ' ya está registrado en el sistema.')
                        ->withInput();
                }
            }

            DB::beginTransaction();

            // Crear usuario
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => $validated['role'],
                'is_active' => true,
                'email_verified_at' => now(), // Auto-verificar
            ]);

            // Crear registro médico solo para roles médicos
            if (in_array($request->role, ['doctor', 'dirmedico'])) {
                $medico = \App\Models\Medico::create([
                    'user_id' => $user->id,
                    'ci' => $validated['ci'],
                    'telefono' => $validated['telefono'] ?? null,
                    'estado' => 'Activo',
                    'codigo_especialidad' => $validated['codigo_especialidad'],
                ]);

                DB::commit();

                return redirect()->route('user-management.index')
                    ->with('success', 'Usuario doctor creado exitosamente.');
            } else {
                // Para roles no médicos (admin, reception, emergencia, caja, gerente)
                DB::commit();

                $roleNames = [
                    'admin' => 'administrador',
                    'reception' => 'recepción',
                    'emergencia' => 'emergencias',
                    'caja' => 'caja',
                    'gerente' => 'gerente'
                ];

                $roleName = $roleNames[$validated['role']] ?? $validated['role'];

                return redirect()->route('user-management.index')
                    ->with('success', 'Usuario de ' . $roleName . ' creado exitosamente.');
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Mostrar errores de validación específicos
            $errorMessages = [];
            foreach ($e->errors() as $field => $messages) {
                $errorMessages[] = $field . ': ' . implode(', ', $messages);
            }
            
            return redirect()->back()
                ->with('error', 'Error de validación: ' . implode('; ', $errorMessages))
                ->withInput();
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Error al crear el usuario: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function edit(User $user)
    {
        $roles = ['admin', 'reception', 'dirmedico', 'emergencia', 'caja', 'gerente', 'doctor', 'farmacia'];
        return view('user-management.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,reception,dirmedico,emergencia,caja,gerente,doctor,farmacia',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ]);

        if ($request->filled('password')) {
            $request->validate([
                'password' => ['required', 'confirmed', Password::defaults()],
            ]);
            $user->update([
                'password' => Hash::make($request->password),
            ]);
        }

        return redirect()->route('user-management.index')
            ->with('success', 'Usuario actualizado exitosamente.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('user-management.index')
                ->with('error', 'No puedes eliminar tu propio usuario.');
        }

        $user->delete();

        return redirect()->route('user-management.index')
            ->with('success', 'Usuario eliminado exitosamente.');
    }

    public function toggleStatus(User $user)
    {
        if ($user->id === auth()->id()) {
            return response()->json(['error' => 'No puedes desactivar tu propio usuario.'], 403);
        }

        $user->update([
            'is_active' => !$user->is_active,
        ]);

        return response()->json([
            'success' => true,
            'status' => $user->is_active ? 'active' : 'inactive'
        ]);
    }
}
