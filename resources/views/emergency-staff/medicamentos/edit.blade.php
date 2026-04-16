@extends('layouts.app')

@section('title', 'Editar Medicamento - Emergencia')

@section('content')
<div class="min-h-screen bg-gray-50 p-6">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Editar Medicamento/Insumo</h1>
                <p class="text-gray-600 mt-1">Modificar {{ $medicamento->nombre }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('emergency-staff.medicamentos.show', $medicamento) }}" 
                   class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    Ver Detalle
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

    <!-- Mensajes de Error -->
    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Formulario -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <form action="{{ route('emergency-staff.medicamentos.update', $medicamento) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Nombre -->
                <div class="lg:col-span-2">
                    <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1">
                        Nombre <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nombre" id="nombre" value="{{ old('nombre', $medicamento->nombre) }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                           placeholder="Nombre del medicamento o insumo">
                </div>

                <!-- Tipo -->
                <div>
                    <label for="tipo" class="block text-sm font-medium text-gray-700 mb-1">
                        Tipo <span class="text-red-500">*</span>
                    </label>
                    <select name="tipo" id="tipo" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                        @foreach($tipos as $value => $label)
                            <option value="{{ $value }}" {{ old('tipo', $medicamento->tipo) == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Descripción -->
                <div class="lg:col-span-3">
                    <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-1">
                        Descripción
                    </label>
                    <textarea name="descripcion" id="descripcion" rows="2"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                              placeholder="Descripción detallada del producto...">{{ old('descripcion', $medicamento->descripcion) }}</textarea>
                </div>

                <!-- Cantidad -->
                <div>
                    <label for="cantidad" class="block text-sm font-medium text-gray-700 mb-1">
                        Cantidad/Stock <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="cantidad" id="cantidad" value="{{ old('cantidad', $medicamento->cantidad) }}" required min="0" step="1"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                           placeholder="0">
                </div>

                <!-- Unidad de Medida -->
                <div>
                    <label for="unidad_medida" class="block text-sm font-medium text-gray-700 mb-1">
                        Unidad de Medida <span class="text-red-500">*</span>
                    </label>
                    <select name="unidad_medida" id="unidad_medida" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                        @foreach($unidades as $value => $label)
                            <option value="{{ $value }}" {{ old('unidad_medida', $medicamento->unidad_medida) == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Stock Mínimo -->
                <div>
                    <label for="stock_minimo" class="block text-sm font-medium text-gray-700 mb-1">
                        Stock Mínimo <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="stock_minimo" id="stock_minimo" value="{{ old('stock_minimo', $medicamento->stock_minimo) }}" required min="0" step="1"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                           placeholder="5">
                    <p class="text-xs text-gray-500 mt-1">Nivel para alertas de bajo stock</p>
                </div>

                <!-- Precio -->
                <div>
                    <label for="precio" class="block text-sm font-medium text-gray-700 mb-1">
                        Precio
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">$</span>
                        <input type="number" name="precio" id="precio" value="{{ old('precio', $medicamento->precio) }}" min="0" step="0.01"
                               class="w-full pl-7 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                               placeholder="0.00">
                    </div>
                </div>

                <!-- Fecha de Vencimiento -->
                <div>
                    <label for="fecha_vencimiento" class="block text-sm font-medium text-gray-700 mb-1">
                        Fecha de Vencimiento
                    </label>
                    <input type="date" name="fecha_vencimiento" id="fecha_vencimiento" 
                           value="{{ old('fecha_vencimiento', $medicamento->fecha_vencimiento ? $medicamento->fecha_vencimiento->format('Y-m-d') : '') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500">
                </div>

                <!-- Lote -->
                <div>
                    <label for="lote" class="block text-sm font-medium text-gray-700 mb-1">
                        Número de Lote
                    </label>
                    <input type="text" name="lote" id="lote" value="{{ old('lote', $medicamento->lote) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                           placeholder="Lote...">
                </div>

                <!-- Observaciones -->
                <div class="lg:col-span-3">
                    <label for="observaciones" class="block text-sm font-medium text-gray-700 mb-1">
                        Observaciones
                    </label>
                    <textarea name="observaciones" id="observaciones" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                              placeholder="Notas adicionales...">{{ old('observaciones', $medicamento->observaciones) }}</textarea>
                </div>
            </div>

            <!-- Botones -->
            <div class="flex items-center justify-end gap-4 pt-6 border-t border-gray-200">
                <a href="{{ route('emergency-staff.medicamentos.index') }}" 
                   class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2 rounded-lg transition-colors">
                    Cancelar
                </a>
                <button type="submit" 
                        class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg transition-colors">
                    Actualizar Medicamento
                </button>
            </div>
        </form>
    </div>

    <!-- Actualizar Stock Rápido -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mt-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Actualizar Stock Rápidamente</h3>
        <form action="{{ route('emergency-staff.medicamentos.stock', $medicamento) }}" method="POST" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="cantidad_stock" class="block text-sm font-medium text-gray-700 mb-1">
                        Nueva Cantidad <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="cantidad" id="cantidad_stock" required min="0" step="1"
                           value="{{ $medicamento->cantidad }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                           placeholder="0">
                </div>
                <div class="md:col-span-2">
                    <label for="motivo" class="block text-sm font-medium text-gray-700 mb-1">
                        Motivo del Cambio <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="motivo" id="motivo" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                           placeholder="Ej: Reposición de stock, Ajuste por inventario, etc.">
                </div>
            </div>
            <div class="flex items-center justify-end">
                <button type="submit" 
                        class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg transition-colors">
                    Actualizar Stock
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
