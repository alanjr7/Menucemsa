@extends('layouts.app')

@section('title', 'Historial: ' . $catalogo->nombre)

@section('content')
<div class="min-h-screen bg-gray-50 p-6">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Historial de Dispensaciones</h1>
            <p class="text-gray-600 mt-1">Ítem: <strong>{{ $catalogo->nombre }}</strong></p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.almacen-medicamentos.show', $catalogo) }}"
               class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm">Ver detalle</a>
            <a href="{{ route('admin.almacen-medicamentos.index') }}"
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">Volver</a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Lote</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Destino</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cantidad</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dispensado por</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Detalle</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($dispensaciones as $dispensacion)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm font-mono text-gray-500">#{{ $dispensacion->id }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $dispensacion->fecha_dispensacion->format('d/m/Y H:i') }}</td>
                        <td class="px-6 py-4 text-sm font-mono text-gray-700">
                            @foreach($dispensacion->detalles as $d)
                                @if($d->lote->catalogo_id == $catalogo->id)
                                    <div>{{ $d->lote->codigo_lote ?? '-' }}</div>
                                @endif
                            @endforeach
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">
                                {{ $dispensacion->ubicacion_destino_label }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm font-semibold text-gray-900">
                            @foreach($dispensacion->detalles as $d)
                                @if($d->lote->catalogo_id == $catalogo->id)
                                    {{ $d->cantidad }}
                                @endif
                            @endforeach
                            {{ $catalogo->unidad_medida }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $dispensacion->dispensadoPor->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.almacen-medicamentos.detalle-dispensacion', $dispensacion->id) }}"
                               class="text-blue-600 hover:text-blue-800 text-xs">Ver</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">No hay dispensaciones registradas para este ítem.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200">{{ $dispensaciones->links() }}</div>
    </div>
</div>
@endsection
