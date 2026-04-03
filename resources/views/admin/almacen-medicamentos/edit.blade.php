@extends('layouts.app')

@section('title', 'Editar Medicamento/Insumo del Almacén')

@section('content')
<div class="min-h-screen bg-gray-50 p-6">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Editar Medicamento/Insumo del Almacén</h1>
                <p class="text-gray-600 mt-1">Actualiza la información del medicamento/insumo seleccionado</p>
            </div>
            <a href="{{ route('admin.almacen-medicamentos.index') }}" 
               class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg flex items-center gap-2 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Volver
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Formulario Principal -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Editar Información</h3>
                </div>
                
                <div class="p-6">
                    <form method="POST" action="{{ route('admin.almacen-medicamentos.update', $almacenMedicamento) }}" class="space-y-6">
                        @csrf
                        @method('PUT')
                        
                        <!-- Información Básica -->
                        <div class="space-y-4">
                            <h4 class="text-md font-medium text-gray-900 pb-2 border-b border-gray-200">Información Básica</h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1">
                                        Nombre del Medicamento/Insumo <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="nombre" id="nombre" 
                                           value="{{ old('nombre', $almacenMedicamento->nombre) }}" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                           required maxlength="255">
                                    @error('nombre')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div>
                                    <label for="tipo" class="block text-sm font-medium text-gray-700 mb-1">
                                        Tipo <span class="text-red-500">*</span>
                                    </label>
                                    <select name="tipo" id="tipo" 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                            required>
                                        <option value="">Seleccionar tipo</option>
                                        @foreach($tipos as $value => $label)
                                            <option value="{{ $value }}" {{ old('tipo', $almacenMedicamento->tipo) == $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('tipo')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div>
                                <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                                <textarea name="descripcion" id="descripcion" rows="3" 
                                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('descripcion', $almacenMedicamento->descripcion) }}</textarea>
                                @error('descripcion')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Ubicación y Uso -->
                        <div class="space-y-4">
                            <h4 class="text-md font-medium text-gray-900 pb-2 border-b border-gray-200">Ubicación y Uso</h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="area" class="block text-sm font-medium text-gray-700 mb-1">
                                        Área de Uso <span class="text-red-500">*</span>
                                    </label>
                                    <select name="area" id="area" 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                            required>
                                        <option value="">Seleccionar área</option>
                                        @foreach($areas as $value => $label)
                                            <option value="{{ $value }}" {{ old('area', $almacenMedicamento->area) == $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('area')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div>
                                    <label for="unidad_medida" class="block text-sm font-medium text-gray-700 mb-1">
                                        Unidad de Medida <span class="text-red-500">*</span>
                                    </label>
                                    <select name="unidad_medida" id="unidad_medida" 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                            required>
                                        <option value="">Seleccionar unidad</option>
                                        @foreach($unidades as $unidad)
                                            <option value="{{ $unidad }}" {{ old('unidad_medida', $almacenMedicamento->unidad_medida) == $unidad ? 'selected' : '' }}>
                                                {{ ucfirst($unidad) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('unidad_medida')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Inventario y Stock -->
                        <div class="space-y-4">
                            <h4 class="text-md font-medium text-gray-900 pb-2 border-b border-gray-200">Inventario y Stock</h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label for="cantidad" class="block text-sm font-medium text-gray-700 mb-1">
                                        Cantidad Actual <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" name="cantidad" id="cantidad" 
                                           value="{{ old('cantidad', $almacenMedicamento->cantidad) }}" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                           min="0" step="0.01" required>
                                    <p class="mt-1 text-xs text-gray-500">Puede usar decimales (ej: 3.5, 10.25)</p>
                                    @error('cantidad')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div>
                                    <label for="stock_minimo" class="block text-sm font-medium text-gray-700 mb-1">
                                        Stock Mínimo <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" name="stock_minimo" id="stock_minimo" 
                                           value="{{ old('stock_minimo', $almacenMedicamento->stock_minimo) }}" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                           min="0" step="0.01" required>
                                    <p class="mt-1 text-xs text-gray-500">Alerta cuando el stock llegue a este nivel</p>
                                    @error('stock_minimo')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div>
                                    <label for="precio" class="block text-sm font-medium text-gray-700 mb-1">Precio Unitario</label>
                                    <input type="number" name="precio" id="precio" 
                                           value="{{ old('precio', $almacenMedicamento->precio ? number_format($almacenMedicamento->precio, 2, '.', '') : '0.00') }}" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                           min="0" step="0.01" placeholder="0.00">
                                    <p class="mt-1 text-xs text-gray-500">Opcional, para control de costos (ej: 0.40)</p>
                                    @error('precio')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Control de Calidad -->
                        <div class="space-y-4">
                            <h4 class="text-md font-medium text-gray-900 pb-2 border-b border-gray-200">Control de Calidad</h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="lote" class="block text-sm font-medium text-gray-700 mb-1">Número de Lote</label>
                                    <input type="text" name="lote" id="lote" 
                                           value="{{ old('lote', $almacenMedicamento->lote) }}" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                           maxlength="100">
                                    <p class="mt-1 text-xs text-gray-500">Opcional</p>
                                    @error('lote')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div>
                                    <label for="fecha_vencimiento" class="block text-sm font-medium text-gray-700 mb-1">Fecha de Vencimiento</label>
                                    <input type="date" name="fecha_vencimiento" id="fecha_vencimiento" 
                                           value="{{ old('fecha_vencimiento', $almacenMedicamento->fecha_vencimiento ? $almacenMedicamento->fecha_vencimiento->format('Y-m-d') : '') }}" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <p class="mt-1 text-xs text-gray-500">Opcional, para medicamentos</p>
                                    @error('fecha_vencimiento')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Observaciones -->
                        <div class="space-y-4">
                            <h4 class="text-md font-medium text-gray-900 pb-2 border-b border-gray-200">Observaciones</h4>
                            
                            <div>
                                <label for="observaciones" class="block text-sm font-medium text-gray-700 mb-1">Observaciones</label>
                                <textarea name="observaciones" id="observaciones" rows="3" 
                                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('observaciones', $almacenMedicamento->observaciones) }}</textarea>
                                <p class="mt-1 text-xs text-gray-500">Notas adicionales sobre el medicamento/insumo</p>
                                @error('observaciones')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="flex justify-between pt-6 border-t border-gray-200">
                            <a href="{{ route('admin.almacen-medicamentos.index') }}" 
                               class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Cancelar
                            </a>
                            <div>
                                <button type="button" onclick="actualizarStock()" 
                                        class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition-colors flex items-center gap-2 mr-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                    </svg>
                                    Actualizar Stock
                                </button>
                                <button type="submit" 
                                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Guardar Cambios
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Panel de Estado -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 sticky top-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Estado Actual
                </h3>
                
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-gray-600">Stock Actual:</p>
                            <p class="font-semibold">
                                @if($almacenMedicamento->cantidad == 0)
                                    <span class="text-red-600">0</span>
                                @elseif($almacenMedicamento->estaBajoStock())
                                    <span class="text-yellow-600">{{ $almacenMedicamento->cantidad }}</span>
                                @else
                                    <span class="text-green-600">{{ $almacenMedicamento->cantidad }}</span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-gray-600">Stock Mínimo:</p>
                            <p class="font-semibold">{{ $almacenMedicamento->stock_minimo }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Estado Stock:</p>
                            <p class="font-semibold">
                                <span class="px-2 py-1 text-xs rounded-full {{ $almacenMedicamento->estado_stock == 'normal' ? 'bg-green-100 text-green-800' : ($almacenMedicamento->estado_stock == 'bajo' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                    {{ ucfirst($almacenMedicamento->estado_stock) }}
                                </span>
                            </p>
                        </div>
                        <div>
                            <p class="text-gray-600">Vencimiento:</p>
                            <p class="font-semibold">
                                @if($almacenMedicamento->fecha_vencimiento)
                                    @if($almacenMedicamento->estaVencido())
                                        <span class="text-red-600">Vencido</span>
                                    @elseif($almacenMedicamento->estaPorVencer())
                                        <span class="text-yellow-600">Por vencer ({{ $almacenMedicamento->dias_para_vencer }} días)</span>
                                    @else
                                        <span class="text-green-600">Vigente</span>
                                    @endif
                                @else
                                    <span class="text-gray-500">Sin fecha</span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-gray-600">Área:</p>
                            <p class="font-semibold">
                                <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                                    {{ $almacenMedicamento->area_label }}
                                </span>
                            </p>
                        </div>
                        <div>
                            <p class="text-gray-600">Tipo:</p>
                            <p class="font-semibold">
                                <span class="px-2 py-1 text-xs rounded-full {{ $almacenMedicamento->tipo == 'medicamento' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $almacenMedicamento->tipo_label }}
                                </span>
                            </p>
                        </div>
                    </div>

                    @if($almacenMedicamento->created_at)
                        <div class="border-t pt-4">
                            <h4 class="font-medium text-gray-900 mb-2">Información de Registro</h4>
                            <div class="space-y-2 text-sm text-gray-600">
                                <p><strong>Creado:</strong> {{ $almacenMedicamento->created_at->format('d/m/Y H:i') }}</p>
                                <p><strong>Actualizado:</strong> {{ $almacenMedicamento->updated_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    @endif

                    <div class="border-t pt-4">
                        <h4 class="font-medium text-gray-900 mb-2">Alertas</h4>
                        @if($almacenMedicamento->estaVencido())
                            <div class="bg-red-50 border border-red-200 rounded-lg p-3 mb-2">
                                <div class="flex items-start gap-2">
                                    <svg class="w-4 h-4 text-red-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                    <div class="text-sm text-red-800">
                                        <p class="font-medium">¡MEDICAMENTO VENCIDO!</p>
                                        <p>Este medicamento ha vencido y no debería usarse</p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if($almacenMedicamento->estaPorVencer())
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mb-2">
                                <div class="flex items-start gap-2">
                                    <svg class="w-4 h-4 text-yellow-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <div class="text-sm text-yellow-800">
                                        <p class="font-medium">Por vencer en {{ $almacenMedicamento->dias_para_vencer }} días</p>
                                        <p>Considere usar o reemplazar pronto</p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if($almacenMedicamento->estaBajoStock())
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mb-2">
                                <div class="flex items-start gap-2">
                                    <svg class="w-4 h-4 text-yellow-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                    </svg>
                                    <div class="text-sm text-yellow-800">
                                        <p class="font-medium">Stock bajo</p>
                                        <p>Actual: {{ $almacenMedicamento->cantidad }}, Mínimo: {{ $almacenMedicamento->stock_minimo }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if($almacenMedicamento->cantidad == 0)
                            <div class="bg-red-50 border border-red-200 rounded-lg p-3 mb-2">
                                <div class="flex items-start gap-2">
                                    <svg class="w-4 h-4 text-red-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                    <div class="text-sm text-red-800">
                                        <p class="font-medium">¡AGOTADO!</p>
                                        <p>No hay unidades disponibles</p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if(!$almacenMedicamento->estaVencido() && !$almacenMedicamento->estaPorVencer() && !$almacenMedicamento->estaBajoStock() && $almacenMedicamento->cantidad > 0)
                            <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                                <div class="flex items-start gap-2">
                                    <svg class="w-4 h-4 text-green-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <div class="text-sm text-green-800">
                                        <p class="font-medium">Todo en orden</p>
                                        <p>Stock adecuado y vigente</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para actualizar stock -->
<div x-data="{ open: false }" 
     x-show="open" 
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>
        
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full" 
             x-show="open" 
             x-transition:enter="ease-out duration-300" 
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
             x-transition:leave="ease-in duration-200" 
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
            <form method="POST" action="{{ route('admin.almacen-medicamentos.actualizar-stock', $almacenMedicamento) }}">
                @csrf
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="mb-4">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Actualizar Stock</h3>
                        <div class="space-y-2">
                            <p><strong>Item:</strong> {{ $almacenMedicamento->nombre }}</p>
                            <p><strong>Stock actual:</strong> {{ $almacenMedicamento->cantidad }}</p>
                        </div>
                    </div>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nueva Cantidad</label>
                            <input type="number" name="cantidad" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" min="0" step="0.01" required>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Motivo del cambio</label>
                            <textarea name="motivo" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" rows="3" required></textarea>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Actualizar
                    </button>
                    <button type="button" @click="open = false" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function actualizarStock() {
    const modal = document.querySelector('[x-data*="open"]');
    if (modal && modal.__x) {
        modal.__x.$data.open = true;
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const tipoSelect = document.getElementById('tipo');
    const fechaVencimiento = document.getElementById('fecha_vencimiento');
    const lote = document.getElementById('lote');
    
    function updateRequiredFields() {
        if (tipoSelect.value === 'medicamento') {
            fechaVencimiento.setAttribute('required', 'required');
            lote.setAttribute('required', 'required');
        } else {
            fechaVencimiento.removeAttribute('required');
            lote.removeAttribute('required');
        }
    }
    
    tipoSelect.addEventListener('change', updateRequiredFields);
    updateRequiredFields();
});
</script>
@endsection
