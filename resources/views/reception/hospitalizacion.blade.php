<x-app-layout>
    <div class="p-6 bg-gray-50/50 min-h-screen">

        <!-- Header -->
        <div class="flex justify-between items-end mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Hospitalización</h1>
                <p class="text-sm text-gray-500">Recepción - Admisión de Hospitalización</p>
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

        <!-- Alerta de Hospitalización -->
        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 rounded-lg">
            <div class="flex items-center">
                <svg class="w-6 h-6 text-blue-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                </svg>
                <div>
                    <h3 class="text-blue-800 font-bold">MÓDULO DE HOSPITALIZACIÓN</h3>
                    <p class="text-blue-700 text-sm">Este módulo está diseñado para gestionar admisiones internas y asignación de habitaciones</p>
                </div>
            </div>
        </div>

        <!-- Formulario de Hospitalización -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-bold text-gray-800 mb-6">Registrar Nueva Hospitalización</h2>
            
            <form id="formHospitalizacion" onsubmit="registrarHospitalizacion(); return false;">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Búsqueda de Paciente -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">C.I. Paciente *</label>
                        <div class="flex gap-3">
                            <input type="text" id="paciente_ci" name="ci" placeholder="Número de CI del paciente" class="flex-1 border border-gray-200 rounded-xl px-4 py-3 text-sm bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all" required>
                            <button type="button" onclick="buscarPaciente()" class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-6 py-3 rounded-xl transition-colors text-sm">
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
                        <select name="tipo_paciente" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all">
                            <option value="existente">Paciente Existente</option>
                            <option value="nuevo">Nuevo Paciente</option>
                        </select>
                    </div>

                    <!-- Tipo de Hospitalización -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Hospitalización *</label>
                        <select name="tipo_hospitalizacion" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all" required>
                            <option value="">Seleccione...</option>
                            <option value="cirugia">Cirugía Programada</option>
                            <option value="emergencia">Emergencia con Hospitalización</option>
                            <option value="observacion">Observación</option>
                            <option value="parto">Parto</option>
                            <option value="tratamiento">Tratamiento Médico</option>
                            <option value="rehabilitacion">Rehabilitación</option>
                            <option value="uci">UCI</option>
                        </select>
                    </div>

                    <!-- Datos Personales (para nuevos pacientes) -->
                    <div id="datosPersonales" class="md:col-span-2 hidden">
                        <h3 class="text-md font-semibold text-gray-800 mb-4 p-3 bg-blue-50 rounded-lg">Datos Personales del Nuevo Paciente</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nombres *</label>
                                <input type="text" name="nombres" placeholder="Nombres del paciente" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Apellidos *</label>
                                <input type="text" name="apellidos" placeholder="Apellidos del paciente" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Sexo *</label>
                                <select name="sexo" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all">
                                    <option value="">Seleccione...</option>
                                    <option value="Masculino">Masculino</option>
                                    <option value="Femenino">Femenino</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Fecha de Nacimiento</label>
                                <input type="date" name="fecha_nacimiento" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Teléfono</label>
                                <input type="tel" name="telefono" placeholder="Teléfono del paciente" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Correo</label>
                                <input type="email" name="correo" placeholder="Correo electrónico" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Dirección</label>
                                <input type="text" name="direccion" placeholder="Dirección del paciente" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all">
                            </div>
                        </div>
                    </div>

                    <!-- Datos de Hospitalización -->
                    <div class="md:col-span-2 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Motivo de Hospitalización *</label>
                            <textarea name="motivo" rows="3" placeholder="Describa el motivo principal de la hospitalización" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all" required></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Diagnóstico Presuntivo *</label>
                            <textarea name="diagnostico" rows="3" placeholder="Diagnóstico presuntivo del paciente" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all" required></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Servicio *</label>
                            <select name="servicio" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all" required>
                                <option value="">Seleccione...</option>
                                <option value="medicina_interna">Medicina Interna</option>
                                <option value="cirugia_general">Cirugía General</option>
                                <option value="ginecologia">Ginecología</option>
                                <option value="pediatria">Pediatría</option>
                                <option value="cardiologia">Cardiología</option>
                                <option value="neurologia">Neurología</option>
                                <option value="oncologia">Oncología</option>
                                <option value="uci">UCI</option>
                                <option value="uci_neonatal">UCI Neonatal</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Médico Tratante *</label>
                            <select name="medico_tratante" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all" required>
                                <option value="">Seleccione...</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Seguro *</label>
                            <select name="seguro" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all" required>
                                <option value="">Seleccione...</option>
                                <option value="particular">Particular</option>
                                <option value="seguro_social">Seguro Social</option>
                                <option value="seguro_privado">Seguro Privado</option>
                                <option value="iseguros">Iseguros</option>
                                <option value="mapfre">Mapfre</option>
                                <option value="palic">Palic</option>
                            </select>
                        </div>

                        <!-- Información de Contacto -->
                        <div class="bg-gray-50 rounded-xl p-4">
                            <h4 class="font-semibold text-gray-800 mb-3">Información de Contacto de Emergencia</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Nombre del Contacto *</label>
                                    <input type="text" name="contacto_nombre" placeholder="Nombre completo del contacto" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Teléfono del Contacto *</label>
                                    <input type="tel" name="contacto_telefono" placeholder="Teléfono del contacto" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Parentesco *</label>
                                    <select name="contacto_parentesco" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all" required>
                                        <option value="">Seleccione...</option>
                                        <option value="esposo/a">Esposo/a</option>
                                        <option value="padre/madre">Padre/Madre</option>
                                        <option value="hijo/a">Hijo/a</option>
                                        <option value="hermano/a">Hermano/a</option>
                                        <option value="otro">Otro</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Relación con Paciente</label>
                                    <input type="text" name="contacto_relacion" placeholder="Descripción de la relación" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all">
                                </div>
                            </div>
                        </div>

                        <!-- Opciones Adicionales -->
                        <div class="bg-gray-50 rounded-xl p-4">
                            <h4 class="font-semibold text-gray-800 mb-3">Opciones Adicionales</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <label class="flex items-center">
                                    <input type="checkbox" name="requiere_cirugia" class="mr-2 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="text-sm text-gray-700">Requiere cirugía</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="requiere_uci" class="mr-2 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="text-sm text-gray-700">Requiere UCI</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="paciente_riesgo" class="mr-2 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="text-sm text-gray-700">Paciente de alto riesgo</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="alergias_severas" class="mr-2 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="text-sm text-gray-700">Alergias severas conocidas</span>
                                </label>
                            </div>
                        </div>

                        <!-- Observaciones -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Observaciones Adicionales</label>
                            <textarea name="observaciones" rows="3" placeholder="Notas adicionales sobre la hospitalización" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Botones de Acción -->
                <div class="flex justify-end gap-3 pt-6 border-t border-gray-200 mt-6">
                    <a href="{{ route('reception') }}" class="px-6 py-3 border border-gray-200 rounded-xl text-gray-700 font-medium hover:bg-gray-50 transition-colors text-sm">
                        Cancelar
                    </a>
                    <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-xl font-medium hover:bg-blue-700 transition-colors flex items-center text-sm shadow-md">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                        </svg>
                        Registrar Hospitalización
                    </button>
                </div>
            </form>
        </div>

        <!-- Lista de Hospitalizaciones Activas -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mt-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-800">Hospitalizaciones Activas</h3>
                <button onclick="cargarHospitalizacionesActivas()" class="bg-blue-500 hover:bg-blue-600 text-white text-xs font-medium px-3 py-1.5 rounded-lg shadow-sm transition-colors">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Actualizar
                </button>
            </div>
            <div id="lista-hospitalizaciones">
                <div class="p-8 text-center text-gray-500">
                    <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                    </svg>
                    <p>Cargando hospitalizaciones activas...</p>
                </div>
            </div>
        </div>

    </div>

    <!-- JavaScript -->
    <script>
        // Cargar datos al iniciar la página
        document.addEventListener('DOMContentLoaded', function() {
            inicializarTipoPaciente();
            cargarMedicos();
            cargarHospitalizacionesActivas();
        });

        // Función para cargar médicos
        async function cargarMedicos() {
            try {
                const response = await fetch('/api/medicos-disponibles');
                const data = await response.json();
                
                if (data.success) {
                    const select = document.querySelector('select[name="medico_tratante"]');
                    select.innerHTML = '<option value="">Seleccione...</option>';
                    
                    data.medicos.forEach(medico => {
                        const option = document.createElement('option');
                        option.value = medico.ci;
                        option.textContent = `Dr. ${medico.usuario.name} - ${medico.especialidad.nombre}`;
                        select.appendChild(option);
                    });
                }
            } catch (error) {
                console.error('Error al cargar médicos:', error);
            }
        }

        // Función para cargar hospitalizaciones activas
        async function cargarHospitalizacionesActivas() {
            try {
                const response = await fetch('/api/hospitalizaciones-activas');
                const data = await response.json();
                
                if (data.success) {
                    mostrarHospitalizaciones(data.hospitalizaciones);
                }
            } catch (error) {
                console.error('Error al cargar hospitalizaciones:', error);
                document.getElementById('lista-hospitalizaciones').innerHTML = `
                    <div class="p-8 text-center text-gray-500">
                        <p>Error al cargar hospitalizaciones activas</p>
                    </div>
                `;
            }
        }

        // Función para mostrar hospitalizaciones
        function mostrarHospitalizaciones(hospitalizaciones) {
            const listaHospitalizaciones = document.getElementById('lista-hospitalizaciones');
            
            if (hospitalizaciones.length === 0) {
                listaHospitalizaciones.innerHTML = `
                    <div class="p-8 text-center text-gray-500">
                        <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p>No hay hospitalizaciones activas en este momento</p>
                    </div>
                `;
                return;
            }
            
            let html = '';
            hospitalizaciones.forEach(hospitalizacion => {
                const tipoClass = hospitalizacion.tipo === 'uci' ? 'bg-red-100 text-red-800' : 
                                  hospitalizacion.tipo === 'cirugia' ? 'bg-purple-100 text-purple-800' : 
                                  'bg-blue-100 text-blue-800';
                
                html += `
                    <div class="p-4 hover:bg-gray-50 transition-colors border-l-4 border-blue-500">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="text-center">
                                    <div class="text-sm font-bold text-gray-800">${hospitalizacion.codigo}</div>
                                    <span class="text-xs px-2 py-1 rounded-full ${tipoClass}">${hospitalizacion.tipo.toUpperCase()}</span>
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900">${hospitalizacion.paciente.nombre}</div>
                                    <div class="text-sm text-gray-500">CI: ${hospitalizacion.paciente.ci}</div>
                                    <div class="text-xs text-gray-500">${hospitalizacion.servicio}</div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-medium text-gray-900">${hospitalizacion.fecha_ingreso}</div>
                                <div class="text-xs text-gray-500">Dr. ${hospitalizacion.medico.usuario.name}</div>
                                <div class="text-xs text-gray-500">Habitación: ${hospitalizacion.habitacion || 'Por asignar'}</div>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            listaHospitalizaciones.innerHTML = html;
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

        // Función para registrar hospitalización
        function registrarHospitalizacion() {
            event.preventDefault();
            
            const form = document.getElementById('formHospitalizacion');
            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());
            
            // Validar campos requeridos básicos
            if (!data.ci || !data.tipo_hospitalizacion || !data.motivo || !data.diagnostico || !data.servicio || !data.medico_tratante || !data.seguro) {
                alert('Por favor complete todos los campos obligatorios');
                return;
            }

            // Validar contacto de emergencia
            if (!data.contacto_nombre || !data.contacto_telefono || !data.contacto_parentesco) {
                alert('Por favor complete los datos del contacto de emergencia');
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
                triage_tipo: 'amarillo', // Hospitalización generalmente usa triage amarillo
                motivo: data.motivo
            };

            fetch('/api/registrar-hospitalizacion', {
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
                    alert(data.message || 'Hospitalización registrada exitosamente');
                    // Recargar lista de hospitalizaciones
                    cargarHospitalizacionesActivas();
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
                alert('Error al registrar la hospitalización');
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
