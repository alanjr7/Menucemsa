@extends('layouts.app')

@section('content')
<div class="w-full p-6 bg-gray-50/50 min-h-screen">

        <!-- Header -->
        <div class="flex justify-between items-end mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Recepción</h1>
                <p class="text-sm text-gray-500">María González - Turno: Mañana (07:00 - 15:00)</p>
            </div>
            <div class="flex gap-3">
                <button onclick="abrirModalNuevaCita()" class="flex items-center px-4 py-2 bg-white border border-gray-200 text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition-all shadow-sm">
                    <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Nueva Cita
                </button>
                
            </div>
        </div>

        <!-- Botones de Acción Principal -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
            <!-- Botón Verde - Consulta Externa -->
            <a href="{{ route('reception.consulta-externa') }}" class="group relative bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-bold py-6 px-6 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex flex-col items-center">
                    <div class="bg-white/20 p-3 rounded-full mb-3 group-hover:bg-white/30 transition-colors">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <span class="text-lg">Consulta Externa</span>
                    <span class="text-xs opacity-90 mt-1">Paciente nuevo o episodio</span>
                </div>
            </a>

            <!-- Botón Amarillo - Emergencia -->
            <a href="{{ route('reception.emergencia') }}" class="group relative bg-gradient-to-r from-yellow-500 to-orange-500 hover:from-yellow-600 hover:to-orange-600 text-white font-bold py-6 px-6 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex flex-col items-center">
                    <div class="bg-white/20 p-3 rounded-full mb-3 group-hover:bg-white/30 transition-colors">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <span class="text-lg">Emergencia</span>
                    <span class="text-xs opacity-90 mt-1">Atención urgente</span>
                </div>
            </a>

            <!-- Botón Rojo - Hospitalización -->
            <a href="{{ route('reception.hospitalizacion') }}" class="group relative bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white font-bold py-6 px-6 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                <div class="flex flex-col items-center">
                    <div class="bg-white/20 p-3 rounded-full mb-3 group-hover:bg-white/30 transition-colors">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                        </svg>
                    </div>
                    <span class="text-lg">Hospitalización</span>
                    <span class="text-xs opacity-90 mt-1">Admisión interna</span>
                </div>
            </a>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8" id="stats-cards">
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex flex-col items-center justify-center h-28 hover:shadow-md transition">
                <span class="text-gray-500 text-sm font-medium mb-1">Citas Programadas</span>
                <span class="text-3xl font-bold text-gray-800" id="stat-citas-programadas">0</span>
            </div>
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex flex-col items-center justify-center h-28 hover:shadow-md transition">
                <span class="text-gray-500 text-sm font-medium mb-1">En Atención</span>
                <span class="text-3xl font-bold text-green-600" id="stat-en-atencion">0</span>
            </div>
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex flex-col items-center justify-center h-28 hover:shadow-md transition">
                <span class="text-gray-500 text-sm font-medium mb-1">En Espera</span>
                <span class="text-3xl font-bold text-orange-500" id="stat-en-espera">0</span>
            </div>
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex flex-col items-center justify-center h-28 hover:shadow-md transition">
                <span class="text-gray-500 text-sm font-medium mb-1">Admisiones</span>
                <span class="text-3xl font-bold text-blue-600" id="stat-admisiones">0</span>
            </div>
        </div>

        <!-- Tabs Navigation -->
        <div class="bg-gray-100/50 p-1.5 rounded-xl inline-flex w-full mb-6">
            <button id="btn-agenda" onclick="switchTab('agenda')" class="flex-1 px-4 py-2.5 bg-white text-gray-800 font-semibold rounded-lg shadow-sm text-center text-sm transition-all duration-200">Agenda del Día</button>
            <button id="btn-llamadas" onclick="switchTab('llamadas')" class="flex-1 px-4 py-2.5 text-gray-600 hover:bg-gray-200/50 font-medium rounded-lg text-center text-sm transition-all duration-200">Gestión de Llamadas</button>
            <button id="btn-temporales" onclick="switchTab('temporales')" class="flex-1 px-4 py-2.5 text-gray-600 hover:bg-gray-200/50 font-medium rounded-lg text-center text-sm transition-all duration-200">Pacientes Temporales</button>
        </div>

        <!-- Container for Tab Content -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 min-h-[400px] overflow-hidden">

            <!-- TAB 1: AGENDA DEL DÍA -->
            <div id="tab-agenda" class="block">
                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/30">
                    <h3 class="text-gray-800 font-bold text-sm">Citas Programadas - <span id="fecha-actual">{{ date('Y-m-d') }}</span></h3>
                    <div class="flex items-center gap-2">
                        <button onclick="cargarAgendaDia()" class="bg-blue-500 hover:bg-blue-600 text-white text-xs font-medium px-3 py-1.5 rounded-lg shadow-sm transition-colors">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Actualizar
                        </button>
                        <button onclick="abrirModalNuevaCita()" class="bg-green-500 hover:bg-green-600 text-white text-xs font-medium px-3 py-1.5 rounded-lg shadow-sm transition-colors">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Nueva Cita
                        </button>
                    </div>
                </div>
                <div class="divide-y divide-gray-100" id="lista-citas">
                    <!-- Las citas se cargarán dinámicamente -->
                    <div class="p-8 text-center text-gray-500">
                        <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <p>Cargando agenda del día...</p>
                    </div>
                </div>
            </div>

            <!-- TAB 3: GESTIÓN DE LLAMADAS -->
            <div id="tab-llamadas" class="hidden p-8">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-bold text-gray-900">Gestión de Llamadas y Confirmaciones</h3>
                    <button onclick="cargarPendientesLlamada()" class="bg-blue-500 hover:bg-blue-600 text-white text-xs font-medium px-3 py-1.5 rounded-lg shadow-sm transition-colors">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Actualizar
                    </button>
                </div>

                <div class="space-y-4 max-w-4xl" id="lista-llamadas">
                    <!-- Las llamadas se cargarán dinámicamente -->
                    <div class="p-8 text-center text-gray-500">
                        <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                        <p>Cargando llamadas pendientes...</p>
                    </div>
                </div>
            </div>

            <!-- TAB: PACIENTES TEMPORALES DE EMERGENCIA -->
            <div id="tab-temporales" class="hidden p-6">
                <div class="flex justify-between items-center mb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-2 0h-2m-2 0h-2m-2 0H8m0 0H6m2 0h2m-2 0v-2m0 2v2m14-6h-3m-3 0h-2m-2 0H8m0 0H6m2 0h2m-2 0V7m0 2v2M7 7h.01M7 11h.01M7 15h.01M11 7h.01M11 11h.01M11 15h.01M15 7h.01M15 11h.01M15 15h.01"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Pacientes Temporales de Emergencia</h3>
                            <p class="text-sm text-gray-500">Pacientes que ingresaron sin documento y necesitan completar sus datos</p>
                        </div>
                    </div>
                    <button onclick="cargarPacientesTemporales()" class="bg-orange-500 hover:bg-orange-600 text-white text-sm font-medium px-4 py-2 rounded-lg shadow-sm transition-colors flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Actualizar Lista
                    </button>
                </div>

                <div id="lista-pacientes-temporales" class="space-y-3">
                    <div class="p-8 text-center text-gray-500">
                        <svg class="w-12 h-12 mx-auto mb-4 text-orange-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p>Cargando pacientes temporales...</p>
                    </div>
                </div>
            </div>

        </div>

    </div>

    <!-- Modal Nueva Cita -->
    <div id="modalNuevaCita" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <!-- Header del Modal -->
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white p-6 rounded-t-2xl">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-2xl font-bold">Nueva Cita</h3>
                        <p class="text-blue-100 text-sm mt-1">Programar nueva cita médica</p>
                    </div>
                    <button onclick="cerrarModalNuevaCita()" class="bg-white/20 hover:bg-white/30 p-2 rounded-full transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Contenido del Modal -->
            <div class="p-6">
                <form id="formNuevaCita" onsubmit="crearNuevaCita(); return false;">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">C.I. Paciente *</label>
                            <div class="flex gap-2">
                                <input type="text" id="cita_ci_paciente" name="ci_paciente" placeholder="Número de CI" class="flex-1 border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all" required>
                                <button type="button" onclick="buscarPacienteParaCita()" class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-4 py-2.5 rounded-xl transition-colors text-sm">
                                    Buscar
                                </button>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Fecha *</label>
                            <input type="date" name="fecha" onchange="cargarMedicosDisponibles()" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all" required>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Hora *</label>
                            <input type="time" name="hora" onchange="cargarMedicosDisponibles()" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all" required>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Especialidad *</label>
                            <select name="codigo_especialidad" onchange="cargarMedicosDisponibles()" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all" required>
                                <option value="">Seleccione...</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Médico *</label>
                            <select name="ci_medico" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all" required>
                                <option value="">Seleccione especialidad primero</option>
                            </select>
                        </div>
                        
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Motivo de Consulta *</label>
                            <input type="text" name="motivo" placeholder="Motivo de la consulta" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all" required>
                        </div>
                        
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Observaciones</label>
                            <textarea name="observaciones" rows="3" placeholder="Notas adicionales" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all"></textarea>
                        </div>
                    </div>

                    <!-- Botones de Acción -->
                    <div class="flex justify-end gap-3 pt-6 border-t border-gray-200">
                        <button type="button" onclick="cerrarModalNuevaCita()" class="px-6 py-2.5 border border-gray-200 rounded-xl text-gray-700 font-medium hover:bg-gray-50 transition-colors text-sm">
                            Cancelar
                        </button>
                        <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white rounded-xl font-medium hover:bg-blue-700 transition-colors flex items-center text-sm shadow-md">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Crear Cita
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- JavaScript para el cambio de pestañas -->
    <script>
        const btnClassesActive = 'bg-white text-gray-800 font-semibold rounded-lg shadow-sm';
        const btnClassesInactive = 'text-gray-600 hover:bg-gray-200/50 font-medium rounded-lg';
        
        // Cargar datos al iniciar la página
        document.addEventListener('DOMContentLoaded', function() {
            cargarEstadisticasDashboard();
            cargarAgendaDia();
            cargarEspecialidades();
        });

        // Función para cargar estadísticas del dashboard
        async function cargarEstadisticasDashboard() {
            try {
                const response = await fetch('/api/estadisticas-dashboard');
                const data = await response.json();
                
                if (data.success) {
                    document.getElementById('stat-citas-programadas').textContent = data.stats.citas_programadas;
                    document.getElementById('stat-en-atencion').textContent = data.stats.en_atencion;
                    document.getElementById('stat-en-espera').textContent = data.stats.en_espera;
                    document.getElementById('stat-admisiones').textContent = data.stats.admisiones;
                }
            } catch (error) {
                console.error('Error al cargar estadísticas:', error);
            }
        }

        // Función para cargar agenda del día
        async function cargarAgendaDia() {
            try {
                const response = await fetch('/api/agenda-dia');
                const data = await response.json();
                
                if (data.success) {
                    mostrarCitas(data.citas);
                }
            } catch (error) {
                console.error('Error al cargar agenda:', error);
            }
        }

        // Función para mostrar citas en la agenda
        function mostrarCitas(citas) {
            const listaCitas = document.getElementById('lista-citas');
            
            if (citas.length === 0) {
                listaCitas.innerHTML = `
                    <div class="p-8 text-center text-gray-500">
                        <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <p>No hay citas programadas para hoy</p>
                    </div>
                `;
                return;
            }
            
            let html = '';
            citas.forEach(cita => {
                const estadoClass = cita.estado === 'atendido' ? 'bg-green-100 text-green-800' : 
                                  cita.estado === 'en_atencion' ? 'bg-blue-100 text-blue-800' : 
                                  cita.estado === 'en_espera' ? 'bg-orange-100 text-orange-800' : 
                                  'bg-gray-100 text-gray-800';
                
                html += `
                    <div class="p-4 hover:bg-gray-50 transition-colors">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="text-center">
                                    <div class="text-lg font-bold text-gray-800">${cita.hora}</div>
                                    <span class="text-xs px-2 py-1 rounded-full ${estadoClass}">${cita.estado}</span>
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900">${cita.paciente.nombre}</div>
                                    <div class="text-sm text-gray-500">CI: ${cita.paciente.ci}</div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-medium text-gray-900">Dr. ${cita.medico.usuario.name}</div>
                                <div class="text-xs text-gray-500">${cita.especialidad.nombre}</div>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            listaCitas.innerHTML = html;
        }

        // Función para cargar pendientes de llamada
        async function cargarPendientesLlamada() {
            try {
                const response = await fetch('/api/llamadas-pendientes');
                const data = await response.json();
                
                if (data.success) {
                    mostrarLlamadas(data.recordatorios, data.confirmaciones, data.seguimientos);
                }
            } catch (error) {
                console.error('Error al cargar llamadas pendientes:', error);
            }
        }

        // Función para mostrar llamadas pendientes
        function mostrarLlamadas(recordatorios, confirmaciones, seguimientos) {
            const listaLlamadas = document.getElementById('lista-llamadas');
            
            if (recordatorios.length === 0 && confirmaciones.length === 0 && seguimientos.length === 0) {
                listaLlamadas.innerHTML = `
                    <div class="p-8 text-center text-gray-500">
                        <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                        <p>No hay llamadas pendientes</p>
                    </div>
                `;
                return;
            }
            
            let html = '';
            
            // Mostrar recordatorios
            if (recordatorios.length > 0) {
                html += '<div class="mb-6"><h4 class="font-semibold text-gray-800 mb-3">Recordatorios de Hoy</h4>';
                recordatorios.forEach(cita => {
                    html += crearTarjetaLlamada(cita, 'recordatorio');
                });
                html += '</div>';
            }
            
            // Mostrar confirmaciones
            if (confirmaciones.length > 0) {
                html += '<div class="mb-6"><h4 class="font-semibold text-gray-800 mb-3">Confirmaciones Futuras</h4>';
                confirmaciones.forEach(cita => {
                    html += crearTarjetaLlamada(cita, 'confirmacion');
                });
                html += '</div>';
            }
            
            listaLlamadas.innerHTML = html;
        }

        // Función para crear tarjeta de llamada
        function crearTarjetaLlamada(cita, tipo) {
            return `
                <div class="bg-white border border-gray-200 rounded-xl p-4 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <div class="text-center">
                                <div class="text-sm font-bold text-gray-800">${cita.fecha}</div>
                                <div class="text-xs text-gray-500">${cita.hora}</div>
                            </div>
                            <div>
                                <div class="font-medium text-gray-900">${cita.paciente.nombre}</div>
                                <div class="text-sm text-gray-500">CI: ${cita.paciente.ci}</div>
                                <div class="text-xs text-gray-500">Dr. ${cita.medico.usuario.name}</div>
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            <button onclick="registrarLlamada(${cita.id}, true)" class="bg-green-500 hover:bg-green-600 text-white text-xs px-3 py-1.5 rounded-lg transition-colors">
                                Confirmó
                            </button>
                            <button onclick="registrarLlamada(${cita.id}, false)" class="bg-red-500 hover:bg-red-600 text-white text-xs px-3 py-1.5 rounded-lg transition-colors">
                                No contestó
                            </button>
                        </div>
                    </div>
                </div>
            `;
        }

        // Función para registrar llamada
        async function registrarLlamada(citaId, confirmo) {
            try {
                const response = await fetch(`/api/cita/${citaId}/registrar-llamada`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ 
                        confirmado: confirmo,
                        notas: confirmo ? 'Paciente confirmó cita' : 'Paciente no contestó'
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    alert('Llamada registrada exitosamente');
                    cargarPendientesLlamada();
                } else {
                    alert('Error: ' + data.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error al registrar llamada');
            }
        }

        function switchTab(tabName) {
            // Ocultar todos los contenidos
            ['agenda', 'llamadas', 'temporales'].forEach(tab => {
                const el = document.getElementById(`tab-${tab}`);
                el.classList.add('hidden');
                el.classList.remove('block');
            });

            // Mostrar contenido seleccionado
            const selectedEl = document.getElementById(`tab-${tabName}`);
            selectedEl.classList.remove('hidden');
            selectedEl.classList.add('block');

            // Activar botón seleccionado
            document.getElementById(`btn-${tabName}`).className = `flex-1 px-4 py-2.5 ${btnClassesActive} text-center text-sm transition-all duration-200`;
            
            // Desactivar otros botones
            ['agenda', 'llamadas', 'temporales'].forEach(tab => {
                if (tab !== tabName) {
                    document.getElementById(`btn-${tab}`).className = `flex-1 px-4 py-2.5 ${btnClassesInactive} text-center text-sm transition-all duration-200`;
                }
            });

            // Cargar datos específicos según el tab
            if (tabName === 'temporales') {
                cargarPacientesTemporales();
            }
        }

        function cerrarModalConsultaExterna() {
            document.getElementById('modalConsultaExterna').classList.add('hidden');
            document.getElementById('modalConsultaExterna').classList.remove('flex');
        }

        function buscarPaciente() {
            const ci = document.getElementById('paciente_ci').value;
            const tipoPacienteSelect = document.querySelector('select[name="tipo_paciente"]');
            const datosPersonales = document.getElementById('datosPersonales');
            
            if (ci.length < 3) {
                alert('Por favor ingrese un número de cédula válido');
                return;
            }
            
            // Mostrar loading
            const ciField = document.getElementById('paciente_ci');
            const btnBuscar = event.target;
            const originalText = btnBuscar.innerHTML;
            ciField.disabled = true;
            btnBuscar.innerHTML = '<svg class="animate-spin w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>Buscando...';
            btnBuscar.disabled = true;
            
            fetch(`/api/buscar-paciente`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ ci: ci })
            })
            .then(response => response.json())
            .then(data => {
                ciField.disabled = false;
                btnBuscar.innerHTML = originalText;
                btnBuscar.disabled = false;
                
                if (data.success) {
                    // PACIENTE ENCONTRADO
                    tipoPacienteSelect.value = 'existente';
                    tipoPacienteSelect.dispatchEvent(new Event('change'));
                    
                    // Mostrar datos del paciente
                    mostrarDatosPaciente(data.paciente);
                } else {
                    // PACIENTE NO ENCONTRADO
                    tipoPacienteSelect.value = 'nuevo';
                    tipoPacienteSelect.dispatchEvent(new Event('change'));
                    
                    // Eliminar sección de datos existentes si hay una
                    const existingSection = ciField.closest('.grid').parentElement.querySelector('.bg-green-50');
                    if (existingSection) {
                        existingSection.remove();
                    }
                }
            })
            .catch(error => {
                ciField.disabled = false;
                btnBuscar.innerHTML = originalText;
                btnBuscar.disabled = false;
                console.error('Error:', error);
                alert('Error al buscar paciente');
            });
        }

        function mostrarDatosPaciente(paciente) {
            // Crear una sección para mostrar los datos del paciente encontrado
            const datosSection = document.createElement('div');
            datosSection.className = 'mt-4 p-4 bg-green-50 rounded-xl border border-green-200';
            datosSection.innerHTML = `
                <div class="flex items-center mb-3">
                    <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="font-semibold text-green-800">Paciente Encontrado</span>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div><strong>Nombre:</strong> ${paciente.nombre}</div>
                    <div><strong>CI:</strong> ${paciente.ci}</div>
                    <div><strong>Teléfono:</strong> ${paciente.telefono || 'N/A'}</div>
                    <div><strong>Correo:</strong> ${paciente.correo || 'N/A'}</div>
                </div>
            `;
            
            // Insertar después del campo de CI
            const ciField = document.getElementById('paciente_ci').closest('.grid').parentElement;
            const existingSection = ciField.querySelector('.bg-green-50');
            if (existingSection) {
                existingSection.remove();
            }
            ciField.appendChild(datosSection);
        }

        function registrarConsultaExterna() {
            event.preventDefault();
            
            const form = document.getElementById('formConsultaExterna');
            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());
            
            // Validar campos requeridos básicos
            if (!data.ci || !data.triage_tipo) {
                alert('Por favor complete CI y tipo de triage');
                return;
            }

            if (data.triage_tipo === 'verde' && (!data.especialidad || !data.medico || !data.seguro || !data.motivo)) {
                alert('Para triage VERDE debe completar los datos de consulta normal');
                return;
            }

            // Validar que si es paciente nuevo, complete todos los datos personales
            if (data.tipo_paciente === 'nuevo') {
                if (!data.nombres || !data.apellidos || !data.sexo) {
                    alert('Por favor complete todos los datos personales obligatorios (nombres, apellidos, sexo)');
                    return;
                }
                
                // Validar que el sexo tenga un valor válido
                if (data.sexo === '' || data.sexo === null) {
                    alert('Por favor seleccione el sexo del paciente');
                    return;
                }
            }

            // Mostrar loading
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<svg class="animate-spin w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>Procesando...';
            submitBtn.disabled = true;

            fetch('/api/triage-general', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message || 'Triage procesado correctamente');
                    cerrarModalConsultaExterna();
                    // Redirigir a página de confirmación solo para consulta normal
                    if (data.redirect_url) {
                        window.location.href = data.redirect_url;
                    }
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al registrar la consulta');
            })
            .finally(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        }

        // Mostrar/ocultar formulario de datos personales según tipo de paciente
        function inicializarTipoPaciente() {
            const tipoPacienteSelect = document.querySelector('select[name="tipo_paciente"]');
            const datosPersonales = document.getElementById('datosPersonales');
            
            if (tipoPacienteSelect && datosPersonales) {
                tipoPacienteSelect.addEventListener('change', function() {
                    console.log('Tipo paciente cambiado a:', this.value); // Debug
                    if (this.value === 'nuevo') {
                        datosPersonales.classList.remove('hidden');
                        datosPersonales.classList.add('block');
                        console.log('Mostrando formulario de datos personales'); // Debug
                        // Habilitar campos de consulta
                        habilitarCamposConsulta(true);
                    } else {
                        datosPersonales.classList.add('hidden');
                        datosPersonales.classList.remove('block');
                        console.log('Ocultando formulario de datos personales'); // Debug
                        // Habilitar campos de consulta
                        habilitarCamposConsulta(true);
                    }
                });
                
                // Disparar el evento change al cargar para establecer el estado inicial
                tipoPacienteSelect.dispatchEvent(new Event('change'));
            } else {
                console.error('No se encontraron los elementos del formulario');
            }
        }



        function actualizarCamposPorTriage() {
            const tipo = document.getElementById('triage_tipo')?.value;
            const rojo = document.getElementById('bloqueRojo');
            const amarillo = document.getElementById('bloqueAmarillo');

            if (rojo) rojo.classList.add('hidden');
            if (amarillo) amarillo.classList.add('hidden');

            habilitarCamposConsulta(tipo === 'verde');

            if (tipo === 'rojo' && rojo) rojo.classList.remove('hidden');
            if (tipo === 'amarillo' && amarillo) amarillo.classList.remove('hidden');
        }

        // Función para habilitar/deshabilitar campos de consulta
        function habilitarCamposConsulta(habilitar) {
            const camposConsulta = [
                'select[name="especialidad"]',
                'select[name="medico"]',
                'select[name="seguro"]',
                'input[name="motivo"]',
                'textarea[name="observaciones"]'
            ];
            
            camposConsulta.forEach(selector => {
                const campo = document.querySelector(selector);
                if (campo) {
                    campo.disabled = !habilitar;
                    if (habilitar) {
                        campo.classList.remove('bg-gray-100');
                        campo.classList.add('bg-white');
                    } else {
                        campo.classList.remove('bg-white');
                        campo.classList.add('bg-gray-100');
                    }
                }
            });
        }

        // Inicializar cuando el DOM esté listo
        document.addEventListener('DOMContentLoaded', function() {
            inicializarTipoPaciente();
            actualizarCamposPorTriage();
        });

        // También inicializar cuando se abre el modal
        function abrirConsultaExterna() {
            document.getElementById('modalConsultaExterna').classList.remove('hidden');
            document.getElementById('modalConsultaExterna').classList.add('flex');
            
            // Re-inicializar el tipo de paciente
            setTimeout(() => {
                inicializarTipoPaciente();
            actualizarCamposPorTriage();
            }, 100);
        }

        // Funciones para modal de nueva cita
        function abrirModalNuevaCita() {
            document.getElementById('modalNuevaCita').classList.remove('hidden');
            document.getElementById('modalNuevaCita').classList.add('flex');
        }

        function cerrarModalNuevaCita() {
            document.getElementById('modalNuevaCita').classList.add('hidden');
            document.getElementById('modalNuevaCita').classList.remove('flex');
            document.getElementById('formNuevaCita').reset();
        }

        async function cargarEspecialidades() {
            try {
                const response = await fetch('/api/especialidades');
                const especialidades = await response.json();
                
                const select = document.querySelector('select[name="codigo_especialidad"]');
                select.innerHTML = '<option value="">Seleccione...</option>';
                
                especialidades.forEach(esp => {
                    select.innerHTML += `<option value="${esp.codigo}">${esp.nombre}</option>`;
                });
            } catch (error) {
                console.error('Error al cargar especialidades:', error);
            }
        }

        async function cargarMedicosDisponibles() {
            const especialidad = document.querySelector('select[name="codigo_especialidad"]').value;
            const fecha = document.querySelector('input[name="fecha"]').value;
            const hora = document.querySelector('input[name="hora"]').value;
            
            if (!especialidad) return;
            
            try {
                const params = new URLSearchParams({ especialidad, fecha, hora });
                const response = await fetch(`/api/medicos-disponibles?${params}`);
                const data = await response.json();
                
                const select = document.querySelector('select[name="ci_medico"]');
                select.innerHTML = '<option value="">Seleccione...</option>';
                
                if (data.success) {
                    data.medicos.forEach(medico => {
                        select.innerHTML += `<option value="${medico.ci}">Dr. ${medico.usuario.name}</option>`;
                    });
                }
            } catch (error) {
                console.error('Error al cargar médicos:', error);
            }
        }

        async function crearNuevaCita() {
            event.preventDefault();
            
            const form = document.getElementById('formNuevaCita');
            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());
            
            try {
                const response = await fetch('/api/nueva-cita', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(data)
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert('Cita creada exitosamente');
                    cerrarModalNuevaCita();
                    cargarAgendaDia();
                    cargarEstadisticasDashboard();
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error al crear la cita');
            }
        }

        async function buscarPacienteParaCita() {
            const ci = document.getElementById('cita_ci_paciente').value;
            
            if (!ci) {
                alert('Ingrese un número de CI');
                return;
            }
            
            try {
                const response = await fetch('/api/buscar-paciente', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ ci })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    alert(`Paciente encontrado: ${data.paciente.nombre}`);
                } else {
                    alert('Paciente no encontrado. Debe registrar el paciente primero.');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error al buscar paciente');
            }
        }

        // Funciones para pacientes temporales de emergencia
        async function cargarPacientesTemporales() {
            try {
                const response = await fetch('/api/emergencias-temporales');
                const data = await response.json();
                
                if (data.success) {
                    mostrarPacientesTemporales(data.emergencias);
                }
            } catch (error) {
                console.error('Error:', error);
                document.getElementById('lista-pacientes-temporales').innerHTML = `
                    <div class="p-8 text-center text-red-500">
                        <p>Error al cargar pacientes temporales</p>
                    </div>
                `;
            }
        }

        function mostrarPacientesTemporales(emergencias) {
            const contenedor = document.getElementById('lista-pacientes-temporales');
            
            if (emergencias.length === 0) {
                contenedor.innerHTML = `
                    <div class="p-8 text-center text-gray-500 bg-gray-50 rounded-xl">
                        <svg class="w-12 h-12 mx-auto mb-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-gray-600 font-medium">No hay pacientes temporales pendientes</p>
                        <p class="text-sm text-gray-400 mt-1">Todos los pacientes de emergencia han completado sus datos</p>
                    </div>
                `;
                return;
            }

            contenedor.innerHTML = emergencias.map(emp => `
                <div class="bg-gradient-to-r from-orange-50 to-white rounded-xl p-4 border-2 border-orange-200 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-gray-800 text-lg">${emp.code}</span>
                                    <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-700 border border-orange-200">
                                        ID Temporal
                                    </span>
                                </div>
                                <p class="text-sm text-gray-600 font-mono">${emp.temp_id || 'Sin identificación'}</p>
                                <div class="flex gap-3 mt-1 text-xs text-gray-500">
                                    <span><strong>Ingreso:</strong> ${emp.tipo_ingreso_label}</span>
                                    <span>|</span>
                                    <span><strong>Fecha:</strong> ${emp.fecha_ingreso}</span>
                                    <span>|</span>
                                    <span><strong>Hora:</strong> ${emp.hora_ingreso}</span>
                                    <span>|</span>
                                    <span class="capitalize"><strong>Estado:</strong> ${emp.status_label}</span>
                                    <span>|</span>
                                    <span class="capitalize text-orange-600"><strong>Ubicación:</strong> ${emp.ubicacion_actual || 'No especificada'}</span>
                                </div>
                            </div>
                        </div>
                        <button onclick="window.location.href = '/reception/completar-datos-paciente/' + '${emp.id}'" 
                            class="px-5 py-2.5 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition-colors text-sm font-medium shadow-sm flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Completar Datos
                        </button>
                    </div>
                </div>
            `).join('');
        }

        function abrirModalCompletarDatos(emergencyId, code, tempId) {
            document.getElementById('modal_temp_emergency_id').value = emergencyId;
            document.getElementById('modal_temp_code').textContent = code;
            document.getElementById('modal_temp_id').textContent = tempId || 'Sin ID temporal';
            document.getElementById('modalCompletarDatos').classList.remove('hidden');
            document.getElementById('modalCompletarDatos').classList.add('flex');
        }

        function cerrarModalCompletarDatos() {
            document.getElementById('modalCompletarDatos').classList.add('hidden');
            document.getElementById('modalCompletarDatos').classList.remove('flex');
            document.getElementById('formCompletarDatos').reset();
        }

        async function guardarDatosPaciente() {
            event.preventDefault();
            
            const form = document.getElementById('formCompletarDatos');
            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());
            
            // Validar campos requeridos
            if (!data.ci || !data.nombres || !data.apellidos || !data.sexo) {
                alert('Por favor complete todos los campos obligatorios: CI, Nombres, Apellidos y Sexo');
                return;
            }
            
            try {
                const response = await fetch('/api/completar-datos-paciente-temporal', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(data)
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert('Datos del paciente guardados correctamente');
                    cerrarModalCompletarDatos();
                    cargarPacientesTemporales();
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error al guardar los datos del paciente');
            }
        }
    </script>

    <!-- Modal Completar Datos del Paciente Temporal -->
    <div id="modalCompletarDatos" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <!-- Header del Modal -->
            <div class="bg-gradient-to-r from-orange-500 to-orange-600 text-white p-6 rounded-t-2xl">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-2xl font-bold">Completar Datos del Paciente</h3>
                        <p class="text-orange-100 text-sm mt-1">Convertir paciente temporal a registro completo</p>
                    </div>
                    <button onclick="cerrarModalCompletarDatos()" class="bg-white/20 hover:bg-white/30 p-2 rounded-full transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Información de la Emergencia -->
            <div class="bg-orange-50 p-4 border-b border-orange-100">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Código de Emergencia: <span id="modal_temp_code" class="font-bold text-orange-700"></span></p>
                        <p class="text-sm text-gray-600">ID Temporal: <span id="modal_temp_id" class="font-mono text-orange-700"></span></p>
                    </div>
                </div>
            </div>

            <!-- Contenido del Modal -->
            <div class="p-6">
                <form id="formCompletarDatos" onsubmit="guardarDatosPaciente(); return false;">
                    <input type="hidden" id="modal_temp_emergency_id" name="emergency_id">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- CI -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Cédula de Identidad (CI) *</label>
                            <input type="text" name="ci" placeholder="Número de CI del paciente" 
                                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-100 transition-all" required>
                            <p class="text-xs text-gray-500 mt-1">Este será el identificador único del paciente en el sistema</p>
                        </div>
                        
                        <!-- Nombres -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nombres *</label>
                            <input type="text" name="nombres" placeholder="Nombres del paciente" 
                                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-100 transition-all" required>
                        </div>
                        
                        <!-- Apellidos -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Apellidos *</label>
                            <input type="text" name="apellidos" placeholder="Apellidos del paciente" 
                                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-100 transition-all" required>
                        </div>
                        
                        <!-- Sexo -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Sexo *</label>
                            <select name="sexo" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-100 transition-all" required>
                                <option value="">Seleccione...</option>
                                <option value="Masculino">Masculino</option>
                                <option value="Femenino">Femenino</option>
                            </select>
                        </div>
                        
                        <!-- Teléfono -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Teléfono</label>
                            <input type="tel" name="telefono" placeholder="Ej: 0414-1234567" 
                                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-100 transition-all">
                        </div>
                        
                        <!-- Correo -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Correo Electrónico</label>
                            <input type="email" name="correo" placeholder="correo@ejemplo.com" 
                                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-100 transition-all">
                        </div>
                        
                        <!-- Dirección -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Dirección</label>
                            <textarea name="direccion" rows="2" placeholder="Dirección completa del paciente" 
                                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-100 transition-all"></textarea>
                        </div>
                    </div>

                    <!-- Botones de Acción -->
                    <div class="flex justify-end gap-3 pt-6 border-t border-gray-200 mt-6">
                        <button type="button" onclick="cerrarModalCompletarDatos()" class="px-6 py-2.5 border border-gray-200 rounded-xl text-gray-700 font-medium hover:bg-gray-50 transition-colors text-sm">
                            Cancelar
                        </button>
                        <button type="submit" class="px-6 py-2.5 bg-orange-500 text-white rounded-xl font-medium hover:bg-orange-600 transition-colors flex items-center text-sm shadow-md">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Guardar Datos del Paciente
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
@endsection