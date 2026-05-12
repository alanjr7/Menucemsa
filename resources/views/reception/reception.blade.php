@extends('layouts.app')

@section('content')
<div class="w-full p-6 bg-gray-50/50 min-h-screen">

        <!-- Header -->
        <div class="flex justify-between items-end mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Recepción</h1>
                <p class="text-sm text-gray-500">hola bienvenido {{ auth()->user()->name ?? 'Recepción' }} !</p>
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

        <!-- Botón de Acción Principal - Nuevo Ingreso -->
        <div class="mb-8">
            <a href="{{ route('reception.ingreso-general') }}" class="group relative bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold py-6 px-8 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 flex items-center justify-center w-full md:w-auto md:inline-flex">
                <div class="bg-white/20 p-3 rounded-full mr-4 group-hover:bg-white/30 transition-colors">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                </div>
                <div class="text-left">
                    <span class="text-xl block">Nuevo Ingreso</span>
                    <span class="text-xs opacity-90">Consulta, Emergencia o Internación</span>
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
                <span class="text-gray-500 text-sm font-medium mb-1">Admisiones</span>
                <span class="text-3xl font-bold text-blue-600" id="stat-admisiones">0</span>
            </div>
        </div>

        <!-- Tabs Navigation -->
        <div class="bg-gray-100/50 p-1.5 rounded-xl inline-flex w-full mb-6">
            <button id="btn-semanal" onclick="switchTab('semanal')" class="flex-1 px-4 py-2.5 bg-white text-gray-800 font-semibold rounded-lg shadow-sm text-center text-sm transition-all duration-200">Calendario Mensual</button>
            <button id="btn-llamadas" onclick="switchTab('llamadas')" class="flex-1 px-4 py-2.5 text-gray-600 hover:bg-gray-200/50 font-medium rounded-lg text-center text-sm transition-all duration-200">Gestión de Llamadas</button>
            <button id="btn-temporales" onclick="switchTab('temporales')" class="flex-1 px-4 py-2.5 text-gray-600 hover:bg-gray-200/50 font-medium rounded-lg text-center text-sm transition-all duration-200">Pacientes Temporales</button>
        </div>

        <!-- Container for Tab Content -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 min-h-[400px] overflow-hidden">

            <!-- TAB: CALENDARIO SEMANAL -->
            <div id="tab-semanal" class="block">
                <!-- Header del Calendario -->
                <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-indigo-50 to-purple-50">
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-indigo-100 rounded-xl flex items-center justify-center">
                                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">Calendario Mensual</h3>
                                <p class="text-sm text-gray-500">Vista mensual de citas programadas</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 bg-white p-1 rounded-xl shadow-sm border border-gray-200">
                            <button onclick="cambiarMes(-1)" class="p-2 hover:bg-gray-100 rounded-lg transition-colors" aria-label="Mes anterior">
                                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                </svg>
                            </button>
                            <span id="rango-semana" class="px-4 py-1.5 text-sm font-semibold text-gray-700 min-w-[140px] text-center"></span>
                            <button onclick="cambiarMes(1)" class="p-2 hover:bg-gray-100 rounded-lg transition-colors" aria-label="Mes siguiente">
                                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </button>
                        </div>
                        <button onclick="abrirModalNuevaCita()" class="sm:ml-auto bg-green-500 hover:bg-green-600 text-white text-xs font-medium px-3 py-1.5 rounded-lg shadow-sm transition-colors mt-3 sm:mt-0 flex items-center">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Nueva Cita
                        </button>
                    </div>
                </div>

                <!-- Grid del Calendario -->
                <div class="p-6">
                    <div id="calendario-semanal" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-7 border-t border-l border-gray-400 bg-white">
                        <!-- Calendario se genera dinámicamente -->
                    </div>
                </div>
            </div>

            <!-- TAB 3: GESTIÓN DE LLAMADAS -->
            <div id="tab-llamadas" class="hidden p-8">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Gestión de Llamadas y Confirmaciones</h3>
                        <p class="text-sm text-gray-500 mt-1">Todos los pacientes con citas programadas - Haga clic en "WhatsApp" para contactar</p>
                    </div>
                    <button onclick="cargarPendientesLlamada()" class="bg-blue-500 hover:bg-blue-600 text-white text-xs font-medium px-3 py-1.5 rounded-lg shadow-sm transition-colors flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Actualizar Lista
                    </button>
                </div>

                <div class="space-y-4 max-w-5xl" id="lista-llamadas">
                    <!-- Las llamadas se cargarán dinámicamente -->
                    <div class="p-8 text-center text-gray-500">
                        <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <p>Cargando pacientes con citas programadas...</p>
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
        <div class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
            <!-- Header del Modal -->
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white p-6 rounded-t-2xl">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-2xl font-bold">Nueva Cita</h3>
                        <p class="text-blue-100 text-sm mt-1">Programar cita médica - Paciente y cita en un solo paso</p>
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
                    <!-- SECCIÓN 1: BÚSQUEDA Y REGISTRO DE PACIENTE -->
                    <div class="mb-6">
                        <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            1. Datos del Paciente
                        </h4>
                        
                        <!-- Búsqueda de paciente -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Buscar por C.I.</label>
                            <div class="flex gap-2">
                                <input type="text" id="cita_ci_paciente" name="ci_paciente" placeholder="Ingrese CI del paciente" 
                                    class="flex-1 border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all">
                                <button type="button" onclick="buscarPacienteParaCita()" 
                                    class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-4 py-2.5 rounded-xl transition-colors text-sm flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                    Buscar
                                </button>
                            </div>
                            <div id="info-paciente-encontrado" class="hidden mt-3 p-3 bg-green-50 rounded-lg border border-green-200">
                                <div class="flex items-center text-green-800">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span id="nombre-paciente-encontrado"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Formulario de registro de paciente nuevo (colapsable) -->
                        <div id="form-paciente-nuevo" class="hidden bg-blue-50 rounded-xl p-4 border border-blue-100">
                            <h5 class="font-medium text-gray-800 mb-3 flex items-center">
                                <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                                </svg>
                                Registrar Nuevo Paciente
                            </h5>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombres *</label>
                                    <input type="text" id="paciente_nombres" name="nombres" placeholder="Nombres del paciente"
                                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Apellidos *</label>
                                    <input type="text" id="paciente_apellidos" name="apellidos" placeholder="Apellidos del paciente"
                                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Sexo *</label>
                                    <select id="paciente_sexo" name="sexo" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                        <option value="">Seleccione...</option>
                                        <option value="M">Masculino</option>
                                        <option value="F">Femenino</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Nacimiento *</label>
                                    <input type="date" id="paciente_fecha_nacimiento" name="fecha_nacimiento"
                                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Lugar de Expedición CI</label>
                                    <select id="paciente_lugar_expedicion" name="lugar_expedicion" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                        <option value="">Seleccione...</option>
                                        <option value="LP">LP - La Paz</option>
                                        <option value="OR">OR - Oruro</option>
                                        <option value="PT">PT - Potosí</option>
                                        <option value="CB">CB - Cochabamba</option>
                                        <option value="CH">CH - Chuquisaca</option>
                                        <option value="TJ">TJ - Tarija</option>
                                        <option value="PN">PN - Pando</option>
                                        <option value="BN">BN - Beni</option>
                                        <option value="SC">SC - Santa Cruz</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nacionalidad</label>
                                    <select id="paciente_nacionalidad" name="nacionalidad" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                        <option value="Boliviana" selected>Boliviana</option>
                                        <option value="Argentina">Argentina</option>
                                        <option value="Brasileña">Brasileña</option>
                                        <option value="Chilena">Chilena</option>
                                        <option value="Colombiana">Colombiana</option>
                                        <option value="Ecuatoriana">Ecuatoriana</option>
                                        <option value="Paraguaya">Paraguaya</option>
                                        <option value="Peruana">Peruana</option>
                                        <option value="Uruguaya">Uruguaya</option>
                                        <option value="Venezolana">Venezolana</option>
                                        <option value="Otra">Otra</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Estado Civil</label>
                                    <select id="paciente_estado_civil" name="estado_civil" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                        <option value="">Seleccione...</option>
                                        <option value="Soltero/a">Soltero/a</option>
                                        <option value="Casado/a">Casado/a</option>
                                        <option value="Divorciado/a">Divorciado/a</option>
                                        <option value="Viudo/a">Viudo/a</option>
                                        <option value="Unión Libre">Unión Libre</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                                    <input type="tel" id="paciente_telefono" name="telefono" placeholder="Ej: 0414-1234567"
                                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Correo</label>
                                    <input type="email" id="paciente_correo" name="correo" placeholder="correo@ejemplo.com"
                                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Profesión</label>
                                    <input type="text" id="paciente_profesion" name="profesion" placeholder="Profesión u oficio"
                                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Empresa de Trabajo</label>
                                    <input type="text" id="paciente_empresa_trabajo" name="empresa_trabajo" placeholder="Nombre de la empresa"
                                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Dirección</label>
                                    <textarea id="paciente_direccion" name="direccion" rows="2" placeholder="Dirección completa del paciente"
                                        class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECCIÓN 2: GARANTE (OPCIONAL) -->
                    <div class="mb-6">
                        <div class="flex items-center mb-4">
                            <input type="checkbox" id="requiere_garante" name="requiere_garante" onchange="toggleGaranteSection()" 
                                class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <label for="requiere_garante" class="ml-2 text-sm font-medium text-gray-700">
                                ¿Requiere garante?
                            </label>
                        </div>
                        
                        <div id="section-garante" class="hidden bg-purple-50 rounded-xl p-4 border border-purple-100">
                            <h5 class="font-medium text-gray-800 mb-3 flex items-center">
                                <svg class="w-4 h-4 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                                Datos del Garante
                            </h5>
                            
                            <!-- Búsqueda de garante -->
                            <div class="mb-3">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Buscar garante por CI</label>
                                <div class="flex gap-2">
                                    <input type="text" id="garante_ci" name="garante_ci_busqueda" placeholder="CI del garante"
                                        class="flex-1 border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-100">
                                    <button type="button" onclick="buscarGaranteParaCita()"
                                        class="bg-purple-600 hover:bg-purple-700 text-white font-medium px-3 py-2 rounded-lg transition-colors text-sm">
                                        Buscar
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Info garante encontrado -->
                            <div id="info-garante" class="hidden p-3 bg-white rounded-lg border border-purple-200 mb-3">
                                <div class="flex justify-between items-center">
                                    <p class="text-sm text-gray-600">Garante: <span id="nombre-garante" class="font-medium"></span></p>
                                    <button type="button" onclick="limpiarGarante()" class="text-xs text-red-500 hover:text-red-700">Cambiar</button>
                                </div>
                            </div>
                            
                            <!-- Mensaje: garante no encontrado -->
                            <div id="garante-no-encontrado" class="hidden p-3 bg-yellow-50 rounded-lg border border-yellow-200 mb-3">
                                <p class="text-sm text-yellow-800 mb-2">Garante no encontrado. Puede registrar uno nuevo:</p>
                                <button type="button" onclick="mostrarFormularioGaranteNuevo()" 
                                    class="bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-medium px-3 py-1.5 rounded-lg transition-colors">
                                    + Registrar Nuevo Garante
                                </button>
                            </div>
                            
                            <!-- Formulario de registro de garante nuevo -->
                            <div id="form-garante-nuevo" class="hidden bg-white rounded-lg p-3 border border-purple-200">
                                <h6 class="font-medium text-gray-700 mb-2 text-sm">Registrar Nuevo Garante</h6>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">CI *</label>
                                        <input type="text" id="garante_nuevo_ci" name="garante_nuevo_ci" placeholder="CI del garante"
                                            class="w-full border border-gray-200 rounded px-2 py-1.5 text-sm bg-white focus:outline-none focus:border-purple-500">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Nombres *</label>
                                        <input type="text" id="garante_nuevo_nombres" name="garante_nuevo_nombres" placeholder="Nombres"
                                            class="w-full border border-gray-200 rounded px-2 py-1.5 text-sm bg-white focus:outline-none focus:border-purple-500">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Apellidos *</label>
                                        <input type="text" id="garante_nuevo_apellidos" name="garante_nuevo_apellidos" placeholder="Apellidos"
                                            class="w-full border border-gray-200 rounded px-2 py-1.5 text-sm bg-white focus:outline-none focus:border-purple-500">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Sexo *</label>
                                        <select id="garante_nuevo_sexo" name="garante_nuevo_sexo" class="w-full border border-gray-200 rounded px-2 py-1.5 text-sm bg-white focus:outline-none focus:border-purple-500">
                                            <option value="">Seleccione...</option>
                                            <option value="M">Masculino</option>
                                            <option value="F">Femenino</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Fecha Nac. *</label>
                                        <input type="date" id="garante_nuevo_fecha_nac" name="garante_nuevo_fecha_nac"
                                            class="w-full border border-gray-200 rounded px-2 py-1.5 text-sm bg-white focus:outline-none focus:border-purple-500">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Teléfono</label>
                                        <input type="tel" id="garante_nuevo_telefono" name="garante_nuevo_telefono" placeholder="Teléfono"
                                            class="w-full border border-gray-200 rounded px-2 py-1.5 text-sm bg-white focus:outline-none focus:border-purple-500">
                                    </div>
                                    <div class="md:col-span-2">
                                        <button type="button" onclick="registrarGaranteNuevo()" 
                                            class="w-full bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium px-3 py-2 rounded-lg transition-colors">
                                            Guardar Garante
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <input type="hidden" id="garante_ci_guardado" name="id_garante_referencia">
                        </div>
                    </div>

                    <!-- SECCIÓN 3: DATOS DE LA CITA -->
                    <div class="mb-6">
                        <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            2. Datos de la Cita
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Tipo de Ingreso -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Ingreso *</label>
                                <select name="tipo_ingreso" id="cita_tipo_ingreso"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100" required>
                                    <option value="">Seleccione el tipo...</option>
                                    <option value="consulta_externa">🩺 Consulta Externa</option>
                                    <option value="internacion">🛏️ Internación</option>
                                    <option value="emergencia">🚨 Emergencia</option>
                                    <option value="enfermeria">💉 Enfermería</option>
                                </select>
                                <p class="text-xs text-gray-400 mt-1">Se generará una cuenta de cobro con el monto de ingreso correspondiente</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha *</label>
                                <input type="date" name="fecha" onchange="cargarMedicosDisponibles()" min="{{ date('Y-m-d') }}"
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Hora *</label>
                                <input type="time" name="hora" onchange="cargarMedicosDisponibles()" 
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Especialidad *</label>
                                <select name="codigo_especialidad" onchange="cargarMedicosDisponibles()" 
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100" required>
                                    <option value="">Seleccione...</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Médico *</label>
                                <select name="ci_medico" 
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100" required>
                                    <option value="">Seleccione especialidad primero</option>
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Motivo de Consulta *</label>
                                <input type="text" id="cita_motivo" name="motivo" placeholder="Motivo de la consulta" 
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100" required>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Observaciones</label>
                                <textarea id="cita_observaciones" name="observaciones" rows="2" placeholder="Notas adicionales" 
                                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Botones de Acción -->
                    <div class="flex justify-end gap-3 pt-6 border-t border-gray-200">
                        <button type="button" onclick="cerrarModalNuevaCita()" class="px-6 py-2.5 border border-gray-200 rounded-xl text-gray-700 font-medium hover:bg-gray-50 transition-colors text-sm">
                            Cancelar
                        </button>
                        <button type="submit" id="btn-crear-cita" class="px-6 py-2.5 bg-blue-600 text-white rounded-xl font-medium hover:bg-blue-700 transition-colors flex items-center text-sm shadow-md">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            <span id="texto-btn-cita">Crear Cita</span>
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
            cargarAgendaMensual();
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

        // Función para cargar pendientes de llamada
        async function cargarPendientesLlamada() {
            try {
                const response = await fetch('/api/llamadas-pendientes');
                const data = await response.json();
                
                if (data.success) {
                    mostrarLlamadas(data.hoy, data.manana, data.futuras);
                }
            } catch (error) {
                console.error('Error al cargar llamadas pendientes:', error);
            }
        }

        // Función para mostrar llamadas pendientes
        function mostrarLlamadas(hoy, manana, futuras) {
            const listaLlamadas = document.getElementById('lista-llamadas');
            
            if (hoy.length === 0 && manana.length === 0 && futuras.length === 0) {
                listaLlamadas.innerHTML = `
                    <div class="p-12 text-center text-gray-500 bg-white border border-gray-100 shadow-sm rounded-2xl flex flex-col items-center">
                        <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4 border border-gray-100">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <h4 class="text-lg font-bold text-gray-800 mb-1">No hay citas programadas</h4>
                        <p class="text-sm text-gray-500">No hay llamadas pendientes para gestionar en este momento.</p>
                    </div>
                `;
                return;
            }
            
            let html = '';
            
            // Mostrar citas de hoy
            if (hoy.length > 0) {
                html += `
                <div class="mb-8">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="flex items-center justify-center w-8 h-8 rounded-full bg-orange-100 text-orange-600 shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <h4 class="text-lg font-bold text-gray-900">Citas de Hoy</h4>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-orange-50 text-orange-700 border border-orange-200">${hoy.length} pendientes</span>
                    </div>
                    <div class="space-y-3">`;
                hoy.forEach(cita => {
                    html += crearTarjetaLlamada(cita, 'hoy');
                });
                html += '</div></div>';
            }
            
            // Mostrar citas de mañana
            if (manana.length > 0) {
                html += `
                <div class="mb-8">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 text-blue-600 shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <h4 class="text-lg font-bold text-gray-900">Citas de Mañana</h4>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">${manana.length} pendientes</span>
                    </div>
                    <div class="space-y-3">`;
                manana.forEach(cita => {
                    html += crearTarjetaLlamada(cita, 'manana');
                });
                html += '</div></div>';
            }
            
            // Mostrar citas futuras
            if (futuras.length > 0) {
                html += `
                <div class="mb-8">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="flex items-center justify-center w-8 h-8 rounded-full bg-purple-100 text-purple-600 shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <h4 class="text-lg font-bold text-gray-900">Citas Futuras</h4>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-purple-50 text-purple-700 border border-purple-200">${futuras.length} programadas</span>
                    </div>
                    <div class="space-y-3">`;
                futuras.forEach(cita => {
                    html += crearTarjetaLlamada(cita, 'futura');
                });
                html += '</div></div>';
            }
            
            listaLlamadas.innerHTML = html;
        }

        // Función para crear tarjeta de llamada
        function crearTarjetaLlamada(cita, tipo) {
            const telefono = cita.paciente?.telefono || 'Sin teléfono';
            const tieneTelefono = telefono !== 'Sin teléfono';
            
            // Limpiar número para WhatsApp (quitar espacios, paréntesis, guiones)
            const telefonoLimpio = tieneTelefono ? telefono.replace(/[\s\(\)\-]/g, '') : '';
            const whatsappUrl = tieneTelefono ? `https://wa.me/${telefonoLimpio}` : '#';
            
            // Formatear fecha para mostrar
            const fechaObj = new Date(cita.fecha);
            const fechaFormateada = fechaObj.toLocaleDateString('es-ES', { 
                weekday: 'long', 
                day: 'numeric', 
                month: 'short' 
            });
            const diaCorto = tipo === 'hoy' ? 'HOY' : tipo === 'manana' ? 'MAÑ.' : fechaObj.toLocaleDateString('es-ES', { weekday: 'short' }).toUpperCase();
            const fechaTextoCompleto = tipo === 'hoy' ? 'Hoy' : tipo === 'manana' ? 'Mañana' : fechaFormateada;
            
            return `
                <div class="group relative bg-white border border-gray-200 shadow-sm hover:shadow-lg hover:border-blue-200 rounded-2xl p-5 transition-all duration-300">
                    <div class="flex flex-col sm:flex-row gap-5 items-start sm:items-center">
                        
                        <!-- Date/Time Badge -->
                        <div class="flex-shrink-0 flex flex-col items-center justify-center w-20 h-20 bg-gradient-to-b from-blue-50 to-blue-100/50 rounded-2xl border border-blue-100 shadow-inner relative overflow-hidden group-hover:scale-105 transition-transform duration-300">
                            <div class="absolute top-0 w-full h-1.5 bg-blue-500"></div>
                            <span class="text-[10px] font-bold text-blue-600 uppercase tracking-widest mt-1">${diaCorto}</span>
                            <span class="text-2xl font-black text-gray-800 tracking-tight leading-none mt-1">${cita.hora.substring(0,5)}</span>
                        </div>

                        <!-- Patient Info -->
                        <div class="flex-1 min-w-0 flex flex-col justify-center">
                            <div class="flex flex-wrap items-center gap-2 mb-1.5">
                                <h3 class="text-lg font-bold text-gray-900 truncate">${cita.paciente?.nombre || 'N/A'}</h3>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-gray-100 text-gray-600 border border-gray-200 uppercase tracking-wider">
                                    CI: ${cita.paciente?.ci || 'N/A'}
                                </span>
                            </div>
                            
                            <div class="flex flex-wrap items-center text-sm text-gray-600 mb-3 gap-y-1 gap-x-4">
                                <div class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    <span class="font-medium">Dr. ${cita.medico?.usuario?.name || cita.medico?.user?.name || 'N/A'}</span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                                    <span>${cita.especialidad?.nombre || 'N/A'}</span>
                                </div>
                                <div class="flex items-center gap-1.5 text-gray-500">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    <span class="capitalize">${fechaTextoCompleto}</span>
                                </div>
                            </div>

                            <div class="flex items-center gap-3">
                                <div class="flex items-center gap-2 px-3 py-1.5 bg-green-50/80 border border-green-200/60 rounded-lg">
                                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                    <span class="text-sm font-bold text-green-800">${telefono}</span>
                                </div>
                                ${tieneTelefono ? `
                                <a href="${whatsappUrl}" target="_blank" class="inline-flex items-center justify-center px-3 py-1.5 rounded-lg bg-[#25D366] text-white hover:bg-[#128C7E] hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200" title="Contactar por WhatsApp">
                                    <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                                    </svg>
                                    <span class="text-sm font-semibold tracking-wide">WhatsApp</span>
                                </a>` : '<span class="ml-2 text-xs font-medium text-gray-400 bg-gray-100 px-2 py-1 rounded-md">Sin teléfono</span>'}
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex sm:flex-col items-center gap-2 w-full sm:w-auto mt-4 sm:mt-0 pt-4 sm:pt-0 border-t sm:border-t-0 sm:border-l border-gray-100 sm:pl-5">
                            <button onclick="marcarAsistidaDesdeLlamadas(${cita.id})" class="flex-1 sm:flex-none flex items-center justify-center gap-1.5 px-4 py-2.5 w-full bg-blue-600 text-white hover:bg-blue-700 hover:shadow-lg hover:-translate-y-0.5 rounded-xl transition-all duration-200 text-sm font-bold shadow-md mt-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Asistió ✓
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

        // Función para marcar asistida desde el tab de Gestión de Llamadas
        async function marcarAsistidaDesdeLlamadas(citaId) {
            if (!confirm('¿Confirmar que el paciente asistió a la cita? Esto generará los registros necesarios y el paciente aparecerá en el listado de pacientes.')) return;
            try {
                const response = await fetch(`/api/cita/${citaId}/asistida`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                const data = await response.json();
                if (data.success) {
                    alert('✅ ' + data.message);
                    cargarPendientesLlamada();
                    cargarEstadisticasDashboard();
                    if (typeof cargarAgendaMensual === 'function') {
                        cargarAgendaMensual();
                    }
                } else {
                    alert('Error: ' + data.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error al marcar asistencia');
            }
        }



        // Funciones para modal de nueva cita
        function abrirModalNuevaCita() {
            document.getElementById('modalNuevaCita').classList.remove('hidden');
            document.getElementById('modalNuevaCita').classList.add('flex');
            
            // Asegurar que los campos de motivo y observaciones estén habilitados
            const motivoField = document.getElementById('cita_motivo');
            const observacionesField = document.getElementById('cita_observaciones');
            if (motivoField) {
                motivoField.disabled = false;
                motivoField.classList.remove('bg-gray-100');
                motivoField.classList.add('bg-white');
            }
            if (observacionesField) {
                observacionesField.disabled = false;
                observacionesField.classList.remove('bg-gray-100');
                observacionesField.classList.add('bg-white');
            }
        }

        function cerrarModalNuevaCita() {
            document.getElementById('modalNuevaCita').classList.add('hidden');
            document.getElementById('modalNuevaCita').classList.remove('flex');
            document.getElementById('formNuevaCita').reset();
            // Limpiar estado
            pacienteExistente = false;
            const infoPaciente = document.getElementById('info-paciente-encontrado');
            if (infoPaciente) infoPaciente.classList.add('hidden');
            const formPaciente = document.getElementById('form-paciente-nuevo');
            if (formPaciente) formPaciente.classList.add('hidden');
            const sectionGarante = document.getElementById('section-garante');
            if (sectionGarante) sectionGarante.classList.add('hidden');
            const infoGarante = document.getElementById('info-garante');
            if (infoGarante) infoGarante.classList.add('hidden');
            const garanteNoEncontrado = document.getElementById('garante-no-encontrado');
            if (garanteNoEncontrado) garanteNoEncontrado.classList.add('hidden');
            const formGaranteNuevo = document.getElementById('form-garante-nuevo');
            if (formGaranteNuevo) formGaranteNuevo.classList.add('hidden');
            const checkboxGarante = document.getElementById('requiere_garante');
            if (checkboxGarante) checkboxGarante.checked = false;
            document.getElementById('texto-btn-cita').textContent = 'Crear Cita';
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
                        select.innerHTML += `<option value="${medico.ci}">Dr. ${medico.nombre}</option>`;
                    });
                }
            } catch (error) {
                console.error('Error al cargar médicos:', error);
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

        // VARIABLES PARA CALENDARIO MENSUAL
        let mesActual = new Date();

        // Función para cargar calendario mensual
        async function cargarAgendaMensual() {
            const inicioMes = new Date(mesActual.getFullYear(), mesActual.getMonth(), 1);
            const ultimoDia = new Date(mesActual.getFullYear(), mesActual.getMonth() + 1, 0);

            const fechaInicio = inicioMes.toISOString().split('T')[0];
            const fechaFin = ultimoDia.toISOString().split('T')[0];

            // Actualizar rango mostrado
            const opciones = { month: 'long', year: 'numeric' };
            const mesStr = mesActual.toLocaleDateString('es-ES', opciones);
            document.getElementById('rango-semana').textContent = mesStr.charAt(0).toUpperCase() + mesStr.slice(1);

            try {
                // Reuse the same endpoint since it takes any range
                const response = await fetch(`/api/agenda-semanal?fecha_inicio=${fechaInicio}&fecha_fin=${fechaFin}`);
                const data = await response.json();
                
                if (data.success) {
                    mostrarCalendarioMensual(data.citas_por_dia, inicioMes, ultimoDia);
                }
            } catch (error) {
                console.error('Error al cargar agenda mensual:', error);
            }
        }

        function mostrarCalendarioMensual(citasPorDia, fechaInicio, ultimoDia) {
            const contenedor = document.getElementById('calendario-semanal');
            const diasSemanaNombres = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];

            let html = '';
            const cantidadDias = ultimoDia.getDate();
            const diaSemanaInicio = fechaInicio.getDay() === 0 ? 6 : fechaInicio.getDay() - 1; // 0 para lunes, etc

            // Agregar cuadros vacíos para el offset del primer día del mes
            for (let i = 0; i < diaSemanaInicio; i++) {
                html += `<div class="bg-gray-50/50 border-r border-b border-gray-400"></div>`;
            }

            for (let i = 0; i < cantidadDias; i++) {
                const fecha = new Date(fechaInicio);
                fecha.setDate(fechaInicio.getDate() + i);
                const fechaStr = fecha.toISOString().split('T')[0];
                const citasDia = citasPorDia[fechaStr] || [];
                const esHoy = fecha.toDateString() === new Date().toDateString();
                const diaSemana = fecha.getDay();
                const esFinDeSemana = diaSemana === 0 || diaSemana === 6;

                // Configuracion de colores por estado con semantica clara
                const coloresEstado = {
                    'programado':   { bg: 'bg-amber-50',   border: 'border-amber-200',   text: 'text-amber-700',   badge: 'bg-amber-100 text-amber-800' },
                    'confirmado':   { bg: 'bg-emerald-50', border: 'border-emerald-200', text: 'text-emerald-700', badge: 'bg-emerald-100 text-emerald-800' },
                    'en_atencion':  { bg: 'bg-blue-50',    border: 'border-blue-200',    text: 'text-blue-700',    badge: 'bg-blue-100 text-blue-800' },
                    'atendido':     { bg: 'bg-slate-50',   border: 'border-slate-200',   text: 'text-slate-700',   badge: 'bg-slate-100 text-slate-800' },
                    'cancelado':    { bg: 'bg-rose-50',    border: 'border-rose-200',    text: 'text-rose-700',    badge: 'bg-rose-100 text-rose-800' }
                };

                const estadoDia = esHoy ? 'bg-indigo-50/30 ring-1 ring-inset ring-indigo-400' : esFinDeSemana ? 'bg-gray-50/50' : 'bg-white';
                const headerBg = esHoy ? 'text-indigo-700 font-bold' : esFinDeSemana ? 'text-gray-500 font-medium' : 'text-gray-700 font-medium';

                html += `
                    <div class="border-r border-b border-gray-400 transition-colors duration-200 hover:bg-gray-50 ${estadoDia} flex flex-col h-32 md:h-40 group relative">
                        <!-- Header del dia minimalista -->
                        <div class="px-2 py-1.5 flex justify-between items-center ${headerBg}">
                            <div class="text-[10px] uppercase tracking-wide opacity-70">${diasSemanaNombres[diaSemana]}</div>
                            <div class="text-sm">${fecha.getDate()}</div>
                        </div>

                        <!-- Lista de citas compacta -->
                        <div class="px-1.5 pb-1.5 space-y-1 overflow-y-auto flex-1 custom-scrollbar">
                            ${citasDia.map(cita => {
                                const estilo = coloresEstado[cita.estado] || coloresEstado['programado'];
                                const hora = cita.hora_formateada || cita.hora?.substring(0, 5) || '--:--';
                                const paciente = cita.paciente?.nombre || 'Sin nombre';

                                return `
                                <div class="px-1.5 py-1 rounded border-l-2 ${estilo.border} ${estilo.bg} cursor-pointer hover:opacity-80 transition-opacity"
                                     onclick="verDetalleCita(${cita.id})" title="${paciente}">
                                    <div class="flex items-center justify-between">
                                        <span class="text-[10px] font-semibold ${estilo.text}">${hora}</span>
                                        <span class="text-[9px] text-gray-600 truncate ml-1">${paciente.split(' ')[0]}</span>
                                    </div>
                                </div>
                                `;
                            }).join('')}

                            <!-- Estado vacio compacto -->
                            ${citasDia.length === 0 ? `
                            <div class="absolute inset-0 flex flex-col items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none">
                                <button onclick="abrirModalNuevaCitaParaFecha('${fechaStr}')"
                                        class="text-[11px] font-medium text-gray-500 bg-white/80 backdrop-blur-sm px-2 py-1 rounded shadow-sm hover:text-indigo-600 hover:bg-white transition-colors pointer-events-auto">
                                    + Añadir
                                </button>
                            </div>
                            ` : ''}
                        </div>
                    </div>
                `;
            }

            contenedor.innerHTML = html;
        }

        // Funcion para abrir modal de nueva cita con fecha preseleccionada
        function abrirModalNuevaCitaParaFecha(fecha) {
            abrirModalNuevaCita();
            document.querySelector('input[name="fecha"]').value = fecha;
        }

        function cambiarMes(direccion) {
            mesActual.setMonth(mesActual.getMonth() + direccion);
            cargarAgendaMensual();
        }

        function verDetalleCita(citaId) {
            window.location.href = '/reception/citas/' + citaId + '/edit';
        }

        // Funciones para buscar paciente y mostrar formulario de registro
        let pacienteExistente = false;

        async function buscarPacienteParaCita() {
            const ci = document.getElementById('cita_ci_paciente').value;
            
            if (!ci || ci.length < 3) {
                alert('Ingrese un CI válido (mínimo 3 caracteres)');
                return;
            }
            
            try {
                const response = await fetch(`/reception/ingreso-general/buscar-paciente?ci=${encodeURIComponent(ci)}`);
                const data = await response.json();

                if (data.success) {
                    // Paciente encontrado
                    pacienteExistente = true;
                    document.getElementById('info-paciente-encontrado').classList.remove('hidden');
                    document.getElementById('nombre-paciente-encontrado').textContent = data.paciente.nombre;
                    document.getElementById('form-paciente-nuevo').classList.add('hidden');
                    document.getElementById('texto-btn-cita').textContent = 'Crear Cita';
                } else {
                    // Paciente no encontrado - mostrar formulario de registro
                    pacienteExistente = false;
                    document.getElementById('info-paciente-encontrado').classList.add('hidden');
                    document.getElementById('form-paciente-nuevo').classList.remove('hidden');
                    document.getElementById('texto-btn-cita').textContent = 'Registrar Paciente y Crear Cita';
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error al buscar paciente');
            }
        }

        // Funciones para garantes
        function toggleGaranteSection() {
            const checkbox = document.getElementById('requiere_garante');
            const section = document.getElementById('section-garante');
            if (checkbox.checked) {
                section.classList.remove('hidden');
            } else {
                section.classList.add('hidden');
                // Limpiar todos los campos de garante
                document.getElementById('garante_ci').value = '';
                document.getElementById('garante_ci_guardado').value = '';
                document.getElementById('info-garante').classList.add('hidden');
                document.getElementById('garante-no-encontrado').classList.add('hidden');
                document.getElementById('form-garante-nuevo').classList.add('hidden');
                // Limpiar campos del formulario de garante nuevo
                document.getElementById('garante_nuevo_ci').value = '';
                document.getElementById('garante_nuevo_nombres').value = '';
                document.getElementById('garante_nuevo_apellidos').value = '';
                document.getElementById('garante_nuevo_sexo').value = '';
                document.getElementById('garante_nuevo_fecha_nac').value = '';
                document.getElementById('garante_nuevo_telefono').value = '';
            }
        }

        async function buscarGaranteParaCita() {
            const ci = document.getElementById('garante_ci').value;
            
            if (!ci || ci.length < 3) {
                alert('Ingrese un CI válido');
                return;
            }
            
            try {
                const response = await fetch(`/api/buscar-garante?ci=${ci}`);
                const data = await response.json();
                
                if (data.success) {
                    // Garante encontrado
                    document.getElementById('info-garante').classList.remove('hidden');
                    document.getElementById('nombre-garante').textContent = data.garante.nombre;
                    document.getElementById('garante_ci_guardado').value = data.garante.ci;
                    document.getElementById('garante-no-encontrado').classList.add('hidden');
                    document.getElementById('form-garante-nuevo').classList.add('hidden');
                } else {
                    // Garante no encontrado - mostrar opción de registro
                    document.getElementById('info-garante').classList.add('hidden');
                    document.getElementById('garante-no-encontrado').classList.remove('hidden');
                    document.getElementById('garante_ci_guardado').value = '';
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error al buscar garante');
            }
        }

        function limpiarGarante() {
            document.getElementById('info-garante').classList.add('hidden');
            document.getElementById('garante_ci').value = '';
            document.getElementById('garante_ci_guardado').value = '';
        }

        function mostrarFormularioGaranteNuevo() {
            const ciBuscado = document.getElementById('garante_ci').value;
            document.getElementById('garante_nuevo_ci').value = ciBuscado;
            document.getElementById('form-garante-nuevo').classList.remove('hidden');
            document.getElementById('garante-no-encontrado').classList.add('hidden');
        }

        async function registrarGaranteNuevo() {
            const ci = document.getElementById('garante_nuevo_ci').value;
            const nombres = document.getElementById('garante_nuevo_nombres').value;
            const apellidos = document.getElementById('garante_nuevo_apellidos').value;
            const sexo = document.getElementById('garante_nuevo_sexo').value;
            const fechaNac = document.getElementById('garante_nuevo_fecha_nac').value;
            const telefono = document.getElementById('garante_nuevo_telefono').value;
            
            if (!ci || !nombres || !apellidos || !sexo || !fechaNac) {
                alert('Complete todos los campos obligatorios del garante');
                return;
            }
            
            try {
                const response = await fetch('/api/registrar-garante', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        ci: ci,
                        nombres: nombres,
                        apellidos: apellidos,
                        sexo: sexo,
                        fecha_nacimiento: fechaNac,
                        telefono: telefono || null
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    alert('Garante registrado exitosamente');
                    document.getElementById('info-garante').classList.remove('hidden');
                    document.getElementById('nombre-garante').textContent = data.garante.nombre;
                    document.getElementById('garante_ci_guardado').value = data.garante.ci;
                    document.getElementById('form-garante-nuevo').classList.add('hidden');
                } else {
                    alert('Error al registrar garante: ' + data.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error al registrar garante');
            }
        }

        // Función crearNuevaCita - Registra paciente completo y crea cita
        async function crearNuevaCita() {
            event.preventDefault();
            
            const form = document.getElementById('formNuevaCita');
            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());
            
            // Validar CI de paciente
            if (!data.ci_paciente || data.ci_paciente.length < 3) {
                alert('Ingrese un CI válido para el paciente');
                return;
            }
            
            // Si no es paciente existente, validar datos de registro completos
            if (!pacienteExistente) {
                if (!data.nombres || !data.apellidos || !data.sexo || !data.fecha_nacimiento) {
                    alert('Complete todos los datos obligatorios del paciente (nombres, apellidos, sexo, fecha de nacimiento)');
                    return;
                }
            }
            
            // Validar datos de cita
            if (!data.fecha || !data.hora || !data.codigo_especialidad || !data.ci_medico || !data.motivo || !data.tipo_ingreso) {
                alert('Complete todos los datos de la cita (incluyendo el tipo de ingreso)');
                return;
            }
            
            const btn = document.getElementById('btn-crear-cita');
            const textoOriginal = document.getElementById('texto-btn-cita').textContent;
            btn.disabled = true;
            document.getElementById('texto-btn-cita').textContent = 'Procesando...';
            
            try {
                // Si es paciente nuevo, primero registrarlo con TODOS los campos
                if (!pacienteExistente) {
                    const registroResponse = await fetch('/api/registrar-paciente-cita', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            ci: data.ci_paciente,
                            nombres: data.nombres,
                            apellidos: data.apellidos,
                            sexo: data.sexo,
                            fecha_nacimiento: data.fecha_nacimiento,
                            lugar_expedicion: data.lugar_expedicion || null,
                            nacionalidad: data.nacionalidad || 'Boliviana',
                            estado_civil: data.estado_civil || null,
                            telefono: data.telefono || null,
                            correo: data.correo || null,
                            direccion: data.direccion || null,
                            profesion: data.profesion || null,
                            empresa_trabajo: data.empresa_trabajo || null
                        })
                    });
                    
                    const registroData = await registroResponse.json();
                    if (!registroData.success) {
                        alert('Error al registrar paciente: ' + registroData.message);
                        btn.disabled = false;
                        document.getElementById('texto-btn-cita').textContent = textoOriginal;
                        return;
                    }
                }
                
                // Crear la cita
                const citaData = {
                    ci_paciente: data.ci_paciente,
                    ci_medico: data.ci_medico,
                    codigo_especialidad: data.codigo_especialidad,
                    fecha: data.fecha,
                    hora: data.hora,
                    motivo: data.motivo,
                    tipo_ingreso: data.tipo_ingreso,
                    observaciones: data.observaciones || '',
                    id_garante_referencia: data.id_garante_referencia || null
                };
                
                const response = await fetch('/api/nueva-cita', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(citaData)
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert(pacienteExistente ? 'Cita creada exitosamente' : 'Paciente registrado y cita creada exitosamente');
                    cerrarModalNuevaCita();
                    cargarAgendaMensual();
                    cargarEstadisticasDashboard();
                    // Resetear estado
                    pacienteExistente = false;
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error al procesar la solicitud');
            } finally {
                btn.disabled = false;
                document.getElementById('texto-btn-cita').textContent = textoOriginal;
            }
        }

        // Actualizar función switchTab para incluir calendario semanal
        function switchTab(tabName) {
            // Ocultar todos los contenidos
            ['semanal', 'llamadas', 'temporales'].forEach(tab => {
                const el = document.getElementById(`tab-${tab}`);
                if (el) {
                    el.classList.add('hidden');
                    el.classList.remove('block');
                }
            });

            // Mostrar contenido seleccionado
            const selectedEl = document.getElementById(`tab-${tabName}`);
            if (selectedEl) {
                selectedEl.classList.remove('hidden');
                selectedEl.classList.add('block');
            }

            // Actualizar clases de botones
            const btnClassesActive = 'bg-white text-gray-800 font-semibold rounded-lg shadow-sm';
            const btnClassesInactive = 'text-gray-600 hover:bg-gray-200/50 font-medium rounded-lg';
            
            ['semanal', 'llamadas', 'temporales'].forEach(tab => {
                const btn = document.getElementById(`btn-${tab}`);
                if (btn) {
                    if (tab === tabName) {
                        btn.className = `flex-1 px-4 py-2.5 ${btnClassesActive} text-center text-sm transition-all duration-200`;
                    } else {
                        btn.className = `flex-1 px-4 py-2.5 ${btnClassesInactive} text-center text-sm transition-all duration-200`;
                    }
                }
            });

            // Cargar datos específicos según el tab
            if (tabName === 'semanal') {
                cargarAgendaMensual();
            } else if (tabName === 'temporales') {
                cargarPacientesTemporales();
            } else if (tabName === 'llamadas') {
                cargarPendientesLlamada();
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
                                <option value="M">Masculino</option>
                                <option value="F">Femenino</option>
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