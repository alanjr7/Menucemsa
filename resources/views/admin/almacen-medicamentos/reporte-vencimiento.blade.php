@extends('layouts.app')
@section('title', 'Reporte de Vencimiento')
@section('content')
<div class="p-6 space-y-8">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-800">Reporte de Vencimiento de Lotes</h1>
        <a href="{{ route('admin.almacen-medicamentos.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm hover:bg-gray-200">Volver</a>
    </div>

    @php
    $renderTable = function($lotes, $titulo, $color) { return [$lotes, $titulo, $color]; };
    @endphp

    @foreach([[$vencidos, 'Lotes Vencidos', 'red'], [$porVencer, 'Lotes por Vencer (próximos 30 días)', 'yellow']] as [$lista, $titulo, $color])
    <div>
        <h2 class="text-lg font-semibold text-{{ $color }}-800 mb-3">{{ $titulo }} ({{ count($lista) }})</h2>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Medicamento/Insumo</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Código Lote</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha Vencimiento</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Días</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($lista as $lote)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $lote->catalogo->nombre ?? 'N/A' }}</td>
                        <td class="px-4 py-3 text-gray-600 font-mono text-xs">{{ $lote->codigo_lote ?? '-' }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $lote->fecha_vencimiento->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 font-bold text-{{ $color }}-600">
                            {{ $lote->dias_para_vencer !== null ? abs($lote->dias_para_vencer) . ($lote->dias_para_vencer < 0 ? ' días vencido' : ' días') : '-' }}
                        </td>
                        <td class="px-4 py-3">
                            <a href="{{ route('admin.almacen-medicamentos.show', $lote->catalogo_id) }}"
                               class="text-blue-600 hover:text-blue-800 text-xs">Ver detalle</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-4 py-8 text-center text-gray-500">No hay lotes en esta categoría</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endforeach
</div>
@endsection
