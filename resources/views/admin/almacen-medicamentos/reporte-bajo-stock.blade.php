@extends('layouts.app')
@section('content')
<div class="p-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Medicamentos con Bajo Stock</h1>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Área</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stock Actual</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stock Mínimo</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($medicamentos as $med)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium text-gray-900">{{ $med->nombre }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $med->area_label }}</td>
                    <td class="px-4 py-3 font-bold text-red-600">{{ $med->cantidad }}</td>
                    <td class="px-4 py-3 text-gray-600">{{ $med->stock_minimo }}</td>
                    <td class="px-4 py-3">
                        <span class="px-2 py-1 text-xs rounded-full {{ $med->cantidad == 0 ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800' }}">
                            {{ $med->cantidad == 0 ? 'Agotado' : 'Bajo Stock' }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-4 py-8 text-center text-gray-500">No hay medicamentos con bajo stock</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
