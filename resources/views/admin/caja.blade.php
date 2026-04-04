@extends('layouts.app')

@section('content')
<div class="w-full p-8 bg-[#f8fafc] min-h-screen font-sans antialiased">
        <div class="flex justify-between items-start mb-8">
            <div>
                <h1 class="text-[28px] font-black text-slate-800 tracking-tight">Caja Central</h1>
                <p class="text-slate-500 text-[15px] font-medium">Gestión de cobros y movimientos de caja</p>
            </div>
            <div class="flex gap-3">
                <a href="/dashboard" class="bg-white border border-slate-200 text-slate-700 px-5 py-2.5 rounded-xl flex items-center gap-2 text-sm font-bold shadow-sm hover:bg-slate-50 transition-all">
                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M10 19l-7-7m0 0l7-7m-7 7h18" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    Volver
                </a>
                <button class="bg-white border border-slate-200 text-slate-700 px-5 py-2.5 rounded-xl flex items-center gap-2 text-sm font-bold shadow-sm hover:bg-slate-50 transition-all">
                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    Imprimir Cierre
                </button>
                <button onclick="openModal('nuevoCobroModal')" class="bg-[#0061df] hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl flex items-center gap-2 text-sm font-bold shadow-lg shadow-blue-100 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    Nuevo Cobro
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white p-6 rounded-[24px] shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-slate-50 relative overflow-hidden">
                <p class="text-slate-400 text-sm font-bold mb-4">Ingresos del Día</p>
                <div class="flex justify-between items-end">
                    <div>
                        <p class="text-[#00a65a] text-3xl font-black tracking-tighter">S/ {{ number_format($resumen['ingresos'], 2) }}</p>
                        <p class="text-slate-400 text-[11px] font-bold mt-1">{{ $resumen['ingresos_count'] }} transacciones</p>
                    </div>
                    <svg class="w-10 h-10 text-[#00a65a]/20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13 7h8m0 0v8m0 0l-8 8-4-4-6 6m8-12V5a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
            </div>

            <div class="bg-white p-6 rounded-[24px] shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-slate-50">
                <p class="text-slate-400 text-sm font-bold mb-4">Egresos del Día</p>
                <div class="flex justify-between items-end">
                    <div>
                        <p class="text-[#f03e3e] text-3xl font-black tracking-tighter">S/ {{ number_format($resumen['egresos'], 2) }}</p>
                        <p class="text-slate-400 text-[11px] font-bold mt-1">{{ $resumen['egresos_count'] }} transacciones</p>
                    </div>
                    <span class="text-[#f03e3e] text-4xl font-light">$</span>
                </div>
            </div>

            <div class="bg-white p-6 rounded-[24px] shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-slate-50">
                <p class="text-slate-400 text-sm font-bold mb-4">Saldo en Caja</p>
                <div class="flex justify-between items-end">
                    <div>
                        <p class="text-[#1c7ed6] text-3xl font-black tracking-tighter">S/ {{ number_format($resumen['saldo'], 2) }}</p>
                        <p class="text-slate-400 text-[11px] font-bold mt-1">Actualizado</p>
                    </div>
                    <span class="text-[#1c7ed6] text-4xl font-light">$</span>
                </div>
            </div>

            <div class="bg-white p-6 rounded-[24px] shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-slate-50">
                <p class="text-slate-400 text-sm font-bold mb-4">Pendientes</p>
                <div class="flex justify-between items-end">
                    <div>
                        <p class="text-[#f39c12] text-3xl font-black tracking-tighter">S/ {{ number_format($resumen['pendientes_monto'], 2) }}</p>
                        <p class="text-slate-400 text-[11px] font-bold mt-1">{{ $resumen['pendientes_count'] }} pendientes</p>
                    </div>
                    <svg class="w-10 h-10 text-[#f39c12]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" stroke-width="2"/></svg>
                </div>
            </div>
        </div>

        {{-- Cajas Abiertas (Rol Caja) --}}
        @if($cajasAbiertas->count() > 0)
        <div class="bg-white rounded-[24px] border border-slate-100 shadow-sm overflow-hidden mb-8">
            <div class="p-6 border-b border-slate-50 flex justify-between items-center">
                <div>
                    <h3 class="font-bold text-slate-700 text-lg flex items-center gap-2">
                        <span class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></span>
                        Cajas Abiertas - Control Operativo
                    </h3>
                    <p class="text-slate-500 text-sm mt-1">Sesiones de caja activas desde el rol caja</p>
                </div>
                <span class="bg-green-100 text-green-700 px-4 py-2 rounded-full text-sm font-bold">
                    {{ $cajasAbiertas->count() }} activa{{ $cajasAbiertas->count() > 1 ? 's' : '' }}
                </span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="text-slate-400 text-[12px] uppercase font-bold tracking-wider">
                        <tr class="border-b border-slate-50">
                            <th class="px-8 py-5">Cajero</th>
                            <th class="px-8 py-5">Apertura</th>
                            <th class="px-8 py-5">Monto Inicial</th>
                            <th class="px-8 py-5">Estado</th>
                            <th class="px-8 py-5 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="text-[14px] divide-y divide-slate-50">
                        @foreach($cajasAbiertas as $caja)
                            <tr class="hover:bg-slate-50/50 transition-all">
                                <td class="px-8 py-5 font-bold text-slate-700">
                                    {{ $caja->usuario->name ?? 'N/A' }}
                                </td>
                                <td class="px-8 py-5 text-slate-600">
                                    {{ $caja->fecha_apertura->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-8 py-5 font-bold text-green-600">
                                    S/ {{ number_format($caja->monto_inicial, 2) }}
                                </td>
                                <td class="px-8 py-5">
                                    <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-[11px] font-bold flex items-center gap-1 w-fit">
                                        <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                                        Abierta
                                    </span>
                                </td>
                                <td class="px-8 py-5 text-right">
                                    <span class="text-slate-400 text-sm">Sesión activa</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <div class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm mb-8 flex items-center gap-4">
            <div class="relative flex-1">
                <input type="text" placeholder="Buscar por paciente, historia clínica o número de recibo..." class="w-full pl-12 pr-4 py-3 bg-white border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-50 outline-none transition-all">
                <svg class="w-5 h-5 absolute left-4 top-3.5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2"/></svg>
            </div>
            <button class="bg-slate-50 text-slate-600 px-8 py-2.5 rounded-xl text-sm font-bold border border-slate-200 hover:bg-slate-100 transition-all">Filtrar</button>
        </div>

        <div class="bg-white rounded-[24px] border border-slate-100 shadow-sm overflow-hidden mb-8">
            <div class="p-6 border-b border-slate-50">
                <h3 class="font-bold text-slate-700 text-lg">Movimientos del Día - {{ $fecha->format('d/m/Y') }}</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="text-slate-400 text-[12px] uppercase font-bold tracking-wider">
                        <tr class="border-b border-slate-50">
                            <th class="px-8 py-5">Hora</th>
                            <th class="px-8 py-5">Tipo</th>
                            <th class="px-8 py-5">Concepto</th>
                            <th class="px-8 py-5">Paciente</th>
                            <th class="px-8 py-5">Monto</th>
                            <th class="px-8 py-5">Estado</th>
                            <th class="px-8 py-5 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="text-[14px] divide-y divide-slate-50">
                        @forelse($movimientos as $movimiento)
                            <tr class="hover:bg-slate-50/50 transition-all">
                                <td class="px-8 py-5 font-bold text-slate-700">{{ $movimiento->fecha->format('H:i') }}</td>
                                <td class="px-8 py-5">
                                    <span class="bg-[#e6fcf5] text-[#0ca678] px-3 py-1 rounded-md text-[11px] font-bold">
                                        {{ $movimiento->total_dia >= 0 ? 'Ingreso' : 'Egreso' }}
                                    </span>
                                </td>
                                <td class="px-8 py-5 text-slate-600">{{ $movimiento->tipo }}</td>
                                <td class="px-8 py-5 text-slate-500 font-medium">{{ $movimiento->nro_pago_internos ?? '-' }}</td>
                                <td class="px-8 py-5 font-bold {{ $movimiento->total_dia >= 0 ? 'text-[#0ca678]' : 'text-[#f03e3e]' }}">
                                    S/ {{ number_format(abs($movimiento->total_dia), 2) }}
                                </td>
                                <td class="px-8 py-5">
                                    <span class="bg-[#e6fcf5] text-[#0ca678] px-3 py-1 rounded-full text-[11px] font-bold flex items-center gap-1 w-fit border border-[#c3fae8]">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" stroke-width="3"/></svg> 
                                        {{ $movimiento->nro_factura ? 'Completado' : 'Pendiente' }}
                                    </span>
                                </td>
                                <td class="px-8 py-5 text-right">
                                    <button class="bg-white border border-slate-200 text-slate-700 px-4 py-1.5 rounded-lg text-[12px] font-bold shadow-sm flex items-center gap-2 ml-auto">
                                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" stroke-width="2"/></svg> 
                                        Imprimir
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-8 py-12 text-center text-slate-400">
                                    <svg class="w-16 h-16 text-slate-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <p class="font-medium">No hay movimientos hoy</p>
                                    <p class="text-sm mt-1">Los movimientos aparecerán aquí cuando se realicen cobros</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white p-8 rounded-[24px] border border-slate-100 shadow-sm">
            <h3 class="font-bold text-slate-700 mb-6 text-lg">Resumen por Forma de Pago</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @php
                    $metodosResumen = $movimientos->groupBy(function($mov) {
                        // Extraer método de pago del campo tipo (ej: "Consulta Externa - EFECTIVO")
                        $parts = explode(' - ', $mov->tipo);
                        return end($parts);
                    })->map(function($grupo) {
                        return $grupo->sum('total_dia');
                    });
                @endphp
                
                <div class="bg-[#fcfdfe] border border-slate-100 p-6 rounded-[20px] flex justify-between items-center">
                    <div>
                        <p class="text-slate-400 text-xs font-bold uppercase mb-2">Efectivo</p>
                        <p class="text-slate-800 text-2xl font-black">S/ {{ number_format($metodosResumen['EFECTIVO'] ?? 0, 2) }}</p>
                    </div>
                    <span class="text-[#40c057] text-4xl font-light">$</span>
                </div>
                <div class="bg-[#fcfdfe] border border-slate-100 p-6 rounded-[20px] flex justify-between items-center">
                    <div>
                        <p class="text-slate-400 text-xs font-bold uppercase mb-2">Tarjeta</p>
                        <p class="text-slate-800 text-2xl font-black">S/ {{ number_format($metodosResumen['TARJETA'] ?? 0, 2) }}</p>
                    </div>
                    <svg class="w-10 h-10 text-[#228be6]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" stroke-width="2"/></svg>
                </div>
                <div class="bg-[#fcfdfe] border border-slate-100 p-6 rounded-[20px] flex justify-between items-center">
                    <div>
                        <p class="text-slate-400 text-xs font-bold uppercase mb-2">Transferencia</p>
                        <p class="text-slate-800 text-2xl font-black">S/ {{ number_format($metodosResumen['TRANSFERENCIA'] ?? 0, 2) }}</p>
                    </div>
                    <svg class="w-10 h-10 text-[#be4bdb]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" stroke-width="2"/></svg>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal Nuevo Cobro -->
    <x-modal name="nuevoCobroModal" maxWidth="4xl">
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-slate-800">Nuevo Cobro - Seleccionar Paciente</h3>
                <button onclick="closeModal('nuevoCobroModal')" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Search Bar -->
            <div class="mb-6">
                <div class="relative">
                    <input 
                        type="text" 
                        id="pacienteSearch" 
                        placeholder="Buscar paciente por nombre o CI..." 
                        class="w-full pl-12 pr-4 py-3 bg-white border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-50 outline-none transition-all"
                        onkeyup="buscarPacientes()"
                    >
                    <svg class="w-5 h-5 absolute left-4 top-3.5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2"/>
                    </svg>
                </div>
            </div>

            <!-- Loading State -->
            <div id="loadingPacientes" class="hidden text-center py-8">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                <p class="text-slate-500 mt-2">Cargando pacientes...</p>
            </div>

            <!-- Patients List -->
            <div id="pacientesList" class="max-h-96 overflow-y-auto">
                <!-- Patients will be loaded here via JavaScript -->
            </div>

            <!-- No Results Message -->
            <div id="noResults" class="hidden text-center py-8">
                <svg class="w-16 h-16 text-slate-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-slate-500 font-medium">No se encontraron pacientes</p>
                <p class="text-slate-400 text-sm mt-1">Intenta con otra búsqueda</p>
            </div>
        </div>
    </x-modal>

    <script>
        let allPacientes = [];

        // Load patients when modal opens
        function openModal(modalName) {
            if (modalName === 'nuevoCobroModal') {
                cargarPacientes();
            }
            // Dispatch event to open the modal
            window.dispatchEvent(new CustomEvent('open-modal', { detail: modalName }));
        }

        function closeModal(modalName) {
            window.dispatchEvent(new CustomEvent('close-modal', { detail: modalName }));
        }

        function cargarPacientes() {
            document.getElementById('loadingPacientes').classList.remove('hidden');
            document.getElementById('pacientesList').innerHTML = '';
            document.getElementById('noResults').classList.add('hidden');

            fetch('/caja/pacientes-registrados')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        allPacientes = data.pacientes;
                        mostrarPacientes(allPacientes);
                    }
                })
                .catch(error => {
                    console.error('Error loading patients:', error);
                })
                .finally(() => {
                    document.getElementById('loadingPacientes').classList.add('hidden');
                });
        }

        function mostrarPacientes(pacientes) {
            const container = document.getElementById('pacientesList');
            
            if (pacientes.length === 0) {
                document.getElementById('noResults').classList.remove('hidden');
                return;
            }

            container.innerHTML = pacientes.map(paciente => `
                <div class="bg-white border border-slate-200 rounded-xl p-4 mb-3 hover:bg-slate-50 transition-all cursor-pointer hover:shadow-md" onclick="seleccionarPaciente('${paciente.ci}', '${paciente.nombre}')">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-semibold text-slate-800">${paciente.nombre}</h4>
                                <p class="text-sm text-slate-500">CI: ${paciente.ci}</p>
                                <p class="text-xs text-slate-400">${paciente.telefono} • ${paciente.sexo}</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <button class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">
                                Seleccionar
                            </button>
                        </div>
                    </div>
                </div>
            `).join('');
        }

        function buscarPacientes() {
            const searchTerm = document.getElementById('pacienteSearch').value.toLowerCase();
            
            if (searchTerm === '') {
                mostrarPacientes(allPacientes);
                return;
            }

            const filtered = allPacientes.filter(paciente => 
                paciente.nombre.toLowerCase().includes(searchTerm) ||
                paciente.ci.includes(searchTerm)
            );

            mostrarPacientes(filtered);
        }

        function seleccionarPaciente(ci, nombre, servicio = 'Consulta Externa', medico = 'Dr. Ramírez', medicoCi = '12345678', monto = 150.00) {
            // Close the modal
            closeModal('nuevoCobroModal');
            
            // Show payment modal with pre-filled data
            mostrarModalCobro(ci, nombre, servicio, medico, medicoCi, monto);
        }

        function mostrarModalCobro(ci, nombre, servicio, medico, medicoCi, monto) {
            // Create payment modal dynamically
            const modalHtml = `
                <div id="cobroModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                    <div class="bg-white rounded-2xl p-8 max-w-md w-full mx-4">
                        <h3 class="text-xl font-bold text-slate-800 mb-6">Registrar Cobro</h3>
                        
                        <div class="mb-6">
                            <p class="text-sm text-slate-600 mb-2">Paciente:</p>
                            <p class="font-semibold text-slate-800">${nombre} (CI: ${ci})</p>
                        </div>
                        
                        <div class="mb-6">
                            <p class="text-sm text-slate-600 mb-2">Servicio:</p>
                            <p class="font-semibold text-slate-800">${servicio}</p>
                            <input type="hidden" id="servicio" value="${servicio}">
                        </div>
                        
                        <div class="mb-6">
                            <p class="text-sm text-slate-600 mb-2">Médico:</p>
                            <p class="font-semibold text-slate-800">${medico}</p>
                            <input type="hidden" id="medico-nombre" value="${medico}">
                            <input type="hidden" id="medico-ci" value="${medicoCi}">
                        </div>
                        
                        <div class="mb-6">
                            <p class="text-sm text-slate-600 mb-2">Monto a pagar:</p>
                            <p class="text-2xl font-bold text-blue-600">S/ ${monto.toFixed(2)}</p>
                            <input type="hidden" id="monto" value="${monto}">
                        </div>
                        
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-slate-700 mb-2">Método de Pago</label>
                            <select id="metodo_pago" class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-50 outline-none">
                                <option value="EFECTIVO">Efectivo</option>
                                <option value="TARJETA">Tarjeta</option>
                                <option value="TRANSFERENCIA">Transferencia</option>
                            </select>
                        </div>
                        
                        <div class="flex gap-3">
                            <button onclick="cancelarCobro()" class="flex-1 px-4 py-2 border border-slate-200 text-slate-700 rounded-lg hover:bg-slate-50 transition">
                                Cancelar
                            </button>
                            <button onclick="procesarCobroDirecto('${ci}', '${nombre}')" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                Cobrar
                            </button>
                        </div>
                    </div>
                </div>
            `;
            
            document.body.insertAdjacentHTML('beforeend', modalHtml);
        }

        function cancelarCobro() {
            const modal = document.getElementById('cobroModal');
            if (modal) {
                modal.remove();
            }
        }

        function procesarCobroDirecto(ci, nombre) {
            const servicio = document.getElementById('servicio').value;
            const medicoCi = document.getElementById('medico-ci').value;
            const medicoNombre = document.getElementById('medico-nombre').value;
            const monto = parseFloat(document.getElementById('monto').value);
            const metodo_pago = document.getElementById('metodo_pago').value;
            
            if (!monto || monto <= 0) {
                alert('Por favor ingrese un monto válido');
                return;
            }
            
            // Show loading
            const cobrarBtn = event.target;
            cobrarBtn.disabled = true;
            cobrarBtn.textContent = 'Procesando...';
            
            // Process payment via API
            fetch('/admin/api/procesar-cobro', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    paciente_ci: ci,
                    paciente_nombre: nombre,
                    concepto: servicio,
                    monto: monto,
                    metodo_pago: metodo_pago,
                    medico_ci: medicoCi,
                    medico_nombre: medicoNombre
                })
            })
            .then(response => {
                // Check if response is actually JSON
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    throw new Error(`Respuesta no válida del servidor (${response.status}): ${contentType}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    alert('Cobro registrado exitosamente');
                    cancelarCobro();
                    // Reload page to show updated data
                    window.location.reload();
                } else {
                    alert(data.message || 'Error al procesar el cobro');
                }
            })
            .catch(error => {
                console.error('Error completo:', error);
                alert('Error al procesar el cobro: ' + error.message);
            })
            .finally(() => {
                cobrarBtn.disabled = false;
                cobrarBtn.textContent = 'Cobrar';
            });
        }
    </script>
@endsection
