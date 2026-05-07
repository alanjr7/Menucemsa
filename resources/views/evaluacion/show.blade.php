@extends('layouts.app')

@section('content')
<div class="p-6 bg-gray-50 min-h-screen" x-data="evaluacion('{{ $area }}', '{{ route('evaluacion.store', $paciente->ci) }}')">

    {{-- Encabezado --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Evaluación — {{ $paciente->nombre }}</h1>
            <p class="text-sm text-gray-500">CI: {{ $paciente->ci }} &bull; Área: <span class="capitalize">{{ $area }}</span></p>
        </div>
        <a href="{{ route('patients.index') }}" class="px-4 py-2 border rounded-lg text-sm text-gray-700">Cancelar</a>
    </div>

    <form @submit.prevent="guardar" class="space-y-6">
        @csrf

        {{-- Medicamentos --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <h2 class="text-base font-semibold text-gray-700 mb-3">Medicamentos</h2>
            <div class="relative mb-3">
                <input type="text" x-model="medQ" @input.debounce.300ms="buscar('medicamento')"
                    @focus="medOpen=true" @click.outside="medOpen=false"
                    placeholder="Buscar medicamento..."
                    class="w-full border rounded-lg px-3 py-2 text-sm">
                <ul x-show="medOpen && medResultados.length" x-cloak
                    class="absolute z-20 bg-white border rounded-lg shadow-lg mt-1 w-full max-h-52 overflow-y-auto">
                    <template x-for="item in medResultados" :key="item.id">
                        <li @click="agregar('medicamento', item)" class="px-3 py-2 text-sm hover:bg-blue-50 cursor-pointer flex justify-between">
                            <span x-text="item.nombre"></span>
                            <span class="text-gray-400 text-xs" x-text="'Stock: ' + item.cantidad_actual + ' ' + item.unidad_medida"></span>
                        </li>
                    </template>
                </ul>
            </div>
            <template x-if="medicamentos.length === 0">
                <p class="text-sm text-gray-400">Sin medicamentos agregados.</p>
            </template>
            <table x-show="medicamentos.length > 0" class="w-full text-sm">
                <thead class="text-gray-500 text-xs border-b">
                    <tr>
                        <th class="pb-2 text-left">Nombre</th>
                        <th class="pb-2 text-center w-28">Cantidad</th>
                        <th class="pb-2 text-center w-20">Stock</th>
                        <th class="pb-2 w-10"></th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="(item, i) in medicamentos" :key="item.id">
                        <tr class="border-t">
                            <td class="py-2" x-text="item.nombre"></td>
                            <td class="py-2 text-center">
                                <input type="number" x-model.number="item.cantidad" :max="item.cantidad_actual" min="1"
                                    @change="if(item.cantidad > item.cantidad_actual) item.cantidad = item.cantidad_actual"
                                    class="w-20 border rounded px-2 py-1 text-center text-sm">
                            </td>
                            <td class="py-2 text-center text-gray-400 text-xs" x-text="item.cantidad_actual + ' ' + item.unidad_medida"></td>
                            <td class="py-2 text-center">
                                <button type="button" @click="medicamentos.splice(i,1)" class="text-red-500 hover:text-red-700 text-lg leading-none">&times;</button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        {{-- Insumos --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <h2 class="text-base font-semibold text-gray-700 mb-3">Insumos</h2>
            <div class="relative mb-3">
                <input type="text" x-model="insQ" @input.debounce.300ms="buscar('insumo')"
                    @focus="insOpen=true" @click.outside="insOpen=false"
                    placeholder="Buscar insumo..."
                    class="w-full border rounded-lg px-3 py-2 text-sm">
                <ul x-show="insOpen && insResultados.length" x-cloak
                    class="absolute z-20 bg-white border rounded-lg shadow-lg mt-1 w-full max-h-52 overflow-y-auto">
                    <template x-for="item in insResultados" :key="item.id">
                        <li @click="agregar('insumo', item)" class="px-3 py-2 text-sm hover:bg-blue-50 cursor-pointer flex justify-between">
                            <span x-text="item.nombre"></span>
                            <span class="text-gray-400 text-xs" x-text="'Stock: ' + item.cantidad_actual + ' ' + item.unidad_medida"></span>
                        </li>
                    </template>
                </ul>
            </div>
            <template x-if="insumos.length === 0">
                <p class="text-sm text-gray-400">Sin insumos agregados.</p>
            </template>
            <table x-show="insumos.length > 0" class="w-full text-sm">
                <thead class="text-gray-500 text-xs border-b">
                    <tr>
                        <th class="pb-2 text-left">Nombre</th>
                        <th class="pb-2 text-center w-28">Cantidad</th>
                        <th class="pb-2 text-center w-20">Stock</th>
                        <th class="pb-2 w-10"></th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="(item, i) in insumos" :key="item.id">
                        <tr class="border-t">
                            <td class="py-2" x-text="item.nombre"></td>
                            <td class="py-2 text-center">
                                <input type="number" x-model.number="item.cantidad" :max="item.cantidad_actual" min="1"
                                    @change="if(item.cantidad > item.cantidad_actual) item.cantidad = item.cantidad_actual"
                                    class="w-20 border rounded px-2 py-1 text-center text-sm">
                            </td>
                            <td class="py-2 text-center text-gray-400 text-xs" x-text="item.cantidad_actual + ' ' + item.unidad_medida"></td>
                            <td class="py-2 text-center">
                                <button type="button" @click="insumos.splice(i,1)" class="text-red-500 hover:text-red-700 text-lg leading-none">&times;</button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        {{-- Procedimientos --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <h2 class="text-base font-semibold text-gray-700 mb-3">Procedimientos</h2>
            <div class="relative mb-3">
                <input type="text" x-model="procQ" @input.debounce.300ms="buscar('procedimiento')"
                    @focus="procOpen=true" @click.outside="procOpen=false"
                    placeholder="Buscar procedimiento..."
                    class="w-full border rounded-lg px-3 py-2 text-sm">
                <ul x-show="procOpen && procResultados.length" x-cloak
                    class="absolute z-20 bg-white border rounded-lg shadow-lg mt-1 w-full max-h-52 overflow-y-auto">
                    <template x-for="item in procResultados" :key="item.id">
                        <li @click="agregar('procedimiento', item)" class="px-3 py-2 text-sm hover:bg-blue-50 cursor-pointer flex justify-between">
                            <span x-text="item.nombre"></span>
                            <span class="text-gray-400 text-xs" x-text="'Bs. ' + item.precio"></span>
                        </li>
                    </template>
                </ul>
            </div>
            <template x-if="procedimientos.length === 0">
                <p class="text-sm text-gray-400">Sin procedimientos agregados.</p>
            </template>
            <table x-show="procedimientos.length > 0" class="w-full text-sm">
                <thead class="text-gray-500 text-xs border-b">
                    <tr>
                        <th class="pb-2 text-left">Nombre</th>
                        <th class="pb-2 text-center w-28">Cantidad</th>
                        <th class="pb-2 text-right w-28">Precio unit.</th>
                        <th class="pb-2 w-10"></th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="(item, i) in procedimientos" :key="item.id">
                        <tr class="border-t">
                            <td class="py-2" x-text="item.nombre"></td>
                            <td class="py-2 text-center">
                                <input type="number" x-model.number="item.cantidad" min="1"
                                    class="w-20 border rounded px-2 py-1 text-center text-sm">
                            </td>
                            <td class="py-2 text-right text-gray-600" x-text="'Bs. ' + parseFloat(item.precio).toFixed(2)"></td>
                            <td class="py-2 text-center">
                                <button type="button" @click="procedimientos.splice(i,1)" class="text-red-500 hover:text-red-700 text-lg leading-none">&times;</button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        {{-- Observaciones --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <h2 class="text-base font-semibold text-gray-700 mb-3">Observaciones</h2>
            <textarea x-model="observaciones" rows="4" placeholder="Observaciones clínicas..."
                class="w-full border rounded-lg px-3 py-2 text-sm"></textarea>
        </div>

        <div x-show="error" class="bg-red-100 text-red-700 rounded-lg px-4 py-3 text-sm" x-text="error"></div>

        <div class="flex gap-3">
            <button type="submit" :disabled="saving"
                class="px-6 py-2 bg-blue-600 text-white rounded-lg text-sm disabled:opacity-50">
                <span x-text="saving ? 'Guardando...' : 'Guardar evaluación'"></span>
            </button>
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
