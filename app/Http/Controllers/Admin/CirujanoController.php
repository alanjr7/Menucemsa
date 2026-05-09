<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Medico;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class CirujanoController extends Controller
{
    public function index()
    {
        $cirujanos = User::where('role', 'cirujano')
            ->with('medico')
            ->orderBy('name')
            ->paginate(15);

        return view('admin.cirujanos.index', compact('cirujanos'));
    }

    public function create()
    {
        return view('admin.cirujanos.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ci' => ['required', 'integer', 'unique:medicos,ci'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'cirujano',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        Medico::create([
            'ci' => $validated['ci'],
            'user_id' => $user->id,
            'estado' => 'activo',
        ]);

        return redirect()->route('admin.cirujanos.index')
            ->with('success', 'Cirujano creado exitosamente.');
    }

    public function edit(User $cirujano)
    {
        if ($cirujano->role !== 'cirujano') {
            return redirect()->route('admin.cirujanos.index')
                ->with('error', 'El usuario seleccionado no es un cirujano.');
        }

        return view('admin.cirujanos.edit', compact('cirujano'));
    }

    public function update(Request $request, User $cirujano)
    {
        if ($cirujano->role !== 'cirujano') {
            return redirect()->route('admin.cirujanos.index')
                ->with('error', 'El usuario seleccionado no es un cirujano.');
        }

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $cirujano->id],
            'is_active' => ['required', 'boolean'],
        ];

        if ($request->filled('password')) {
            $rules['password'] = ['confirmed', Password::defaults()];
        }

        $validated = $request->validate($rules);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'is_active' => $validated['is_active'],
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($validated['password']);
        }

        $cirujano->update($data);

        return redirect()->route('admin.cirujanos.index')
            ->with('success', 'Cirujano actualizado exitosamente.');
    }

    public function destroy(User $cirujano)
    {
        if ($cirujano->role !== 'cirujano') {
            return redirect()->route('admin.cirujanos.index')
                ->with('error', 'El usuario seleccionado no es un cirujano.');
        }

        $cirujano->delete();

        return redirect()->route('admin.cirujanos.index')
            ->with('success', 'Cirujano eliminado exitosamente.');
    }
}
