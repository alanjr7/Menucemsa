@extends('layouts.app')

@php
// Obtener permisos del usuario actual
$user = auth()->user();
$userPermissions = [];

if ($user->isEnfermeraEmergencia()) {
    $enfermera = \App\Models\Enfermera::where('user_id', $user->id)->first();
    if ($enfermera) {
        $userPermissions = $enfermera->getPermissionKeys();
    }
} else {
    // Roles emergencia, admin, dirmedico tienen todos los permisos
    $userPermissions = array_keys(\App\Models\EnfermeraPermission::AVAILABLE_PERMISSIONS);
}

// Helper function to check permission
$hasPermission = function($permission) use ($userPermissions) {
    return in_array($permission, $userPermissions);
};
@endphp

@section('content')
<div class="p-6 bg-gray-50/50 min-h-screen">
    <!-- Header -->
    <div class="flex justify-between items-end mb-8">
        <div>
            @if(auth()->user()->isEnfermeraEmergencia())
                <h1 class="text-2xl font-bold text-gray-800">Panel de Enfermería</h1>
                <p class="text-sm text-gray-500">Enfermera Emergencia - Atención y signos vitales</p>
            @else
                <h1 class="text-2xl font-bold text-gray-800">Dashboard de Emergencias</h1>
                <p class="text-sm text-gray-500">Usuario Emergencia - Atención en tiempo real</p>
            @endif
        </div>
        <div class="flex gap-3">
            <!-- <button onclick="cargarEmergencias()" class="flex items-center px-4 py-2 bg-white border border-gray-200 text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition-all shadow-sm">
                <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Actualizar
            </button> -->
            {{-- @if($hasPermission('aplicar_medicamentos')) --}}
             @if(auth()->user()->isEmergencia() || auth()->user()->isAdmin())
            <a href="{{ route('emergency-staff.medicamentos.index') }}" class="flex items-center px-4 py-2 bg-red-600 text-white font-medium rounded-xl hover:bg-red-700 transition-all shadow-sm">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                </svg>
                Gestionar Medicamentos
            </a>
            @endif
            @if(auth()->user()->isEmergencia() || auth()->user()->isAdmin())
            <a href="{{ route('emergency-staff.enfermeras.index') }}" class="flex items-center px-4 py-2 bg-blue-600 text-white font-medium rounded-xl hover:bg-blue-700 transition-all shadow-sm">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                Gestionar Enfermeras
            </a>
            @endif
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
                <select id="filtro-estado" onchange="onFiltroChange()" class="border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:border-red-500">
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

    <!-- Modal de Acciones -->
    <div id="modalAcciones" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-6">
        <div class="bg-white shadow-2xl max-w-3xl w-full max-h-[85vh] overflow-y-auto">
            <!-- Header minimalista y profesional -->
            <div class="border-b border-gray-200 bg-gray-50 px-8 py-5">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 tracking-tight">Acciones del Paciente</h3>
                        <p class="text-sm text-gray-500 mt-0.5" id="modal-paciente-nombre"></p>
                    </div>
                    <button onclick="cerrarModal()" class="text-gray-400 hover:text-gray-600 transition-colors p-1">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Contenido con diseño minimalista -->
            <div class="p-8">
                <div class="grid grid-cols-2 gap-4">
                    @if($hasPermission('cambiar_estados'))
                    <button onclick="iniciarEvaluacion()" class="group flex items-center p-4 border border-gray-200 hover:border-gray-400 hover:bg-gray-50 transition-all text-left">
                        <div class="w-9 h-9 bg-gray-100 group-hover:bg-gray-200 flex items-center justify-center mr-3 transition-colors">
                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <span class="font-medium text-gray-700 text-sm">Iniciar Evaluación</span>
                            <p class="text-xs text-gray-400 mt-0.5">Comenzar atención médica</p>
                        </div>
                    </button>
                    @endif

                    @if($hasPermission('ver_historial'))
                    <button onclick="verHistorial()" class="group flex items-center p-4 border border-gray-200 hover:border-gray-400 hover:bg-gray-50 transition-all text-left">
                        <div class="w-9 h-9 bg-gray-100 group-hover:bg-gray-200 flex items-center justify-center mr-3 transition-colors">
                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <span class="font-medium text-gray-700 text-sm">Ver Historial</span>
                            <p class="text-xs text-gray-400 mt-0.5">Evaluaciones y medicamentos</p>
                        </div>
                    </button>
                    @endif

                    @if($hasPermission('cambiar_estados'))
                    <button onclick="cambiarEstado('estabilizado')" class="group flex items-center p-4 border border-gray-200 hover:border-gray-400 hover:bg-gray-50 transition-all text-left">
                        <div class="w-9 h-9 bg-gray-100 group-hover:bg-gray-200 flex items-center justify-center mr-3 transition-colors">
                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <div>
                            <span class="font-medium text-gray-700 text-sm">Marcar Estabilizado</span>
                            <p class="text-xs text-gray-400 mt-0.5">Paciente estable</p>
                        </div>
                    </button>
                    @endif

                    @if($hasPermission('derivar_pacientes'))
                    <button onclick="derivarA('cirugia')" class="group flex items-center p-4 border border-gray-200 hover:border-gray-400 hover:bg-gray-50 transition-all text-left">
                        <div class="w-9 h-9 bg-gray-100 group-hover:bg-gray-200 flex items-center justify-center mr-3 transition-colors">
                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                            </svg>
                        </div>
                        <div>
                            <span class="font-medium text-gray-700 text-sm">Enviar a Cirugía</span>
                            <p class="text-xs text-gray-400 mt-0.5">Derivar a quirófano</p>
                        </div>
                    </button>

                    <button onclick="derivarA('uti')" class="group flex items-center p-4 border border-gray-200 hover:border-gray-400 hover:bg-gray-50 transition-all text-left">
                        <div class="w-9 h-9 bg-gray-100 group-hover:bg-gray-200 flex items-center justify-center mr-3 transition-colors">
                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                        </div>
                        <div>
                            <span class="font-medium text-gray-700 text-sm">Enviar a UTI</span>
                            <p class="text-xs text-gray-400 mt-0.5">Unidad Terapia Intensiva</p>
                        </div>
                    </button>

                    <button onclick="derivarA('hospitalizacion')" class="group flex items-center p-4 border border-gray-200 hover:border-gray-400 hover:bg-gray-50 transition-all text-left">
                        <div class="w-9 h-9 bg-gray-100 group-hover:bg-gray-200 flex items-center justify-center mr-3 transition-colors">
                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <div>
                            <span class="font-medium text-gray-700 text-sm">Internación</span>
                            <p class="text-xs text-gray-400 mt-0.5">Hospitalización</p>
                        </div>
                    </button>

                    <button onclick="derivarA('observacion')" class="group flex items-center p-4 border border-gray-200 hover:border-gray-400 hover:bg-gray-50 transition-all text-left">
                        <div class="w-9 h-9 bg-gray-100 group-hover:bg-gray-200 flex items-center justify-center mr-3 transition-colors">
                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </div>
                        <div>
                            <span class="font-medium text-gray-700 text-sm">Observación</span>
                            <p class="text-xs text-gray-400 mt-0.5">Área de observación</p>
                        </div>
                    </button>
                    @endif

                    @if($hasPermission('dar_alta'))
                    <button onclick="darAlta()" class="group flex items-center p-4 border border-gray-200 hover:border-gray-400 hover:bg-gray-50 transition-all text-left">
                        <div class="w-9 h-9 bg-gray-100 group-hover:bg-gray-200 flex items-center justify-center mr-3 transition-colors">
                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                        </div>
                        <div>
                            <span class="font-medium text-gray-700 text-sm">Dar de Alta</span>
                            <p class="text-xs text-gray-400 mt-0.5">Paciente egresado</p>
                        </div>
                    </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('js/auto-refresh.js') }}"></script>
<script>
    let emergencyIdActual = null;
    let autoRefresh = null;

    // Cargar emergencias al iniciar
    document.addEventListener('DOMContentLoaded', function() {
        cargarEstadisticas();
        iniciarAutoRefresh();
    });

    function iniciarAutoRefresh() {
        const filtro = document.getElementById('filtro-estado').value;
        
        autoRefresh = new AutoRefresh({
            interval: 5000,
            endpoint: '/emergency-staff/api/emergencias?estado=' + filtro,
            onData: (data) => {
                if (data.success) {
                    mostrarEmergencias(data.emergencias);
                }
            },
            onError: (err) => {
                console.warn('Error al actualizar datos:', err);
            }
        });
        
        autoRefresh.start();
    }

    function onFiltroChange() {
        // Reiniciar auto-refresh con el nuevo filtro
        if (autoRefresh) {
            autoRefresh.stop();
        }
        iniciarAutoRefresh();
    }

    async function cargarEmergencias() {
        // Función manual para refrescar (desde el botón)
        const filtro = document.getElementById('filtro-estado').value;
        
        try {
            const response = await fetch('/emergency-staff/api/emergencias?estado=' + filtro, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
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
            if (error.response) {
                const data = await error.response.json().catch(() => ({}));
                alert('Error al derivar paciente: ' + (data.message || error.message || 'Error desconocido'));
            } else {
                alert('Error al derivar paciente: ' + (error.message || 'Error de conexión'));
            }
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
            } else {
                alert('Error: ' + (data.message || 'No se pudo derivar'));
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error al forzar derivación: ' + (error.message || 'Error de conexión'));
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

    // Detener auto-refresh cuando el usuario sale de la página
    window.addEventListener('beforeunload', () => {
        if (autoRefresh) {
            autoRefresh.stop();
        }
    });
</script>
@endsection
