@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Inventario - UTI</h1>
    </div>

    @if (session('success'))
        <div class="bg-green-100 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
    @endif

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-blue-50 p-4 rounded"><div class="text-sm text-gray-500">Total</div><div class="text-2xl font-bold">{{ $stats['total'] }}</div></div>
        <div class="bg-yellow-50 p-4 rounded"><div class="text-sm text-gray-500">Bajo Stock</div><div class="text-2xl font-bold">{{ $stats['bajo_stock'] }}</div></div>
        <div class="bg-red-50 p-4 rounded"><div class="text-sm text-gray-500">Agotados</div><div class="text-2xl font-bold">{{ $stats['agotados'] }}</div></div>
        <div class="bg-orange-50 p-4 rounded"><div class="text-sm text-gray-500">Vencidos</div><div class="text-2xl font-bold">{{ $stats['vencidos'] }}</div></div>
    </div>

    <form method="GET" class="flex gap-3 mb-4">
        <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Buscar..." class="border rounded px-3 py-2 text-sm flex-1">
        <select name="tipo" class="border rounded px-3 py-2 text-sm">
            <option value="">Todos los tipos</option>
            <option value="medicamento" @selected(request('tipo') === 'medicamento')>Medicamento</option>
            <option value="insumo" @selected(request('tipo') === 'insumo')>Insumo</option>
        </select>
        <button type="submit" class="bg-gray-700 text-white px-4 py-2 rounded text-sm">Filtrar</button>
    </form>

    <div class="bg-white rounded shadow overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-100 text-sm text-gray-600">
                <tr>
                    <th class="px-4 py-3 text-left">Nombre</th>
                    <th class="px-4 py-3 text-left">Tipo</th>
                    <th class="px-4 py-3 text-center">Stock</th>
                    <th class="px-4 py-3 text-right">Precio</th>
                    <th class="px-4 py-3 text-center">Vencimiento</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($medicamentos as $med)
                    @foreach ($med->lotes as $lote)
                        @php $stock = $lote->stocks->first(); @endphp
                        @if ($stock)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 font-medium text-gray-900">{{ $med->nombre }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $med->tipo }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="font-medium {{ $stock->cantidad_actual <= 0 ? 'text-red-600' : ($stock->cantidad_actual <= ($stock->stock_minimo ?? 5) ? 'text-yellow-600' : 'text-green-700') }}">
                                        {{ $stock->cantidad_actual }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right text-sm">Bs. {{ number_format($lote->precio_venta, 2) }}</td>
                                <td class="px-4 py-3 text-center text-sm {{ $lote->fecha_vencimiento && $lote->fecha_vencimiento->isPast() ? 'text-red-600 font-medium' : 'text-gray-600' }}">
                                    {{ $lote->fecha_vencimiento ? $lote->fecha_vencimiento->format('d/m/Y') : 'S/F' }}
                                </td>
                            </tr>
                        @endif
                    @endforeach
                @empty
                    <tr><td colspan="5" class="px-4 py-6 text-center text-gray-400">Sin medicamentos registrados para UTI</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $medicamentos->withQueryString()->links() }}</div>
</div>
@endsection
