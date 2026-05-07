@extends('layouts.app')

@section('content')
    <div class="p-6 bg-gray-50 min-h-screen">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Procedimientos</h1>
                <p class="text-sm text-gray-500">Gestión de procedimientos clínicos por área</p>
            </div>
            <a href="{{ route('admin.procedimientos.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg">Nuevo procedimiento</a>
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
                        <th class="px-4 py-3 text-left">Área</th>
                        <th class="px-4 py-3 text-left">Descripción</th>
                        <th class="px-4 py-3 text-right">Precio</th>
                        <th class="px-4 py-3 text-center">Estado</th>
                        <th class="px-4 py-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($procedimientos as $proc)
                        <tr class="border-t">
                            <td class="px-4 py-3">{{ $proc->nombre }}</td>
                            <td class="px-4 py-3 capitalize">{{ $proc->area }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $proc->descripcion ?: '-' }}</td>
                            <td class="px-4 py-3 text-right">{{ number_format($proc->precio, 2) }}</td>
                            <td class="px-4 py-3 text-center">
                                @if($proc->activo)
                                    <span class="px-2 py-0.5 bg-green-100 text-green-700 rounded-full text-xs">Activo</span>
                                @else
                                    <span class="px-2 py-0.5 bg-gray-100 text-gray-500 rounded-full text-xs">Inactivo</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.procedimientos.edit', $proc) }}" class="px-3 py-1 border rounded text-gray-700">Editar</a>
                                    <form action="{{ route('admin.procedimientos.destroy', $proc) }}" method="POST" onsubmit="return confirm('¿Eliminar procedimiento?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-3 py-1 bg-red-600 text-white rounded">Eliminar</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-gray-500">No hay procedimientos registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $procedimientos->links() }}
        </div>
    </div>
@endsection
