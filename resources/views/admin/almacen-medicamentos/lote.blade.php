@extends('layouts.app')

@section('title', 'Registrar Lote')

@section('content')
<div class="min-h-screen bg-gray-50 p-6"
     x-data="loteForm()">
    <div class="max-w-4xl mx-auto">
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Registrar Lote</h1>
                <p class="text-gray-600 mt-1">Ingresa mercadería a un medicamento/insumo que ya existe en el catálogo</p>
            </div>
            <a href="{{ route('admin.almacen-medicamentos.index') }}"
               class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Volver
            </a>
        </div>

        @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">
            <ul class="list-disc list-inside space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
        @endif

        <form method="POST" action="{{ route('admin.almacen-medicamentos.lote.store') }}">
            @csrf
            <input type="hidden" name="catalogo_id" :value="seleccionado?.id || ''">

            <!-- Sección 1: Elegir medicamento existente -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                <h3 class="text-base font-semibold text-gray-900 mb-4">1. Medicamento / Insumo</h3>

                <!-- Seleccionado -->
                <template x-if="seleccionado">
                    <div class="flex items-center justify-between bg-blue-50 border border-blue-200 rounded-lg p-3 mb-2">
                        <div>
                            <p class="font-semibold text-gray-900" x-text="seleccionado.nombre"></p>
                            <p class="text-xs text-gray-500">
                                <span x-text="seleccionado.tipo === 'insumo' ? 'Insumo' : 'Medicamento'"></span>
                                · <span x-text="seleccionado.unidad"></span>
                                <template x-if="seleccionado.atc"><span> · ATC <span x-text="seleccionado.atc"></span></span></template>
                            </p>
                        </div>
                        <button type="button" @click="limpiar()" class="text-sm text-blue-600 hover:text-blue-800 font-medium">Cambiar</button>
                    </div>
                </template>

                <!-- Buscador -->
                <div x-show="!seleccionado" class="relative">
                    <input type="text" x-model="busqueda" @focus="abierto = true" placeholder="Buscar por nombre… (ej. Paracetamol)"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg" autocomplete="off">
                    <div x-show="abierto && busqueda.length >= 2" @click.outside="abierto = false"
                         class="absolute z-20 mt-1 w-full bg-white border border-gray-200 rounded-lg shadow-lg max-h-72 overflow-y-auto">
                        <template x-for="c in filtrados" :key="c.id">
                            <button type="button" @click="elegir(c)"
                                    class="w-full text-left px-3 py-2 hover:bg-blue-50 border-b border-gray-100 last:border-0">
                                <span class="text-sm text-gray-900" x-text="c.nombre"></span>
                                <span class="text-xs px-1.5 py-0.5 rounded ml-1"
                                      :class="c.tipo === 'insumo' ? 'bg-gray-100 text-gray-600' : 'bg-green-100 text-green-700'"
                                      x-text="c.tipo === 'insumo' ? 'Insumo' : 'Med'"></span>
                            </button>
                        </template>
                        <p x-show="filtrados.length === 0" class="px-3 py-3 text-sm text-gray-500">Sin coincidencias.</p>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">
                        ¿No está en la lista?
                        <a href="{{ route('admin.almacen-medicamentos.create') }}" class="text-blue-600 hover:underline font-medium">Crear medicamento nuevo →</a>
                    </p>
                </div>
            </div>

            <!-- Sección 2: Datos del lote -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6" :class="!seleccionado && 'opacity-50 pointer-events-none'">
                <h3 class="text-base font-semibold text-gray-900 mb-5">2. Datos del Lote e Ingreso</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Área destino <span class="text-red-500">*</span></label>
                        <select name="ubicacion" required class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                            @foreach($areas as $key => $label)
                                <option value="{{ $key }}" {{ $key === 'central' ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Código de lote</label>
                        <input type="text" name="codigo_lote" value="{{ old('codigo_lote') }}" maxlength="100"
                               placeholder="Ej: L-2026-001" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">N° lote (fabricante)</label>
                        <input type="text" name="numero_lote_fabricante" value="{{ old('numero_lote_fabricante') }}" maxlength="150"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Proveedor</label>
                        <input type="text" name="proveedor" value="{{ old('proveedor') }}" maxlength="150"
                               placeholder="Distribuidora…" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Laboratorio</label>
                        <input type="text" name="laboratorio" value="{{ old('laboratorio') }}" maxlength="150"
                               placeholder="Genérico, Bagó…" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de vencimiento</label>
                        <input type="date" name="fecha_vencimiento" value="{{ old('fecha_vencimiento') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Precio de compra (Bs)</label>
                        <input type="number" name="precio_compra" x-model="precioCompra" @input="calcVentaDesdeGanancia()" @blur="precioCompra = round2(precioCompra); calcVentaDesdeGanancia()" step="0.01" min="0"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ganancia (Bs)</label>
                        <input type="number" name="ganancia" x-model="ganancia" @input="calcVentaDesdeGanancia()" @blur="ganancia = round2(ganancia); calcVentaDesdeGanancia()" step="0.01" min="0"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Precio de venta (Bs)</label>
                        <input type="number" name="precio_venta" x-model="precioVenta" @input="calcGananciaDesdeVenta()" @blur="precioVenta = round2(precioVenta); calcGananciaDesdeVenta()" step="0.01" min="0"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        <p class="text-xs text-gray-500 mt-1">La ganancia y el precio de venta se ajustan entre sí.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cantidad a ingresar <span class="text-red-500">*</span></label>
                        <input type="number" name="cantidad" value="{{ old('cantidad', 0) }}" @blur="$event.target.value = soloEntero($event.target.value)" step="1" min="0" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cantidad recibida</label>
                        <input type="number" name="cantidad_recibida" value="{{ old('cantidad_recibida') }}" @blur="$event.target.value = soloEntero($event.target.value)" step="1" min="0"
                               placeholder="= cantidad" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Stock mínimo <span class="text-red-500">*</span></label>
                        <input type="number" name="stock_minimo" value="{{ old('stock_minimo', 0) }}" @blur="$event.target.value = soloEntero($event.target.value)" step="1" min="0" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between">
                <p class="text-sm text-gray-500">El lote se agrega al medicamento seleccionado, sin crear un catálogo nuevo.</p>
                <button type="submit" :disabled="!seleccionado"
                        class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors disabled:opacity-40 disabled:cursor-not-allowed">
                    Registrar Lote
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function loteForm() {
    return {
        catalogos: @json($catalogos),
        busqueda: '',
        abierto: false,
        seleccionado: null,
        precioCompra: {{ old('precio_compra', 0) }},
        ganancia: {{ old('ganancia', 0) }},
        precioVenta: '0.00',
        init() {
            const pre = {{ $preseleccion ?? 'null' }};
            if (pre) this.seleccionado = this.catalogos.find(c => c.id === pre) || null;
            this.calcVentaDesdeGanancia(); // inicializa venta a partir de compra y ganancia
        },
        get filtrados() {
            const q = this.busqueda.toLowerCase().trim();
            if (q.length < 2) return [];
            return this.catalogos.filter(c => c.nombre.toLowerCase().includes(q)).slice(0, 50);
        },
        // venta = compra + ganancia (Bs). Se dispara al editar compra o ganancia.
        calcVentaDesdeGanancia() {
            const p = parseFloat(this.precioCompra) || 0;
            const g = parseFloat(this.ganancia) || 0;
            this.precioVenta = this.round2(p + g);
        },
        // ganancia = venta - compra. Se dispara al editar la venta.
        calcGananciaDesdeVenta() {
            const p = parseFloat(this.precioCompra) || 0;
            const v = parseFloat(this.precioVenta) || 0;
            this.ganancia = this.round2(v - p);
        },
        // Redondeo half-up a 2 decimales (espeja Money::round del backend).
        round2(v) {
            const n = parseFloat(String(v).replace(',', '.'));
            if (isNaN(n)) return '0.00';
            return (Math.round((n + Number.EPSILON) * 100) / 100).toFixed(2);
        },
        // Cantidades: solo enteros >= 0. Vacío se conserva (campos opcionales).
        soloEntero(v) {
            if (v === '' || v === null) return '';
            const n = parseInt(v, 10);
            return (isNaN(n) || n < 0) ? '' : n;
        },
        elegir(c) { this.seleccionado = c; this.abierto = false; this.busqueda = ''; },
        limpiar() { this.seleccionado = null; },
    };
}
</script>
@endsection
