@extends('layouts.app')

@section('content')
<div class="p-8 bg-[#f8fafc] min-h-screen font-sans" x-data="contabilidad()" x-init="cargar()">
    <div class="w-full max-w-7xl mx-auto">
        <!-- Header -->
        <div class="flex flex-wrap justify-between items-center gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Contabilidad</h1>
                <p class="text-gray-500 text-sm">Libro de caja — ingresos automáticos y egresos manuales</p>
            </div>
            <div class="flex items-end gap-2">
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Desde</label>
                    <input type="date" x-model="filtros.fecha_inicio" @change="cargar()"
                        class="border-gray-300 rounded-md text-sm">
                </div>
                <div>
                    <label class="block text-xs text-gray-500 mb-1">Hasta</label>
                    <input type="date" x-model="filtros.fecha_fin" @change="cargar()"
                        class="border-gray-300 rounded-md text-sm">
                </div>
                <a :href="urlExportar()"
                    class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-md h-[38px]">
                    Exportar Excel
                </a>
                <a href="{{ route('caja.gestion.index') }}"
                    class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-md h-[38px]">
                    Volver
                </a>
            </div>
        </div>

        <!-- Tarjetas resumen -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-white shadow-sm rounded-lg p-5 border-l-4 border-green-500">
                <p class="text-xs font-medium text-gray-500 uppercase">Total Ingresos</p>
                <p class="text-2xl font-bold text-green-600">Bs. <span x-text="fmt(totales.ingresos)"></span></p>
            </div>
            <div class="bg-white shadow-sm rounded-lg p-5 border-l-4 border-red-500">
                <p class="text-xs font-medium text-gray-500 uppercase">Total Egresos</p>
                <p class="text-2xl font-bold text-red-600">Bs. <span x-text="fmt(totales.egresos)"></span></p>
            </div>
            <div class="bg-white shadow-sm rounded-lg p-5 border-l-4"
                :class="parseFloat(totales.saldo) >= 0 ? 'border-blue-500' : 'border-orange-500'">
                <p class="text-xs font-medium text-gray-500 uppercase">Saldo Neto</p>
                <p class="text-2xl font-bold" :class="parseFloat(totales.saldo) >= 0 ? 'text-blue-600' : 'text-orange-600'">
                    Bs. <span x-text="fmt(totales.saldo)"></span>
                </p>
            </div>
        </div>

        <!-- Gráfico ingresos vs egresos -->
        <div class="bg-white shadow-sm rounded-lg mb-6">
            <div class="p-4 border-b border-gray-200 flex items-center gap-4">
                <h3 class="text-lg font-medium text-gray-900">Ingresos vs Egresos por día</h3>
                <div class="flex items-center gap-3 text-xs text-gray-500">
                    <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-sm bg-green-500"></span>Ingresos caja</span>
                    <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-sm bg-blue-500"></span>Ingresos farmacia</span>
                    <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-sm bg-red-500"></span>Egresos</span>
                </div>
            </div>
            <div class="p-4">
                <div class="h-72">
                    <canvas id="graficoFlujo"></canvas>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Formulario egreso -->
            <div class="lg:col-span-1">
                <div class="bg-white shadow-sm rounded-lg">
                    <div class="p-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Registrar Egreso</h3>
                    </div>
                    <form @submit.prevent="guardarEgreso()" class="p-4 space-y-3">
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Fecha</label>
                            <input type="date" x-model="form.fecha" required class="w-full border-gray-300 rounded-md text-sm">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Categoría</label>
                            <select x-model="form.categoria" required class="w-full border-gray-300 rounded-md text-sm">
                                <option value="">Seleccionar...</option>
                                @foreach ($categorias as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Descripción</label>
                            <input type="text" x-model="form.descripcion" required maxlength="255"
                                class="w-full border-gray-300 rounded-md text-sm" placeholder="Ej: Pago sueldo enfermería">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Monto (Bs)</label>
                            <input type="text" inputmode="decimal" x-model="form.monto" required
                                class="w-full border-gray-300 rounded-md text-sm" placeholder="0.00">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Método de pago</label>
                            <select x-model="form.metodo_pago" required class="w-full border-gray-300 rounded-md text-sm">
                                <option value="efectivo">Efectivo</option>
                                <option value="transferencia">Transferencia</option>
                                <option value="cheque">Cheque</option>
                                <option value="tarjeta">Tarjeta</option>
                                <option value="qr">QR</option>
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Proveedor (opc.)</label>
                                <input type="text" x-model="form.proveedor" maxlength="255"
                                    class="w-full border-gray-300 rounded-md text-sm">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Comprobante (opc.)</label>
                                <input type="text" x-model="form.comprobante_nro" maxlength="50"
                                    class="w-full border-gray-300 rounded-md text-sm">
                            </div>
                        </div>
                        <button type="submit" :disabled="guardando"
                            class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 disabled:opacity-50 text-white text-sm font-medium rounded-md">
                            <span x-text="guardando ? 'Guardando...' : 'Registrar egreso'"></span>
                        </button>
                    </form>
                </div>

                <!-- Desglose por categoría -->
                <div class="bg-white shadow-sm rounded-lg mt-6" x-show="egresosPorCategoria.length">
                    <div class="p-4 border-b border-gray-200">
                        <h3 class="text-sm font-medium text-gray-900">Egresos por categoría</h3>
                    </div>
                    <div class="p-4 space-y-2">
                        <template x-for="c in egresosPorCategoria" :key="c.label">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600" x-text="c.label"></span>
                                <span class="font-medium">Bs. <span x-text="fmt(c.total)"></span></span>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Tabla de egresos -->
            <div class="lg:col-span-2">
                <div class="bg-white shadow-sm rounded-lg">
                    <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="text-lg font-medium text-gray-900">Egresos del período</h3>
                        <span class="text-sm text-gray-500" x-text="egresos.length + ' registros'"></span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Categoría</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Descripción</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Método</th>
                                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Monto</th>
                                    <th class="px-4 py-2"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <template x-for="e in egresos" :key="e.id">
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-2 whitespace-nowrap text-gray-600" x-text="e.fecha"></td>
                                        <td class="px-4 py-2 whitespace-nowrap" x-text="e.categoria"></td>
                                        <td class="px-4 py-2">
                                            <span x-text="e.descripcion"></span>
                                            <span class="block text-xs text-gray-400" x-show="e.proveedor" x-text="e.proveedor"></span>
                                        </td>
                                        <td class="px-4 py-2 whitespace-nowrap text-gray-600" x-text="e.metodo_pago"></td>
                                        <td class="px-4 py-2 whitespace-nowrap text-right font-medium text-red-600">
                                            Bs. <span x-text="fmt(e.monto)"></span>
                                        </td>
                                        <td class="px-4 py-2 text-right">
                                            <button @click="eliminarEgreso(e)" class="text-gray-400 hover:text-red-600" title="Eliminar">
                                                &times;
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                                <tr x-show="!egresos.length">
                                    <td colspan="6" class="px-4 py-8 text-center text-gray-400">Sin egresos en el período</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Ingresos por método -->
                <div class="bg-white shadow-sm rounded-lg mt-6">
                    <div class="p-4 border-b border-gray-200">
                        <h3 class="text-sm font-medium text-gray-900">Ingresos por método de pago</h3>
                    </div>
                    <div class="p-4 grid grid-cols-2 md:grid-cols-4 gap-3">
                        <template x-for="(monto, metodo) in ingresosPorMetodo" :key="metodo">
                            <div class="p-3 bg-gray-50 rounded">
                                <p class="text-xs text-gray-500 capitalize" x-text="metodo"></p>
                                <p class="font-medium text-green-600">Bs. <span x-text="fmt(monto)"></span></p>
                            </div>
                        </template>
                        <p x-show="Object.keys(ingresosPorMetodo).length === 0" class="text-gray-400 text-sm col-span-4">
                            Sin ingresos en el período
                        </p>
                    </div>
                </div>

                <!-- Detalle de ingresos -->
                <div class="bg-white shadow-sm rounded-lg mt-6">
                    <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="text-lg font-medium text-gray-900">Ingresos del período</h3>
                        <span class="text-sm text-gray-500" x-text="ingresos.length + ' registros'"></span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Origen</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Paciente</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Descripción</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Método</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Cajero</th>
                                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Monto</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <template x-for="i in ingresos" :key="i.id">
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-2 whitespace-nowrap text-gray-600" x-text="i.fecha"></td>
                                        <td class="px-4 py-2 whitespace-nowrap">
                                            <span class="px-2 py-0.5 rounded-full text-xs font-medium"
                                                :class="i.origen === 'Farmacia' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700'"
                                                x-text="i.origen"></span>
                                        </td>
                                        <td class="px-4 py-2 whitespace-nowrap font-medium text-gray-900" x-text="i.paciente"></td>
                                        <td class="px-4 py-2">
                                            <span x-text="i.descripcion"></span>
                                            <span class="block text-xs text-gray-400" x-text="i.cuenta_id"></span>
                                            <span class="block text-xs text-gray-400" x-show="i.referencia" x-text="'Ref: ' + i.referencia"></span>
                                        </td>
                                        <td class="px-4 py-2 whitespace-nowrap text-gray-600" x-text="i.metodo_pago"></td>
                                        <td class="px-4 py-2 whitespace-nowrap text-gray-600" x-text="i.usuario"></td>
                                        <td class="px-4 py-2 whitespace-nowrap text-right font-medium text-green-600">
                                            Bs. <span x-text="fmt(i.monto)"></span>
                                        </td>
                                    </tr>
                                </template>
                                <tr x-show="!ingresos.length">
                                    <td colspan="7" class="px-4 py-8 text-center text-gray-400">Sin ingresos en el período</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
function contabilidad() {
    const hoy = new Date();
    const primerDia = new Date(hoy.getFullYear(), hoy.getMonth(), 1);
    const iso = d => d.toISOString().slice(0, 10);

    return {
        filtros: { fecha_inicio: iso(primerDia), fecha_fin: iso(hoy) },
        totales: { ingresos: '0', egresos: '0', saldo: '0' },
        ingresosPorMetodo: {},
        ingresos: [],
        egresosPorCategoria: [],
        egresos: [],
        guardando: false,
        chart: null,
        form: {
            fecha: iso(hoy),
            categoria: '', descripcion: '', monto: '',
            metodo_pago: 'efectivo', proveedor: '', comprobante_nro: '',
        },

        formVacio() {
            return {
                fecha: iso(new Date()),
                categoria: '', descripcion: '', monto: '',
                metodo_pago: 'efectivo', proveedor: '', comprobante_nro: '',
            };
        },

        fmt(v) {
            return parseFloat(v || 0).toLocaleString('es-BO', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        },

        urlExportar() {
            const p = new URLSearchParams(this.filtros);
            return `{{ route('caja.contabilidad.exportar') }}?${p.toString()}`;
        },

        async cargar() {
            const p = new URLSearchParams(this.filtros);
            const res = await fetch(`{{ route('caja.contabilidad.resumen') }}?${p.toString()}`);
            const data = await res.json();
            if (!data.success) { alert(data.message); return; }
            this.totales = data.totales;
            this.ingresosPorMetodo = data.ingresos_por_metodo;
            this.ingresos = data.ingresos ?? [];
            this.egresosPorCategoria = data.egresos_por_categoria;
            this.egresos = data.egresos;
            this.renderChart(data.serie);
        },

        renderChart(serie) {
            const ctx = document.getElementById('graficoFlujo');
            if (this.chart) {
                this.chart.data.labels = serie.labels;
                this.chart.data.datasets[0].data = serie.ingresos;
                this.chart.data.datasets[1].data = serie.farmacia;
                this.chart.data.datasets[2].data = serie.egresos;
                this.chart.update();
                return;
            }
            this.chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: serie.labels,
                    datasets: [
                        { label: 'Ingresos caja', data: serie.ingresos, backgroundColor: '#22c55e', borderRadius: 4 },
                        { label: 'Ingresos farmacia', data: serie.farmacia, backgroundColor: '#3b82f6', borderRadius: 4 },
                        { label: 'Egresos', data: serie.egresos, backgroundColor: '#ef4444', borderRadius: 4 },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: c => `${c.dataset.label}: Bs. ${this.fmt(c.raw)}`,
                            },
                        },
                    },
                    scales: {
                        y: { beginAtZero: true, ticks: { callback: v => 'Bs. ' + v } },
                        x: { grid: { display: false } },
                    },
                },
            });
        },

        async guardarEgreso() {
            this.guardando = true;
            try {
                const res = await fetch('{{ route('caja.contabilidad.egresos.store') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify(this.form),
                });
                const data = await res.json();
                if (!data.success) { alert(data.message); return; }
                this.form = this.formVacio();
                await this.cargar();
            } finally {
                this.guardando = false;
            }
        },

        async eliminarEgreso(e) {
            if (!confirm(`Eliminar egreso "${e.descripcion}" por Bs. ${this.fmt(e.monto)}?`)) return;
            const res = await fetch(`{{ url('contabilidad/egresos') }}/${e.id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            });
            const data = await res.json();
            if (!data.success) { alert(data.message); return; }
            await this.cargar();
        },
    };
}
</script>
@endpush
