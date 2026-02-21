@extends('layouts.app')

@section('content')
<div class="w-full p-6 bg-gray-50/50 min-h-screen">

        <!-- Header -->
        <div class="flex justify-between items-end mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Recepción y Admisión</h1>
                <p class="text-sm text-gray-500">María González - Turno: Mañana (07:00 - 15:00)</p>
            </div>
            <div class="flex gap-3">
                <button class="flex items-center px-4 py-2 bg-white border border-gray-200 text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition-all shadow-sm">
                    <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Nueva Cita
                </button>
                <button onclick="switchTab('admision')" class="flex items-center px-4 py-2 bg-[#0056b3] text-white font-medium rounded-xl hover:bg-blue-700 transition-all shadow-md">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                    Admisión Rápida
                </button>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex flex-col items-center justify-center h-28 hover:shadow-md transition">
                <span class="text-gray-500 text-sm font-medium mb-1">Citas Programadas</span>
                <span class="text-3xl font-bold text-gray-800">24</span>
            </div>
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex flex-col items-center justify-center h-28 hover:shadow-md transition">
                <span class="text-gray-500 text-sm font-medium mb-1">En Atención</span>
                <span class="text-3xl font-bold text-green-600">3</span>
            </div>
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex flex-col items-center justify-center h-28 hover:shadow-md transition">
                <span class="text-gray-500 text-sm font-medium mb-1">En Espera</span>
                <span class="text-3xl font-bold text-orange-500">5</span>
            </div>
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex flex-col items-center justify-center h-28 hover:shadow-md transition">
                <span class="text-gray-500 text-sm font-medium mb-1">Admisiones</span>
                <span class="text-3xl font-bold text-blue-600">8</span>
            </div>
        </div>

        <!-- Tabs Navigation -->
        <div class="bg-gray-100/50 p-1.5 rounded-xl inline-flex w-full mb-6">
            <button id="btn-agenda" onclick="switchTab('agenda')" class="flex-1 px-4 py-2.5 bg-white text-gray-800 font-semibold rounded-lg shadow-sm text-center text-sm transition-all duration-200">Agenda del Día</button>
            <button id="btn-admision" onclick="switchTab('admision')" class="flex-1 px-4 py-2.5 text-gray-600 hover:bg-gray-200/50 font-medium rounded-lg text-center text-sm transition-all duration-200">Admisión Rápida</button>
            <button id="btn-llamadas" onclick="switchTab('llamadas')" class="flex-1 px-4 py-2.5 text-gray-600 hover:bg-gray-200/50 font-medium rounded-lg text-center text-sm transition-all duration-200">Gestión de Llamadas</button>
        </div>

        <!-- Container for Tab Content -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 min-h-[400px] overflow-hidden">

            <!-- TAB 1: AGENDA DEL DÍA -->
            <div id="tab-agenda" class="block">
                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/30">
                    <h3 class="text-gray-800 font-bold text-sm">Citas Programadas - 2026-02-03</h3>
                    <span class="text-[10px] bg-blue-50 text-blue-600 px-2 py-0.5 rounded-full font-bold uppercase tracking-wider">Hoy</span>
                </div>
                <div class="divide-y divide-gray-100">
                    <!-- Item 1 -->
                    <div class="p-4 hover:bg-gray-50/50 transition-colors flex flex-col sm:flex-row items-start sm:items-center gap-4">
                        <div class="flex flex-col items-center justify-center min-w-[60px] text-gray-700">
                            <svg class="w-5 h-5 text-blue-500 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="font-bold text-sm">08:00</span>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="font-bold text-gray-900">García, Juan</span>
                                <span class="px-2 py-0.5 rounded-full bg-blue-50 text-blue-600 text-[10px] font-bold border border-blue-100 uppercase tracking-wide">Confirmado</span>
                            </div>
                            <div class="text-sm text-gray-500 flex items-center flex-wrap gap-2">
                                <span class="font-mono text-xs bg-gray-100 px-1.5 py-0.5 rounded">DNI: 12345678</span>
                                <span class="text-gray-300">•</span>
                                <span>Cardiología</span>
                                <span class="text-gray-300">•</span>
                                <span class="text-gray-700">Dr. Ramírez</span>
                            </div>
                            <div class="text-xs text-gray-400 mt-1 flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                987654321
                            </div>
                        </div>
                        <div class="flex items-center gap-2 mt-2 sm:mt-0">
                            <button class="bg-[#0056b3] hover:bg-blue-700 text-white text-xs font-medium px-4 py-2 rounded-lg shadow-sm transition-colors">Registrar Llegada</button>
                            <button class="border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 text-xs font-medium px-3 py-2 rounded-lg shadow-sm transition-colors">Ficha</button>
                        </div>
                    </div>
                    
                    <!-- Item 2 -->
                    <div class="p-4 hover:bg-gray-50/50 transition-colors flex flex-col sm:flex-row items-start sm:items-center gap-4 border-l-4 border-green-500 bg-green-50/10">
                        <div class="flex flex-col items-center justify-center min-w-[60px] text-gray-700">
                            <svg class="w-5 h-5 text-green-500 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="font-bold text-sm">08:30</span>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="font-bold text-gray-900">López, María</span>
                                <span class="px-2 py-0.5 rounded-full bg-green-100 text-green-700 text-[10px] font-bold border border-green-200 uppercase tracking-wide">En Atención</span>
                            </div>
                            <div class="text-sm text-gray-500 flex items-center flex-wrap gap-2">
                                <span class="font-mono text-xs bg-gray-100 px-1.5 py-0.5 rounded">DNI: 23456789</span>
                                <span class="text-gray-300">•</span>
                                <span>Pediatría</span>
                                <span class="text-gray-300">•</span>
                                <span class="text-gray-700">Dra. Torres</span>
                            </div>
                            <div class="text-xs text-gray-400 mt-1 flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                987654322
                            </div>
                        </div>
                        <div class="flex items-center gap-2 mt-2 sm:mt-0">
                            <button class="border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 text-xs font-medium px-3 py-2 rounded-lg shadow-sm transition-colors">Ficha</button>
                        </div>
                    </div>

                    <!-- Item 3 -->
                    <div class="p-4 hover:bg-gray-50/50 transition-colors flex flex-col sm:flex-row items-start sm:items-center gap-4">
                        <div class="flex flex-col items-center justify-center min-w-[60px] text-gray-700">
                            <svg class="w-5 h-5 text-orange-500 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="font-bold text-sm">09:00</span>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="font-bold text-gray-900">Rodríguez, Pedro</span>
                                <span class="px-2 py-0.5 rounded-full bg-orange-100 text-orange-700 text-[10px] font-bold border border-orange-200 uppercase tracking-wide">En Espera</span>
                            </div>
                            <div class="text-sm text-gray-500 flex items-center flex-wrap gap-2">
                                <span class="font-mono text-xs bg-gray-100 px-1.5 py-0.5 rounded">DNI: 34567890</span>
                                <span class="text-gray-300">•</span>
                                <span>Medicina General</span>
                                <span class="text-gray-300">•</span>
                                <span class="text-gray-700">Dr. Silva</span>
                            </div>
                            <div class="text-xs text-gray-400 mt-1 flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                987654323
                            </div>
                        </div>
                        <div class="flex items-center gap-2 mt-2 sm:mt-0">
                            <button class="bg-[#10b981] hover:bg-green-600 text-white text-xs font-medium px-4 py-2 rounded-lg shadow-sm transition-colors">Llamar a Consultorio</button>
                            <button class="border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 text-xs font-medium px-3 py-2 rounded-lg shadow-sm transition-colors">Ficha</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 2: ADMISIÓN RÁPIDA -->
            <div id="tab-admision" class="hidden p-8">
                <div class="max-w-4xl mx-auto">
                    <h3 class="text-lg font-bold text-gray-900 mb-6">Admisión Rápida de Paciente</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <!-- Tipo de Documento -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Documento</label>
                            <select class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all">
                                <option>DNI</option>
                                <option>Pasaporte</option>
                                <option>Carnet de Extranjería</option>
                            </select>
                        </div>
                        
                        <!-- Número de Documento + Botón Buscar -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Número de Documento</label>
                            <div class="flex gap-2">
                                <input type="text" placeholder="Ingrese número" class="flex-1 border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-gray-50 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all" value="12345678">
                                <button class="bg-[#0056b3] hover:bg-blue-700 text-white font-medium px-6 py-2.5 rounded-xl transition-colors text-sm">Buscar</button>
                            </div>
                        </div>
                    </div>

                    <!-- Resultado Búsqueda (Paciente encontrado) -->
                    <div class="bg-blue-50/50 border border-blue-100 rounded-2xl p-6 mb-6">
                        <div class="flex items-center gap-2 mb-4">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <h4 class="text-sm font-bold text-blue-800">Paciente encontrado en el sistema</h4>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-white rounded-xl p-3 border border-blue-100">
                                <p class="text-xs text-gray-500 uppercase tracking-wide">Nombre</p>
                                <p class="font-semibold text-gray-900 text-sm">García Mendoza, Juan Carlos</p>
                            </div>
                            <div class="bg-white rounded-xl p-3 border border-blue-100">
                                <p class="text-xs text-gray-500 uppercase tracking-wide">H.C.</p>
                                <p class="font-semibold text-gray-900 text-sm font-mono">HC-001234</p>
                            </div>
                            <div class="bg-white rounded-xl p-3 border border-blue-100">
                                <p class="text-xs text-gray-500 uppercase tracking-wide">Edad</p>
                                <p class="font-semibold text-gray-900 text-sm">45 años</p>
                            </div>
                            <div class="bg-white rounded-xl p-3 border border-blue-100">
                                <p class="text-xs text-gray-500 uppercase tracking-wide">Teléfono</p>
                                <p class="font-semibold text-gray-900 text-sm">987654321</p>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Atención</label>
                            <select class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all">
                                <option>Consulta Externa</option>
                                <option>Emergencia</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Especialidad</label>
                            <select class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all">
                                <option>Medicina General</option>
                                <option>Cardiología</option>
                                <option>Pediatría</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Médico</label>
                            <select class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all">
                                <option>Dr. Ramírez, Carlos</option>
                                <option>Dra. Torres, Ana</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Seguro</label>
                            <select class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all">
                                <option>Particular</option>
                                <option>SIS</option>
                                <option>EsSalud</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-6 border-t border-gray-100">
                        <button onclick="switchTab('agenda')" class="px-6 py-2.5 border border-gray-200 rounded-xl text-gray-700 font-medium hover:bg-gray-50 transition-colors text-sm">Cancelar</button>
                        <button class="px-6 py-2.5 bg-[#0056b3] text-white rounded-xl font-medium hover:bg-blue-700 transition-colors flex items-center text-sm shadow-md">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                            Registrar Admisión
                        </button>
                    </div>
                </div>
            </div>

            <!-- TAB 3: GESTIÓN DE LLAMADAS -->
            <div id="tab-llamadas" class="hidden p-8">
                <h3 class="text-lg font-bold text-gray-900 mb-6">Gestión de Llamadas y Confirmaciones</h3>

                <div class="space-y-4 max-w-4xl">
                    <!-- Card 1 -->
                    <div class="bg-white border border-gray-100 rounded-2xl p-6 flex flex-col md:flex-row justify-between items-center hover:shadow-md transition-shadow">
                        <div class="mb-4 md:mb-0 flex items-start gap-4">
                            <div class="p-3 bg-blue-50 rounded-xl text-blue-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900">Recordatorios de Citas - Mañana</h4>
                                <p class="text-gray-500 text-sm mt-1">15 pacientes por confirmar</p>
                            </div>
                        </div>
                        <button class="bg-[#0056b3] hover:bg-blue-700 text-white font-medium px-6 py-2.5 rounded-xl flex items-center transition-colors text-sm shadow-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            Iniciar Llamadas
                        </button>
                    </div>

                    <!-- Card 2 -->
                    <div class="bg-white border border-gray-100 rounded-2xl p-6 flex flex-col md:flex-row justify-between items-center hover:shadow-md transition-shadow">
                        <div class="mb-4 md:mb-0 flex items-start gap-4">
                            <div class="p-3 bg-green-50 rounded-xl text-green-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900">Seguimiento Post-Alta</h4>
                                <p class="text-gray-500 text-sm mt-1">8 pacientes pendientes</p>
                            </div>
                        </div>
                        <button class="border border-gray-200 text-gray-700 hover:bg-gray-50 font-medium px-6 py-2.5 rounded-xl transition-colors text-sm">
                            Ver Lista
                        </button>
                    </div>

                    <!-- Card 3 -->
                    <div class="bg-white border border-gray-100 rounded-2xl p-6 flex flex-col md:flex-row justify-between items-center hover:shadow-md transition-shadow">
                        <div class="mb-4 md:mb-0 flex items-start gap-4">
                            <div class="p-3 bg-purple-50 rounded-xl text-purple-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900">Confirmación de Cirugías</h4>
                                <p class="text-gray-500 text-sm mt-1">3 confirmaciones pendientes</p>
                            </div>
                        </div>
                        <button class="border border-gray-200 text-gray-700 hover:bg-gray-50 font-medium px-6 py-2.5 rounded-xl transition-colors text-sm">
                            Ver Lista
                        </button>
                    </div>
                </div>
            </div>

        </div>

    </div>

    <!-- JavaScript para el cambio de pestañas -->
    <script>
        function switchTab(tabName) {
            // Ocultar todos los contenidos
            ['agenda', 'admision', 'llamadas'].forEach(tab => {
                const el = document.getElementById(`tab-${tab}`);
                el.classList.add('hidden');
                el.classList.remove('block');
            });

            // Resetear estilos de botones
            const btnClassesInactive = "text-gray-600 hover:bg-gray-200/50 font-medium";
            const btnClassesActive = "bg-white text-gray-800 font-semibold shadow-sm";
            
            ['agenda', 'admision', 'llamadas'].forEach(tab => {
                const btn = document.getElementById(`btn-${tab}`);
                btn.className = `flex-1 px-4 py-2.5 rounded-lg text-center text-sm transition-all duration-200 ${btnClassesInactive}`;
            });

            // Mostrar contenido seleccionado
            const selectedEl = document.getElementById(`tab-${tabName}`);
            selectedEl.classList.remove('hidden');
            selectedEl.classList.add('block');

            // Activar botón seleccionado
            document.getElementById(`btn-${tabName}`).className = `flex-1 px-4 py-2.5 rounded-lg text-center text-sm transition-all duration-200 ${btnClassesActive}`;
        }
    </script>
@endsection