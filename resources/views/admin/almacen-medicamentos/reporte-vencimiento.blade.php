@extends('layouts.app')
@section('content')
<div class="p-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Medicamentos por Vencer</h1>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Área</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Lote</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha Vencimiento</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Días Restantes</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($vencidos as $med)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium text-gray-900">{{ $med->nombre }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $med->area_label }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $med->lote }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $med->fecha_vencimiento->format('d/m/Y') }}</td>
                    <td class="px-4 py-3 font-bold text-gray-900">{{ $med->dias_restantes }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 text-xs rounded-full {{ $med->dias_restantes <= 0 ? 'bg-red-100 text-red-800' : ($med->dias_restantes <= 30 ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                            {{ $med->dias_restantes <= 0 ? 'Vencido' : ($med->dias_restantes <= 30 ? 'Próximo a vencer' : 'OK') }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-4 py-8 text-center text-gray-500">No hay medicamentos próximos a vencer</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
