@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-slate-50 font-sans overflow-x-hidden">
    <div class="p-4 sm:p-6 lg:p-8">
        <div class="max-w-7xl mx-auto">

            {{-- ===== Header ===== --}}
            <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3 mb-6">
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Devoluciones / Notas de Crédito</h1>
                    <p class="text-gray-500 text-sm mt-0.5">
                        Contra-ingresos: NC sobre pagos de caja (el recibo original no se modifica) y ventas de farmacia anuladas con stock reingresado.
                    </p>
                </div>
                <a href="{{ route('caja.contabilidad.index') }}"
                    class="inline-flex items-center justify-center px-4 py-2 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-lg h-[38px] transition-colors">
                    Ir a Contabilidad
                </a>
            </div>

            {{-- ===== KPIs (del filtro actual del listado) ===== --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a4 4 0 014 4v1m-14-5l4-4m-4 4l4 4"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Total devuelto (vigente)</p>
                        <p class="text-xl font-bold text-amber-600 mt-0.5 font-mono">Bs <span id="kpiTotal">0.00</span></p>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-slate-100 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Notas de crédito</p>
                        <p class="text-xl font-bold text-gray-800 mt-0.5 font-mono" id="kpiCantidad">0</p>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-red-50 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-12.728 12.728M5.636 5.636l12.728 12.728"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Anuladas</p>
                        <p class="text-xl font-bold text-red-500 mt-0.5 font-mono" id="kpiAnuladas">0</p>
                    </div>
                </div>
            </div>

            {{-- ===== Paso 1: Buscar el pago a devolver ===== --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-6">
                <div class="px-5 py-3.5 border-b border-gray-100">
                    <h3 class="text-sm font-semibold text-gray-900">1 · Buscar el pago a devolver</h3>
                    <p class="text-[11px] text-gray-400 mt-0.5">Busque el recibo (PAGO-), la cuenta (CTA-) o el paciente y presione "Devolver" en la fila correspondiente.</p>
                </div>
                <div class="p-4 flex flex-col sm:flex-row gap-2">
                    <input type="text" id="buscarPago" placeholder="Nº recibo, cuenta, nombre o CI del paciente..."
                        onkeydown="if(event.key==='Enter') buscarPagos(1)"
                        class="flex-1 text-sm rounded-lg border-gray-200 shadow-sm focus:border-amber-500 focus:ring-amber-500">
                    <div class="flex gap-2">
                        <input type="date" id="buscarPagoDesde" class="text-sm rounded-lg border-gray-200 shadow-sm focus:border-amber-500 focus:ring-amber-500">
                        <input type="date" id="buscarPagoHasta" class="text-sm rounded-lg border-gray-200 shadow-sm focus:border-amber-500 focus:ring-amber-500">
                        <button onclick="buscarPagos(1)"
                            class="inline-flex items-center gap-1.5 px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white text-sm font-semibold rounded-lg transition-colors whitespace-nowrap">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            Buscar
                        </button>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-50 text-sm">
                        <thead>
                            <tr class="bg-gray-50/70">
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Recibo</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Fecha</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Paciente</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Método</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Monto</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Disponible</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide"></th>
                            </tr>
                        </thead>
                        <tbody id="tablaPagos" class="divide-y divide-gray-50">
                            <tr><td colspan="7" class="px-4 py-8 text-center text-gray-400 text-sm">Busque un pago para comenzar.</td></tr>
                        </tbody>
                    </table>
                </div>
                <div id="paginacionPagos" class="px-4 py-3"></div>
            </div>

            {{-- ===== Paso 2: Devoluciones emitidas ===== --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-5 py-3.5 border-b border-gray-100 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <div class="min-w-0">
                        <h3 class="text-sm font-semibold text-gray-900">2 · Devoluciones emitidas (Caja + Farmacia)</h3>
                        <p class="text-[11px] text-gray-400 mt-0.5">NC de caja: imprimir / anular / reactivar. Ventas de farmacia anuladas: solo consulta (su ticket se reimprime en Farmacia → Ventas).</p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <input type="text" id="filtroNc" placeholder="Nº NC, recibo, cuenta o paciente..."
                            onkeydown="if(event.key==='Enter') cargarDevoluciones(1)"
                            class="text-sm rounded-lg border-gray-200 shadow-sm focus:border-amber-500 focus:ring-amber-500 w-56">
                        <input type="date" id="filtroNcDesde" class="text-sm rounded-lg border-gray-200 shadow-sm focus:border-amber-500 focus:ring-amber-500">
                        <input type="date" id="filtroNcHasta" class="text-sm rounded-lg border-gray-200 shadow-sm focus:border-amber-500 focus:ring-amber-500">
                        <select id="filtroNcEstado" onchange="cargarDevoluciones(1)"
                            class="text-sm rounded-lg border-gray-200 shadow-sm focus:border-amber-500 focus:ring-amber-500">
                            <option value="todas">Todas</option>
                            <option value="vigentes">Vigentes</option>
                            <option value="anuladas">Anuladas</option>
                        </select>
                        <button onclick="cargarDevoluciones(1)"
                            class="inline-flex items-center px-3 py-2 border border-gray-200 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </button>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-50 text-sm">
                        <thead>
                            <tr class="bg-gray-50/70">
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Nota Crédito</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Fecha</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Paciente</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Recibo / Cuenta</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Motivo / Tipo</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Monto</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tablaDevoluciones" class="divide-y divide-gray-50">
                            <tr><td colspan="7" class="px-4 py-8 text-center text-gray-400 text-sm">Cargando...</td></tr>
                        </tbody>
                    </table>
                </div>
                <div id="paginacionDevoluciones" class="px-4 py-3"></div>
            </div>

        </div>
    </div>

    {{-- ===== Modal Devolución (idéntico en función al probado en caja-gestión) ===== --}}
    <div id="modalDevolucion" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="cerrarModalDevolucion()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6">
                    <div class="flex items-center justify-between mb-1">
                        <h3 class="text-lg font-medium text-gray-900">Devolución / Nota de Crédito</h3>
                        <span id="devPagoId" class="text-sm font-mono text-gray-500"></span>
                    </div>
                    <p class="text-xs text-gray-500 mb-4">
                        El pago original no se modifica: se emite una Nota de Crédito que resta de los
                        ingresos del período actual. El efecto sobre la cuenta depende del caso:
                    </p>

                    {{-- Caso de negocio: qué pasa con el cargo --}}
                    <div class="space-y-2 mb-4">
                        <label id="devTipoServicioLabel"
                            class="flex items-start gap-3 p-3 rounded-lg border cursor-pointer transition-colors border-amber-400 bg-amber-50">
                            <input type="radio" name="devTipo" value="servicio" checked
                                onchange="devSetTipo(true)"
                                class="mt-0.5 text-amber-600 focus:ring-amber-500">
                            <span>
                                <span class="block text-sm font-medium text-gray-900">Servicio no realizado / cancelado</span>
                                <span class="block text-xs text-gray-500 mt-0.5">
                                    La clínica no cumplió el servicio: se devuelve el dinero y
                                    <strong>se anula también el cargo</strong>. La cuenta queda en Bs 0 y no vuelve a cobro.
                                </span>
                            </span>
                        </label>
                        <label id="devTipoErrorLabel"
                            class="flex items-start gap-3 p-3 rounded-lg border cursor-pointer transition-colors border-gray-200 bg-white">
                            <input type="radio" name="devTipo" value="error"
                                onchange="devSetTipo(false)"
                                class="mt-0.5 text-amber-600 focus:ring-amber-500">
                            <span>
                                <span class="block text-sm font-medium text-gray-900">Error de cobro — se volverá a cobrar</span>
                                <span class="block text-xs text-gray-500 mt-0.5">
                                    Se ingresó mal el monto: se devuelve el dinero pero
                                    <strong>el cargo se mantiene</strong>. La cuenta vuelve a pendiente para cobrarse correctamente.
                                </span>
                            </span>
                        </label>
                    </div>

                    {{-- Resumen del pago --}}
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-4 text-sm bg-gray-50 rounded-md p-3">
                        <div><span class="block text-xs text-gray-500">Paciente</span><span id="devPaciente" class="font-medium text-gray-900">—</span></div>
                        <div><span class="block text-xs text-gray-500">Monto del pago</span><span id="devMontoPago" class="font-medium text-gray-900">—</span></div>
                        <div><span class="block text-xs text-gray-500">Ya devuelto</span><span id="devDevuelto" class="font-medium text-amber-700">—</span></div>
                        <div><span class="block text-xs text-gray-500">Disponible</span><span id="devDisponible" class="font-semibold text-green-700">—</span></div>
                    </div>

                    {{-- Formulario --}}
                    <div id="devFormulario" class="space-y-3">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Monto a devolver (Bs) *</label>
                                <input type="text" inputmode="decimal" id="devMonto" placeholder="0.00"
                                    class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Método de devolución *</label>
                                <select id="devMetodo" class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500">
                                    <option value="efectivo">Efectivo</option>
                                    <option value="transferencia">Transferencia</option>
                                    <option value="tarjeta">Tarjeta</option>
                                    <option value="qr">QR</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Motivo *</label>
                            <input type="text" id="devMotivo" maxlength="255" placeholder="Ej.: cobro duplicado, servicio no realizado..."
                                class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500">
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Referencia (opcional)</label>
                                <input type="text" id="devReferencia" maxlength="255" placeholder="Nº transferencia / voucher"
                                    class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Observaciones (opcional)</label>
                                <input type="text" id="devObservaciones" maxlength="1000"
                                    class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-amber-500 focus:ring-amber-500">
                            </div>
                        </div>
                        <p id="devError" class="hidden text-sm text-red-600"></p>
                    </div>

                    {{-- Devoluciones ya emitidas sobre este pago --}}
                    <div id="devListado" class="mt-4"></div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" id="devBtnRegistrar" onclick="registrarDevolucion()"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-amber-600 text-base font-medium text-white hover:bg-amber-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                        Registrar Devolución
                    </button>
                    <button type="button" onclick="cerrarModalDevolucion()"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // ══════════ Devoluciones / Notas de Crédito — página propia ══════════
    // Reusa los MISMOS endpoints JSON del módulo (caja.gestion.devoluciones.* y
    // el historial de pagos): una sola fuente de verdad en el backend.

    document.addEventListener('DOMContentLoaded', function () {
        cargarDevoluciones(1);
    });

    function fmtBs(v) { return parseFloat(v || 0).toFixed(2); }

    function renderPaginacion(contId, data, fn) {
        const cont = document.getElementById(contId);
        if (!data || data.last_page <= 1) { cont.innerHTML = ''; return; }
        let html = '<div class="flex items-center justify-between text-sm">';
        html += `<span class="text-gray-500">${data.from ?? 0}–${data.to ?? 0} de ${data.total}</span>`;
        html += '<div class="flex gap-1">';
        if (data.current_page > 1)
            html += `<button onclick="${fn}(${data.current_page - 1})" class="px-3 py-1.5 border border-gray-200 rounded-lg bg-white hover:bg-gray-50 text-gray-700">Anterior</button>`;
        if (data.current_page < data.last_page)
            html += `<button onclick="${fn}(${data.current_page + 1})" class="px-3 py-1.5 border border-gray-200 rounded-lg bg-white hover:bg-gray-50 text-gray-700">Siguiente</button>`;
        html += '</div></div>';
        cont.innerHTML = html;
    }

    // ───── Paso 1: buscador de pagos (reusa caja.gestion.historial-pagos) ─────
    async function buscarPagos(page = 1) {
        const params = new URLSearchParams({
            q: document.getElementById('buscarPago').value,
            fecha_inicio: document.getElementById('buscarPagoDesde').value,
            fecha_fin: document.getElementById('buscarPagoHasta').value,
            metodo_pago: 'todos',
            page,
        });
        const tbody = document.getElementById('tablaPagos');
        tbody.innerHTML = '<tr><td colspan="7" class="px-4 py-6 text-center text-gray-400 text-sm">Buscando...</td></tr>';
        try {
            const resp = await fetch(`{{ route('caja.gestion.historial-pagos') }}?${params}`);
            const data = await resp.json();
            if (!data.success) throw new Error();
            const pagos = data.pagos.data;
            if (!pagos.length) {
                tbody.innerHTML = '<tr><td colspan="7" class="px-4 py-6 text-center text-gray-400 text-sm">Sin pagos que coincidan con la búsqueda.</td></tr>';
                renderPaginacion('paginacionPagos', null);
                return;
            }
            tbody.innerHTML = pagos.map(p => {
                const disponible = parseFloat(p.monto_disponible ?? p.monto);
                const devuelto = parseFloat(p.monto_devuelto || 0);
                const badge = devuelto > 0
                    ? `<span class="ml-1 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium ${disponible <= 0 ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700'}">${disponible <= 0 ? 'Devuelto' : 'Dev. parcial'}</span>`
                    : '';
                const accion = disponible > 0
                    ? `<button onclick="abrirModalDevolucion('${p.id}')" class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold text-white bg-amber-600 hover:bg-amber-700 transition-colors">Devolver</button>`
                    : '<span class="text-xs text-gray-400">Sin saldo devolvible</span>';
                return `
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        <td class="px-4 py-3 whitespace-nowrap"><span class="font-mono font-semibold text-gray-900">${p.id}</span>${badge}<span class="block text-xs text-gray-400">${p.cuenta_cobro_id}</span></td>
                        <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-500">${p.fecha}</td>
                        <td class="px-4 py-3 whitespace-nowrap"><span class="font-medium text-gray-900">${p.paciente}</span><span class="block text-xs text-gray-400">${p.ci}</span></td>
                        <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-500">${p.metodo_pago}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-right font-mono font-semibold text-green-600">Bs ${fmtBs(p.monto)}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-right font-mono ${disponible > 0 ? 'text-gray-800' : 'text-gray-400'}">Bs ${fmtBs(disponible)}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-center">${accion}</td>
                    </tr>`;
            }).join('');
            renderPaginacion('paginacionPagos', data.pagos, 'buscarPagos');
        } catch (e) {
            tbody.innerHTML = '<tr><td colspan="7" class="px-4 py-6 text-center text-red-500 text-sm">Error al buscar pagos.</td></tr>';
        }
    }

    // ───── Paso 2: listado de NC emitidas ─────
    async function cargarDevoluciones(page = 1) {
        const params = new URLSearchParams({
            q: document.getElementById('filtroNc').value,
            fecha_inicio: document.getElementById('filtroNcDesde').value,
            fecha_fin: document.getElementById('filtroNcHasta').value,
            estado: document.getElementById('filtroNcEstado').value,
            page,
        });
        const tbody = document.getElementById('tablaDevoluciones');
        try {
            const resp = await fetch(`{{ route('caja.gestion.devoluciones.listar') }}?${params}`);
            const data = await resp.json();
            if (!data.success) throw new Error();

            document.getElementById('kpiTotal').textContent = fmtBs(data.stats.total_vigente);
            document.getElementById('kpiCantidad').textContent = data.stats.cantidad;
            document.getElementById('kpiAnuladas').textContent = data.stats.anuladas;

            const items = data.devoluciones.data;
            if (!items.length) {
                tbody.innerHTML = '<tr><td colspan="7" class="px-4 py-8 text-center text-gray-400 text-sm">Sin devoluciones registradas.</td></tr>';
                renderPaginacion('paginacionDevoluciones', null);
                return;
            }
            tbody.innerHTML = items.map(d => {
                const esFarmacia = d.origen === 'Farmacia';
                const badgeOrigen = `<span class="ml-1 inline-flex px-2 py-0.5 rounded-full text-[10px] font-medium ${esFarmacia ? 'bg-cyan-100 text-cyan-700' : 'bg-emerald-100 text-emerald-700'}">${d.origen}</span>`;
                // Farmacia: solo lectura aquí — el ticket (con sello ANULADA) se
                // reimprime desde Farmacia → Ventas; no hay anular/reactivar.
                const acciones = esFarmacia
                    ? '<span class="text-[10px] text-gray-400">Ticket en Farmacia → Ventas</span>'
                    : `<a href="{{ url('caja-gestion/devoluciones') }}/${encodeURIComponent(d.id)}/comprobante" target="_blank"
                          class="text-xs text-blue-600 hover:text-blue-800 font-medium">Imprimir</a>
                       ${d.anulado
                            ? `<button onclick="revertirDevolucion('${d.id}')" class="text-xs text-emerald-600 hover:text-emerald-800 font-medium">Reactivar</button>`
                            : `<button onclick="anularDevolucion('${d.id}')" class="text-xs text-red-600 hover:text-red-800 font-medium">Anular</button>`}`;
                return `
                <tr class="hover:bg-slate-50/60 transition-colors ${d.anulado ? 'opacity-50' : ''}">
                    <td class="px-4 py-3 whitespace-nowrap">
                        <span class="font-mono font-semibold text-gray-900">${d.id}</span>${badgeOrigen}
                        ${d.anulado ? `<span class="ml-1 inline-flex px-2 py-0.5 rounded-full text-[10px] font-medium bg-gray-200 text-gray-600" title="${d.motivo_anulacion ?? ''}">Anulada</span>` : ''}
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-500">${d.fecha}</td>
                    <td class="px-4 py-3 whitespace-nowrap font-medium text-gray-900">${d.paciente}</td>
                    <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-500"><span class="block">${d.pago_id}</span><span class="block text-gray-400">${d.cuenta_id}</span></td>
                    <td class="px-4 py-3 text-xs text-gray-600">
                        <span>${d.motivo}</span>
                        <span class="block text-amber-700">${d.tipo_label ?? ''}</span>
                        <span class="block text-gray-400">${d.usuario}</span>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-right"><span class="font-semibold font-mono ${d.anulado ? 'text-gray-400 line-through' : 'text-amber-600'}">− Bs ${fmtBs(d.monto)}</span></td>
                    <td class="px-4 py-3 whitespace-nowrap text-center">
                        <div class="flex items-center justify-center gap-3">${acciones}</div>
                    </td>
                </tr>`;
            }).join('');
            renderPaginacion('paginacionDevoluciones', data.devoluciones, 'cargarDevoluciones');
        } catch (e) {
            tbody.innerHTML = '<tr><td colspan="7" class="px-4 py-8 text-center text-red-500 text-sm">Error al cargar devoluciones.</td></tr>';
        }
    }

    // ───── Modal de devolución (misma lógica probada de caja-gestión) ─────
    let devPagoActual = null;
    let devToken = null;
    let devAnulaCargos = true;

    function devSetTipo(anulaCargos) {
        devAnulaCargos = anulaCargos;
        const sel = ['border-amber-400', 'bg-amber-50'];
        const noSel = ['border-gray-200', 'bg-white'];
        const servicio = document.getElementById('devTipoServicioLabel');
        const error = document.getElementById('devTipoErrorLabel');
        servicio.classList.remove(...(anulaCargos ? noSel : sel));
        servicio.classList.add(...(anulaCargos ? sel : noSel));
        error.classList.remove(...(anulaCargos ? sel : noSel));
        error.classList.add(...(anulaCargos ? noSel : sel));
    }

    async function abrirModalDevolucion(pagoId) {
        devPagoActual = pagoId;
        devToken = (crypto.randomUUID ? crypto.randomUUID() : Date.now() + '-' + Math.random());
        document.getElementById('devPagoId').textContent = pagoId;
        document.getElementById('devMonto').value = '';
        document.getElementById('devMotivo').value = '';
        document.getElementById('devReferencia').value = '';
        document.getElementById('devObservaciones').value = '';
        document.getElementById('devError').classList.add('hidden');
        document.querySelector('input[name="devTipo"][value="servicio"]').checked = true;
        devSetTipo(true);
        document.getElementById('modalDevolucion').classList.remove('hidden');
        await cargarEstadoDevolucion();
    }

    function cerrarModalDevolucion() {
        document.getElementById('modalDevolucion').classList.add('hidden');
        devPagoActual = null;
    }

    async function cargarEstadoDevolucion() {
        try {
            const resp = await fetch(`{{ url('caja-gestion/pagos') }}/${encodeURIComponent(devPagoActual)}/devoluciones`);
            const data = await resp.json();
            if (!data.success) return;

            document.getElementById('devPaciente').textContent = data.pago.paciente;
            document.getElementById('devMontoPago').textContent = 'Bs ' + fmtBs(data.pago.monto);
            document.getElementById('devDevuelto').textContent = 'Bs ' + fmtBs(data.pago.monto_devuelto);
            document.getElementById('devDisponible').textContent = 'Bs ' + fmtBs(data.pago.monto_disponible);

            const sinSaldo = parseFloat(data.pago.monto_disponible) <= 0;
            document.getElementById('devFormulario').style.display = sinSaldo ? 'none' : '';
            document.getElementById('devBtnRegistrar').style.display = sinSaldo ? 'none' : '';

            const cont = document.getElementById('devListado');
            if (!data.devoluciones.length) { cont.innerHTML = ''; return; }
            cont.innerHTML = `
                <h4 class="text-xs font-semibold text-gray-500 uppercase mb-2">Notas de crédito de este recibo</h4>
                <div class="divide-y divide-gray-100 border border-gray-200 rounded-md">
                    ${data.devoluciones.map(d => `
                        <div class="flex items-center justify-between px-3 py-2 text-sm ${d.anulado ? 'opacity-60' : ''}">
                            <div>
                                <span class="font-mono font-medium text-gray-900">${d.id}</span>
                                <span class="text-gray-500">· ${d.fecha} · ${d.metodo}</span>
                                ${d.anulado ? `<span class="ml-1 inline-flex px-2 py-0.5 rounded text-xs font-medium bg-gray-200 text-gray-600" title="${d.motivo_anulacion ?? ''}">Anulada</span>` : ''}
                                <div class="text-xs text-gray-500">${d.motivo} — ${d.usuario}</div>
                                <div class="text-xs text-amber-700">${d.tipo_label ?? ''}</div>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="font-semibold ${d.anulado ? 'text-gray-400 line-through' : 'text-red-600'}">- Bs ${fmtBs(d.monto)}</span>
                                <a href="{{ url('caja-gestion/devoluciones') }}/${encodeURIComponent(d.id)}/comprobante" target="_blank"
                                   class="text-xs text-blue-600 hover:underline">Imprimir</a>
                                ${d.anulado
                                    ? `<button onclick="revertirDevolucion('${d.id}')" class="text-xs text-emerald-600 hover:underline">Reactivar</button>`
                                    : `<button onclick="anularDevolucion('${d.id}')" class="text-xs text-red-600 hover:underline">Anular</button>`}
                            </div>
                        </div>
                    `).join('')}
                </div>`;
        } catch (e) {
            console.error('Error:', e);
        }
    }

    async function registrarDevolucion() {
        const errorEl = document.getElementById('devError');
        errorEl.classList.add('hidden');

        const monto = document.getElementById('devMonto').value.replace(',', '.').trim();
        const motivo = document.getElementById('devMotivo').value.trim();
        if (!monto || isNaN(parseFloat(monto)) || parseFloat(monto) <= 0) {
            errorEl.textContent = 'Ingrese un monto válido mayor a 0.';
            errorEl.classList.remove('hidden');
            return;
        }
        if (!motivo) {
            errorEl.textContent = 'El motivo es obligatorio.';
            errorEl.classList.remove('hidden');
            return;
        }
        if (!confirm(`¿Registrar devolución de Bs ${parseFloat(monto).toFixed(2)} sobre el recibo ${devPagoActual}?`)) return;

        const btn = document.getElementById('devBtnRegistrar');
        btn.disabled = true;
        try {
            const resp = await fetch(`{{ url('caja-gestion/pagos') }}/${encodeURIComponent(devPagoActual)}/devoluciones`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    monto: monto,
                    metodo_devolucion: document.getElementById('devMetodo').value,
                    motivo: motivo,
                    anula_cargos: devAnulaCargos,
                    referencia: document.getElementById('devReferencia').value.trim() || null,
                    observaciones: document.getElementById('devObservaciones').value.trim() || null,
                    idempotency_key: devToken,
                })
            });
            const data = await resp.json();
            if (data.success) {
                devToken = (crypto.randomUUID ? crypto.randomUUID() : Date.now() + '-' + Math.random());
                document.getElementById('devMonto').value = '';
                document.getElementById('devMotivo').value = '';
                await cargarEstadoDevolucion();
                buscarPagos(1);
                cargarDevoluciones(1);
                alert(data.message);
            } else {
                errorEl.textContent = data.message || 'No se pudo registrar la devolución.';
                errorEl.classList.remove('hidden');
            }
        } catch (e) {
            errorEl.textContent = 'Error de red al registrar la devolución.';
            errorEl.classList.remove('hidden');
        } finally {
            btn.disabled = false;
        }
    }

    async function anularDevolucion(id) {
        const motivo = prompt('Motivo de la anulación de la nota de crédito:');
        if (!motivo || !motivo.trim()) return;
        try {
            const resp = await fetch(`{{ url('caja-gestion/devoluciones') }}/${encodeURIComponent(id)}/anular`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ motivo: motivo.trim() })
            });
            const data = await resp.json();
            if (!data.success) alert(data.message);
            if (devPagoActual) await cargarEstadoDevolucion();
            cargarDevoluciones(1);
        } catch (e) { console.error('Error:', e); }
    }

    async function revertirDevolucion(id) {
        if (!confirm('¿Reactivar esta nota de crédito? Volverá a restar de los ingresos y a aplicar su efecto sobre la cuenta.')) return;
        try {
            const resp = await fetch(`{{ url('caja-gestion/devoluciones') }}/${encodeURIComponent(id)}/revertir`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            });
            const data = await resp.json();
            if (!data.success) alert(data.message);
            if (devPagoActual) await cargarEstadoDevolucion();
            cargarDevoluciones(1);
        } catch (e) { console.error('Error:', e); }
    }
</script>
@endpush
@endsection
