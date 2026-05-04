@extends('layouts.app')
@section('content')
<div class="p-6 bg-gray-50/50 min-h-screen">
    <!-- Header -->
    <div class="flex justify-between items-end mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Nuevo Ingreso</h1>
            <p class="text-sm text-gray-500">Recepción - Registro de pacientes</p>
        </div>
        <a href="{{ route('reception') }}" class="flex items-center px-4 py-2 bg-white border border-gray-200 text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition-all shadow-sm">
            <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver a Recepción
        </a>
    </div>

    <!-- Formulario General -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <form id="formIngresoGeneral" onsubmit="procesarIngreso(event); return false;">
            @csrf

            <!-- PASO 1: DATOS DEL PACIENTE -->
            <div class="mb-8">
                <div class="flex items-center mb-4">
                    <div class="w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold text-sm mr-3">1</div>
                    <h2 class="text-lg font-bold text-gray-800">Datos del Paciente</h2>
                </div>

                <!-- Búsqueda por CI -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">C.I. Paciente *</label>
                    <div class="flex gap-3">
                        <input type="text" id="paciente_ci" name="ci" placeholder="Número de CI del paciente"
                               class="flex-1 border border-gray-200 rounded-xl px-4 py-3 text-sm bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all">
                        <button type="button" onclick="buscarPaciente()" class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-6 py-3 rounded-xl transition-colors text-sm">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            Buscar
                        </button>
                    </div>
                </div>

                <!-- ID Temporal (solo para emergencia, controlado por JS) -->
                <div id="temp_id_container" class="mb-4 hidden">
                    <label class="flex items-center cursor-pointer mb-2">
                        <input type="checkbox" id="usar_temp_id" name="usar_temp_id" value="1" class="sr-only peer" onchange="toggleTempId()">
                        <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-red-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-red-600"></div>
                        <span class="ms-3 text-sm font-medium text-gray-700">Usar ID Temporal (Paciente sin documento)</span>
                    </label>
                    <div id="temp_id_field" class="hidden mt-2">
                        <input type="text" id="temp_id" name="temp_id" placeholder="Ej: TEMP-001"
                               class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm bg-white focus:outline-none focus:border-red-500 focus:ring-2 focus:ring-red-100 transition-all">
                        <p class="text-xs text-gray-500 mt-1">Se generará automáticamente si se deja vacío</p>
                    </div>
                </div>

                <!-- Datos del Paciente (nuevo o existente) -->
                <div id="datos_paciente_container" class="hidden bg-blue-50 rounded-xl p-5 border border-blue-100">
                    <div id="paciente_encontrado_card" class="hidden mb-4">
                        <div class="bg-green-50 border border-green-200 rounded-xl p-4">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span class="font-semibold text-green-800">Paciente Encontrado</span>
                            </div>
                            <div class="mt-2 text-sm text-gray-700">
                                <p><strong>Nombre:</strong> <span id="paciente_nombre_encontrado"></span></p>
                                <p><strong>CI:</strong> <span id="paciente_ci_encontrado"></span></p>
                            </div>
                        </div>
                    </div>

                    <h3 class="text-md font-semibold text-gray-800 mb-4">Datos Personales</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nombres *</label>
                            <input type="text" name="nombres" id="nombres" required
                                   class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Apellidos *</label>
                            <input type="text" name="apellidos" id="apellidos" required
                                   class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Sexo *</label>
                            <select name="sexo" id="sexo" required
                                    class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all">
                                <option value="">Seleccione...</option>
                                <option value="M">Masculino</option>
                                <option value="F">Femenino</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Nacimiento</label>
                            <input type="date" name="fecha_nacimiento" id="fecha_nacimiento"
                                   class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Lugar de Expedición CI</label>
                            <select name="lugar_expedicion" id="lugar_expedicion"
                                    class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all">
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
                            <select name="nacionalidad" id="nacionalidad"
                                    class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all">
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
                            <select name="estado_civil" id="estado_civil"
                                    class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all">
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
                            <input type="tel" name="telefono" id="telefono"
                                   class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Correo</label>
                            <input type="email" name="correo" id="correo"
                                   class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Profesión</label>
                            <input type="text" name="profesion" id="profesion"
                                   class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Empresa de Trabajo</label>
                            <input type="text" name="empresa_trabajo" id="empresa_trabajo"
                                   class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Dirección</label>
                            <input type="text" name="direccion" id="direccion"
                                   class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all">
                        </div>
                    </div>
                </div>
            </div>

            <!-- PASO 2: TIPO DE INGRESO -->
            <div class="mb-8">
                <div class="flex items-center mb-4">
                    <div class="w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold text-sm mr-3">2</div>
                    <h2 class="text-lg font-bold text-gray-800">Tipo de Ingreso *</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Consulta Externa -->
                    <label class="relative flex flex-col p-5 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-green-400 transition-all has-[:checked]:border-green-500 has-[:checked]:bg-green-50">
                        <input type="radio" name="tipo_ingreso" value="consulta_externa" class="sr-only peer" onchange="seleccionarTipoIngreso('consulta_externa')">
                        <div class="flex items-center mb-3">
                            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mr-3">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <span class="font-bold text-gray-800">Consulta Externa</span>
                                <p class="text-xs text-gray-500">Atención ambulatoria</p>
                            </div>
                        </div>
                        <div class="absolute top-3 right-3 w-5 h-5 rounded-full border-2 border-gray-300 peer-checked:border-green-500 peer-checked:bg-green-500"></div>
                    </label>

                    <!-- Emergencia -->
                    <label class="relative flex flex-col p-5 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-red-400 transition-all has-[:checked]:border-red-500 has-[:checked]:bg-red-50">
                        <input type="radio" name="tipo_ingreso" value="emergencia" class="sr-only peer" onchange="seleccionarTipoIngreso('emergencia')">
                        <div class="flex items-center mb-3">
                            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mr-3">
                                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            </div>
                            <div>
                                <span class="font-bold text-gray-800">Emergencia</span>
                                <p class="text-xs text-gray-500">Atención urgente</p>
                            </div>
                        </div>
                        <div class="absolute top-3 right-3 w-5 h-5 rounded-full border-2 border-gray-300 peer-checked:border-red-500 peer-checked:bg-red-500"></div>
                    </label>

                    <!-- Internación -->
                    <label class="relative flex flex-col p-5 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-blue-400 transition-all has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50">
                        <input type="radio" name="tipo_ingreso" value="internacion" class="sr-only peer" onchange="seleccionarTipoIngreso('internacion')">
                        <div class="flex items-center mb-3">
                            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                                </svg>
                            </div>
                            <div>
                                <span class="font-bold text-gray-800">Internación</span>
                                <p class="text-xs text-gray-500">Hospitalización</p>
                            </div>
                        </div>
                        <div class="absolute top-3 right-3 w-5 h-5 rounded-full border-2 border-gray-300 peer-checked:border-blue-500 peer-checked:bg-blue-500"></div>
                    </label>
                </div>

            </div>

            <!-- PASO 3: GARANTE (Obligatorio para internación, opcional para emergencia) -->
            <div id="seccion_garante" class="mb-8 hidden">
                <div class="flex items-center mb-4">
                    <div class="w-8 h-8 bg-amber-600 text-white rounded-full flex items-center justify-center font-bold text-sm mr-3">3</div>
                    <h2 class="text-lg font-bold text-gray-800">
                        Datos del Garante
                        <span id="garante_obligatorio" class="text-sm font-normal text-red-600 ml-2">(Obligatorio para internación)</span>
                    </h2>
                </div>

                <div class="bg-amber-50 rounded-xl p-5 border border-amber-100">
                    <!-- Búsqueda de garante -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">C.I. del Garante</label>
                        <div class="flex gap-3">
                            <input type="text" id="garante_ci" name="garante_ci" placeholder="CI del garante"
                                   class="flex-1 border border-gray-200 rounded-xl px-4 py-3 text-sm bg-white focus:outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100 transition-all">
                            <button type="button" onclick="buscarGarante()" class="bg-amber-600 hover:bg-amber-700 text-white font-medium px-6 py-3 rounded-xl transition-colors text-sm">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                Buscar
                            </button>
                        </div>
                    </div>

                    <!-- Info garante encontrado -->
                    <div id="garante_info" class="hidden mb-4">
                        <div class="bg-green-50 border border-green-200 rounded-xl p-4">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span class="font-semibold text-green-800">Garante encontrado</span>
                            </div>
                            <p class="text-sm text-gray-700 mt-1"><strong>Nombre:</strong> <span id="garante_nombre_encontrado"></span></p>
                        </div>
                    </div>

                    <!-- Formulario garante -->
                    <div id="formulario_garante" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nombres *</label>
                            <input type="text" name="garante_nombres" id="garante_nombres"
                                   class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100 transition-all">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Apellidos *</label>
                            <input type="text" name="garante_apellidos" id="garante_apellidos"
                                   class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100 transition-all">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Sexo *</label>
                            <select name="garante_sexo" id="garante_sexo"
                                    class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100 transition-all">
                                <option value="">Seleccione...</option>
                                <option value="M">Masculino</option>
                                <option value="F">Femenino</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Nacimiento</label>
                            <input type="date" name="garante_fecha_nacimiento" id="garante_fecha_nacimiento"
                                   class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100 transition-all">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Lugar de Expedición CI</label>
                            <select name="garante_lugar_expedicion" id="garante_lugar_expedicion"
                                    class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100 transition-all">
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
                            <select name="garante_nacionalidad" id="garante_nacionalidad"
                                    class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100 transition-all">
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
                            <select name="garante_estado_civil" id="garante_estado_civil"
                                    class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100 transition-all">
                                <option value="">Seleccione...</option>
                                <option value="Soltero/a">Soltero/a</option>
                                <option value="Casado/a">Casado/a</option>
                                <option value="Divorciado/a">Divorciado/a</option>
                                <option value="Viudo/a">Viudo/a</option>
                                <option value="Unión Libre">Unión Libre</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono *</label>
                            <input type="tel" name="garante_telefono" id="garante_telefono"
                                   class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100 transition-all">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Correo</label>
                            <input type="email" name="garante_correo" id="garante_correo"
                                   class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100 transition-all">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Profesión</label>
                            <input type="text" name="garante_profesion" id="garante_profesion"
                                   class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100 transition-all">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Empresa de Trabajo</label>
                            <input type="text" name="garante_empresa_trabajo" id="garante_empresa_trabajo"
                                   class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100 transition-all">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Dirección</label>
                            <input type="text" name="garante_direccion" id="garante_direccion"
                                   class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none focus:border-amber-500 focus:ring-2 focus:ring-amber-100 transition-all">
                        </div>
                    </div>
                </div>
            </div>

            <!-- PASO 4: SEGURO -->
            <div class="mb-8">
                <div class="flex items-center mb-4">
                    <div class="w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold text-sm mr-3" id="numero_seguro">3</div>
                    <h2 class="text-lg font-bold text-gray-800">Seguro / Cobertura</h2>
                </div>

                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <select name="seguro_id" id="seguro_id"
                            class="w-full pl-10 pr-10 py-3 border border-gray-200 rounded-xl text-sm bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all appearance-none cursor-pointer"
                            onchange="mostrarInfoSeguro()">
                        <option value="">Particular (Sin seguro) - Pago directo</option>
                        @foreach($seguros as $seguro)
                            <option value="{{ $seguro->id }}" data-descripcion="{{ $seguro->descripcion_cobertura }}">
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
                    </div>
                </div>
            </div>

            <!-- BOTONES DE ACCIÓN -->
            <div class="flex justify-end gap-3 pt-6 border-t border-gray-200">
                <a href="{{ route('reception') }}" class="px-6 py-3 border border-gray-200 rounded-xl text-gray-700 font-medium hover:bg-gray-50 transition-colors text-sm">
                    Cancelar
                </a>
                <button type="submit" id="btn_crear" class="px-6 py-3 bg-blue-600 text-white rounded-xl font-medium hover:bg-blue-700 transition-colors flex items-center text-sm shadow-md disabled:bg-gray-400 disabled:cursor-not-allowed">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Crear Ingreso
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let tipoIngresoSeleccionado = null;
let pacienteEncontrado = false;

// Buscar paciente por CI
async function buscarPaciente() {
    const ci = document.getElementById('paciente_ci').value;

    if (ci.length < 3) {
        alert('Ingrese al menos 3 caracteres para buscar');
        return;
    }

    const btn = event.target;
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<svg class="animate-spin w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>Buscando...';

    try {
        const response = await fetch('/reception/ingreso-general/buscar-paciente?ci=' + encodeURIComponent(ci));
        const data = await response.json();

        document.getElementById('datos_paciente_container').classList.remove('hidden');

        if (data.success) {
            pacienteEncontrado = true;
            document.getElementById('paciente_encontrado_card').classList.remove('hidden');
            document.getElementById('paciente_nombre_encontrado').textContent = data.paciente.nombre;
            document.getElementById('paciente_ci_encontrado').textContent = data.paciente.ci;

            // Llenar campos
            const nombreParts = data.paciente.nombre ? data.paciente.nombre.split(' ') : ['', ''];
            document.getElementById('nombres').value = nombreParts[0] || '';
            document.getElementById('apellidos').value = nombreParts.slice(1).join(' ') || '';
            document.getElementById('sexo').value = data.paciente.sexo || '';
            document.getElementById('fecha_nacimiento').value = data.paciente.fecha_nacimiento || '';
            document.getElementById('lugar_expedicion').value = data.paciente.lugar_expedicion || '';
            document.getElementById('nacionalidad').value = data.paciente.nacionalidad || 'Boliviana';
            document.getElementById('estado_civil').value = data.paciente.estado_civil || '';
            document.getElementById('telefono').value = data.paciente.telefono || '';
            document.getElementById('correo').value = data.paciente.correo || '';
            document.getElementById('profesion').value = data.paciente.profesion || '';
            document.getElementById('empresa_trabajo').value = data.paciente.empresa_trabajo || '';
            document.getElementById('direccion').value = data.paciente.direccion || '';

            // Deshabilitar campos para paciente existente
            setCamposPacienteReadOnly(true);
        } else {
            pacienteEncontrado = false;
            document.getElementById('paciente_encontrado_card').classList.add('hidden');

            // Limpiar campos
            limpiarCamposPaciente();

            // Habilitar campos
            setCamposPacienteReadOnly(false);

            // Focus en nombres
            document.getElementById('nombres').focus();
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error al buscar paciente');
    } finally {
        btn.disabled = false;
        btn.innerHTML = originalText;
    }
}

// Toggle ID Temporal
function toggleTempId() {
    const checkbox = document.getElementById('usar_temp_id');
    const field = document.getElementById('temp_id_field');
    const ciInput = document.getElementById('paciente_ci');
    const datosContainer = document.getElementById('datos_paciente_container');

    if (checkbox.checked) {
        field.classList.remove('hidden');
        ciInput.value = '';
        ciInput.disabled = true;
        datosContainer.classList.add('hidden');

        // Quitar required de todos los campos de datos personales
        datosContainer.querySelectorAll('input, select').forEach(field => {
            field.removeAttribute('required');
        });

        // Generar ID temporal
        const date = new Date();
        const tempId = 'TEMP-' + date.getFullYear() + String(date.getMonth()+1).padStart(2,'0') + String(date.getDate()).padStart(2,'0') + '-' + Math.floor(Math.random() * 999).toString().padStart(3, '0');
        document.getElementById('temp_id').value = tempId;
    } else {
        field.classList.add('hidden');
        ciInput.disabled = false;
        document.getElementById('temp_id').value = '';

        // Mostrar datos del paciente y agregar required a campos obligatorios
        datosContainer.classList.remove('hidden');

        const nombresField = document.getElementById('nombres');
        const apellidosField = document.getElementById('apellidos');
        const sexoField = document.getElementById('sexo');

        if (nombresField) nombresField.setAttribute('required', '');
        if (apellidosField) apellidosField.setAttribute('required', '');
        if (sexoField) sexoField.setAttribute('required', '');

        document.getElementById('paciente_ci').focus();
    }
}

// Seleccionar tipo de ingreso
function seleccionarTipoIngreso(tipo) {
    tipoIngresoSeleccionado = tipo;

    // Ocultar todos los campos específicos
    document.getElementById('seccion_garante').classList.add('hidden');
    document.getElementById('temp_id_container').classList.add('hidden');

    // Actualizar número del paso de seguro
    document.getElementById('numero_seguro').textContent = '3';

    // Mostrar campos según tipo
    switch(tipo) {
        case 'consulta_externa':
            // No hay campos adicionales
            break;

        case 'emergencia':
            // Solo ID temporal opcional
            document.getElementById('temp_id_container').classList.remove('hidden');
            break;

        case 'internacion':
            // Solo garante obligatorio
            document.getElementById('seccion_garante').classList.remove('hidden');
            document.getElementById('garante_obligatorio').classList.remove('hidden');
            document.getElementById('numero_seguro').textContent = '4';
            break;
    }
}

// Funciones auxiliares para manejo de campos del paciente
function setCamposPacienteReadOnly(readonly) {
    const campos = ['nombres', 'apellidos', 'fecha_nacimiento', 'lugar_expedicion', 'nacionalidad', 'estado_civil', 'telefono', 'correo', 'profesion', 'empresa_trabajo', 'direccion'];
    campos.forEach(id => {
        const el = document.getElementById(id);
        if (el) el.readOnly = readonly;
    });
    const sexo = document.getElementById('sexo');
    if (sexo) sexo.disabled = readonly;
}

function limpiarCamposPaciente() {
    const campos = ['nombres', 'apellidos', 'sexo', 'fecha_nacimiento', 'lugar_expedicion', 'nacionalidad', 'estado_civil', 'telefono', 'correo', 'profesion', 'empresa_trabajo', 'direccion'];
    campos.forEach(id => {
        const el = document.getElementById(id);
        if (el) el.value = '';
    });
}

function setCamposGaranteReadOnly(readonly) {
    const campos = ['garante_nombres', 'garante_apellidos', 'garante_fecha_nacimiento', 'garante_lugar_expedicion', 'garante_nacionalidad', 'garante_estado_civil', 'garante_telefono', 'garante_correo', 'garante_profesion', 'garante_empresa_trabajo', 'garante_direccion'];
    campos.forEach(id => {
        const el = document.getElementById(id);
        if (el) el.readOnly = readonly;
    });
    const sexo = document.getElementById('garante_sexo');
    if (sexo) sexo.disabled = readonly;
}

function limpiarCamposGarante() {
    const campos = ['garante_nombres', 'garante_apellidos', 'garante_sexo', 'garante_fecha_nacimiento', 'garante_lugar_expedicion', 'garante_nacionalidad', 'garante_estado_civil', 'garante_telefono', 'garante_correo', 'garante_profesion', 'garante_empresa_trabajo', 'garante_direccion'];
    campos.forEach(id => {
        const el = document.getElementById(id);
        if (el) el.value = '';
    });
}

// Buscar garante
async function buscarGarante() {
    const ci = document.getElementById('garante_ci').value;

    if (!ci) {
        alert('Ingrese el CI del garante');
        return;
    }

    try {
        const response = await fetch('/reception/ingreso-general/buscar-garante?ci=' + encodeURIComponent(ci));
        const data = await response.json();

        if (data.success) {
            document.getElementById('garante_info').classList.remove('hidden');
            document.getElementById('garante_nombre_encontrado').textContent = data.garante.nombre;

            // Llenar campos
            const nombreParts = data.garante.nombre ? data.garante.nombre.split(' ') : ['', ''];
            document.getElementById('garante_nombres').value = nombreParts[0] || '';
            document.getElementById('garante_apellidos').value = nombreParts.slice(1).join(' ') || '';
            document.getElementById('garante_sexo').value = data.garante.sexo || '';
            document.getElementById('garante_fecha_nacimiento').value = data.garante.fecha_nacimiento || '';
            document.getElementById('garante_lugar_expedicion').value = data.garante.lugar_expedicion || '';
            document.getElementById('garante_nacionalidad').value = data.garante.nacionalidad || 'Boliviana';
            document.getElementById('garante_estado_civil').value = data.garante.estado_civil || '';
            document.getElementById('garante_telefono').value = data.garante.telefono || '';
            document.getElementById('garante_correo').value = data.garante.correo || '';
            document.getElementById('garante_profesion').value = data.garante.profesion || '';
            document.getElementById('garante_empresa_trabajo').value = data.garante.empresa_trabajo || '';
            document.getElementById('garante_direccion').value = data.garante.direccion || '';

            // Deshabilitar campos para garante existente
            setCamposGaranteReadOnly(true);
        } else {
            document.getElementById('garante_info').classList.add('hidden');
            // Limpiar campos para nuevo garante
            limpiarCamposGarante();
            // Habilitar campos
            setCamposGaranteReadOnly(false);
            document.getElementById('garante_nombres').focus();
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error al buscar garante');
    }
}

// Mostrar info del seguro
function mostrarInfoSeguro() {
    const select = document.getElementById('seguro_id');
    const infoDiv = document.getElementById('info_seguro');
    const descripcionP = document.getElementById('descripcion_seguro');

    if (select.value) {
        const option = select.selectedOptions[0];
        const descripcion = option.getAttribute('data-descripcion');
        descripcionP.textContent = descripcion || 'Seguro seleccionado';
        infoDiv.classList.remove('hidden');
    } else {
        infoDiv.classList.add('hidden');
    }
}

// Procesar ingreso
async function procesarIngreso(event) {
    event.preventDefault();

    // Validaciones básicas
    const ci = document.getElementById('paciente_ci').value;
    const usarTempId = document.getElementById('usar_temp_id')?.checked;
    const tempId = document.getElementById('temp_id')?.value;

    if (!usarTempId && !ci) {
        alert('Ingrese el CI del paciente o use ID temporal');
        return;
    }

    // Solo validar datos del paciente si NO se usa ID temporal
    if (!usarTempId && (!document.getElementById('nombres').value || !document.getElementById('apellidos').value || !document.getElementById('sexo').value)) {
        alert('Complete los datos del paciente (nombres, apellidos, sexo)');
        return;
    }

    if (!tipoIngresoSeleccionado) {
        alert('Seleccione un tipo de ingreso');
        return;
    }

    // Validaciones específicas
    if (tipoIngresoSeleccionado === 'internacion') {
        if (!document.getElementById('garante_ci').value || !document.getElementById('garante_nombres').value || !document.getElementById('garante_apellidos').value) {
            alert('La internación requiere garante obligatorio. Complete los datos del garante.');
            return;
        }
    }

    // Preparar datos
    const formData = new FormData(event.target);
    const data = Object.fromEntries(formData.entries());

    // Agregar temp_id si aplica
    if (usarTempId) {
        data.ci = tempId || data.ci;
        data.usar_temp_id = '1';
    }

    const btn = document.getElementById('btn_crear');
    btn.disabled = true;
    btn.innerHTML = '<svg class="animate-spin w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>Procesando...';

    try {
        const response = await fetch('/reception/ingreso-general/procesar', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        if (result.success) {
            alert(result.message);
            if (result.redirect_url) {
                window.location.href = result.redirect_url;
            } else {
                window.location.href = '{{ route("reception") }}';
            }
        } else {
            alert(result.message || 'Error al procesar el ingreso');
            btn.disabled = false;
            btn.innerHTML = '<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>Crear Ingreso';
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error al procesar el ingreso');
        btn.disabled = false;
        btn.innerHTML = '<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>Crear Ingreso';
    }
}
</script>
@endsection
