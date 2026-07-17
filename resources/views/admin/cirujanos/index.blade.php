@extends('layouts.app')

@section('content')
    <div class="p-6 bg-gray-50 min-h-screen">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Cirujanos</h1>
                <p class="text-sm text-gray-500">Gestión de cirujanos del sistema</p>
            </div>
            <a href="{{ route('admin.cirujanos.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Nuevo Cirujano</a>
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
                        <th class="px-4 py-3 text-left">CI</th>
                        <th class="px-4 py-3 text-left">Email</th>
                        <th class="px-4 py-3 text-left">Estado</th>
                        <th class="px-4 py-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cirujanos as $cirujano)
                        <tr class="border-t">
                            <td class="px-4 py-3">{{ $cirujano->name }}</td>
                            <td class="px-4 py-3">{{ $cirujano->medico->ci ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $cirujano->email }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 text-xs rounded-full {{ $cirujano->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $cirujano->is_active ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.cirujanos.edit', $cirujano) }}" class="px-3 py-1 border rounded hover:bg-gray-50">Editar</a>
                                    <form action="{{ route('admin.cirujanos.destroy', $cirujano) }}" method="POST" onsubmit="return confirm('¿Eliminar cirujano?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700">Eliminar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-gray-500">No hay cirujanos registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $cirujanos->links() }}
        </div>
    </div>
@endsection
