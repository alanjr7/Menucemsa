@extends('layouts.app')

@section('title', 'Editar Activo — Almacén Inventario')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header con navegación -->
        <div class="mb-8">
            <nav class="flex items-center text-sm text-gray-500 mb-4" aria-label="Breadcrumb">
                <a href="{{ route('dashboard') }}" class="hover:text-gray-700 transition-colors">Dashboard</a>
                <svg class="w-4 h-4 mx-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                </svg>
                <a href="{{ route('admin.almacen-inventario.index') }}" class="hover:text-gray-700 transition-colors">Almacén Inventario</a>
                <svg class="w-4 h-4 mx-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                </svg>
                <span class="text-gray-900 font-medium">Editar Activo</span>
            </nav>
            
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Editar Activo</h1>
                    <p class="text-gray-600">Modifique la información del activo seleccionado</p>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                            <path fill-rule="evenodd" d="M4 5a2 2 0 012-2v1a1 1 0 102 0V3h4v1a1 1 0 102 0V3a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                        </svg>
                        {{ $almacenInventario->codigo_activo }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Tarjeta principal del formulario -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
            <!-- Barra superior de la tarjeta -->
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-white mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <h2 class="text-xl font-semibold text-white">Información del Activo</h2>
                </div>
            </div>
            
            <div class="p-8">
                <form method="POST" action="{{ route('admin.almacen-inventario.update', $almacenInventario) }}" class="space-y-8" id="editForm">
                    @csrf @method('PUT')
                    
                    <!-- Sección: Información Básica -->
                    <div class="space-y-6">
                        <div class="border-b border-gray-200 pb-4">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                                Información Básica
                            </h3>
                            <p class="text-sm text-gray-600 mt-1">Datos principales del activo</p>
                        </div>
                        
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label for="codigo_activo" class="block text-sm font-semibold text-gray-900">
                                    Código de Activo <span class="text-red-500" aria-label="requerido">*</span>
                                </label>
                                <div class="relative">
                                    <input type="text" id="codigo_activo" name="codigo_activo"
                                           value="{{ old('codigo_activo', $almacenInventario->codigo_activo) }}"
                                           class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 @error('codigo_activo') border-red-400 bg-red-50 @enderror"
                                           placeholder="Ej: EQM001"
                                           required
                                           aria-describedby="codigo_activo_help @error('codigo_activo') codigo_activo_error @enderror">
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                </div>
                                <p id="codigo_activo_help" class="text-xs text-gray-500">Código único de identificación del activo</p>
                                @error('codigo_activo')
                                <p id="codigo_activo_error" class="text-sm text-red-600 font-medium mt-1 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                                @enderror
                            </div>

                            <div class="space-y-2">
                                <label for="nombre" class="block text-sm font-semibold text-gray-900">
                                    Nombre del Activo <span class="text-red-500" aria-label="requerido">*</span>
                                </label>
                                <div class="relative">
                                    <input type="text" id="nombre" name="nombre"
                                           value="{{ old('nombre', $almacenInventario->nombre) }}"
                                           class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 @error('nombre') border-red-400 bg-red-50 @enderror"
                                           placeholder="Ej: Monitor de Signos Vitales"
                                           required
                                           aria-describedby="nombre_help @error('nombre') nombre_error @enderror">
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                </div>
                                <p id="nombre_help" class="text-xs text-gray-500">Nombre descriptivo del activo</p>
                                @error('nombre')
                                <p id="nombre_error" class="text-sm text-red-600 font-medium mt-1 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                                @enderror
                            </div>

                            <div class="space-y-2">
                                <label for="precio" class="block text-sm font-semibold text-gray-900">
                                    Precio (Bs.) <span class="text-red-500" aria-label="requerido">*</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 sm:text-sm font-medium">Bs.</span>
                                    </div>
                                    <input type="number" id="precio" name="precio"
                                           value="{{ old('precio', $almacenInventario->precio) }}"
                                           step="0.01" min="0"
                                           class="w-full border-2 border-gray-300 rounded-xl pl-12 pr-4 py-3 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 @error('precio') border-red-400 bg-red-50 @enderror"
                                           placeholder="0.00"
                                           required
                                           aria-describedby="precio_help @error('precio') precio_error @enderror">
                                </div>
                                <p id="precio_help" class="text-xs text-gray-500">Valor unitario del activo</p>
                                @error('precio')
                                <p id="precio_error" class="text-sm text-red-600 font-medium mt-1 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                                @enderror
                            </div>

                            <div class="space-y-2">
                                <label for="cantidad" class="block text-sm font-semibold text-gray-900">
                                    Cantidad <span class="text-red-500" aria-label="requerido">*</span>
                                </label>
                                <div class="relative">
                                    <input type="number" id="cantidad" name="cantidad"
                                           value="{{ old('cantidad', $almacenInventario->cantidad) }}"
                                           min="0"
                                           class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 @error('cantidad') border-red-400 bg-red-50 @enderror"
                                           placeholder="0"
                                           required
                                           aria-describedby="cantidad_help @error('cantidad') cantidad_error @enderror">
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"/>
                                        </svg>
                                    </div>
                                </div>
                                <p id="cantidad_help" class="text-xs text-gray-500">Unidades disponibles en inventario</p>
                                @error('cantidad')
                                <p id="cantidad_error" class="text-sm text-red-600 font-medium mt-1 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <!-- Sección: Información Adicional -->
                    <div class="space-y-6">
                        <div class="border-b border-gray-200 pb-4">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                                Información Adicional
                            </h3>
                            <p class="text-sm text-gray-600 mt-1">Detalles complementarios del activo</p>
                        </div>
                        
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label for="marca" class="block text-sm font-semibold text-gray-900">Marca</label>
                                <div class="relative">
                                    <input type="text" id="marca" name="marca"
                                           value="{{ old('marca', $almacenInventario->marca) }}"
                                           class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                                           placeholder="Ej: Philips, GE Healthcare"
                                           aria-describedby="marca_help">
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                </div>
                                <p id="marca_help" class="text-xs text-gray-500">Fabricante o marca comercial</p>
                            </div>

                            <div class="space-y-2">
                                <label for="proveedor" class="block text-sm font-semibold text-gray-900">Proveedor</label>
                                <div class="relative">
                                    <input type="text" id="proveedor" name="proveedor"
                                           value="{{ old('proveedor', $almacenInventario->proveedor) }}"
                                           class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                                           placeholder="Ej: Médica Santa Cruz"
                                           aria-describedby="proveedor_help">
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                                        </svg>
                                    </div>
                                </div>
                                <p id="proveedor_help" class="text-xs text-gray-500">Nombre del proveedor</p>
                            </div>

                            <div class="space-y-2">
                                <label for="nro_factura" class="block text-sm font-semibold text-gray-900">Número de Factura</label>
                                <div class="relative">
                                    <input type="text" id="nro_factura" name="nro_factura"
                                           value="{{ old('nro_factura', $almacenInventario->nro_factura) }}"
                                           class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                                           placeholder="Ej: 001-123-456"
                                           aria-describedby="nro_factura_help">
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 0h8v12H6V4z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                </div>
                                <p id="nro_factura_help" class="text-xs text-gray-500">Número de factura de compra</p>
                            </div>

                            <div class="space-y-2">
                                <label for="numero_recibo" class="block text-sm font-semibold text-gray-900">Número de Recibo</label>
                                <div class="relative">
                                    <input type="text" id="numero_recibo" name="numero_recibo"
                                           value="{{ old('numero_recibo', $almacenInventario->numero_recibo) }}"
                                           class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                                           placeholder="Ej: REC-2023-001"
                                           aria-describedby="numero_recibo_help">
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 0h8v12H6V4z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                </div>
                                <p id="numero_recibo_help" class="text-xs text-gray-500">Número de recibo interno</p>
                            </div>
                        </div>
                    </div>

                    <!-- Botones de acción -->
                    <div class="flex flex-col sm:flex-row justify-between items-center pt-6 border-t border-gray-200 space-y-4 sm:space-y-0">
                        <div class="flex items-center space-x-2 text-sm text-gray-500">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                            <span>Los campos marcados con <span class="text-red-500 font-medium">*</span> son obligatorios</span>
                        </div>
                        
                        <div class="flex items-center space-x-3">
                            <a href="{{ route('admin.almacen-inventario.index') }}"
                               class="inline-flex items-center px-6 py-3 text-sm font-semibold text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Cancelar
                            </a>
                            <button type="submit"
                                    class="inline-flex items-center px-6 py-3 text-sm font-semibold text-white bg-gradient-to-r from-blue-600 to-blue-700 rounded-xl hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
                                    id="submitBtn">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Actualizar Activo
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Información adicional -->
        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-xl p-4">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
                <div class="text-sm text-blue-800">
                    <p class="font-semibold mb-1">Información de ayuda</p>
                    <p>Al actualizar el activo, se modificará la información en el sistema de inventario. Asegúrese de verificar los datos antes de guardar los cambios.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Script para mejoras de UX -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('editForm');
    const submitBtn = document.getElementById('submitBtn');
    
    // Manejo del estado de carga
    form.addEventListener('submit', function(e) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = `
            <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Actualizando...
        `;
    });
    
    // Auto-formato para campos numéricos
    const precioInput = document.getElementById('precio');
    if (precioInput) {
        precioInput.addEventListener('blur', function() {
            if (this.value && !isNaN(this.value)) {
                this.value = parseFloat(this.value).toFixed(2);
            }
        });
    }
    
    // Navegación con teclado mejorada
    const inputs = form.querySelectorAll('input, button, a');
    inputs.forEach((input, index) => {
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && e.target.type !== 'submit') {
                e.preventDefault();
                const nextInput = inputs[index + 1];
                if (nextInput && nextInput.focus) {
                    nextInput.focus();
                }
            }
        });
    });
});
</script>
@endsection
