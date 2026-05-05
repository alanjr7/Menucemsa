@extends('layouts.app')

@section('title', $catalogo->nombre)

@section('content')
<div class="min-h-screen bg-gray-50 p-6">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ $catalogo->nombre }}</h1>
            <div class="flex items-center gap-3 mt-2">
                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $catalogo->tipo == 'medicamento' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-700' }}">
                    {{ $catalogo->tipo_label }}
                </span>
                <span class="text-sm text-gray-500">{{ $catalogo->unidad_medida }}</span>
                @if(!$catalogo->activo)
                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Inactivo</span>
                @endif
            </div>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.almacen-medicamentos.edit', $catalogo) }}"
               class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm">Editar</a>
            <a href="{{ route('admin.almacen-medicamentos.index') }}"
               class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm">Volver</a>
        </div>
    </div>

    @if($catalogo->descripcion)
    <div class="mb-6 bg-white rounded-lg shadow-sm border border-gray-200 p-4 text-sm text-gray-700">
        {{ $catalogo->descripcion }}
    </div>
    @endif

    <!-- Lotes y stock -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-semibold text-gray-900">Lotes y Stock</h3>
        </div>
        @forelse($catalogo->lotes as $lote)
        <div class="p-6 border-b border-gray-100 last:border-0">
            <div class="flex items-start justify-between mb-3">
                <div>
                    <span class="text-sm font-semibold text-gray-900">Lote: {{ $lote->codigo_lote ?? 'Sin código' }}</span>
                    @if($lote->fecha_vencimiento)
                        <span class="ml-3 text-xs px-2 py-0.5 rounded-full
                            {{ $lote->estado_vencimiento == 'vencido' ? 'bg-red-100 text-red-800' :
                               ($lote->estado_vencimiento == 'por_vencer' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                            Vence {{ $lote->fecha_vencimiento->format('d/m/Y') }}
                        </span>
                    @endif
                </div>
                <div class="text-right text-xs text-gray-500">
                    @if($lote->precio_venta)
                        <div>Precio venta: <span class="font-semibold text-gray-900">Bs {{ number_format($lote->precio_venta, 2) }}</span></div>
                    @endif
                    @if($lote->precio_compra)
                        <div>Costo: Bs {{ number_format($lote->precio_compra, 2) }}
                            @if($lote->porcentaje_ganancia) ({{ $lote->porcentaje_ganancia }}%) @endif
                        </div>
                    @endif
                </div>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                @foreach($lote->stocks as $stock)
                <div class="rounded-lg p-3 border
                    {{ $stock->estado_stock == 'agotado' ? 'border-red-200 bg-red-50' :
                       ($stock->estado_stock == 'bajo' ? 'border-yellow-200 bg-yellow-50' : 'border-gray-200 bg-gray-50') }}">
                    <p class="text-xs font-medium text-gray-600">{{ $stock->ubicacion_label }}</p>
                    <p class="text-lg font-bold {{ $stock->estado_stock == 'agotado' ? 'text-red-700' : ($stock->estado_stock == 'bajo' ? 'text-yellow-700' : 'text-gray-900') }}">
                        {{ $stock->cantidad_actual }}
                    </p>
                    <p class="text-xs text-gray-500">mín. {{ $stock->stock_minimo }}</p>
                </div>
                @endforeach
            </div>
        </div>
        @empty
        <div class="p-8 text-center text-gray-500">No hay lotes registrados para este ítem.</div>
        @endforelse
    </div>

    <!-- Historial de Entregas a Pacientes -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">Entregas a Pacientes</h3>
            <span class="text-sm text-gray-600">{{ $entregas->total() }} registros</span>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Paciente</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">CI</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cantidad</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Entregado por</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Área</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hora</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($entregas as $entrega)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $entrega->paciente?->nombre ?? 'N/A' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $entrega->paciente_ci }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                {{ $entrega->cantidad }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            {{ $entrega->entregadoPor?->name ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full
                                {{ match($entrega->origen) {
                                    'emergencia' => 'bg-red-100 text-red-800',
                                    'internacion' => 'bg-blue-100 text-blue-800',
                                    'uti' => 'bg-purple-100 text-purple-800',
                                    'cirugia' => 'bg-green-100 text-green-800',
                                    default => 'bg-gray-100 text-gray-800',
                                } }}">
                                {{ ucfirst($entrega->origen) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $entrega->fecha_entrega?->format('d/m/Y') ?? '—' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $entrega->fecha_entrega?->format('H:i') ?? '—' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            No hay entregas registradas para este medicamento.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-gray-200">
            {{ $entregas->links() }}
        </div>
    </div>
</div>
@endsection
