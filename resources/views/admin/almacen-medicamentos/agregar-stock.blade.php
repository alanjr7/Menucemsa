@extends('layouts.app')

@section('title', 'Agregar Stock al Almacén Central')

@push('head')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('agregarStock', () => ({
        motivo: '',
        busqueda: '',
        filtroEstado: '',
        filtroTipo: '',
        cantidades: {},
        pagina: 1,
        porPagina: 25,

        medicamentos: @js($medicamentos),

        get filtrados() {
            return this.medicamentos.filter(m => {
                if (this.busqueda && !m.nombre.toLowerCase().includes(this.busqueda.toLowerCase())) return false;
                if (this.filtroTipo && m.tipo !== this.filtroTipo) return false;
                if (this.filtroEstado) {
                    const estado = this.estadoItem(m);
                    if (this.filtroEstado !== estado) return false;
                }
                return true;
            });
        },

        get totalPaginas() {
            return Math.max(1, Math.ceil(this.filtrados.length / this.porPagina));
        },

        get paginados() {
            const inicio = (this.pagina - 1) * this.porPagina;
            return this.filtrados.slice(inicio, inicio + this.porPagina);
        },

        irPagina(n) {
            this.pagina = Math.min(Math.max(1, n), this.totalPaginas);
        },

        resetPagina() {
            this.pagina = 1;
        },

        estadoItem(m) {
            if (m.stock <= 0) return 'agotado';
            if (m.stock_minimo > 0 && m.stock <= m.stock_minimo) return 'bajo';
            return 'normal';
        },

        getCantidad(catalogoId) {
            return this.cantidades[catalogoId] ?? '';
        },

        setCantidad(catalogoId, val) {
            const n = parseInt(val);
            if (!val || isNaN(n) || n <= 0) {
                delete this.cantidades[catalogoId];
            } else {
                this.cantidades[catalogoId] = n;
            }
        },

        nuevoStock(m) {
            const c = parseInt(this.cantidades[m.catalogo_id]) || 0;
            return m.stock + c;
        },

        get itemsConCantidad() {
            return this.medicamentos.filter(m => (parseInt(this.cantidades[m.catalogo_id]) || 0) > 0);
        },

        get totalUnidades() {
            return Object.values(this.cantidades).reduce((sum, c) => sum + (parseInt(c) || 0), 0);
        },

        get puedeGuardar() {
            return this.motivo.trim().length > 0 && this.itemsConCantidad.length > 0;
        },

        limpiarCantidades() {
            this.cantidades = {};
        },

        enviar() {
            if (!this.puedeGuardar) return;
            const items = this.itemsConCantidad.map(m => ({
                catalogo_id: m.catalogo_id,
                cantidad: parseInt(this.cantidades[m.catalogo_id]),
            }));
            document.getElementById('input-items').value = JSON.stringify(items);
            document.getElementById('input-motivo').value = this.motivo;
            document.getElementById('form-ingreso').submit();
        },
    }));
});
</script>
@endpush


@section('content')
<div class="min-h-screen bg-gray-50" x-data="agregarStock">

    <!-- Header fijo -->
    <div class="bg-white border-b border-gray-200 px-6 py-4 sticky top-0 z-20 shadow-sm">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.almacen-medicamentos.index') }}"
                   class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <div>
                    <h1 class="text-xl font-bold text-gray-900">Agregar Stock — Almacén Central</h1>
                    <p class="text-xs text-gray-400 mt-0.5">Ingresa las cantidades a sumar para cada ítem</p>
                </div>
            </div>
            <!-- Contador flotante -->
            <div x-show="itemsConCantidad.length > 0"
                 class="flex items-center gap-4">
                <div class="text-right">
                    <p class="text-xs text-gray-500">Ítems modificados</p>
                    <p class="text-lg font-bold text-amber-600" x-text="itemsConCantidad.length"></p>
                </div>
                <div class="text-right">
                    <p class="text-xs text-gray-500">Unidades a ingresar</p>
                    <p class="text-lg font-bold text-gray-800" x-text="totalUnidades"></p>
                </div>
            </div>
        </div>
    </div>

    <div class="p-6 space-y-5 pb-32">

        @if(session('error'))
        <div class="p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm">{{ session('error') }}</div>
        @endif

        <!-- Motivo (requerido) -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                Motivo del ingreso
                <span class="text-red-500">*</span>
                <span class="ml-1 font-normal text-gray-400 text-xs">(requerido para registrar el ajuste)</span>
            </label>
            <input type="text" x-model="motivo" maxlength="255"
                   placeholder="Ej: Compra mensual, Donación, Ajuste de inventario..."
                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-amber-300 focus:border-amber-400 outline-none transition-colors"
                   :class="motivo.trim() ? 'border-green-400 bg-green-50' : ''">
            <p x-show="!motivo.trim() && itemsConCantidad.length > 0"
               class="mt-1.5 text-xs text-red-500">Ingresa el motivo para poder guardar.</p>
        </div>

        <!-- Filtros -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm px-5 py-4">
            <div class="flex flex-wrap gap-3 items-end">
                <div class="flex-1 min-w-48">
                    <label class="block text-xs font-medium text-gray-500 mb-1">Buscar</label>
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input x-model="busqueda" @input="resetPagina()" type="text" placeholder="Nombre del medicamento..."
                               class="w-full pl-9 pr-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-amber-200 focus:border-amber-400 outline-none">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Tipo</label>
                    <select x-model="filtroTipo" @change="resetPagina()" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-200">
                        <option value="">Todos</option>
                        <option value="medicamento">Medicamento</option>
                        <option value="insumo">Insumo</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Estado</label>
                    <select x-model="filtroEstado" @change="resetPagina()" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-amber-200">
                        <option value="">Todos</option>
                        <option value="agotado">Agotados</option>
                        <option value="bajo">Bajo stock</option>
                        <option value="normal">Normal</option>
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="button" @click="busqueda = ''; filtroTipo = ''; filtroEstado = ''; resetPagina()"
                            class="px-3 py-2 text-sm text-gray-500 hover:text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50">
                        Limpiar filtros
                    </button>
                    <button x-show="itemsConCantidad.length > 0" type="button" @click="limpiarCantidades()"
                            class="px-3 py-2 text-sm text-red-500 hover:text-red-700 border border-red-200 rounded-lg hover:bg-red-50">
                        Limpiar cantidades
                    </button>
                </div>
                <div class="ml-auto">
                    <span class="text-xs text-gray-400"
                          x-text="filtrados.length + ' de {{ $medicamentos->count() }} ítems'"></span>
                </div>
            </div>
        </div>

        <!-- Tabla -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Medicamento / Insumo</th>
                        <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide w-28">Stock actual</th>
                        <th class="text-center px-4 py-3 text-xs font-semibold text-amber-600 uppercase tracking-wide w-36">Agregar</th>
                        <th class="text-center px-4 py-3 text-xs font-semibold text-green-600 uppercase tracking-wide w-28">Nuevo total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <template x-for="m in paginados" :key="m.catalogo_id">
                        <tr class="hover:bg-gray-50 transition-colors"
                            :class="{
                                'bg-red-50 hover:bg-red-100': estadoItem(m) === 'agotado',
                                'bg-yellow-50 hover:bg-yellow-100': estadoItem(m) === 'bajo',
                            }">

                            <!-- Nombre -->
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-2">
                                    <div>
                                        <p class="font-medium text-gray-900 text-sm" x-text="m.nombre"></p>
                                        <p x-show="m.descripcion" class="text-xs text-gray-500 mt-0.5 leading-snug" x-text="m.descripcion"></p>
                                        <div class="flex items-center gap-2 mt-0.5">
                                            <span class="text-xs text-gray-400" x-text="m.unidad"></span>
                                            <span class="text-xs px-1.5 py-0.5 rounded-full font-medium"
                                                  :class="m.tipo === 'medicamento' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600'"
                                                  x-text="m.tipo === 'medicamento' ? 'Med.' : 'Ins.'"></span>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Stock actual -->
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold"
                                      :class="{
                                          'bg-red-100 text-red-700':    estadoItem(m) === 'agotado',
                                          'bg-yellow-100 text-yellow-700': estadoItem(m) === 'bajo',
                                          'bg-green-100 text-green-700':   estadoItem(m) === 'normal',
                                      }"
                                      x-text="m.stock + ' ' + m.unidad"></span>
                                <p x-show="estadoItem(m) === 'agotado'" class="text-xs text-red-500 mt-0.5">Agotado</p>
                                <p x-show="estadoItem(m) === 'bajo'" class="text-xs text-yellow-600 mt-0.5">Bajo stock</p>
                            </td>

                            <!-- Input cantidad -->
                            <td class="px-4 py-3 text-center">
                                <input type="number"
                                       :value="getCantidad(m.catalogo_id)"
                                       @input="setCantidad(m.catalogo_id, $event.target.value)"
                                       @wheel.prevent
                                       min="1"
                                       placeholder="0"
                                       class="w-24 px-3 py-1.5 text-center border rounded-lg text-sm font-medium transition-all focus:outline-none focus:ring-2"
                                       :class="(parseInt(cantidades[m.catalogo_id]) || 0) > 0
                                           ? 'border-amber-400 bg-amber-50 text-amber-800 focus:ring-amber-200'
                                           : 'border-gray-200 text-gray-500 focus:ring-amber-100 focus:border-amber-300 hover:border-gray-300'">
                            </td>

                            <!-- Nuevo total (preview) -->
                            <td class="px-4 py-3 text-center">
                                <template x-if="(parseInt(cantidades[m.catalogo_id]) || 0) > 0">
                                    <div>
                                        <span class="text-sm font-bold text-green-700" x-text="nuevoStock(m)"></span>
                                        <span class="text-xs text-green-500 ml-1" x-text="m.unidad"></span>
                                        <p class="text-xs text-green-500 mt-0.5">
                                            +<span x-text="cantidades[m.catalogo_id]"></span>
                                        </p>
                                    </div>
                                </template>
                                <template x-if="(parseInt(cantidades[m.catalogo_id]) || 0) <= 0">
                                    <span class="text-gray-300 text-xs">—</span>
                                </template>
                            </td>
                        </tr>
                    </template>

                    <!-- Sin resultados -->
                    <tr x-show="filtrados.length === 0">
                        <td colspan="4" class="px-5 py-12 text-center text-gray-400 text-sm">
                            Sin ítems que coincidan con los filtros.
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Paginación -->
            <div x-show="totalPaginas > 1"
                 class="flex items-center justify-between px-5 py-3 border-t border-gray-100 bg-gray-50">
                <span class="text-xs text-gray-500"
                      x-text="'Página ' + pagina + ' de ' + totalPaginas + ' — ' + filtrados.length + ' ítems'"></span>
                <div class="flex items-center gap-1">
                    <button type="button" @click="irPagina(1)" :disabled="pagina === 1"
                            class="px-2 py-1 text-xs rounded border transition-colors"
                            :class="pagina === 1 ? 'border-gray-200 text-gray-300 cursor-not-allowed' : 'border-gray-300 text-gray-600 hover:bg-gray-100'">
                        «
                    </button>
                    <button type="button" @click="irPagina(pagina - 1)" :disabled="pagina === 1"
                            class="px-2.5 py-1 text-xs rounded border transition-colors"
                            :class="pagina === 1 ? 'border-gray-200 text-gray-300 cursor-not-allowed' : 'border-gray-300 text-gray-600 hover:bg-gray-100'">
                        ‹ Anterior
                    </button>
                    <template x-for="n in totalPaginas" :key="n">
                        <button x-show="n === 1 || n === totalPaginas || Math.abs(n - pagina) <= 1"
                                type="button" @click="irPagina(n)"
                                class="w-7 h-7 text-xs rounded border transition-colors font-medium"
                                :class="n === pagina
                                    ? 'bg-amber-500 border-amber-500 text-white'
                                    : 'border-gray-300 text-gray-600 hover:bg-gray-100'">
                            <span x-text="n"></span>
                        </button>
                    </template>
                    <button type="button" @click="irPagina(pagina + 1)" :disabled="pagina === totalPaginas"
                            class="px-2.5 py-1 text-xs rounded border transition-colors"
                            :class="pagina === totalPaginas ? 'border-gray-200 text-gray-300 cursor-not-allowed' : 'border-gray-300 text-gray-600 hover:bg-gray-100'">
                        Siguiente ›
                    </button>
                    <button type="button" @click="irPagina(totalPaginas)" :disabled="pagina === totalPaginas"
                            class="px-2 py-1 text-xs rounded border transition-colors"
                            :class="pagina === totalPaginas ? 'border-gray-200 text-gray-300 cursor-not-allowed' : 'border-gray-300 text-gray-600 hover:bg-gray-100'">
                        »
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Barra de acción fija en el fondo -->
    <div class="fixed bottom-0 left-0 right-0 z-30 bg-white border-t border-gray-200 shadow-lg px-6 py-4">
        <div class="max-w-screen-xl mx-auto flex items-center justify-between gap-4">

            <!-- Resumen de lo que se va a guardar -->
            <div x-show="itemsConCantidad.length === 0" class="text-sm text-gray-400">
                Ingresa cantidades en la tabla para continuar.
            </div>
            <div x-show="itemsConCantidad.length > 0" class="flex flex-wrap gap-2 flex-1">
                <template x-for="m in itemsConCantidad.slice(0, 6)" :key="m.catalogo_id">
                    <div class="flex items-center gap-1.5 bg-amber-50 border border-amber-200 rounded-lg px-2.5 py-1 text-xs">
                        <span class="font-medium text-amber-800 truncate max-w-32" x-text="m.nombre"></span>
                        <span class="text-amber-500 font-bold">+<span x-text="cantidades[m.catalogo_id]"></span></span>
                    </div>
                </template>
                <div x-show="itemsConCantidad.length > 6"
                     class="flex items-center px-2.5 py-1 bg-gray-100 text-gray-600 text-xs rounded-lg font-medium"
                     x-text="'+' + (itemsConCantidad.length - 6) + ' más'"></div>
            </div>

            <!-- Botón guardar -->
            <div class="flex items-center gap-3 flex-shrink-0">
                <div x-show="!motivo.trim() && itemsConCantidad.length > 0"
                     class="text-xs text-red-500 font-medium text-right max-w-36">
                    Falta el motivo
                </div>
                <button type="button" @click="enviar()" :disabled="!puedeGuardar"
                        class="px-6 py-2.5 rounded-lg font-semibold text-sm flex items-center gap-2 transition-all"
                        :class="puedeGuardar
                            ? 'bg-amber-500 text-white hover:bg-amber-600 shadow-sm hover:shadow'
                            : 'bg-gray-200 text-gray-400 cursor-not-allowed'">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                    </svg>
                    <span x-text="itemsConCantidad.length > 0
                        ? 'Guardar ingreso (' + itemsConCantidad.length + ' ítems, ' + totalUnidades + ' uds)'
                        : 'Guardar ingreso'"></span>
                </button>
            </div>
        </div>
    </div>

</div>

<form id="form-ingreso" method="POST" action="{{ route('admin.almacen-medicamentos.agregar-stock.procesar') }}" style="display:none">
    @csrf
    <input type="hidden" id="input-motivo" name="motivo">
    <input type="hidden" id="input-items" name="items">
</form>
@endsection
