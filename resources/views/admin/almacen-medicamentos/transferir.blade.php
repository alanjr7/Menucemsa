@extends('layouts.app')

@section('title', 'Transferir a Área')

@section('content')
<div class="min-h-screen bg-gray-50 p-6"
     x-data="{
        areaDestino: '',
        recibidoPor: '',
        busqueda: '',
        lotes: @js($lotes->map(fn($l) => [
            'lote_id' => $l->id,
            'nombre' => $l->catalogo->nombre,
            'codigo_lote' => $l->codigo_lote ?? 'Sin código',
            'vencimiento' => $l->fecha_vencimiento?->format('d/m/Y') ?? 'Sin fecha',
            'stock' => $l->stocks->where('ubicacion', 'central')->sum('cantidad_actual'),
            'unidad' => $l->catalogo->unidad_medida,
        ])),
        seleccionados: [],

        get filtrados() {
            if (!this.busqueda) return this.lotes;
            const t = this.busqueda.toLowerCase();
            return this.lotes.filter(l => l.nombre.toLowerCase().includes(t) || l.codigo_lote.toLowerCase().includes(t));
        },

        agregar(lote) {
            if (!this.seleccionados.find(s => s.lote_id === lote.lote_id)) {
                this.seleccionados.push({ ...lote, cantidad: 1 });
            }
        },

        quitar(loteId) {
            this.seleccionados = this.seleccionados.filter(s => s.lote_id !== loteId);
        },

        validar(item) {
            if (item.cantidad < 1) item.cantidad = 1;
            if (item.cantidad > item.stock) item.cantidad = item.stock;
        },

        get puedeTransferir() {
            return this.areaDestino && this.seleccionados.length > 0 && this.seleccionados.every(s => s.cantidad > 0);
        },

        get totalUnidades() {
            return this.seleccionados.reduce((sum, s) => sum + parseInt(s.cantidad || 0), 0);
        }
     }">

    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Transferir a Área</h1>
            <p class="text-gray-600 mt-1">Mueve lotes del almacén central a un área clínica</p>
        </div>
        <a href="{{ route('admin.almacen-medicamentos.index') }}"
           class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg flex items-center gap-2 text-sm">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver
        </a>
    </div>

    @if(session('error'))
    <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">{{ session('error') }}</div>
    @endif

    <!-- Configuración -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Área destino <span class="text-red-500">*</span></label>
                <select x-model="areaDestino" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    <option value="">Seleccionar área</option>
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
                <label class="block text-sm font-medium text-gray-700 mb-1">Recibido por</label>
                <input type="text" x-model="recibidoPor" maxlength="150"
                       placeholder="Nombre de quien recibe"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
            </div>
        </div>
    </div>

    <!-- Paneles -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Lotes disponibles -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-semibold text-gray-900">Lotes en Almacén Central
                    <span class="ml-2 px-2 py-0.5 bg-blue-100 text-blue-800 text-xs rounded-full" x-text="filtrados.length + ' lotes'"></span>
                </h3>
            </div>
            <div class="p-4">
                <input x-model="busqueda" type="text" placeholder="Buscar por nombre o código de lote..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm mb-4">
                <div class="space-y-2 max-h-[500px] overflow-y-auto">
                    <template x-for="lote in filtrados" :key="lote.lote_id">
                        <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:bg-gray-50"
                             :class="{ 'bg-green-50 border-green-200': seleccionados.find(s => s.lote_id === lote.lote_id) }">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900" x-text="lote.nombre"></p>
                                <p class="text-xs text-gray-500" x-text="`Lote: ${lote.codigo_lote} — Vence: ${lote.vencimiento}`"></p>
                                <p class="text-xs font-semibold"
                                   :class="lote.stock === 0 ? 'text-red-600' : 'text-green-600'"
                                   x-text="`Stock central: ${lote.stock} ${lote.unidad}`"></p>
                            </div>
                            <button type="button" @click="agregar(lote)"
                                    :disabled="Number(lote.stock) === 0 || seleccionados.find(s => s.lote_id === lote.lote_id)"
                                    class="ml-2 p-2 rounded-lg transition-colors flex-shrink-0"
                                    :class="(Number(lote.stock) === 0 || seleccionados.find(s => s.lote_id === lote.lote_id)) ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : 'bg-green-100 text-green-600 hover:bg-green-200'">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                            </button>
                        </div>
                    </template>
                    <div x-show="filtrados.length === 0" class="text-center py-8 text-gray-500 text-sm">
                        No se encontraron lotes disponibles.
                    </div>
                </div>
            </div>
        </div>

        <!-- Seleccionados -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-semibold text-gray-900">Lotes a Transferir
                    <span class="ml-2 px-2 py-0.5 bg-indigo-100 text-indigo-800 text-xs rounded-full" x-show="seleccionados.length > 0" x-text="seleccionados.length + ' lotes'"></span>
                </h3>
            </div>
            <div class="p-4">
                <div x-show="seleccionados.length === 0" class="text-center py-12 text-gray-500 text-sm">
                    Haz clic en + para agregar lotes a la transferencia.
                </div>
                <div x-show="seleccionados.length > 0" class="space-y-3">
                    <template x-for="item in seleccionados" :key="item.lote_id">
                        <div class="flex items-center gap-3 p-3 border border-indigo-200 rounded-lg bg-indigo-50">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900" x-text="item.nombre"></p>
                                <p class="text-xs text-gray-500" x-text="`Lote ${item.codigo_lote} — Disponible: ${item.stock} ${item.unidad}`"></p>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-sm text-gray-600">×</span>
                                <input type="number" x-model.number="item.cantidad" @change="validar(item)"
                                       class="w-20 px-2 py-1 text-center border border-gray-300 rounded text-sm"
                                       min="1" :max="item.stock">
                                <span class="text-sm text-gray-500" x-text="item.unidad"></span>
                            </div>
                            <button @click="quitar(item.lote_id)" class="p-1.5 text-red-500 hover:bg-red-100 rounded">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </template>
                    <div class="mt-4 p-3 bg-blue-50 rounded-lg border border-blue-200 flex justify-between items-center text-sm">
                        <span class="text-gray-700"><span class="font-semibold" x-text="seleccionados.length"></span> lotes — <span class="font-semibold" x-text="totalUnidades"></span> unidades total</span>
                        <button @click="seleccionados = []" class="text-red-600 hover:text-red-800 font-medium text-xs">Limpiar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Botón transferir -->
    <div class="mt-6 bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <form method="POST" action="{{ route('admin.almacen-medicamentos.transferir.procesar') }}"
              x-ref="form"
              @submit.prevent="
                  $refs.form.querySelector('[name=items]').value = JSON.stringify(seleccionados.map(s => ({ lote_id: s.lote_id, cantidad: s.cantidad })));
                  $refs.form.querySelector('[name=recibido_por]').value = recibidoPor;
                  $refs.form.querySelector('[name=ubicacion_destino]').value = areaDestino;
                  $refs.form.submit();
              ">
            @csrf
            <input type="hidden" name="ubicacion_destino">
            <input type="hidden" name="recibido_por">
            <input type="hidden" name="items">

            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-600 space-y-1">
                    <p>Destino: <span class="font-semibold text-gray-900" x-text="areaDestino ? areaDestino.charAt(0).toUpperCase() + areaDestino.slice(1) : 'No seleccionado'"></span></p>
                    <p>Lotes: <span class="font-semibold text-gray-900" x-text="seleccionados.length"></span></p>
                </div>
                <button type="submit" :disabled="!puedeTransferir"
                        class="px-6 py-3 rounded-lg font-medium flex items-center gap-2 transition-colors"
                        :class="puedeTransferir ? 'bg-green-600 text-white hover:bg-green-700' : 'bg-gray-300 text-gray-500 cursor-not-allowed'">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                    </svg>
                    Realizar Transferencia
                </button>
            </div>
            <p x-show="!areaDestino && seleccionados.length > 0" class="mt-2 text-xs text-yellow-600">
                Selecciona un área destino para continuar.
            </p>
        </form>
    </div>
</div>
@endsection
