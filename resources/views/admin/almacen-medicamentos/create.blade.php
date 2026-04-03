@extends('layouts.app')

@section('title', 'Agregar Medicamento/Insumo al Almacén')

@section('content')
<div class="min-h-screen bg-gray-50 p-6">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Agregar Medicamento/Insumo al Almacén</h1>
                <p class="text-gray-600 mt-1">Registra nuevos medicamentos e insumos para las diferentes áreas médicas</p>
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
                    <h3 class="text-lg font-semibold text-gray-900">Información del Medicamento/Insumo</h3>
                </div>
                
                <div class="p-6">
                    <form method="POST" action="{{ route('admin.almacen-medicamentos.store') }}" class="space-y-6">
                        @csrf
                        
                        <!-- Información Básica -->
                        <div class="space-y-4">
                            <h4 class="text-md font-medium text-gray-900 pb-2 border-b border-gray-200">Información Básica</h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1">
                                        Nombre del Medicamento/Insumo <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="nombre" id="nombre" 
                                           value="{{ old('nombre') }}" 
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
                                            <option value="{{ $value }}" {{ old('tipo') == $value ? 'selected' : '' }}>
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
                                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('descripcion') }}</textarea>
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
                                            <option value="{{ $value }}" {{ old('area') == $value ? 'selected' : '' }}>
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
                                            <option value="{{ $unidad }}" {{ old('unidad_medida') == $unidad ? 'selected' : '' }}>
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
                                           value="{{ old('cantidad', 0) }}" 
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
                                           value="{{ old('stock_minimo', 5) }}" 
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
                                           value="{{ old('precio', '0.00') }}" 
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
                                           value="{{ old('lote') }}" 
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
                                           value="{{ old('fecha_vencimiento') }}" 
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
                                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('observaciones') }}</textarea>
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
                            <button type="submit" 
                                    class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Guardar Medicamento/Insumo
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Panel de Ayuda -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 sticky top-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Información de Ayuda
                </h3>
                
                <div class="space-y-4">
                    <div>
                        <h4 class="font-medium text-gray-900 mb-2">Áreas Disponibles:</h4>
                        <ul class="space-y-2 text-sm text-gray-600">
                            <li class="flex items-start gap-2">
                                <span class="text-blue-600 mt-1">•</span>
                                <div>
                                    <strong>Emergencia:</strong> Medicamentos e insumos para atención de emergencias
                                </div>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-blue-600 mt-1">•</span>
                                <div>
                                    <strong>Cirugía:</strong> Insumos y medicamentos quirúrgicos
                                </div>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-blue-600 mt-1">•</span>
                                <div>
                                    <strong>Hospitalización:</strong> Medicamentos para pacientes hospitalizados
                                </div>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-blue-600 mt-1">•</span>
                                <div>
                                    <strong>UTI:</strong> Unidad de Terapia Intensiva
                                </div>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-blue-600 mt-1">•</span>
                                <div>
                                    <strong>USI:</strong> Unidad de Semi-Intensivos
                                </div>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="text-blue-600 mt-1">•</span>
                                <div>
                                    <strong>Neonato:</strong> Medicamentos e insumos para neonatos
                                </div>
                            </li>
                        </ul>
                    </div>

                    <div>
                        <h4 class="font-medium text-gray-900 mb-2">Recomendaciones:</h4>
                        <ul class="space-y-2 text-sm text-gray-600">
                            <li class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-green-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span>Establezca un stock mínimo para recibir alertas automáticas</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-green-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span>Registre el lote y vencimiento para medicamentos</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-green-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span>Sea específico en la descripción para facilitar la búsqueda</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-green-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span>El precio es opcional pero útil para control de costos</span>
                            </li>
                        </ul>
                    </div>

                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-start gap-2">
                            <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div class="text-sm text-blue-800">
                                <p class="font-medium mb-1">Tip Importante</p>
                                <p>Los datos registrados aquí estarán disponibles para todos los módulos médicos según el área asignada.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
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
    
    // Establecer fecha mínima para vencimiento (hoy)
    const today = new Date().toISOString().split('T')[0];
    fechaVencimiento.setAttribute('min', today);
});
</script>
@endsection
