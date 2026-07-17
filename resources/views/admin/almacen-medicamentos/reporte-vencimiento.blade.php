@extends('layouts.app')
@section('title', 'Reporte de Vencimiento')
@section('content')
<div class="p-6 space-y-8">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-800">Reporte de Vencimiento de Lotes</h1>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.almacen-medicamentos.reporte.vencimiento.exportar') }}"
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

    {{-- Vencidos --}}
    <div>
        <h2 class="text-lg font-semibold text-red-800 mb-3">Lotes Vencidos ({{ $vencidos->total() }})</h2>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Medicamento/Insumo</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Código Lote</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha Vencimiento</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Días Vencido</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($vencidos as $lote)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $lote->catalogo->nombre ?? 'N/A' }}</td>
                        <td class="px-4 py-3 text-gray-600 font-mono text-xs">{{ $lote->codigo_lote ?? '-' }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $lote->fecha_vencimiento->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 font-bold text-red-600">
                            {{ abs($lote->dias_para_vencer) }} días
                        </td>
                        <td class="px-4 py-3">
                            <a href="{{ route('admin.almacen-medicamentos.show', $lote->catalogo_id) }}"
                               class="text-blue-600 hover:text-blue-800 text-xs">Ver detalle</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-4 py-8 text-center text-gray-500">No hay lotes vencidos</td></tr>
                    @endforelse
                </tbody>
            </table>
            @if($vencidos->hasPages())
            <div class="px-4 py-3 border-t border-gray-100">
                {{ $vencidos->links() }}
            </div>
            @endif
        </div>
    </div>

    {{-- Por vencer --}}
    <div>
        <h2 class="text-lg font-semibold text-yellow-800 mb-3">Lotes por Vencer — próximos 30 días ({{ $porVencer->total() }})</h2>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Medicamento/Insumo</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Código Lote</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha Vencimiento</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Días Restantes</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($porVencer as $lote)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $lote->catalogo->nombre ?? 'N/A' }}</td>
                        <td class="px-4 py-3 text-gray-600 font-mono text-xs">{{ $lote->codigo_lote ?? '-' }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $lote->fecha_vencimiento->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 font-bold text-yellow-600">
                            {{ $lote->dias_para_vencer }} días
                        </td>
                        <td class="px-4 py-3">
                            <a href="{{ route('admin.almacen-medicamentos.show', $lote->catalogo_id) }}"
                               class="text-blue-600 hover:text-blue-800 text-xs">Ver detalle</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-4 py-8 text-center text-gray-500">No hay lotes por vencer en los próximos 30 días</td></tr>
                    @endforelse
                </tbody>
            </table>
            @if($porVencer->hasPages())
            <div class="px-4 py-3 border-t border-gray-100">
                {{ $porVencer->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
