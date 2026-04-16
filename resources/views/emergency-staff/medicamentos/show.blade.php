@extends('layouts.app')

@section('title', 'Detalle Medicamento - Emergencia')

@section('content')
<div class="min-h-screen bg-gray-50 p-6">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Detalle del Medicamento/Insumo</h1>
                <p class="text-gray-600 mt-1">Información completa del item</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('emergency-staff.medicamentos.edit', $medicamento) }}" 
                   class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Editar
                </a>
                <a href="{{ route('emergency-staff.medicamentos.index') }}" 
                   class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Volver
                </a>
            </div>
        </div>
    </div>

    <!-- Información Principal -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Columna Principal -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Datos Generales -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Información General</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Nombre</label>
                        <p class="text-lg font-semibold text-gray-900">{{ $medicamento->nombre }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Tipo</label>
                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full {{ $medicamento->tipo == 'medicamento' ? 'bg-green-100 text-green-800' : 'bg-purple-100 text-purple-800' }}">
                            {{ ucfirst($medicamento->tipo) }}
                        </span>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-500">Descripción</label>
                        <p class="text-gray-900">{{ $medicamento->descripcion ?? 'Sin descripción' }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500">Precio</label>
                        <p class="text-lg text-gray-900">
                            @if($medicamento->precio)
                                ${{ number_format($medicamento->precio, 2) }}
                            @else
                                <span class="text-gray-400">No especificado</span>
                            @endif
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500">Área</label>
                        <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                            Emergencia
                        </span>
                    </div>
                </div>
            </div>

            <!-- Stock Information -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Información de Stock</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Cantidad Actual</label>
                        <p class="text-2xl font-bold {{ $medicamento->cantidad == 0 ? 'text-red-600' : ($medicamento->estaBajoStock() ? 'text-yellow-600' : 'text-green-600') }}">
                            {{ $medicamento->cantidad }}
                        </p>
                        <p class="text-sm text-gray-500">{{ $medicamento->unidad_medida }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Stock Mínimo</label>
                        <p class="text-lg text-gray-900">{{ $medicamento->stock_minimo }} {{ $medicamento->unidad_medida }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500">Estado</label>
                        @if($medicamento->cantidad == 0)
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                Agotado
                            </span>
                        @elseif($medicamento->estaBajoStock())
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                Bajo Stock
                            </span>
                        @else
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Stock Normal
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Vencimiento -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Información de Vencimiento</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Fecha de Vencimiento</label>
                        <p class="text-lg {{ $medicamento->estaVencido() ? 'text-red-600 font-semibold' : ($medicamento->estaPorVencer() ? 'text-yellow-600' : 'text-gray-900') }}">
                            @if($medicamento->fecha_vencimiento)
                                {{ $medicamento->fecha_vencimiento->format('d/m/Y') }}
                            @else
                                <span class="text-gray-400">No especificada</span>
                            @endif
                        </p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Número de Lote</label>
                        <p class="text-lg text-gray-900">{{ $medicamento->lote ?? 'No especificado' }}</p>
                    </div>

                    @if($medicamento->fecha_vencimiento)
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-500">Estado de Vencimiento</label>
                            @if($medicamento->estaVencido())
                                <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                    VENCIDO
                                </span>
                            @elseif($medicamento->estaPorVencer())
                                <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    Por Vencer ({{ $medicamento->dias_para_vencer }} días restantes)
                                </span>
                            @else
                                <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Vigente
                                </span>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <!-- Observaciones -->
            @if($medicamento->observaciones)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Observaciones</h2>
                    <p class="text-gray-700 whitespace-pre-line">{{ $medicamento->observaciones }}</p>
                </div>
            @endif
        </div>

        <!-- Columna Lateral -->
        <div class="space-y-6">
            <!-- Estado Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Resumen</h3>
                
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">ID:</span>
                        <span class="text-sm font-mono text-gray-900">#{{ $medicamento->id }}</span>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">Estado:</span>
                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $medicamento->activo ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $medicamento->activo ? 'Activo' : 'Inactivo' }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">Creado:</span>
                        <span class="text-sm text-gray-900">{{ $medicamento->created_at->format('d/m/Y H:i') }}</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">Actualizado:</span>
                        <span class="text-sm text-gray-900">{{ $medicamento->updated_at->format('d/m/Y H:i') }}</span>
                    </div>
                </div>
            </div>

            <!-- Acciones Rápidas -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Acciones Rápidas</h3>
                
                <div class="space-y-3">
                    <a href="{{ route('emergency-staff.medicamentos.edit', $medicamento) }}" 
                       class="w-full flex items-center justify-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Editar
                    </a>
                    
                    <form action="{{ route('emergency-staff.medicamentos.destroy', $medicamento) }}" method="POST" 
                          onsubmit="return confirm('¿Estás seguro de eliminar este medicamento/insumo?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="w-full flex items-center justify-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Eliminar
                        </button>
                    </form>
                </div>
            </div>

            <!-- Alertas -->
            @if($medicamento->estaVencido() || $medicamento->estaBajoStock() || $medicamento->cantidad == 0)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-red-600 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        Alertas
                    </h3>
                    
                    <div class="space-y-2">
                        @if($medicamento->estaVencido())
                            <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                                <p class="text-sm text-red-700">
                                    <strong>Producto Vencido</strong><br>
                                    La fecha de vencimiento ha pasado.
                                </p>
                            </div>
                        @endif

                        @if($medicamento->cantidad == 0)
                            <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                                <p class="text-sm text-red-700">
                                    <strong>Stock Agotado</strong><br>
                                    No hay unidades disponibles.
                                </p>
                            </div>
                        @elseif($medicamento->estaBajoStock())
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                                <p class="text-sm text-yellow-700">
                                    <strong>Bajo Stock</strong><br>
                                    La cantidad actual está por debajo del mínimo.
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
