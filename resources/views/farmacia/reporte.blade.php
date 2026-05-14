@extends('layouts.app')

@push('head')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
@endpush

@section('content')
<div class="p-6 bg-[#f1f5f9] min-h-screen font-sans" x-data="reporteFarmacia()" x-init="init()">

    {{-- HEADER --}}
    <div class="flex flex-wrap gap-4 justify-between items-start mb-6">
        <div>
            <h1 class="text-2xl font-black text-slate-800">Reportes Farmacia</h1>
            <p class="text-sm text-slate-500 mt-0.5" x-text="periodoLabel">Cargando...</p>
        </div>
        <div class="flex flex-wrap gap-2 items-center">
            <a :href="exportUrl" target="_blank"
               class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Exportar Excel
            </a>
            <a :href="pdfUrl" target="_blank"
               class="inline-flex items-center gap-2 px-4 py-2 bg-slate-600 hover:bg-slate-700 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                Generar PDF
            </a>
        </div>
    </div>

    {{-- FILTROS --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4 mb-6">
        <div class="flex flex-wrap gap-2 items-end">
            <div class="flex gap-1.5 flex-wrap">
                <template x-for="(label, key) in periodos" :key="key">
                    <button @click="setPeriodo(key)"
                            :class="periodo === key && !usandoRango
                                ? 'bg-blue-600 text-white shadow-sm'
                                : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                            class="px-3 py-1.5 rounded-lg text-sm font-semibold transition-colors"
                            x-text="label">
                    </button>
                </template>
            </div>
            <div class="flex items-center gap-2 ml-auto flex-wrap">
                <span class="text-xs text-slate-400 font-medium">Rango personalizado:</span>
                <input type="date" x-model="desde"
                       class="border border-slate-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200"/>
                <span class="text-slate-400 text-sm">—</span>
                <input type="date" x-model="hasta"
                       class="border border-slate-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200"/>
                <button @click="aplicarRango()"
                        class="px-4 py-1.5 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700 transition-colors">
                    Aplicar
                </button>
            </div>
        </div>
        <div x-show="loading" class="mt-3 flex items-center gap-2 text-sm text-blue-600">
            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
            Cargando datos...
        </div>
    </div>

    {{-- KPI CARDS - FILA 1: VENTAS --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Total Ventas</p>
            <p class="text-3xl font-black text-slate-800" x-text="fmt.num(data.totalVentas)">—</p>
            <p class="text-xs text-slate-400 mt-1">En el período</p>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Ingresos Totales</p>
            <p class="text-3xl font-black text-emerald-600" x-text="'Bs. ' + fmt.money(data.ingresosTotales)">—</p>
            <p class="text-xs text-slate-400 mt-1" x-text="'Solo completadas: Bs. ' + fmt.money(data.ingresosCompletadas)"></p>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Ticket Promedio</p>
            <p class="text-3xl font-black text-slate-800" x-text="'Bs. ' + fmt.money(data.promedioPorVenta)">—</p>
            <p class="text-xs text-slate-400 mt-1">Por venta</p>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Completadas / Anuladas</p>
            <div class="flex items-baseline gap-2">
                <p class="text-3xl font-black text-emerald-600" x-text="fmt.num(data.completadas)">—</p>
                <span class="text-slate-400 font-bold text-lg">/</span>
                <p class="text-3xl font-black text-red-500" x-text="fmt.num(data.anuladas)">—</p>
            </div>
        </div>
    </div>

    {{-- KPI CARDS - FILA 2: INVENTARIO (server-side) --}}
    <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 flex justify-between items-center">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Valor Inventario</p>
                <p class="text-2xl font-black text-slate-800">Bs. {{ number_format($valorInventario, 2) }}</p>
                <p class="text-xs text-slate-400 mt-1">Stock actual × precio</p>
            </div>
            <div class="bg-blue-100 p-3 rounded-xl">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
            </div>
        </div>
        <div class="bg-white rounded-xl border {{ $alertasStock->count() > 0 ? 'border-orange-200 bg-orange-50' : 'border-slate-200' }} shadow-sm p-5 flex justify-between items-center">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Stock Bajo Mínimo</p>
                <p class="text-2xl font-black {{ $alertasStock->count() > 0 ? 'text-orange-600' : 'text-slate-800' }}">{{ $alertasStock->count() }} productos</p>
                <p class="text-xs text-slate-400 mt-1">Requieren reposición</p>
            </div>
            <div class="bg-orange-100 p-3 rounded-xl">
                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
        </div>
        <div class="bg-white rounded-xl border {{ $alertasVencimiento->count() > 0 ? 'border-red-200 bg-red-50' : 'border-slate-200' }} shadow-sm p-5 flex justify-between items-center">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Próx. a Vencer (30d)</p>
                <p class="text-2xl font-black {{ $alertasVencimiento->count() > 0 ? 'text-red-600' : 'text-slate-800' }}">{{ $alertasVencimiento->count() }} productos</p>
                <p class="text-xs text-slate-400 mt-1">En los próximos 30 días</p>
            </div>
            <div class="bg-red-100 p-3 rounded-xl">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
        </div>
    </div>

    {{-- GRÁFICOS FILA 1 --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-4">
        {{-- Ingresos por día --}}
        <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200 shadow-sm p-6">
            <h2 class="text-sm font-bold text-slate-700 uppercase tracking-wider mb-4">Ingresos por Día</h2>
            <div class="relative h-56">
                <canvas id="chartIngresos"></canvas>
                <div x-show="!data.ventasPorDia || data.ventasPorDia.length === 0"
                     class="absolute inset-0 flex items-center justify-center text-slate-400 text-sm">
                    Sin datos en el período
                </div>
            </div>
        </div>
        {{-- Método de pago --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
            <h2 class="text-sm font-bold text-slate-700 uppercase tracking-wider mb-4">Método de Pago</h2>
            <div class="relative h-56">
                <canvas id="chartMetodoPago"></canvas>
                <div x-show="!data.ventasPorMetodoPago || data.ventasPorMetodoPago.length === 0"
                     class="absolute inset-0 flex items-center justify-center text-slate-400 text-sm">
                    Sin datos
                </div>
            </div>
        </div>
    </div>

    {{-- GRÁFICOS FILA 2 --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">
        {{-- Ventas por vendedor --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
            <h2 class="text-sm font-bold text-slate-700 uppercase tracking-wider mb-4">Ingresos por Vendedor</h2>
            <div class="relative h-56">
                <canvas id="chartVendedor"></canvas>
                <div x-show="!data.ventasPorVendedor || data.ventasPorVendedor.length === 0"
                     class="absolute inset-0 flex items-center justify-center text-slate-400 text-sm">
                    Sin datos
                </div>
            </div>
        </div>
        {{-- Top productos --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
            <h2 class="text-sm font-bold text-slate-700 uppercase tracking-wider mb-4">Top 15 Productos Más Vendidos</h2>
            <div class="overflow-y-auto max-h-56">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-xs text-slate-400 uppercase">
                            <th class="text-left pb-2 pr-3 w-6">#</th>
                            <th class="text-left pb-2">Producto</th>
                            <th class="text-right pb-2 pr-3">Cant.</th>
                            <th class="text-right pb-2">Total (Bs.)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(p, i) in data.productosMasVendidos" :key="i">
                            <tr class="border-t border-slate-50 hover:bg-slate-50">
                                <td class="py-1.5 pr-3 text-slate-400 font-medium" x-text="i+1"></td>
                                <td class="py-1.5 text-slate-700 font-medium truncate max-w-[180px]" x-text="p.nombre_producto"></td>
                                <td class="py-1.5 pr-3 text-right text-slate-600" x-text="p.total_vendido"></td>
                                <td class="py-1.5 text-right font-bold text-emerald-600" x-text="fmt.money(p.total_ingresos)"></td>
                            </tr>
                        </template>
                        <tr x-show="!data.productosMasVendidos || data.productosMasVendidos.length === 0">
                            <td colspan="4" class="py-8 text-center text-slate-400">Sin datos</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- TABLA VENTAS DETALLADAS --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm mb-6">
        <div class="p-5 border-b border-slate-100 flex flex-wrap gap-3 justify-between items-center">
            <div>
                <h2 class="text-sm font-bold text-slate-700 uppercase tracking-wider">Ventas Detalladas</h2>
                <p class="text-xs text-slate-400 mt-0.5" x-text="'Mostrando ' + ventasFiltradas.length + ' registros'"></p>
            </div>
            <div class="flex gap-2 items-center">
                <input type="text" x-model="busqueda" placeholder="Buscar por vendedor, cliente, producto..."
                       class="border border-slate-300 rounded-lg px-3 py-1.5 text-sm w-72 focus:outline-none focus:ring-2 focus:ring-blue-200"/>
                <select x-model="filtroEstado" class="border border-slate-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200">
                    <option value="">Todos los estados</option>
                    <option value="COMPLETADA">Completadas</option>
                    <option value="ANULADA">Anuladas</option>
                    <option value="PENDIENTE">Pendientes</option>
                </select>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="text-left px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Código</th>
                        <th class="text-left px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Fecha</th>
                        <th class="text-left px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Hora</th>
                        <th class="text-left px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Vendedor</th>
                        <th class="text-left px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Cliente</th>
                        <th class="text-left px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Productos</th>
                        <th class="text-right px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Items</th>
                        <th class="text-left px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Método</th>
                        <th class="text-right px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Total (Bs.)</th>
                        <th class="text-center px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="(v, i) in ventasPaginadas" :key="v.codigo_venta">
                        <tr class="border-t border-slate-50 hover:bg-slate-50 transition-colors">
                            <td class="px-4 py-3 font-mono text-xs text-slate-500" x-text="v.codigo_venta"></td>
                            <td class="px-4 py-3 text-slate-700 whitespace-nowrap" x-text="v.fecha"></td>
                            <td class="px-4 py-3 text-slate-500 whitespace-nowrap" x-text="v.hora"></td>
                            <td class="px-4 py-3 font-semibold text-slate-700" x-text="v.vendedor"></td>
                            <td class="px-4 py-3 text-slate-600" x-text="v.cliente"></td>
                            <td class="px-4 py-3 text-slate-500 max-w-xs">
                                <span class="truncate block" x-text="v.productos" :title="v.productos"></span>
                            </td>
                            <td class="px-4 py-3 text-right text-slate-500" x-text="v.total_items"></td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold capitalize"
                                      :class="{
                                          'bg-blue-100 text-blue-700': v.metodo_pago === 'efectivo',
                                          'bg-purple-100 text-purple-700': v.metodo_pago === 'tarjeta',
                                          'bg-teal-100 text-teal-700': v.metodo_pago === 'transferencia',
                                          'bg-yellow-100 text-yellow-700': v.metodo_pago === 'qr',
                                          'bg-slate-100 text-slate-600': !['efectivo','tarjeta','transferencia','qr'].includes(v.metodo_pago)
                                      }"
                                      x-text="v.metodo_pago">
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right font-bold text-slate-800" x-text="fmt.money(v.total)"></td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-0.5 rounded-full text-xs font-bold"
                                      :class="{
                                          'bg-emerald-100 text-emerald-700': v.estado === 'COMPLETADA',
                                          'bg-red-100 text-red-700': v.estado === 'ANULADA',
                                          'bg-amber-100 text-amber-700': v.estado === 'PENDIENTE'
                                      }"
                                      x-text="v.estado">
                                </span>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="ventasFiltradas.length === 0">
                        <td colspan="10" class="px-4 py-12 text-center text-slate-400">Sin resultados</td>
                    </tr>
                </tbody>
                <tfoot x-show="ventasFiltradas.length > 0" class="bg-slate-50 border-t-2 border-slate-200">
                    <tr>
                        <td colspan="8" class="px-4 py-3 text-sm font-bold text-slate-600 text-right">
                            Total visible (<span x-text="ventasFiltradas.length"></span> ventas):
                        </td>
                        <td class="px-4 py-3 text-right font-black text-emerald-700 text-sm"
                            x-text="'Bs. ' + fmt.money(ventasFiltradas.reduce((s,v) => s + v.total, 0))">
                        </td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        {{-- Paginación --}}
        <div class="p-4 border-t border-slate-100 flex items-center justify-between">
            <p class="text-xs text-slate-400">
                Página <span x-text="paginaActual"></span> de <span x-text="totalPaginas"></span>
                &nbsp;·&nbsp; mostrando <span x-text="Math.min(pagSize, ventasFiltradas.length - (paginaActual-1)*pagSize)"></span> de <span x-text="ventasFiltradas.length"></span>
            </p>
            <div class="flex gap-1">
                <button @click="paginaActual = Math.max(1, paginaActual - 1)"
                        :disabled="paginaActual === 1"
                        class="px-3 py-1 rounded-lg text-sm font-medium border border-slate-200 disabled:opacity-40 hover:bg-slate-50 transition-colors">
                    ← Anterior
                </button>
                <button @click="paginaActual = Math.min(totalPaginas, paginaActual + 1)"
                        :disabled="paginaActual >= totalPaginas"
                        class="px-3 py-1 rounded-lg text-sm font-medium border border-slate-200 disabled:opacity-40 hover:bg-slate-50 transition-colors">
                    Siguiente →
                </button>
            </div>
        </div>
    </div>

    {{-- ALERTAS STOCK E INVENTARIO --}}
    @if($alertasStock->count() > 0 || $alertasVencimiento->count() > 0)
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

        @if($alertasStock->count() > 0)
        <div class="bg-white rounded-xl border border-orange-200 shadow-sm">
            <div class="p-5 border-b border-orange-100">
                <h2 class="text-sm font-bold text-orange-700 uppercase tracking-wider">Productos bajo stock mínimo</h2>
            </div>
            <div class="overflow-x-auto max-h-64 overflow-y-auto">
                <table class="w-full text-sm">
                    <thead class="bg-orange-50 sticky top-0">
                        <tr>
                            <th class="text-left px-4 py-2 text-xs font-bold text-orange-600">Producto</th>
                            <th class="text-right px-4 py-2 text-xs font-bold text-orange-600">Disponible</th>
                            <th class="text-right px-4 py-2 text-xs font-bold text-orange-600">Mínimo</th>
                            <th class="text-right px-4 py-2 text-xs font-bold text-orange-600">Déficit</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($alertasStock as $item)
                        <tr class="border-t border-orange-50 hover:bg-orange-50/50">
                            <td class="px-4 py-2 font-medium text-slate-700">
                                {{ $item->medicamento?->nombre ?? $item->codigo_item }}
                            </td>
                            <td class="px-4 py-2 text-right font-bold text-red-600">{{ $item->stock_disponible }}</td>
                            <td class="px-4 py-2 text-right text-slate-500">{{ $item->stock_minimo }}</td>
                            <td class="px-4 py-2 text-right font-bold text-orange-600">
                                {{ $item->stock_minimo - $item->stock_disponible }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        @if($alertasVencimiento->count() > 0)
        <div class="bg-white rounded-xl border border-red-200 shadow-sm">
            <div class="p-5 border-b border-red-100">
                <h2 class="text-sm font-bold text-red-700 uppercase tracking-wider">Próximos a vencer (30 días)</h2>
            </div>
            <div class="overflow-x-auto max-h-64 overflow-y-auto">
                <table class="w-full text-sm">
                    <thead class="bg-red-50 sticky top-0">
                        <tr>
                            <th class="text-left px-4 py-2 text-xs font-bold text-red-600">Producto</th>
                            <th class="text-left px-4 py-2 text-xs font-bold text-red-600">Lote</th>
                            <th class="text-right px-4 py-2 text-xs font-bold text-red-600">Stock</th>
                            <th class="text-right px-4 py-2 text-xs font-bold text-red-600">Vence</th>
                            <th class="text-right px-4 py-2 text-xs font-bold text-red-600">Días</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($alertasVencimiento->sortBy('fecha_vencimiento') as $item)
                        @php $dias = \Carbon\Carbon::today()->diffInDays($item->fecha_vencimiento, false); @endphp
                        <tr class="border-t border-red-50 hover:bg-red-50/50">
                            <td class="px-4 py-2 font-medium text-slate-700">
                                {{ $item->medicamento?->nombre ?? $item->codigo_item }}
                            </td>
                            <td class="px-4 py-2 text-slate-500 text-xs">{{ $item->lote ?? '—' }}</td>
                            <td class="px-4 py-2 text-right text-slate-600">{{ $item->stock_disponible }}</td>
                            <td class="px-4 py-2 text-right font-medium text-red-600">
                                {{ \Carbon\Carbon::parse($item->fecha_vencimiento)->format('d/m/Y') }}
                            </td>
                            <td class="px-4 py-2 text-right">
                                <span class="px-2 py-0.5 rounded-full text-xs font-bold
                                    {{ $dias <= 7 ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700' }}">
                                    {{ $dias }}d
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

    </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
function reporteFarmacia() {
    return {
        periodo: 'mes',
        usandoRango: false,
        desde: '',
        hasta: '',
        loading: false,
        periodoLabel: '',
        busqueda: '',
        filtroEstado: '',
        paginaActual: 1,
        pagSize: 25,

        periodos: {
            hoy:   'Hoy',
            '7dias': '7 días',
            mes:   'Este mes',
            anio:  'Este año',
            todo:  'Todo',
        },

        data: {
            totalVentas: 0,
            completadas: 0,
            anuladas: 0,
            ingresosTotales: 0,
            ingresosCompletadas: 0,
            promedioPorVenta: 0,
            ventasPorDia: [],
            ventasPorMetodoPago: [],
            ventasPorVendedor: [],
            productosMasVendidos: [],
            ventasDetalladas: [],
        },

        charts: { ingresos: null, metodoPago: null, vendedor: null },

        fmt: {
            money: v => isNaN(v) ? '0.00' : Number(v).toLocaleString('es-BO', {minimumFractionDigits: 2, maximumFractionDigits: 2}),
            num:   v => isNaN(v) ? '0'    : Number(v).toLocaleString('es-BO'),
        },

        get ventasFiltradas() {
            const q = this.busqueda.toLowerCase().trim();
            const est = this.filtroEstado;
            return this.data.ventasDetalladas.filter(v => {
                const matchQ = !q || [v.vendedor, v.cliente, v.productos, v.codigo_venta]
                    .some(s => s && s.toLowerCase().includes(q));
                const matchEst = !est || v.estado === est;
                return matchQ && matchEst;
            });
        },

        get totalPaginas() {
            return Math.max(1, Math.ceil(this.ventasFiltradas.length / this.pagSize));
        },

        get ventasPaginadas() {
            const ini = (this.paginaActual - 1) * this.pagSize;
            return this.ventasFiltradas.slice(ini, ini + this.pagSize);
        },

        init() {
            this.cargar();
        },

        setPeriodo(p) {
            this.periodo = p;
            this.usandoRango = false;
            this.paginaActual = 1;
            this.cargar();
        },

        aplicarRango() {
            if (!this.desde || !this.hasta) return;
            this.usandoRango = true;
            this.paginaActual = 1;
            this.cargar();
        },

        async cargar() {
            this.loading = true;
            try {
                const params = new URLSearchParams();
                if (this.usandoRango && this.desde && this.hasta) {
                    params.set('desde', this.desde);
                    params.set('hasta', this.hasta);
                } else {
                    params.set('periodo', this.periodo);
                }
                const res = await fetch(`/farmacia/reporte/datos?${params}`);
                if (!res.ok) throw new Error('Error ' + res.status);
                const json = await res.json();
                this.data = json;
                this.periodoLabel = json.periodo_label;
                this.$nextTick(() => this.renderCharts());
            } catch (e) {
                console.error('Error cargando reporte:', e);
            } finally {
                this.loading = false;
            }
        },

        renderCharts() {
            this.renderIngresos();
            this.renderMetodoPago();
            this.renderVendedor();
        },

        renderIngresos() {
            const el = document.getElementById('chartIngresos');
            if (!el) return;
            if (this.charts.ingresos) this.charts.ingresos.destroy();
            const dias = this.data.ventasPorDia || [];
            this.charts.ingresos = new Chart(el, {
                type: 'line',
                data: {
                    labels: dias.map(d => d.fecha),
                    datasets: [{
                        label: 'Ingresos (Bs.)',
                        data: dias.map(d => parseFloat(d.total_ingresos) || 0),
                        backgroundColor: 'rgba(37,99,235,0.08)',
                        borderColor: '#2563EB',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#2563EB',
                        pointRadius: 3,
                    }, {
                        label: 'N° Ventas',
                        data: dias.map(d => parseInt(d.total_ventas) || 0),
                        borderColor: '#10B981',
                        borderWidth: 2,
                        borderDash: [4, 4],
                        fill: false,
                        tension: 0.4,
                        pointBackgroundColor: '#10B981',
                        pointRadius: 3,
                        yAxisID: 'y2',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: { position: 'top', labels: { font: { size: 11 }, boxWidth: 12 } },
                        tooltip: {
                            callbacks: {
                                label: ctx => ctx.dataset.yAxisID === 'y2'
                                    ? `Ventas: ${ctx.raw}`
                                    : `Bs. ${ctx.raw.toLocaleString('es-BO', {minimumFractionDigits: 2})}`
                            }
                        }
                    },
                    scales: {
                        x: { grid: { display: false }, ticks: { font: { size: 11 } } },
                        y: { grid: { color: '#f1f5f9' }, ticks: { font: { size: 11 } } },
                        y2: { position: 'right', grid: { display: false }, ticks: { font: { size: 11 } } },
                    }
                }
            });
        },

        renderMetodoPago() {
            const el = document.getElementById('chartMetodoPago');
            if (!el) return;
            if (this.charts.metodoPago) this.charts.metodoPago.destroy();
            const metodos = this.data.ventasPorMetodoPago || [];
            if (!metodos.length) return;
            const colors = ['#2563EB','#10B981','#F59E0B','#8B5CF6','#EF4444','#06B6D4'];
            this.charts.metodoPago = new Chart(el, {
                type: 'doughnut',
                data: {
                    labels: metodos.map(m => m.metodo_pago ? (m.metodo_pago.charAt(0).toUpperCase() + m.metodo_pago.slice(1)) : '—'),
                    datasets: [{
                        data: metodos.map(m => parseFloat(m.total_ingresos) || 0),
                        backgroundColor: colors,
                        borderWidth: 2,
                        borderColor: '#fff',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom', labels: { font: { size: 11 }, boxWidth: 12 } },
                        tooltip: {
                            callbacks: {
                                label: ctx => `Bs. ${parseFloat(ctx.raw).toLocaleString('es-BO', {minimumFractionDigits:2})} (${ctx.label})`
                            }
                        }
                    },
                    cutout: '62%',
                }
            });
        },

        renderVendedor() {
            const el = document.getElementById('chartVendedor');
            if (!el) return;
            if (this.charts.vendedor) this.charts.vendedor.destroy();
            const vendedores = this.data.ventasPorVendedor || [];
            if (!vendedores.length) return;
            this.charts.vendedor = new Chart(el, {
                type: 'bar',
                data: {
                    labels: vendedores.map(v => v.vendedor),
                    datasets: [{
                        label: 'Ingresos (Bs.)',
                        data: vendedores.map(v => parseFloat(v.total_ingresos) || 0),
                        backgroundColor: '#2563EB',
                        borderRadius: 6,
                    }, {
                        label: 'N° Ventas',
                        data: vendedores.map(v => parseInt(v.total_ventas) || 0),
                        backgroundColor: '#10B981',
                        borderRadius: 6,
                        yAxisID: 'y2',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: { position: 'top', labels: { font: { size: 11 }, boxWidth: 12 } },
                    },
                    scales: {
                        x: { grid: { display: false }, ticks: { font: { size: 11 } } },
                        y: { grid: { color: '#f1f5f9' }, ticks: { font: { size: 11 } } },
                        y2: { position: 'right', grid: { display: false }, ticks: { font: { size: 11 } } },
                    }
                }
            });
        },

        get exportUrl() {
            return '/farmacia/reporte/exportar?' + this._filtroParams();
        },

        get pdfUrl() {
            return '/farmacia/reporte/pdf?' + this._filtroParams();
        },

        _filtroParams() {
            const params = new URLSearchParams();
            if (this.usandoRango && this.desde && this.hasta) {
                params.set('desde', this.desde);
                params.set('hasta', this.hasta);
            } else {
                params.set('periodo', this.periodo);
            }
            return params.toString();
        },
    };
}
</script>
@endpush
