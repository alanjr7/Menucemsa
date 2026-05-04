@extends('layouts.app')

@section('title', 'Historial del Item - ' . $almacenMedicamento->nombre)

@section('content')
<div class="min-h-screen bg-gray-50 p-6">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Historial de Dispensaciones</h1>
                <p class="text-gray-600 mt-1">
                    Item: <strong>{{ $almacenMedicamento->nombre }}</strong>
                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800 ml-2">
                        {{ $almacenMedicamento->area_label }}
                    </span>
                </p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.almacen-medicamentos.show', $almacenMedicamento) }}"
                   class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    Ver Detalle
                </a>
                <a href="{{ route('admin.almacen-medicamentos.index') }}"
                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Volver al Almacén
                </a>
            </div>
        </div>
    </div>

    <!-- Info Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div>
                <p class="text-sm font-medium text-gray-600">Stock Actual</p>
                <p class="text-2xl font-bold {{ $almacenMedicamento->cantidad == 0 ? 'text-red-600' : ($almacenMedicamento->estaBajoStock() ? 'text-yellow-600' : 'text-green-600') }}">
                    {{ $almacenMedicamento->cantidad }} {{ $almacenMedicamento->unidad_medida }}
                </p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-600">Stock Mínimo</p>
                <p class="text-xl font-bold text-gray-900">{{ $almacenMedicamento->stock_minimo }} {{ $almacenMedicamento->unidad_medida }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-600">Total Dispensado</p>
                <p class="text-xl font-bold text-blue-600">{{ $almacenMedicamento->dispensaciones->sum('cantidad') }} {{ $almacenMedicamento->unidad_medida }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-600">N° Dispensaciones</p>
                <p class="text-xl font-bold text-purple-600">{{ $almacenMedicamento->dispensaciones->count() }}</p>
            </div>
        </div>
    </div>

    <!-- Tabla de Dispensaciones -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Registro de Dispensaciones</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cantidad</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Área Destino</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dispensado Por</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Recibido Por</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Observaciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($dispensaciones as $dispensacion)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $dispensacion->fecha_dispensacion->format('d/m/Y') }}</div>
                                <div class="text-xs text-gray-500">{{ $dispensacion->fecha_dispensacion->format('H:i') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ $dispensacion->cantidad }} {{ $almacenMedicamento->unidad_medida }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                    {{ $dispensacion->area_destino_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $dispensacion->dispensadoPor->name ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $dispensacion->recibido_por ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500 max-w-xs truncate" title="{{ $dispensacion->observaciones }}">
                                    {{ $dispensacion->observaciones ?? '-' }}
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                    <p class="text-gray-500 text-lg font-medium">No hay dispensaciones registradas</p>
                                    <p class="text-gray-400 text-sm mt-1">Este ítem aún no ha sido dispensado</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
            <div class="text-sm text-gray-700">
                Mostrando <span class="font-medium">{{ $dispensaciones->firstItem() }}</span>
                a <span class="font-medium">{{ $dispensaciones->lastItem() }}</span>
                de <span class="font-medium">{{ $dispensaciones->total() }}</span> resultados
            </div>
            {{ $dispensaciones->links() }}
        </div>
    </div>
</div>
@endsection
