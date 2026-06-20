@php
    // Estado inicial para Alpine: prioriza old() (revalidación) sobre el modelo.
    $itemsModelo = $proforma->exists
        ? $proforma->items->map(fn ($i) => [
            'descripcion'     => $i->descripcion,
            'cantidad'        => rtrim(rtrim(number_format($i->cantidad, 2, '.', ''), '0'), '.'),
            'precio_unitario' => number_format($i->precio_unitario, 2, '.', ''),
            'codigo_item'     => $i->codigo_item,
            'tipo_item'       => $i->tipo_item,
        ])->values()->toArray()
        : [];

    $initial = [
        'paciente_nombre'    => old('paciente_nombre', $proforma->paciente_nombre),
        'paciente_documento' => old('paciente_documento', $proforma->paciente_documento),
        'paciente_telefono'  => old('paciente_telefono', $proforma->paciente_telefono),
        'validez_dias'       => (int) old('validez_dias', $proforma->validez_dias ?? 15),
        'descuento'          => old('descuento', $proforma->descuento > 0 ? number_format($proforma->descuento, 2, '.', '') : ''),
        'observaciones'      => old('observaciones', $proforma->observaciones),
        'items'              => old('items', $itemsModelo),
    ];
@endphp

<div x-data="proformaForm(@js($initial))" class="max-w-5xl mx-auto">

    @if($errors->any())
        <div class="mb-4 px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-red-800 text-sm">
            <p class="font-semibold mb-1">Revisa los siguientes campos:</p>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form action="{{ $action }}" method="POST">
        @csrf
        @if(($method ?? 'POST') !== 'POST') @method($method) @endif

        {{-- ── Datos del paciente ── --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 mb-5">
            <h2 class="text-sm font-bold text-slate-700 uppercase tracking-wide mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                Datos del paciente
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Nombre del paciente <span class="text-red-500">*</span></label>
                    <input type="text" name="paciente_nombre" x-model="paciente_nombre" required maxlength="255"
                           class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                           placeholder="Nombre y apellidos">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">CI / NIT</label>
                    <input type="text" name="paciente_documento" x-model="paciente_documento" maxlength="50"
                           class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                           placeholder="Opcional">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Teléfono</label>
                    <input type="text" name="paciente_telefono" x-model="paciente_telefono" maxlength="50"
                           class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                           placeholder="Opcional">
                </div>
            </div>
        </div>

        {{-- ── Detalle de servicios ── --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 mb-5">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-sm font-bold text-slate-700 uppercase tracking-wide flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    Servicios a cotizar
                </h2>
                <button type="button" @click="agregar()"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-semibold rounded-lg transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Agregar fila
                </button>
            </div>

            {{-- Encabezado de columnas (solo en pantallas medianas+) --}}
            <div class="hidden md:grid grid-cols-12 gap-2 px-1 pb-2 text-[11px] font-semibold text-slate-400 uppercase tracking-wider">
                <div class="col-span-6">Concepto</div>
                <div class="col-span-2 text-center">Cantidad</div>
                <div class="col-span-2 text-right">P. Unitario</div>
                <div class="col-span-2 text-right">Subtotal</div>
            </div>

            <div class="space-y-3">
                <template x-for="(item, idx) in items" :key="idx">
                    <div class="grid grid-cols-12 gap-2 items-start bg-slate-50/60 md:bg-transparent rounded-lg p-2 md:p-0">
                        {{-- Concepto + buscador --}}
                        <div class="col-span-12 md:col-span-6 relative">
                            <label class="md:hidden block text-[11px] font-semibold text-slate-400 mb-1">Concepto</label>
                            <input type="text" :name="`items[${idx}][descripcion]`" x-model="item.descripcion"
                                   @input.debounce.300ms="buscar(idx)" @focus="if(item._resultados.length) item._abierto=true"
                                   @click.away="item._abierto=false" autocomplete="off" required maxlength="255"
                                   class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                   placeholder="Escribe para buscar en el catálogo o teclea libremente…">
                            {{-- código (oculto) --}}
                            <input type="hidden" :name="`items[${idx}][codigo_item]`" :value="item.codigo_item">
                            <input type="hidden" :name="`items[${idx}][tipo_item]`" :value="item.tipo_item">
                            <template x-if="item.codigo_item">
                                <span class="inline-block mt-1 text-[10px] font-mono text-slate-400">Cód: <span x-text="item.codigo_item"></span></span>
                            </template>

                            {{-- Dropdown de resultados --}}
                            <div x-show="item._abierto" x-cloak
                                 class="absolute z-20 mt-1 w-full bg-white rounded-lg shadow-xl border border-slate-200 max-h-64 overflow-y-auto">
                                <template x-for="(res, ri) in item._resultados" :key="ri">
                                    <button type="button" @click="seleccionar(idx, res)"
                                            class="w-full text-left px-3 py-2 hover:bg-emerald-50 border-b border-slate-50 last:border-0">
                                        <div class="flex items-center justify-between gap-2">
                                            <span class="text-sm text-slate-800" x-text="res.descripcion"></span>
                                            <span class="shrink-0 text-[10px] font-semibold px-1.5 py-0.5 rounded bg-slate-100 text-slate-500" x-text="res.grupo"></span>
                                        </div>
                                        <div class="flex items-center justify-between text-[11px] text-slate-400 mt-0.5">
                                            <span class="font-mono" x-text="res.codigo"></span>
                                            <span x-show="res.precio" x-text="'Bs ' + res.precio"></span>
                                        </div>
                                    </button>
                                </template>
                            </div>
                        </div>

                        {{-- Cantidad --}}
                        <div class="col-span-4 md:col-span-2">
                            <label class="md:hidden block text-[11px] font-semibold text-slate-400 mb-1">Cantidad</label>
                            <input type="text" inputmode="decimal" :name="`items[${idx}][cantidad]`" x-model="item.cantidad"
                                   class="w-full px-2 py-2 rounded-lg border border-slate-300 text-sm text-center focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        </div>

                        {{-- Precio unitario --}}
                        <div class="col-span-5 md:col-span-2">
                            <label class="md:hidden block text-[11px] font-semibold text-slate-400 mb-1">P. Unitario</label>
                            <input type="text" inputmode="decimal" :name="`items[${idx}][precio_unitario]`" x-model="item.precio_unitario"
                                   class="w-full px-2 py-2 rounded-lg border border-slate-300 text-sm text-right focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                   placeholder="0.00">
                        </div>

                        {{-- Subtotal + eliminar --}}
                        <div class="col-span-3 md:col-span-2 flex items-center justify-end gap-1 pt-2 md:pt-2">
                            <span class="text-sm font-semibold text-slate-700 tabular-nums" x-text="fmt(subtotal(item))"></span>
                            <button type="button" @click="quitar(idx)" title="Quitar"
                                    class="p-1.5 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        {{-- ── Resumen / observaciones ── --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-5">
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
                <h2 class="text-sm font-bold text-slate-700 uppercase tracking-wide mb-3">Observaciones</h2>
                <textarea name="observaciones" x-model="observaciones" rows="4" maxlength="2000"
                          class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                          placeholder="Notas para el paciente (ej. incluye/excluye, condiciones, etc.)"></textarea>
                <div class="mt-3">
                    <label class="block text-xs font-semibold text-slate-500 mb-1">Validez de la cotización (días)</label>
                    <input type="number" name="validez_dias" x-model="validez_dias" min="1" max="365"
                           class="w-32 px-3 py-2 rounded-lg border border-slate-300 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
                <h2 class="text-sm font-bold text-slate-700 uppercase tracking-wide mb-3">Resumen</h2>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between text-slate-600">
                        <span>Subtotal</span>
                        <span class="font-semibold tabular-nums" x-text="'Bs ' + fmt(totalBruto)"></span>
                    </div>
                    <div class="flex justify-between items-center text-slate-600">
                        <span>Descuento (Bs)</span>
                        <input type="text" inputmode="decimal" name="descuento" x-model="descuento"
                               class="w-28 px-2 py-1.5 rounded-lg border border-slate-300 text-sm text-right focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                               placeholder="0.00">
                    </div>
                    <div class="flex justify-between pt-3 mt-2 border-t border-slate-200">
                        <span class="text-base font-bold text-slate-800">TOTAL</span>
                        <span class="text-xl font-extrabold text-emerald-700 tabular-nums" x-text="'Bs ' + fmt(total)"></span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Acciones ── --}}
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('proformas.index') }}" class="px-4 py-2.5 text-sm font-semibold text-slate-600 hover:text-slate-800">Cancelar</a>
            <button type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg shadow-sm transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                {{ $submitLabel ?? 'Guardar proforma' }}
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    function proformaForm(initial) {
        return {
            paciente_nombre:    initial.paciente_nombre    ?? '',
            paciente_documento: initial.paciente_documento ?? '',
            paciente_telefono:  initial.paciente_telefono  ?? '',
            validez_dias:       initial.validez_dias       ?? 15,
            descuento:          initial.descuento          ?? '',
            observaciones:      initial.observaciones      ?? '',
            items: (initial.items ?? []).map(i => ({
                descripcion:     i.descripcion     ?? '',
                cantidad:        i.cantidad        ?? '1',
                precio_unitario: i.precio_unitario ?? '',
                codigo_item:     i.codigo_item     ?? '',
                tipo_item:       i.tipo_item       ?? '',
                _resultados: [],
                _abierto: false,
            })),

            init() {
                if (this.items.length === 0) this.agregar();
            },

            itemVacio() {
                return { descripcion: '', cantidad: '1', precio_unitario: '', codigo_item: '', tipo_item: '', _resultados: [], _abierto: false };
            },
            agregar() { this.items.push(this.itemVacio()); },
            quitar(idx) {
                this.items.splice(idx, 1);
                if (this.items.length === 0) this.agregar();
            },

            num(v) {
                if (v === null || v === undefined || v === '') return 0;
                const n = parseFloat(String(v).replace(',', '.'));
                return isNaN(n) ? 0 : n;
            },
            subtotal(item) { return this.num(item.cantidad) * this.num(item.precio_unitario); },
            get totalBruto() { return this.items.reduce((acc, i) => acc + this.subtotal(i), 0); },
            get total() {
                const t = this.totalBruto - this.num(this.descuento);
                return t > 0 ? t : 0;
            },
            fmt(n) { return Number(n).toLocaleString('es-BO', { minimumFractionDigits: 2, maximumFractionDigits: 2 }); },

            async buscar(idx) {
                const item = this.items[idx];
                // Al teclear libremente se rompe el vínculo con el código de catálogo.
                item.codigo_item = '';
                item.tipo_item = '';
                const q = (item.descripcion || '').trim();
                if (q.length < 2) { item._resultados = []; item._abierto = false; return; }
                try {
                    const r = await fetch(`{{ route('proformas.buscar-catalogo') }}?q=` + encodeURIComponent(q));
                    item._resultados = await r.json();
                    item._abierto = item._resultados.length > 0;
                } catch (e) {
                    item._resultados = []; item._abierto = false;
                }
            },
            seleccionar(idx, res) {
                const item = this.items[idx];
                item.descripcion = res.descripcion;
                item.codigo_item = res.codigo ?? '';
                item.tipo_item = res.tipo_item ?? '';
                if (res.precio !== null && res.precio !== undefined && res.precio !== '') {
                    item.precio_unitario = res.precio;
                }
                item._resultados = [];
                item._abierto = false;
            },
        };
    }
</script>
@endpush
