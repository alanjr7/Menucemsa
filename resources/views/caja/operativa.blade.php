@extends('layouts.app')

@section('content')
<div class="p-8 bg-[#f8fafc] min-h-screen font-sans">
     <div class="w-full">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Caja Operativa</h1>
                <p class="text-gray-500 text-sm">Gestión de cobros y pagos</p>
            </div>
            <div class="flex items-center space-x-4">
                <a href="{{ route('caja.gestion.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    Historial y Gestión
                </a>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                    <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                    Caja Abierta
                </span>
                <button onclick="mostrarModalCierre()" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-red-700 bg-red-100 hover:bg-red-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                    Cerrar Caja
                </button>
            </div>
        </div>

        <!-- Estadísticas del día -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white shadow-sm rounded-lg p-4 border border-gray-100">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-[10px] font-bold text-gray-400 uppercase">Total Cobrado Hoy</p>
                        <p class="text-lg font-bold text-gray-900" id="totalCobrado">Bs 0.00</p>
                    </div>
                </div>
            </div>
            <div class="bg-white shadow-sm rounded-lg p-4 border border-gray-100">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-[10px] font-bold text-gray-400 uppercase">Transacciones</p>
                        <p class="text-lg font-bold text-gray-900" id="totalTransacciones">0</p>
                    </div>
                </div>
            </div>
            <div class="bg-white shadow-sm rounded-lg p-4 border border-gray-100">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-[10px] font-bold text-gray-400 uppercase">Pendientes</p>
                        <p class="text-lg font-bold text-gray-900" id="totalPendientes">0</p>
                    </div>
                </div>
            </div>
            <div class="bg-white shadow-sm rounded-lg p-4 border border-gray-100">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-orange-100 text-orange-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-[10px] font-bold text-gray-400 uppercase">Pago Parcial</p>
                        <p class="text-lg font-bold text-gray-900" id="totalParciales">0</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Desglose por método de pago -->
        <div class="bg-white shadow-sm rounded-lg mb-6 border border-gray-100">
            <div class="p-4 border-b border-gray-200 bg-gray-50/50">
                <h3 class="text-md font-bold text-gray-800">Recaudación por Método de Pago</h3>
            </div>
            <div class="p-4">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4" id="metodosPago">
                    <!-- JS Fill -->
                </div>
            </div>
        </div>

        <!-- Lista de pacientes -->
        <div class="bg-white shadow-sm rounded-lg border border-gray-100">
            <div class="p-4 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <h3 class="text-lg font-bold text-gray-800">Cuentas por Cobrar</h3>
                <div class="flex items-center gap-2">
                    <input type="text" id="buscarPaciente"
                           placeholder="Nombre, CI, Atención o Estado..."
                           class="block w-full sm:w-80 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                    <button onclick="recargarTodo()" class="p-2 border border-gray-300 rounded-md bg-white hover:bg-gray-50 text-gray-600" title="Recargar">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Paciente</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Atención</th>
                            <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase">Total</th>
                            <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase">Saldo</th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase">Estado</th>
                            <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="tablaPacientes">
                        <tr><td colspan="6" class="px-6 py-8 text-center text-gray-400 italic font-medium">Cargando pacientes...</td></tr>
                    </tbody>
                </table>
            </div>

            <div class="px-4 py-4 border-t border-gray-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="text-sm text-gray-500" id="paginacionInfo">Mostrando 0 registros</div>
                <div class="flex items-center gap-2">
                    <button type="button" id="btnPaginaAnterior" onclick="cambiarPagina(-1)" class="px-3 py-2 text-sm rounded-md border border-gray-300 bg-white hover:bg-gray-50 text-gray-700 disabled:opacity-50 disabled:cursor-not-allowed">Anterior</button>
                    <div class="px-3 py-2 text-sm font-medium text-gray-700 bg-gray-50 rounded-md border border-gray-200" id="paginacionPagina">Página 1 de 1</div>
                    <button type="button" id="btnPaginaSiguiente" onclick="cambiarPagina(1)" class="px-3 py-2 text-sm rounded-md border border-gray-300 bg-white hover:bg-gray-50 text-gray-700 disabled:opacity-50 disabled:cursor-not-allowed">Siguiente</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Cobro -->
<div id="modalCobro" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="cerrarModalCobro()"></div>
        <div class="relative bg-white rounded-lg shadow-xl max-w-2xl w-full">
            <div class="p-6">
                <h3 class="text-xl font-bold text-gray-900 border-b pb-3 mb-4">Procesar Pago</h3>
                <div id="detalleCuenta" class="mb-6"></div>

                <form id="formCobro" class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Monto a Pagar (Bs)</label>
                            <input type="number" id="montoPago" step="0.01" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Método de Pago</label>
                            <select id="metodoPago" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Seleccionar...</option>
                                <option value="efectivo">Efectivo</option>
                                <option value="transferencia">Transferencia</option>
                                <option value="tarjeta">Tarjeta</option>
                                <option value="qr">QR</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Referencia / Observación</label>
                        <input type="text" id="referenciaPago" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    </div>
                    <div class="grid grid-cols-2 gap-4 border-t pt-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">NIT/CI para Factura</label>
                            <input type="text" id="ciNitFactura" class="mt-1 block w-full rounded-md border-gray-300">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Razón Social</label>
                            <input type="text" id="razonSocialFactura" class="mt-1 block w-full rounded-md border-gray-300">
                        </div>
                    </div>
                    <label class="flex items-center gap-2 text-sm font-bold text-blue-600 cursor-pointer select-none">
                        <input type="checkbox" id="esPagoTotal" class="rounded text-blue-600"> Marcar como Pago Total
                    </label>
                </form>
            </div>
            <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse gap-3">
                <button onclick="procesarCobro()" id="btnConfirmarCobro" class="px-6 py-2 bg-blue-600 text-white font-bold rounded-md hover:bg-blue-700 transition shadow-md">CONFIRMAR COBRO</button>
                <button onclick="cerrarModalCobro()" class="px-6 py-2 bg-white border border-gray-300 rounded-md font-medium text-gray-700 hover:bg-gray-50">CANCELAR</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Cierre -->
<div id="modalCierre" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="cerrarModalCierre()"></div>
        <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full p-6">
            <h3 class="text-xl font-bold text-gray-900 mb-4 border-b pb-2">Cierre de Caja Diario</h3>
            <div id="resumenCierre" class="bg-blue-50 p-4 rounded-md mb-4 text-sm border border-blue-100"></div>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Monto Final Físico (Bs)</label>
                    <input type="number" id="montoFinal" step="0.01" class="mt-1 block w-full rounded-md border-gray-300 font-bold text-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Notas de Cierre</label>
                    <textarea id="observacionesCierre" class="mt-1 block w-full rounded-md border-gray-300" rows="2"></textarea>
                </div>
            </div>
            <div class="mt-6 flex flex-row-reverse gap-3">
                <button onclick="cerrarCaja()" class="px-6 py-2 bg-red-600 text-white font-bold rounded-md hover:bg-red-700 transition">CERRAR CAJA</button>
                <button onclick="cerrarModalCierre()" class="px-6 py-2 bg-white border border-gray-300 text-gray-700 rounded-md">VOLVER</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let cuentaActual = null;
    let pacientesData = [];
    let paginaActual = 1;
    const registrosPorPagina = 10;

    // Cargar datos al iniciar
    document.addEventListener('DOMContentLoaded', () => {
        recargarTodo();
        // Buscador inteligente en tiempo real
        document.getElementById('buscarPaciente').addEventListener('input', filtrarPacientes);
    });

    async function recargarTodo() {
        // Bloqueamos la UI brevemente con texto de carga si fuera necesario
        await Promise.all([
            cargarPacientesPendientes(),
            cargarResumenDia()
        ]);
    }

    async function cargarPacientesPendientes() {
        try {
            const response = await fetch('{{ route("caja.operativa.pacientes-pendientes") }}');
            const data = await response.json();
            if (data.success) {
                pacientesData = data.cuentas;
                paginaActual = 1;
                filtrarPacientes(); // Refresca la vista
            }
        } catch (error) {
            console.error('Error:', error);
            document.getElementById('tablaPacientes').innerHTML = '<tr><td colspan="6" class="px-6 py-8 text-center text-red-500 font-medium font-medium">Error al conectar con el servidor</td></tr>';
        }
    }

    function filtrarPacientes() {
        const t = document.getElementById('buscarPaciente').value.toLowerCase().trim();
        const filtrados = pacientesData.filter(c => {
            return (c.paciente_nombre || '').toLowerCase().includes(t) ||
                   (c.paciente_ci || '').toLowerCase().includes(t) ||
                   (c.tipo_atencion || '').toLowerCase().includes(t) ||
                   (c.estado_label || '').toLowerCase().includes(t);
        });
        paginaActual = 1;
        renderizarTabla(filtrados);
    }

    function renderizarTabla(cuentas) {
        const tbody = document.getElementById('tablaPacientes');
        const totalRegistros = cuentas.length;
        const totalPaginas = Math.max(1, Math.ceil(totalRegistros / registrosPorPagina));
        if (paginaActual > totalPaginas) paginaActual = totalPaginas;

        const inicio = (paginaActual - 1) * registrosPorPagina;
        const fin = inicio + registrosPorPagina;
        const paginaCuentas = cuentas.slice(inicio, fin);

        document.getElementById('paginacionInfo').textContent = totalRegistros === 0
            ? 'Mostrando 0 registros'
            : `Mostrando ${inicio + 1}-${Math.min(fin, totalRegistros)} de ${totalRegistros} registros`;
        document.getElementById('paginacionPagina').textContent = `Página ${totalPaginas === 0 ? 0 : paginaActual} de ${totalPaginas}`;
        document.getElementById('btnPaginaAnterior').disabled = paginaActual <= 1;
        document.getElementById('btnPaginaSiguiente').disabled = paginaActual >= totalPaginas;

        if (cuentas.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" class="px-6 py-12 text-center text-gray-400 font-medium">No hay registros que coincidan con la búsqueda</td></tr>';
            return;
        }
        tbody.innerHTML = paginaCuentas.map(c => `
            <tr class="hover:bg-blue-50/30 transition-colors">
                <td class="px-6 py-4">
                    <div class="text-sm font-bold text-gray-800">${c.paciente_nombre}</div>
                    <div class="text-[11px] text-gray-500">DNI/CI: ${c.paciente_ci}</div>
                </td>
                <td class="px-6 py-4">
                    <div class="text-xs text-gray-700 font-medium">${c.tipo_atencion}</div>
                    ${c.es_emergencia ? '<span class="text-[9px] bg-red-600 text-white px-1.5 py-0.5 rounded font-black mt-1 inline-block">EMERGENCIA</span>' : ''}
                </td>
                <td class="px-6 py-4 text-right text-sm font-medium text-gray-600">Bs ${parseFloat(c.total_calculado).toFixed(2)}</td>
                <td class="px-6 py-4 text-right text-sm font-black text-red-600">Bs ${parseFloat(c.saldo_pendiente).toFixed(2)}</td>
                <td class="px-6 py-4 text-center">
                    <span class="px-2.5 py-1 rounded text-[10px] font-black tracking-tighter bg-${c.estado_color}-100 text-${c.estado_color}-800 border border-${c.estado_color}-200">${c.estado_label.toUpperCase()}</span>
                </td>
                <td class="px-6 py-4 text-center">
                    <button onclick="abrirModalCobro('${c.id}')" class="px-5 py-1.5 bg-blue-600 text-white text-[11px] font-black rounded shadow hover:bg-blue-700 transition-all uppercase">COBRAR</button>
                </td>
            </tr>
        `).join('');
    }

    function cambiarPagina(direccion) {
        const t = document.getElementById('buscarPaciente').value.toLowerCase().trim();
        const filtrados = pacientesData.filter(c => {
            return (c.paciente_nombre || '').toLowerCase().includes(t) ||
                   (c.paciente_ci || '').toLowerCase().includes(t) ||
                   (c.tipo_atencion || '').toLowerCase().includes(t) ||
                   (c.estado_label || '').toLowerCase().includes(t);
        });
        const totalPaginas = Math.max(1, Math.ceil(filtrados.length / registrosPorPagina));
        paginaActual = Math.min(totalPaginas, Math.max(1, paginaActual + direccion));
        renderizarTabla(filtrados);
    }

    async function cargarResumenDia() {
        try {
            const response = await fetch('{{ route("caja.operativa.resumen-dia") }}');
            const data = await response.json();
            if (data.success) {
                const r = data.resumen;
                document.getElementById('totalCobrado').textContent = 'Bs ' + parseFloat(r.totales.general).toFixed(2);
                document.getElementById('totalTransacciones').textContent = r.transacciones.total;
                document.getElementById('totalPendientes').textContent = r.cuentas.pendientes;
                document.getElementById('totalParciales').textContent = r.cuentas.parciales;

                const config = [
                    { k: 'efectivo', l: 'Efectivo', c: 'green', i: 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a1 1 0 11-2 0 1 1 0 012 0z' },
                    { k: 'transferencia', l: 'Transf.', c: 'blue', i: 'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4' },
                    { k: 'tarjeta', l: 'Tarjeta', c: 'purple', i: 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z' },
                    { k: 'qr', l: 'QR', c: 'orange', i: 'M12 4v1m6 11h2m-6 0h-2v4h2v-4zM8 12h2v4H8v-4zm-2 4h2v4H6v-4zm10-4h2v4h-2v-4zM6 8h2v4H6V8zm10 0h2v4h-2V8z' }
                ];

                document.getElementById('metodosPago').innerHTML = config.map(m => `
                    <div class="flex items-center p-3 bg-white rounded-lg border border-gray-100 shadow-sm">
                        <div class="p-2 rounded-full bg-${m.c}-100 text-${m.c}-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="${m.i}"></path></svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-[9px] text-gray-400 font-black uppercase tracking-widest">${m.l}</p>
                            <p class="text-sm font-bold text-gray-800">Bs ${parseFloat(r.totales[m.k] || 0).toFixed(2)}</p>
                        </div>
                    </div>
                `).join('');
            }
        } catch (e) { console.error(e); }
    }

    async function abrirModalCobro(id) {
        const response = await fetch(`{{ url('/caja-operativa/detalle-cuenta') }}/${id}`);
        const data = await response.json();
        if (data.success) {
            cuentaActual = data.cuenta;
            document.getElementById('detalleCuenta').innerHTML = `
                <div class="bg-gray-50 p-4 rounded-md border border-gray-200 text-sm">
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-500 font-medium">Paciente:</span>
                        <span class="font-bold text-gray-900">${cuentaActual.paciente.nombre}</span>
                    </div>
                    <div class="space-y-1 text-xs text-gray-600 border-t pt-3 mb-3">
                        ${cuentaActual.detalles.map(d => {
                            // Limpiar floats crudos en descripciones antiguas (ej: 40.3666 hrs → 40h 22min)
                            const desc = d.descripcion.replace(/(\d+\.\d{3,})\s*hrs?/g, (_, h) => {
                                const hh = Math.floor(parseFloat(h));
                                const mm = Math.round((parseFloat(h) - hh) * 60);
                                return mm > 0 ? `${hh}h ${mm}min` : `${hh}h`;
                            });
                            return `<div class="flex justify-between"><span>${desc}</span><span class="font-mono">Bs ${parseFloat(d.subtotal).toFixed(2)}</span></div>`;
                        }).join('')}
                    </div>
                    <div class="border-t mt-2 pt-3 flex justify-between font-black text-xl text-gray-900">
                        <span>Pagar:</span>
                        <span class="text-red-600">Bs ${parseFloat(cuentaActual.saldo_pendiente).toFixed(2)}</span>
                    </div>
                </div>
            `;
            document.getElementById('montoPago').value = parseFloat(cuentaActual.saldo_pendiente).toFixed(2);
            document.getElementById('montoPago').max = parseFloat(cuentaActual.saldo_pendiente).toFixed(2);
            document.getElementById('ciNitFactura').value = cuentaActual.ci_nit_facturacion || '';
            document.getElementById('razonSocialFactura').value = cuentaActual.razon_social || '';
            document.getElementById('modalCobro').classList.remove('hidden');
        }
    }

    async function procesarCobro() {
        if (!cuentaActual) return;
        const btn = document.getElementById('btnConfirmarCobro');
        btn.disabled = true;
        btn.innerText = 'PROCESANDO...';

        const payload = {
            cuenta_cobro_id: cuentaActual.id,
            cuenta_ids: cuentaActual.cuenta_ids,
            monto: document.getElementById('montoPago').value,
            metodo_pago: document.getElementById('metodoPago').value,
            referencia: document.getElementById('referenciaPago').value,
            ci_nit_facturacion: document.getElementById('ciNitFactura').value,
            razon_social: document.getElementById('razonSocialFactura').value,
            es_pago_total: document.getElementById('esPagoTotal').checked
        };

        if(!payload.metodo_pago) {
            alert("Seleccione un método de pago");
            btn.disabled = false;
            btn.innerText = 'CONFIRMAR COBRO';
            return;
        }

        try {
            const response = await fetch('{{ route("caja.operativa.procesar-cobro") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify(payload)
            });
            const data = await response.json();
            if (data.success) {
                if (data.print_url) window.open(data.print_url, '_blank');
                cerrarModalCobro();
                await recargarTodo();
            } else { alert(data.message); }
        } catch (e) { alert("Error al procesar el pago"); }
        finally { btn.disabled = false; btn.innerText = 'CONFIRMAR COBRO'; }
    }

    function cerrarModalCobro() {
        document.getElementById('modalCobro').classList.add('hidden');
        cuentaActual = null;
    }

    async function mostrarModalCierre() {
        const response = await fetch('{{ route("caja.operativa.resumen-dia") }}');
        const data = await response.json();
        if (data.success) {
            const r = data.resumen;
            const total = (parseFloat(r.monto_inicial) + parseFloat(r.totales.general)).toFixed(2);
            document.getElementById('resumenCierre').innerHTML = `
                <div class="flex justify-between mb-1 text-gray-600"><span>Monto Inicial Apertura:</span><span class="font-bold">Bs ${r.monto_inicial}</span></div>
                <div class="flex justify-between mb-1 text-gray-600"><span>Ventas Recaudadas:</span><span class="font-bold text-green-600">+ Bs ${r.totales.general}</span></div>
                <div class="flex justify-between border-t border-blue-200 mt-2 pt-2 font-black text-blue-900 text-lg"><span>Saldo en Sistema:</span><span>Bs ${total}</span></div>
            `;
            document.getElementById('modalCierre').classList.remove('hidden');
        }
    }

    function cerrarModalCierre() { document.getElementById('modalCierre').classList.add('hidden'); }

    async function cerrarCaja() {
        const payload = {
            monto_final: document.getElementById('montoFinal').value,
            observaciones: document.getElementById('observacionesCierre').value
        };
        if (!payload.monto_final) return alert("Ingrese el monto real que tiene en caja");

        try {
            const response = await fetch('{{ route("caja.operativa.cerrar") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify(payload)
            });
            const data = await response.json();
            if (data.success) {
                alert("Caja cerrada correctamente.");
                window.location.reload();
            } else { alert(data.message); }
        } catch(e) { alert("Error al cerrar caja"); }
    }

    // Toggle automático de monto total
    document.getElementById('esPagoTotal').addEventListener('change', function() {
        if (this.checked && cuentaActual) {
            document.getElementById('montoPago').value = parseFloat(cuentaActual.saldo_pendiente).toFixed(2);
        }
    });
</script>
@endpush
@endsection
