@extends('layouts.app')

@section('title', 'Agregar Medicamento/Insumo al Almacén')

@section('content')
<div class="min-h-screen bg-gray-50 p-6">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Agregar Medicamento/Insumo</h1>
            <p class="text-gray-600 mt-1">Se crea el ítem en el catálogo central con su primer lote</p>
        </div>
        <a href="{{ route('admin.almacen-medicamentos.index') }}"
           class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver
        </a>
    </div>

    @if($errors->any())
    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">
        <ul class="list-disc list-inside space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    <form method="POST" action="{{ route('admin.almacen-medicamentos.store') }}"
          x-data="{
              precioCompra: {{ old('precio_compra', 0) }},
              porcentaje: {{ old('porcentaje_ganancia', 0) }},
              get precioVenta() {
                  const p = parseFloat(this.precioCompra) || 0;
                  const g = parseFloat(this.porcentaje) || 0;
                  return (p * (1 + g / 100)).toFixed(2);
              }
          }">
        @csrf

        <div class="max-w-4xl">
            <!-- Sección 1: Identificación del Ítem -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                <h3 class="text-base font-semibold text-gray-900 mb-5">1. Identificación del Medicamento/Insumo</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre <span class="text-red-500">*</span></label>
                        <input type="text" name="nombre" value="{{ old('nombre') }}" required maxlength="255"
                               placeholder="Ej: Paracetamol 500mg"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg @error('nombre') border-red-500 @enderror">
                        @error('nombre')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tipo <span class="text-red-500">*</span></label>
                            <select name="tipo" required class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                <option value="">Seleccionar tipo</option>
                                @foreach($tipos as $v => $l)
                                    <option value="{{ $v }}" {{ old('tipo') == $v ? 'selected' : '' }}>{{ $l }}</option>
                                @endforeach
                            </select>
                            @error('tipo')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Unidad de medida <span class="text-red-500">*</span></label>
                            <select name="unidad_medida" required class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                @foreach($unidades as $u)
                                    <option value="{{ $u }}" {{ old('unidad_medida', 'unidades') == $u ? 'selected' : '' }}>{{ $u }}</option>
                                @endforeach
                            </select>
                            @error('unidad_medida')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                        <textarea name="descripcion" rows="2" placeholder="Descripción opcional del medicamento o insumo"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg">{{ old('descripcion') }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Observaciones</label>
                        <textarea name="observaciones" rows="2" placeholder="Notas adicionales sobre el ítem"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg">{{ old('observaciones') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Sección 2: Precios -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                <h3 class="text-base font-semibold text-gray-900 mb-5">2. Configuración de Precios</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Precio de compra (Bs) <span class="text-red-500">*</span></label>
                        <input type="number" name="precio_compra" x-model="precioCompra" step="0.01" min="0" required
                               value="{{ old('precio_compra') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">% Ganancia <span class="text-red-500">*</span></label>
                        <input type="number" name="porcentaje_ganancia" x-model="porcentaje" step="0.1" min="0" max="999" required
                               value="{{ old('porcentaje_ganancia') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Precio de venta (Bs)</label>
                        <input type="number" name="precio_venta" :value="precioVenta" step="0.01" min="0" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50">
                        <p class="text-xs text-gray-500 mt-1">Calculado automáticamente</p>
                    </div>
                </div>
            </div>

            <!-- Sección 3: Stock Inicial -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                <h3 class="text-base font-semibold text-gray-900 mb-5">3. Stock Inicial (Primer Lote)</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Código de lote</label>
                        <input type="text" name="codigo_lote" value="{{ old('codigo_lote') }}" maxlength="100"
                               placeholder="Ej: L-2026-001"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de vencimiento <span class="text-red-500">*</span></label>
                        <input type="date" name="fecha_vencimiento" value="{{ old('fecha_vencimiento') }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        @error('fecha_vencimiento')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cantidad inicial <span class="text-red-500">*</span></label>
                        <input type="number" name="cantidad_inicial" value="{{ old('cantidad_inicial', 0) }}" min="0" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        @error('cantidad_inicial')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Stock mínimo <span class="text-red-500">*</span></label>
                        <input type="number" name="stock_minimo" value="{{ old('stock_minimo', 0) }}" min="0" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        @error('stock_minimo')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            <!-- Acción Principal -->
            <div class="flex items-center justify-between">
                <p class="text-sm text-gray-500">Los campos con <span class="text-red-500">*</span> son obligatorios</p>
                <button type="submit"
                        class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                    Guardar en Almacén
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
