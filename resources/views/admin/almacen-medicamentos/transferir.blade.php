@extends('layouts.app')

@section('title', 'Distribución Masiva')

@push('head')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('distribucionMasiva', () => ({
        recibidoPor: '',
        busqueda: '',
        medicamentos: @js($medicamentos->map(fn($m) => [
            'catalogo_id' => $m->id,
            'nombre'      => $m->nombre,
            'unidad'      => $m->unidad_medida,
            'stock'       => (int) $m->stock_central,
        ])),
        seleccionados: [],
        areasActivas: [],

        todasLasAreas: [
            { value: 'emergencia',      label: 'Emergencia' },
            { value: 'cirugia',         label: 'Cirugía' },
            { value: 'hospitalizacion', label: 'Hospitalización' },
            { value: 'uti',             label: 'UTI' },
            { value: 'usi',             label: 'USI' },
            { value: 'neonato',         label: 'Neonato' },
            { value: 'internacion',     label: 'Internación' },
        ],

        get filtrados() {
            if (!this.busqueda) return this.medicamentos;
            const t = this.busqueda.toLowerCase();
            return this.medicamentos.filter(m => m.nombre.toLowerCase().includes(t));
        },

        estaSeleccionado(catalogoId) {
            return this.seleccionados.some(s => s.catalogo_id === catalogoId);
        },

        areaActiva(area) {
            return this.areasActivas.includes(area);
        },

        toggleArea(area) {
            if (this.areaActiva(area)) {
                this.areasActivas = this.areasActivas.filter(a => a !== area);
            } else {
                this.areasActivas.push(area);
                this.seleccionados.forEach(s => {
                    if (s.cantidades[area] === undefined) s.cantidades[area] = 0;
                });
            }
        },

        agregar(catalogoId) {
            if (this.estaSeleccionado(catalogoId)) return;
            const med = this.medicamentos.find(m => m.catalogo_id === catalogoId);
            if (!med) return;
            const cantidades = {};
            this.areasActivas.forEach(a => { cantidades[a] = 0; });
            this.seleccionados.push({
                catalogo_id: med.catalogo_id,
                nombre:      med.nombre,
                unidad:      med.unidad,
                stock:       med.stock,
                cantidades,
            });
        },

        quitar(catalogoId) {
            this.seleccionados = this.seleccionados.filter(s => s.catalogo_id !== catalogoId);
        },

        stockUsado(item) {
            return this.areasActivas.reduce((sum, area) => sum + (parseInt(item.cantidades[area]) || 0), 0);
        },

        stockExcedido(item) {
            return this.stockUsado(item) > item.stock;
        },

        validar(item, area) {
            const val = parseInt(item.cantidades[area]);
            if (isNaN(val) || val < 0) item.cantidades[area] = 0;
        },

        totalPorArea(area) {
            return this.seleccionados.reduce((sum, s) => sum + (parseInt(s.cantidades[area]) || 0), 0);
        },

        get areasConItems() {
            return this.areasActivas.filter(a => this.totalPorArea(a) > 0);
        },

        get hayExcedidos() {
            return this.seleccionados.some(s => this.stockExcedido(s));
        },

        get totalUnidadesGlobal() {
            return this.areasActivas.reduce((sum, a) => sum + this.totalPorArea(a), 0);
        },

        get puedeTransferir() {
            return this.areasActivas.length > 0
                && this.seleccionados.length > 0
                && this.areasConItems.length > 0
                && !this.hayExcedidos;
        },

        enviar() {
            if (!this.puedeTransferir) return;
            const data = {};
            this.areasActivas.forEach(area => {
                const items = this.seleccionados
                    .filter(s => (parseInt(s.cantidades[area]) || 0) > 0)
                    .map(s => ({ catalogo_id: s.catalogo_id, cantidad: parseInt(s.cantidades[area]) }));
                if (items.length > 0) data[area] = items;
            });
            if (Object.keys(data).length === 0) return;
            document.getElementById('input-data').value = JSON.stringify(data);
            document.getElementById('input-recibido-por').value = this.recibidoPor;
            document.getElementById('form-transferir').submit();
        },
    }));
});
</script>
@endpush


@section('content')
<div class="min-h-screen bg-gray-50 p-4 lg:p-6" x-data="distribucionMasiva">

    <!-- Header -->
    <div class="mb-5 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Distribución Masiva</h1>
            <p class="text-gray-500 text-sm mt-0.5">Reparte medicamentos del almacén central a múltiples áreas en una sola operación</p>
        </div>
        <a href="{{ route('admin.almacen-medicamentos.index') }}"
           class="bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 px-3 py-2 rounded-lg flex items-center gap-2 text-sm shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver
        </a>
    </div>

    @if(session('error'))
    <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">{{ session('error') }}</div>
    @endif

    <!-- Paso 1: Seleccionar áreas -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 mb-5">
        <div class="flex items-start gap-4 flex-wrap md:flex-nowrap">
            <div class="flex-1 min-w-0">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">1. Selecciona las áreas destino</p>
                <div class="flex flex-wrap gap-2">
                    <template x-for="area in todasLasAreas" :key="area.value">
                        <button type="button" @click="toggleArea(area.value)"
                                class="px-4 py-1.5 rounded-full text-sm font-medium border transition-all"
                                :class="areaActiva(area.value)
                                    ? 'bg-indigo-600 border-indigo-600 text-white shadow-sm'
                                    : 'bg-white border-gray-300 text-gray-600 hover:border-indigo-400 hover:text-indigo-600'">
                            <span x-text="area.label"></span>
                            <span x-show="areaActiva(area.value) && totalPorArea(area.value) > 0"
                                  class="ml-1.5 bg-indigo-500 text-white text-xs px-1.5 py-0.5 rounded-full"
                                  x-text="totalPorArea(area.value)"></span>
                        </button>
                    </template>
                </div>
            </div>
            <div class="w-full md:w-56">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">Recibido por</p>
                <input type="text" x-model="recibidoPor" maxlength="150"
                       placeholder="Nombre de quien recibe"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 outline-none">
            </div>
        </div>
        <p x-show="areasActivas.length === 0" class="mt-3 text-xs text-amber-600 flex items-center gap-1">
            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
            Selecciona al menos un área para comenzar
        </p>
    </div>

    <!-- Paso 2: Medicamentos + Tabla distribución -->
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-5">

        <!-- Panel izquierdo: catálogo -->
        <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 shadow-sm flex flex-col">
            <div class="px-5 py-3.5 border-b border-gray-100 bg-gray-50 rounded-t-xl">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-0.5">2. Agrega medicamentos</p>
                <div class="flex items-center gap-2">
                    <span class="text-sm font-semibold text-gray-800">Almacén Central</span>
                    <span class="px-2 py-0.5 bg-blue-100 text-blue-700 text-xs rounded-full" x-text="filtrados.length + ' disponibles'"></span>
                </div>
            </div>
            <div class="p-4 flex flex-col gap-3 flex-1">
                <input x-model="busqueda" type="text" placeholder="Buscar por nombre..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-200 focus:border-blue-400 outline-none">
                <div class="space-y-1.5 overflow-y-auto" style="max-height: 460px">
                    <template x-for="med in filtrados" :key="med.catalogo_id">
                        <div class="flex items-center justify-between px-3 py-2.5 border rounded-lg transition-all cursor-default"
                             :class="estaSeleccionado(med.catalogo_id)
                                ? 'bg-green-50 border-green-200'
                                : 'border-gray-200 hover:bg-gray-50'">
                            <div class="flex-1 min-w-0 pr-2">
                                <p class="text-sm font-medium text-gray-900 truncate" x-text="med.nombre"></p>
                                <p class="text-xs text-green-600 font-semibold" x-text="'Stock: ' + med.stock + ' ' + med.unidad"></p>
                            </div>
                            <button type="button" @click.stop="agregar(med.catalogo_id)"
                                    :disabled="estaSeleccionado(med.catalogo_id)"
                                    class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-lg transition-colors"
                                    :class="estaSeleccionado(med.catalogo_id)
                                        ? 'bg-gray-100 text-gray-300 cursor-not-allowed'
                                        : 'bg-green-100 text-green-600 hover:bg-green-200'">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                            </button>
                        </div>
                    </template>
                    <div x-show="filtrados.length === 0" class="text-center py-8 text-gray-400 text-sm">
                        Sin resultados
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel derecho: tabla de distribución -->
        <div class="lg:col-span-3 bg-white rounded-xl border border-gray-200 shadow-sm flex flex-col">
            <div class="px-5 py-3.5 border-b border-gray-100 bg-gray-50 rounded-t-xl flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-0.5">3. Ingresa las cantidades</p>
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-semibold text-gray-800">Tabla de distribución</span>
                        <span x-show="seleccionados.length > 0"
                              class="px-2 py-0.5 bg-indigo-100 text-indigo-700 text-xs rounded-full"
                              x-text="seleccionados.length + ' medicamentos'"></span>
                    </div>
                </div>
                <button x-show="seleccionados.length > 0" type="button" @click="seleccionados = []"
                        class="text-xs text-red-500 hover:text-red-700 font-medium">Limpiar todo</button>
            </div>

            <!-- Estado vacío -->
            <div x-show="seleccionados.length === 0 || areasActivas.length === 0"
                 class="flex-1 flex flex-col items-center justify-center py-16 text-center px-6">
                <svg class="w-12 h-12 text-gray-200 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <p class="text-gray-400 text-sm" x-text="areasActivas.length === 0 ? 'Selecciona áreas arriba para comenzar' : 'Agrega medicamentos desde el panel izquierdo'"></p>
            </div>

            <!-- Tabla -->
            <div x-show="seleccionados.length > 0 && areasActivas.length > 0" class="flex-1 overflow-auto">
                <table class="w-full text-sm border-collapse min-w-max">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="text-left px-4 py-2.5 font-semibold text-gray-600 text-xs sticky left-0 bg-gray-50 z-10 min-w-[180px]">Medicamento</th>
                            <template x-for="area in areasActivas" :key="area">
                                <th class="text-center px-3 py-2.5 font-semibold text-indigo-700 text-xs min-w-[90px]"
                                    x-text="todasLasAreas.find(a => a.value === area)?.label"></th>
                            </template>
                            <th class="text-center px-3 py-2.5 font-semibold text-gray-500 text-xs min-w-[80px]">Usado / Stock</th>
                            <th class="px-3 py-2.5 min-w-[36px]"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="item in seleccionados" :key="item.catalogo_id">
                            <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors"
                                :class="stockExcedido(item) ? 'bg-red-50' : ''">
                                <!-- Nombre -->
                                <td class="px-4 py-2.5 sticky left-0 bg-white z-10"
                                    :class="stockExcedido(item) ? '!bg-red-50' : 'group-hover:bg-gray-50'">
                                    <p class="font-medium text-gray-900 text-xs leading-tight" x-text="item.nombre"></p>
                                    <p class="text-gray-400 text-xs" x-text="item.unidad"></p>
                                </td>
                                <!-- Cantidad por área -->
                                <template x-for="area in areasActivas" :key="area">
                                    <td class="px-3 py-2 text-center">
                                        <input type="number"
                                               x-model.number="item.cantidades[area]"
                                               @change="validar(item, area)"
                                               @wheel.prevent
                                               min="0" :max="item.stock"
                                               class="w-16 px-1.5 py-1 text-center border rounded text-sm focus:ring-2 focus:outline-none transition-colors"
                                               :class="(parseInt(item.cantidades[area]) || 0) > 0
                                                   ? 'border-indigo-400 bg-indigo-50 text-indigo-800 focus:ring-indigo-200'
                                                   : 'border-gray-200 text-gray-500 focus:ring-blue-200 focus:border-blue-400'">
                                    </td>
                                </template>
                                <!-- Indicador usado/stock -->
                                <td class="px-3 py-2 text-center">
                                    <span class="text-xs font-semibold"
                                          :class="stockExcedido(item) ? 'text-red-600' : (stockUsado(item) > 0 ? 'text-green-700' : 'text-gray-400')"
                                          x-text="stockUsado(item) + ' / ' + item.stock"></span>
                                    <div x-show="stockExcedido(item)" class="text-xs text-red-500 leading-tight">excedido</div>
                                </td>
                                <!-- Quitar -->
                                <td class="px-2 py-2 text-center">
                                    <button type="button" @click.stop="quitar(item.catalogo_id)"
                                            class="w-6 h-6 flex items-center justify-center text-gray-300 hover:text-red-500 hover:bg-red-50 rounded transition-colors mx-auto">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                    <!-- Footer totales -->
                    <tfoot>
                        <tr class="bg-gray-50 border-t-2 border-gray-200">
                            <td class="px-4 py-2.5 text-xs font-semibold text-gray-500 sticky left-0 bg-gray-50 z-10">Total por área</td>
                            <template x-for="area in areasActivas" :key="area">
                                <td class="px-3 py-2.5 text-center">
                                    <span class="text-sm font-bold"
                                          :class="totalPorArea(area) > 0 ? 'text-indigo-700' : 'text-gray-300'"
                                          x-text="totalPorArea(area)"></span>
                                </td>
                            </template>
                            <td class="px-3 py-2.5 text-center">
                                <span class="text-sm font-bold text-gray-700" x-text="totalUnidadesGlobal"></span>
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Resumen + Botón -->
    <div class="mt-5 bg-white rounded-xl border border-gray-200 shadow-sm p-5">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <!-- Resumen por área -->
            <div x-show="areasConItems.length > 0" class="flex flex-wrap gap-3">
                <template x-for="area in areasConItems" :key="area">
                    <div class="flex items-center gap-2 bg-indigo-50 border border-indigo-200 rounded-lg px-3 py-1.5">
                        <div class="w-2 h-2 rounded-full bg-indigo-500"></div>
                        <span class="text-xs font-semibold text-indigo-800"
                              x-text="todasLasAreas.find(a => a.value === area)?.label"></span>
                        <span class="text-xs text-indigo-600"
                              x-text="totalPorArea(area) + ' uds'"></span>
                    </div>
                </template>
            </div>
            <div x-show="areasConItems.length === 0" class="text-sm text-gray-400">
                Ingresa cantidades en la tabla para continuar
            </div>

            <div class="flex items-center gap-3">
                <!-- Alerta de excedido -->
                <span x-show="hayExcedidos" class="text-xs text-red-600 font-medium flex items-center gap-1">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                    Stock excedido en uno o más ítems
                </span>

                <button type="button" @click="enviar()" :disabled="!puedeTransferir"
                        class="px-6 py-2.5 rounded-lg font-semibold text-sm flex items-center gap-2 transition-all"
                        :class="puedeTransferir
                            ? 'bg-green-600 text-white hover:bg-green-700 shadow-sm hover:shadow'
                            : 'bg-gray-200 text-gray-400 cursor-not-allowed'">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                    </svg>
                    <span x-text="areasConItems.length > 1
                        ? 'Distribuir a ' + areasConItems.length + ' áreas'
                        : 'Realizar transferencia'"></span>
                </button>
            </div>
        </div>
    </div>

</div>

<form id="form-transferir" method="POST" action="{{ route('admin.almacen-medicamentos.transferir.procesar') }}" style="display:none">
    @csrf
    <input type="hidden" id="input-recibido-por" name="recibido_por">
    <input type="hidden" id="input-data" name="data">
</form>
@endsection
