<x-app-layout>
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
            
            <form id="formConsultaExterna" onsubmit="registrarConsultaExterna(); return false;">
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
                        <h3 class="text-md font-semibold text-gray-800 mb-4 p-3 bg-blue-50 rounded-lg">Datos Personales del Nuevo Paciente</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nombres *</label>
                                <input type="text" name="nombres" placeholder="Nombres del paciente" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition-all">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Apellidos *</label>
                                <input type="text" name="apellidos" placeholder="Apellidos del paciente" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition-all">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Sexo *</label>
                                <select name="sexo" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition-all">
                                    <option value="">Seleccione...</option>
                                    <option value="Masculino">Masculino</option>
                                    <option value="Femenino">Femenino</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Teléfono</label>
                                <input type="tel" name="telefono" placeholder="Teléfono del paciente" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition-all">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Correo</label>
                                <input type="email" name="correo" placeholder="Correo electrónico" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition-all">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Dirección</label>
                                <input type="text" name="direccion" placeholder="Dirección del paciente" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition-all">
                            </div>
                        </div>
                    </div>

                    <!-- Datos de Consulta -->
                    <div class="md:col-span-2 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Especialidad *</label>
                            <select name="especialidad" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm bg-white focus:outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition-all">
                                <option value="">Seleccione...</option>
                                <option value="GENERAL">Medicina General</option>
                                <option value="CARDIO">Cardiología</option>
                                <option value="PEDIAT">Pediatría</option>
                                <option value="GINE">Ginecología</option>
                                <option value="DERMA">Dermatología</option>
                                <option value="NEURO">Neurología</option>
                                <option value="ORTOP">Ortopedia</option>
                                <option value="OFTAL">Oftalmología</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Médico *</label>
                            <select name="medico" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm bg-white focus:outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition-all">
                                <option value="">Seleccione especialidad primero</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Seguro *</label>
                            <select name="seguro" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm bg-white focus:outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition-all">
                                <option value="">Seleccione...</option>
                                <option value="particular">Particular</option>
                                <option value="seguro social">Seguro Social</option>
                                <option value="seguro privado">Seguro Privado</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Motivo de Consulta *</label>
                            <input type="text" name="motivo" placeholder="Motivo principal de la consulta" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm bg-white focus:outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition-all">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Observaciones</label>
                            <textarea name="observaciones" rows="3" placeholder="Notas adicionales sobre la consulta" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm bg-white focus:outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition-all"></textarea>
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
            cargarEspecialidades();
            inicializarTipoPaciente();
        });

        // Función para cargar especialidades
        async function cargarEspecialidades() {
            try {
                const response = await fetch('/api/especialidades');
                let especialidades = await response.json();
                
                // Si no hay especialidades en la BD, usar opciones por defecto
                if (!especialidades || especialidades.length === 0) {
                    especialidades = [
                        {codigo: 'GENERAL', nombre: 'Medicina General'},
                        {codigo: 'CARDIO', nombre: 'Cardiología'},
                        {codigo: 'PEDIAT', nombre: 'Pediatría'},
                        {codigo: 'GINE', nombre: 'Ginecología'},
                        {codigo: 'DERMA', nombre: 'Dermatología'},
                        {codigo: 'NEURO', nombre: 'Neurología'},
                        {codigo: 'ORTOP', nombre: 'Ortopedia'},
                        {codigo: 'OFTAL', nombre: 'Oftalmología'}
                    ];
                }
                
                const select = document.querySelector('select[name="especialidad"]');
                select.innerHTML = '<option value="">Seleccione...</option>';
                
                especialidades.forEach(esp => {
                    const option = document.createElement('option');
                    option.value = esp.codigo;
                    option.textContent = esp.nombre;
                    select.appendChild(option);
                });
            } catch (error) {
                console.error('Error al cargar especialidades:', error);
                // En caso de error, cargar opciones por defecto
                const select = document.querySelector('select[name="especialidad"]');
                const opcionesDefecto = [
                    {codigo: 'GENERAL', nombre: 'Medicina General'},
                    {codigo: 'CARDIO', nombre: 'Cardiología'},
                    {codigo: 'PEDIAT', nombre: 'Pediatría'}
                ];
                
                select.innerHTML = '<option value="">Seleccione...</option>';
                opcionesDefecto.forEach(esp => {
                    const option = document.createElement('option');
                    option.value = esp.codigo;
                    option.textContent = esp.nombre;
                    select.appendChild(option);
                });
            }
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
            // Crear una sección para mostrar los datos del paciente encontrado
            const datosSection = document.createElement('div');
            datosSection.className = 'mt-4 p-4 bg-green-50 rounded-xl border border-green-200';
            datosSection.innerHTML = `
                <div class="flex items-center mb-3">
                    <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="font-semibold text-green-800">Paciente Existente Encontrado</span>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div><strong>Nombre:</strong> ${paciente.nombre}</div>
                    <div><strong>CI:</strong> ${paciente.ci}</div>
                    <div><strong>Teléfono:</strong> ${paciente.telefono || 'N/A'}</div>
                    <div><strong>Correo:</strong> ${paciente.correo || 'N/A'}</div>
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
            
            // Validar campos requeridos básicos
            if (!data.ci || !data.especialidad || !data.medico || !data.seguro || !data.motivo) {
                alert('Por favor complete todos los campos obligatorios');
                return;
            }

            // Validar que si es paciente nuevo, complete todos los datos personales
            if (data.tipo_paciente === 'nuevo') {
                if (!data.nombres || !data.apellidos || !data.sexo) {
                    alert('Por favor complete todos los datos personales obligatorios (nombres, apellidos, sexo)');
                    return;
                }
                
                // Validar que el sexo tenga un valor válido
                if (data.sexo === '' || data.sexo === null) {
                    alert('Por favor seleccione el sexo del paciente');
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

        // Función para habilitar/deshabilitar campos de consulta
        function habilitarCamposConsulta(habilitar) {
            const camposConsulta = [
                'select[name="especialidad"]',
                'select[name="medico"]',
                'select[name="seguro"]',
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
    </script>
</x-app-layout>
