@extends('layouts.app')
@section('content')
    <div class="p-6 bg-gray-50/50 min-h-screen">

        <!-- Header -->
        <div class="flex justify-between items-end mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Consulta Externa</h1>
                <p class="text-sm text-gray-500">Recepción - Consulta Externa</p>
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

        <!-- Formulario de Consulta Externa -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-bold text-gray-800 mb-6">Registrar Nueva Consulta Externa</h2>
            
            <form id="formConsultaExterna" onsubmit="registrarConsultaExterna(); return false;" novalidate>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Búsqueda de Paciente -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">C.I. Paciente *</label>
                        <div class="flex gap-3">
                            <input type="text" id="paciente_ci" name="ci" placeholder="Número de CI del paciente" 
                                   class="flex-1 border border-gray-200 rounded-xl px-4 py-3 text-sm bg-white focus:outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition-all" 
                                   oninput="resetearFormularioPaciente()" required>
                            <button type="button" onclick="buscarPaciente()" class="bg-green-600 hover:bg-green-700 text-white font-medium px-6 py-3 rounded-xl transition-colors text-sm">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                Buscar Paciente
                            </button>
                        </div>
                    </div>

                    <!-- Tipo de Paciente -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Paciente</label>
                        <select name="tipo_paciente" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm bg-white focus:outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition-all">
                            <option value="existente">Paciente Existente</option>
                            <option value="nuevo">Nuevo Paciente</option>
                        </select>
                    </div>

                    
                    <!-- Datos Personales (para nuevos pacientes) -->
                    <div id="datosPersonales" class="md:col-span-2 hidden">
                        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-5 border border-blue-100">
                            <h3 class="text-md font-semibold text-gray-800 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                                </svg>
                                Datos del Nuevo Paciente
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombres *</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                        </div>
                                        <input type="text" name="nombres" placeholder="Nombres del paciente" required
                                               class="w-full pl-9 pr-3 py-2.5 border border-gray-200 rounded-lg text-sm bg-white focus:outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition-all">
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
                                        <input type="text" name="apellidos" placeholder="Apellidos del paciente" required
                                               class="w-full pl-9 pr-3 py-2.5 border border-gray-200 rounded-lg text-sm bg-white focus:outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition-all">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Sexo *</label>
                                    <div class="grid grid-cols-2 gap-2">
                                        <label class="relative flex items-center p-2.5 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-blue-400 transition-all has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50">
                                            <input type="radio" name="sexo" value="M" class="sr-only peer" required>
                                            <svg class="w-4 h-4 text-blue-500 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                            </svg>
                                            <span class="text-xs font-medium">Masculino</span>
                                            <div class="absolute top-1.5 right-1.5 w-2.5 h-2.5 rounded-full border-2 border-gray-300 peer-checked:border-blue-500 peer-checked:bg-blue-500"></div>
                                        </label>
                                        <label class="relative flex items-center p-2.5 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-pink-400 transition-all has-[:checked]:border-pink-500 has-[:checked]:bg-pink-50">
                                            <input type="radio" name="sexo" value="F" class="sr-only peer" required>
                                            <svg class="w-4 h-4 text-pink-500 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4a4 4 0 100 8 4 4 0 000-8z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14v7m-3-3h6"/>
                                            </svg>
                                            <span class="text-xs font-medium">Femenino</span>
                                            <div class="absolute top-1.5 right-1.5 w-2.5 h-2.5 rounded-full border-2 border-gray-300 peer-checked:border-pink-500 peer-checked:bg-pink-500"></div>
                                        </label>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Nacimiento *</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                        <input type="date" name="fecha_nacimiento" required
                                               class="w-full pl-9 pr-3 py-2.5 border border-gray-200 rounded-lg text-sm bg-white focus:outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition-all">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Lugar de Expedición CI</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0"/>
                                            </svg>
                                        </div>
                                        <select name="lugar_expedicion"
                                                class="w-full pl-9 pr-3 py-2.5 border border-gray-200 rounded-lg text-sm bg-white focus:outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition-all appearance-none">
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
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nacionalidad</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064"/>
                                            </svg>
                                        </div>
                                        <select name="nacionalidad"
                                                class="w-full pl-9 pr-3 py-2.5 border border-gray-200 rounded-lg text-sm bg-white focus:outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition-all appearance-none">
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
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Estado Civil</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                            </svg>
                                        </div>
                                        <select name="estado_civil"
                                                class="w-full pl-9 pr-3 py-2.5 border border-gray-200 rounded-lg text-sm bg-white focus:outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition-all appearance-none">
                                            <option value="">Seleccione...</option>
                                            <option value="Soltero/a">Soltero/a</option>
                                            <option value="Casado/a">Casado/a</option>
                                            <option value="Divorciado/a">Divorciado/a</option>
                                            <option value="Viudo/a">Viudo/a</option>
                                            <option value="Unión Libre">Unión Libre</option>
                                        </select>
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
                                               class="w-full pl-9 pr-3 py-2.5 border border-gray-200 rounded-lg text-sm bg-white focus:outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition-all">
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
                                               class="w-full pl-9 pr-3 py-2.5 border border-gray-200 rounded-lg text-sm bg-white focus:outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition-all">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Profesión</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                        <input type="text" name="profesion" placeholder="Profesión u oficio" 
                                               class="w-full pl-9 pr-3 py-2.5 border border-gray-200 rounded-lg text-sm bg-white focus:outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition-all">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Empresa de Trabajo</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                            </svg>
                                        </div>
                                        <input type="text" name="empresa_trabajo" placeholder="Nombre de la empresa" 
                                               class="w-full pl-9 pr-3 py-2.5 border border-gray-200 rounded-lg text-sm bg-white focus:outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition-all">
                                    </div>
                                </div>
                                <div class="md:col-span-2 lg:col-span-3">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Dirección de Residencia</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                        </div>
                                        <input type="text" name="direccion_residencia" placeholder="Dirección completa de residencia" 
                                               class="w-full pl-9 pr-3 py-2.5 border border-gray-200 rounded-lg text-sm bg-white focus:outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition-all">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Datos del Garante -->
                    <div class="md:col-span-2 space-y-5 pt-4 border-t border-gray-100">
                        <h3 class="text-md font-semibold text-gray-800 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            Garante / Responsable (Opcional)
                        </h3>
                        <div class="bg-amber-50 rounded-xl p-5 border border-amber-100">
                            <div class="flex gap-3 mb-4">
                                <input type="text" id="garante_ci" placeholder="CI del garante" 
                                       class="flex-1 border border-gray-200 rounded-xl px-4 py-3 text-sm bg-white focus:outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100 transition-all">
                                <button type="button" onclick="buscarGarante()" class="bg-amber-600 hover:bg-amber-700 text-white font-medium px-6 py-3 rounded-xl transition-colors text-sm">
                                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                    Buscar
                                </button>
                                <button type="button" onclick="toggleFormularioGarante()" class="bg-gray-600 hover:bg-gray-700 text-white font-medium px-4 py-3 rounded-xl transition-colors text-sm" title="Registrar nuevo garante">
                                    <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                </button>
                            </div>
                            <div id="garante_info" class="hidden">
                                <div class="bg-white rounded-lg p-4 border border-amber-200">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <p class="font-medium text-gray-800" id="garante_nombre"></p>
                                            <p class="text-sm text-gray-500">CI: <span id="garante_ci_display"></span></p>
                                            <p class="text-sm text-gray-500">Tel: <span id="garante_telefono"></span></p>
                                        </div>
                                        <button type="button" onclick="limpiarGarante()" class="text-red-500 hover:text-red-700 text-sm">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </div>
                                    <input type="hidden" name="id_garante_referencia" id="id_garante_referencia">
                                </div>
                            </div>

                            <!-- Formulario para nuevo garante (inline, oculto por defecto) -->
                            <div id="formularioNuevoGarante" class="hidden mt-4 pt-4 border-t border-amber-200">
                                <h4 class="text-sm font-semibold text-amber-800 mb-3 flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                                    </svg>
                                    Registrar Nuevo Garante
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    <!-- CI -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">CI *</label>
                                        <input type="text" id="nuevo_garante_ci"
                                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100 transition-all">
                                    </div>
                                    <!-- Lugar de Expedición -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Lugar de Expedición CI</label>
                                        <select id="nuevo_garante_lugar_expedicion"
                                                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100 transition-all appearance-none">
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
                                    <!-- Nombres -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombres *</label>
                                        <input type="text" id="nuevo_garante_nombres"
                                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100 transition-all">
                                    </div>
                                    <!-- Apellidos -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Apellidos *</label>
                                        <input type="text" id="nuevo_garante_apellidos"
                                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100 transition-all">
                                    </div>
                                    <!-- Sexo -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Sexo *</label>
                                        <div class="flex gap-3">
                                            <label class="flex-1 flex items-center justify-center p-2.5 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-blue-400 transition-all has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50">
                                                <input type="radio" name="garante_sexo" value="M" class="sr-only peer">
                                                <span class="text-sm font-medium peer-checked:text-blue-700">Masculino</span>
                                            </label>
                                            <label class="flex-1 flex items-center justify-center p-2.5 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-pink-400 transition-all has-[:checked]:border-pink-500 has-[:checked]:bg-pink-50">
                                                <input type="radio" name="garante_sexo" value="F" class="sr-only peer">
                                                <span class="text-sm font-medium peer-checked:text-pink-700">Femenino</span>
                                            </label>
                                        </div>
                                    </div>
                                    <!-- Fecha de Nacimiento -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Nacimiento *</label>
                                        <input type="date" id="nuevo_garante_fecha_nacimiento"
                                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100 transition-all">
                                    </div>
                                    <!-- Nacionalidad -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Nacionalidad</label>
                                        <select id="nuevo_garante_nacionalidad"
                                                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100 transition-all appearance-none">
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
                                    <!-- Estado Civil -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Estado Civil</label>
                                        <select id="nuevo_garante_estado_civil"
                                                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100 transition-all appearance-none">
                                            <option value="">Seleccione...</option>
                                            <option value="Soltero/a">Soltero/a</option>
                                            <option value="Casado/a">Casado/a</option>
                                            <option value="Divorciado/a">Divorciado/a</option>
                                            <option value="Viudo/a">Viudo/a</option>
                                            <option value="Unión Libre">Unión Libre</option>
                                        </select>
                                    </div>
                                    <!-- Teléfono -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                                        <input type="tel" id="nuevo_garante_telefono"
                                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100 transition-all">
                                    </div>
                                    <!-- Correo -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Correo</label>
                                        <input type="email" id="nuevo_garante_correo"
                                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100 transition-all">
                                    </div>
                                    <!-- Profesión -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Profesión</label>
                                        <input type="text" id="nuevo_garante_profesion"
                                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100 transition-all">
                                    </div>
                                    <!-- Empresa de Trabajo -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Empresa de Trabajo</label>
                                        <input type="text" id="nuevo_garante_empresa"
                                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100 transition-all">
                                    </div>
                                    <!-- Dirección -->
                                    <div class="md:col-span-2 lg:col-span-3">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Dirección de Residencia</label>
                                        <textarea id="nuevo_garante_direccion" rows="2"
                                                  class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100 transition-all resize-none"></textarea>
                                    </div>
                                </div>
                                <div class="flex gap-3 pt-4">
                                    <button type="button" onclick="toggleFormularioGarante()"
                                            class="px-4 py-2.5 border border-gray-300 rounded-xl text-gray-700 font-medium hover:bg-gray-50 transition-colors text-sm">
                                        Cancelar
                                    </button>
                                    <button type="button" onclick="registrarNuevoGarante()"
                                            class="px-4 py-2.5 bg-amber-600 text-white rounded-xl font-medium hover:bg-amber-700 transition-colors text-sm">
                                        Guardar Garante
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Datos de Consulta -->
                    <div class="md:col-span-2 space-y-5 pt-4 border-t border-gray-100">
                        <h3 class="text-md font-semibold text-gray-800 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            Datos de la Consulta
                        </h3>

                        <!-- Especialidad -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Especialidad *</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                                    </svg>
                                </div>
                                <select name="especialidad" id="especialidad_select"
                                        class="w-full pl-10 pr-10 py-3 border border-gray-200 rounded-xl text-sm bg-white focus:outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition-all appearance-none cursor-pointer">
                                    <option value="">Seleccione una especialidad...</option>
                                    @foreach($especialidades as $especialidad)
                                        <option value="{{ $especialidad->codigo }}">{{ $especialidad->nombre }}</option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Médico -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Médico *</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                                <select name="medico" id="medico_select"
                                        class="w-full pl-10 pr-10 py-3 border border-gray-200 rounded-xl text-sm bg-white focus:outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition-all appearance-none cursor-pointer">
                                    <option value="">Seleccione especialidad primero</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
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
                                        class="w-full pl-10 pr-10 py-3 border border-gray-200 rounded-xl text-sm bg-white focus:outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition-all appearance-none cursor-pointer"
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
                        
                        <!-- Motivo -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Motivo de Consulta *</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                                    </svg>
                                </div>
                                <input type="text" name="motivo" placeholder="Describa el motivo principal de la consulta" 
                                       class="w-full pl-10 pr-3 py-3 border border-gray-200 rounded-xl text-sm bg-white focus:outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition-all">
                            </div>
                            <div class="mt-2 flex flex-wrap gap-2">
                                <span class="text-xs text-gray-500">Motivos comunes:</span>
                                <button type="button" onclick="setMotivo('Dolor de cabeza')" class="text-xs px-2 py-1 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-full transition-colors">Dolor de cabeza</button>
                                <button type="button" onclick="setMotivo('Control médico')" class="text-xs px-2 py-1 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-full transition-colors">Control médico</button>
                                <button type="button" onclick="setMotivo('Presión arterial')" class="text-xs px-2 py-1 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-full transition-colors">Presión arterial</button>
                                <button type="button" onclick="setMotivo('Dolor abdominal')" class="text-xs px-2 py-1 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-full transition-colors">Dolor abdominal</button>
                            </div>
                        </div>
                        
                        <!-- Observaciones -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Observaciones</label>
                            <div class="relative">
                                <div class="absolute top-3 left-3 flex items-start pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                                <textarea name="observaciones" rows="3" placeholder="Notas adicionales sobre la consulta..." 
                                          class="w-full pl-10 pr-3 py-3 border border-gray-200 rounded-xl text-sm bg-white focus:outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition-all resize-none"></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botones de Acción -->
                <div class="flex justify-end gap-3 pt-6 border-t border-gray-200 mt-6">
                    <a href="{{ route('reception') }}" class="px-6 py-3 border border-gray-200 rounded-xl text-gray-700 font-medium hover:bg-gray-50 transition-colors text-sm">
                        Cancelar
                    </a>
                    <button type="submit" class="px-6 py-3 bg-green-600 text-white rounded-xl font-medium hover:bg-green-700 transition-colors flex items-center text-sm shadow-md">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Registrar Consulta
                    </button>
                </div>
            </form>
        </div>

    </div>

    <!-- JavaScript -->
    <script>
        // Cargar datos al iniciar la página
        document.addEventListener('DOMContentLoaded', function() {
            inicializarTipoPaciente();
            inicializarCargaMedicos();
        });

        // Función para calcular edad desde fecha de nacimiento
        function calcularEdad(fechaNacimiento) {
            const hoy = new Date();
            const fechaNac = new Date(fechaNacimiento);
            let edad = hoy.getFullYear() - fechaNac.getFullYear();
            const mes = hoy.getMonth() - fechaNac.getMonth();
            if (mes < 0 || (mes === 0 && hoy.getDate() < fechaNac.getDate())) {
                edad--;
            }
            return edad;
        }

        // Función para inicializar la carga de médicos
        function inicializarCargaMedicos() {
            const especialidadSelect = document.querySelector('select[name="especialidad"]');
            const medicoSelect = document.querySelector('select[name="medico"]');
            
            if (especialidadSelect && medicoSelect) {
                especialidadSelect.addEventListener('change', function() {
                    cargarMedicosPorEspecialidad(this.value);
                });
            }
        }

        // Función para cargar médicos por especialidad
        async function cargarMedicosPorEspecialidad(especialidadCodigo) {
            const medicoSelect = document.querySelector('select[name="medico"]');
            
            if (!especialidadCodigo) {
                medicoSelect.innerHTML = '<option value="">Seleccione especialidad primero</option>';
                return;
            }
            
            // Mostrar loading
            medicoSelect.innerHTML = '<option value="">Cargando médicos...</option>';
            medicoSelect.disabled = true;
            
            try {
                const response = await fetch(`/api/medicos-disponibles?especialidad=${especialidadCodigo}`);
                const data = await response.json();
                
                medicoSelect.disabled = false;
                
                if (data.success && data.medicos.length > 0) {
                    medicoSelect.innerHTML = '<option value="">Seleccione un médico...</option>';
                    
                    data.medicos.forEach(medico => {
                        const option = document.createElement('option');
                        option.value = medico.ci;
                        option.textContent = `Dr. ${medico.nombre}`;
                        medicoSelect.appendChild(option);
                    });
                } else {
                    medicoSelect.innerHTML = '<option value="">No hay médicos disponibles</option>';
                }
            } catch (error) {
                console.error('Error al cargar médicos:', error);
                medicoSelect.innerHTML = '<option value="">Error al cargar médicos</option>';
                medicoSelect.disabled = false;
            }
        }

        // Función para setear motivo rápido
        function setMotivo(texto) {
            document.querySelector('input[name="motivo"]').value = texto;
        }


        // Función para buscar paciente
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
                    // PACIENTE ENCONTRADO - Bloquear tipo de paciente en "existente"
                    tipoPacienteSelect.value = 'existente';
                    tipoPacienteSelect.disabled = true; // Bloquear el selector
                    tipoPacienteSelect.dispatchEvent(new Event('change'));
                    
                    // Mostrar mensaje de éxito
                    mostrarDatosPaciente(data.paciente);
                    
                    // Ocultar formulario de datos personales
                    if (datosPersonales) {
                        datosPersonales.classList.add('hidden');
                        datosPersonales.classList.remove('block');
                    }
                } else {
                    // PACIENTE NO ENCONTRADO - Bloquear tipo de paciente en "nuevo"
                    tipoPacienteSelect.value = 'nuevo';
                    tipoPacienteSelect.disabled = true; // Bloquear el selector
                    tipoPacienteSelect.dispatchEvent(new Event('change'));
                    
                    // Mostrar mensaje de paciente no encontrado
                    mostrarPacienteNoEncontrado(ci);
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

        // Función para mostrar mensaje de paciente no encontrado
        function mostrarPacienteNoEncontrado(ci) {
            // Crear una sección para mostrar que el paciente no fue encontrado
            const datosSection = document.createElement('div');
            datosSection.className = 'mt-4 p-4 bg-orange-50 rounded-xl border border-orange-200';
            datosSection.innerHTML = `
                <div class="flex items-center mb-3">
                    <svg class="w-5 h-5 text-orange-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <span class="font-semibold text-orange-800">Paciente No Encontrado</span>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div><strong>CI Buscado:</strong> ${ci}</div>
                    <div><strong>Estado:</strong> No registrado en el sistema</div>
                </div>
                <div class="mt-3 text-sm text-orange-700">
                    <strong>ℹ️ Por favor complete los datos personales para registrar nuevo paciente</strong>
                </div>
            `;
            
            // Insertar después del campo de CI
            const ciField = document.getElementById('paciente_ci').closest('.grid').parentElement;
            const existingSection = ciField.querySelector('.bg-orange-50, .bg-green-50');
            if (existingSection) {
                existingSection.remove();
            }
            ciField.appendChild(datosSection);
        }

        // Función para mostrar datos del paciente encontrado
        function mostrarDatosPaciente(paciente) {
            const sexoLabel = paciente.sexo === 'M' ? 'Masculino' : (paciente.sexo === 'F' ? 'Femenino' : 'N/A');
            const fechaNac = paciente.fecha_nacimiento ? new Date(paciente.fecha_nacimiento).toLocaleDateString('es-VE') : 'N/A';
            const edad = paciente.fecha_nacimiento ? calcularEdad(paciente.fecha_nacimiento) : '';

            const datosSection = document.createElement('div');
            datosSection.className = 'mt-4 p-4 bg-green-50 rounded-xl border border-green-200';
            datosSection.innerHTML = `
                <div class="flex items-center mb-3">
                    <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="font-semibold text-green-800">Paciente Existente Encontrado</span>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 text-sm">
                    <div class="md:col-span-2 lg:col-span-3"><strong>Nombre:</strong> ${paciente.nombre || 'N/A'}</div>
                    <div><strong>CI:</strong> ${paciente.ci}${paciente.lugar_expedicion ? ' ' + paciente.lugar_expedicion : ''}</div>
                    <div><strong>Sexo:</strong> ${sexoLabel}</div>
                    <div><strong>Fecha Nac.:</strong> ${fechaNac} ${edad ? '(' + edad + ' años)' : ''}</div>
                    <div><strong>Teléfono:</strong> ${paciente.telefono || 'N/A'}</div>
                    <div><strong>Correo:</strong> ${paciente.correo || 'N/A'}</div>
                    <div><strong>Nacionalidad:</strong> ${paciente.nacionalidad || 'N/A'}</div>
                    <div><strong>Estado Civil:</strong> ${paciente.estado_civil || 'N/A'}</div>
                    <div><strong>Profesión:</strong> ${paciente.profesion || 'N/A'}</div>
                    <div class="md:col-span-2 lg:col-span-3"><strong>Dirección:</strong> ${paciente.direccion_residencia || paciente.direccion || 'N/A'}</div>
                    ${paciente.empresa_trabajo ? `<div class="md:col-span-2 lg:col-span-3"><strong>Empresa:</strong> ${paciente.empresa_trabajo}</div>` : ''}
                </div>
                <div class="mt-3 text-sm text-green-700">
                    <strong>✓ Paciente registrado en el sistema</strong>
                </div>
            `;
            
            // Insertar después del campo de CI
            const ciField = document.getElementById('paciente_ci').closest('.grid').parentElement;
            const existingSection = ciField.querySelector('.bg-green-50, .bg-orange-50');
            if (existingSection) {
                existingSection.remove();
            }
            ciField.appendChild(datosSection);
        }

        // Función para registrar consulta externa
        function registrarConsultaExterna() {
            event.preventDefault();

            const form = document.getElementById('formConsultaExterna');
            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());

            // Debug: mostrar id_garante_referencia
            console.log('ID Garante Referencia:', data.id_garante_referencia);

            // Validar campos requeridos básicos
            const camposFaltantes = [];
            if (!data.ci || data.ci.trim() === '') camposFaltantes.push('C.I. Paciente');
            if (!data.especialidad || data.especialidad.trim() === '') camposFaltantes.push('Especialidad');
            if (!data.medico || data.medico.trim() === '') camposFaltantes.push('Médico');
            if (!data.motivo || data.motivo.trim() === '') camposFaltantes.push('Motivo de Consulta');

            if (camposFaltantes.length > 0) {
                alert('Faltan los siguientes campos obligatorios:\n\n• ' + camposFaltantes.join('\n• '));
                return;
            }

            // Validar que si es paciente nuevo, complete todos los datos personales
            if (data.tipo_paciente === 'nuevo') {
                const datosFaltantes = [];
                if (!data.nombres || data.nombres.trim() === '') datosFaltantes.push('Nombres');
                if (!data.apellidos || data.apellidos.trim() === '') datosFaltantes.push('Apellidos');
                if (!data.sexo || data.sexo.trim() === '') datosFaltantes.push('Sexo');
                if (!data.fecha_nacimiento || data.fecha_nacimiento.trim() === '') datosFaltantes.push('Fecha de Nacimiento');

                if (datosFaltantes.length > 0) {
                    alert('Faltan los siguientes datos personales obligatorios:\n\n• ' + datosFaltantes.join('\n• '));
                    return;
                }
            }

            // Mostrar loading
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<svg class="animate-spin w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>Procesando...';
            submitBtn.disabled = true;

            // Preparar datos para el API - Asignar automáticamente triage verde
            const apiData = {
                ...data,
                triage_tipo: 'verde', // Siempre verde para consulta externa
                motivo: data.motivo
            };

            fetch('/api/triage-general', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(apiData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message || 'Consulta registrada exitosamente');
                    // Redirigir a página de confirmación
                    if (data.redirect_url) {
                        window.location.href = data.redirect_url;
                    } else {
                        window.location.href = '{{ route("reception") }}';
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

        // Función para resetear el formulario cuando se cambia el CI
        function resetearFormularioPaciente() {
            const tipoPacienteSelect = document.querySelector('select[name="tipo_paciente"]');
            const datosPersonales = document.getElementById('datosPersonales');
            const ciField = document.getElementById('paciente_ci');
            
            // Habilitar selector de tipo de paciente
            tipoPacienteSelect.disabled = false;
            
            // Resetear a valor por defecto
            tipoPacienteSelect.value = 'existente';
            
            // Ocultar formulario de datos personales
            datosPersonales.classList.add('hidden');
            datosPersonales.classList.remove('block');
            
            // Eliminar sección de datos existentes si hay una
            const existingSection = ciField.closest('.grid').parentElement.querySelector('.bg-green-50, .bg-orange-50');
            if (existingSection) {
                existingSection.remove();
            }
            
            // Disparar evento change
            tipoPacienteSelect.dispatchEvent(new Event('change'));
        }

        // Función para mostrar información del seguro seleccionado
        function mostrarInfoSeguro() {
            const select = document.getElementById('seguro_select');
            const infoDiv = document.getElementById('info_seguro');
            const descripcionP = document.getElementById('descripcion_seguro');
            
            if (select.value) {
                const option = select.selectedOptions[0];
                const descripcion = option.getAttribute('data-descripcion');
                descripcionP.textContent = descripcion;
                infoDiv.classList.remove('hidden');
            } else {
                infoDiv.classList.add('hidden');
            }
        }

        // Función para habilitar/deshabilitar campos de consulta
        function habilitarCamposConsulta(habilitar) {
            const camposConsulta = [
                'select[name="especialidad"]',
                'select[name="medico"]',
                'select[name="seguro_id"]',
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

        // ========== FUNCIONES PARA GARANTE ==========

        async function buscarGarante() {
            const ci = document.getElementById('garante_ci').value.trim();

            if (ci.length < 3) {
                alert('Por favor ingrese al menos 3 dígitos del CI');
                return;
            }

            try {
                const response = await fetch('/api/buscar-garante-exacto', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ ci: ci })
                });
                const data = await response.json();

                if (data.success && data.garante) {
                    mostrarGarante(data.garante);
                } else {
                    alert('Garante no encontrado con el CI: ' + ci);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error al buscar garante');
            }
        }

        function mostrarGarante(garante) {
            document.getElementById('garante_nombre').textContent = garante.nombre || 'N/A';
            document.getElementById('garante_ci_display').textContent = garante.ci;
            document.getElementById('garante_telefono').textContent = garante.telefono || 'N/A';
            document.getElementById('id_garante_referencia').value = garante.ci;
            document.getElementById('garante_info').classList.remove('hidden');
        }

        function limpiarGarante() {
            document.getElementById('garante_ci').value = '';
            document.getElementById('garante_info').classList.add('hidden');
            document.getElementById('id_garante_referencia').value = '';
        }

        function toggleFormularioGarante() {
            const formulario = document.getElementById('formularioNuevoGarante');
            const ciBuscado = document.getElementById('garante_ci').value.trim();

            if (formulario.classList.contains('hidden')) {
                formulario.classList.remove('hidden');
                // Pre-llenar CI si se ingresó
                if (ciBuscado) {
                    document.getElementById('nuevo_garante_ci').value = ciBuscado;
                }
            } else {
                formulario.classList.add('hidden');
                limpiarFormularioGarante();
            }
        }

        function limpiarFormularioGarante() {
            document.getElementById('nuevo_garante_ci').value = '';
            document.getElementById('nuevo_garante_lugar_expedicion').value = '';
            document.getElementById('nuevo_garante_nombres').value = '';
            document.getElementById('nuevo_garante_apellidos').value = '';
            document.getElementById('nuevo_garante_fecha_nacimiento').value = '';
            document.getElementById('nuevo_garante_nacionalidad').value = 'Boliviana';
            document.getElementById('nuevo_garante_estado_civil').value = '';
            document.getElementById('nuevo_garante_telefono').value = '';
            document.getElementById('nuevo_garante_correo').value = '';
            document.getElementById('nuevo_garante_profesion').value = '';
            document.getElementById('nuevo_garante_empresa').value = '';
            document.getElementById('nuevo_garante_direccion').value = '';
            const radios = document.querySelectorAll('input[name="garante_sexo"]');
            radios.forEach(radio => radio.checked = false);
        }

        async function registrarNuevoGarante() {
            const ci = document.getElementById('nuevo_garante_ci').value.trim();
            const lugarExpedicion = document.getElementById('nuevo_garante_lugar_expedicion').value;
            const nombres = document.getElementById('nuevo_garante_nombres').value.trim();
            const apellidos = document.getElementById('nuevo_garante_apellidos').value.trim();
            const fechaNacimiento = document.getElementById('nuevo_garante_fecha_nacimiento').value;
            const nacionalidad = document.getElementById('nuevo_garante_nacionalidad').value;
            const estadoCivil = document.getElementById('nuevo_garante_estado_civil').value;
            const telefono = document.getElementById('nuevo_garante_telefono').value.trim();
            const correo = document.getElementById('nuevo_garante_correo').value.trim();
            const profesion = document.getElementById('nuevo_garante_profesion').value.trim();
            const empresa = document.getElementById('nuevo_garante_empresa').value.trim();
            const direccion = document.getElementById('nuevo_garante_direccion').value.trim();
            const sexo = document.querySelector('input[name="garante_sexo"]:checked')?.value;

            // Validar campos requeridos
            if (!ci || !nombres || !apellidos || !sexo || !fechaNacimiento) {
                alert('Por favor complete todos los campos obligatorios (CI, Nombres, Apellidos, Sexo y Fecha de Nacimiento)');
                return;
            }

            const data = {
                ci: ci,
                lugar_expedicion: lugarExpedicion,
                nombres: nombres,
                apellidos: apellidos,
                sexo: sexo,
                fecha_nacimiento: fechaNacimiento,
                nacionalidad: nacionalidad,
                estado_civil: estadoCivil,
                telefono: telefono,
                correo: correo,
                profesion: profesion,
                empresa_trabajo: empresa,
                direccion_residencia: direccion
            };

            try {
                const response = await fetch('/api/registrar-garante', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (result.success) {
                    mostrarGarante(result.garante);
                    toggleFormularioGarante();
                    alert('Garante registrado exitosamente');
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error al registrar garante');
            }
        }
    </script>
@endsection
