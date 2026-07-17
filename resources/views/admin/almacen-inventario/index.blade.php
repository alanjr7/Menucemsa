@extends('layouts.app')

@section('title', 'Almacén Inventario')

@section('content')
<div class="min-h-screen bg-gray-50 p-6">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Almacén Inventario</h1>
            <p class="text-gray-500 text-sm mt-1">Registro de activos, equipos e insumos locales</p>
        </div>
        <a href="{{ route('admin.almacen-inventario.create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors shadow-sm text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nuevo Activo
        </a>
    </div>

    @if(session('success'))
    <div class="mb-4 bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded-lg">
        {{ session('success') }}
    </div>
    @endif

    <!-- Filtro búsqueda -->
    <form method="GET" class="mb-5 flex gap-3">
        <input type="text" name="buscar" value="{{ request('buscar') }}"
               placeholder="Buscar por nombre, código o proveedor..."
               class="flex-1 border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" autocomplete='off'>
        <button type="submit"
                class="bg-gray-700 text-white px-4 py-2 rounded-lg text-sm hover:bg-gray-800 transition-colors">
            Buscar
        </button>
        @if(request('buscar'))
        <a href="{{ route('admin.almacen-inventario.index') }}"
           class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-300 transition-colors">
            Limpiar
        </a>
        @endif
    </form>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Código Activo</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Nombre</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Marca</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-600">Precio</th>
                    <th class="px-4 py-3 text-right font-semibold text-gray-600">Cantidad</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Proveedor</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Nro. Factura</th>
                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Nro. Recibo</th>
                    <th class="px-4 py-3 text-center font-semibold text-gray-600">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($items as $item)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-4 py-3 font-mono text-xs text-gray-700">{{ $item->codigo_activo }}</td>
                    <td class="px-4 py-3 font-medium text-gray-900">{{ $item->nombre }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $item->marca ?? '-' }}</td>
                    <td class="px-4 py-3 text-right text-gray-900">Bs. {{ number_format($item->precio, 2) }}</td>
                    <td class="px-4 py-3 text-right">
                        <span class="px-2 py-0.5 rounded text-xs font-semibold
                            {{ $item->cantidad <= 0 ? 'bg-red-100 text-red-700' : ($item->cantidad <= 5 ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700') }}">
                            {{ $item->cantidad }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-gray-600">{{ $item->proveedor ?? '-' }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $item->nro_factura ?? '-' }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $item->numero_recibo ?? '-' }}</td>
                    <td class="px-4 py-3">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('admin.almacen-inventario.edit', $item) }}"
                               class="text-blue-600 hover:text-blue-800 text-xs font-medium">Editar</a>
                            <form method="POST" action="{{ route('admin.almacen-inventario.destroy', $item) }}"
                                  onsubmit="return confirm('¿Eliminar este activo?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 text-xs font-medium">
                                    Eliminar
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="px-4 py-10 text-center text-gray-400">
                        No hay activos registrados.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $items->links() }}
    </div>
</div>
@endsection
