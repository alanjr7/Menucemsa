@extends('layouts.app')

@section('content')
<!-- Cabecera -->
<div class="flex flex-col items-start justify-between gap-4 mb-6 sm:flex-row sm:items-center sm:mb-8">
    <div>
        <h2 class="text-2xl font-bold text-slate-800">Gestión de Menús</h2>
        <p class="mt-1 text-sm text-slate-500">
            Administra los enlaces y módulos de la barra de navegación lateral.
        </p>
    </div>
    <a href="{{ route('menus.create') }}" class="inline-flex items-center justify-center px-4 py-2 text-sm font-semibold text-white transition-all bg-blue-600 border border-transparent rounded-lg shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
        <svg class="w-5 h-5 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Nuevo Menú
    </a>
</div>

<!-- Alertas -->
@if(session('success'))
<div class="p-4 mb-6 text-sm text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-xl">
    {{ session('success') }}
</div>
@endif

<!-- Tabla -->
<div class="overflow-hidden bg-white border shadow-sm border-slate-200 rounded-xl">
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left border-collapse">
            <thead class="text-xs uppercase bg-slate-50 text-slate-600 border-b border-slate-200">
                <tr>
                    <th class="px-6 py-4 font-semibold">Orden</th>
                    <th class="px-6 py-4 font-semibold">Nombre del Menú</th>
                    <th class="px-6 py-4 font-semibold">Ruta / Enlace</th>
                    <th class="px-6 py-4 font-semibold">Permisos (Roles)</th>
                    <th class="px-6 py-4 font-semibold text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($menus as $menu)
                <!-- Fila Menú Padre -->
                <tr class="transition-colors hover:bg-slate-50">
                    <td class="px-6 py-4 font-medium text-slate-500">{{ $menu->order }}</td>
                    <td class="px-6 py-4 font-bold text-slate-800 flex items-center gap-3">
                        @if($menu->icon_path)
                        <svg class="w-5 h-5 text-{{ $menu->color }}-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $menu->icon_path }}" />
                        </svg>
                        @endif
                        {{ $menu->name }}
                    </td>
                    <td class="px-6 py-4 text-slate-600 font-mono text-xs">{{ $menu->route ?? '-- Dropdown --' }}</td>
                    <td class="px-6 py-4 text-slate-600">{{ $menu->roles ?? 'Todos' }}</td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('menus.edit', $menu) }}" class="text-blue-600 hover:text-blue-800 font-medium mr-3">Editar</a>
                        <form action="{{ route('menus.destroy', $menu) }}" method="POST" class="inline-block" onsubmit="return confirm('¿Estás seguro de eliminar este menú?');">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800 font-medium">Eliminar</button>
                        </form>
                    </td>
                </tr>

                <!-- Filas Submenús (Hijos) -->
                @foreach($menu->children as $child)
                <tr class="transition-colors bg-slate-50/50 hover:bg-slate-50">
                    <td class="px-6 py-3 text-slate-400 pl-10">{{ $menu->order }}.{{ $child->order }}</td>
                    <td class="px-6 py-3 text-slate-600 pl-12 flex items-center gap-2">
                        <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                        {{ $child->name }}
                    </td>
                    <td class="px-6 py-3 text-slate-600 font-mono text-xs">{{ $child->route }}</td>
                    <td class="px-6 py-3 text-slate-600">{{ $child->roles ?? 'Heredado' }}</td>
                    <td class="px-6 py-3 text-right">
                        <a href="{{ route('menus.edit', $child) }}" class="text-blue-600 hover:text-blue-800 font-medium mr-3">Editar</a>
                        <form action="{{ route('menus.destroy', $child) }}" method="POST" class="inline-block" onsubmit="return confirm('¿Estás seguro de eliminar este submenú?');">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800 font-medium">Eliminar</button>
                        </form>
                    </td>
                </tr>
                @endforeach
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-slate-500">No hay menús registrados.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($menus->hasPages())
    <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
        {{ $menus->links() }}
    </div>
    @endif
</div>
@endsection