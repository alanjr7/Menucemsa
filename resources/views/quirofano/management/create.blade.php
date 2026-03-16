@extends('layouts.app')

@section('content')
<div class="w-full p-6 bg-gray-50/50 min-h-screen">

    <!-- Page Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Nuevo Quirófano</h1>
            <p class="text-sm text-gray-500">Registrar un nuevo quirófano en el sistema</p>
        </div>
        <a href="{{ route('quirofanos.management.index') }}" class="flex items-center px-4 py-2 border border-gray-200 rounded-lg text-gray-600 bg-white hover:bg-gray-50 font-medium transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
        <form id="quirofanoForm" class="p-6">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                
                <!-- Información Básica -->
                <div class="space-y-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Información Básica</h3>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Número de Quirófano *</label>
                                <input type="number" name="nro" id="nro" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required min="1" placeholder="Ej: 6">
                                <p class="text-xs text-gray-500 mt-1">Número único identificador del quirófano</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Quirófano *</label>
                                <select name="tipo" id="tipo" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                                    <option value="">Seleccionar tipo...</option>
                                    <option value="General">General - Para cirugías comunes</option>
                                    <option value="Especializado">Especializado - Para procedimientos complejos</option>
                                    <option value="Urgencias">Urgencias - Para emergencias médicas</option>
                                    <option value="Pediatrico">Pediatrico - Para pacientes infantiles</option>
                                    <option value="Cardiologia">Cardiologia - Para procedimientos cardíacos</option>
                                    <option value="Neurologia">Neurologia - Para procedimientos neurológicos</option>
                                    <option value="Oftalmologia">Oftalmologia - Para procedimientos oculares</option>
                                    <option value="Ginecologia">Ginecologia - Para procedimientos ginecológicos</option>
                                    <option value="Urologia">Urologia - Para procedimientos urológicos</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Estado *</label>
                                <select name="estado" id="estado" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                                    <option value="">Seleccionar estado...</option>
                                    <option value="Activo">Activo - Disponible para uso</option>
                                    <option value="Inactivo">Inactivo - No disponible temporalmente</option>
                                    <option value="Mantenimiento">Mantenimiento - En reparación</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Información Adicional -->
                <div class="space-y-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Información Adicional</h3>
                        
                        <div class="space-y-4">
                            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                                <h4 class="text-sm font-semibold text-blue-800 mb-2">Tipos de Quirófano y Compatibilidad</h4>
                                <div class="text-sm text-blue-700 space-y-1">
                                    <p><strong>General:</strong> Compatible con todos los tipos de cirugía</p>
                                    <p><strong>Especializado:</strong> Cirugías mediana y mayor</p>
                                    <p><strong>Urgencias:</strong> Cirugías menor, mediana y ambulatoria</p>
                                    <p><strong>Pediatrico:</strong> Cirugías menor y ambulatoria infantil</p>
                                    <p><strong>Cardiologia/Neurologia:</strong> Procedimientos especializados</p>
                                </div>
                            </div>

                            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4">
                                <h4 class="text-sm font-semibold text-amber-800 mb-2">Recomendaciones</h4>
                                <div class="text-sm text-amber-700 space-y-1">
                                    <p>• Asigne números secuenciales para mejor organización</p>
                                    <p>• Considere el flujo de trabajo al asignar tipos</p>
                                    <p>• Mantenga al menos un quirófano de urgencias activo</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botones de Acción -->
            <div class="flex justify-end gap-4 mt-8 pt-6 border-t border-gray-200">
                <a href="{{ route('quirofanos.management.index') }}" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition-colors">
                    Cancelar
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium transition-colors">
                    Crear Quirófano
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('quirofanoForm');
    
    // SOLUCIÓN: Obtener el siguiente número disponible con manejo de errores
    // Si la API no existe, simplemente no pre-rellenamos el número
    fetch('{{ route("quirofanos.api.next-number") }}', {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
        .then(response => {
            // Solo procesar si la respuesta es 200-299
            if (!response.ok) {
                console.warn('API no disponible, continuando sin pre-rellenar número');
                return null;
            }
            return response.json();
        })
        .then(data => {
            if (data && data.nextNumber) {
                document.getElementById('nro').value = data.nextNumber;
                document.getElementById('nro').min = data.nextNumber;
            }
        })
        .catch(error => {
            console.warn('No se pudo obtener siguiente número:', error);
            console.log('El formulario funcionará sin pre-rellenar el número');
        });
    
    // SOLUCIÓN: Submit del formulario con manejo robusto de errores
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());
        
        // Validación básica en frontend
        if (!data.nro || !data.tipo || !data.estado) {
            alert('Por favor completa todos los campos requeridos');
            return;
        }
        
        // SOLUCIÓN: Usar route() helper de Laravel
        const submitUrl = '{{ route("quirofanos.management.store") }}';
        
        console.log('Enviando POST a:', submitUrl);
        console.log('Datos:', data);
        
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
            console.log('Status:', response.status);
            
            // Si es 404, mostrar error específico
            if (response.status === 404) {
                throw new Error('Ruta no encontrada (404) - Contacta al administrador');
            }
            
            if (!response.ok) {
                return response.json().then(errorData => {
                    throw new Error(errorData.message || `Error ${response.status}: ${response.statusText}`);
                });
            }
            
            return response.json();
        })
        .then(data => {
            console.log('Respuesta:', data);
            
            if (data.success) {
                alert(data.message || 'Quirófano creado exitosamente');
                window.location.href = '{{ route("quirofanos.management.index") }}';
            } else {
                alert(data.message || 'Error al crear el quirófano');
            }
        })
        .catch(error => {
            console.error('Error completo:', error);
            alert('Error: ' + error.message);
        });
    });
});
</script>
@endsection