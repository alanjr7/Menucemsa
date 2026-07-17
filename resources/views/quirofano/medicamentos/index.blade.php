@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl sm:text-3xl font-bold">Inventario - Quirófano</h1>
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

    <form method="GET" class="flex flex-col sm:flex-row gap-2 sm:gap-3 mb-4">
        <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Buscar..." class="border rounded px-3 py-2 text-sm flex-1">
        <select name="tipo" class="border rounded px-3 py-2 text-sm w-full sm:w-auto">
            <option value="">Todos los tipos</option>
            <option value="medicamento" @selected(request('tipo') === 'medicamento')>Medicamento</option>
            <option value="insumo" @selected(request('tipo') === 'insumo')>Insumo</option>
        </select>
        <div class="flex gap-2">
            <button type="submit" class="flex-1 sm:flex-none bg-gray-700 text-white px-4 py-2 rounded text-sm">Filtrar</button>
            @if(request('buscar') || request('tipo'))
                <a href="{{ route('quirofano.medicamentos.index') }}" class="flex-1 sm:flex-none text-center bg-gray-200 text-gray-700 px-4 py-2 rounded text-sm">Limpiar</a>
            @endif
        </div>
    </form>

    {{-- Tabla (sm+) --}}
    <div class="hidden sm:block overflow-x-auto bg-white rounded shadow">
        <table class="w-full">
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
                                <td class="px-4 py-3 whitespace-nowrap">Bs. {{ $lote->precio_venta }}</td>
                                <td class="px-4 py-3 text-center whitespace-nowrap">{{ $lote->fecha_vencimiento ? $lote->fecha_vencimiento->format('d/m') : 'S/F' }}</td>
                                <td class="px-4 py-3 text-center">
                                    <a href="{{ route('quirofano.medicamentos.show', $med) }}" class="text-blue-500">Ver</a>
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

    {{-- Tarjetas (móvil) --}}
    <div class="sm:hidden space-y-3">
        @forelse ($medicamentos as $med)
            @foreach ($med->lotes as $lote)
                @php $stock = $lote->stocks->first(); @endphp
                @if ($stock)
                    <a href="{{ route('quirofano.medicamentos.show', $med) }}"
                       class="block bg-white rounded-lg shadow-sm border border-gray-100 p-4 active:bg-gray-50">
                        <div class="flex justify-between items-start gap-3">
                            <div class="min-w-0">
                                <p class="font-semibold text-gray-800">{{ $med->nombre }}</p>
                                <span class="inline-block mt-1 px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 capitalize">{{ $med->tipo }}</span>
                            </div>
                            <span class="text-blue-500 text-sm font-medium shrink-0">Ver →</span>
                        </div>
                        <div class="grid grid-cols-3 gap-2 mt-3 text-sm">
                            <div>
                                <p class="text-[10px] text-gray-400 uppercase font-semibold">Stock</p>
                                <p class="font-bold text-gray-800">{{ $stock->cantidad_actual }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] text-gray-400 uppercase font-semibold">Precio</p>
                                <p class="font-medium text-gray-700">Bs. {{ $lote->precio_venta }}</p>
                            </div>
                            <div>
                                <p class="text-[10px] text-gray-400 uppercase font-semibold">Vence</p>
                                <p class="font-medium text-gray-700">{{ $lote->fecha_vencimiento ? $lote->fecha_vencimiento->format('d/m') : 'S/F' }}</p>
                            </div>
                        </div>
                    </a>
                @endif
            @endforeach
        @empty
            <div class="bg-white rounded-lg shadow-sm p-8 text-center text-gray-400">Sin medicamentos</div>
        @endforelse
    </div>

    <div class="mt-4">{{ $medicamentos->withQueryString()->links() }}</div>
</div>
@endsection
