@extends('layouts.app')

@section('content')
<div class="w-full p-6 bg-gray-50/50 min-h-screen">

    <!-- Page Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Editar Quirófano Q{{ $quirofano->id }}</h1>
            <p class="text-sm text-gray-500">Modificar información del quirófano</p>
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
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                
                <!-- Información Básica -->
                <div class="space-y-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Información Básica</h3>
                        
                        <div class="space-y-4">
                            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Número de Quirófano</label>
                                <p class="text-lg font-bold text-gray-900">Q{{ $quirofano->id }}</p>
                                <p class="text-xs text-gray-500 mt-1">El número se asigna automáticamente por la base de datos</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Quirófano *</label>
                                <select name="tipo" id="tipo" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                                    <option value="">Seleccionar tipo...</option>
                                    <option value="General" {{ $quirofano->tipo == 'General' ? 'selected' : '' }}>General - Para cirugías comunes</option>
                                    <option value="Especializado" {{ $quirofano->tipo == 'Especializado' ? 'selected' : '' }}>Especializado - Para procedimientos complejos</option>
                                    <option value="Urgencias" {{ $quirofano->tipo == 'Urgencias' ? 'selected' : '' }}>Urgencias - Para emergencias médicas</option>
                                    <option value="Pediatrico" {{ $quirofano->tipo == 'Pediatrico' ? 'selected' : '' }}>Pediatrico - Para pacientes infantiles</option>
                                    <option value="Cardiologia" {{ $quirofano->tipo == 'Cardiologia' ? 'selected' : '' }}>Cardiologia - Para procedimientos cardíacos</option>
                                    <option value="Neurologia" {{ $quirofano->tipo == 'Neurologia' ? 'selected' : '' }}>Neurologia - Para procedimientos neurológicos</option>
                                    <option value="Oftalmologia" {{ $quirofano->tipo == 'Oftalmologia' ? 'selected' : '' }}>Oftalmologia - Para procedimientos oculares</option>
                                    <option value="Ginecologia" {{ $quirofano->tipo == 'Ginecologia' ? 'selected' : '' }}>Ginecologia - Para procedimientos ginecológicos</option>
                                    <option value="Urologia" {{ $quirofano->tipo == 'Urologia' ? 'selected' : '' }}>Urologia - Para procedimientos urológicos</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Estado *</label>
                                <select name="estado" id="estado" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                                    <option value="">Seleccionar estado...</option>
                                    <option value="disponible" {{ $quirofano->estado == 'disponible' ? 'selected' : '' }}>Disponible - Listo para usar</option>
                                    <option value="ocupado" {{ $quirofano->estado == 'ocupado' ? 'selected' : '' }}>Ocupado - En uso actualmente</option>
                                    <option value="mantenimiento" {{ $quirofano->estado == 'mantenimiento' ? 'selected' : '' }}>Mantenimiento - En reparación</option>
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
                                <h4 class="text-sm font-semibold text-amber-800 mb-2">Estados</h4>
                                <div class="text-sm text-amber-700 space-y-1">
                                    <p><strong>Disponible:</strong> Listo para programar cirugías</p>
                                    <p><strong>Ocupado:</strong> Actualmente en uso</p>
                                    <p><strong>Mantenimiento:</strong> No disponible temporalmente</p>
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
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('quirofanoForm');
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());
        
        // Validación básica en frontend
        if (!data.tipo || !data.estado) {
            alert('Por favor completa todos los campos requeridos');
            return;
        }
        
        const submitUrl = '{{ route("quirofanos.management.update", $quirofano->id) }}';
        
        fetch(submitUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                ...data,
                _method: 'PUT'
            })
        })
        .then(response => {
            if (response.status === 404) {
                throw new Error('Ruta no encontrada (404)');
            }
            
            if (!response.ok) {
                return response.json().then(errorData => {
                    throw new Error(errorData.message || `Error ${response.status}`);
                });
            }
            
            return response.json();
        })
        .then(data => {
            if (data.success) {
                alert(data.message || 'Quirófano actualizado exitosamente');
                window.location.href = '{{ route("quirofanos.management.index") }}';
            } else {
                alert(data.message || 'Error al actualizar el quirófano');
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
