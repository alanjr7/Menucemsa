@extends('layouts.app')

@section('content')
    <div class="p-6 bg-gray-50 min-h-screen">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Doctores</h1>
                <p class="text-sm text-gray-500">Gestión de médicos y sus especialidades</p>
            </div>
            <a href="{{ route('admin.doctors.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg">Nuevo Doctor</a>
        </div>

        @if(session('success'))
            <div class="mb-4 rounded-lg bg-green-100 px-4 py-3 text-green-800">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="mb-4 rounded-lg bg-red-100 px-4 py-3 text-red-800">{{ session('error') }}</div>
        @endif

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left">Nombre</th>
                        <th class="px-4 py-3 text-left">Email</th>
                        <th class="px-4 py-3 text-left">CI</th>
                        <th class="px-4 py-3 text-left">Teléfono</th>
                        <th class="px-4 py-3 text-left">Especialidad</th>
                        <th class="px-4 py-3 text-left">Estado</th>
                        <th class="px-4 py-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($doctors as $doctor)
                        <tr class="border-t">
                            <td class="px-4 py-3">{{ $doctor->name }}</td>
                            <td class="px-4 py-3">{{ $doctor->email }}</td>
                            <td class="px-4 py-3">{{ $doctor->medico->ci ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $doctor->medico->telefono ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $doctor->medico->especialidad->nombre ?? '-' }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 text-xs rounded-full {{ $doctor->medico->estado === 'Activo' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $doctor->medico->estado ?? 'Desconocido' }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.doctors.edit', $doctor) }}" class="px-3 py-1 border rounded">Editar</a>
                                    <form action="{{ route('admin.doctors.destroy', $doctor) }}" method="POST" onsubmit="return confirm('¿Eliminar doctor?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-3 py-1 bg-red-600 text-white rounded">Eliminar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-6 text-center text-gray-500">No hay doctores registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $doctors->links() }}
        </div>
    </div>
@endsection
