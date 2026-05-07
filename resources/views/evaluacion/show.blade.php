@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-slate-50" x-data="evaluacion('{{ $area }}', '{{ route('evaluacion.store', $paciente->ci) }}')">

    {{-- Encabezado --}}
    <div class="bg-white border-b border-slate-200 px-8 py-6">
        <div class="max-w-4xl mx-auto flex items-start justify-between gap-6">
            <div>
                <div class="flex items-center gap-3 mb-1">
                    <span class="inline-block px-2.5 py-0.5 rounded text-xs font-semibold uppercase tracking-widest bg-slate-100 text-slate-600 border border-slate-200">
                        {{ $area }}
                    </span>
                </div>
                <h1 class="text-2xl font-bold text-slate-900 leading-tight">{{ $paciente->nombre }}</h1>
                <p class="mt-1 text-base text-slate-500">C.I. {{ $paciente->ci }}</p>
            </div>
            <a href="{{ route('patients.index') }}"
               class="shrink-0 px-5 py-2.5 border-2 border-slate-300 rounded-lg text-base font-medium text-slate-600 hover:bg-slate-50 transition-colors">
                Cancelar
            </a>
        </div>
    </div>

    <form @submit.prevent="guardar" class="max-w-4xl mx-auto px-8 py-8 space-y-6">
        @csrf

        {{-- Medicamentos --}}
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100">
                <h2 class="text-lg font-semibold text-slate-800">Medicamentos</h2>
            </div>
            <div class="px-6 py-5">
                <div class="relative mb-4">
                    <input type="text" x-model="medQ" @input.debounce.300ms="buscar('medicamento')"
                        @focus="medOpen=true" @click.outside="medOpen=false"
                        placeholder="Buscar medicamento por nombre..."
                        class="w-full border border-slate-300 rounded-lg px-4 py-3 text-base text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-400 focus:border-transparent">
                    <ul x-show="medOpen && medResultados.length" x-cloak
                        class="absolute z-20 bg-white border border-slate-200 rounded-lg shadow-lg mt-1 w-full max-h-60 overflow-y-auto">
                        <template x-for="item in medResultados" :key="item.id">
                            <li @click="agregar('medicamento', item)"
                                class="px-4 py-3 text-base hover:bg-slate-50 cursor-pointer flex justify-between items-center border-b border-slate-100 last:border-0">
                                <span class="font-medium text-slate-900" x-text="item.nombre"></span>
                                <span class="text-slate-500 text-sm ml-4 shrink-0" x-text="'Stock: ' + item.cantidad_actual + ' ' + item.unidad_medida"></span>
                            </li>
                        </template>
                    </ul>
                </div>

                <template x-if="medicamentos.length === 0">
                    <p class="text-base text-slate-400 py-2">Sin medicamentos agregados.</p>
                </template>
                <table x-show="medicamentos.length > 0" class="w-full">
                    <thead>
                        <tr class="border-b-2 border-slate-200">
                            <th class="pb-3 text-left text-sm font-semibold text-slate-500 uppercase tracking-wide">Medicamento</th>
                            <th class="pb-3 text-center text-sm font-semibold text-slate-500 uppercase tracking-wide w-36">Cantidad</th>
                            <th class="pb-3 text-center text-sm font-semibold text-slate-500 uppercase tracking-wide w-32">Stock</th>
                            <th class="pb-3 w-12"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(item, i) in medicamentos" :key="item.id">
                            <tr class="border-b border-slate-100 last:border-0">
                                <td class="py-3 text-base text-slate-900 font-medium" x-text="item.nombre"></td>
                                <td class="py-3 text-center">
                                    <input type="number" x-model.number="item.cantidad" :max="item.cantidad_actual" min="1"
                                        @change="if(item.cantidad > item.cantidad_actual) item.cantidad = item.cantidad_actual"
                                        class="w-24 border border-slate-300 rounded-lg px-3 py-2 text-center text-base text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-400">
                                </td>
                                <td class="py-3 text-center text-base text-slate-500" x-text="item.cantidad_actual + ' ' + item.unidad_medida"></td>
                                <td class="py-3 text-center">
                                    <button type="button" @click="medicamentos.splice(i,1)"
                                        class="w-8 h-8 flex items-center justify-center rounded-md text-slate-400 hover:text-red-600 hover:bg-red-50 transition-colors text-xl font-light mx-auto">
                                        &times;
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Insumos --}}
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100">
                <h2 class="text-lg font-semibold text-slate-800">Insumos</h2>
            </div>
            <div class="px-6 py-5">
                <div class="relative mb-4">
                    <input type="text" x-model="insQ" @input.debounce.300ms="buscar('insumo')"
                        @focus="insOpen=true" @click.outside="insOpen=false"
                        placeholder="Buscar insumo por nombre..."
                        class="w-full border border-slate-300 rounded-lg px-4 py-3 text-base text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-400 focus:border-transparent">
                    <ul x-show="insOpen && insResultados.length" x-cloak
                        class="absolute z-20 bg-white border border-slate-200 rounded-lg shadow-lg mt-1 w-full max-h-60 overflow-y-auto">
                        <template x-for="item in insResultados" :key="item.id">
                            <li @click="agregar('insumo', item)"
                                class="px-4 py-3 text-base hover:bg-slate-50 cursor-pointer flex justify-between items-center border-b border-slate-100 last:border-0">
                                <span class="font-medium text-slate-900" x-text="item.nombre"></span>
                                <span class="text-slate-500 text-sm ml-4 shrink-0" x-text="'Stock: ' + item.cantidad_actual + ' ' + item.unidad_medida"></span>
                            </li>
                        </template>
                    </ul>
                </div>

                <template x-if="insumos.length === 0">
                    <p class="text-base text-slate-400 py-2">Sin insumos agregados.</p>
                </template>
                <table x-show="insumos.length > 0" class="w-full">
                    <thead>
                        <tr class="border-b-2 border-slate-200">
                            <th class="pb-3 text-left text-sm font-semibold text-slate-500 uppercase tracking-wide">Insumo</th>
                            <th class="pb-3 text-center text-sm font-semibold text-slate-500 uppercase tracking-wide w-36">Cantidad</th>
                            <th class="pb-3 text-center text-sm font-semibold text-slate-500 uppercase tracking-wide w-32">Stock</th>
                            <th class="pb-3 w-12"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(item, i) in insumos" :key="item.id">
                            <tr class="border-b border-slate-100 last:border-0">
                                <td class="py-3 text-base text-slate-900 font-medium" x-text="item.nombre"></td>
                                <td class="py-3 text-center">
                                    <input type="number" x-model.number="item.cantidad" :max="item.cantidad_actual" min="1"
                                        @change="if(item.cantidad > item.cantidad_actual) item.cantidad = item.cantidad_actual"
                                        class="w-24 border border-slate-300 rounded-lg px-3 py-2 text-center text-base text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-400">
                                </td>
                                <td class="py-3 text-center text-base text-slate-500" x-text="item.cantidad_actual + ' ' + item.unidad_medida"></td>
                                <td class="py-3 text-center">
                                    <button type="button" @click="insumos.splice(i,1)"
                                        class="w-8 h-8 flex items-center justify-center rounded-md text-slate-400 hover:text-red-600 hover:bg-red-50 transition-colors text-xl font-light mx-auto">
                                        &times;
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Procedimientos --}}
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100">
                <h2 class="text-lg font-semibold text-slate-800">Procedimientos</h2>
            </div>
            <div class="px-6 py-5">
                <div class="relative mb-4">
                    <input type="text" x-model="procQ" @input.debounce.300ms="buscar('procedimiento')"
                        @focus="procOpen=true" @click.outside="procOpen=false"
                        placeholder="Buscar procedimiento..."
                        class="w-full border border-slate-300 rounded-lg px-4 py-3 text-base text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-400 focus:border-transparent">
                    <ul x-show="procOpen && procResultados.length" x-cloak
                        class="absolute z-20 bg-white border border-slate-200 rounded-lg shadow-lg mt-1 w-full max-h-60 overflow-y-auto">
                        <template x-for="item in procResultados" :key="item.id">
                            <li @click="agregar('procedimiento', item)"
                                class="px-4 py-3 text-base hover:bg-slate-50 cursor-pointer flex justify-between items-center border-b border-slate-100 last:border-0">
                                <span class="font-medium text-slate-900" x-text="item.nombre"></span>
                                <span class="text-slate-500 text-sm ml-4 shrink-0" x-text="'Bs. ' + item.precio"></span>
                            </li>
                        </template>
                    </ul>
                </div>

                <template x-if="procedimientos.length === 0">
                    <p class="text-base text-slate-400 py-2">Sin procedimientos agregados.</p>
                </template>
                <table x-show="procedimientos.length > 0" class="w-full">
                    <thead>
                        <tr class="border-b-2 border-slate-200">
                            <th class="pb-3 text-left text-sm font-semibold text-slate-500 uppercase tracking-wide">Procedimiento</th>
                            <th class="pb-3 text-center text-sm font-semibold text-slate-500 uppercase tracking-wide w-36">Cantidad</th>
                            <th class="pb-3 text-right text-sm font-semibold text-slate-500 uppercase tracking-wide w-36">Precio unit.</th>
                            <th class="pb-3 w-12"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(item, i) in procedimientos" :key="item.id">
                            <tr class="border-b border-slate-100 last:border-0">
                                <td class="py-3 text-base text-slate-900 font-medium" x-text="item.nombre"></td>
                                <td class="py-3 text-center">
                                    <input type="number" x-model.number="item.cantidad" min="1"
                                        class="w-24 border border-slate-300 rounded-lg px-3 py-2 text-center text-base text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-400">
                                </td>
                                <td class="py-3 text-right text-base text-slate-700 font-medium" x-text="'Bs. ' + parseFloat(item.precio).toFixed(2)"></td>
                                <td class="py-3 text-center">
                                    <button type="button" @click="procedimientos.splice(i,1)"
                                        class="w-8 h-8 flex items-center justify-center rounded-md text-slate-400 hover:text-red-600 hover:bg-red-50 transition-colors text-xl font-light mx-auto">
                                        &times;
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Observaciones --}}
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100">
                <h2 class="text-lg font-semibold text-slate-800">Observaciones clínicas</h2>
            </div>
            <div class="px-6 py-5">
                <textarea x-model="observaciones" rows="4"
                    placeholder="Ingrese observaciones clínicas del paciente..."
                    class="w-full border border-slate-300 rounded-lg px-4 py-3 text-base text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-400 resize-none"></textarea>
            </div>
        </div>

        {{-- Error --}}
        <div x-show="error" x-cloak
            class="border border-red-200 bg-red-50 text-red-700 rounded-lg px-5 py-4 text-base"
            x-text="error">
        </div>

        {{-- Acciones --}}
        <div class="flex items-center gap-4 pb-8">
            <button type="submit" :disabled="saving"
                class="px-8 py-3 bg-slate-800 text-white rounded-lg text-base font-semibold hover:bg-slate-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                <span x-text="saving ? 'Guardando...' : 'Guardar evaluación'"></span>
            </button>
            <a href="{{ route('patients.index') }}"
               class="px-6 py-3 text-base text-slate-600 hover:text-slate-900 transition-colors">
                Cancelar
            </a>
        </div>
    </form>
</div>

<script>
function evaluacion(area, storeUrl) {
    return {
        area,
        storeUrl,
        medicamentos: [],
        insumos: [],
        procedimientos: [],
        observaciones: '',
        medQ: '', insQ: '', procQ: '',
        medResultados: [], insResultados: [], procResultados: [],
        medOpen: false, insOpen: false, procOpen: false,
        saving: false,
        error: '',

        async buscar(tipo) {
            const q = tipo === 'medicamento' ? this.medQ : tipo === 'insumo' ? this.insQ : this.procQ;
            const url = tipo === 'medicamento' ? '/api/evaluacion/medicamentos'
                      : tipo === 'insumo'      ? '/api/evaluacion/insumos'
                      :                          '/api/evaluacion/procedimientos';
            try {
                const res = await axios.get(url, { params: { q, area } });
                if (tipo === 'medicamento') { this.medResultados = res.data; this.medOpen = true; }
                else if (tipo === 'insumo') { this.insResultados = res.data; this.insOpen = true; }
                else { this.procResultados = res.data; this.procOpen = true; }
            } catch(e) {}
        },

        agregar(tipo, item) {
            const lista = tipo === 'medicamento' ? this.medicamentos
                        : tipo === 'insumo'      ? this.insumos
                        :                          this.procedimientos;
            const existente = lista.find(i => i.id === item.id);
            if (existente) {
                if (tipo !== 'procedimiento' && existente.cantidad < existente.cantidad_actual) {
                    existente.cantidad++;
                }
            } else {
                lista.push({ ...item, cantidad: 1 });
            }
            if (tipo === 'medicamento') { this.medQ = ''; this.medOpen = false; }
            else if (tipo === 'insumo') { this.insQ = ''; this.insOpen = false; }
            else { this.procQ = ''; this.procOpen = false; }
        },

        async guardar() {
            this.error = '';
            this.saving = true;
            const items = [
                ...this.medicamentos.map(i => ({ tipo: 'medicamento', item_id: i.id, nombre: i.nombre, cantidad: i.cantidad, precio: i.precio })),
                ...this.insumos.map(i => ({ tipo: 'insumo', item_id: i.id, nombre: i.nombre, cantidad: i.cantidad, precio: i.precio })),
                ...this.procedimientos.map(i => ({ tipo: 'procedimiento', item_id: i.id, nombre: i.nombre, cantidad: i.cantidad, precio: i.precio })),
            ];
            try {
                const res = await axios.post(this.storeUrl, {
                    _token: document.querySelector('meta[name="csrf-token"]').content,
                    observaciones: this.observaciones,
                    items,
                });
                if (res.data.redirect) window.location.href = res.data.redirect;
            } catch(e) {
                this.error = e.response?.data?.message || 'Error al guardar la evaluación.';
                this.saving = false;
            }
        }
    }
}
</script>
@endsection
