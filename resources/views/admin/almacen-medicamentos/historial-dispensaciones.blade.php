@extends('layouts.app')

@section('title', 'Historial de Dispensaciones')

@section('content')
<div class="min-h-screen bg-gray-50 p-6">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Historial de Dispensaciones</h1>
            <p class="text-gray-600 mt-1">Transferencias del almacén central a áreas clínicas</p>
        </div>
        <a href="{{ route('admin.almacen-medicamentos.index') }}"
           class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver al Almacén
        </a>
    </div>

    <!-- Estadísticas -->
    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <p class="text-sm text-gray-600">Total Dispensaciones</p>
            <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <p class="text-sm text-gray-600">Últimos 30 días</p>
            <p class="text-2xl font-bold text-green-600">{{ $stats['ultimos_30_dias'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <p class="text-sm text-gray-600">Áreas Activas</p>
            <p class="text-2xl font-bold text-purple-600">{{ $stats['areas_activas'] }}</p>
        </div>
    </div>

    <!-- Filtros -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <form method="GET" action="{{ route('admin.almacen-medicamentos.historial') }}" class="flex flex-wrap gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Área Destino</label>
                <select name="ubicacion_destino" class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
                    <option value="">Todas las áreas</option>
                    @foreach($areas as $key => $label)
                        <option value="{{ $key }}" {{ request('ubicacion_destino') == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Desde</label>
                <input type="date" name="fecha_desde" value="{{ request('fecha_desde') }}"
                       class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Hasta</label>
                <input type="date" name="fecha_hasta" value="{{ request('fecha_hasta') }}"
                       class="px-3 py-2 border border-gray-300 rounded-lg text-sm">
            </div>
            <div class="flex-1 min-w-48">
                <label class="block text-sm font-medium text-gray-700 mb-1">Buscar ítem</label>
                <input type="text" name="buscar" value="{{ request('buscar') }}"
                       placeholder="Nombre de medicamento o insumo"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">Filtrar</button>
                <a href="{{ route('admin.almacen-medicamentos.historial') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm">Limpiar</a>
            </div>
        </form>
    </div>

    <!-- Tabla -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Registro de Dispensaciones</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ítems dispensados</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Área destino</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dispensado por</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Recibido por</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Detalle</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($dispensaciones as $dispensacion)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-500 font-mono">#{{ $dispensacion->id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $dispensacion->fecha_dispensacion->format('d/m/Y') }}</div>
                            <div class="text-xs text-gray-500">{{ $dispensacion->fecha_dispensacion->format('H:i') }}</div>
                        </td>
                        <td class="px-6 py-4">
                            @foreach($dispensacion->detalles as $detalle)
                                <div class="text-sm text-gray-900">
                                    {{ $detalle->lote->catalogo->nombre ?? 'N/A' }}
                                    <span class="text-gray-500">× {{ $detalle->cantidad }}</span>
                                </div>
                            @endforeach
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">
                                {{ $dispensacion->ubicacion_destino_label }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $dispensacion->dispensadoPor->name ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            {{ $dispensacion->recibido_por ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <a href="{{ route('admin.almacen-medicamentos.detalle-dispensacion', $dispensacion->id) }}"
                               class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-lg">
                                Ver detalle
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">No se encontraron dispensaciones.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-gray-200">
            @if($dispensaciones->hasPages())
            <div class="text-sm text-gray-700 mb-2">
                Mostrando {{ $dispensaciones->firstItem() }} a {{ $dispensaciones->lastItem() }} de {{ $dispensaciones->total() }} resultados
            </div>
            @endif
            {{ $dispensaciones->links() }}
        </div>
    </div>
</div>
@endsection
