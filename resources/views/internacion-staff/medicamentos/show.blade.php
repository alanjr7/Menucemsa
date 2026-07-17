@extends('layouts.app')
@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-3xl font-bold mb-6">{{ $medicamento->nombre }}</h1>
    <div class="bg-white p-6 rounded shadow mb-6">
        <p><strong>Tipo:</strong> {{ $medicamento->tipo }}</p>
        <p><strong>Unidad:</strong> {{ $medicamento->unidad_medida }}</p>
        @if($medicamento->descripcion)<p><strong>Descripción:</strong> {{ $medicamento->descripcion }}</p>@endif
    </div>
    <h2 class="text-2xl font-bold mb-4">Lotes</h2>
    <table class="w-full bg-white rounded shadow">
        <thead class="bg-gray-200">
            <tr><th class="px-4 py-3">Código</th><th class="px-4 py-3">Stock</th><th class="px-4 py-3">Precio</th><th class="px-4 py-3">Vencimiento</th></tr>
        </thead>
        <tbody>
            @forelse ($medicamento->lotes as $lote)
                @php $stock = $lote->stocks->first(); @endphp
                @if ($stock)
                    <tr class="border-b"><td class="px-4 py-3">{{ $lote->codigo_lote }}</td><td class="px-4 py-3">{{ $stock->cantidad_actual }}</td><td class="px-4 py-3">{{ $lote->precio_venta }}</td><td class="px-4 py-3">{{ $lote->fecha_vencimiento ? $lote->fecha_vencimiento->format('d/m/Y') : 'S/F' }}</td></tr>
                @endif
            @empty
                <tr><td colspan="4" class="px-4 py-3 text-center">Sin lotes</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="mt-6"><a href="{{ route('internacion-staff.medicamentos.index') }}" class="bg-gray-500 text-white px-6 py-2 rounded">Volver</a></div>
</div>
@endsection
