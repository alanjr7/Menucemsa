@extends('layouts.app')

@section('title', 'Completar Datos del Paciente - Recepción')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center gap-4 mb-4">
                <a href="{{ route('reception') }}" 
                   class="flex items-center text-gray-600 hover:text-orange-600 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Volver a Recepción
                </a>
            </div>
            <h1 class="text-3xl font-bold text-gray-800">Completar Datos del Paciente</h1>
            <p class="text-gray-600 mt-2">Convertir paciente temporal de emergencia a registro completo</p>
        </div>

        <!-- Información de la Emergencia -->
        <div class="bg-gradient-to-r from-orange-50 to-orange-100 rounded-2xl p-6 border border-orange-200 mb-8">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-orange-200 rounded-full flex items-center justify-center">
                    <svg class="w-7 h-7 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Código de Emergencia: <span class="font-bold text-orange-700 text-lg">{{ $emergency->code }}</span></p>
                    <p class="text-sm text-gray-600">ID Temporal: <span class="font-mono text-orange-700">{{ $emergency->temp_id ?? 'Sin ID temporal' }}</span></p>
                    <p class="text-sm text-gray-500 mt-1">Tipo de ingreso: {{ $emergency->tipo_ingreso_label ?? 'General' }}</p>
                </div>
            </div>
        </div>

        <!-- Formulario -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-orange-500 to-orange-600 text-white p-6">
                <h2 class="text-xl font-bold">Datos Personales del Paciente</h2>
                <p class="text-orange-100 text-sm mt-1">Complete todos los campos obligatorios marcados con *</p>
            </div>

            <div class="p-8">
                <form id="formCompletarDatos" action="{{ route('reception.completar-datos-paciente.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="emergency_id" value="{{ $emergency->id }}">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- CI -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Cédula de Identidad (CI) <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="ci" id="ci" placeholder="Número de CI del paciente" 
                                class="w-full border border-gray-300 rounded-xl px-4 py-3 text-base bg-white focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition-all" 
                                required>
                            <p class="text-xs text-gray-500 mt-1">Este será el identificador único del paciente en el sistema</p>
                            @error('ci')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Nombres -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Nombres <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="nombres" id="nombres" placeholder="Nombres del paciente" 
                                class="w-full border border-gray-300 rounded-xl px-4 py-3 text-base bg-white focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition-all" 
                                required>
                        </div>
                        
                        <!-- Apellidos -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Apellidos <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="apellidos" id="apellidos" placeholder="Apellidos del paciente" 
                                class="w-full border border-gray-300 rounded-xl px-4 py-3 text-base bg-white focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition-all" 
                                required>
                        </div>
                        
                        <!-- Sexo -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Sexo <span class="text-red-500">*</span>
                            </label>
                            <select name="sexo" id="sexo" 
                                class="w-full border border-gray-300 rounded-xl px-4 py-3 text-base bg-white focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition-all" 
                                required>
                                <option value="">Seleccione...</option>
                                <option value="Masculino">Masculino</option>
                                <option value="Femenino">Femenino</option>
                            </select>
                        </div>
                        
                        <!-- Teléfono -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Teléfono</label>
                            <input type="tel" name="telefono" id="telefono" placeholder="Ej: 0414-1234567" 
                                class="w-full border border-gray-300 rounded-xl px-4 py-3 text-base bg-white focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition-all">
                        </div>
                        
                        <!-- Correo -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Correo Electrónico</label>
                            <input type="email" name="correo" id="correo" placeholder="correo@ejemplo.com" 
                                class="w-full border border-gray-300 rounded-xl px-4 py-3 text-base bg-white focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition-all">
                        </div>
                        
                        <!-- Dirección -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Dirección</label>
                            <textarea name="direccion" id="direccion" rows="3" placeholder="Dirección completa del paciente" 
                                class="w-full border border-gray-300 rounded-xl px-4 py-3 text-base bg-white focus:outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-200 transition-all"></textarea>
                        </div>
                    </div>

                    <!-- Botones de Acción -->
                    <div class="flex justify-between items-center pt-8 border-t border-gray-200 mt-8 gap-4">
                        <a href="{{ route('reception') }}" 
                           class="px-8 py-3 border border-gray-300 rounded-xl text-gray-700 font-medium hover:bg-gray-50 transition-colors text-base">
                            Cancelar
                        </a>
                        <button type="submit" 
                            class="px-8 py-3 bg-orange-500 text-white rounded-xl font-medium hover:bg-orange-600 transition-colors flex items-center text-base shadow-lg shadow-orange-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Guardar Datos del Paciente
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Mensajes de éxito/error -->
        @if (session('success'))
            <div class="mt-6 bg-green-50 border border-green-200 rounded-xl p-4 flex items-center gap-3">
                <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-green-700">{{ session('success') }}</p>
            </div>
        @endif

        @if (session('error'))
            <div class="mt-6 bg-red-50 border border-red-200 rounded-xl p-4 flex items-center gap-3">
                <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-red-700">{{ session('error') }}</p>
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.getElementById('formCompletarDatos').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        // Validar campos requeridos
        const ci = formData.get('ci');
        const nombres = formData.get('nombres');
        const apellidos = formData.get('apellidos');
        const sexo = formData.get('sexo');
        
        if (!ci || !nombres || !apellidos || !sexo) {
            alert('Por favor complete todos los campos obligatorios: CI, Nombres, Apellidos y Sexo');
            return;
        }
        
        // Deshabilitar botón durante el envío
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<svg class="w-5 h-5 mr-2 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Guardando...';
        
        try {
            const response = await fetch(this.action, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(Object.fromEntries(formData))
            });
            
            const result = await response.json();
            
            if (result.success) {
                alert('Datos del paciente guardados correctamente');
                window.location.href = '{{ route("reception") }}';
            } else {
                alert('Error: ' + result.message);
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error al guardar los datos del paciente');
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    });
</script>
@endsection
