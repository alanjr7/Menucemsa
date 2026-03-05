<x-app-layout>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="p-8 bg-[#f8fafc] min-h-screen font-sans antialiased">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-[28px] font-black text-slate-800 tracking-tight">Nuevo Cobro</h1>
                <p class="text-slate-500 text-[15px] font-medium">Registrar nuevo pago de paciente</p>
            </div>
            <div class="flex gap-3">
                <a href="/admin/caja" class="bg-white border border-slate-200 text-slate-700 px-5 py-2.5 rounded-xl flex items-center gap-2 text-sm font-bold shadow-sm hover:bg-slate-50 transition-all">
                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Volver a Caja
                </a>
            </div>
        </div>

        <!-- Patient Selection Section -->
        <div class="bg-white rounded-[24px] border border-slate-100 shadow-sm p-8 mb-8">
            <h3 class="font-bold text-slate-700 mb-6 text-lg">Seleccionar Paciente</h3>
            
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
            <div id="pacientesList" class="max-h-64 overflow-y-auto">
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

        <!-- Payment Section -->
        <div id="paymentSection" class="hidden">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Patient Info -->
                <div class="bg-white rounded-[24px] border border-slate-100 shadow-sm p-8">
                    <h3 class="font-bold text-slate-700 mb-6 text-lg">Información del Paciente</h3>
                    <div id="selectedPatientInfo">
                        <!-- Patient info will be displayed here -->
                    </div>
                </div>

                <!-- Payment Form -->
                <div class="bg-white rounded-[24px] border border-slate-100 shadow-sm p-8">
                    <h3 class="font-bold text-slate-700 mb-6 text-lg">Información del Cobro</h3>
                    
                    <form id="paymentForm">
                        <!-- Service Type -->
                        <div class="mb-6">
                            <label class="block text-sm font-bold text-slate-700 mb-2">Tipo de Servicio</label>
                            <div id="servicioContainer">
                                <!-- Service will be loaded here based on patient selection -->
                            </div>
                            <p id="servicioDescripcion" class="text-xs text-slate-500 mt-1 hidden"></p>
                        </div>

                        <!-- Amount (Read-only, auto-filled) -->
                        <div class="mb-6">
                            <label class="block text-sm font-bold text-slate-700 mb-2">Monto a Cobrar</label>
                            <div class="relative">
                                <span class="absolute left-4 top-3.5 text-slate-500 font-bold">S/</span>
                                <input 
                                    type="number" 
                                    id="monto" 
                                    step="0.01"
                                    class="w-full pl-12 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-bold text-slate-700" 
                                    placeholder="0.00"
                                    readonly
                                >
                            </div>
                            <p class="text-xs text-slate-500 mt-1">El monto se calcula automáticamente según el servicio seleccionado</p>
                        </div>

                        <!-- Payment Method -->
                        <div class="mb-6">
                            <label class="block text-sm font-bold text-slate-700 mb-4">Método de Pago</label>
                            <div class="grid grid-cols-2 gap-4">
                                <button type="button" onclick="selectPaymentMethod('EFECTIVO')" class="payment-method-btn border-2 border-slate-200 rounded-xl p-4 hover:border-blue-500 transition-all text-center">
                                    <svg class="w-8 h-8 mx-auto mb-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                    <p class="font-bold text-slate-700">Efectivo</p>
                                    <p class="text-xs text-slate-500">Pago en efectivo</p>
                                </button>

                                <button type="button" onclick="selectPaymentMethod('TARJETA')" class="payment-method-btn border-2 border-slate-200 rounded-xl p-4 hover:border-blue-500 transition-all text-center">
                                    <svg class="w-8 h-8 mx-auto mb-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                    </svg>
                                    <p class="font-bold text-slate-700">Tarjeta</p>
                                    <p class="text-xs text-slate-500">Débito/Crédito</p>
                                </button>

                                <button type="button" onclick="selectPaymentMethod('QR')" class="payment-method-btn border-2 border-slate-200 rounded-xl p-4 hover:border-blue-500 transition-all text-center">
                                    <svg class="w-8 h-8 mx-auto mb-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                                    </svg>
                                    <p class="font-bold text-slate-700">QR</p>
                                    <p class="text-xs text-slate-500">Yape/Plin</p>
                                </button>

                                <button type="button" onclick="selectPaymentMethod('TRANSFERENCIA')" class="payment-method-btn border-2 border-slate-200 rounded-xl p-4 hover:border-blue-500 transition-all text-center">
                                    <svg class="w-8 h-8 mx-auto mb-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                    <p class="font-bold text-slate-700">Transferencia</p>
                                    <p class="text-xs text-slate-500">Bancaria</p>
                                </button>
                            </div>
                        </div>

                        <!-- Reference Number (for QR and Transfer) -->
                        <div id="referenceField" class="mb-6 hidden">
                            <label class="block text-sm font-bold text-slate-700 mb-2">Número de Referencia</label>
                            <input 
                                type="text" 
                                id="referencia" 
                                class="w-full px-4 py-3 bg-white border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-50 outline-none transition-all" 
                                placeholder="Ingrese el número de operación"
                            >
                        </div>

                        <!-- Submit Button -->
                        <button 
                            type="submit" 
                            class="w-full bg-[#0061df] hover:bg-blue-700 text-white px-6 py-3 rounded-xl font-bold shadow-lg shadow-blue-100 transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                            id="submitPayment"
                        >
                            <span class="flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                </svg>
                                Procesar Cobro
                            </span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        let allPacientes = [];
        let allServicios = [];
        let pacientesPendientes = [];
        let selectedPatient = null;
        let selectedPaymentMethod = null;
        let isFromReception = false;

        // Load data on page load
        document.addEventListener('DOMContentLoaded', function() {
            cargarPacientesPendientes();
            cargarServicios();
            cargarPacientesRegistrados();
        });

        function cargarPacientesPendientes() {
            fetch('/caja/pacientes-pendientes')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        pacientesPendientes = data.pacientes;
                        mostrarPacientesPendientes();
                    }
                })
                .catch(error => {
                    console.error('Error loading pending patients:', error);
                });
        }

        function mostrarPacientesPendientes() {
            const container = document.getElementById('pacientesList');
            
            if (pacientesPendientes.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-8">
                        <svg class="w-16 h-16 text-slate-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p class="text-slate-500 font-medium">No hay pacientes pendientes de recepción</p>
                        <p class="text-slate-400 text-sm mt-1">Busca pacientes registrados para cobros directos</p>
                    </div>
                `;
                return;
            }

            container.innerHTML = `
                <div class="mb-6">
                    <h4 class="text-sm font-bold text-slate-700 mb-3 flex items-center">
                        <svg class="w-4 h-4 mr-2 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Pacientes Pendientes de Recepción
                    </h4>
                </div>
            ` + pacientesPendientes.map(paciente => `
                <div class="bg-orange-50 border border-orange-200 rounded-xl p-4 mb-3 hover:bg-orange-100 transition-all cursor-pointer hover:shadow-md" onclick="seleccionarPacientePendiente('${paciente.id}', '${paciente.ci}', '${paciente.nombre}', '${paciente.telefono}', '${paciente.sexo}', '${paciente.tipo_servicio}', '${paciente.monto}', '${paciente.motivo}')">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-semibold text-slate-800">${paciente.nombre}</h4>
                                <p class="text-sm text-slate-500">CI: ${paciente.ci}</p>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="text-xs bg-orange-200 text-orange-800 px-2 py-0.5 rounded-full font-medium">${paciente.tipo_servicio.replace('_', ' ')}</span>
                                    <span class="text-xs text-slate-400">Registrado: ${paciente.fecha_registro}</span>
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-lg font-bold text-orange-600">S/ ${paciente.monto.toFixed(2)}</p>
                            <button class="bg-orange-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-orange-700 transition-colors mt-2">
                                Cobrar Ahora
                            </button>
                        </div>
                    </div>
                </div>
            `).join('');

            // Add separator
            if (pacientesPendientes.length > 0 && allPacientes.length > 0) {
                container.innerHTML += `
                    <div class="border-t border-slate-200 my-6 pt-6">
                        <h4 class="text-sm font-bold text-slate-700 mb-3">Cobros Directos (Pacientes Registrados)</h4>
                    </div>
                `;
            }
        }

        function seleccionarPacientePendiente(id, ci, nombre, telefono, sexo, tipoServicio, monto, motivo) {
            selectedPatient = { id, ci, nombre, telefono, sexo };
            isFromReception = true;
            
            // Show patient info
            document.getElementById('selectedPatientInfo').innerHTML = `
                <div class="space-y-4">
                    <div class="flex items-center space-x-4">
                        <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center">
                            <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-xl font-bold text-slate-800">${nombre}</h4>
                            <p class="text-slate-500">CI: ${ci}</p>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="text-xs bg-orange-200 text-orange-800 px-2 py-0.5 rounded-full font-medium">Pendiente de Recepción</span>
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-slate-400">Teléfono:</span>
                            <span class="text-slate-700 ml-2">${telefono}</span>
                        </div>
                        <div>
                            <span class="text-slate-400">Sexo:</span>
                            <span class="text-slate-700 ml-2">${sexo}</span>
                        </div>
                    </div>
                    <div class="bg-orange-50 p-3 rounded-lg">
                        <p class="text-sm font-medium text-slate-700">Motivo: ${motivo}</p>
                    </div>
                </div>
            `;

            // Show service (readonly for reception patients)
            mostrarServicioAsignado(tipoServicio, monto);

            // Show payment section
            document.getElementById('paymentSection').classList.remove('hidden');
            
            // Scroll to payment section
            document.getElementById('paymentSection').scrollIntoView({ behavior: 'smooth' });
        }

        function mostrarServicioAsignado(tipoServicio, monto) {
            const container = document.getElementById('servicioContainer');
            
            // Find service details
            const servicio = allServicios.find(s => s.tipo === tipoServicio);
            
            container.innerHTML = `
                <div class="bg-orange-50 border border-orange-200 rounded-xl p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="font-semibold text-slate-800">${servicio ? servicio.nombre : tipoServicio.replace('_', ' ')}</h4>
                            <p class="text-sm text-slate-500">Servicio asignado en recepción</p>
                        </div>
                        <div class="text-right">
                            <p class="text-lg font-bold text-orange-600">S/ ${parseFloat(monto).toFixed(2)}</p>
                        </div>
                    </div>
                </div>
                <input type="hidden" id="tipoServicio" value="${tipoServicio}">
            `;

            // Update amount
            document.getElementById('monto').value = monto;

            // Show description if available
            if (servicio && servicio.descripcion) {
                const descElement = document.getElementById('servicioDescripcion');
                descElement.textContent = servicio.descripcion;
                descElement.classList.remove('hidden');
            }
        }

        function cargarServicios() {
            fetch('/caja/servicios-disponibles')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        allServicios = data.servicios;
                    }
                })
                .catch(error => {
                    console.error('Error loading services:', error);
                });
        }

        function cargarPacientesRegistrados() {
            fetch('/caja/pacientes-registrados')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        allPacientes = data.pacientes;
                        if (pacientesPendientes.length === 0) {
                            mostrarPacientes(allPacientes);
                        }
                    }
                })
                .catch(error => {
                    console.error('Error loading patients:', error);
                });
        }

        function mostrarPacientes(pacientes) {
            const container = document.getElementById('pacientesList');
            
            if (pacientes.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-8">
                        <svg class="w-16 h-16 text-slate-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-slate-500 font-medium">No hay pacientes registrados</p>
                        <p class="text-slate-400 text-sm mt-1">Registre nuevos pacientes en recepción</p>
                    </div>
                `;
                return;
            }

            if (pacientesPendientes.length === 0) {
                container.innerHTML = `
                    <div class="mb-6">
                        <h4 class="text-sm font-bold text-slate-700 mb-3 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            Cobros Directos (Pacientes Registrados)
                        </h4>
                    </div>
                `;
            }

            container.innerHTML += pacientes.map(paciente => `
                <div class="bg-white border border-slate-200 rounded-xl p-4 mb-3 hover:bg-slate-50 transition-all cursor-pointer hover:shadow-md" onclick="seleccionarPaciente('${paciente.ci}', '${paciente.nombre}', '${paciente.telefono}', '${paciente.sexo}')">
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
                if (pacientesPendientes.length > 0) {
                    mostrarPacientesPendientes();
                    if (allPacientes.length > 0) {
                        const container = document.getElementById('pacientesList');
                        container.innerHTML += `
                            <div class="border-t border-slate-200 my-6 pt-6">
                                <h4 class="text-sm font-bold text-slate-700 mb-3">Cobros Directos (Pacientes Registrados)</h4>
                            </div>
                        `;
                        mostrarPacientes(allPacientes);
                    }
                } else {
                    mostrarPacientes(allPacientes);
                }
                return;
            }

            // Filter all patients (both pending and registered)
            const allPatients = [...pacientesPendientes, ...allPacientes];
            const filtered = allPatients.filter(paciente => 
                paciente.nombre.toLowerCase().includes(searchTerm) ||
                paciente.ci.includes(searchTerm)
            );

            const container = document.getElementById('pacientesList');
            if (filtered.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-8">
                        <svg class="w-16 h-16 text-slate-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-slate-500 font-medium">No se encontraron pacientes</p>
                        <p class="text-slate-400 text-sm mt-1">Intenta con otra búsqueda</p>
                    </div>
                `;
                return;
            }

            container.innerHTML = filtered.map(paciente => {
                const isPendiente = pacientesPendientes.some(p => p.ci === paciente.ci);
                const bgColor = isPendiente ? 'bg-orange-50 border-orange-200' : 'bg-white border-slate-200';
                const iconColor = isPendiente ? 'text-orange-600' : 'text-blue-600';
                const badge = isPendiente ? `
                    <span class="text-xs bg-orange-200 text-orange-800 px-2 py-0.5 rounded-full font-medium">${paciente.tipo_servicio ? paciente.tipo_servicio.replace('_', ' ') : 'Pendiente'}</span>
                ` : '';

                return `
                    <div class="${bgColor} border rounded-xl p-4 mb-3 hover:bg-opacity-80 transition-all cursor-pointer hover:shadow-md" onclick="seleccionarPacienteDirecto('${paciente.ci}', '${paciente.nombre}', '${paciente.telefono}', '${paciente.sexo}', ${isPendiente})">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="w-12 h-12 ${isPendiente ? 'bg-orange-100' : 'bg-blue-100'} rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6 ${iconColor}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-slate-800">${paciente.nombre}</h4>
                                    <p class="text-sm text-slate-500">CI: ${paciente.ci}</p>
                                    <div class="flex items-center gap-2 mt-1">
                                        ${badge}
                                        <span class="text-xs text-slate-400">${paciente.telefono} • ${paciente.sexo}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                ${paciente.monto ? `<p class="text-lg font-bold ${isPendiente ? 'text-orange-600' : 'text-blue-600'}">S/ ${parseFloat(paciente.monto).toFixed(2)}</p>` : ''}
                                <button class="${isPendiente ? 'bg-orange-600 hover:bg-orange-700' : 'bg-blue-600 hover:bg-blue-700'} text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                    ${isPendiente ? 'Cobrar Ahora' : 'Seleccionar'}
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
        }

        function seleccionarPaciente(ci, nombre, telefono, sexo) {
            selectedPatient = { ci, nombre, telefono, sexo };
            isFromReception = false;
            
            // Show patient info
            document.getElementById('selectedPatientInfo').innerHTML = `
                <div class="space-y-4">
                    <div class="flex items-center space-x-4">
                        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center">
                            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="text-xl font-bold text-slate-800">${nombre}</h4>
                            <p class="text-slate-500">CI: ${ci}</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-slate-400">Teléfono:</span>
                            <span class="text-slate-700 ml-2">${telefono}</span>
                        </div>
                        <div>
                            <span class="text-slate-400">Sexo:</span>
                            <span class="text-slate-700 ml-2">${sexo}</span>
                        </div>
                    </div>
                </div>
            `;

            // Show service selector for direct payments
            mostrarSelectorServicios();

            // Show payment section
            document.getElementById('paymentSection').classList.remove('hidden');
            
            // Scroll to payment section
            document.getElementById('paymentSection').scrollIntoView({ behavior: 'smooth' });
        }

        function seleccionarPacienteDirecto(ci, nombre, telefono, sexo, esPendiente) {
            if (esPendiente) {
                // Find the pending patient and select with service
                const pendiente = pacientesPendientes.find(p => p.ci === ci);
                if (pendiente) {
                    seleccionarPacientePendiente(pendiente.id, ci, nombre, telefono, sexo, pendiente.tipo_servicio, pendiente.monto, pendiente.motivo);
                }
            } else {
                seleccionarPaciente(ci, nombre, telefono, sexo);
            }
        }

        function mostrarSelectorServicios() {
            const container = document.getElementById('servicioContainer');
            
            if (allServicios.length === 0) {
                container.innerHTML = '<div class="text-red-500">No hay servicios disponibles</div>';
                return;
            }

            container.innerHTML = `
                <select id="tipoServicio" class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-50 outline-none transition-all" required>
                    <option value="">Seleccionar tipo de servicio</option>
                    ${allServicios.map(servicio => `
                        <option value="${servicio.tipo}" data-precio="${servicio.precio}" data-descripcion="${servicio.descripcion || ''}">
                            ${servicio.nombre} - S/ ${servicio.precio.toFixed(2)}
                        </option>
                    `).join('')}
                </select>
            `;

            // Add change listener
            document.getElementById('tipoServicio').addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const precio = selectedOption.dataset.precio || 0;
                const descripcion = selectedOption.dataset.descripcion || '';
                
                // Update amount
                document.getElementById('monto').value = precio;
                
                // Update description
                const descElement = document.getElementById('servicioDescripcion');
                if (descripcion) {
                    descElement.textContent = descripcion;
                    descElement.classList.remove('hidden');
                } else {
                    descElement.textContent = '';
                    descElement.classList.add('hidden');
                }
            });
        }

        function selectPaymentMethod(method) {
            selectedPaymentMethod = method;
            
            // Update button styles
            document.querySelectorAll('.payment-method-btn').forEach(btn => {
                btn.classList.remove('border-blue-500', 'bg-blue-50');
                btn.classList.add('border-slate-200');
            });
            
            event.target.closest('.payment-method-btn').classList.remove('border-slate-200');
            event.target.closest('.payment-method-btn').classList.add('border-blue-500', 'bg-blue-50');

            // Show/hide reference field
            const referenceField = document.getElementById('referenceField');
            if (method === 'QR' || method === 'TRANSFERENCIA') {
                referenceField.classList.remove('hidden');
                document.getElementById('referencia').required = true;
            } else {
                referenceField.classList.add('hidden');
                document.getElementById('referencia').required = false;
            }
        }

        // Handle payment form submission
        document.getElementById('paymentForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (!selectedPatient) {
                alert('Por favor seleccione un paciente');
                return;
            }

            if (!selectedPaymentMethod) {
                alert('Por favor seleccione un método de pago');
                return;
            }

            const formData = {
                ci_paciente: selectedPatient.ci,
                tipo_servicio: document.getElementById('tipoServicio').value,
                metodo_pago: selectedPaymentMethod,
                referencia: document.getElementById('referencia').value
            };

            // Disable submit button
            const submitBtn = document.getElementById('submitPayment');
            submitBtn.disabled = true;
            submitBtn.innerHTML = `
                <span class="flex items-center justify-center gap-2">
                    <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-white"></div>
                    Procesando...
                </span>
            `;

            // Send payment data
            fetch('/admin/procesar-nuevo-cobro', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(`Cobro procesado exitosamente\n\nServicio: ${data.servicio}\nMonto: S/ ${data.monto}\nFactura: ${data.factura}`);
                    
                    // Always redirect to caja after successful payment
                    setTimeout(() => {
                        window.location.href = '/admin/caja';
                    }, 1500);
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al procesar el cobro');
            })
            .finally(() => {
                // Re-enable submit button
                submitBtn.disabled = false;
                submitBtn.innerHTML = `
                    <span class="flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                        Procesar Cobro
                    </span>
                `;
            });
        });
    </script>
</x-app-layout>
