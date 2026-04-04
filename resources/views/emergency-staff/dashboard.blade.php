@extends('layouts.app')

@section('content')
<div class="p-6 bg-gray-50/50 min-h-screen">
    <!-- Header -->
    <div class="flex justify-between items-end mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Dashboard de Emergencias</h1>
            <p class="text-sm text-gray-500">Usuario Emergencia - Atención en tiempo real</p>
        </div>
        <div class="flex gap-3">
            <button onclick="cargarEmergencias()" class="flex items-center px-4 py-2 bg-white border border-gray-200 text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition-all shadow-sm">
                <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Actualizar
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex flex-col items-center justify-center h-28 hover:shadow-md transition">
            <span class="text-gray-500 text-sm font-medium mb-1">Pacientes Activos</span>
            <span class="text-3xl font-bold text-red-600" id="stat-activos">0</span>
        </div>
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex flex-col items-center justify-center h-28 hover:shadow-md transition">
            <span class="text-gray-500 text-sm font-medium mb-1">En Espera</span>
            <span class="text-3xl font-bold text-yellow-600" id="stat-espera">0</span>
        </div>
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex flex-col items-center justify-center h-28 hover:shadow-md transition">
            <span class="text-gray-500 text-sm font-medium mb-1">En Atención</span>
            <span class="text-3xl font-bold text-blue-600" id="stat-atencion">0</span>
        </div>
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex flex-col items-center justify-center h-28 hover:shadow-md transition">
            <span class="text-gray-500 text-sm font-medium mb-1">Hoy Ingresados</span>
            <span class="text-3xl font-bold text-green-600" id="stat-hoy">0</span>
        </div>
    </div>

    <!-- Alertas de Recursos -->
    <div id="alertas-recursos" class="mb-6 hidden">
        <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded-lg">
            <div class="flex items-center">
                <svg class="w-6 h-6 text-yellow-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <div>
                    <h3 class="text-yellow-800 font-bold">Alerta de Recursos</h3>
                    <p class="text-yellow-700 text-sm" id="alerta-mensaje"></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Pacientes en Emergencia -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-lg font-bold text-gray-800">Pacientes en Emergencia</h2>
            <div class="flex gap-2">
                <select id="filtro-estado" onchange="cargarEmergencias()" class="border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:border-red-500">
                    <option value="todos">Todos los estados</option>
                    <option value="recibido">Recibidos</option>
                    <option value="en_evaluacion">En Evaluación</option>
                    <option value="estabilizado">Estabilizados</option>
                </select>
            </div>
        </div>

        <!-- Tabla de Pacientes -->
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 rounded-l-lg">Código</th>
                        <th scope="col" class="px-6 py-3">Paciente</th>
                        <th scope="col" class="px-6 py-3">Tipo Ingreso</th>
                        <th scope="col" class="px-6 py-3">Destino Inicial</th>
                        <th scope="col" class="px-6 py-3">Hora Ingreso</th>
                        <th scope="col" class="px-6 py-3">Estado</th>
                        <th scope="col" class="px-6 py-3 rounded-r-lg">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tabla-emergencias">
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-gray-400">
                            <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <p>Cargando pacientes...</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- SECCIÓN: Pacientes con ID Temporal -->
    <div class="bg-gradient-to-r from-orange-50 to-red-50 rounded-2xl shadow-sm border-2 border-orange-200 p-6 mb-6">
        <div class="flex justify-between items-center mb-4">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-2 0h-2m-2 0h-2m-2 0H8m0 0H6m2 0h2m-2 0v-2m0 2v2m14-6h-3m-3 0h-2m-2 0H8m0 0H6m2 0h2m-2 0V7m0 2v2M7 7h.01M7 11h.01M7 15h.01M11 7h.01M11 11h.01M11 15h.01M15 7h.01M15 11h.01M15 15h.01"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-orange-800">Pacientes con ID Temporal</h2>
                    <p class="text-sm text-orange-600">Pacientes que ingresaron sin documento y necesitan completar sus datos</p>
                </div>
            </div>
            <button onclick="cargarEmergenciasTemporales()" class="flex items-center px-4 py-2 bg-orange-500 text-white font-medium rounded-xl hover:bg-orange-600 transition-all shadow-sm">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Actualizar
            </button>
        </div>

        <div id="lista-emergencias-temporales" class="space-y-3">
            <div class="p-8 text-center text-gray-500">
                <svg class="w-12 h-12 mx-auto mb-4 text-orange-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-gray-600">Cargando pacientes temporales...</p>
            </div>
        </div>
    </div>

    <!-- Modal de Acciones -->
    <div id="modalAcciones" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full max-h-[90vh] overflow-y-auto">
            <div class="bg-gradient-to-r from-red-500 to-red-600 text-white p-6 rounded-t-2xl">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-xl font-bold">Acciones del Paciente</h3>
                        <p class="text-red-100 text-sm mt-1" id="modal-paciente-nombre"></p>
                    </div>
                    <button onclick="cerrarModal()" class="bg-white/20 hover:bg-white/30 p-2 rounded-full transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
            
            <div class="p-6">
                <div class="grid grid-cols-1 gap-3">
                    <button onclick="iniciarEvaluacion()" class="flex items-center p-4 border border-gray-200 rounded-xl hover:bg-blue-50 hover:border-blue-300 transition-all text-left">
                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <span class="font-semibold text-gray-800">Iniciar Evaluación</span>
                            <p class="text-xs text-gray-500">Comenzar atención médica</p>
                        </div>
                    </button>

                    <button onclick="verHistorial()" class="flex items-center p-4 border border-gray-200 rounded-xl hover:bg-gray-50 hover:border-gray-300 transition-all text-left">
                        <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center mr-4">
                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <span class="font-semibold text-gray-800">Ver Historial</span>
                            <p class="text-xs text-gray-500">Evaluaciones, medicamentos y triage</p>
                        </div>
                    </button>

                    <button onclick="cambiarEstado('estabilizado')" class="flex items-center p-4 border border-gray-200 rounded-xl hover:bg-green-50 hover:border-green-300 transition-all text-left">
                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mr-4">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <div>
                            <span class="font-semibold text-gray-800">Marcar Estabilizado</span>
                            <p class="text-xs text-gray-500">Paciente estable para decisión</p>
                        </div>
                    </button>

                    <button onclick="derivarA('cirugia')" class="flex items-center p-4 border border-gray-200 rounded-xl hover:bg-purple-50 hover:border-purple-300 transition-all text-left">
                        <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center mr-4">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                            </svg>
                        </div>
                        <div>
                            <span class="font-semibold text-gray-800">Enviar a Cirugía</span>
                            <p class="text-xs text-gray-500">Derivar a quirófano</p>
                        </div>
                    </button>

                    <button onclick="derivarA('uti')" class="flex items-center p-4 border border-gray-200 rounded-xl hover:bg-red-50 hover:border-red-300 transition-all text-left">
                        <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center mr-4">
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                        </div>
                        <div>
                            <span class="font-semibold text-gray-800">Enviar a UTI</span>
                            <p class="text-xs text-gray-500">Unidad de Terapia Intensiva</p>
                        </div>
                    </button>

                    <button onclick="derivarA('hospitalizacion')" class="flex items-center p-4 border border-gray-200 rounded-xl hover:bg-indigo-50 hover:border-indigo-300 transition-all text-left">
                        <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center mr-4">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <div>
                            <span class="font-semibold text-gray-800">Internación</span>
                            <p class="text-xs text-gray-500">Enviar a hospitalización</p>
                        </div>
                    </button>

                    <button onclick="derivarA('observacion')" class="flex items-center p-4 border border-gray-200 rounded-xl hover:bg-yellow-50 hover:border-yellow-300 transition-all text-left">
                        <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center mr-4">
                            <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </div>
                        <div>
                            <span class="font-semibold text-gray-800">Observación</span>
                            <p class="text-xs text-gray-500">Área de observación/camilla</p>
                        </div>
                    </button>

                    <button onclick="darAlta()" class="flex items-center p-4 border border-gray-200 rounded-xl hover:bg-green-50 hover:border-green-300 transition-all text-left">
                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mr-4">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                        </div>
                        <div>
                            <span class="font-semibold text-gray-800">Dar de Alta</span>
                            <p class="text-xs text-gray-500">Paciente egresado</p>
                        </div>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let emergencyIdActual = null;

    // Cargar emergencias al iniciar
    document.addEventListener('DOMContentLoaded', function() {
        cargarEmergencias();
        cargarEstadisticas();
        cargarEmergenciasTemporales();
        // Auto-refresh cada 30 segundos
        setInterval(() => {
            cargarEmergencias();
            cargarEmergenciasTemporales();
        }, 30000);
    });

    async function cargarEmergencias() {
        try {
            const filtro = document.getElementById('filtro-estado').value;
            const response = await fetch('/emergency-staff/api/emergencias?estado=' + filtro);
            const data = await response.json();
            
            if (data.success) {
                mostrarEmergencias(data.emergencias);
                actualizarEstadisticas(data.stats);
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }

    async function cargarEstadisticas() {
        try {
            const response = await fetch('/emergency-staff/api/estadisticas');
            const data = await response.json();
            
            if (data.success) {
                actualizarEstadisticas(data.stats);
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }

    function actualizarEstadisticas(stats) {
        document.getElementById('stat-activos').textContent = stats.activos || 0;
        document.getElementById('stat-espera').textContent = stats.espera || 0;
        document.getElementById('stat-atencion').textContent = stats.atencion || 0;
        document.getElementById('stat-hoy').textContent = stats.hoy || 0;
    }

    async function cargarEmergenciasTemporales() {
        try {
            const response = await fetch('/emergency-staff/api/emergencias-temporales');
            const data = await response.json();
            
            if (data.success) {
                mostrarEmergenciasTemporales(data.emergencias);
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }

    function mostrarEmergenciasTemporales(emergencias) {
        const contenedor = document.getElementById('lista-emergencias-temporales');
        
        if (emergencias.length === 0) {
            contenedor.innerHTML = `
                <div class="p-6 text-center text-gray-500 bg-white/50 rounded-xl">
                    <svg class="w-12 h-12 mx-auto mb-3 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-gray-600">No hay pacientes con ID temporal pendientes</p>
                    <p class="text-sm text-gray-400 mt-1">Todos los pacientes han completado sus datos</p>
                </div>
            `;
            return;
        }

        contenedor.innerHTML = emergencias.map(emp => `
            <div class="bg-white rounded-xl p-4 border-2 border-orange-200 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-gray-800">${emp.code}</span>
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-700 border border-orange-200">
                                    ID Temporal
                                </span>
                            </div>
                            <p class="text-sm text-gray-600">${emp.temp_id || 'Sin identificación'}</p>
                            <div class="flex gap-3 mt-1 text-xs text-gray-500">
                                <span><strong>Ingreso:</strong> ${emp.tipo_ingreso_label}</span>
                                <span>|</span>
                                <span><strong>Hora:</strong> ${emp.hora_ingreso}</span>
                                <span>|</span>
                                <span class="capitalize"><strong>Estado:</strong> ${emp.status_label}</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <button onclick="abrirModal(${emp.id}, '${emp.code} - ID Temporal')" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors text-sm font-medium">
                            Acciones
                        </button>
                        <a href="{{ route('emergency-staff.historial', '') }}/${emp.id}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-sm font-medium">
                            Ver
                        </a>
                    </div>
                </div>
            </div>
        `).join('');
    }

    function mostrarEmergencias(emergencias) {
        const tbody = document.getElementById('tabla-emergencias');
        
        if (emergencias.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="7" class="px-6 py-8 text-center text-gray-400">
                        <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p>No hay pacientes en emergencia</p>
                    </td>
                </tr>
            `;
            return;
        }

        tbody.innerHTML = emergencias.map(emp => `
            <tr class="bg-white border-b hover:bg-gray-50">
                <td class="px-6 py-4 font-medium text-gray-900">${emp.code}</td>
                <td class="px-6 py-4">
                    <div class="font-medium text-gray-900">${emp.paciente_nombre}</div>
                    <div class="text-xs text-gray-500">${emp.is_temp_id ? 'ID Temporal' : 'CI: ' + emp.paciente_id}</div>
                </td>
                <td class="px-6 py-4">
                    <span class="px-2 py-1 rounded-full text-xs font-medium ${getTipoIngresoClass(emp.tipo_ingreso)}">
                        ${emp.tipo_ingreso_label}
                    </span>
                </td>
                <td class="px-6 py-4 capitalize">${emp.destino_inicial || 'Pendiente'}</td>
                <td class="px-6 py-4">${emp.hora_ingreso}</td>
                <td class="px-6 py-4">
                    <span class="px-2 py-1 rounded-full text-xs font-medium ${getEstadoClass(emp.status)}">
                        ${emp.status_label}
                    </span>
                </td>
                <td class="px-6 py-4">
                    <button onclick="abrirModal(${emp.id}, '${emp.paciente_nombre}')" class="text-red-600 hover:text-red-900 font-medium text-sm">
                        Acciones
                    </button>
                </td>
            </tr>
        `).join('');
    }

    function getTipoIngresoClass(tipo) {
        const classes = {
            'soat': 'bg-orange-100 text-orange-800',
            'parto': 'bg-pink-100 text-pink-800',
            'general': 'bg-blue-100 text-blue-800'
        };
        return classes[tipo] || 'bg-gray-100 text-gray-800';
    }

    function getEstadoClass(estado) {
        const classes = {
            'recibido': 'bg-yellow-100 text-yellow-800',
            'en_evaluacion': 'bg-blue-100 text-blue-800',
            'estabilizado': 'bg-green-100 text-green-800',
            'cirugia': 'bg-purple-100 text-purple-800',
            'uti': 'bg-red-100 text-red-800',
            'alta': 'bg-gray-100 text-gray-800'
        };
        return classes[estado] || 'bg-gray-100 text-gray-800';
    }

    function abrirModal(id, nombre) {
        emergencyIdActual = id;
        document.getElementById('modal-paciente-nombre').textContent = nombre;
        document.getElementById('modalAcciones').classList.remove('hidden');
    }

    function cerrarModal() {
        document.getElementById('modalAcciones').classList.add('hidden');
        emergencyIdActual = null;
    }

    function iniciarEvaluacion() {
        if (!emergencyIdActual) return;
        window.location.href = `/emergency-staff/${emergencyIdActual}/evaluacion`;
    }

    function verHistorial() {
        if (!emergencyIdActual) return;
        window.location.href = `/emergency-staff/${emergencyIdActual}/historial`;
    }

    async function cambiarEstado(nuevoEstado) {
        if (!emergencyIdActual) return;
        
        try {
            const response = await fetch(`/emergency-staff/${emergencyIdActual}/update-status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ status: nuevoEstado })
            });
            
            const data = await response.json();
            
            if (data.success) {
                cerrarModal();
                cargarEmergencias();
                alert('Estado actualizado correctamente');
            } else {
                alert('Error: ' + data.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error al actualizar estado');
        }
    }

    async function derivarA(destino) {
        if (!emergencyIdActual) return;
        
        try {
            const response = await fetch(`/emergency-staff/${emergencyIdActual}/derivar`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ destino: destino })
            });
            
            const data = await response.json();
            
            if (data.success) {
                cerrarModal();
                cargarEmergencias();
                alert(`Paciente derivado a ${destino} correctamente`);
            } else {
                if (data.requiere_confirmacion) {
                    if (confirm(data.message + '\n\n¿Desea continuar de todos modos?')) {
                        // Reintentar con forzar=true
                        forzarDerivacion(destino);
                    }
                } else {
                    alert('Error: ' + data.message);
                }
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error al derivar paciente');
        }
    }

    async function forzarDerivacion(destino) {
        try {
            const response = await fetch(`/emergency-staff/${emergencyIdActual}/derivar`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ destino: destino, forzar: true })
            });
            
            const data = await response.json();
            
            if (data.success) {
                cerrarModal();
                cargarEmergencias();
                alert('Paciente derivado correctamente');
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }

    async function darAlta() {
        if (!emergencyIdActual) return;
        
        if (!confirm('¿Está seguro de dar de alta a este paciente?')) return;
        
        try {
            const response = await fetch(`/emergency-staff/${emergencyIdActual}/alta`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                cerrarModal();
                cargarEmergencias();
                alert('Paciente dado de alta correctamente');
            } else {
                alert('Error: ' + data.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error al dar de alta');
        }
    }

    // Cerrar modal al hacer click fuera
    document.getElementById('modalAcciones').addEventListener('click', function(e) {
        if (e.target === this) {
            cerrarModal();
        }
    });
</script>
@endsection
