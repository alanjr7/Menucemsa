@extends('layouts.app')

@section('content')
<!-- Toast flotante -->
<div x-data="posSystem()">
<div
    x-show="toast.show"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 -translate-y-3"
    x-transition:enter-end="opacity-100 translate-y-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-y-0"
    x-transition:leave-end="opacity-0 -translate-y-3"
    :class="toast.type === 'success' ? 'bg-green-500' : 'bg-red-500'"
    class="fixed top-5 right-5 z-50 flex items-center gap-3 px-5 py-3.5 rounded-xl shadow-2xl text-white text-sm font-medium max-w-sm pointer-events-none"
    style="display:none;"
>
    <template x-if="toast.type === 'success'">
        <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
        </svg>
    </template>
    <template x-if="toast.type === 'error'">
        <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
        </svg>
    </template>
    <span x-text="toast.message" class="leading-snug"></span>
</div>

<div class="flex flex-col lg:grid lg:grid-cols-3 h-[calc(100vh-64px)] overflow-hidden font-sans">

    {{-- ══════════════════════════════════════════════════════
         COLUMNA 1: Catálogo de productos
    ══════════════════════════════════════════════════════ --}}
    <div x-show="mobileView === 'productos'"
         class="flex-1 min-h-0 lg:flex-none flex flex-col overflow-hidden bg-[#f8fafc] border-r border-gray-200 lg:!flex">

        {{-- Header + buscador --}}
        <div class="p-6 pb-3 border-b border-gray-100">
            <h1 class="text-base font-bold text-gray-800 mb-3">Productos</h1>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </span>
                <input type="text"
                    x-model="searchQuery"
                    class="block w-full pl-9 pr-3 py-2.5 border border-blue-300 rounded-xl bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all shadow-sm placeholder-gray-400 text-[13px]"
                    placeholder="Buscar por nombre o código...">
            </div>
        </div>

        {{-- Grid de productos --}}
        <div class="flex-1 overflow-y-auto p-4 custom-scrollbar">
            @if($productos->isEmpty())
                <div class="text-center py-12">
                    <svg class="w-14 h-14 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                    </svg>
                    <h3 class="text-sm font-medium text-gray-900 mb-1">No hay productos disponibles</h3>
                    <p class="text-xs text-gray-400">No se encontraron medicamentos en el inventario.</p>
                </div>
            @else
                <div class="grid grid-cols-2 gap-3">
                    <template x-for="producto in filteredProducts" :key="producto.id">
                        <div @click="addToCart(producto.nombre, producto.precio, producto.id, producto.stock, producto.requiere_receta)"
                             class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm hover:border-blue-400 hover:shadow-md transition-all cursor-pointer flex flex-col justify-between"
                             :class="{ 'opacity-50 cursor-not-allowed hover:border-gray-100 hover:shadow-none': producto.stock <= 0 }">
                            <div>
                                <div class="flex justify-between items-start mb-1">
                                    <h3 class="text-[13px] font-bold text-gray-800 leading-tight pr-1" x-text="producto.nombre"></h3>
                                    <template x-if="producto.requerimiento === 'Receta'">
                                        <span class="bg-red-50 text-red-500 text-[9px] font-bold px-1.5 py-0.5 rounded border border-red-100 shrink-0">Receta</span>
                                    </template>
                                </div>
                                <p class="text-[11px] text-gray-400 font-medium mb-1" x-text="producto.categoria"></p>
                                <div class="text-[10px] text-gray-400 space-y-0.5 mb-3">
                                    <p x-text="'Lote: ' + producto.lote"></p>
                                    <p class="flex items-center gap-1">
                                        <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-width="2"/>
                                        </svg>
                                        <span x-text="'Vence: ' + producto.vencimiento"></span>
                                    </p>
                                </div>
                            </div>
                            <div class="flex justify-between items-end">
                                <span class="text-base font-bold text-blue-600" x-text="'Bs' + parseFloat(producto.precio).toFixed(2)"></span>
                                <span class="text-[11px] font-medium"
                                      :class="producto.stock > 0 ? 'text-green-600' : 'text-red-500'"
                                      x-text="'Stock: ' + producto.stock"></span>
                            </div>
                        </div>
                    </template>

                    <div x-show="filteredProducts.length === 0 && searchQuery" class="col-span-full text-center py-10">
                        <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <h3 class="text-sm font-medium text-gray-900 mb-1">Sin resultados</h3>
                        <p class="text-xs text-gray-400">Ningún producto coincide con "<span x-text="searchQuery"></span>"</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════
         COLUMNA 2: Items del carrito
    ══════════════════════════════════════════════════════ --}}
    <div x-show="mobileView === 'carrito'"
         class="flex-1 min-h-0 lg:flex-none flex flex-col overflow-hidden bg-white border-r border-gray-200 lg:!flex">

        {{-- Header --}}
        <div class="p-5 pb-4 border-b border-gray-100 flex items-center gap-2">
            <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            <h2 class="text-base font-bold text-gray-800">Carrito</h2>
            <span class="ml-auto bg-blue-100 text-blue-600 text-[11px] font-bold px-2 py-0.5 rounded-full" x-text="cart.length + ' items'"></span>
        </div>

        {{-- Selector de cliente / paciente (buscador unificado) --}}
        <div class="px-5 py-3 border-b border-gray-50" @click.outside="receptorOpen = false">
            <label class="block text-[10px] text-gray-400 font-semibold uppercase tracking-wide mb-1.5">Cliente / Paciente</label>

            {{-- Modo búsqueda (oculto al registrar un nuevo cliente) --}}
            <div x-show="!nuevoCliente">
                {{-- Receptor seleccionado --}}
                <template x-if="receptor">
                    <div class="flex items-center justify-between gap-2 border border-blue-200 bg-blue-50 rounded-lg py-2 px-3">
                        <div class="min-w-0">
                            <p class="text-[13px] font-bold text-gray-800 truncate" x-text="receptor.nombre"></p>
                            <p class="text-[10px] text-gray-500">
                                <span class="font-semibold"
                                      :class="receptor.tipo === 'paciente' ? 'text-green-600' : 'text-blue-600'"
                                      x-text="receptor.tipo === 'paciente' ? 'Paciente' : 'Cliente'"></span>
                                <span x-show="receptor.numero_documento" x-text="' · ' + receptor.documento_label + ' ' + receptor.numero_documento"></span>
                            </p>
                        </div>
                        <button @click="limpiarReceptor()" type="button"
                                class="text-gray-400 hover:text-red-500 shrink-0 p-1" title="Quitar">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </template>

                {{-- Buscador --}}
                <div class="relative" x-show="!receptor">
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </span>
                        <input type="text"
                               x-model="receptorQuery"
                               @input.debounce.300ms="buscarReceptor()"
                               @focus="receptorOpen = true"
                               class="w-full border border-gray-200 rounded-lg py-2 pl-9 pr-3 text-[13px] text-gray-700 shadow-sm focus:ring-blue-500 focus:border-blue-500 bg-gray-50"
                               placeholder="Buscar por nombre o CI/NIT...">
                    </div>

                    {{-- Dropdown de resultados --}}
                    <div x-show="receptorOpen && (receptorQuery.length >= 2)"
                         x-transition.opacity
                         class="absolute z-30 mt-1 w-full bg-white border border-gray-200 rounded-lg shadow-lg max-h-64 overflow-y-auto custom-scrollbar"
                         style="display:none;">
                        <button type="button" @click="limpiarReceptor()"
                                class="w-full text-left px-3 py-2 text-[12px] text-gray-500 hover:bg-gray-50 border-b border-gray-100">
                            Cliente General (sin receptor)
                        </button>

                        <template x-if="receptorLoading">
                            <p class="px-3 py-3 text-[12px] text-gray-400 text-center">Buscando...</p>
                        </template>

                        <template x-if="!receptorLoading && receptorResults.length === 0">
                            <p class="px-3 py-3 text-[12px] text-gray-400 text-center">Sin coincidencias</p>
                        </template>

                        <template x-for="r in receptorResults" :key="r.tipo + '-' + r.id">
                            <button type="button" @click="elegirReceptor(r)"
                                    class="w-full text-left px-3 py-2 hover:bg-blue-50 transition-colors border-b border-gray-50 last:border-0">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="text-[13px] font-medium text-gray-800 truncate" x-text="r.nombre"></span>
                                    <span class="text-[9px] font-bold px-1.5 py-0.5 rounded shrink-0"
                                          :class="r.tipo === 'paciente' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700'"
                                          x-text="r.tipo === 'paciente' ? 'Paciente' : 'Cliente'"></span>
                                </div>
                                <p class="text-[10px] text-gray-400 mt-0.5">
                                    <span x-show="r.numero_documento" x-text="r.documento_label + ' ' + r.numero_documento"></span>
                                    <span x-show="r.telefono" x-text="(r.numero_documento ? ' · ' : '') + 'Tel: ' + r.telefono"></span>
                                </p>
                            </button>
                        </template>
                    </div>
                </div>
            </div>

            {{-- Checkbox: registrar nuevo cliente al vuelo --}}
            <label class="flex items-center gap-2 cursor-pointer mt-2.5">
                <input type="checkbox" x-model="nuevoCliente" @change="toggleNuevoCliente()"
                       class="w-4 h-4 rounded text-blue-600 border-gray-300 focus:ring-blue-500">
                <span class="text-[12px] text-gray-700 font-semibold">Registrar nuevo cliente</span>
            </label>

            {{-- Campos del nuevo cliente (se guardan en clientes y van a la factura) --}}
            <div x-show="nuevoCliente" class="mt-2.5 space-y-2 bg-blue-50/60 rounded-xl border border-blue-100 p-3">
                <div>
                    <label class="block text-[10px] text-gray-400 font-semibold uppercase tracking-wide mb-1">Nombre / Razón social <span class="text-red-500">*</span></label>
                    <input type="text" x-model="clienteNuevo.nombre"
                           class="w-full border border-gray-200 rounded-lg py-1.5 px-2.5 text-[13px] text-gray-700 bg-white focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Nombre tal cual va en la factura">
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-[10px] text-gray-400 font-semibold uppercase tracking-wide mb-1">Tipo doc.</label>
                        <select x-model.number="clienteNuevo.tipo_documento"
                                class="w-full border border-gray-200 rounded-lg py-1.5 px-2 text-[13px] text-gray-700 bg-white focus:ring-blue-500 focus:border-blue-500">
                            <template x-for="t in tiposDocumento" :key="t.code">
                                <option :value="t.code" x-text="t.label"></option>
                            </template>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] text-gray-400 font-semibold uppercase tracking-wide mb-1">N° documento <span class="text-red-500">*</span></label>
                        <input type="text" inputmode="numeric" x-model="clienteNuevo.numero_documento"
                               class="w-full border border-gray-200 rounded-lg py-1.5 px-2.5 text-[13px] text-gray-700 bg-white focus:ring-blue-500 focus:border-blue-500"
                               placeholder="NIT o CI">
                    </div>
                </div>
                <div x-show="clienteNuevo.tipo_documento === 1">
                    <label class="block text-[10px] text-gray-400 font-semibold uppercase tracking-wide mb-1">Complemento (opcional)</label>
                    <input type="text" x-model="clienteNuevo.complemento" maxlength="5"
                           class="w-full border border-gray-200 rounded-lg py-1.5 px-2.5 text-[13px] text-gray-700 bg-white focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Ej: 1A">
                </div>
                <div>
                    <label class="block text-[10px] text-gray-400 font-semibold uppercase tracking-wide mb-1">Teléfono (opcional)</label>
                    <input type="text" inputmode="numeric" x-model="clienteNuevo.telefono"
                           class="w-full border border-gray-200 rounded-lg py-1.5 px-2.5 text-[13px] text-gray-700 bg-white focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Teléfono de contacto">
                </div>
            </div>

            <p class="text-[10px] text-gray-400 mt-1">
                <a href="{{ route('farmacia.clientes') }}" class="text-blue-500 hover:underline">Administrar clientes →</a>
            </p>
        </div>

        {{-- Lista de items --}}
        <div class="flex-1 overflow-y-auto px-4 py-3 space-y-3 custom-scrollbar">
            <template x-if="cart.length === 0">
                <div class="text-center py-16">
                    <svg class="w-12 h-12 mx-auto text-gray-200 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <p class="text-[12px] text-gray-400">Selecciona productos del catálogo</p>
                </div>
            </template>

            <template x-for="(item, index) in cart" :key="index">
                <div class="bg-gray-50 p-3.5 rounded-xl border border-gray-100 shadow-sm">
                    <div class="flex justify-between items-start mb-2">
                        <div class="pr-2 flex-1 min-w-0">
                            <p class="font-bold text-gray-800 text-[13px] leading-tight truncate" x-text="item.name"></p>
                            <p class="text-[11px] text-gray-400 mt-0.5" x-text="'Bs' + item.price.toFixed(2) + ' c/u'"></p>
                        </div>
                        <button @click="removeFromCart(index)"
                                class="text-red-400 hover:text-red-600 p-1 transition-colors rounded-lg hover:bg-red-50 shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </div>
                    <div class="flex justify-between items-center">
                        <div class="flex items-center border border-gray-200 rounded-lg overflow-hidden bg-white shadow-sm">
                            <button @click="updateQty(index, -1)" class="px-2.5 py-1 text-gray-500 hover:bg-gray-100 font-bold transition-colors text-sm">−</button>
                            <span class="text-[13px] font-bold w-7 text-center text-gray-700 bg-gray-50 py-1" x-text="item.qty"></span>
                            <button @click="updateQty(index, 1)" class="px-2.5 py-1 text-gray-500 hover:bg-gray-100 font-bold transition-colors text-sm">+</button>
                        </div>
                        <span class="font-bold text-gray-900 text-[15px]" x-text="'Bs' + (item.price * item.qty).toFixed(2)"></span>
                    </div>
                </div>
            </template>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════
         COLUMNA 3: Método de pago, total y acciones
    ══════════════════════════════════════════════════════ --}}
    <div x-show="mobileView === 'pago'"
         class="flex-1 min-h-0 lg:flex-none flex flex-col overflow-hidden bg-white lg:!flex">

        {{-- Header --}}
        <div class="p-5 pb-4 border-b border-gray-100">
            <h2 class="text-base font-bold text-gray-800">Pago</h2>
        </div>

        {{-- Contenido scrollable --}}
        <div class="flex-1 overflow-y-auto px-5 py-4 space-y-5 custom-scrollbar">

            {{-- Método de pago --}}
            <div>
                <label class="block text-[10px] text-gray-400 font-semibold uppercase tracking-wide mb-2">Método de Pago</label>
                <div class="grid grid-cols-3 gap-2">
                    <button @click="metodoPago = 'efectivo'"
                            :class="metodoPago === 'efectivo'
                                ? 'border-2 border-blue-500 text-blue-600 bg-blue-50'
                                : 'border border-gray-200 text-gray-600 bg-white hover:bg-gray-50'"
                            class="py-2.5 text-[11px] font-bold rounded-xl transition-all">
                        <div class="text-center">
                            <svg class="w-4 h-4 mx-auto mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            Efectivo
                        </div>
                    </button>
                    <button @click="metodoPago = 'tarjeta'"
                            :class="metodoPago === 'tarjeta'
                                ? 'border-2 border-blue-500 text-blue-600 bg-blue-50'
                                : 'border border-gray-200 text-gray-600 bg-white hover:bg-gray-50'"
                            class="py-2.5 text-[11px] font-bold rounded-xl transition-all">
                        <div class="text-center">
                            <svg class="w-4 h-4 mx-auto mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                            </svg>
                            Tarjeta
                        </div>
                    </button>
                    <button @click="metodoPago = 'transferencia'"
                            :class="metodoPago === 'transferencia'
                                ? 'border-2 border-blue-500 text-blue-600 bg-blue-50'
                                : 'border border-gray-200 text-gray-600 bg-white hover:bg-gray-50'"
                            class="py-2.5 text-[11px] font-bold rounded-xl transition-all">
                        <div class="text-center">
                            <svg class="w-4 h-4 mx-auto mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                            </svg>
                            Transfer.
                        </div>
                    </button>
                </div>
            </div>

            {{-- Aviso: cuando se registra un nuevo cliente, sus datos ya son los de la factura --}}
            <div x-show="nuevoCliente" class="bg-blue-50 rounded-xl border border-blue-100 p-3.5">
                <p class="text-[11px] text-blue-700 leading-snug">
                    Se facturará al <span class="font-semibold">nuevo cliente</span> con los datos ingresados arriba (se guardará en clientes).
                </p>
            </div>

            {{-- Datos de factura (receptor) --}}
            <div x-show="!nuevoCliente" class="bg-gray-50 rounded-xl border border-gray-100 p-3.5 space-y-3">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox"
                           x-model="conCreditoFiscal"
                           class="w-4 h-4 rounded text-blue-600 border-gray-300 focus:ring-blue-500">
                    <span class="text-[12px] text-gray-700 font-semibold">Factura con datos (crédito fiscal)</span>
                </label>

                <template x-if="conCreditoFiscal">
                    <div class="space-y-2.5">
                        <div>
                            <label class="block text-[10px] text-gray-400 font-semibold uppercase tracking-wide mb-1">Razón social / Nombre</label>
                            <input type="text" x-model="factura.razon_social"
                                   class="w-full border border-gray-200 rounded-lg py-1.5 px-2.5 text-[13px] text-gray-700 bg-white focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Nombre tal cual va en la factura">
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-[10px] text-gray-400 font-semibold uppercase tracking-wide mb-1">Tipo doc.</label>
                                <select x-model.number="factura.tipo_documento"
                                        class="w-full border border-gray-200 rounded-lg py-1.5 px-2 text-[13px] text-gray-700 bg-white focus:ring-blue-500 focus:border-blue-500">
                                    <template x-for="t in tiposDocumento" :key="t.code">
                                        <option :value="t.code" x-text="t.label"></option>
                                    </template>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] text-gray-400 font-semibold uppercase tracking-wide mb-1">N° documento</label>
                                <input type="text" inputmode="numeric" x-model="factura.numero_documento"
                                       class="w-full border border-gray-200 rounded-lg py-1.5 px-2.5 text-[13px] text-gray-700 bg-white focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="NIT o CI">
                            </div>
                        </div>
                        <div x-show="factura.tipo_documento === 1">
                            <label class="block text-[10px] text-gray-400 font-semibold uppercase tracking-wide mb-1">Complemento (opcional)</label>
                            <input type="text" x-model="factura.complemento" maxlength="5"
                                   class="w-full border border-gray-200 rounded-lg py-1.5 px-2.5 text-[13px] text-gray-700 bg-white focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Ej: 1A">
                        </div>
                    </div>
                </template>

                <p x-show="!conCreditoFiscal" class="text-[11px] text-gray-400 leading-snug">
                    Se emitirá <span class="font-semibold">sin nombre (S/N)</span>. Activá la casilla si el cliente pide factura con su NIT.
                </p>
            </div>

            {{-- Receta --}}
            <div class="flex items-center gap-3 p-3.5 bg-gray-50 rounded-xl border border-gray-100">
                <input type="checkbox"
                       x-model="requiereReceta"
                       class="w-4 h-4 rounded text-blue-600 border-gray-300 focus:ring-blue-500">
                <span class="text-[12px] text-gray-600 font-medium">Venta con receta médica</span>
            </div>

            {{-- Resumen / totales --}}
            <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100">
                <div class="space-y-2 mb-3">
                    <div class="flex justify-between">
                        <span class="text-[12px] text-gray-500">Subtotal</span>
                        <span class="text-[12px] text-gray-700" x-text="'Bs' + total.toFixed(2)"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-[12px] text-gray-500">Items</span>
                        <span class="text-[12px] text-gray-700" x-text="cart.reduce((s,i) => s + i.qty, 0) + ' unidades'"></span>
                    </div>
                </div>
                <div class="border-t border-dashed border-gray-200 pt-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-bold text-gray-700">TOTAL</span>
                        <span class="text-2xl font-extrabold text-gray-900" x-text="'Bs' + total.toFixed(2)"></span>
                    </div>
                </div>
            </div>

            {{-- Método seleccionado badge --}}
            <div class="flex items-center gap-2 text-[11px] text-gray-500">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Pago por: <span class="font-semibold text-gray-700 capitalize" x-text="metodoPago"></span>
            </div>
        </div>

        {{-- Botones fijos al fondo --}}
        <div class="p-5 border-t border-gray-100 space-y-2 bg-white">
            <button @click="procesarVenta()"
                    class="w-full bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-bold py-4 rounded-2xl shadow-lg shadow-blue-100 transition-all text-[14px] flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Procesar Venta
            </button>
            <button @click="cart = []; mostrarImprimir = false; ultimaVenta = null; limpiarReceptor(); resetClienteNuevo(); resetFactura();"
                    class="w-full text-gray-400 hover:text-red-500 font-medium py-2 text-[12px] transition-colors">
                Limpiar Carrito
            </button>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════
         Navegación por pestañas (solo móvil)
    ══════════════════════════════════════════════════════ --}}
    <div class="lg:hidden flex border-t border-gray-200 bg-white shrink-0">
        <button @click="mobileView = 'productos'"
                :class="mobileView === 'productos' ? 'text-blue-600 bg-blue-50' : 'text-gray-500'"
                class="flex-1 py-2.5 flex flex-col items-center gap-0.5 text-[10px] font-bold transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
            </svg>
            Productos
        </button>
        <button @click="mobileView = 'carrito'"
                :class="mobileView === 'carrito' ? 'text-blue-600 bg-blue-50' : 'text-gray-500'"
                class="flex-1 py-2.5 flex flex-col items-center gap-0.5 text-[10px] font-bold transition-colors relative">
            <span class="relative">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <span x-show="cart.length > 0"
                      class="absolute -top-1.5 -right-2 bg-blue-600 text-white text-[9px] font-bold rounded-full min-w-[16px] h-4 px-1 flex items-center justify-center"
                      x-text="cart.length"></span>
            </span>
            Carrito
        </button>
        <button @click="mobileView = 'pago'"
                :class="mobileView === 'pago' ? 'text-blue-600 bg-blue-50' : 'text-gray-500'"
                class="flex-1 py-2.5 flex flex-col items-center gap-0.5 text-[10px] font-bold transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            Pago
        </button>
    </div>

</div>

<script>
    function posSystem() {
        return {
            productos: @json($productos),
            tiposDocumento: @json($tiposDocumento),
            searchQuery: '',
            receptor: null,
            receptorQuery: '',
            receptorResults: [],
            receptorLoading: false,
            receptorOpen: false,
            nuevoCliente: false,
            clienteNuevo: { nombre: '', telefono: '', tipo_documento: 5, numero_documento: '', complemento: '' },
            metodoPago: 'tarjeta',
            requiereReceta: false,
            conCreditoFiscal: false,
            factura: { razon_social: '', tipo_documento: 5, numero_documento: '', complemento: '' },
            cart: [],
            ultimaVenta: null,
            mostrarImprimir: false,
            mobileView: 'productos',
            toast: { show: false, type: 'success', message: '' },
            _toastTimer: null,
            init() {},
            showToast(type, message, duration = 3000) {
                clearTimeout(this._toastTimer);
                this.toast = { show: true, type, message };
                this._toastTimer = setTimeout(() => { this.toast.show = false; }, duration);
            },
            async buscarReceptor() {
                const term = this.receptorQuery.trim();
                if (term.length < 2) {
                    this.receptorResults = [];
                    return;
                }
                this.receptorLoading = true;
                this.receptorOpen = true;
                try {
                    const url = '{{ route("farmacia.pos.buscar-receptor") }}?q=' + encodeURIComponent(term);
                    const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
                    this.receptorResults = res.ok ? await res.json() : [];
                } catch (e) {
                    this.receptorResults = [];
                } finally {
                    this.receptorLoading = false;
                }
            },
            elegirReceptor(r) {
                this.receptor = r;
                this.receptorOpen = false;
                this.receptorQuery = '';
                this.receptorResults = [];
                // Autocompletar datos fiscales para la factura con crédito fiscal
                if (r.numero_documento) {
                    this.conCreditoFiscal = true;
                    this.factura = {
                        razon_social: r.nombre || '',
                        tipo_documento: r.tipo_documento || 5,
                        numero_documento: r.numero_documento || '',
                        complemento: r.complemento || ''
                    };
                } else {
                    this.factura.razon_social = r.nombre || '';
                }
            },
            limpiarReceptor() {
                this.receptor = null;
                this.receptorOpen = false;
                this.receptorQuery = '';
                this.receptorResults = [];
            },
            toggleNuevoCliente() {
                if (this.nuevoCliente) {
                    // Modo nuevo cliente: descarta el receptor buscado para no mezclar.
                    this.limpiarReceptor();
                } else {
                    this.resetClienteNuevo();
                }
            },
            resetClienteNuevo() {
                this.nuevoCliente = false;
                this.clienteNuevo = { nombre: '', telefono: '', tipo_documento: 5, numero_documento: '', complemento: '' };
            },
            get filteredProducts() {
                if (!this.searchQuery) return this.productos;
                const query = this.searchQuery.toLowerCase();
                return this.productos.filter(p =>
                    p.nombre.toLowerCase().includes(query) ||
                    p.codigo_barras.toLowerCase().includes(query) ||
                    p.categoria.toLowerCase().includes(query)
                );
            },
            get total() {
                return this.cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
            },
            addToCart(name, price, id, stock, requiereReceta) {
                if (stock <= 0) return;
                const existing = this.cart.find(i => i.id === id);
                if (existing) {
                    if (existing.qty >= stock) return;
                    existing.qty++;
                } else {
                    this.cart.push({ name, price: parseFloat(price) || 0, qty: 1, id, stock, requiereReceta });
                }
            },
            updateQty(index, amount) {
                const item = this.cart[index];
                const newQty = item.qty + amount;
                if (amount > 0 && newQty > item.stock) return;
                if (newQty <= 0) { this.removeFromCart(index); return; }
                item.qty = newQty;
            },
            removeFromCart(index) {
                this.cart.splice(index, 1);
            },
            async procesarVenta() {
                if (this.cart.length === 0) {
                    this.showToast('error', 'El carrito está vacío.');
                    return;
                }
                const productosConReceta = this.cart.filter(item => item.requiereReceta);
                if (productosConReceta.length > 0 && !this.requiereReceta) {
                    this.showToast('error', 'Hay productos que requieren receta médica. Marque la casilla antes de continuar.');
                    return;
                }
                if (this.conCreditoFiscal && (!this.factura.razon_social.trim() || !this.factura.numero_documento.trim())) {
                    this.showToast('error', 'Para factura con crédito fiscal debe ingresar la razón social y el número de documento.');
                    return;
                }
                if (this.nuevoCliente && (!this.clienteNuevo.nombre.trim() || !this.clienteNuevo.numero_documento.trim())) {
                    this.showToast('error', 'Para registrar un nuevo cliente ingrese el nombre y el número de documento.');
                    return;
                }
                try {
                    const response = await fetch('{{ route("farmacia.pos.procesar") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            items: this.cart.map(item => ({
                                id: item.id,
                                cantidad: item.qty,
                                precio: item.price
                            })),
                            receptor_tipo: this.receptor ? this.receptor.tipo : null,
                            receptor_id: this.receptor ? this.receptor.id : null,
                            metodo_pago: this.metodoPago,
                            requiere_receta: this.requiereReceta,
                            con_credito_fiscal: this.conCreditoFiscal,
                            factura_razon_social: this.factura.razon_social,
                            factura_tipo_documento: this.factura.tipo_documento,
                            factura_numero_documento: this.factura.numero_documento,
                            factura_complemento: this.factura.complemento,
                            nuevo_cliente: this.nuevoCliente,
                            nuevo_cliente_nombre: this.clienteNuevo.nombre,
                            nuevo_cliente_telefono: this.clienteNuevo.telefono,
                            nuevo_cliente_tipo_documento: this.clienteNuevo.tipo_documento,
                            nuevo_cliente_numero_documento: this.clienteNuevo.numero_documento,
                            nuevo_cliente_complemento: this.clienteNuevo.complemento
                        })
                    });

                    const result = await response.json();

                    if (result.success) {
                        this.ultimaVenta = {
                            codigo: result.codigo_venta,
                            total: result.total,
                            items: this.cart,
                            cliente: this.nuevoCliente ? this.clienteNuevo.nombre : (this.receptor ? this.receptor.nombre : 'Cliente General'),
                            metodo_pago: this.metodoPago,
                            fecha: new Date().toLocaleString(),
                            requiere_receta: this.requiereReceta,
                            factura: result.factura
                        };
                        this.showToast('success', 'Venta exitosa · ' + result.codigo_venta + ' · Bs ' + parseFloat(result.total).toFixed(2));
                        this.generarTicketHTML(this.ultimaVenta);
                        this.cart = [];
                        this.limpiarReceptor();
                        this.resetClienteNuevo();
                        this.requiereReceta = false;
                        this.resetFactura();
                        this.mobileView = 'productos';
                    } else {
                        this.showToast('error', result.message);
                    }
                } catch (error) {
                    this.showToast('error', 'Error al procesar la venta: ' + error.message);
                }
            },
            resetFactura() {
                this.conCreditoFiscal = false;
                this.factura = { razon_social: '', tipo_documento: 5, numero_documento: '', complemento: '' };
            },
            generarTicketHTML(venta) {
                const f = venta.factura || {};
                imprimirTicketFarmacia({
                    codigo: venta.codigo,
                    fecha: venta.fecha,
                    cliente: venta.cliente,
                    metodoPago: venta.metodo_pago,
                    requiereReceta: venta.requiere_receta,
                    conCreditoFiscal: !!f.con_credito_fiscal,
                    razonSocial: f.razon_social,
                    docLabel: f.tipo_documento,        // ya viene como etiqueta desde el backend
                    docNumero: f.numero_documento,
                    docComplemento: f.complemento,
                    items: venta.items.map(i => ({ cantidad: i.qty, nombre: i.name, precioUnitario: i.price, descuento: 0, importe: i.price * i.qty })),
                    total: venta.total,
                    reimpresion: false
                });
            }
        }
    }
</script>
@include('farmacia.partials.ticket')
</div>{{-- cierre x-data="posSystem()" --}}
@endsection