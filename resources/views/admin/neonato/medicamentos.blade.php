@extends('layouts.app')

@section('content')
<div class="p-6 bg-gray-50/50 min-h-screen">

    <div class="flex justify-between items-end mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Medicamentos — Neonatología</h1>
            <p class="text-sm text-gray-500">Stock disponible en el área · Solo lectura</p>
        </div>
        <a href="{{ route('admin.neonato.dashboard') }}"
            class="px-4 py-2 border border-gray-200 rounded-xl text-sm text-gray-600 hover:bg-gray-50">
            ← Volver
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-bold text-gray-800 text-sm">Stock área neonato ({{ $stocks->total() }} ítems)</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Medicamento / Insumo</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Tipo</th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase">Stock</th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase">Precio</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($stocks as $stock)
                        <tr class="hover:bg-gray-50/30">
                            <td class="px-6 py-3 text-sm text-gray-800">{{ $stock->lote?->catalogo?->nombre ?? '—' }}</td>
                            <td class="px-6 py-3 text-sm text-gray-500 capitalize">{{ $stock->lote?->catalogo?->tipo ?? '—' }}</td>
                            <td class="px-6 py-3 text-sm text-right font-mono
                                {{ $stock->cantidad_actual <= 5 ? 'text-red-600 font-bold' : 'text-gray-700' }}">
                                {{ $stock->cantidad_actual }}
                            </td>
                            <td class="px-6 py-3 text-sm text-right text-gray-700">
                                Bs. {{ number_format($stock->lote?->precio_venta ?? 0, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-gray-400">
                                Sin stock registrado para el área neonato
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($stocks->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">{{ $stocks->links() }}</div>
        @endif
    </div>
</div>
@endsection
