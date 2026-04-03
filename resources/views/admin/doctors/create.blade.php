@extends('layouts.app')

@section('content')
    <div class="p-6 bg-gray-50 min-h-screen">
        <!-- Header -->
        <div class="flex justify-between items-end mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Crear Nuevo Doctor</h1>
                <p class="text-sm text-gray-500">Registro de médicos y asignación de especialidad</p>
            </div>
            <a href="{{ route('admin.doctors.index') }}" class="flex items-center px-4 py-2 bg-white border border-gray-200 text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition-all shadow-sm">
                <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Volver al listado
            </a>
        </div>

        <!-- Progress Steps -->
        <div class="mb-8">
            <div class="flex items-center justify-center">
                <div class="flex items-center">
                    <div class="flex items-center justify-center w-10 h-10 rounded-full bg-blue-600 text-white font-bold text-sm" id="step1-indicator">1</div>
                    <div class="ml-2 text-sm font-medium text-blue-600" id="step1-text">Datos del Usuario</div>
                </div>
                <div class="w-16 h-0.5 bg-gray-200 mx-4" id="step-line"></div>
                <div class="flex items-center">
                    <div class="flex items-center justify-center w-10 h-10 rounded-full bg-gray-200 text-gray-500 font-bold text-sm" id="step2-indicator">2</div>
                    <div class="ml-2 text-sm font-medium text-gray-500" id="step2-text">Info. Profesional</div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 max-w-4xl mx-auto">
            <form method="POST" action="{{ route('admin.doctors.store') }}" id="doctorForm">
                @csrf

                <!-- Step 1: Datos del Usuario -->
                <div id="step1" class="space-y-6">
                    <div class="border-b border-gray-100 pb-4 mb-6">
                        <h2 class="text-lg font-semibold text-gray-800 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Información Personal y de Acceso
                        </h2>
                        <p class="text-sm text-gray-500 mt-1">Datos básicos del médico para el sistema</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Nombre -->
                        <div class="group">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Nombre Completo <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                                <input type="text" name="name" value="{{ old('name') }}" 
                                       class="w-full pl-10 pr-3 py-2.5 border border-gray-200 rounded-xl text-sm bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all"
                                       placeholder="Ej: Dr. Juan Carlos Pérez González" required>
                            </div>
                            @error('name') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- CI -->
                        <div class="group">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Cédula de Identidad <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3 3 0 00-3 3"/>
                                    </svg>
                                </div>
                                <input type="text" name="ci" value="{{ old('ci') }}" 
                                       class="w-full pl-10 pr-3 py-2.5 border border-gray-200 rounded-xl text-sm bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all"
                                       placeholder="Ej: 12345678" required>
                            </div>
                            @error('ci') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Email -->
                        <div class="group">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Correo Electrónico <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                                    </svg>
                                </div>
                                <input type="email" name="email" value="{{ old('email') }}" 
                                       class="w-full pl-10 pr-3 py-2.5 border border-gray-200 rounded-xl text-sm bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all"
                                       placeholder="doctor@clinica.com" required>
                            </div>
                            @error('email') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Teléfono -->
                        <div class="group">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Teléfono</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                </div>
                                <input type="tel" name="telefono" value="{{ old('telefono') }}" 
                                       class="w-full pl-10 pr-3 py-2.5 border border-gray-200 rounded-xl text-sm bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all"
                                       placeholder="Ej: 0414-1234567">
                            </div>
                            @error('telefono') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Contraseña -->
                        <div class="group">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Contraseña <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                </div>
                                <input type="password" name="password" id="password"
                                       class="w-full pl-10 pr-10 py-2.5 border border-gray-200 rounded-xl text-sm bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all"
                                       placeholder="Mínimo 8 caracteres" required>
                                <button type="button" onclick="togglePassword('password', this)" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                    <svg class="h-5 w-5 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </button>
                            </div>
                            @error('password') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Confirmar Contraseña -->
                        <div class="group">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Confirmar Contraseña <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <input type="password" name="password_confirmation" id="password_confirmation"
                                       class="w-full pl-10 pr-10 py-2.5 border border-gray-200 rounded-xl text-sm bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all"
                                       placeholder="Repita la contraseña" required>
                                <button type="button" onclick="togglePassword('password_confirmation', this)" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                    <svg class="h-5 w-5 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end pt-6">
                        <button type="button" onclick="goToStep(2)" class="px-6 py-3 bg-blue-600 text-white rounded-xl font-medium hover:bg-blue-700 transition-colors flex items-center">
                            Continuar
                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Step 2: Información Profesional -->
                <div id="step2" class="space-y-6 hidden">
                    <div class="border-b border-gray-100 pb-4 mb-6">
                        <h2 class="text-lg font-semibold text-gray-800 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                            </svg>
                            Información Profesional
                        </h2>
                        <p class="text-sm text-gray-500 mt-1">Datos de la especialidad y área de trabajo</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Especialidad -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Especialidad <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                                    </svg>
                                </div>
                                <select name="codigo_especialidad" id="especialidad_select" 
                                        class="w-full pl-10 pr-10 py-2.5 border border-gray-200 rounded-xl text-sm bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all appearance-none cursor-pointer"
                                        required>
                                    <option value="">Seleccione una especialidad...</option>
                                    @foreach($especialidades as $especialidad)
                                        <option value="{{ $especialidad->codigo }}" {{ old('codigo_especialidad') == $especialidad->codigo ? 'selected' : '' }}>
                                            {{ $especialidad->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </div>
                            </div>
                            @error('codigo_especialidad') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                            
                            <!-- Especialidades populares (quick select) -->
                            <div class="mt-3 flex flex-wrap gap-2">
                                <span class="text-xs text-gray-500">Populares:</span>
                                @php
                                    $populares = $especialidades->take(5);
                                @endphp
                                @foreach($populares as $esp)
                                    <button type="button" onclick="selectEspecialidad('{{ $esp->codigo }}')" 
                                            class="text-xs px-3 py-1 bg-gray-100 hover:bg-blue-100 text-gray-600 hover:text-blue-600 rounded-full transition-colors">
                                        {{ $esp->nombre }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Preview de la especialidad seleccionada -->
                    <div id="especialidad-preview" class="hidden bg-blue-50 rounded-xl p-4 border border-blue-100">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Especialidad seleccionada:</p>
                                <p class="font-semibold text-gray-800" id="especialidad-nombre"></p>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-between pt-6">
                        <button type="button" onclick="goToStep(1)" class="px-6 py-3 border border-gray-200 text-gray-700 rounded-xl font-medium hover:bg-gray-50 transition-colors flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                            Anterior
                        </button>
                        <button type="submit" class="px-6 py-3 bg-green-600 text-white rounded-xl font-medium hover:bg-green-700 transition-colors flex items-center shadow-md">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Guardar Doctor
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Wizard Navigation
        function goToStep(step) {
            // Validar campos del paso actual antes de avanzar
            if (step === 2) {
                const requiredFields = ['name', 'ci', 'email', 'password', 'password_confirmation'];
                let valid = true;
                requiredFields.forEach(field => {
                    const input = document.querySelector(`[name="${field}"]`);
                    if (!input.value.trim()) {
                        input.classList.add('border-red-500');
                        valid = false;
                    } else {
                        input.classList.remove('border-red-500');
                    }
                });

                // Validar que las contraseñas coincidan
                const password = document.querySelector('[name="password"]').value;
                const confirm = document.querySelector('[name="password_confirmation"]').value;
                if (password && confirm && password !== confirm) {
                    alert('Las contraseñas no coinciden');
                    valid = false;
                }

                if (!valid) return;
            }

            // Ocultar todos los pasos
            document.getElementById('step1').classList.add('hidden');
            document.getElementById('step2').classList.add('hidden');
            
            // Mostrar paso seleccionado
            document.getElementById(`step${step}`).classList.remove('hidden');
            
            // Actualizar indicadores
            updateStepIndicators(step);
        }

        function updateStepIndicators(currentStep) {
            const indicators = [
                { num: 1, el: document.getElementById('step1-indicator'), text: document.getElementById('step1-text') },
                { num: 2, el: document.getElementById('step2-indicator'), text: document.getElementById('step2-text') }
            ];

            indicators.forEach(ind => {
                if (ind.num <= currentStep) {
                    ind.el.classList.remove('bg-gray-200', 'text-gray-500');
                    ind.el.classList.add('bg-blue-600', 'text-white');
                    ind.text.classList.remove('text-gray-500');
                    ind.text.classList.add('text-blue-600');
                } else {
                    ind.el.classList.remove('bg-blue-600', 'text-white');
                    ind.el.classList.add('bg-gray-200', 'text-gray-500');
                    ind.text.classList.remove('text-blue-600');
                    ind.text.classList.add('text-gray-500');
                }
            });

            // Línea de progreso
            const line = document.getElementById('step-line');
            if (currentStep === 2) {
                line.classList.remove('bg-gray-200');
                line.classList.add('bg-blue-600');
            } else {
                line.classList.remove('bg-blue-600');
                line.classList.add('bg-gray-200');
            }
        }

        // Toggle password visibility
        function togglePassword(inputId, button) {
            const input = document.getElementById(inputId);
            const type = input.type === 'password' ? 'text' : 'password';
            input.type = type;
            
            // Cambiar icono
            button.innerHTML = type === 'password' 
                ? `<svg class="h-5 w-5 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>`
                : `<svg class="h-5 w-5 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>`;
        }

        // Quick select especialidad
        function selectEspecialidad(codigo) {
            const select = document.getElementById('especialidad_select');
            select.value = codigo;
            updateEspecialidadPreview();
        }

        // Update especialidad preview
        function updateEspecialidadPreview() {
            const select = document.getElementById('especialidad_select');
            const preview = document.getElementById('especialidad-preview');
            const nombre = document.getElementById('especialidad-nombre');
            
            if (select.value) {
                const option = select.options[select.selectedIndex];
                nombre.textContent = option.text;
                preview.classList.remove('hidden');
            } else {
                preview.classList.add('hidden');
            }
        }

        // Event listeners
        document.getElementById('especialidad_select').addEventListener('change', updateEspecialidadPreview);

        // Remove error styling on input
        document.querySelectorAll('input, select').forEach(input => {
            input.addEventListener('input', function() {
                this.classList.remove('border-red-500');
            });
        });
    </script>
@endsection
