@extends('layouts.app')
@section('title', 'Reporte Bajo Stock')
@section('content')
<div class="p-6">
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-800">Stocks por Debajo del Mínimo</h1>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.almacen-medicamentos.reporte.bajo-stock.exportar') }}"
               class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm hover:bg-green-700 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Exportar Excel
            </a>
            <a href="{{ route('admin.almacen-medicamentos.index') }}"
               class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm hover:bg-gray-200">Volver</a>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Medicamento/Insumo</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Lote</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ubicación</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stock Actual</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mínimo</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($stocks as $stock)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium text-gray-900">{{ $stock->lote->catalogo->nombre ?? 'N/A' }}</td>
                    <td class="px-4 py-3 text-gray-600 font-mono text-xs">{{ $stock->lote->codigo_lote ?? '-' }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $stock->ubicacion_label }}</td>
                    <td class="px-4 py-3 font-bold {{ $stock->cantidad_actual <= 0 ? 'text-red-600' : 'text-yellow-600' }}">
                        {{ $stock->cantidad_actual }}
                    </td>
                    <td class="px-4 py-3 text-gray-600">{{ $stock->stock_minimo }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 text-xs rounded-full {{ $stock->cantidad_actual <= 0 ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800' }}">
                            {{ $stock->cantidad_actual <= 0 ? 'Agotado' : 'Bajo Stock' }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-4 py-8 text-center text-gray-500">No hay stocks por debajo del mínimo</td></tr>
                @endforelse
            </tbody>
        </table>

        @if($stocks->hasPages())
        <div class="px-4 py-3 border-t border-gray-100">
            {{ $stocks->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
