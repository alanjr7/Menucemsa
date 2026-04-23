@extends('layouts.app')
@section('content')
<div class="p-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Medicamentos por Área</h1>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Área</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Medicamentos</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stock Total</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Bajo Stock</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Agotados</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($porArea as $area)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium text-gray-900">{{ $area['nombre'] }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $area['total'] }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $area['stock_total'] }}</td>
                    <td class="px-4 py-3 font-bold text-yellow-600">{{ $area['bajo_stock'] }}</td>
                    <td class="px-4 py-3 font-bold text-red-600">{{ $area['agotados'] }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-4 py-8 text-center text-gray-500">No hay datos disponibles</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
