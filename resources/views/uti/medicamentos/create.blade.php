@extends('layouts.app')

@section('title', 'Agregar Medicamento - UTI')

@section('content')
<div class="min-h-screen bg-gray-50 p-6">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Agregar Medicamento/Insumo</h1>
                <p class="text-gray-600 mt-1">Añadir nuevo item al inventario de UTI</p>
            </div>
            <a href="{{ route('uti.operativa.medicamentos.index') }}" 
               class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Volver
            </a>
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
        <form action="{{ route('uti.operativa.medicamentos.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Nombre -->
                <div class="lg:col-span-2">
                    <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1">
                        Nombre <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nombre" id="nombre" value="{{ old('nombre') }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Nombre del medicamento o insumo">
                </div>

                <!-- Tipo -->
                <div>
                    <label for="tipo" class="block text-sm font-medium text-gray-700 mb-1">
                        Tipo <span class="text-red-500">*</span>
                    </label>
                    <select name="tipo" id="tipo" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @foreach($tipos as $value => $label)
                            <option value="{{ $value }}" {{ old('tipo') == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Descripción -->
                <div class="lg:col-span-3">
                    <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-1">
                        Descripción
                    </label>
                    <textarea name="descripcion" id="descripcion" rows="2"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Descripción detallada del producto...">{{ old('descripcion') }}</textarea>
                </div>

                <!-- Cantidad -->
                <div>
                    <label for="cantidad" class="block text-sm font-medium text-gray-700 mb-1">
                        Cantidad/Stock <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="cantidad" id="cantidad" value="{{ old('cantidad', 0) }}" required min="0" step="1"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="0">
                </div>

                <!-- Unidad de Medida -->
                <div>
                    <label for="unidad_medida" class="block text-sm font-medium text-gray-700 mb-1">
                        Unidad de Medida <span class="text-red-500">*</span>
                    </label>
                    <select name="unidad_medida" id="unidad_medida" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @foreach($unidades as $value => $label)
                            <option value="{{ $value }}" {{ old('unidad_medida') == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Stock Mínimo -->
                <div>
                    <label for="stock_minimo" class="block text-sm font-medium text-gray-700 mb-1">
                        Stock Mínimo <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="stock_minimo" id="stock_minimo" value="{{ old('stock_minimo', 5) }}" required min="0" step="1"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
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
                        <input type="number" name="precio" id="precio" value="{{ old('precio') }}" min="0" step="0.01"
                               class="w-full pl-7 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               placeholder="0.00">
                    </div>
                </div>

                <!-- Fecha de Vencimiento -->
                <div>
                    <label for="fecha_vencimiento" class="block text-sm font-medium text-gray-700 mb-1">
                        Fecha de Vencimiento
                    </label>
                    <input type="date" name="fecha_vencimiento" id="fecha_vencimiento" value="{{ old('fecha_vencimiento') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- Lote -->
                <div>
                    <label for="lote" class="block text-sm font-medium text-gray-700 mb-1">
                        Número de Lote
                    </label>
                    <input type="text" name="lote" id="lote" value="{{ old('lote') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Lote...">
                </div>

                <!-- Observaciones -->
                <div class="lg:col-span-3">
                    <label for="observaciones" class="block text-sm font-medium text-gray-700 mb-1">
                        Observaciones
                    </label>
                    <textarea name="observaciones" id="observaciones" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Notas adicionales...">{{ old('observaciones') }}</textarea>
                </div>
            </div>

            <!-- Botones -->
            <div class="flex items-center justify-end gap-4 pt-6 border-t border-gray-200">
                <a href="{{ route('uti.operativa.medicamentos.index') }}" 
                   class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2 rounded-lg transition-colors">
                    Cancelar
                </a>
                <button type="submit" 
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors">
                    Guardar Medicamento
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
