@extends('layouts.app')

@section('content')
<div class="p-8 bg-[#f8fafc] min-h-screen font-sans">
    <div class="max-w-7xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Gestión de Caja</h1>
                <p class="text-gray-500 text-sm">Panel administrativo - {{ now()->format('d/m/Y H:i') }}</p>
            </div>
        </div>
            <!-- Estadísticas principales -->
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4">
                    <p class="text-xs font-medium text-gray-500 uppercase">Total Hoy</p>
                    <p class="text-xl font-bold text-gray-900" id="statTotalHoy">Bs. {{ number_format($estadisticas['total_recaudado_hoy'], 2) }}</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4">
                    <p class="text-xs font-medium text-gray-500 uppercase">Transacciones</p>
                    <p class="text-xl font-bold text-gray-900" id="statTransacciones">{{ $estadisticas['transacciones_hoy'] }}</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4">
                    <p class="text-xs font-medium text-gray-500 uppercase">Cajas Abiertas</p>
                    <p class="text-xl font-bold text-green-600" id="statCajasAbiertas">{{ $estadisticas['cajas_abiertas'] }}</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4">
                    <p class="text-xs font-medium text-gray-500 uppercase">Cajas Cerradas</p>
                    <p class="text-xl font-bold text-blue-600" id="statCajasCerradas">{{ $estadisticas['cajas_cerradas_hoy'] }}</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4">
                    <p class="text-xs font-medium text-gray-500 uppercase">Pendientes</p>
                    <p class="text-xl font-bold text-yellow-600" id="statPendientes">{{ $estadisticas['cuentas_pendientes'] }}</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4">
                    <p class="text-xs font-medium text-gray-500 uppercase">Emergencias</p>
                    <p class="text-xl font-bold text-red-600" id="statEmergencias">{{ $estadisticas['emergencias_pendientes'] }}</p>
                </div>
            </div>

            <!-- Desglose por método de pago y cajas abiertas -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Métodos de pago -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Recaudación por Método de Pago - Hoy</h3>
                    </div>
                    <div class="p-4">
                        <div class="space-y-3" id="metodosPagoContainer">
                            @foreach($metodosPagoHoy as $metodo => $monto)
                            <div class="flex justify-between items-center p-2 bg-gray-50 rounded">
                                <span class="text-sm text-gray-600 capitalize">{{ $metodo }}:</span>
                                <span class="font-medium">Bs. {{ number_format($monto, 2) }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Cajas abiertas -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Cajas Abiertas</h3>
                    </div>
                    <div class="p-4">
                        <div class="space-y-3" id="cajasAbiertasContainer">
                            @forelse($cajasAbiertas as $caja)
                            <div class="flex justify-between items-center p-3 bg-green-50 border border-green-200 rounded-lg">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $caja['usuario'] }}</p>
                                    <p class="text-sm text-gray-500">{{ $caja['fecha_apertura'] }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm text-gray-600">Inicial: <span class="font-medium">Bs. {{ number_format($caja['monto_inicial'], 2) }}</span></p>
                                    <p class="text-sm text-green-600">+ Bs. {{ number_format($caja['total_ingresos'], 2) }}</p>
                                </div>
                            </div>
                            @empty
                            <p class="text-gray-500 text-center py-4">No hay cajas abiertas</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabs para diferentes secciones -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex" aria-label="Tabs">
                        <button onclick="cambiarTab('transacciones')" id="tab-transacciones" class="w-1/5 py-4 px-1 text-center border-b-2 border-blue-500 text-blue-600 font-medium text-sm">
                            Transacciones
                        </button>
                        <button onclick="cambiarTab('control')" id="tab-control" class="w-1/5 py-4 px-1 text-center border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium text-sm">
                            Control de Cajas
                        </button>
                        <button onclick="cambiarTab('resumen')" id="tab-resumen" class="w-1/5 py-4 px-1 text-center border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium text-sm">
                            Resumen Financiero
                        </button>
                        <button onclick="cambiarTab('auditoria')" id="tab-auditoria" class="w-1/5 py-4 px-1 text-center border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium text-sm">
                            Auditoría
                        </button>
                        <button onclick="cambiarTab('items-eliminados')" id="tab-items-eliminados" class="w-1/5 py-4 px-1 text-center border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium text-sm">
                            Ítems Eliminados
                        </button>
                    </nav>
                </div>

                <!-- Tab: Transacciones -->
                <div id="panel-transacciones" class="p-4">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4 space-y-2 sm:space-y-0">
                        <h4 class="text-md font-medium text-gray-900">Listado de Transacciones</h4>
                        <div class="flex items-center space-x-2">
                            <input type="date" id="filtroFechaInicio" class="text-sm rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <span class="text-gray-500">-</span>
                            <input type="date" id="filtroFechaFin" class="text-sm rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <select id="filtroEstado" class="text-sm rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="todos">Todos los estados</option>
                                <option value="pagado">Pagado</option>
                                <option value="parcial">Parcial</option>
                                <option value="pendiente">Pendiente</option>
                            </select>
                            <select id="filtroTipoFlujo" class="text-sm rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="todos">Todos los flujos</option>
                                <option value="normal">Normal</option>
                                <option value="emergencia">Emergencia</option>
                            </select>
                            <button onclick="cargarTransacciones()" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </button>
                            <button onclick="verTodoHistorial()" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-blue-700 bg-blue-50 hover:bg-blue-100">
                                Ver Todo el Historial
                            </button>
                        </div>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Paciente</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Flujo</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Monto</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Pagado</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Saldo</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Estado</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Métodos</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="tablaTransacciones">
                                <tr><td colspan="10" class="px-4 py-4 text-center text-gray-500">Cargando...</td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div id="paginacionTransacciones" class="mt-4"></div>
                </div>

                <!-- Tab: Control de Cajas -->
                <div id="panel-control" class="p-4 hidden">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4 space-y-2 sm:space-y-0">
                        <h4 class="text-md font-medium text-gray-900">Control de Aperturas y Cierres</h4>
                        <div class="flex items-center space-x-2">
                            <input type="date" id="filtroCajaFechaInicio" class="text-sm rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <span class="text-gray-500">-</span>
                            <input type="date" id="filtroCajaFechaFin" class="text-sm rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <select id="filtroCajaEstado" class="text-sm rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="todas">Todas</option>
                                <option value="abierta">Abiertas</option>
                                <option value="cerrada">Cerradas</option>
                            </select>
                            <button onclick="cargarControlCajas()" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Usuario</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Apertura</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cierre</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Monto Inicial</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Ingresos</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Monto Final</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Diferencia</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Trans.</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Estado</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="tablaControlCajas">
                                <tr><td colspan="10" class="px-4 py-4 text-center text-gray-500">Cargando...</td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div id="paginacionControlCajas" class="mt-4"></div>
                </div>

                <!-- Tab: Resumen Financiero -->
                <div id="panel-resumen" class="p-4 hidden">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-md font-medium text-gray-900">Resumen Financiero por Período</h4>
                        <div class="flex items-center space-x-2">
                            <input type="date" id="resumenFechaInicio" value="{{ now()->startOfMonth()->format('Y-m-d') }}" class="text-sm rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <span class="text-gray-500">-</span>
                            <input type="date" id="resumenFechaFin" value="{{ now()->format('Y-m-d') }}" class="text-sm rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <button onclick="cargarResumenFinanciero()" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700">
                                Generar Resumen
                            </button>
                        </div>
                    </div>
                    
                    <div id="resumenFinancieroContent">
                        <p class="text-gray-500 text-center py-8">Seleccione un rango de fechas y haga clic en "Generar Resumen"</p>
                    </div>
                </div>

                <!-- Tab: Auditoría -->
                <div id="panel-auditoria" class="p-4 hidden">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4 space-y-2 sm:space-y-0">
                        <h4 class="text-md font-medium text-gray-900">Auditoría de Movimientos</h4>
                        <div class="flex items-center space-x-2">
                            <input type="date" id="filtroAuditoriaFecha" class="text-sm rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <select id="filtroAuditoriaTipo" class="text-sm rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="todos">Todas las acciones</option>
                                <option value="apertura">Apertura</option>
                                <option value="cierre">Cierre</option>
                                <option value="pago">Pago</option>
                                <option value="ingreso">Ingreso</option>
                                <option value="egreso">Egreso</option>
                            </select>
                            <button onclick="cargarAuditoria()" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Usuario</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Concepto</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Monto</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Método</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Referencia</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="tablaAuditoria">
                                <tr><td colspan="7" class="px-4 py-4 text-center text-gray-500">Cargando...</td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div id="paginacionAuditoria" class="mt-4"></div>
                </div>

                <!-- Tab: Ítems Eliminados -->
                <div id="panel-items-eliminados" class="p-4 hidden">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4 space-y-2 sm:space-y-0">
                        <h4 class="text-md font-medium text-gray-900">Ítems Eliminados de Cuentas</h4>
                        <div class="flex items-center space-x-2">
                            <input type="date" id="filtroItemsEliminadosFechaInicio" class="text-sm rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <span class="text-gray-500">-</span>
                            <input type="date" id="filtroItemsEliminadosFechaFin" class="text-sm rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <input type="text" id="filtroItemsEliminadosCuenta" placeholder="ID cuenta..." class="text-sm rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <button onclick="cargarItemsEliminados()" class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha Eliminación</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cuenta</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Paciente</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Descripción</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Cant.</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Precio Unit.</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Motivo</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Eliminado por</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="tablaItemsEliminados">
                                <tr><td colspan="10" class="px-4 py-4 text-center text-gray-500">Cargando...</td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div id="paginacionItemsEliminados" class="mt-4"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Detalle de Transacción -->
    <div id="modalDetalle" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="cerrarModalDetalle()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Detalle de Transacción</h3>
                    <div id="detalleTransaccionContent">
                        <!-- Se llena con JS -->
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" onclick="cerrarModalDetalle()" class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        let transaccionesPage = 1;
        let controlCajasPage = 1;
        let auditoriaPage = 1;
        let itemsEliminadosPage = 1;
        const userRole = @json(auth()->user()->role);

        document.addEventListener('DOMContentLoaded', function() {
            cargarEstadisticas();
            cargarTransacciones();
            
            // Set fechas por defecto
            const hoy = new Date().toISOString().split('T')[0];
            document.getElementById('filtroFechaInicio').value = hoy;
            document.getElementById('filtroFechaFin').value = hoy;
            document.getElementById('filtroCajaFechaInicio').value = hoy;
            document.getElementById('filtroCajaFechaFin').value = hoy;
            document.getElementById('filtroAuditoriaFecha').value = hoy;
            document.getElementById('filtroItemsEliminadosFechaInicio').value = hoy;
            document.getElementById('filtroItemsEliminadosFechaFin').value = hoy;
        });

        // Cambiar entre tabs
        function cambiarTab(tab) {
            // Ocultar todos los panels
            ['transacciones', 'control', 'resumen', 'auditoria', 'items-eliminados'].forEach(t => {
                document.getElementById('panel-' + t).classList.add('hidden');
                document.getElementById('tab-' + t).classList.remove('border-blue-500', 'text-blue-600');
                document.getElementById('tab-' + t).classList.add('border-transparent', 'text-gray-500');
            });
            
            // Mostrar panel seleccionado
            document.getElementById('panel-' + tab).classList.remove('hidden');
            document.getElementById('tab-' + tab).classList.remove('border-transparent', 'text-gray-500');
            document.getElementById('tab-' + tab).classList.add('border-blue-500', 'text-blue-600');
            
            // Cargar datos según tab
            if (tab === 'control') cargarControlCajas();
            if (tab === 'auditoria') cargarAuditoria();
            if (tab === 'items-eliminados') cargarItemsEliminados();
        }

        // Cargar estadísticas principales desde el servidor
        async function cargarEstadisticas() {
            try {
                const response = await fetch('{{ route('caja.gestion.resumen-financiero') }}?fecha_inicio={{ now()->toDateString() }}&fecha_fin={{ now()->toDateString() }}');
                const data = await response.json();
                
                if (data.success) {
                    const r = data.resumen;
                    document.getElementById('statTotalHoy').textContent = 'Bs ' + parseFloat(r.totales_generales.total_recaudado).toFixed(2);
                    document.getElementById('statTransacciones').textContent = r.totales_generales.total_transacciones;
                    
                    // Actualizar métodos de pago
                    document.getElementById('metodosPagoContainer').innerHTML = Object.entries(r.por_metodo_pago).map(([k, v]) => `
                        <div class="flex justify-between items-center p-2 bg-gray-50 rounded">
                            <span class="text-sm text-gray-600 capitalize">${k}:</span>
                            <span class="font-medium">Bs ${parseFloat(v).toFixed(2)}</span>
                        </div>
                    `).join('');
                }
            } catch (error) {
                console.error('Error cargando estadísticas:', error);
            }
        }

        // Cargar transacciones
        async function cargarTransacciones(page = 1) {
            try {
                const params = new URLSearchParams({
                    fecha_inicio: document.getElementById('filtroFechaInicio').value,
                    fecha_fin: document.getElementById('filtroFechaFin').value,
                    estado: document.getElementById('filtroEstado').value,
                    tipo_flujo: document.getElementById('filtroTipoFlujo').value,
                    page: page
                });

                const response = await fetch(`{{ route('caja.gestion.transacciones') }}?${params}`);
                const data = await response.json();
                
                if (data.success) {
                    renderizarTransacciones(data.transacciones.data);
                    renderizarPaginacion('paginacionTransacciones', data.transacciones, 'cargarTransacciones');
                    
                    // Actualizar estadísticas
                    if (page === 1) {
                        actualizarEstadisticasDesdeTransacciones(data.transacciones.data);
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                document.getElementById('tablaTransacciones').innerHTML = '<tr><td colspan="10" class="px-4 py-4 text-center text-red-500">Error al cargar transacciones</td></tr>';
            }
        }

        function verTodoHistorial() {
            document.getElementById('filtroFechaInicio').value = '';
            document.getElementById('filtroFechaFin').value = '';
            document.getElementById('filtroEstado').value = 'todos';
            document.getElementById('filtroTipoFlujo').value = 'todos';
            cargarTransacciones(1);
        }

        function renderizarTransacciones(transacciones) {
            const tbody = document.getElementById('tablaTransacciones');
            
            if (transacciones.length === 0) {
                tbody.innerHTML = '<tr><td colspan="10" class="px-4 py-4 text-center text-gray-500">No hay transacciones</td></tr>';
                return;
            }

            tbody.innerHTML = transacciones.map(t => `
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">${t.id.substring(0, 15)}...</td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">${t.paciente.nombre}</td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">${t.tipo_atencion}</td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                        ${t.tipo_flujo === 'emergencia' 
                            ? `<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">Emergencia ${t.es_post_pago ? '(Post-pago)' : ''}</span>`
                            : `<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">Normal</span>`
                        }
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-right font-medium">Bs ${parseFloat(t.monto).toFixed(2)}</td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-right text-green-600">Bs ${parseFloat(t.total_pagado).toFixed(2)}</td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-right ${t.saldo_pendiente > 0 ? 'text-red-600' : 'text-gray-500'}">Bs ${parseFloat(t.saldo_pendiente).toFixed(2)}</td>
                    <td class="px-4 py-3 whitespace-nowrap text-center">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-${t.estado_color}-100 text-${t.estado_color}-800">${t.estado_label}</span>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">${t.metodos_pago.join(', ')}</td>
                    <td class="px-4 py-3 whitespace-nowrap text-center">
                        <button onclick="verDetalleTransaccion('${t.id}')" class="text-blue-600 hover:text-blue-900">
                            Ver
                        </button>
                    </td>
                </tr>
            `).join('');
        }

        // Cargar control de cajas
        async function cargarControlCajas(page = 1) {
            try {
                const params = new URLSearchParams({
                    fecha_inicio: document.getElementById('filtroCajaFechaInicio').value,
                    fecha_fin: document.getElementById('filtroCajaFechaFin').value,
                    estado: document.getElementById('filtroCajaEstado').value,
                    page: page
                });

                const response = await fetch(`{{ route('caja.gestion.control-cajas') }}?${params}`);
                const data = await response.json();
                
                if (data.success) {
                    const tbody = document.getElementById('tablaControlCajas');
                    
                    if (data.cajas.data.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="10" class="px-4 py-4 text-center text-gray-500">No hay registros</td></tr>';
                        return;
                    }

                    tbody.innerHTML = data.cajas.data.map(c => `
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">${c.id}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">${c.user ? c.user.nombre : 'N/A'}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">${c.fecha_apertura}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">${c.fecha_cierre || '-'}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-right">Bs ${parseFloat(c.monto_inicial).toFixed(2)}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-right text-green-600">+ Bs ${parseFloat(c.total_ingresos).toFixed(2)}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-right font-medium">Bs ${parseFloat(c.monto_final || 0).toFixed(2)}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-center">
                                ${c.diferencia !== null 
                                    ? `<span class="${Math.abs(c.diferencia) < 0.01 ? 'text-green-600' : 'text-red-600 font-bold'}">Bs ${parseFloat(c.diferencia).toFixed(2)}</span>`
                                    : '-'
                                }
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-center text-sm">${c.transacciones_count}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-${c.estado === 'abierta' ? 'green' : 'gray'}-100 text-${c.estado === 'abierta' ? 'green' : 'gray'}-800">${c.estado}</span>
                            </td>
                        </tr>
                    `).join('');
                    
                    renderizarPaginacion('paginacionControlCajas', data.cajas, 'cargarControlCajas');
                } else {
                    document.getElementById('tablaControlCajas').innerHTML = `<tr><td colspan="10" class="px-4 py-4 text-center text-red-500">${data.message || 'Error al cargar datos'}</td></tr>`;
                }
            } catch (error) {
                console.error('Error:', error);
                document.getElementById('tablaControlCajas').innerHTML = '<tr><td colspan="10" class="px-4 py-4 text-center text-red-500">Error de red o servidor</td></tr>';
            }
        }

        // Cargar resumen financiero
        async function cargarResumenFinanciero() {
            try {
                const params = new URLSearchParams({
                    fecha_inicio: document.getElementById('resumenFechaInicio').value,
                    fecha_fin: document.getElementById('resumenFechaFin').value
                });

                const response = await fetch(`{{ route('caja.gestion.resumen-financiero') }}?${params}`);
                const data = await response.json();
                
                if (data.success) {
                    const r = data.resumen;
                    document.getElementById('resumenFinancieroContent').innerHTML = `
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h5 class="font-medium text-gray-900 mb-3">Totales Generales</h5>
                                <div class="space-y-2">
                                    <div class="flex justify-between"><span class="text-gray-600">Total Recaudado:</span><span class="font-bold">Bs ${parseFloat(r.totales_generales.total_recaudado).toFixed(2)}</span></div>
                                    <div class="flex justify-between"><span class="text-gray-600">Total Transacciones:</span><span class="font-bold">${r.totales_generales.total_transacciones}</span></div>
                                </div>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h5 class="font-medium text-gray-900 mb-3">Por Método de Pago</h5>
                                <div class="space-y-2">
                                    ${Object.entries(r.por_metodo_pago).map(([k, v]) => `
                                        <div class="flex justify-between"><span class="text-gray-600 capitalize">${k}:</span><span class="font-medium">Bs ${parseFloat(v).toFixed(2)}</span></div>
                                    `).join('')}
                                </div>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h5 class="font-medium text-gray-900 mb-3">Flujos</h5>
                                <div class="space-y-2">
                                    <div class="flex justify-between"><span class="text-gray-600">Normal:</span><span class="font-medium">${r.flujos.normal.cantidad} trans. - Bs ${parseFloat(r.flujos.normal.monto).toFixed(2)}</span></div>
                                    <div class="flex justify-between"><span class="text-gray-600">Emergencias:</span><span class="font-medium">${r.flujos.emergencia.cantidad} trans. (${r.flujos.emergencia.post_pago} post-pago) - Bs ${parseFloat(r.flujos.emergencia.monto).toFixed(2)}</span></div>
                                </div>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h5 class="font-medium text-gray-900 mb-3">Cuentas Pendientes</h5>
                                <div class="space-y-2">
                                    <div class="flex justify-between"><span class="text-gray-600">Total Pendientes:</span><span class="font-bold text-yellow-600">${r.pendientes.total}</span></div>
                                    <div class="flex justify-between"><span class="text-gray-600">Monto Pendiente:</span><span class="font-bold text-yellow-600">Bs ${parseFloat(r.pendientes.monto).toFixed(2)}</span></div>
                                    <div class="flex justify-between"><span class="text-gray-600">Emergencias sin pagar:</span><span class="font-bold text-red-600">${r.pendientes.emergencias}</span></div>
                                </div>
                            </div>
                        </div>
                    `;
                }
            } catch (error) {
                console.error('Error:', error);
                document.getElementById('resumenFinancieroContent').innerHTML = '<p class="text-red-500 text-center py-8">Error al cargar resumen</p>';
            }
        }

        // Cargar auditoría
        async function cargarAuditoria(page = 1) {
            try {
                const params = new URLSearchParams({
                    fecha_inicio: document.getElementById('filtroAuditoriaFecha').value,
                    tipo_accion: document.getElementById('filtroAuditoriaTipo').value,
                    page: page
                });

                const response = await fetch(`{{ route('caja.gestion.auditoria') }}?${params}`);
                const data = await response.json();
                
                if (data.success) {
                    const tbody = document.getElementById('tablaAuditoria');
                    
                    if (data.movimientos.data.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="7" class="px-4 py-4 text-center text-gray-500">No hay movimientos</td></tr>';
                        return;
                    }

                    tbody.innerHTML = data.movimientos.data.map(m => `
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">${m.fecha}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">${m.usuario}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-${m.tipo === 'ingreso' || m.tipo === 'apertura' ? 'green' : m.tipo === 'egreso' || m.tipo === 'cierre' ? 'red' : 'blue'}-100 text-${m.tipo === 'ingreso' || m.tipo === 'apertura' ? 'green' : m.tipo === 'egreso' || m.tipo === 'cierre' ? 'red' : 'blue'}-800">${m.tipo_label}</span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">${m.concepto}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-right font-medium ${m.tipo === 'egreso' || m.tipo === 'cierre' ? 'text-red-600' : 'text-green-600'}">${m.monto_formateado}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-center text-gray-500 capitalize">${m.metodo_pago || '-'}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">${m.referencia || '-'}</td>
                        </tr>
                    `).join('');
                    
                    renderizarPaginacion('paginacionAuditoria', data.movimientos, 'cargarAuditoria');
                }
            } catch (error) {
                console.error('Error:', error);
                document.getElementById('tablaAuditoria').innerHTML = '<tr><td colspan="7" class="px-4 py-4 text-center text-red-500">Error al cargar auditoría</td></tr>';
            }
        }

        // Ver detalle de transacción
        async function verDetalleTransaccion(id) {
            try {
                const response = await fetch(`/caja-gestion/transaccion/${id}`);
                const data = await response.json();
                
                if (data.success) {
                    const t = data.transaccion;
                    document.getElementById('detalleTransaccionContent').innerHTML = `
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div><span class="text-sm text-gray-500">Paciente:</span><p class="font-medium">${t.paciente.nombre} (CI: ${t.paciente.ci})</p></div>
                            <div><span class="text-sm text-gray-500">Tipo Atención:</span><p class="font-medium">${t.tipo_atencion}</p></div>
                            <div><span class="text-sm text-gray-500">Flujo:</span><p class="font-medium">${t.tipo_flujo === 'emergencia' ? '<span class="text-red-600">Emergencia</span>' + (t.es_post_pago ? ' (Post-pago)' : '') : '<span class="text-blue-600">Normal (Pre-pago)</span>'}</p></div>
                            <div><span class="text-sm text-gray-500">Estado:</span><p class="font-medium">${t.estado_label}</p></div>
                        </div>
                        <div class="border-t pt-4 mb-4">
                            <h5 class="font-medium text-gray-900 mb-2">Detalle de Cargos:</h5>
                            <table class="min-w-full text-sm">
                                <thead><tr class="border-b"><th class="text-left py-1">Item</th><th class="text-right py-1">Cant.</th><th class="text-right py-1">Precio</th><th class="text-right py-1">Subtotal</th>${(userRole === 'admin' || userRole === 'administrador') && t.estado !== 'pagado' ? '<th class="text-center py-1">Acciones</th>' : ''}</tr></thead>
                                <tbody>
                                    ${t.detalles.map(d => `<tr class="border-b border-gray-100"><td class="py-1">${d.descripcion}</td><td class="text-right">${d.cantidad}</td><td class="text-right">Bs ${parseFloat(d.precio_unitario).toFixed(2)}</td><td class="text-right">Bs ${parseFloat(d.subtotal).toFixed(2)}</td>${(userRole === 'admin' || userRole === 'administrador') && t.estado !== 'pagado' ? `<td class="text-center py-1"><button onclick="eliminarDetalleItem('${d.id}', '${d.descripcion.replace(/'/g, "\\'")}')" class="text-red-600 hover:text-red-900 text-xs">Eliminar</button></td>` : ''}</tr>`).join('')}
                                </tbody>
                                <tfoot>
                                    <tr class="font-bold"><td colspan="3" class="text-right py-2">Total:</td><td class="text-right py-2">Bs ${parseFloat(t.total_calculado).toFixed(2)}</td></tr>
                                    <tr class="text-green-600"><td colspan="3" class="text-right py-1">Pagado:</td><td class="text-right py-1">Bs ${parseFloat(t.total_pagado).toFixed(2)}</td></tr>
                                    <tr class="${t.saldo_pendiente > 0 ? 'text-red-600' : 'text-green-600'} font-bold"><td colspan="3" class="text-right py-1">Saldo:</td><td class="text-right py-1">Bs ${parseFloat(t.saldo_pendiente).toFixed(2)}</td></tr>
                                </tfoot>
                            </table>
                        </div>
                        <div class="border-t pt-4 mb-4">
                            <h5 class="font-medium text-gray-900 mb-2">Pagos Realizados:</h5>
                            <div class="space-y-2">
                                ${t.pagos.map(p => `
                                    <div class="flex justify-between items-center bg-gray-50 p-2 rounded">
                                        <div>
                                            <span class="font-medium">${p.metodo_pago}</span>
                                            ${p.referencia ? `<span class="text-sm text-gray-500 ml-2">(Ref: ${p.referencia})</span>` : ''}
                                            <span class="text-sm text-gray-500 ml-2">- ${p.usuario}</span>
                                        </div>
                                        <div class="font-bold">Bs ${parseFloat(p.monto).toFixed(2)}</div>
                                    </div>
                                `).join('')}
                            </div>
                        </div>
                        ${t.ci_nit_facturacion ? `
                            <div class="border-t pt-4">
                                <h5 class="font-medium text-gray-900 mb-2">Datos de Facturación:</h5>
                                <p class="text-sm"><span class="text-gray-500">CI/NIT:</span> ${t.ci_nit_facturacion}</p>
                                <p class="text-sm"><span class="text-gray-500">Razón Social:</span> ${t.razon_social}</p>
                            </div>
                        ` : '<div class="border-t pt-4"><p class="text-yellow-600 text-sm"><strong>Sin datos de facturación completos</strong></p></div>'}
                    `;
                    document.getElementById('detalleTransaccionContent').dataset.cuentaId = id;
                    document.getElementById('modalDetalle').classList.remove('hidden');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error al cargar detalle');
            }
        }

        async function eliminarDetalleItem(detalleId, descripcion) {
            const motivo = prompt('Motivo de eliminación del ítem: ' + descripcion);
            if (!motivo || motivo.trim() === '') return;

            if (!confirm('¿Confirmar eliminación del ítem "' + descripcion + '"?')) return;

            try {
                const url = '{{ route('caja.gestion.eliminar-detalle', ['detalleId' => 'DETALLE_ID']) }}'.replace('DETALLE_ID', detalleId);
                const response = await fetch(url, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ motivo: motivo.trim() })
                });

                const data = await response.json();
                if (data.success) {
                    alert('Ítem eliminado correctamente.');
                    const cuentaIdActual = document.getElementById('detalleTransaccionContent').dataset.cuentaId;
                    if (cuentaIdActual) verDetalleTransaccion(cuentaIdActual);
                    cargarTransacciones();
                } else {
                    alert(data.message || 'Error al eliminar.');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error de red al eliminar ítem.');
            }
        }

        async function cargarItemsEliminados(page = 1) {
            try {
                const params = new URLSearchParams({
                    fecha_inicio: document.getElementById('filtroItemsEliminadosFechaInicio').value,
                    fecha_fin: document.getElementById('filtroItemsEliminadosFechaFin').value,
                    cuenta_cobro_id: document.getElementById('filtroItemsEliminadosCuenta').value,
                    page: page
                });

                const response = await fetch(`{{ route('caja.gestion.detalles-eliminados') }}?${params}`);
                const data = await response.json();

                if (data.success) {
                    const tbody = document.getElementById('tablaItemsEliminados');
                    if (data.eliminados.data.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="10" class="px-4 py-4 text-center text-gray-500">No hay ítems eliminados</td></tr>';
                        return;
                    }

                    tbody.innerHTML = data.eliminados.data.map(item => `
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">${item.eliminado_en}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">${item.cuenta_cobro_id.substring(0, 15)}...</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">${item.paciente.nombre}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">${item.tipo_item}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">${item.descripcion}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-right">${item.cantidad}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-right">Bs ${parseFloat(item.precio_unitario).toFixed(2)}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-right font-medium text-red-600">Bs ${parseFloat(item.subtotal).toFixed(2)}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">${item.motivo_eliminacion}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">${item.usuario}</td>
                        </tr>
                    `).join('');

                    renderizarPaginacion('paginacionItemsEliminados', data.eliminados, 'cargarItemsEliminados');
                } else {
                    document.getElementById('tablaItemsEliminados').innerHTML = `<tr><td colspan="10" class="px-4 py-4 text-center text-red-500">${data.message || 'Error al cargar datos'}</td></tr>`;
                }
            } catch (error) {
                console.error('Error:', error);
                document.getElementById('tablaItemsEliminados').innerHTML = '<tr><td colspan="10" class="px-4 py-4 text-center text-red-500">Error de red o servidor</td></tr>';
            }
        }

        function cerrarModalDetalle() {
            document.getElementById('modalDetalle').classList.add('hidden');
        }

        // Renderizar paginación
        function renderizarPaginacion(containerId, data, callbackName) {
            if (!data.data.length) {
                document.getElementById(containerId).innerHTML = '';
                return;
            }

            let html = '<div class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-3 sm:px-6"><div class="flex flex-1 justify-between sm:hidden">';
            
            if (data.prev_page_url) {
                html += `<button onclick="${callbackName}(${data.current_page - 1})" class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Anterior</button>`;
            }
            if (data.next_page_url) {
                html += `<button onclick="${callbackName}(${data.current_page + 1})" class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Siguiente</button>`;
            }
            
            html += '</div><div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between"><div><p class="text-sm text-gray-700">Mostrando <span class="font-medium">' + data.from + '</span> a <span class="font-medium">' + data.to + '</span> de <span class="font-medium">' + data.total + '</span> resultados</p></div><div><nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">';
            
            // Simplified pagination for demo
            for (let i = 1; i <= data.last_page; i++) {
                if (i === data.current_page) {
                    html += `<span aria-current="page" class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-white bg-blue-600 focus:z-20 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">${i}</span>`;
                } else {
                    html += `<button onclick="${callbackName}(${i})" class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0">${i}</button>`;
                }
            }
            
            html += '</nav></div></div></div>';
            document.getElementById(containerId).innerHTML = html;
        }

        // Actualizar estadísticas desde transacciones cargadas
        function actualizarEstadisticasDesdeTransacciones(transacciones) {
            // Calcular totales
            let totalHoy = 0;
            let totalTrans = transacciones.length;
            let pendientes = transacciones.filter(t => t.estado === 'pendiente').length;
            let emergencias = transacciones.filter(t => t.tipo_flujo === 'emergencia').length;
            
            const metodos = { efectivo: 0, transferencia: 0, tarjeta: 0, qr: 0 };
            
            transacciones.forEach(t => {
                if (t.estado === 'pagado') {
                    totalHoy += parseFloat(t.monto);
                }
                // metodos_pago es un array de strings
                const metodosArray = t.metodos_pago || [];
                metodosArray.forEach(m => {
                    const metodo = m.toLowerCase();
                    if (metodos.hasOwnProperty(metodo)) {
                        metodos[metodo] += parseFloat(t.total_pagado) / metodosArray.length;
                    }
                });
            });

            document.getElementById('statTotalHoy').textContent = 'Bs ' + totalHoy.toFixed(2);
            document.getElementById('statTransacciones').textContent = totalTrans;
            document.getElementById('statPendientes').textContent = pendientes;
            document.getElementById('statEmergencias').textContent = emergencias;

            // Renderizar métodos de pago
            document.getElementById('metodosPagoContainer').innerHTML = Object.entries(metodos).map(([k, v]) => `
                <div class="flex justify-between items-center p-2 bg-gray-50 rounded">
                    <span class="text-sm text-gray-600 capitalize">${k}:</span>
                    <span class="font-medium">Bs ${v.toFixed(2)}</span>
                </div>
            `).join('');
        }
    </script>
@endpush
@endsection
