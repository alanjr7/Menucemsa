<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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
        $roles = ['admin', 'reception', 'dirmedico', 'emergencia', 'caja', 'gerente'];
        return view('user-management.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Password::defaults()],
            'role' => 'required|in:admin,reception,dirmedico,emergencia,caja,gerente',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return redirect()->route('user-management.index')
            ->with('success', 'Usuario creado exitosamente.');
    }

    public function edit(User $user)
    {
        $roles = ['admin', 'reception', 'dirmedico', 'emergencia', 'caja', 'gerente'];
        return view('user-management.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,reception,dirmedico,emergencia,caja,gerente',
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

        // Aquí podrías agregar un campo 'active' en la tabla users
        // Por ahora, simulamos el cambio de estado
        $user->update([
            'email_verified_at' => $user->email_verified_at ? null : now(),
        ]);

        return response()->json([
            'success' => true,
            'status' => $user->email_verified_at ? 'active' : 'inactive'
        ]);
    }
}
