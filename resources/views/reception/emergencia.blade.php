@extends('layouts.app')
@section('content')
    <div class="p-6 bg-gray-50/50 min-h-screen">

        <!-- Header -->
        <div class="flex justify-between items-end mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Ingreso a Emergencia</h1>
                <p class="text-sm text-gray-500">Recepción - Registro de Pacientes de Emergencia</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('reception') }}" class="flex items-center px-4 py-2 bg-white border border-gray-200 text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition-all shadow-sm">
                    <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Volver a Recepción
                </a>
            </div>
        </div>

        <!-- Alerta de Emergencia -->
        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-lg">
            <div class="flex items-center">
                <svg class="w-6 h-6 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <div>
                    <h3 class="text-red-800 font-bold">MÓDULO DE EMERGENCIA</h3>
                    <p class="text-red-700 text-sm">Registro de ingreso para emergencias médicas</p>
                </div>
            </div>
        </div>

        <!-- Formulario de Emergencia -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-bold text-gray-800 mb-6">Registrar Ingreso a Emergencia</h2>
            
            <form id="formEmergencia" onsubmit="registrarEmergencia(event); return false;">
                @csrf
                
                <!-- Sección 1: Identificación del Paciente -->
                <div class="mb-6">
                    <h3 class="text-md font-semibold text-gray-700 mb-4 pb-2 border-b border-gray-200">1. Identificación del Paciente</h3>
                    
                    <!-- Opción ID Temporal -->
                    <div class="mb-4 flex items-center">
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="hidden" name="usar_temp_id" value="0">
                            <input type="checkbox" id="usar_temp_id" name="usar_temp_id" value="1" class="sr-only peer" onchange="toggleTempId()">
                            <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-red-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-red-600"></div>
                            <span class="ms-3 text-sm font-medium text-gray-700">Usar ID Temporal (Paciente sin documento)</span>
                        </label>
                    </div>

                    <!-- Campo CI -->
                    <div class="mb-4" id="ci_container">
                        <label class="block text-sm font-medium text-gray-700 mb-2">C.I. Paciente *</label>
                        <div class="flex gap-3">
                            <input type="text" id="paciente_ci" name="ci" placeholder="Número de CI del paciente" class="flex-1 border border-gray-200 rounded-xl px-4 py-3 text-sm bg-white focus:outline-none focus:border-red-500 focus:ring-2 focus:ring-red-100 transition-all" required>
                            <button type="button" onclick="buscarPaciente()" class="bg-red-600 hover:bg-red-700 text-white font-medium px-6 py-3 rounded-xl transition-colors text-sm">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                Buscar
                            </button>
                        </div>
                    </div>

                    <!-- Campo ID Temporal -->
                    <div class="mb-4 hidden" id="temp_id_container">
                        <label class="block text-sm font-medium text-gray-700 mb-2">ID Temporal *</label>
                        <input type="text" id="temp_id" name="temp_id" placeholder="Ej: TEMP-001" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm bg-white focus:outline-none focus:border-red-500 focus:ring-2 focus:ring-red-100 transition-all">
                        <p class="text-xs text-gray-500 mt-1">Se generará automáticamente si se deja vacío</p>
                    </div>

                    <!-- Tipo de Paciente -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Paciente</label>
                        <!-- Campo hidden que se envía al servidor -->
                        <input type="hidden" name="tipo_paciente" id="tipo_paciente_hidden" value="existente">
                        <!-- Select visual (solo para mostrar, bloqueado) -->
                        <select id="tipo_paciente" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm bg-white focus:outline-none focus:border-red-500 focus:ring-2 focus:ring-red-100 transition-all disabled:bg-gray-100 disabled:cursor-not-allowed" onchange="toggleDatosPersonales()">
                            <option value="existente">Paciente Existente</option>
                            <option value="nuevo">Nuevo Paciente</option>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">El sistema selecciona automáticamente según la búsqueda</p>
                    </div>

                    <!-- Datos Personales (para nuevos pacientes) -->
                    <div id="datosPersonales" class="hidden">
                        <div class="bg-gradient-to-r from-red-50 to-orange-50 rounded-xl p-5 border border-red-100">
                            <h4 class="text-sm font-semibold text-gray-800 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                                </svg>
                                Datos del Nuevo Paciente
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombres *</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                        </div>
                                        <input type="text" name="nombres" placeholder="Nombres del paciente" 
                                               class="w-full pl-9 pr-3 py-2.5 border border-gray-200 rounded-lg text-sm bg-white focus:outline-none focus:border-red-500 focus:ring-2 focus:ring-red-100 transition-all">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Apellidos *</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0"/>
                                            </svg>
                                        </div>
                                        <input type="text" name="apellidos" placeholder="Apellidos del paciente" 
                                               class="w-full pl-9 pr-3 py-2.5 border border-gray-200 rounded-lg text-sm bg-white focus:outline-none focus:border-red-500 focus:ring-2 focus:ring-red-100 transition-all">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Sexo *</label>
                                    <div class="grid grid-cols-2 gap-3">
                                        <label class="relative flex items-center p-3 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-blue-400 transition-all has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50">
                                            <input type="radio" name="sexo" value="Masculino" class="sr-only peer">
                                            <svg class="w-5 h-5 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                            </svg>
                                            <span class="text-sm font-medium">Masculino</span>
                                            <div class="absolute top-2 right-2 w-3 h-3 rounded-full border-2 border-gray-300 peer-checked:border-blue-500 peer-checked:bg-blue-500"></div>
                                        </label>
                                        <label class="relative flex items-center p-3 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-pink-400 transition-all has-[:checked]:border-pink-500 has-[:checked]:bg-pink-50">
                                            <input type="radio" name="sexo" value="Femenino" class="sr-only peer">
                                            <svg class="w-5 h-5 text-pink-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4a4 4 0 100 8 4 4 0 000-8z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14v7m-3-3h6"/>
                                            </svg>
                                            <span class="text-sm font-medium">Femenino</span>
                                            <div class="absolute top-2 right-2 w-3 h-3 rounded-full border-2 border-gray-300 peer-checked:border-pink-500 peer-checked:bg-pink-500"></div>
                                        </label>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                            </svg>
                                        </div>
                                        <input type="tel" name="telefono" placeholder="Ej: 0414-1234567" 
                                               class="w-full pl-9 pr-3 py-2.5 border border-gray-200 rounded-lg text-sm bg-white focus:outline-none focus:border-red-500 focus:ring-2 focus:ring-red-100 transition-all">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Correo</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                                            </svg>
                                        </div>
                                        <input type="email" name="correo" placeholder="correo@ejemplo.com" 
                                               class="w-full pl-9 pr-3 py-2.5 border border-gray-200 rounded-lg text-sm bg-white focus:outline-none focus:border-red-500 focus:ring-2 focus:ring-red-100 transition-all">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Dirección</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                        </div>
                                        <input type="text" name="direccion" placeholder="Dirección completa" 
                                               class="w-full pl-9 pr-3 py-2.5 border border-gray-200 rounded-lg text-sm bg-white focus:outline-none focus:border-red-500 focus:ring-2 focus:ring-red-100 transition-all">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Seguro - Select dinámico desde CRUD -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Cobertura *</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                            </div>
                            <select name="seguro_id" id="seguro_select"
                                    class="w-full pl-10 pr-10 py-3 border border-gray-200 rounded-xl text-sm bg-white focus:outline-none focus:border-red-500 focus:ring-2 focus:ring-red-100 transition-all appearance-none cursor-pointer"
                                    onchange="mostrarInfoSeguro()">
                                <option value="">Particular (Sin seguro) - Pago directo</option>
                                @foreach($seguros as $seguro)
                                    <option value="{{ $seguro->id }}"
                                            data-tipo="{{ $seguro->tipo_cobertura }}"
                                            data-descripcion="{{ $seguro->descripcion_cobertura }}">
                                        {{ $seguro->nombre_empresa }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </div>
                        </div>
                        <div id="info_seguro" class="mt-3 hidden">
                            <div class="bg-blue-50 border border-blue-100 rounded-xl p-4">
                                <p class="text-sm text-blue-800 font-medium" id="descripcion_seguro"></p>
                                <p class="text-xs text-blue-600 mt-1">El paciente será enviado a autorización de seguros.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sección 2: Clasificación de Ingreso -->
                <div class="mb-6">
                    <h3 class="text-md font-semibold text-gray-700 mb-4 pb-2 border-b border-gray-200">2. Clasificación de Ingreso</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- SOAT -->
                        <label class="relative flex flex-col p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-orange-400 transition-all has-[:checked]:border-orange-500 has-[:checked]:bg-orange-50">
                            <input type="radio" name="tipo_ingreso" value="soat" class="sr-only peer" onchange="updateDestinoOptions()">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center mr-3">
                                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                    </svg>
                                </div>
                                <div>
                                    <span class="font-semibold text-gray-800">SOAT</span>
                                    <p class="text-xs text-gray-500">Accidente de tránsito</p>
                                </div>
                            </div>
                            <div class="absolute top-2 right-2 w-4 h-4 rounded-full border-2 border-gray-300 peer-checked:border-orange-500 peer-checked:bg-orange-500"></div>
                        </label>

                        <!-- Parto -->
                        <label class="relative flex flex-col p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-pink-400 transition-all has-[:checked]:border-pink-500 has-[:checked]:bg-pink-50">
                            <input type="radio" name="tipo_ingreso" value="parto" class="sr-only peer" onchange="updateDestinoOptions()">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-pink-100 rounded-full flex items-center justify-center mr-3">
                                    <svg class="w-5 h-5 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <span class="font-semibold text-gray-800">Parto</span>
                                    <p class="text-xs text-gray-500">Atención obstétrica</p>
                                </div>
                            </div>
                            <div class="absolute top-2 right-2 w-4 h-4 rounded-full border-2 border-gray-300 peer-checked:border-pink-500 peer-checked:bg-pink-500"></div>
                        </label>

                        <!-- Emergencia General -->
                        <label class="relative flex flex-col p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-blue-400 transition-all has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50">
                            <input type="radio" name="tipo_ingreso" value="general" class="sr-only peer" checked onchange="updateDestinoOptions()">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                </div>
                                <div>
                                    <span class="font-semibold text-gray-800">Emergencia General</span>
                                    <p class="text-xs text-gray-500">Otras emergencias</p>
                                </div>
                            </div>
                            <div class="absolute top-2 right-2 w-4 h-4 rounded-full border-2 border-gray-300 peer-checked:border-blue-500 peer-checked:bg-blue-500"></div>
                        </label>
                    </div>
                </div>

                <!-- Sección 3: Destino Inicial -->
                <div class="mb-6">
                    <h3 class="text-md font-semibold text-gray-700 mb-4 pb-2 border-b border-gray-200">3. Destino Inicial</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- Cirugía -->
                        <label class="relative flex flex-col p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-purple-400 transition-all has-[:checked]:border-purple-500 has-[:checked]:bg-purple-50">
                            <input type="radio" name="destino_inicial" value="cirugia" class="sr-only peer">
                            <div class="text-center">
                                <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-2">
                                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                                    </svg>
                                </div>
                                <span class="font-semibold text-gray-800">Cirugía</span>
                                <p class="text-xs text-gray-500">Quirófano inmediato</p>
                            </div>
                            <div class="absolute top-2 right-2 w-4 h-4 rounded-full border-2 border-gray-300 peer-checked:border-purple-500 peer-checked:bg-purple-500"></div>
                        </label>

                        <!-- Camilla/Observación -->
                        <label class="relative flex flex-col p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-yellow-400 transition-all has-[:checked]:border-yellow-500 has-[:checked]:bg-yellow-50">
                            <input type="radio" name="destino_inicial" value="camilla" class="sr-only peer" checked>
                            <div class="text-center">
                                <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-2">
                                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                    </svg>
                                </div>
                                <span class="font-semibold text-gray-800">Camilla</span>
                                <p class="text-xs text-gray-500">Área de observación</p>
                            </div>
                            <div class="absolute top-2 right-2 w-4 h-4 rounded-full border-2 border-gray-300 peer-checked:border-yellow-500 peer-checked:bg-yellow-500"></div>
                        </label>

                        <!-- UTI -->
                        <label class="relative flex flex-col p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-red-400 transition-all has-[:checked]:border-red-500 has-[:checked]:bg-red-50">
                            <input type="radio" name="destino_inicial" value="uti" class="sr-only peer">
                            <div class="text-center">
                                <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-2">
                                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                    </svg>
                                </div>
                                <span class="font-semibold text-gray-800">UTI</span>
                                <p class="text-xs text-gray-500">Unidad de Terapia Intensiva</p>
                            </div>
                            <div class="absolute top-2 right-2 w-4 h-4 rounded-full border-2 border-gray-300 peer-checked:border-red-500 peer-checked:bg-red-500"></div>
                        </label>

                        <!-- Sala de Parto -->
                        <label class="relative flex flex-col p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-pink-400 transition-all has-[:checked]:border-pink-500 has-[:checked]:bg-pink-50 opacity-50 cursor-not-allowed" id="sala_parto_option">
                            <input type="radio" name="destino_inicial" value="parto" class="sr-only peer" disabled>
                            <div class="text-center">
                                <div class="w-12 h-12 bg-pink-100 rounded-full flex items-center justify-center mx-auto mb-2">
                                    <svg class="w-6 h-6 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                    </svg>
                                </div>
                                <span class="font-semibold text-gray-800">Sala de Parto</span>
                                <p class="text-xs text-gray-500">Solo para tipo Parto</p>
                            </div>
                            <div class="absolute top-2 right-2 w-4 h-4 rounded-full border-2 border-gray-300 peer-checked:border-pink-500 peer-checked:bg-pink-500"></div>
                        </label>
                    </div>
                </div>

                <!-- Sección 4: Datos de la Emergencia -->
                <div class="mb-6">
                    <h3 class="text-md font-semibold text-gray-700 mb-4 pb-2 border-b border-gray-200">4. Datos de la Emergencia</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Emergencia *</label>
                            <select name="tipo_emergencia" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm bg-white focus:outline-none focus:border-red-500 focus:ring-2 focus:ring-red-100 transition-all" required>
                                <option value="">Seleccione...</option>
                                <option value="trauma">Trauma</option>
                                <option value="accidente">Accidente Vehicular</option>
                                <option value="cardiaco">Paro Cardíaco</option>
                                <option value="respiratorio">Insuficiencia Respiratoria</option>
                                <option value="neurologico">Emergencia Neurológica</option>
                                <option value="obstetrico">Emergencia Obstétrica</option>
                                <option value="pediatrico">Emergencia Pediátrica</option>
                                <option value="quemadura">Quemaduras</option>
                                <option value="intoxicacion">Intoxicación</option>
                                <option value="otro">Otro</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Descripción de la Emergencia *</label>
                            <textarea name="descripcion" rows="3" placeholder="Describa detalladamente los síntomas y la situación de emergencia" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm bg-white focus:outline-none focus:border-red-500 focus:ring-2 focus:ring-red-100 transition-all" required></textarea>
                        </div>

                        <div class="bg-blue-50 border border-blue-100 rounded-xl p-4">
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-blue-800">Información importante</p>
                                    <p class="text-sm text-blue-600 mt-1">Los signos vitales, alergias y medicamentos serán registrados por el personal de emergencia durante la evaluación médica inicial.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botones de Acción -->
                <div class="flex justify-end gap-3 pt-6 border-t border-gray-200">
                    <a href="{{ route('reception') }}" class="px-6 py-3 border border-gray-200 rounded-xl text-gray-700 font-medium hover:bg-gray-50 transition-colors text-sm">
                        Cancelar
                    </a>
                    <button type="submit" class="px-6 py-3 bg-red-600 text-white rounded-xl font-medium hover:bg-red-700 transition-colors flex items-center text-sm shadow-md">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        Registrar Ingreso a Emergencia
                    </button>
                </div>
            </form>
        </div>

    </div>

    <script>
        // Toggle ID Temporal
        function toggleTempId() {
            const checkbox = document.getElementById('usar_temp_id');
            const ciContainer = document.getElementById('ci_container');
            const tempIdContainer = document.getElementById('temp_id_container');
            const ciInput = document.getElementById('paciente_ci');
            const tempIdInput = document.getElementById('temp_id');
            const tipoPacienteSelect = document.getElementById('tipo_paciente');
            
            if (checkbox.checked) {
                ciContainer.classList.add('hidden');
                ciContainer.classList.remove('block');
                tempIdContainer.classList.remove('hidden');
                tempIdContainer.classList.add('block');
                ciInput.removeAttribute('required');
                tempIdInput.setAttribute('required', 'required');
                // Generar ID temporal automáticamente
                const date = new Date();
                const tempId = 'TEMP-' + date.getFullYear() + String(date.getMonth()+1).padStart(2,'0') + String(date.getDate()).padStart(2,'0') + '-' + Math.floor(Math.random() * 999).toString().padStart(3, '0');
                tempIdInput.value = tempId;

                // Cambiar automáticamente a paciente existente, deshabilitar y ocultar datos personales
                tipoPacienteSelect.value = 'existente';
                tipoPacienteSelect.disabled = true;
                document.getElementById('tipo_paciente_hidden').value = 'existente';
                toggleDatosPersonales();
            } else {
                ciContainer.classList.remove('hidden');
                ciContainer.classList.add('block');
                tempIdContainer.classList.add('hidden');
                tempIdContainer.classList.remove('block');
                ciInput.setAttribute('required', 'required');
                tempIdInput.removeAttribute('required');
                tempIdInput.value = '';

                // Habilitar select de tipo de paciente para nueva búsqueda
                tipoPacienteSelect.disabled = false;
                tipoPacienteSelect.classList.remove('bg-gray-100', 'cursor-not-allowed');
                tipoPacienteSelect.value = 'existente';
                document.getElementById('tipo_paciente_hidden').value = 'existente';
            }
        }

        // Toggle datos personales
        function toggleDatosPersonales() {
            const tipo = document.getElementById('tipo_paciente').value;
            const usarTempId = document.getElementById('usar_temp_id').checked;
            const container = document.getElementById('datosPersonales');
            
            // Si usa ID temporal, no mostrar datos personales
            if (usarTempId) {
                container.classList.add('hidden');
                // Quitar required de los campos de datos personales
                container.querySelectorAll('input, select').forEach(field => {
                    field.removeAttribute('required');
                });
            } else if (tipo === 'nuevo') {
                container.classList.remove('hidden');
                // Agregar required a campos obligatorios
                const nombresField = container.querySelector('input[name="nombres"]');
                const apellidosField = container.querySelector('input[name="apellidos"]');
                const sexoField = container.querySelector('select[name="sexo"]');
                
                if (nombresField) nombresField.setAttribute('required', 'required');
                if (apellidosField) apellidosField.setAttribute('required', 'required');
                if (sexoField) sexoField.setAttribute('required', 'required');
            } else {
                container.classList.add('hidden');
            }
        }

        // Update destino options based on tipo ingreso
        function updateDestinoOptions() {
            const tipoIngreso = document.querySelector('input[name="tipo_ingreso"]:checked').value;
            const salaPartoOption = document.getElementById('sala_parto_option');
            const salaPartoInput = salaPartoOption.querySelector('input');
            
            if (tipoIngreso === 'parto') {
                salaPartoOption.classList.remove('opacity-50', 'cursor-not-allowed');
                salaPartoInput.disabled = false;
                salaPartoInput.checked = true;
            } else {
                salaPartoOption.classList.add('opacity-50', 'cursor-not-allowed');
                salaPartoInput.disabled = true;
                salaPartoInput.checked = false;
            }
        }

        // Buscar paciente
        async function buscarPaciente() {
            const ci = document.getElementById('paciente_ci').value;
            
            if (!ci || ci.length < 3) {
                alert('Por favor ingrese un número de CI válido');
                return;
            }
            
            const btn = event.target;
            const originalHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<svg class="animate-spin w-4 h-4 inline mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Buscando...';
            
            try {
                const response = await fetch('/api/buscar-paciente', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ ci: ci })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    mostrarPacienteEncontrado(data.paciente);

                    // Poner como paciente existente y bloquear el select
                    const tipoPacienteSelect = document.getElementById('tipo_paciente');
                    tipoPacienteSelect.value = 'existente';
                    tipoPacienteSelect.disabled = true;
                    document.getElementById('tipo_paciente_hidden').value = 'existente';
                    toggleDatosPersonales();
                } else {
                    // Limpiar notificación de paciente anterior si existe
                    const existing = document.querySelector('.paciente-encontrado-notification');
                    if (existing) existing.remove();

                    // Poner como paciente nuevo y bloquear el select
                    const tipoPacienteSelect = document.getElementById('tipo_paciente');
                    tipoPacienteSelect.value = 'nuevo';
                    tipoPacienteSelect.disabled = true;
                    document.getElementById('tipo_paciente_hidden').value = 'nuevo';
                    toggleDatosPersonales();
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error al buscar paciente');
            } finally {
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            }
        }

        // Mostrar paciente encontrado
        function mostrarPacienteEncontrado(paciente) {
            const existing = document.querySelector('.paciente-encontrado-notification');
            if (existing) existing.remove();
            
            const notification = document.createElement('div');
            notification.className = 'paciente-encontrado-notification mt-4 p-4 bg-green-50 border border-green-200 rounded-xl';
            notification.innerHTML = `
                <div class="flex items-center mb-2">
                    <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="font-semibold text-green-800">Paciente Encontrado</span>
                </div>
                <div class="text-sm text-gray-700">
                    <p><strong>Nombre:</strong> ${paciente.nombre}</p>
                    <p><strong>CI:</strong> ${paciente.ci}</p>
                    <p><strong>Teléfono:</strong> ${paciente.telefono || 'N/A'}</p>
                </div>
            `;
            
            document.getElementById('ci_container').appendChild(notification);
        }

        // Registrar emergencia
        async function registrarEmergencia(event) {
            event.preventDefault();
            
            const form = document.getElementById('formEmergencia');
            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());
            
            // Validación
            const usarTempId = data.usar_temp_id === '1' || data.usar_temp_id === true;
            
            if (!usarTempId && !data.ci) {
                alert('Por favor ingrese el CI del paciente o use ID Temporal');
                return;
            }
            
            // Si usa ID temporal, no validar datos personales
            if (!usarTempId && data.tipo_paciente === 'nuevo') {
                if (!data.nombres || !data.apellidos || !data.sexo) {
                    alert('Por favor complete los datos personales obligatorios');
                    return;
                }
            }
            
            if (!data.tipo_ingreso) {
                alert('Por favor seleccione el tipo de ingreso');
                return;
            }
            
            if (!data.destino_inicial) {
                alert('Por favor seleccione el destino inicial');
                return;
            }
            
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalHtml = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<svg class="animate-spin w-4 h-4 inline mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Procesando...';
            
            try {
                const response = await fetch('/api/emergency-ingreso', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(data)
                });
                
                const result = await response.json();
                
                if (result.success) {
                    // Redirigir al comprobante de emergencia
                    if (result.redirect_url) {
                        window.location.href = result.redirect_url;
                    } else {
                        alert('Paciente registrado en emergencia exitosamente. Código: ' + result.emergency_code);
                        form.reset();
                        document.querySelector('.paciente-encontrado-notification')?.remove();
                        toggleTempId();
                        toggleDatosPersonales();

                        // Habilitar select de tipo de paciente para nuevo registro
                        const tipoPacienteSelect = document.getElementById('tipo_paciente');
                        tipoPacienteSelect.disabled = false;
                        tipoPacienteSelect.classList.remove('bg-gray-100', 'cursor-not-allowed');
                        document.getElementById('tipo_paciente_hidden').value = 'existente';
                    }
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error al registrar la emergencia');
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalHtml;
            }
        }

        // Función para mostrar información del seguro seleccionado
        function mostrarInfoSeguro() {
            const select = document.getElementById('seguro_select');
            const infoDiv = document.getElementById('info_seguro');
            const descripcionP = document.getElementById('descripcion_seguro');
            
            if (select.value) {
                const option = select.selectedOptions[0];
                const tipo = option.getAttribute('data-tipo');
                const descripcion = option.getAttribute('data-descripcion');
                
                descripcionP.textContent = `${descripcion}`;
                infoDiv.classList.remove('hidden');
            } else {
                infoDiv.classList.add('hidden');
            }
        }

        // Inicializar
        document.addEventListener('DOMContentLoaded', function() {
            toggleDatosPersonales();
            updateDestinoOptions();
        });
    </script>
@endsection
