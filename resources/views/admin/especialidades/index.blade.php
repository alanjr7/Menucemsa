@extends('layouts.app')

@section('content')
    <div class="p-6 bg-gray-50 min-h-screen">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Especialidades</h1>
                <p class="text-sm text-gray-500">Gestión de especialidades médicas</p>
            </div>
            <a href="{{ route('admin.especialidades.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg">Nueva especialidad</a>
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
                        <th class="px-4 py-3 text-left">Código</th>
                        <th class="px-4 py-3 text-left">Nombre</th>
                        <th class="px-4 py-3 text-left">Descripción</th>
                        <th class="px-4 py-3 text-left">Estado</th>
                        <th class="px-4 py-3 text-center">Médicos</th>
                        <th class="px-4 py-3 text-center">Consultas</th>
                        <th class="px-4 py-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($especialidades as $especialidad)
                        <tr class="border-t">
                            <td class="px-4 py-3">{{ $especialidad->codigo }}</td>
                            <td class="px-4 py-3">{{ $especialidad->nombre }}</td>
                            <td class="px-4 py-3">{{ $especialidad->descripcion ?: '-' }}</td>
                            <td class="px-4 py-3 capitalize">{{ $especialidad->estado ?? 'activo' }}</td>
                            <td class="px-4 py-3 text-center">{{ $especialidad->medicos_count }}</td>
                            <td class="px-4 py-3 text-center">{{ $especialidad->consultas_count }}</td>
                            <td class="px-4 py-3">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.especialidades.edit', $especialidad) }}" class="px-3 py-1 border rounded">Editar</a>
                                    <form action="{{ route('admin.especialidades.destroy', $especialidad) }}" method="POST" onsubmit="return confirm('¿Eliminar especialidad?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-3 py-1 bg-red-600 text-white rounded">Eliminar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-6 text-center text-gray-500">No hay especialidades registradas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $especialidades->links() }}
        </div>
    </div>
@endsection
