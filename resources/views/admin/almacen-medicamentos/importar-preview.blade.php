@extends('layouts.app')

@section('title', 'Previsualización de Importación')

@section('content')
<div class="min-h-screen bg-gray-50 p-6"
     x-data="{
        modo: '{{ $modo }}',
        crearNuevos: {{ $crearNuevos ? 'true' : 'false' }},
        items: @js($items),
        normId(v) { return (v === '' || v === null || v === undefined) ? null : v; },
        esNuevo(it) { return this.normId(it.catalogo_id) === null; },
        omitir(it) { return this.esNuevo(it) && !this.crearNuevos; },
        selectedStock(it) {
            const id = this.normId(it.catalogo_id);
            if (id === null) return 0;
            const c = (it.candidatos || []).find(x => x.id == id);
            return c ? c.stock : it.stock_actual;
        },
        resultado(it) {
            if (this.omitir(it)) return null;
            return this.modo === 'reemplazar' ? it.cantidad : this.selectedStock(it) + it.cantidad;
        },
        get totalAplicar() {
            return this.items.filter(i => !this.omitir(i)).length;
        },
        confirmar() {
            const payload = this.items.map(it => ({ ...it, catalogo_id: this.normId(it.catalogo_id) }));
            document.getElementById('input-modo').value = this.modo;
            document.getElementById('input-items').value = JSON.stringify(payload);
            document.getElementById('form-confirmar').submit();
        }
     }">
    <div class="max-w-7xl mx-auto">

        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.almacen-medicamentos.importar.form') }}" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Previsualización</h1>
                    <p class="text-sm text-gray-500">Área destino: <span class="font-semibold text-gray-700">{{ $areaLabel }}</span> · Motivo: {{ $motivo }}</p>
                </div>
            </div>
        </div>

        <!-- Resumen -->
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-5">
            <div class="bg-white rounded-lg border border-gray-200 p-4">
                <p class="text-xs text-gray-500">Total filas</p>
                <p class="text-2xl font-bold text-gray-900">{{ $resumen['total'] }}</p>
            </div>
            <div class="bg-white rounded-lg border border-gray-200 p-4">
                <p class="text-xs text-gray-500">Coinciden</p>
                <p class="text-2xl font-bold text-green-600">{{ $resumen['coinciden'] }}</p>
            </div>
            <div class="bg-white rounded-lg border border-gray-200 p-4">
                <p class="text-xs text-gray-500">Sin coincidencia</p>
                <p class="text-2xl font-bold text-blue-600">{{ $resumen['nuevos'] }}</p>
            </div>
            <div class="bg-white rounded-lg border border-gray-200 p-4">
                <p class="text-xs text-gray-500">Se aplicarán</p>
                <p class="text-2xl font-bold text-amber-600" x-text="totalAplicar"></p>
            </div>
            <div class="bg-white rounded-lg border border-gray-200 p-4">
                <p class="text-xs text-gray-500">Errores</p>
                <p class="text-2xl font-bold text-red-600">{{ $resumen['con_errores'] }}</p>
            </div>
        </div>

        <!-- Modo: sumar / reemplazar -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 mb-5">
            <p class="text-sm font-semibold text-gray-700 mb-2">¿Cómo aplicar las cantidades del Excel?</p>
            <div class="flex flex-col sm:flex-row gap-3">
                <label class="flex-1 flex items-start gap-3 p-3 border rounded-lg cursor-pointer transition-colors"
                       :class="modo === 'sumar' ? 'border-amber-400 bg-amber-50' : 'border-gray-200 hover:bg-gray-50'">
                    <input type="radio" value="sumar" x-model="modo" class="mt-0.5 w-4 h-4 text-amber-600">
                    <span class="text-sm">
                        <span class="font-medium text-gray-900">Sumar al stock existente</span>
                        <span class="block text-xs text-gray-500">Stock actual + cantidad del Excel</span>
                    </span>
                </label>
                <label class="flex-1 flex items-start gap-3 p-3 border rounded-lg cursor-pointer transition-colors"
                       :class="modo === 'reemplazar' ? 'border-amber-400 bg-amber-50' : 'border-gray-200 hover:bg-gray-50'">
                    <input type="radio" value="reemplazar" x-model="modo" class="mt-0.5 w-4 h-4 text-amber-600">
                    <span class="text-sm">
                        <span class="font-medium text-gray-900">Reemplazar el stock</span>
                        <span class="block text-xs text-gray-500">El stock pasa a ser exactamente la cantidad del Excel</span>
                    </span>
                </label>
            </div>
        </div>

        <!-- Errores -->
        @if(count($errores))
        <div class="bg-red-50 border border-red-200 rounded-xl p-5 mb-5">
            <p class="text-sm font-semibold text-red-800 mb-2">{{ count($errores) }} fila(s) con problemas (no se importarán):</p>
            <ul class="text-sm text-red-700 space-y-0.5 list-disc list-inside max-h-40 overflow-y-auto">
                @foreach($errores as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <!-- Aviso desambiguación -->
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-5 text-sm text-blue-800">
            En <strong>"Mapear a"</strong> elegí a qué producto del catálogo corresponde cada fila. Si hay varios parecidos (ej. distintas presentaciones de Omeprazol), seleccioná el correcto o dejá <strong>Crear nuevo</strong>. El proveedor/laboratorio se guarda como un lote dentro del producto elegido.
        </div>

        <!-- Tabla previsualización -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden mb-24">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Fila Excel</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Proveedor / Lab</th>
                            <th class="text-left px-4 py-3 text-xs font-semibold text-indigo-600 uppercase w-72">Mapear a (catálogo)</th>
                            <th class="text-center px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Stock actual</th>
                            <th class="text-center px-4 py-3 text-xs font-semibold text-amber-600 uppercase">Excel</th>
                            <th class="text-center px-4 py-3 text-xs font-semibold text-green-600 uppercase">Resultado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <template x-for="it in items" :key="it.linea">
                            <tr :class="omitir(it) ? 'bg-gray-50 opacity-60' : 'hover:bg-gray-50'">
                                <!-- Nombre Excel -->
                                <td class="px-4 py-3">
                                    <p class="font-medium text-gray-900" x-text="it.nombre"></p>
                                    <span class="text-xs text-gray-400" x-text="it.unidad + (it.tipo === 'insumo' ? ' · Insumo' : ' · Medicamento')"></span>
                                </td>

                                <!-- Proveedor / Lab -->
                                <td class="px-4 py-3 text-xs text-gray-600">
                                    <template x-if="it.proveedor || it.laboratorio">
                                        <div>
                                            <p x-show="it.proveedor"><span class="text-gray-400">Prov:</span> <span x-text="it.proveedor"></span></p>
                                            <p x-show="it.laboratorio"><span class="text-gray-400">Lab:</span> <span x-text="it.laboratorio"></span></p>
                                        </div>
                                    </template>
                                    <span x-show="!it.proveedor && !it.laboratorio" class="text-gray-300">—</span>
                                </td>

                                <!-- Mapear a -->
                                <td class="px-4 py-3">
                                    <select x-model="it.catalogo_id"
                                            class="w-full px-2 py-1.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-200"
                                            :class="esNuevo(it) ? 'border-blue-300 bg-blue-50 text-blue-800' : 'border-green-300 bg-green-50 text-green-800'">
                                        <template x-for="c in it.candidatos" :key="c.id">
                                            <option :value="c.id" x-text="c.nombre + (c.exacto ? '  (coincidencia exacta)' : '') + ' — stock: ' + c.stock"></option>
                                        </template>
                                        <option value="" x-text="'➕ Crear nuevo producto: ' + it.nombre"></option>
                                    </select>
                                    <p class="mt-1 text-xs">
                                        <template x-if="omitir(it)">
                                            <span class="text-gray-500 font-medium">Se omitirá (no existe y "crear nuevos" está apagado)</span>
                                        </template>
                                        <template x-if="!omitir(it) && esNuevo(it)">
                                            <span class="text-blue-600 font-medium">Se creará como producto nuevo</span>
                                        </template>
                                        <template x-if="!esNuevo(it)">
                                            <span class="text-green-600 font-medium">Se actualiza producto existente</span>
                                        </template>
                                    </p>
                                </td>

                                <!-- Stock actual -->
                                <td class="px-4 py-3 text-center text-gray-700" x-text="esNuevo(it) ? '—' : selectedStock(it)"></td>

                                <!-- Excel -->
                                <td class="px-4 py-3 text-center font-medium text-amber-700" x-text="it.cantidad"></td>

                                <!-- Resultado -->
                                <td class="px-4 py-3 text-center">
                                    <template x-if="omitir(it)">
                                        <span class="text-gray-300">—</span>
                                    </template>
                                    <template x-if="!omitir(it)">
                                        <span class="font-bold text-green-700" x-text="resultado(it)"></span>
                                    </template>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Barra de acción fija -->
        <div class="fixed bottom-0 left-0 right-0 z-30 bg-white border-t border-gray-200 shadow-lg px-6 py-4">
            <div class="max-w-7xl mx-auto flex items-center justify-between gap-4">
                <p class="text-sm text-gray-500">
                    Se aplicarán <span class="font-bold text-gray-800" x-text="totalAplicar"></span> ítem(s) en <span class="font-semibold">{{ $areaLabel }}</span>
                    (<span x-text="modo === 'sumar' ? 'sumando' : 'reemplazando'"></span>).
                </p>
                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.almacen-medicamentos.importar.form') }}" class="px-4 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg text-sm hover:bg-gray-50">Volver</a>
                    <button type="button" @click="confirmar()" :disabled="totalAplicar === 0"
                            class="px-6 py-2.5 rounded-lg font-semibold text-sm flex items-center gap-2 transition-all"
                            :class="totalAplicar > 0 ? 'bg-green-600 text-white hover:bg-green-700' : 'bg-gray-200 text-gray-400 cursor-not-allowed'">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Confirmar importación
                    </button>
                </div>
            </div>
        </div>
    </div>

    <form id="form-confirmar" method="POST" action="{{ route('admin.almacen-medicamentos.importar.confirmar') }}" style="display:none">
        @csrf
        <input type="hidden" name="area" value="{{ $area }}">
        <input type="hidden" name="motivo" value="{{ $motivo }}">
        <input type="hidden" name="crear_nuevos" value="{{ $crearNuevos ? 1 : 0 }}">
        <input type="hidden" id="input-modo" name="modo" value="{{ $modo }}">
        <input type="hidden" id="input-items" name="items">
    </form>
</div>
@endsection
