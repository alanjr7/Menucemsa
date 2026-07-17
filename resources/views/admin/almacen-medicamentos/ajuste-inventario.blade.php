@extends('layouts.app')

@section('title', 'Ajuste de Inventario — Almacén Central')

@push('head')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('ajusteInventario', () => ({
        motivo: '',
        busqueda: '',
        filtroEstado: '',
        filtroTipo: '',
        contados: {},
        pagina: 1,
        porPagina: 25,

        stocks: @js($stocks),

        get filtrados() {
            return this.stocks.filter(s => {
                if (this.busqueda) {
                    const q = this.busqueda.toLowerCase();
                    if (!s.nombre.toLowerCase().includes(q) && !(s.lote || '').toLowerCase().includes(q)) return false;
                }
                if (this.filtroTipo && s.tipo !== this.filtroTipo) return false;
                if (this.filtroEstado && this.estadoItem(s) !== this.filtroEstado) return false;
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

        estadoItem(s) {
            if (s.stock <= 0) return 'agotado';
            if (s.stock_minimo > 0 && s.stock <= s.stock_minimo) return 'bajo';
            return 'normal';
        },

        getContado(stockId) {
            return (stockId in this.contados) ? this.contados[stockId] : '';
        },

        setContado(stockId, val) {
            if (val === '' || val === null) {
                delete this.contados[stockId];
                return;
            }
            const n = parseInt(val);
            if (isNaN(n) || n < 0) {
                delete this.contados[stockId];
            } else {
                this.contados[stockId] = n;
            }
        },

        modificado(s) {
            return (s.stock_id in this.contados) && this.contados[s.stock_id] !== s.stock;
        },

        delta(s) {
            if (!this.modificado(s)) return 0;
            return this.contados[s.stock_id] - s.stock;
        },

        nuevoStock(s) {
            return (s.stock_id in this.contados) ? this.contados[s.stock_id] : s.stock;
        },

        get itemsModificados() {
            return this.stocks.filter(s => this.modificado(s));
        },

        get totalDelta() {
            return this.itemsModificados.reduce((sum, s) => sum + this.delta(s), 0);
        },

        get puedeGuardar() {
            return this.motivo.trim().length > 0 && this.itemsModificados.length > 0;
        },

        limpiarConteos() {
            this.contados = {};
        },

        enviar() {
            if (!this.puedeGuardar) return;
            const items = this.itemsModificados.map(s => ({
                stock_id: s.stock_id,
                contado: this.contados[s.stock_id],
            }));
            document.getElementById('input-items').value = JSON.stringify(items);
            document.getElementById('input-motivo').value = this.motivo;
            document.getElementById('form-ajuste').submit();
        },
    }));
});
</script>
@endpush


@section('content')
<div class="min-h-screen bg-gray-50" x-data="ajusteInventario">

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
                    <h1 class="text-xl font-bold text-gray-900">Ajuste de Inventario — Almacén Central</h1>
                    <p class="text-xs text-gray-400 mt-0.5">Conteo físico por lote: escribe la cantidad real en estante. No crea lotes.</p>
                </div>
            </div>
            <!-- Contador flotante -->
            <div x-show="itemsModificados.length > 0" class="flex items-center gap-4">
                <div class="text-right">
                    <p class="text-xs text-gray-500">Lotes ajustados</p>
                    <p class="text-lg font-bold text-amber-600" x-text="itemsModificados.length"></p>
                </div>
                <div class="text-right">
                    <p class="text-xs text-gray-500">Diferencia neta</p>
                    <p class="text-lg font-bold"
                       :class="totalDelta > 0 ? 'text-green-600' : (totalDelta < 0 ? 'text-red-600' : 'text-gray-800')"
                       x-text="(totalDelta > 0 ? '+' : '') + totalDelta"></p>
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
                Motivo del ajuste
                <span class="text-red-500">*</span>
                <span class="ml-1 font-normal text-gray-400 text-xs">(requerido — queda registrado en la auditoría)</span>
            </label>
            <input type="text" x-model="motivo" maxlength="255"
                   placeholder="Ej: Conteo físico mensual, Merma por rotura, Corrección de inventario..."
                   class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-amber-300 focus:border-amber-400 outline-none transition-colors"
                   :class="motivo.trim() ? 'border-green-400 bg-green-50' : ''">
            <p x-show="!motivo.trim() && itemsModificados.length > 0"
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
                        <input x-model="busqueda" @input="resetPagina()" type="text" placeholder="Nombre o código de lote..."
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
                    <button x-show="itemsModificados.length > 0" type="button" @click="limpiarConteos()"
                            class="px-3 py-2 text-sm text-red-500 hover:text-red-700 border border-red-200 rounded-lg hover:bg-red-50">
                        Limpiar conteos
                    </button>
                </div>
                <div class="ml-auto">
                    <span class="text-xs text-gray-400"
                          x-text="filtrados.length + ' de {{ $stocks->count() }} lotes'"></span>
                </div>
            </div>
        </div>

        <!-- Tabla -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Medicamento / Insumo · Lote</th>
                        <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide w-28">Sistema</th>
                        <th class="text-center px-4 py-3 text-xs font-semibold text-amber-600 uppercase tracking-wide w-36">Contado</th>
                        <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide w-32">Diferencia</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <template x-for="s in paginados" :key="s.stock_id">
                        <tr class="hover:bg-gray-50 transition-colors"
                            :class="{
                                'bg-red-50 hover:bg-red-100': estadoItem(s) === 'agotado',
                                'bg-yellow-50 hover:bg-yellow-100': estadoItem(s) === 'bajo',
                            }">

                            <!-- Nombre + lote -->
                            <td class="px-5 py-3">
                                <p class="font-medium text-gray-900 text-sm" x-text="s.nombre"></p>
                                <p x-show="s.descripcion" class="text-xs text-gray-500 mt-0.5 leading-snug" x-text="s.descripcion"></p>
                                <div class="flex flex-wrap items-center gap-2 mt-1">
                                    <span class="text-xs px-1.5 py-0.5 rounded-full font-medium"
                                          :class="s.tipo === 'medicamento' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600'"
                                          x-text="s.tipo === 'medicamento' ? 'Med.' : 'Ins.'"></span>
                                    <span class="text-xs px-1.5 py-0.5 rounded bg-blue-50 text-blue-700 font-medium" x-text="s.lote"></span>
                                    <span x-show="s.proveedor" class="text-xs text-gray-400" x-text="s.proveedor"></span>
                                    <span x-show="s.vencimiento" class="text-xs"
                                          :class="{
                                              'text-red-600 font-medium': s.estado_venc === 'vencido',
                                              'text-amber-600': s.estado_venc === 'por_vencer',
                                              'text-gray-400': s.estado_venc === 'vigente' || s.estado_venc === 'sin_fecha',
                                          }">
                                        Vence <span x-text="s.vencimiento"></span>
                                    </span>
                                </div>
                            </td>

                            <!-- Stock de sistema -->
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold"
                                      :class="{
                                          'bg-red-100 text-red-700':       estadoItem(s) === 'agotado',
                                          'bg-yellow-100 text-yellow-700': estadoItem(s) === 'bajo',
                                          'bg-green-100 text-green-700':    estadoItem(s) === 'normal',
                                      }"
                                      x-text="s.stock + ' ' + s.unidad"></span>
                            </td>

                            <!-- Input conteo real -->
                            <td class="px-4 py-3 text-center">
                                <input type="number"
                                       :value="getContado(s.stock_id)"
                                       @input="setContado(s.stock_id, $event.target.value)"
                                       @wheel.prevent
                                       min="0"
                                       :placeholder="s.stock"
                                       class="w-24 px-3 py-1.5 text-center border rounded-lg text-sm font-medium transition-all focus:outline-none focus:ring-2"
                                       :class="modificado(s)
                                           ? (delta(s) > 0 ? 'border-green-400 bg-green-50 text-green-800 focus:ring-green-200'
                                                           : 'border-red-400 bg-red-50 text-red-800 focus:ring-red-200')
                                           : 'border-gray-200 text-gray-500 focus:ring-amber-100 focus:border-amber-300 hover:border-gray-300'">
                            </td>

                            <!-- Diferencia (preview) -->
                            <td class="px-4 py-3 text-center">
                                <template x-if="modificado(s)">
                                    <div>
                                        <span class="text-sm font-bold"
                                              :class="delta(s) > 0 ? 'text-green-700' : 'text-red-700'"
                                              x-text="(delta(s) > 0 ? '+' : '') + delta(s)"></span>
                                        <p class="text-xs text-gray-400 mt-0.5">
                                            <span x-text="s.stock"></span> → <span class="font-semibold text-gray-600" x-text="nuevoStock(s)"></span>
                                        </p>
                                    </div>
                                </template>
                                <template x-if="!modificado(s)">
                                    <span class="text-gray-300 text-xs">—</span>
                                </template>
                            </td>
                        </tr>
                    </template>

                    <!-- Sin resultados -->
                    <tr x-show="filtrados.length === 0">
                        <td colspan="4" class="px-5 py-12 text-center text-gray-400 text-sm">
                            Sin lotes que coincidan con los filtros.
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Paginación -->
            <div x-show="totalPaginas > 1"
                 class="flex items-center justify-between px-5 py-3 border-t border-gray-100 bg-gray-50">
                <span class="text-xs text-gray-500"
                      x-text="'Página ' + pagina + ' de ' + totalPaginas + ' — ' + filtrados.length + ' lotes'"></span>
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

            <!-- Resumen -->
            <div x-show="itemsModificados.length === 0" class="text-sm text-gray-400">
                Escribe el conteo real de los lotes que difieran del sistema.
            </div>
            <div x-show="itemsModificados.length > 0" class="flex flex-wrap gap-2 flex-1">
                <template x-for="s in itemsModificados.slice(0, 6)" :key="s.stock_id">
                    <div class="flex items-center gap-1.5 border rounded-lg px-2.5 py-1 text-xs"
                         :class="delta(s) > 0 ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200'">
                        <span class="font-medium truncate max-w-32"
                              :class="delta(s) > 0 ? 'text-green-800' : 'text-red-800'" x-text="s.nombre"></span>
                        <span class="font-bold"
                              :class="delta(s) > 0 ? 'text-green-600' : 'text-red-600'"
                              x-text="(delta(s) > 0 ? '+' : '') + delta(s)"></span>
                    </div>
                </template>
                <div x-show="itemsModificados.length > 6"
                     class="flex items-center px-2.5 py-1 bg-gray-100 text-gray-600 text-xs rounded-lg font-medium"
                     x-text="'+' + (itemsModificados.length - 6) + ' más'"></div>
            </div>

            <!-- Botón guardar -->
            <div class="flex items-center gap-3 flex-shrink-0">
                <div x-show="!motivo.trim() && itemsModificados.length > 0"
                     class="text-xs text-red-500 font-medium text-right max-w-36">
                    Falta el motivo
                </div>
                <button type="button" @click="enviar()" :disabled="!puedeGuardar"
                        class="px-6 py-2.5 rounded-lg font-semibold text-sm flex items-center gap-2 transition-all"
                        :class="puedeGuardar
                            ? 'bg-amber-500 text-white hover:bg-amber-600 shadow-sm hover:shadow'
                            : 'bg-gray-200 text-gray-400 cursor-not-allowed'">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span x-text="itemsModificados.length > 0
                        ? 'Aplicar ajuste (' + itemsModificados.length + ' lotes)'
                        : 'Aplicar ajuste'"></span>
                </button>
            </div>
        </div>
    </div>

</div>

<form id="form-ajuste" method="POST" action="{{ route('admin.almacen-medicamentos.ajuste-inventario.procesar') }}" style="display:none">
    @csrf
    <input type="hidden" id="input-motivo" name="motivo">
    <input type="hidden" id="input-items" name="items">
</form>
@endsection
