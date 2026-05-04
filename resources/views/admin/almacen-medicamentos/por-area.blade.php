@extends('layouts.app')
@section('title', 'Stocks en ' . ucfirst($area))
@section('content')
<div class="p-6">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Stocks en {{ ucfirst($area) }}</h1>
            <p class="text-gray-500 text-sm mt-1">{{ $stats['total'] }} lotes — {{ $stats['medicamentos'] }} medicamentos, {{ $stats['insumos'] }} insumos</p>
        </div>
        <a href="{{ route('admin.almacen-medicamentos.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm hover:bg-gray-200">Volver</a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Medicamento/Insumo</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Lote</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vencimiento</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stock</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($stocks as $stock)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium text-gray-900">{{ $stock->lote->catalogo->nombre ?? 'N/A' }}</td>
                    <td class="px-4 py-3 text-gray-600 font-mono text-xs">{{ $stock->lote->codigo_lote ?? '-' }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $stock->lote->fecha_vencimiento?->format('d/m/Y') ?? '-' }}</td>
                    <td class="px-4 py-3 font-semibold">{{ $stock->cantidad_actual }} {{ $stock->lote->catalogo->unidad_medida ?? '' }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 text-xs rounded-full
                            {{ $stock->estado_stock === 'agotado' ? 'bg-red-100 text-red-800' :
                               ($stock->estado_stock === 'bajo' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                            {{ ucfirst($stock->estado_stock) }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-4 py-8 text-center text-gray-500">No hay stock en {{ ucfirst($area) }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $stocks->links() }}
</div>
@endsection
