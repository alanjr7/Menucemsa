<x-app-layout>
    <div class="p-6 bg-gray-50/50 min-h-screen">

        <!-- Header -->
        <div class="flex justify-between items-end mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Emergencia</h1>
                <p class="text-sm text-gray-500">Recepción - Atención de Emergencias</p>
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
                    <p class="text-red-700 text-sm">Este módulo está diseñado para atender casos de alta prioridad que requieren atención inmediata</p>
                </div>
            </div>
        </div>

        <!-- Formulario de Emergencia -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-bold text-gray-800 mb-6">Registrar Nueva Emergencia</h2>
            
            <form id="formEmergencia" onsubmit="registrarEmergencia(); return false;">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Búsqueda de Paciente -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">C.I. Paciente *</label>
                        <div class="flex gap-3">
                            <input type="text" id="paciente_ci" name="ci" placeholder="Número de CI del paciente" class="flex-1 border border-gray-200 rounded-xl px-4 py-3 text-sm bg-white focus:outline-none focus:border-red-500 focus:ring-2 focus:ring-red-100 transition-all" required>
                            <button type="button" onclick="buscarPaciente()" class="bg-red-600 hover:bg-red-700 text-white font-medium px-6 py-3 rounded-xl transition-colors text-sm">
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
                        <select name="tipo_paciente" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm bg-white focus:outline-none focus:border-red-500 focus:ring-2 focus:ring-red-100 transition-all">
                            <option value="existente">Paciente Existente</option>
                            <option value="nuevo">Nuevo Paciente</option>
                        </select>
                    </div>

                    <!-- Nivel de Emergencia -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nivel de Emergencia *</label>
                        <select name="nivel_emergencia" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm bg-white focus:outline-none focus:border-red-500 focus:ring-2 focus:ring-red-100 transition-all" required>
                            <option value="">Seleccione...</option>
                            <option value="rojo">Rojo - Emergencia Inmediata</option>
                            <option value="naranja">Naranja - Emergencia Urgente</option>
                            <option value="amarillo">Amarillo - Emergencia Media</option>
                            <option value="verde">Verde - Emergencia Baja</option>
                        </select>
                    </div>

                    <!-- Datos Personales (para nuevos pacientes) -->
                    <div id="datosPersonales" class="md:col-span-2 hidden">
                        <h3 class="text-md font-semibold text-gray-800 mb-4 p-3 bg-red-50 rounded-lg">Datos Personales del Nuevo Paciente</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nombres *</label>
                                <input type="text" name="nombres" placeholder="Nombres del paciente" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none focus:border-red-500 focus:ring-2 focus:ring-red-100 transition-all">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Apellidos *</label>
                                <input type="text" name="apellidos" placeholder="Apellidos del paciente" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none focus:border-red-500 focus:ring-2 focus:ring-red-100 transition-all">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Sexo *</label>
                                <select name="sexo" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none focus:border-red-500 focus:ring-2 focus:ring-red-100 transition-all">
                                    <option value="">Seleccione...</option>
                                    <option value="Masculino">Masculino</option>
                                    <option value="Femenino">Femenino</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Teléfono</label>
                                <input type="tel" name="telefono" placeholder="Teléfono del paciente" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none focus:border-red-500 focus:ring-2 focus:ring-red-100 transition-all">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Correo</label>
                                <input type="email" name="correo" placeholder="Correo electrónico" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none focus:border-red-500 focus:ring-2 focus:ring-red-100 transition-all">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Dirección</label>
                                <input type="text" name="direccion" placeholder="Dirección del paciente" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none focus:border-red-500 focus:ring-2 focus:ring-red-100 transition-all">
                            </div>
                        </div>
                    </div>

                    <!-- Datos de la Emergencia -->
                    <div class="md:col-span-2 space-y-4">
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
                            <textarea name="descripcion" rows="4" placeholder="Describa detalladamente los síntomas y la situación de emergencia" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm bg-white focus:outline-none focus:border-red-500 focus:ring-2 focus:ring-red-100 transition-all" required></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Signos Vitales</label>
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <div>
                                    <label class="block text-xs text-gray-600 mb-1">Presión Arterial</label>
                                    <input type="text" name="presion_arterial" placeholder="120/80" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:border-red-500 focus:ring-2 focus:ring-red-100 transition-all">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-600 mb-1">Frecuencia Cardíaca</label>
                                    <input type="text" name="frecuencia_cardiaca" placeholder="80 lpm" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:border-red-500 focus:ring-2 focus:ring-red-100 transition-all">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-600 mb-1">Frecuencia Respiratoria</label>
                                    <input type="text" name="frecuencia_respiratoria" placeholder="16 rpm" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:border-red-500 focus:ring-2 focus:ring-red-100 transition-all">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-600 mb-1">Temperatura</label>
                                    <input type="text" name="temperatura" placeholder="37.0°C" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:border-red-500 focus:ring-2 focus:ring-red-100 transition-all">
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Alergias Conocidas</label>
                            <input type="text" name="alergias" placeholder="Ej: Penicilina, Aspirina, etc." class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm bg-white focus:outline-none focus:border-red-500 focus:ring-2 focus:ring-red-100 transition-all">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Medicamentos Actuales</label>
                            <textarea name="medicamentos" rows="2" placeholder="Medicamentos que el paciente está tomando actualmente" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm bg-white focus:outline-none focus:border-red-500 focus:ring-2 focus:ring-red-100 transition-all"></textarea>
                        </div>

                        <!-- Opciones Adicionales -->
                        <div class="bg-gray-50 rounded-xl p-4">
                            <h4 class="font-semibold text-gray-800 mb-3">Opciones Adicionales</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <label class="flex items-center">
                                    <input type="checkbox" name="accidente_automovilistico" class="mr-2 rounded border-gray-300 text-red-600 focus:ring-red-500">
                                    <span class="text-sm text-gray-700">Accidente automovilístico</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="requiere_cirugia_inmediata" class="mr-2 rounded border-gray-300 text-red-600 focus:ring-red-500">
                                    <span class="text-sm text-gray-700">Requiere cirugía inmediata</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="requiere_uci" class="mr-2 rounded border-gray-300 text-red-600 focus:ring-red-500">
                                    <span class="text-sm text-gray-700">Requiere UCI</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="contacto_familia" class="mr-2 rounded border-gray-300 text-red-600 focus:ring-red-500">
                                    <span class="text-sm text-gray-700">Contactar a familiar</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botones de Acción -->
                <div class="flex justify-end gap-3 pt-6 border-t border-gray-200 mt-6">
                    <a href="{{ route('reception') }}" class="px-6 py-3 border border-gray-200 rounded-xl text-gray-700 font-medium hover:bg-gray-50 transition-colors text-sm">
                        Cancelar
                    </a>
                    <button type="submit" class="px-6 py-3 bg-red-600 text-white rounded-xl font-medium hover:bg-red-700 transition-colors flex items-center text-sm shadow-md">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        Registrar Emergencia
                    </button>
                </div>
            </form>
        </div>

        <!-- Lista de Emergencias Activas -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mt-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-800">Emergencias Activas</h3>
                <button onclick="cargarEmergenciasActivas()" class="bg-red-500 hover:bg-red-600 text-white text-xs font-medium px-3 py-1.5 rounded-lg shadow-sm transition-colors">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Actualizar
                </button>
            </div>
            <div id="lista-emergencias">
                <div class="p-8 text-center text-gray-500">
                    <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <p>Cargando emergencias activas...</p>
                </div>
            </div>
        </div>

    </div>

    <!-- JavaScript -->
    <script>
        // Cargar datos al iniciar la página
        document.addEventListener('DOMContentLoaded', function() {
            inicializarTipoPaciente();
            cargarEmergenciasActivas();
        });

        // Función para cargar emergencias activas
        async function cargarEmergenciasActivas() {
            try {
                const response = await fetch('/api/emergencias-activas');
                const data = await response.json();
                
                if (data.success) {
                    mostrarEmergencias(data.emergencias);
                }
            } catch (error) {
                console.error('Error al cargar emergencias:', error);
                document.getElementById('lista-emergencias').innerHTML = `
                    <div class="p-8 text-center text-gray-500">
                        <p>Error al cargar emergencias activas</p>
                    </div>
                `;
            }
        }

        // Función para mostrar emergencias
        function mostrarEmergencias(emergencias) {
            const listaEmergencias = document.getElementById('lista-emergencias');
            
            if (emergencias.length === 0) {
                listaEmergencias.innerHTML = `
                    <div class="p-8 text-center text-gray-500">
                        <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p>No hay emergencias activas en este momento</p>
                    </div>
                `;
                return;
            }
            
            let html = '';
            emergencias.forEach(emergencia => {
                const nivelClass = emergencia.nivel === 'rojo' ? 'bg-red-100 text-red-800' : 
                                  emergencia.nivel === 'naranja' ? 'bg-orange-100 text-orange-800' : 
                                  emergencia.nivel === 'amarillo' ? 'bg-yellow-100 text-yellow-800' : 
                                  'bg-green-100 text-green-800';
                
                html += `
                    <div class="p-4 hover:bg-gray-50 transition-colors border-l-4 ${emergencia.nivel === 'rojo' ? 'border-red-500' : emergencia.nivel === 'naranja' ? 'border-orange-500' : emergencia.nivel === 'amarillo' ? 'border-yellow-500' : 'border-green-500'}">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="text-center">
                                    <div class="text-sm font-bold text-gray-800">${emergencia.codigo}</div>
                                    <span class="text-xs px-2 py-1 rounded-full ${nivelClass}">${emergencia.nivel.toUpperCase()}</span>
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900">${emergencia.paciente.nombre}</div>
                                    <div class="text-sm text-gray-500">CI: ${emergencia.paciente.ci}</div>
                                    <div class="text-xs text-gray-500">${emergencia.tipo_emergencia}</div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-medium text-gray-900">${emergencia.hora_ingreso}</div>
                                <div class="text-xs text-gray-500">${emergencia.descripcion.substring(0, 50)}...</div>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            listaEmergencias.innerHTML = html;
        }

        // Función para buscar paciente
        function buscarPaciente() {
            const ci = document.getElementById('paciente_ci').value;
            const tipoPacienteSelect = document.querySelector('select[name="tipo_paciente"]');
            const datosPersonales = document.getElementById('datosPersonales');
            
            if (ci.length < 8) {
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

        // Función para mostrar datos del paciente encontrado
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

        // Función para registrar emergencia
        function registrarEmergencia() {
            event.preventDefault();
            
            const form = document.getElementById('formEmergencia');
            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());
            
            // Validar campos requeridos básicos
            if (!data.ci || !data.nivel_emergencia || !data.tipo_emergencia || !data.descripcion) {
                alert('Por favor complete todos los campos obligatorios');
                return;
            }

            // Validar que si es paciente nuevo, complete todos los datos personales
            if (data.tipo_paciente === 'nuevo') {
                if (!data.nombres || !data.apellidos || !data.sexo) {
                    alert('Por favor complete todos los datos personales obligatorios (nombres, apellidos, sexo)');
                    return;
                }
            }

            // Mostrar loading
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<svg class="animate-spin w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>Procesando...';
            submitBtn.disabled = true;

            // Preparar datos para el API
            const apiData = {
                ...data,
                triage_tipo: data.nivel_emergencia === 'rojo' ? 'rojo' : 'amarillo',
                motivo: data.descripcion
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
                    alert(data.message || 'Emergencia registrada exitosamente');
                    // Recargar lista de emergencias
                    cargarEmergenciasActivas();
                    // Resetear formulario
                    form.reset();
                    // Eliminar sección de datos existentes si hay una
                    const existingSection = document.querySelector('.bg-green-50');
                    if (existingSection) {
                        existingSection.remove();
                    }
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al registrar la emergencia');
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
                    if (this.value === 'nuevo') {
                        datosPersonales.classList.remove('hidden');
                        datosPersonales.classList.add('block');
                    } else {
                        datosPersonales.classList.add('hidden');
                        datosPersonales.classList.remove('block');
                    }
                });
                
                // Disparar el evento change al cargar para establecer el estado inicial
                tipoPacienteSelect.dispatchEvent(new Event('change'));
            }
        }
    </script>
</x-app-layout>
