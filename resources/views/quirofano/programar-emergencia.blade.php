@extends('layouts.app')

@section('content')
<div class="w-full p-6 bg-gray-50/50 min-h-screen">

    <!-- Page Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Nueva Cita Quirúrgica</h1>
            <p class="text-sm text-gray-500">Programar una nueva intervención quirúrgica</p>
        </div>
        <a href="{{ route('quirofano.index') }}" class="flex items-center px-4 py-2 border border-gray-200 rounded-lg text-gray-600 bg-white hover:bg-gray-50 font-medium transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
        <form id="citaQuirurgicaForm" class="p-6">
            @csrf
            <input type="hidden" name="emergency_id" value="{{ $emergencia->id }}">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                
                <!-- Columna Izquierda - Información Básica -->
                <div class="space-y-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Información del Paciente</h3>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Paciente *</label>
                                <input type="hidden" name="ci_paciente" value="{{ $emergencia->patient_id }}">
                                <div class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 text-gray-700">
                                    @if($emergencia->is_temp_id)
                                        Paciente Temporal (ID: {{ $emergencia->patient_id }})
                                    @elseif($emergencia->paciente)
                                        {{ $emergencia->paciente->nombre }} (CI: {{ $emergencia->patient_id }})
                                    @else
                                        Paciente (CI: {{ $emergencia->patient_id }})
                                    @endif
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Fecha de Cirugía *</label>
                                <input type="date" name="fecha" id="fecha" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Hora de Inicio Estimada *</label>
                                <input type="time" name="hora_inicio_estimada" id="hora_inicio" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Información Quirúrgica</h3>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Cirugía *</label>
                                <select name="tipo_cirugia" id="tipo_cirugia" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                                    <option value="">Seleccionar tipo...</option>
                                    @foreach($tiposCirugia as $tipo)
                                        <option value="{{ $tipo->nombre }}" data-duracion="{{ $tipo->duracion_minutos }}" data-costo="{{ $tipo->costo_base }}">
                                            {{ $tipo->nombre }} - {{ $tipo->duracion_formateada }} - ${{ number_format($tipo->costo_base, 2) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Quirófano *</label>
                                <select name="nro_quirofano" id="quirofano" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                                    <option value="">Seleccionar quirófano...</option>
                                    @foreach($quirofanos as $quirofano)
                                        <option value="{{ $quirofano->id }}" data-tipo="{{ $quirofano->tipo }}">Quirófano {{ $quirofano->nro }} - {{ $quirofano->tipo }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Descripción de Cirugía</label>
                                <textarea name="descripcion_cirugia" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Describir el procedimiento quirúrgico..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Columna Derecha - Equipo Quirúrgico -->
                <div class="space-y-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Equipo Quirúrgico</h3>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Cirujano Responsable *</label>
                                <select name="ci_cirujano" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                                    <option value="">Seleccionar cirujano...</option>
                                    @foreach($medicos as $medico)
                                        <option value="{{ $medico->ci }}">{{ $medico->usuario->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Instrumentista</label>
                                <input type="text" name="nombre_instrumentista" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Nombre del instrumentista...">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Anestesiólogo</label>
                                <input type="text" name="nombre_anestesiologo" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Nombre del anestesiólogo...">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Costo Base ($) *</label>
                                <input type="number" name="costo_base" id="costo_manual" step="0.01" min="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Ingrese el costo..." required>
                            </div>
                        </div>
                    </div>

                    <!-- Resumen de Programación -->
                    <div class="bg-gray-50 rounded-xl p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Resumen de Programación</h3>
                        
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Duración Estimada:</span>
                                <span id="duracion_estimada" class="font-semibold text-gray-900">-</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Hora Fin Estimada:</span>
                                <span id="hora_fin_estimada" class="font-semibold text-gray-900">-</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Costo Base:</span>
                                <span id="costo_base" class="font-semibold text-gray-900">$0.00</span>
                            </div>
                            <div class="flex justify-between items-center pt-3 border-t border-gray-200">
                                <span class="text-sm text-gray-600">Disponibilidad:</span>
                                <span id="disponibilidad" class="font-semibold">-</span>
                            </div>
                        </div>
                    </div>

                    <!-- Observaciones -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Observaciones</label>
                        <textarea name="observaciones" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Notas adicionales..."></textarea>
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
    const form = document.getElementById('citaQuirurgicaForm');
    const tipoCirugiaSelect = document.getElementById('tipo_cirugia');
    const horaInicioInput = document.getElementById('hora_inicio');
    const fechaInput = document.getElementById('fecha');
    const costoManualInput = document.getElementById('costo_manual');
    
    // Establecer fecha mínima como hoy
    fechaInput.min = new Date().toISOString().split('T')[0];
    
    // Actualizar resumen cuando cambian los campos
    function actualizarResumen() {
        const tipoCirugia = tipoCirugiaSelect.options[tipoCirugiaSelect.selectedIndex];
        const duracion = tipoCirugia ? parseInt(tipoCirugia.dataset.duracion) : 0;
        const costoTipo = tipoCirugia ? parseFloat(tipoCirugia.dataset.costo) : 0;
        const horaInicio = horaInicioInput.value;
        
        // Actualizar duración
        document.getElementById('duracion_estimada').textContent = duracion ? `${duracion} minutos` : '-';
        
        // Actualizar hora fin estimada
        if (horaInicio && duracion) {
            const [horas, minutos] = horaInicio.split(':').map(Number);
            const totalMinutos = horas * 60 + minutos + duracion;
            const finHoras = Math.floor(totalMinutos / 60);
            const finMinutos = totalMinutos % 60;
            document.getElementById('hora_fin_estimada').textContent = 
                `${String(finHoras).padStart(2, '0')}:${String(finMinutos).padStart(2, '0')}`;
        } else {
            document.getElementById('hora_fin_estimada').textContent = '-';
        }
        
        // Actualizar costo: usar manual si existe, sino el del tipo
        const costoManual = parseFloat(costoManualInput.value) || 0;
        const costoFinal = costoManual > 0 ? costoManual : costoTipo;
        document.getElementById('costo_base').textContent = costoFinal ? `$${costoFinal.toFixed(2)}` : '$0.00';
        
        // Verificar disponibilidad
        verificarDisponibilidad();
    }
    
    function verificarDisponibilidad() {
        const nroQuirofano = document.getElementById('quirofano').value;
        const fecha = fechaInput.value;
        const horaInicio = horaInicioInput.value;
        const tipoCirugia = tipoCirugiaSelect.value;
        
        if (!nroQuirofano || !fecha || !horaInicio || !tipoCirugia) {
            document.getElementById('disponibilidad').textContent = '-';
            document.getElementById('disponibilidad').className = 'font-semibold';
            return;
        }
        
        fetch('/quirofano/disponibilidad', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                nro_quirofano: nroQuirofano,
                fecha: fecha,
                hora_inicio: horaInicio,
                tipo_cirugia: tipoCirugia
            })
        })
        .then(response => response.json())
        .then(data => {
            const disponibilidadElement = document.getElementById('disponibilidad');
            if (data.error) {
                // Mostrar error específico del servidor
                console.error('Error del servidor:', data);
                disponibilidadElement.textContent = 'Error: ' + data.message;
                disponibilidadElement.className = 'font-semibold text-red-600';
            } else if (data.disponible) {
                disponibilidadElement.textContent = 'Disponible';
                disponibilidadElement.className = 'font-semibold text-green-600';
            } else {
                disponibilidadElement.textContent = 'Ocupado';
                disponibilidadElement.className = 'font-semibold text-red-600';
                if (data.conflictos && data.conflictos.length > 0) {
                    console.log('Conflictos encontrados:', data.conflictos);
                }
            }
        })
        .catch(error => {
            console.error('Error verificando disponibilidad:', error);
            document.getElementById('disponibilidad').textContent = 'Error';
            document.getElementById('disponibilidad').className = 'font-semibold text-red-600';
        });
    }
    
    // Event listeners
    tipoCirugiaSelect.addEventListener('change', actualizarResumen);
    horaInicioInput.addEventListener('change', actualizarResumen);
    document.getElementById('quirofano').addEventListener('change', actualizarResumen);
    fechaInput.addEventListener('change', actualizarResumen);
    costoManualInput.addEventListener('input', actualizarResumen);
    
    // Submit del formulario
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Validación básica del lado del cliente
        const requiredFields = ['ci_paciente', 'ci_cirujano', 'nro_quirofano', 'tipo_cirugia', 'fecha', 'hora_inicio_estimada', 'costo_base'];
        const missingFields = [];
        
        requiredFields.forEach(field => {
            const element = document.querySelector(`[name="${field}"]`);
            if (!element || !element.value) {
                missingFields.push(field);
            }
        });
        
        if (missingFields.length > 0) {
            alert('Por favor, complete todos los campos requeridos:\n' + missingFields.join(', '));
            return;
        }
        
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());
        
        console.log('Enviando datos:', data);
        
        fetch('/quirofano/emergencia/store', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(async response => {
            const text = await response.text();
            console.log('RESPUESTA RAW:', text);

            let data;
            try {
                data = JSON.parse(text);
            } catch (e) {
                data = { message: text };
            }

            if (!response.ok) {
                console.error('HTTP Error:', response.status, data);
                let errorMessage = `HTTP error! status: ${response.status}\n\n`;
                if (data.errors) {
                    errorMessage += 'Errores de validación:\n';
                    for (const [field, messages] of Object.entries(data.errors)) {
                        errorMessage += `- ${field}: ${Array.isArray(messages) ? messages.join(', ') : messages}\n`;
                    }
                } else if (data.message) {
                    errorMessage += `Mensaje: ${data.message}`;
                } else {
                    errorMessage += `Respuesta: ${text}`;
                }
                throw new Error(errorMessage);
            }

            return data;
        })
        .then(data => {
            console.log('Respuesta data:', data);

            if (data.success) {
                alert(data.message);
                window.location.href = '/quirofano';
            } else {
                if (data.errors) {
                    let errorMessage = 'Errores de validación:\n\n';
                    for (const [field, messages] of Object.entries(data.errors)) {
                        errorMessage += `${field}: ${Array.isArray(messages) ? messages.join(', ') : messages}\n`;
                    }
                    console.log('Validation errors:', data.errors);
                    alert(errorMessage);
                } else {
                    console.log('Other error:', data);
                    alert(data.message || 'Error al programar la cita');
                }
            }
        })
        .catch(error => {
            console.error('Error completo:', error);
            console.error('Error stack:', error.stack);
            
            if (error.name === 'TypeError' && error.message.includes('Failed to fetch')) {
                alert('Error de conexión: No se puede conectar al servidor. Verifica tu conexión a internet.');
            } else if (error.message.includes('HTTP error')) {
                alert('Error del servidor: ' + error.message);
            } else {
                alert('Error inesperado: ' + error.message);
            }
        });
    });
});
</script>
@endsection
