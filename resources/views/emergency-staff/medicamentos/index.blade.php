@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Inventario - Emergencia</h1>
        </div>

    @if (session('success'))
        <div class="bg-green-100 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
    @endif

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-blue-50 p-4 rounded"><div class="text-sm">Total</div><div class="text-2xl font-bold">{{ $stats['total'] }}</div></div>
        <div class="bg-yellow-50 p-4 rounded"><div class="text-sm">Bajo Stock</div><div class="text-2xl font-bold">{{ $stats['bajo_stock'] }}</div></div>
        <div class="bg-red-50 p-4 rounded"><div class="text-sm">Agotados</div><div class="text-2xl font-bold">{{ $stats['agotados'] }}</div></div>
        <div class="bg-orange-50 p-4 rounded"><div class="text-sm">Vencidos</div><div class="text-2xl font-bold">{{ $stats['vencidos'] }}</div></div>
    </div>

    <table class="w-full bg-white rounded shadow">
        <thead class="bg-gray-200">
            <tr>
                <th class="px-4 py-3 text-left">Nombre</th>
                <th class="px-4 py-3 text-left">Tipo</th>
                <th class="px-4 py-3">Stock</th>
                <th class="px-4 py-3">Precio</th>
                <th class="px-4 py-3">Vencimiento</th>
                <th class="px-4 py-3">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($medicamentos as $med)
                @foreach ($med->lotes as $lote)
                    @php $stock = $lote->stocks->first(); @endphp
                    @if ($stock)
                        <tr class="border-b">
                            <td class="px-4 py-3">{{ $med->nombre }}</td>
                            <td class="px-4 py-3">{{ $med->tipo }}</td>
                            <td class="px-4 py-3 text-center">{{ $stock->cantidad_actual }}</td>
                            <td class="px-4 py-3">Bs. {{ $lote->precio_venta }}</td>
                            <td class="px-4 py-3">{{ $lote->fecha_vencimiento ? $lote->fecha_vencimiento->format('d/m') : 'S/F' }}</td>
                            <td class="px-4 py-3">
                             <a href="{{ route('emergency-staff.medicamentos.show', $med) }}" class="text-blue-500">Ver</a>
                            </td>
                        </tr>
                    @endif
                @endforeach
            @empty
                <tr><td colspan="6" class="px-4 py-3 text-center">Sin medicamentos</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
