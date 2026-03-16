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
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Información del Paciente</h3>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">CI del Paciente *</label>
                                <input type="number" name="ci_paciente" id="ci_paciente" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required placeholder="Ej: 12345678">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nombre del Paciente</label>
                                <input type="text" id="nombre_paciente" class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50" readonly placeholder="Se autocompletará">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Información Quirúrgica -->
                <div class="space-y-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Información Quirúrgica</h3>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">CI del Cirujano *</label>
                                <input type="number" name="ci_cirujano" id="ci_cirujano" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required placeholder="Ej: 87654321">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nombre del Cirujano</label>
                                <input type="text" id="nombre_cirujano" class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50" readonly placeholder="Se autocompletará">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Número de Quirófano *</label>
                                <select name="nro_quirofano" id="nro_quirofano" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                                    <option value="">Seleccionar quirófano...</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Cirugía *</label>
                                <select name="tipo_cirugia" id="tipo_cirugia" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                                    <option value="">Seleccionar tipo...</option>
                                    <option value="menor">Menor</option>
                                    <option value="mediana">Mediana</option>
                                    <option value="mayor">Mayor</option>
                                    <option value="ambulatoria">Ambulatoria</option>
                                </select>
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
document.addEventListener('DOMContentLoaded', function() {
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

    // Autocompletar nombre del paciente
    document.getElementById('ci_paciente').addEventListener('blur', function() {
        const ci = this.value;
        if (ci) {
            fetch(`/api/paciente/${ci}`, {
                headers: {
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('nombre_paciente').value = data.paciente.nombre;
                }
            })
            .catch(error => {
                console.warn('No se encontró el paciente:', error);
            });
        }
    });

    // Autocompletar nombre del cirujano
    document.getElementById('ci_cirujano').addEventListener('blur', function() {
        const ci = this.value;
        if (ci) {
            fetch(`/api/medico/${ci}`, {
                headers: {
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('nombre_cirujano').value = data.medico.nombre;
                }
            })
            .catch(error => {
                console.warn('No se encontró el médico:', error);
            });
        }
    });

    // Submit del formulario
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());
        
        // Validación básica
        if (!data.ci_paciente || !data.ci_cirujano || !data.nro_quirofano || !data.tipo_cirugia || !data.fecha || !data.hora_inicio_estimada) {
            alert('Por favor completa todos los campos requeridos');
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
</script>
@endsection
