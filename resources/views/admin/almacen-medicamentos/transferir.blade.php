@extends('layouts.app')
 
@section('title', 'Agregar a Área - Transferir Medicamentos')
 
@section('content')
<div class="min-h-screen bg-gray-50 p-6"
     x-data="{
        areaDestino: '',
        recibidoPor: '',
        busqueda: '',
        medicamentos: @js($medicamentos),
        seleccionados: [],
 
        get filtrados() {
            if (!this.busqueda) return this.medicamentos;
            const term = this.busqueda.toLowerCase();
            return this.medicamentos.filter(m =>
                m.nombre.toLowerCase().includes(term) ||
                (m.descripcion && m.descripcion.toLowerCase().includes(term)) ||
                (m.lote && m.lote.toLowerCase().includes(term))
            );
        },
 
        agregar(med) {
            console.log('Agregando medicamento:', med);
            console.log('ID del medicamento:', med.id, 'Tipo:', typeof med.id);
            console.log('Seleccionados actuales:', this.seleccionados);
            
            const yaExiste = this.seleccionados.some(s => Number(s.id) === Number(med.id));
            console.log('¿Ya existe?', yaExiste);
            
            if (!yaExiste) {
                const nuevoItem = {
                    id: med.id,
                    nombre: med.nombre,
                    descripcion: med.descripcion,
                    lote: med.lote,
                    stock: med.cantidad,
                    unidad: med.unidad_medida,
                    cantidad: 1
                };
                console.log('Insertando:', nuevoItem);
                this.seleccionados.push(nuevoItem);
                console.log('Seleccionados después:', this.seleccionados);
            } else {
                console.log('El medicamento ya está en la lista');
            }
        },
 
        quitar(id) {
            this.seleccionados = this.seleccionados.filter(s => s.id !== id);
        },
 
        validarCantidad(item) {
            if (item.cantidad < 1) item.cantidad = 1;
            if (item.cantidad > item.stock) item.cantidad = item.stock;
        },
 
        get puedeTransferir() {
            return this.areaDestino && this.seleccionados.length > 0 && this.seleccionados.every(s => s.cantidad > 0);
        },
 
        get totalItems() {
            return this.seleccionados.length;
        },
 
        get totalUnidades() {
            return this.seleccionados.reduce((sum, s) => sum + parseInt(s.cantidad || 0), 0);
        }
     }">
 
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Agregar a Área</h1>
                <p class="text-gray-600 mt-1">Transfiere medicamentos del almacén central a un área específica</p>
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
 
    <!-- Configuración de Transferencia -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            Configuración de Transferencia
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="area_destino" class="block text-sm font-medium text-gray-700 mb-1">
                    Área Destino <span class="text-red-500">*</span>
                </label>
                <select x-model="areaDestino" id="area_destino"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        required>
                    <option value="">Seleccionar área destino</option>
                    <option value="emergencia">Emergencia</option>
                    <option value="cirugia">Cirugía</option>
                    <option value="hospitalizacion">Hospitalización</option>
                    <option value="uti">UTI</option>
                    <option value="usi">USI</option>
                    <option value="neonato">Neonato</option>
                    <option value="internacion">Internación</option>
                </select>
            </div>
            <div>
                <label for="recibido_por" class="block text-sm font-medium text-gray-700 mb-1">
                    Recibido por (nombre)
                </label>
                <input x-model="recibidoPor" type="text" id="recibido_por"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="Nombre de quien recibe físicamente"
                       maxlength="150">
            </div>
        </div>
    </div>
 
    <!-- Paneles de Selección -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Panel Izquierdo: Medicamentos Disponibles -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                    Medicamentos en Almacén Central
                    <span class="ml-2 px-2 py-0.5 bg-blue-100 text-blue-800 text-xs rounded-full" x-text="filtrados.length + ' items'"></span>
                </h3>
            </div>
            <div class="p-4">
                <!-- Búsqueda -->
                <div class="relative mb-4">
                    <input x-model="busqueda" type="text"
                           placeholder="Buscar medicamento por nombre, descripción o lote..."
                           class="w-full px-4 py-2 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <svg class="w-5 h-5 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
 
                <!-- Lista de Medicamentos -->
                <div class="space-y-2 max-h-[500px] overflow-y-auto">
                    <template x-for="med in filtrados" :key="med.id">
                        <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors"
                             :class="{ 'bg-green-50 border-green-200': seleccionados.find(s => s.id === med.id) }">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate" x-text="med.nombre"></p>
                                <p class="text-xs text-gray-500 truncate" x-text="med.descripcion || 'Sin descripción'"></p>
                                <div class="flex items-center gap-3 mt-1">
                                    <span class="text-xs font-semibold"
                                          :class="med.cantidad === 0 ? 'text-red-600' : (med.cantidad <= med.stock_minimo ? 'text-yellow-600' : 'text-green-600')"
                                          x-text="'Stock: ' + med.cantidad + ' ' + med.unidad_medida"></span>
                                    <template x-if="med.lote">
                                        <span class="text-xs text-gray-400" x-text="'Lote: ' + med.lote"></span>
                                    </template>
                                </div>
                            </div>
                            <button type="button"
                                    @click.prevent="agregar(med)"
                                    :disabled="med.cantidad === 0 || seleccionados.some(s => Number(s.id) === Number(med.id))"
                                    class="ml-2 p-2 rounded-lg transition-colors flex-shrink-0"
                                    :class="(med.cantidad === 0 || seleccionados.some(s => Number(s.id) === Number(med.id))) ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : 'bg-green-100 text-green-600 hover:bg-green-200'">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                            </button>
                        </div>
                    </template>
 
                    <div x-show="filtrados.length === 0" class="text-center py-8">
                        <svg class="w-12 h-12 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                        <p class="text-gray-500 text-sm">No se encontraron medicamentos</p>
                    </div>
                </div>
            </div>
        </div>
 
        <!-- Panel Derecho: Medicamentos Seleccionados -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    Medicamentos a Transferir
                    <span class="ml-2 px-2 py-0.5 bg-indigo-100 text-indigo-800 text-xs rounded-full" x-show="seleccionados.length > 0" x-text="seleccionados.length + ' items'"></span>
                </h3>
            </div>
            <div class="p-4">
                <!-- Lista Vacía -->
                <div x-show="seleccionados.length === 0" class="text-center py-12">
                    <svg class="w-16 h-16 text-gray-200 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                    </svg>
                    <p class="text-gray-500 font-medium">No hay medicamentos seleccionados</p>
                    <p class="text-gray-400 text-sm mt-1">Haga clic en el botón + para agregar medicamentos</p>
                </div>
 
                <!-- Lista de Seleccionados -->
                <div x-show="seleccionados.length > 0" class="space-y-3">
                    <template x-for="(item, index) in seleccionados" :key="item.id">
                        <div class="flex items-center gap-3 p-3 border border-indigo-200 rounded-lg bg-indigo-50">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate" x-text="item.nombre"></p>
                                <p class="text-xs text-gray-500" x-text="'Disponible: ' + item.stock + ' ' + item.unidad"></p>
                            </div>
 
                            <!-- Input de Cantidad -->
                            <div class="flex items-center gap-2">
                                <span class="text-sm text-gray-600">x</span>
                                <input type="number" x-model.number="item.cantidad" @change="validarCantidad(item)"
                                       class="w-20 px-2 py-1 text-center border border-gray-300 rounded focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                       min="1" :max="item.stock" required>
                                <span class="text-sm text-gray-500" x-text="item.unidad"></span>
                            </div>
 
                            <!-- Botón Quitar -->
                            <button @click="quitar(item.id)"
                                    class="p-2 text-red-500 hover:bg-red-100 rounded-lg transition-colors flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </template>
 
                    <!-- Resumen -->
                    <div class="mt-4 p-4 bg-blue-50 rounded-lg border border-blue-200">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-sm text-gray-600">Total de ítems: <span class="font-semibold text-gray-900" x-text="totalItems"></span></p>
                                <p class="text-sm text-gray-600">Total de unidades: <span class="font-semibold text-gray-900" x-text="totalUnidades"></span></p>
                            </div>
                            <button @click="seleccionados = []"
                                    class="text-sm text-red-600 hover:text-red-800 font-medium">
                                Limpiar todo
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
 
    <!-- Botón de Transferencia -->
    <div class="mt-6 bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <form method="POST" action="{{ route('admin.almacen-medicamentos.transferir.procesar') }}"
              x-ref="transferForm"
              @submit.prevent="
                  const items = JSON.stringify(seleccionados.map(s => ({ id: s.id, cantidad: s.cantidad })));
                  $refs.transferForm.querySelector('input[name=items]').value = items;
                  $refs.transferForm.querySelector('input[name=recibido_por]').value = recibidoPor;
                  $refs.transferForm.querySelector('input[name=area_destino]').value = areaDestino;
                  $refs.transferForm.submit();
              ">
            @csrf
            <input type="hidden" name="area_destino" value="">
            <input type="hidden" name="recibido_por" value="">
            <input type="hidden" name="items" value="">
 
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">
                        Área destino: <span class="font-semibold text-gray-900" x-text="areaDestino ? areaDestino.charAt(0).toUpperCase() + areaDestino.slice(1) : 'No seleccionada'"></span>
                    </p>
                    <p class="text-sm text-gray-600">
                        Medicamentos a transferir: <span class="font-semibold text-gray-900" x-text="seleccionados.length"></span>
                    </p>
                </div>
                <button type="submit"
                        :disabled="!puedeTransferir"
                        class="px-6 py-3 rounded-lg font-medium flex items-center gap-2 transition-colors"
                        :class="puedeTransferir ? 'bg-green-600 text-white hover:bg-green-700' : 'bg-gray-300 text-gray-500 cursor-not-allowed'">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                    </svg>
                    Realizar Transferencia
                </button>
            </div>
 
            <div x-show="!areaDestino && seleccionados.length > 0" class="mt-3 text-sm text-yellow-600">
                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                Seleccione un área destino para continuar
            </div>
        </form>
    </div>
</div>
@endsection