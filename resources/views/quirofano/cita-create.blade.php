@extends('layouts.app')

@section('content')
<div class="w-full p-6 bg-gray-50/50 min-h-screen">

    <!-- Page Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Nueva Cita Quirúrgica</h1>
            <p class="text-sm text-gray-500">Programar una nueva cita quirúrgica</p>
        </div>
        <a href="{{ route('quirofano.index') }}" class="flex items-center px-4 py-2 border border-gray-200 rounded-lg text-gray-600 bg-white hover:bg-gray-50 font-medium transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
        <form id="citaForm" class="p-6">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                
                <!-- Información del Paciente -->
                <div class="space-y-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Información del Paciente
                        </h3>
                        
                        <div class="space-y-4">
                            <!-- Buscador de Paciente -->
                            <div class="relative">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Buscar Paciente *</label>
                                <div class="flex gap-2">
                                    <div class="relative flex-1">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                            </svg>
                                        </div>
                                        <input type="text" id="buscar_paciente" 
                                               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                               placeholder="Escriba nombre o CI del paciente..." autocomplete="off">
                                        <!-- Dropdown de resultados -->
                                        <div id="resultados_paciente" class="hidden absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-60 overflow-auto"></div>
                                    </div>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Escriba al menos 3 caracteres para buscar</p>
                            </div>

                            <!-- CI del Paciente (oculto pero se envía) -->
                            <input type="hidden" name="ci_paciente" id="ci_paciente">

                            <!-- Info del paciente seleccionado -->
                            <div id="info_paciente" class="hidden bg-blue-50 rounded-lg p-4 border border-blue-100">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Paciente seleccionado:</p>
                                        <p id="nombre_paciente" class="font-semibold text-gray-900"></p>
                                        <p id="ci_paciente_display" class="text-xs text-gray-500"></p>
                                    </div>
                                    <button type="button" onclick="limpiarPaciente()" class="ml-auto text-gray-400 hover:text-red-500">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Información Quirúrgica -->
                <div class="space-y-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                            </svg>
                            Información Quirúrgica
                        </h3>
                        
                        <div class="space-y-4">
                            <!-- Buscador de Cirujano -->
                            <div class="relative">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Buscar Cirujano *</label>
                                <div class="flex gap-2">
                                    <div class="relative flex-1">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                            </svg>
                                        </div>
                                        <input type="text" id="buscar_cirujano" 
                                               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                               placeholder="Escriba nombre o CI del cirujano..." autocomplete="off">
                                        <!-- Dropdown de resultados -->
                                        <div id="resultados_cirujano" class="hidden absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-60 overflow-auto"></div>
                                    </div>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Escriba al menos 3 caracteres para buscar</p>
                            </div>

                            <!-- CI del Cirujano (oculto) -->
                            <input type="hidden" name="ci_cirujano" id="ci_cirujano">

                            <!-- Info del cirujano seleccionado -->
                            <div id="info_cirujano" class="hidden bg-purple-50 rounded-lg p-4 border border-purple-100">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center mr-3">
                                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Cirujano seleccionado:</p>
                                        <p id="nombre_cirujano" class="font-semibold text-gray-900"></p>
                                        <p id="ci_cirujano_display" class="text-xs text-gray-500"></p>
                                    </div>
                                    <button type="button" onclick="limpiarCirujano()" class="ml-auto text-gray-400 hover:text-red-500">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Número de Quirófano *</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                        </svg>
                                    </div>
                                    <select name="nro_quirofano" id="nro_quirofano" class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 appearance-none cursor-pointer" required>
                                        <option value="">Seleccionar quirófano...</option>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Cirugía *</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <select name="tipo_cirugia" id="tipo_cirugia" class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 appearance-none cursor-pointer" required>
                                        <option value="">Seleccionar tipo...</option>
                                        <option value="menor">Menor - 60 min</option>
                                        <option value="mediana">Mediana - 90 min</option>
                                        <option value="mayor">Mayor - 120 min</option>
                                        <option value="ambulatoria">Ambulatoria - 45 min</option>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Fecha y Hora -->
                <div class="space-y-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Fecha y Hora</h3>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Fecha de la Cirugía *</label>
                                <input type="date" name="fecha" id="fecha" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Hora de Inicio Estimada *</label>
                                <input type="time" name="hora_inicio_estimada" id="hora_inicio_estimada" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Información Adicional -->
                <div class="space-y-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Información Adicional</h3>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nombre del Instrumentista</label>
                                <input type="text" name="nombre_instrumentista" id="nombre_instrumentista" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Nombre completo del instrumentista">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nombre del Anestesiólogo</label>
                                <input type="text" name="nombre_anestesiologo" id="nombre_anestesiologo" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Nombre completo del anestesiólogo">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Descripción de la Cirugía</label>
                                <textarea name="descripcion_cirugia" id="descripcion_cirugia" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Descripción detallada del procedimiento"></textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Observaciones</label>
                                <textarea name="observaciones" id="observaciones" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Observaciones adicionales"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botones de Acción -->
            <div class="flex justify-end gap-4 mt-8 pt-6 border-t border-gray-200">
                <a href="{{ route('quirofano.index') }}" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition-colors">
                    Cancelar
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium transition-colors">
                    Programar Cita
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Variables globales para almacenar los datos
let pacientesData = [];
let medicosData = [];

// Cargar datos al iniciar
document.addEventListener('DOMContentLoaded', function() {
    cargarPacientes();
    cargarMedicos();
    inicializarBuscadores();
    
    const form = document.getElementById('citaForm');
    
    // Cargar quirófanos disponibles
    fetch('/api/quirofanos-disponibles', {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        const select = document.getElementById('nro_quirofano');
        select.innerHTML = '<option value="">Seleccionar quirófano...</option>';
        if (data.quirofanos && data.quirofanos.length > 0) {
            data.quirofanos.forEach(quirofano => {
                const option = document.createElement('option');
                option.value = quirofano.nro;
                option.textContent = `Quirófano ${quirofano.nro} - ${quirofano.tipo} (${quirofano.estado})`;
                select.appendChild(option);
            });
        }
    })
    .catch(error => {
        console.warn('No se pudieron cargar los quirófanos:', error);
    });

    // Submit del formulario
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());
        
        // Validación básica
        if (!data.ci_paciente || !data.ci_cirujano || !data.nro_quirofano || !data.tipo_cirugia || !data.fecha || !data.hora_inicio_estimada) {
            alert('Por favor completa todos los campos requeridos.\n\nBusque y seleccione un paciente y un cirujano.');
            return;
        }
        
        const submitUrl = '{{ route("quirofano.store") }}';
        
        fetch(submitUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(data)
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(errorData => {
                    throw new Error(errorData.message || `Error ${response.status}: ${response.statusText}`);
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                alert('Cita quirúrgica programada exitosamente');
                window.location.href = '{{ route("quirofano.index") }}';
            } else {
                alert(data.message || 'Error al programar la cita');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error: ' + error.message);
        });
    });
});

// Cargar lista de pacientes
async function cargarPacientes() {
    try {
        const response = await fetch('/api/pacientes-lista', {
            headers: { 'Accept': 'application/json' }
        });
        const data = await response.json();
        if (data.success) {
            pacientesData = data.pacientes;
        }
    } catch (error) {
        console.warn('No se pudieron cargar los pacientes:', error);
    }
}

// Cargar lista de médicos
async function cargarMedicos() {
    try {
        const response = await fetch('/api/medicos-lista', {
            headers: { 'Accept': 'application/json' }
        });
        const data = await response.json();
        if (data.success) {
            medicosData = data.medicos;
        }
    } catch (error) {
        console.warn('No se pudieron cargar los médicos:', error);
    }
}

// Inicializar buscadores
function inicializarBuscadores() {
    // Buscador de pacientes
    const inputPaciente = document.getElementById('buscar_paciente');
    const resultadosPaciente = document.getElementById('resultados_paciente');
    
    inputPaciente.addEventListener('input', function() {
        const query = this.value.toLowerCase().trim();
        if (query.length < 3) {
            resultadosPaciente.classList.add('hidden');
            return;
        }
        
        const resultados = pacientesData.filter(p => 
            p.nombre.toLowerCase().includes(query) || 
            p.ci.toString().includes(query)
        ).slice(0, 10);
        
        mostrarResultadosPacientes(resultados);
    });
    
    // Cerrar dropdown al hacer click fuera
    document.addEventListener('click', function(e) {
        if (!e.target.closest('#buscar_paciente') && !e.target.closest('#resultados_paciente')) {
            resultadosPaciente.classList.add('hidden');
        }
        if (!e.target.closest('#buscar_cirujano') && !e.target.closest('#resultados_cirujano')) {
            document.getElementById('resultados_cirujano').classList.add('hidden');
        }
    });
    
    // Buscador de cirujanos
    const inputCirujano = document.getElementById('buscar_cirujano');
    const resultadosCirujano = document.getElementById('resultados_cirujano');
    
    inputCirujano.addEventListener('input', function() {
        const query = this.value.toLowerCase().trim();
        if (query.length < 3) {
            resultadosCirujano.classList.add('hidden');
            return;
        }
        
        const resultados = medicosData.filter(m => 
            m.nombre.toLowerCase().includes(query) || 
            m.ci.toString().includes(query)
        ).slice(0, 10);
        
        mostrarResultadosCirujanos(resultados);
    });
}

// Mostrar resultados de pacientes
function mostrarResultadosPacientes(resultados) {
    const container = document.getElementById('resultados_paciente');
    
    if (resultados.length === 0) {
        container.innerHTML = '<div class="p-3 text-sm text-gray-500">No se encontraron pacientes</div>';
        container.classList.remove('hidden');
        return;
    }
    
    container.innerHTML = resultados.map(p => `
        <div class="p-3 hover:bg-blue-50 cursor-pointer border-b border-gray-100 last:border-0" onclick="seleccionarPaciente(${p.ci}, '${p.nombre.replace(/'/g, "\\'")}')">
            <div class="font-medium text-gray-900">${p.nombre}</div>
            <div class="text-xs text-gray-500">CI: ${p.ci}${p.telefono ? ' - Tel: ' + p.telefono : ''}</div>
        </div>
    `).join('');
    
    container.classList.remove('hidden');
}

// Mostrar resultados de cirujanos
function mostrarResultadosCirujanos(resultados) {
    const container = document.getElementById('resultados_cirujano');
    
    if (resultados.length === 0) {
        container.innerHTML = '<div class="p-3 text-sm text-gray-500">No se encontraron médicos</div>';
        container.classList.remove('hidden');
        return;
    }
    
    container.innerHTML = resultados.map(m => `
        <div class="p-3 hover:bg-purple-50 cursor-pointer border-b border-gray-100 last:border-0" onclick="seleccionarCirujano(${m.ci}, '${m.nombre.replace(/'/g, "\\'")}')">
            <div class="font-medium text-gray-900">${m.nombre}</div>
            <div class="text-xs text-gray-500">CI: ${m.ci}${m.especialidad ? ' - ' + m.especialidad : ''}</div>
        </div>
    `).join('');
    
    container.classList.remove('hidden');
}

// Seleccionar paciente
function seleccionarPaciente(ci, nombre) {
    document.getElementById('ci_paciente').value = ci;
    document.getElementById('nombre_paciente').textContent = nombre;
    document.getElementById('ci_paciente_display').textContent = 'CI: ' + ci;
    document.getElementById('info_paciente').classList.remove('hidden');
    document.getElementById('buscar_paciente').value = '';
    document.getElementById('resultados_paciente').classList.add('hidden');
}

// Seleccionar cirujano
function seleccionarCirujano(ci, nombre) {
    document.getElementById('ci_cirujano').value = ci;
    document.getElementById('nombre_cirujano').textContent = nombre;
    document.getElementById('ci_cirujano_display').textContent = 'CI: ' + ci;
    document.getElementById('info_cirujano').classList.remove('hidden');
    document.getElementById('buscar_cirujano').value = '';
    document.getElementById('resultados_cirujano').classList.add('hidden');
}

// Limpiar selección de paciente
function limpiarPaciente() {
    document.getElementById('ci_paciente').value = '';
    document.getElementById('info_paciente').classList.add('hidden');
    document.getElementById('buscar_paciente').value = '';
}

// Limpiar selección de cirujano
function limpiarCirujano() {
    document.getElementById('ci_cirujano').value = '';
    document.getElementById('info_cirujano').classList.add('hidden');
    document.getElementById('buscar_cirujano').value = '';
}</script>
@endsection
