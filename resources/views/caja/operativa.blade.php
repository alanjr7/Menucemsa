@extends('layouts.app')

@section('content')
<div class="p-8 bg-[#f8fafc] min-h-screen font-sans" x-data="cajaOperativa()">
    <div class="max-w-7xl mx-auto">
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
                <button onclick="mostrarModalCierre()" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                    Cerrar Caja
                </button>
            </div>
        </div>
            <!-- Estadísticas del día -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Total Cobrado Hoy</p>
                            <p class="text-lg font-bold text-gray-900" id="totalCobrado">Bs 0.00</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Transacciones</p>
                            <p class="text-lg font-bold text-gray-900" id="totalTransacciones">0</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-yellow-100">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Pendientes</p>
                            <p class="text-lg font-bold text-gray-900" id="totalPendientes">0</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-orange-100">
                            <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Pago Parcial</p>
                            <p class="text-lg font-bold text-gray-900" id="totalParciales">0</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Desglose por método de pago -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Desglose por Método de Pago</h3>
                </div>
                <div class="p-4">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4" id="metodosPago">
                        <!-- Se llena con JS -->
                    </div>
                </div>
            </div>

            <!-- Lista de pacientes con cuenta pendiente -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center sm:justify-between">
                    <h3 class="text-lg font-medium text-gray-900 mb-2 sm:mb-0">
                        Pacientes con Cuenta Pendiente
                    </h3>
                    <div class="flex items-center space-x-2">
                        <input type="text" 
                               id="buscarPaciente" 
                               placeholder="Buscar paciente..."
                               class="block w-full sm:w-64 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        <button onclick="filtrarPacientes()" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </button>
                        <button onclick="cargarPacientesPendientes()" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Paciente</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo Atención</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Pagado</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Saldo</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="tablaPacientes">
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                    Cargando...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pacientes UTI con Alta Clínica (listos para cobro) -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
                <div class="p-4 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center gap-3">
                        <h3 class="text-lg font-medium text-gray-900">
                            Pacientes UTI - Listos para Cobro
                        </h3>
                        <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full" id="contadorUti">0</span>
                    </div>
                    <div class="flex items-center space-x-2 mt-2 sm:mt-0">
                        <button onclick="cargarPacientesUti()" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Paciente</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cama</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Días UTI</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo Pago</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="tablaPacientesUti">
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                    Cargando pacientes UTI...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Cobro -->
    <div id="modalCobro" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="cerrarModalCobro()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Realizar Cobro</h3>
                            
                            <div id="detalleCuenta" class="mt-4">
                                <!-- Se llena con JS -->
                            </div>

                            <form id="formCobro" class="mt-6 space-y-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Monto a Pagar (Bs)</label>
                                        <input type="number" 
                                               id="montoPago" 
                                               name="monto" 
                                               step="0.01" 
                                               min="0.01"
                                               required
                                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Método de Pago</label>
                                        <select id="metodoPago" name="metodo_pago" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                            <option value="">Seleccione...</option>
                                            <option value="efectivo">Efectivo</option>
                                            <option value="transferencia">Transferencia</option>
                                            <option value="tarjeta">Tarjeta</option>
                                            <option value="qr">QR</option>
                                        </select>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Referencia (opcional)</label>
                                    <input type="text" 
                                           id="referenciaPago" 
                                           name="referencia" 
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                           placeholder="N° operación, código, etc.">
                                </div>

                                <div class="border-t pt-4 mt-4">
                                    <h4 class="text-sm font-medium text-gray-900 mb-3">Datos para Facturación</h4>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">CI / NIT</label>
                                            <input type="text" 
                                                   id="ciNitFactura" 
                                                   name="ci_nit_facturacion" 
                                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Razón Social</label>
                                            <input type="text" 
                                                   id="razonSocialFactura" 
                                                   name="razon_social" 
                                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center">
                                    <input type="checkbox" id="esPagoTotal" name="es_pago_total" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <label for="esPagoTotal" class="ml-2 block text-sm text-gray-900">
                                        Pago total (saldo completo)
                                    </label>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" onclick="procesarCobro()" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Cobrar
                    </button>
                    <button type="button" onclick="cerrarModalCobro()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Cierre de Caja -->
    <div id="modalCierre" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="cerrarModalCierre()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Cierre de Caja</h3>
                    
                    <div id="resumenCierre" class="bg-gray-50 p-4 rounded-md mb-4">
                        <!-- Se llena con JS -->
                    </div>

                    <form id="formCierre" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Monto Final (Bs)</label>
                            <input type="number" 
                                   id="montoFinal" 
                                   name="monto_final" 
                                   step="0.01" 
                                   min="0"
                                   required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Observaciones</label>
                            <textarea id="observacionesCierre" 
                                      name="observaciones" 
                                      rows="2"
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                        </div>
                    </form>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" onclick="cerrarCaja()" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Cerrar Caja
                    </button>
                    <button type="button" onclick="cerrarModalCierre()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="{{ asset('js/auto-refresh.js') }}"></script>
    <script>
        let cuentaActual = null;
        let pacientesData = [];
        let autoRefresh = null;

        // Definir componente Alpine.js
        function cajaOperativa() {
            return {
                open: false,
                sidebarOpen: true,
                init() {
                    this.cargarDatos();
                },
                async cargarDatos() {
                    await cargarPacientesPendientes();
                    await cargarPacientesUti();
                    await cargarResumenDia();
                }
            };
        }

        // Cargar datos al iniciar
        document.addEventListener('DOMContentLoaded', function() {
            cargarPacientesPendientes();
            cargarPacientesUti();
            cargarResumenDia();
            iniciarAutoRefresh();
        });

        // Iniciar auto-refresh cada 3 segundos
        function iniciarAutoRefresh() {
            autoRefresh = new AutoRefresh({
                interval: 3000,
                endpoint: '{{ route("caja.operativa.resumen-dia") }}',
                onData: (data) => {
                    if (data.success) {
                        // Actualizar estadísticas
                        document.getElementById('totalCobrado').textContent = 'Bs ' + parseFloat(data.resumen.totales.general).toFixed(2);
                        document.getElementById('totalTransacciones').textContent = data.resumen.transacciones.total;
                        document.getElementById('totalPendientes').textContent = data.resumen.cuentas.pendientes;
                        document.getElementById('totalParciales').textContent = data.resumen.cuentas.parciales;

                        // Actualizar métodos de pago
                        const metodos = [
                            { key: 'efectivo', label: 'Efectivo', icon: 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a1 1 0 11-2 0 1 1 0 012 0z', color: 'green' },
                            { key: 'transferencia', label: 'Transferencia', icon: 'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4', color: 'blue' },
                            { key: 'tarjeta', label: 'Tarjeta', icon: 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z', color: 'purple' },
                            { key: 'qr', label: 'QR', icon: 'M12 4v1m6 11h2m-6 0h-2v4h2v-4zM8 12h2v4H8v-4zm-2 4h2v4H6v-4zm10-4h2v4h-2v-4zM6 8h2v4H6V8zm10 0h2v4h-2V8z', color: 'orange' }
                        ];

                        document.getElementById('metodosPago').innerHTML = metodos.map(m => `
                            <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                                <div class="p-2 rounded-full bg-${m.color}-100">
                                    <svg class="w-5 h-5 text-${m.color}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${m.icon}"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-xs text-gray-500">${m.label}</p>
                                    <p class="text-sm font-bold text-gray-900">Bs ${parseFloat(data.resumen.totales[m.key] || 0).toFixed(2)}</p>
                                </div>
                            </div>
                        `).join('');
                    }
                },
                onError: (err) => {
                    console.warn('Error al actualizar datos de caja:', err);
                }
            });
            autoRefresh.start();

            // Auto-refresh para pacientes pendientes y UTI
            setInterval(() => {
                cargarPacientesPendientes();
                cargarPacientesUti();
            }, 3000);
        }

        // Cargar pacientes con cuenta pendiente
        async function cargarPacientesPendientes() {
            try {
                const response = await fetch('{{ route("caja.operativa.pacientes-pendientes") }}');
                const data = await response.json();
                
                if (data.success) {
                    pacientesData = data.cuentas;
                    renderizarTabla(pacientesData);
                }
            } catch (error) {
                console.error('Error:', error);
                document.getElementById('tablaPacientes').innerHTML = `
                    <tr><td colspan="7" class="px-6 py-4 text-center text-red-500">Error al cargar datos</td></tr>
                `;
            }
        }

        // Renderizar tabla de pacientes
        function renderizarTabla(cuentas) {
            const tbody = document.getElementById('tablaPacientes');
            
            if (cuentas.length === 0) {
                tbody.innerHTML = `
                    <tr><td colspan="7" class="px-6 py-4 text-center text-gray-500">No hay pacientes con cuenta pendiente</td></tr>
                `;
                return;
            }

            tbody.innerHTML = cuentas.map(cuenta => `
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div>
                                <div class="text-sm font-medium text-gray-900">${cuenta.paciente_nombre}</div>
                                <div class="text-sm text-gray-500">CI: ${cuenta.paciente_ci}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">${cuenta.tipo_atencion}</div>
                        ${cuenta.es_emergencia ? '<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">Emergencia</span>' : ''}
                        ${cuenta.es_post_pago ? '<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-orange-100 text-orange-800 ml-1">Post-pago</span>' : ''}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium text-gray-900">
                        Bs ${parseFloat(cuenta.total_calculado).toFixed(2)}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-600">
                        Bs ${parseFloat(cuenta.total_pagado).toFixed(2)}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold ${cuenta.saldo_pendiente > 0 ? 'text-red-600' : 'text-green-600'}">
                        Bs ${parseFloat(cuenta.saldo_pendiente).toFixed(2)}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-${cuenta.estado_color}-100 text-${cuenta.estado_color}-800">
                            ${cuenta.estado_label}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                        <button onclick="abrirModalCobro('${cuenta.id}')" class="text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 px-3 py-1 rounded-md transition">
                            Cobrar
                        </button>
                    </td>
                </tr>
            `).join('');
        }

        // Filtrar pacientes
        function filtrarPacientes() {
            const termino = document.getElementById('buscarPaciente').value.toLowerCase();
            const filtrados = pacientesData.filter(c =>
                c.paciente_nombre.toLowerCase().includes(termino) ||
                c.paciente_ci.toLowerCase().includes(termino)
            );
            renderizarTabla(filtrados);
        }

        // Cargar pacientes UTI listos para cobro
        async function cargarPacientesUti() {
            try {
                const response = await fetch('/caja-operativa/uti-pacientes?estado=alta_clinica');
                const data = await response.json();

                const tbody = document.getElementById('tablaPacientesUti');

                if (data.success && data.pacientes.length > 0) {
                    document.getElementById('contadorUti').textContent = data.pacientes.length;

                    tbody.innerHTML = data.pacientes.map(p => `
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">${p.paciente?.nombre || 'Sin nombre'}</div>
                                        <div class="text-sm text-gray-500">CI: ${p.paciente?.ci || '-'}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">Cama ${p.cama}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium text-gray-900">
                                ${p.dias_en_uti} días
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium ${p.tipo_pago === 'seguro' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800'}">
                                    ${p.tipo_pago === 'seguro' ? 'Seguro: ' + p.seguro : 'Particular'}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold text-gray-900">
                                Bs ${parseFloat(p.cuenta?.total || 0).toFixed(2)}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-${p.estado_color}-100 text-${p.estado_color}-800">
                                    ${p.estado_label}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                <a href="/uti-caja" class="text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 px-3 py-1 rounded-md transition">
                                    Ir a Cobro UTI
                                </a>
                            </td>
                        </tr>
                    `).join('');
                } else {
                    document.getElementById('contadorUti').textContent = '0';
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                No hay pacientes UTI con alta clínica pendientes de cobro
                            </td>
                        </tr>
                    `;
                }
            } catch (error) {
                console.error('Error cargando pacientes UTI:', error);
                document.getElementById('tablaPacientesUti').innerHTML = `
                    <tr><td colspan="7" class="px-6 py-4 text-center text-red-500">Error al cargar pacientes UTI</td></tr>
                `;
            }
        }

        // Cargar resumen del día
        async function cargarResumenDia() {
            try {
                const response = await fetch('{{ route("caja.operativa.resumen-dia") }}');
                const data = await response.json();
                
                if (data.success) {
                    document.getElementById('totalCobrado').textContent = 'Bs ' + parseFloat(data.resumen.totales.general).toFixed(2);
                    document.getElementById('totalTransacciones').textContent = data.resumen.transacciones.total;
                    document.getElementById('totalPendientes').textContent = data.resumen.cuentas.pendientes;
                    document.getElementById('totalParciales').textContent = data.resumen.cuentas.parciales;

                    // Renderizar métodos de pago
                    const metodos = [
                        { key: 'efectivo', label: 'Efectivo', icon: 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a1 1 0 11-2 0 1 1 0 012 0z', color: 'green' },
                        { key: 'transferencia', label: 'Transferencia', icon: 'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4', color: 'blue' },
                        { key: 'tarjeta', label: 'Tarjeta', icon: 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z', color: 'purple' },
                        { key: 'qr', label: 'QR', icon: 'M12 4v1m6 11h2m-6 0h-2v4h2v-4zM8 12h2v4H8v-4zm-2 4h2v4H6v-4zm10-4h2v4h-2v-4zM6 8h2v4H6V8zm10 0h2v4h-2V8z', color: 'orange' }
                    ];

                    document.getElementById('metodosPago').innerHTML = metodos.map(m => `
                        <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                            <div class="p-2 rounded-full bg-${m.color}-100">
                                <svg class="w-5 h-5 text-${m.color}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${m.icon}"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-xs text-gray-500">${m.label}</p>
                                <p class="text-sm font-bold text-gray-900">Bs ${parseFloat(data.resumen.totales[m.key] || 0).toFixed(2)}</p>
                            </div>
                        </div>
                    `).join('');
                }
            } catch (error) {
                console.error('Error:', error);
            }
        }

        // Abrir modal de cobro
        async function abrirModalCobro(cuentaId) {
            try {
                const response = await fetch(`{{ url('/caja-operativa/detalle-cuenta') }}/${cuentaId}`);
                const data = await response.json();
                
                if (data.success) {
                    cuentaActual = data.cuenta;
                    
                    // Mostrar detalle
                    document.getElementById('detalleCuenta').innerHTML = `
                        <div class="bg-gray-50 p-4 rounded-md">
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div>
                                    <span class="text-sm text-gray-500">Paciente:</span>
                                    <p class="font-medium">${data.cuenta.paciente.nombre}</p>
                                </div>
                                <div>
                                    <span class="text-sm text-gray-500">Tipo:</span>
                                    <p class="font-medium">${data.cuenta.tipo_atencion}</p>
                                </div>
                            </div>
                            <div class="border-t pt-3">
                                <h4 class="text-sm font-medium text-gray-900 mb-2">Detalle de Cargos:</h4>
                                <ul class="space-y-1 text-sm">
                                    ${data.cuenta.detalles.map(d => `
                                        <li class="flex justify-between">
                                            <span>${d.descripcion} (${d.cantidad}x)</span>
                                            <span>Bs ${parseFloat(d.subtotal).toFixed(2)}</span>
                                        </li>
                                    `).join('')}
                                </ul>
                                <div class="border-t mt-2 pt-2 flex justify-between font-bold">
                                    <span>Total:</span>
                                    <span>Bs ${parseFloat(data.cuenta.total_calculado).toFixed(2)}</span>
                                </div>
                                ${data.cuenta.seguro ? `
                                    <div class="flex justify-between text-sm text-green-600">
                                        <span>Seguro ${data.cuenta.seguro.nombre} (-${data.cuenta.seguro.ya_aplicado ? 'aplicado' : 'proyectado'}):</span>
                                        <span>-Bs ${parseFloat(data.cuenta.seguro.monto_cubierto).toFixed(2)}</span>
                                    </div>
                                    <div class="flex justify-between font-bold ${data.cuenta.seguro.monto_paciente > 0 ? 'text-red-600' : 'text-green-600'}">
                                        <span>A pagar por paciente:</span>
                                        <span>Bs ${parseFloat(data.cuenta.seguro.monto_paciente).toFixed(2)}</span>
                                    </div>
                                ` : `
                                    <div class="flex justify-between text-sm ${data.cuenta.total_pagado > 0 ? 'text-green-600' : ''}">
                                        <span>Pagado:</span>
                                        <span>Bs ${parseFloat(data.cuenta.total_pagado).toFixed(2)}</span>
                                    </div>
                                    <div class="flex justify-between font-bold ${data.cuenta.saldo_pendiente > 0 ? 'text-red-600' : 'text-green-600'}">
                                        <span>Saldo Pendiente:</span>
                                        <span>Bs ${parseFloat(data.cuenta.saldo_pendiente).toFixed(2)}</span>
                                    </div>
                                `}
                            </div>
                        </div>
                    `;
                    
                    // Pre-llenar datos de facturación si existen
                    if (data.cuenta.ci_nit_facturacion) {
                        document.getElementById('ciNitFactura').value = data.cuenta.ci_nit_facturacion;
                    }
                    if (data.cuenta.razon_social) {
                        document.getElementById('razonSocialFactura').value = data.cuenta.razon_social;
                    }
                    
                    // Pre-llenar monto con saldo pendiente o copago del seguro
                    const montoSugerido = data.cuenta.seguro ? data.cuenta.seguro.monto_paciente : data.cuenta.saldo_pendiente;
                    const montoSugeridoFloat = parseFloat(String(montoSugerido).replace(',', '.')) || 0;
                    document.getElementById('montoPago').value = montoSugeridoFloat.toFixed(2);
                    document.getElementById('montoPago').max = montoSugeridoFloat.toFixed(2);
                    
                    // Limpiar otros campos
                    document.getElementById('metodoPago').value = '';
                    document.getElementById('referenciaPago').value = '';
                    document.getElementById('esPagoTotal').checked = false;
                    
                    document.getElementById('modalCobro').classList.remove('hidden');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error al cargar detalle de la cuenta');
            }
        }

        // Cerrar modal de cobro
        function cerrarModalCobro() {
            document.getElementById('modalCobro').classList.add('hidden');
            cuentaActual = null;
        }

        // Procesar cobro
        async function procesarCobro() {
            if (!cuentaActual) return;
            
            // Sanitizar el monto: reemplazar coma por punto y convertir a número
            const montoRaw = String(document.getElementById('montoPago').value).replace(',', '.');
            const montoParsed = parseFloat(montoRaw);

            if (isNaN(montoParsed) || montoParsed <= 0) {
                alert('Ingrese un monto válido mayor a 0');
                return;
            }

            const formData = {
                cuenta_cobro_id: cuentaActual.id,
                monto: montoParsed,
                metodo_pago: document.getElementById('metodoPago').value,
                referencia: document.getElementById('referenciaPago').value,
                ci_nit_facturacion: document.getElementById('ciNitFactura').value,
                razon_social: document.getElementById('razonSocialFactura').value,
                es_pago_total: document.getElementById('esPagoTotal').checked
            };

            if (!formData.metodo_pago) {
                alert('Seleccione un método de pago');
                return;
            }

            try {
                const response = await fetch('{{ route("caja.operativa.procesar-cobro") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(formData)
                });
                
                const data = await response.json();
                
                if (data.success) {
                    alert(data.message);
                    if (data.print_url) {
                        window.open(data.print_url, '_blank');
                    }
                    cerrarModalCobro();
                    cargarPacientesPendientes();
                    cargarPacientesUti();
                    cargarResumenDia();
                } else {
                    alert(data.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error al procesar el cobro');
            }
        }

        // Mostrar modal de cierre
        async function mostrarModalCierre() {
            try {
                const response = await fetch('{{ route("caja.operativa.resumen-dia") }}');
                const data = await response.json();
                
                if (data.success) {
                    document.getElementById('resumenCierre').innerHTML = `
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Monto Inicial:</span>
                                <span class="font-medium">Bs ${parseFloat(data.resumen.monto_inicial).toFixed(2)}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Total Ingresos:</span>
                                <span class="font-medium text-green-600">+ Bs ${parseFloat(data.resumen.totales.general).toFixed(2)}</span>
                            </div>
                            <div class="border-t pt-2 flex justify-between font-bold">
                                <span>Total Esperado:</span>
                                <span>Bs ${(parseFloat(data.resumen.monto_inicial) + parseFloat(data.resumen.totales.general)).toFixed(2)}</span>
                            </div>
                        </div>
                    `;
                    document.getElementById('montoFinal').value = '';
                    document.getElementById('observacionesCierre').value = '';
                    document.getElementById('modalCierre').classList.remove('hidden');
                }
            } catch (error) {
                console.error('Error:', error);
            }
        }

        // Cerrar modal de cierre
        function cerrarModalCierre() {
            document.getElementById('modalCierre').classList.add('hidden');
        }

        // Cerrar caja
        async function cerrarCaja() {
            const montoFinal = document.getElementById('montoFinal').value;
            
            if (!montoFinal) {
                alert('Ingrese el monto final');
                return;
            }

            if (!confirm('¿Está seguro de cerrar la caja? Esta acción no se puede deshacer.')) {
                return;
            }

            try {
                const response = await fetch('{{ route("caja.operativa.cerrar") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        monto_final: montoFinal,
                        observaciones: document.getElementById('observacionesCierre').value
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    alert('Caja cerrada correctamente');
                    window.location.reload();
                } else {
                    alert(data.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error al cerrar caja');
            }
        }

        // Actualizar monto al marcar pago total
        document.getElementById('esPagoTotal').addEventListener('change', function() {
            if (this.checked && cuentaActual) {
                const montoSugerido = cuentaActual.seguro ? cuentaActual.seguro.monto_paciente : cuentaActual.saldo_pendiente;
                document.getElementById('montoPago').value = montoSugerido;
            }
        });
    </script>
@endpush
@endsection
