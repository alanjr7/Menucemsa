@extends('layouts.app')

@section('content')
<div class="w-full p-4 sm:p-6 bg-slate-100 min-h-screen text-slate-800">

    <!-- Page Header (Estilo Ejecutivo Clínico con 1px de redondeo) -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 pb-4 border-b-2 border-slate-300 gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2 py-0.5 bg-blue-900 text-white text-xs font-bold uppercase tracking-wider rounded-[1px]">Quirófano</span>
                 </div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight mt-1">PROGRAMACIÓN DE CIRUGIAS                                                                                                         </h1>
        </div>
        <a href="{{ route('quirofano.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 text-xs font-bold uppercase tracking-wider transition-colors shadow-xs rounded-[1px]">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver al Panel
        </a>
    </div>

    <!-- Notification Toast (1px de redondeo) -->
    <div id="toastNotification" class="fixed top-5 right-5 z-50 transform transition-all duration-200 translate-y-[-100%] opacity-0 pointer-events-none rounded-[1px]">
        <div class="bg-slate-900 text-white px-5 py-3 border-l-4 border-emerald-500 shadow-2xl flex items-center gap-3 rounded-[1px]">
            <span id="toastIcon" class="text-base"></span>
            <span id="toastMsg" class="text-xs font-bold tracking-wide uppercase"></span>
        </div>
    </div>

    <form id="citaForm" class="space-y-6">
        @csrf

        <!-- SECCIÓN 1: DATOS DEL PACIENTE (INTERNACIÓN) -->
        <div class="bg-white border border-slate-300 shadow-xs rounded-[1px] p-5">
            <div class="flex flex-wrap items-center justify-between border-b border-slate-200 pb-3 mb-4 gap-2">
                <div class="flex items-center gap-2">
                    <span class="w-6 h-6 bg-blue-700 text-white text-xs font-bold flex items-center justify-center rounded-full">1</span>
                    <h2 class="text-sm font-black text-slate-900 uppercase tracking-wide">Datos del Paciente</h2>
                </div>
                <div class="flex items-center gap-2">
                    
                </div>
            </div>

            <div class="space-y-4">
                <!-- Buscador de Paciente -->
                <div class="relative" id="contenedor_buscar_paciente">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Buscar o Registrar Paciente <span class="text-red-600">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <input type="text" id="buscar_paciente" 
                               class="w-full pl-9 pr-4 py-2.5 text-sm bg-white border border-slate-300 text-slate-900 placeholder-slate-400 focus:bg-white focus:border-blue-700 focus:outline-hidden rounded-[1px]" 
                               placeholder="Escriba nombre o documento para buscar, o tipee para agregar nuevo..." autocomplete="off">
                    </div>
                    
                    <!-- Dropdown de resultados de pacientes -->
                    <div id="resultados_paciente" class="hidden absolute z-50 w-full mt-1 bg-white border-2 border-slate-400 shadow-2xl max-h-60 overflow-y-auto divide-y divide-slate-200 rounded-[1px]"></div>
                </div>

                <!-- Input oculto para paciente_id -->
                <input type="hidden" name="paciente_id" id="paciente_id" required>

                <!-- Tarjeta de Paciente Seleccionado -->
                <div id="info_paciente" class="hidden bg-slate-50 border border-slate-300 p-4 rounded-[1px]">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-9 h-9 bg-blue-700 text-white flex items-center justify-center font-bold text-sm rounded-[1px]">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <p id="nombre_paciente" class="font-black text-slate-900 text-sm uppercase tracking-wide"></p>
                                    <span class="px-2 py-0.5 bg-blue-800 text-white text-[10px] font-bold uppercase rounded-[1px]">Internación</span>
                                </div>
                                <p id="ci_paciente_display" class="text-xs text-slate-600 font-mono mt-0.5"></p>
                            </div>
                        </div>
                        <button type="button" onclick="limpiarPaciente()" class="px-3 py-1.5 bg-white border border-slate-300 text-red-700 hover:bg-red-50 text-xs font-bold uppercase tracking-wider transition-colors rounded-[1px]">
                            Cambiar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECCIÓN 2: EQUIPO MÉDICO QUIRÚRGICO -->
        <div class="bg-white border border-slate-300 shadow-xs rounded-[1px] p-5">
            <div class="border-b border-slate-200 pb-3 mb-4">
                <div class="flex items-center gap-2">
                    <span class="w-6 h-6 bg-purple-700 text-white text-xs font-bold flex items-center justify-center rounded-full">2</span>
                    <h2 class="text-sm font-black text-slate-900 uppercase tracking-wide">Equipo Médico Quirúrgico</h2>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Cirujano Principal -->
                <div class="space-y-3">
                    <div class="relative" id="contenedor_buscar_cirujano">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Cirujano Principal <span class="text-red-600">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            <input type="text" id="buscar_cirujano" 
                                   class="w-full pl-9 pr-4 py-2.5 text-sm bg-white border border-slate-300 text-slate-900 placeholder-slate-400 focus:bg-white focus:border-purple-700 focus:outline-hidden rounded-[1px]" 
                                   placeholder="Escriba nombre de cirujano para buscar o crear..." autocomplete="off">
                        </div>
                        
                        <!-- Dropdown de resultados de cirujanos -->
                        <div id="resultados_cirujano" class="hidden absolute z-50 w-full mt-1 bg-white border-2 border-slate-400 shadow-2xl max-h-60 overflow-y-auto divide-y divide-slate-200 rounded-[1px]"></div>
                    </div>

                    <!-- Input oculto para ci_cirujano -->
                    <input type="hidden" name="ci_cirujano" id="ci_cirujano" required>

                    <!-- Tarjeta de Cirujano Seleccionado -->
                    <div id="info_cirujano" class="hidden bg-slate-50 border border-slate-300 p-4 rounded-[1px]">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-9 h-9 bg-purple-700 text-white flex items-center justify-center font-bold text-sm rounded-[1px]">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                <div>
                                    <p id="nombre_cirujano" class="font-black text-slate-900 text-sm uppercase tracking-wide"></p>
                                    <p id="ci_cirujano_display" class="text-xs text-slate-600 font-mono mt-0.5"></p>
                                </div>
                            </div>
                            <button type="button" onclick="limpiarCirujano()" class="px-3 py-1.5 bg-white border border-slate-300 text-red-700 hover:bg-red-50 text-xs font-bold uppercase tracking-wider transition-colors rounded-[1px]">
                                Cambiar
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Personal Asistente (Sin CIs obligatorios) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Nombre del Instrumentista
                        </label>
                        <input type="text" name="nombre_instrumentista" id="nombre_instrumentista" 
                               class="w-full px-3 py-2.5 text-sm bg-white border border-slate-300 text-slate-900 focus:border-slate-700 focus:outline-hidden rounded-[1px]" 
                               placeholder="Nombre completo">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Nombre del Anestesiólogo
                        </label>
                        <input type="text" name="nombre_anestesiologo" id="nombre_anestesiologo" 
                               class="w-full px-3 py-2.5 text-sm bg-white border border-slate-300 text-slate-900 focus:border-slate-700 focus:outline-hidden rounded-[1px]" 
                               placeholder="Nombre completo">
                    </div>
                </div>
            </div>
        </div>

        <!-- SECCIÓN 3: PROCEDIMIENTO, QUIRÓFANO Y ANESTESIA -->
        <div class="bg-white border border-slate-300 shadow-xs rounded-[1px] p-5">
            <div class="border-b border-slate-200 pb-3 mb-4">
                <div class="flex items-center gap-2">
                    <span class="w-6 h-6 bg-emerald-700 text-white text-xs font-bold flex items-center justify-center rounded-full">3</span>
                    <h2 class="text-sm font-black text-slate-900 uppercase tracking-wide">Procedimiento y Anestesia</h2>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Quirófano -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Quirófano <span class="text-red-600">*</span>
                    </label>
                    <select name="nro_quirofano" id="nro_quirofano" class="w-full px-3 py-2.5 text-sm bg-white border border-slate-300 text-slate-900 focus:border-emerald-700 focus:outline-hidden rounded-[1px] cursor-pointer" required>
                        <option value="">Cargando quirófanos...</option>
                    </select>
                </div>

                <!-- Tipo de Cirugía -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Tipo de Cirugía <span class="text-red-600">*</span>
                    </label>
                    <select name="tipo_cirugia" id="tipo_cirugia" class="w-full px-3 py-2.5 text-sm bg-white border border-slate-300 text-slate-900 focus:border-emerald-700 focus:outline-hidden rounded-[1px] cursor-pointer" required>
                        <option value="">Seleccionar tipo...</option>
                        @foreach($tiposCirugia as $tipo)
                            <option value="{{ $tipo->nombre }}" data-duracion="{{ $tipo->duracion_minutos }}" data-costo="{{ $tipo->costo_base }}" class="capitalize">
                                {{ ucfirst($tipo->nombre) }} - {{ $tipo->duracion_formateada }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Tipo de Anestesia -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Tipo de Anestesia <span class="text-red-600">*</span>
                    </label>
                    <select name="tipo_anestesia" id="tipo_anestesia" class="w-full px-3 py-2.5 text-sm bg-white border border-slate-300 text-slate-900 focus:border-emerald-700 focus:outline-hidden rounded-[1px] cursor-pointer" required>
                        <option value="">Seleccionar anestesia...</option>
                        <option value="Anestesia general">Anestesia general</option>
                        <option value="Anestesia local + sedación">Anestesia local + sedación</option>
                        <option value="Anestesia epidural o raquídea">Anestesia epidural o raquídea</option>
                    </select>
                </div>

                <!-- Precio Base Cirugía -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Precio Cirugía (Bs) <span class="text-red-600">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-slate-500 font-bold text-xs font-mono">Bs</span>
                        </div>
                        <input type="text" inputmode="decimal" name="costo_base" id="costo_base"
                               class="w-full pl-9 pr-3 py-2.5 text-sm font-bold font-mono bg-white border border-slate-300 text-slate-900 focus:border-emerald-700 focus:outline-hidden rounded-[1px]"
                               placeholder="0.00" required>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECCIÓN 4: EQUIPAMIENTO ESPECIAL Y PRECIO EN CATÁLOGO -->
        <div class="bg-white border border-slate-300 shadow-xs rounded-[1px] p-5">
            <div class="flex flex-wrap items-center justify-between border-b border-slate-200 pb-3 mb-4 gap-2">
                <div class="flex items-center gap-2">
                    <span class="w-6 h-6 bg-amber-600 text-white text-xs font-bold flex items-center justify-center rounded-full">4</span>
                    <h2 class="text-sm font-black text-slate-900 uppercase tracking-wide">Equipamiento Especial de Quirófano</h2>
                </div>
           </div>

            <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                <!-- Selector de Equipamiento -->
                <div class="md:col-span-5">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Equipamiento Requerido
                    </label>
                    <select name="equipamiento_nombre" id="equipamiento_nombre" class="w-full px-3 py-2.5 text-sm bg-white border border-slate-300 text-slate-900 focus:border-amber-600 focus:outline-hidden rounded-[1px] cursor-pointer">
                        <option value="">Ninguno</option>
                        <option value="Arco en C (C-Arm)">Arco en C (C-Arm)</option>
                        <option value="Torre de lámparas">Torre de lámparas</option>
                    </select>
                </div>

                <!-- Input Decimal 12,2 -->
                <div class="md:col-span-4">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Precio Equipamiento (Bs)
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-slate-500 font-bold text-xs font-mono">Bs</span>
                        </div>
                        <input type="text" inputmode="decimal" name="equipamiento_precio" id="equipamiento_precio"
                               class="w-full pl-9 pr-3 py-2.5 text-sm font-bold font-mono bg-white border border-slate-300 text-slate-900 focus:border-amber-600 focus:outline-hidden rounded-[1px]"
                               placeholder="0.00" value="0.00">
                    </div>
                </div>

                <!-- Botón Guardar/Editar Catálogo -->
                <div class="md:col-span-3">
                    <button type="button" id="btnGuardarPrecioEquipo" 
                            class="w-full py-2.5 px-4 bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs uppercase tracking-wider border border-amber-700 transition-colors flex items-center justify-center gap-2 rounded-[1px]"
                            title="Guardar o editar el precio de este equipo en el catálogo para futuras cirugías">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                        </svg>
                        <span>Guardar en Catálogo</span>
                    </button>
                </div>
            </div>
          
        </div>

        <!-- SECCIÓN 5: PROGRAMACIÓN HORARIA Y OBSERVACIONES -->
        <div class="bg-white border border-slate-300 shadow-xs rounded-[1px] p-5">
            <div class="border-b border-slate-200 pb-3 mb-4">
                <div class="flex items-center gap-2">
                    <span class="w-6 h-6 bg-indigo-700 text-white text-xs font-bold flex items-center justify-center rounded-full">5</span>
                    <h2 class="text-sm font-black text-slate-900 uppercase tracking-wide">Agenda Temporal y Observaciones Clínicas</h2>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Fecha y Hora -->
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Fecha de Intervención <span class="text-red-600">*</span>
                        </label>
                        <input type="date" name="fecha" id="fecha" 
                               class="w-full px-3 py-2.5 text-sm bg-white border border-slate-300 text-slate-900 focus:border-indigo-700 focus:outline-hidden rounded-[1px]" required>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Hora Estimada de Inicio <span class="text-red-600">*</span>
                        </label>
                        <input type="time" name="hora_inicio_estimada" id="hora_inicio_estimada" 
                               class="w-full px-3 py-2.5 text-sm bg-white border border-slate-300 text-slate-900 focus:border-indigo-700 focus:outline-hidden rounded-[1px]" required>
                    </div>
                </div>

                <!-- Descripción y Observaciones -->
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Descripción del Procedimiento
                        </label>
                        <textarea name="descripcion_cirugia" id="descripcion_cirugia" rows="2" 
                                  class="w-full px-3 py-2 text-sm bg-white border border-slate-300 text-slate-900 focus:border-indigo-700 focus:outline-hidden rounded-[1px]" 
                                  ></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Observaciones Generales
                        </label>
                        <textarea name="observaciones" id="observaciones" rows="2" 
                                  class="w-full px-3 py-2 text-sm bg-white border border-slate-300 text-slate-900 focus:border-indigo-700 focus:outline-hidden rounded-[1px]" 
                                 ></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- BOTONES DE ACCIÓN -->
        <div class="flex items-center justify-end gap-3 pt-2">
            <a href="{{ route('quirofano.index') }}" class="px-6 py-2.5 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 text-xs font-bold uppercase tracking-wider transition-colors rounded-[1px] shadow-xs">
                Cancelar
            </a>
            <button type="submit" id="btnSubmitForm" class="px-7 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold uppercase tracking-wider border border-blue-600 transition-colors flex items-center gap-2 rounded-[1px] shadow-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                </svg>
                <span>Programar Cita Quirúrgica</span>
            </button>
        </div>
    </form>
</div>

<script>
// Estado global
let pacientesData = [];
let medicosData = [];
let equipamientosData = {};

document.addEventListener('DOMContentLoaded', function() {
    // Inicializar fecha por defecto hoy
    const today = new Date().toISOString().split('T')[0];
    const fechaInput = document.getElementById('fecha');
    if (fechaInput && !fechaInput.value) {
        fechaInput.value = today;
    }

    cargarPacientes();
    cargarMedicos();
    cargarEquipamientos();
    cargarQuirofanos();
    inicializarBuscadorPacientes();
    inicializarBuscadorCirujanos();
    inicializarEquipamientos();
    inicializarFormateoMonedas();
    inicializarSubmit();
});

// Toast notification helper (1px de redondeo)
function showToast(message, type = 'success') {
    const toast = document.getElementById('toastNotification');
    const toastMsg = document.getElementById('toastMsg');
    const toastIcon = document.getElementById('toastIcon');

    toastMsg.textContent = message;
    if (type === 'success') {
        toastIcon.innerHTML = '✓';
    } else if (type === 'error') {
        toastIcon.innerHTML = '✕';
    } else {
        toastIcon.innerHTML = 'ℹ';
    }

    toast.classList.remove('translate-y-[-100%]', 'opacity-0', 'pointer-events-none');
    toast.classList.add('translate-y-0', 'opacity-100');

    setTimeout(() => {
        toast.classList.remove('translate-y-0', 'opacity-100');
        toast.classList.add('translate-y-[-100%]', 'opacity-0', 'pointer-events-none');
    }, 3500);
}

// Cargar catálogo de quirófanos
function cargarQuirofanos() {
    fetch('/api/quirofanos-disponibles', {
        headers: { 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        const select = document.getElementById('nro_quirofano');
        select.innerHTML = '<option value="">Seleccionar quirófano...</option>';
        if (data.quirofanos && data.quirofanos.length > 0) {
            data.quirofanos.forEach(q => {
                const opt = document.createElement('option');
                opt.value = q.id;
                opt.textContent = `Quirófano Q${q.id} - ${q.tipo} (${q.estado})`;
                select.appendChild(opt);
            });
        }
    })
    .catch(err => console.warn('Error al cargar quirófanos:', err));
}

// Cargar catálogo de pacientes
async function cargarPacientes() {
    try {
        const res = await fetch('/api/pacientes-lista', { headers: { 'Accept': 'application/json' } });
        const data = await res.json();
        if (data.success) {
            pacientesData = data.pacientes || [];
        }
    } catch (e) {
        console.warn('Error al cargar pacientes:', e);
    }
}

// Cargar catálogo de médicos
async function cargarMedicos() {
    try {
        const res = await fetch('/api/medicos-lista', { headers: { 'Accept': 'application/json' } });
        const data = await res.json();
        if (data.success) {
            medicosData = data.medicos || [];
        }
    } catch (e) {
        console.warn('Error al cargar médicos:', e);
    }
}

// Cargar catálogo de equipamientos
async function cargarEquipamientos() {
    try {
        const res = await fetch('/quirofano/api/equipamientos', { headers: { 'Accept': 'application/json' } });
        const data = await res.json();
        if (data.success && data.equipamientos) {
            equipamientosData = {};
            data.equipamientos.forEach(eq => {
                equipamientosData[eq.nombre] = parseFloat(eq.precio_base) || 0;
            });
        }
    } catch (e) {
        console.warn('Error al cargar equipamientos:', e);
    }
}

// ----------------- BUSCADOR Y CREACIÓN DE PACIENTE -----------------
function inicializarBuscadorPacientes() {
    const input = document.getElementById('buscar_paciente');
    const drop = document.getElementById('resultados_paciente');

    input.addEventListener('input', function() {
        const query = this.value.trim();
        if (query.length === 0) {
            drop.classList.add('hidden');
            return;
        }

        const qLower = query.toLowerCase();
        const matches = pacientesData.filter(p => 
            (p.nombre || '').toLowerCase().includes(qLower) ||
            (p.ci && p.ci.toString().includes(qLower)) ||
            (p.temp_code && p.temp_code.toLowerCase().includes(qLower))
        ).slice(0, 8);

        renderResultadosPacientes(matches, query);
    });

    document.addEventListener('click', function(e) {
        if (!e.target.closest('#contenedor_buscar_paciente')) {
            drop.classList.add('hidden');
        }
    });
}

function renderResultadosPacientes(matches, query) {
    const drop = document.getElementById('resultados_paciente');
    drop.innerHTML = '';

    const exactMatch = matches.some(m => (m.nombre || '').trim().toLowerCase() === query.trim().toLowerCase());
    
    if (!exactMatch && query.length >= 2) {
        const createBtn = document.createElement('div');
        createBtn.className = 'p-3 bg-blue-50 hover:bg-blue-100 cursor-pointer text-blue-950 font-bold text-xs flex items-center justify-between border-b border-blue-200 transition-colors rounded-[1px]';
        createBtn.innerHTML = `
            <div class="flex items-center gap-2">
                <span class="w-5 h-5 bg-blue-700 text-white flex items-center justify-center font-bold text-xs rounded-[1px]">+</span>
                <span>REGISTRAR NUEVO PACIENTE: "<strong>${escapeHtml(query)}</strong>"</span>
            </div>
            <span class="px-2 py-0.5 bg-blue-700 text-white text-[10px] font-bold uppercase rounded-[1px]">Internación</span>
        `;
        createBtn.onclick = () => crearPacienteRapido(query);
        drop.appendChild(createBtn);
    }

    if (matches.length > 0) {
        matches.forEach(p => {
            const item = document.createElement('div');
            item.className = 'p-3 hover:bg-slate-100 cursor-pointer text-xs transition-colors flex items-center justify-between rounded-[1px]';
            item.innerHTML = `
                <div>
                    <p class="font-bold text-slate-900 text-sm uppercase">${escapeHtml(p.nombre || 'Sin nombre')}</p>
                    <p class="text-slate-500 text-xs font-mono">CI / CÓDIGO: ${p.ci || p.temp_code || 'N/A'}${p.telefono ? ' | TEL: ' + p.telefono : ''}</p>
                </div>
                <span class="text-blue-700 font-bold text-xs uppercase tracking-wider">Seleccionar →</span>
            `;
            item.onclick = () => seleccionarPaciente(p.id, p.nombre, p.ci || p.temp_code);
            drop.appendChild(item);
        });
    } else if (exactMatch) {
        drop.innerHTML = '<div class="p-3 text-xs text-slate-500 font-medium">No se encontraron más pacientes</div>';
    }

    drop.classList.remove('hidden');
}

async function crearPacienteRapido(nombre) {
    const drop = document.getElementById('resultados_paciente');
    drop.innerHTML = '<div class="p-3 text-xs text-blue-800 font-bold flex items-center gap-2 rounded-[1px]"><svg class="animate-spin h-4 w-4 text-blue-700" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg> Registrando paciente en Internación...</div>';

    try {
        const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const res = await fetch('{{ route("quirofano.quick-paciente") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrf,
            },
            body: JSON.stringify({ nombre: nombre })
        });

        const data = await res.json();
        if (data.success && data.paciente) {
            pacientesData.unshift(data.paciente);
            seleccionarPaciente(data.paciente.id, data.paciente.nombre, data.paciente.temp_code);
            showToast(`Paciente "${data.paciente.nombre}" registrado en Internación`, 'success');
        } else {
            alert(data.message || 'Error al registrar paciente');
            drop.classList.add('hidden');
        }
    } catch (e) {
        console.error(e);
        alert('Error al crear paciente: ' + e.message);
        drop.classList.add('hidden');
    }
}

function seleccionarPaciente(id, nombre, identificador) {
    document.getElementById('paciente_id').value = id;
    document.getElementById('nombre_paciente').textContent = nombre;
    document.getElementById('ci_paciente_display').textContent = identificador ? 'Código / CI: ' + identificador : 'Sin CI asignado';
    
    document.getElementById('info_paciente').classList.remove('hidden');
    document.getElementById('contenedor_buscar_paciente').classList.add('hidden');
    document.getElementById('buscar_paciente').value = '';
    document.getElementById('resultados_paciente').classList.add('hidden');
}

function limpiarPaciente() {
    document.getElementById('paciente_id').value = '';
    document.getElementById('info_paciente').classList.add('hidden');
    document.getElementById('contenedor_buscar_paciente').classList.remove('hidden');
    document.getElementById('buscar_paciente').value = '';
    document.getElementById('buscar_paciente').focus();
}

// ----------------- BUSCADOR Y CREACIÓN DE CIRUJANO -----------------
function inicializarBuscadorCirujanos() {
    const input = document.getElementById('buscar_cirujano');
    const drop = document.getElementById('resultados_cirujano');

    input.addEventListener('input', function() {
        const query = this.value.trim();
        if (query.length === 0) {
            drop.classList.add('hidden');
            return;
        }

        const qLower = query.toLowerCase();
        const matches = medicosData.filter(m => 
            (m.nombre || '').toLowerCase().includes(qLower) ||
            (m.ci && m.ci.toString().includes(qLower)) ||
            (m.especialidad && m.especialidad.toLowerCase().includes(qLower))
        ).slice(0, 8);

        renderResultadosCirujanos(matches, query);
    });

    document.addEventListener('click', function(e) {
        if (!e.target.closest('#contenedor_buscar_cirujano')) {
            drop.classList.add('hidden');
        }
    });
}

function renderResultadosCirujanos(matches, query) {
    const drop = document.getElementById('resultados_cirujano');
    drop.innerHTML = '';

    const exactMatch = matches.some(m => (m.nombre || '').trim().toLowerCase() === query.trim().toLowerCase());

    if (!exactMatch && query.length >= 2) {
        const createBtn = document.createElement('div');
        createBtn.className = 'p-3 bg-purple-50 hover:bg-purple-100 cursor-pointer text-purple-950 font-bold text-xs flex items-center justify-between border-b border-purple-200 transition-colors rounded-[1px]';
        createBtn.innerHTML = `
            <div class="flex items-center gap-2">
                <span class="w-5 h-5 bg-purple-700 text-white flex items-center justify-center font-bold text-xs rounded-[1px]">+</span>
                <span>REGISTRAR NUEVO CIRUJANO: "<strong>${escapeHtml(query)}</strong>"</span>
            </div>
            <span class="px-2 py-0.5 bg-purple-700 text-white text-[10px] font-bold uppercase rounded-[1px]">Cirujano</span>
        `;
        createBtn.onclick = () => crearCirujanoRapido(query);
        drop.appendChild(createBtn);
    }

    if (matches.length > 0) {
        matches.forEach(m => {
            const item = document.createElement('div');
            item.className = 'p-3 hover:bg-slate-100 cursor-pointer text-xs transition-colors flex items-center justify-between rounded-[1px]';
            item.innerHTML = `
                <div>
                    <p class="font-bold text-slate-900 text-sm uppercase">${escapeHtml(m.nombre)}</p>
                    <p class="text-slate-500 text-xs font-mono">CI: ${m.ci}${m.especialidad ? ' | ESP: ' + m.especialidad : ''}</p>
                </div>
                <span class="text-purple-700 font-bold text-xs uppercase tracking-wider">Seleccionar →</span>
            `;
            item.onclick = () => seleccionarCirujano(m.ci, m.nombre);
            drop.appendChild(item);
        });
    } else if (exactMatch) {
        drop.innerHTML = '<div class="p-3 text-xs text-slate-500 font-medium">No se encontraron más cirujanos</div>';
    }

    drop.classList.remove('hidden');
}

async function crearCirujanoRapido(nombre) {
    const drop = document.getElementById('resultados_cirujano');
    drop.innerHTML = '<div class="p-3 text-xs text-purple-800 font-bold flex items-center gap-2 rounded-[1px]"><svg class="animate-spin h-4 w-4 text-purple-700" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg> Registrando cirujano...</div>';

    try {
        const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const res = await fetch('{{ route("quirofano.quick-cirujano") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrf,
            },
            body: JSON.stringify({ nombre: nombre })
        });

        const data = await res.json();
        if (data.success && data.medico) {
            medicosData.unshift(data.medico);
            seleccionarCirujano(data.medico.ci, data.medico.nombre);
            showToast(`Cirujano "${data.medico.nombre}" registrado exitosamente`, 'success');
        } else {
            alert(data.message || 'Error al registrar cirujano');
            drop.classList.add('hidden');
        }
    } catch (e) {
        console.error(e);
        alert('Error al crear cirujano: ' + e.message);
        drop.classList.add('hidden');
    }
}

function seleccionarCirujano(ci, nombre) {
    document.getElementById('ci_cirujano').value = ci;
    document.getElementById('nombre_cirujano').textContent = nombre;
    document.getElementById('ci_cirujano_display').textContent = 'CI: ' + ci;
    
    document.getElementById('info_cirujano').classList.remove('hidden');
    document.getElementById('contenedor_buscar_cirujano').classList.add('hidden');
    document.getElementById('buscar_cirujano').value = '';
    document.getElementById('resultados_cirujano').classList.add('hidden');
}

function limpiarCirujano() {
    document.getElementById('ci_cirujano').value = '';
    document.getElementById('info_cirujano').classList.add('hidden');
    document.getElementById('contenedor_buscar_cirujano').classList.remove('hidden');
    document.getElementById('buscar_cirujano').value = '';
    document.getElementById('buscar_cirujano').focus();
}

// ----------------- EQUIPAMIENTOS Y PRECIO EDITABLE -----------------
function inicializarEquipamientos() {
    const selectEquipo = document.getElementById('equipamiento_nombre');
    const inputPrecio = document.getElementById('equipamiento_precio');
    const btnGuardar = document.getElementById('btnGuardarPrecioEquipo');

    selectEquipo.addEventListener('change', function() {
        const nombre = this.value;
        if (!nombre) {
            inputPrecio.value = '0.00';
            return;
        }

        if (equipamientosData[nombre] !== undefined) {
            inputPrecio.value = parseFloat(equipamientosData[nombre]).toFixed(2);
        } else {
            inputPrecio.value = '0.00';
        }
    });

    btnGuardar.addEventListener('click', async function() {
        const nombre = selectEquipo.value;
        if (!nombre) {
            alert('Por favor selecciona primero un equipamiento (Arco en C o Torre de lámparas) para guardar su precio.');
            return;
        }

        const precio = parseFloat(inputPrecio.value) || 0;
        btnGuardar.disabled = true;
        btnGuardar.classList.add('opacity-50');

        try {
            const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const res = await fetch('{{ route("quirofano.equipamiento.guardar-precio") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                },
                body: JSON.stringify({ nombre: nombre, precio: precio })
            });

            const data = await res.json();
            if (data.success) {
                equipamientosData[nombre] = precio;
                showToast(`Precio de "${nombre}" actualizado a Bs. ${precio.toFixed(2)}`, 'success');
            } else {
                alert(data.message || 'Error al guardar precio');
            }
        } catch (e) {
            console.error(e);
            alert('Error al guardar precio: ' + e.message);
        } finally {
            btnGuardar.disabled = false;
            btnGuardar.classList.remove('opacity-50');
        }
    });
}

// ----------------- FORMATEO DE MONEDA Y TIPO CIRUGÍA -----------------
function inicializarFormateoMonedas() {
    const camposDecimales = ['costo_base', 'equipamiento_precio'];

    camposDecimales.forEach(id => {
        const el = document.getElementById(id);
        if (!el) return;

        el.addEventListener('keypress', function(e) {
            const allowed = /[0-9.,]/;
            if (!allowed.test(e.key)) e.preventDefault();
        });

        el.addEventListener('input', function() {
            let val = this.value.replace(',', '.').replace(/[^0-9.]/g, '');
            const parts = val.split('.');
            if (parts.length > 2) val = parts[0] + '.' + parts.slice(1).join('');
            this.value = val;
        });

        el.addEventListener('blur', function() {
            const num = parseFloat(this.value);
            if (!isNaN(num)) this.value = num.toFixed(2);
        });
    });

    const tipoCirugiaSelect = document.getElementById('tipo_cirugia');
    const costoBaseInput = document.getElementById('costo_base');

    tipoCirugiaSelect.addEventListener('change', function() {
        const opt = this.options[this.selectedIndex];
        const costo = opt ? parseFloat(opt.dataset.costo) : NaN;
        if (!isNaN(costo) && costo > 0) {
            costoBaseInput.value = costo.toFixed(2);
        }
    });
}

// ----------------- SUBMIT DEL FORMULARIO -----------------
function inicializarSubmit() {
    const form = document.getElementById('citaForm');
    const btnSubmit = document.getElementById('btnSubmitForm');

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        const pacienteId = document.getElementById('paciente_id').value;
        const ciCirujano = document.getElementById('ci_cirujano').value;
        const quirofano = document.getElementById('nro_quirofano').value;
        const tipoCirugia = document.getElementById('tipo_cirugia').value;
        const tipoAnestesia = document.getElementById('tipo_anestesia').value;
        const fecha = document.getElementById('fecha').value;
        const hora = document.getElementById('hora_inicio_estimada').value;
        const costoBase = document.getElementById('costo_base').value;

        if (!pacienteId) {
            alert('Por favor busca o registra un paciente.');
            document.getElementById('buscar_paciente').focus();
            return;
        }

        if (!ciCirujano) {
            alert('Por favor busca o registra un cirujano.');
            document.getElementById('buscar_cirujano').focus();
            return;
        }

        if (!quirofano || !tipoCirugia || !tipoAnestesia || !fecha || !hora || !costoBase) {
            alert('Por favor completa todos los campos obligatorios marcados con (*).');
            return;
        }

        const formData = new FormData(form);
        const payload = Object.fromEntries(formData.entries());

        btnSubmit.disabled = true;
        btnSubmit.innerHTML = `
            <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
            </svg>
            <span>Procesando Registro...</span>
        `;

        fetch('{{ route("quirofano.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(payload)
        })
        .then(async response => {
            const data = await response.json();
            if (!response.ok) {
                throw new Error(data.message || (data.errors ? Object.values(data.errors).flat().join('\n') : 'Error al programar cita'));
            }
            return data;
        })
        .then(data => {
            if (data.success) {
                alert('¡Cita quirúrgica programada exitosamente!\nSe asoció a Internación y se generó la cuenta en Caja.');
                window.location.href = '{{ route("quirofano.index") }}';
            } else {
                alert(data.message || 'Error al programar la cita');
                btnSubmit.disabled = false;
                btnSubmit.innerHTML = 'Programar Cita Quirúrgica';
            }
        })
        .catch(err => {
            console.error(err);
            alert('Error: ' + err.message);
            btnSubmit.disabled = false;
            btnSubmit.innerHTML = 'Programar Cita Quirúrgica';
        });
    });
}

function escapeHtml(text) {
    if (!text) return '';
    return text
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}
</script>
@endsection
