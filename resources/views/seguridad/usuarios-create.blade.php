<x-app-layout>
    <div class="p-6 bg-gray-50/50 min-h-screen">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Nuevo Usuario</h1>
                    <p class="text-sm text-gray-500">Crear un nuevo usuario en el sistema</p>
                </div>
                <a href="{{ route('seguridad.usuarios.index') }}" class="px-4 py-2 text-gray-600 hover:text-gray-800 font-medium flex items-center gap-2 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Volver
                </a>
            </div>

            <!-- Form -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8">
                <form action="{{ route('seguridad.usuarios.store') }}" method="POST">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Nombre -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Nombre Completo
                            </label>
                            <input type="text" 
                                   name="name" 
                                   value="{{ old('name') }}"
                                   class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   required>
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Correo Electrónico
                            </label>
                            <input type="email" 
                                   name="email" 
                                   value="{{ old('email') }}"
                                   class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   required>
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Rol -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Rol
                            </label>
                            <select name="role" 
                                    class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    required>
                                <option value="">Seleccionar rol...</option>
                                <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Administrador</option>
                                <option value="dirmedico" {{ old('role') == 'dirmedico' ? 'selected' : '' }}>Director Médico</option>
                                <option value="reception" {{ old('role') == 'reception' ? 'selected' : '' }}>Recepción</option>
                                <option value="emergencia" {{ old('role') == 'emergencia' ? 'selected' : '' }}>Emergencia</option>
                                <option value="caja" {{ old('role') == 'caja' ? 'selected' : '' }}>Caja</option>
                                <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>Usuario</option>
                            </select>
                            @error('role')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Contraseña -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Contraseña
                            </label>
                            <input type="password" 
                                   name="password" 
                                   class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   required
                                   placeholder="Mínimo 8 caracteres">
                            @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Confirmar Contraseña -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Confirmar Contraseña
                            </label>
                            <input type="password" 
                                   name="password_confirmation" 
                                   class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   required
                                   placeholder="Repetir contraseña">
                            @error('password_confirmation')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <!-- Datos adicionales para médicos -->
                        <div id="medico-extra-fields" class="md:col-span-2 mt-4 {{ old('role') === 'dirmedico' ? '' : 'hidden' }}">
                            <div class="border-t border-gray-100 pt-4 mt-2">
                                <h3 class="text-sm font-semibold text-gray-700 mb-3">Datos del Médico</h3>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            CI del Médico *
                                        </label>
                                        <input type="number"
                                               name="ci"
                                               value="{{ old('ci') }}"
                                               class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        @error('ci')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Teléfono
                                        </label>
                                        <input type="number"
                                               name="telefono"
                                               value="{{ old('telefono') }}"
                                               class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        @error('telefono')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Especialidad *
                                        </label>
                                        <select name="codigo_especialidad"
                                                class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                            <option value="">Seleccionar especialidad...</option>
                                            @foreach($especialidades as $esp)
                                                <option value="{{ $esp->codigo }}" {{ old('codigo_especialidad') == $esp->codigo ? 'selected' : '' }}>
                                                    {{ $esp->nombre }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('codigo_especialidad')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Información de roles -->
                    <div class="mt-8 p-4 bg-blue-50 rounded-lg">
                        <h3 class="text-sm font-medium text-blue-900 mb-2">Información de Roles</h3>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3 text-sm">
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 bg-purple-500 rounded-full"></span>
                                <span class="text-blue-700"><strong>Administrador:</strong> Acceso completo</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                                <span class="text-blue-700"><strong>Director Médico:</strong> Consultas médicas (equivalente a Médico)</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                                <span class="text-blue-700"><strong>Recepción:</strong> Registro pacientes</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 bg-orange-500 rounded-full"></span>
                                <span class="text-blue-700"><strong>Emergencia:</strong> Urgencias</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 bg-indigo-500 rounded-full"></span>
                                <span class="text-blue-700"><strong>Caja:</strong> Pagos y facturación</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 bg-gray-500 rounded-full"></span>
                                <span class="text-blue-700"><strong>Usuario:</strong> Acceso básico</span>
                            </div>
                        </div>
                        <p class="text-xs text-blue-600 mt-2">
                            <strong>Nota:</strong> Los roles "Director Médico" y "Médico" son equivalentes. 
                            Ambos controlan a todos los usuarios con rol médico.
                        </p>
                    </div>

                    <!-- Botones -->
                    <div class="mt-8 flex justify-end gap-4">
                        <a href="{{ route('seguridad.usuarios.index') }}" 
                           class="px-6 py-2 text-gray-600 hover:text-gray-800 font-medium transition">
                            Cancelar
                        </a>
                        <button type="submit" 
                                class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Crear Usuario
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const roleSelect = document.querySelector('select[name="role"]');
            const medicoFields = document.getElementById('medico-extra-fields');

            function toggleMedicoFields() {
                if (!roleSelect || !medicoFields) return;
                if (roleSelect.value === 'dirmedico') {
                    medicoFields.classList.remove('hidden');
                } else {
                    medicoFields.classList.add('hidden');
                }
            }

            if (roleSelect) {
                roleSelect.addEventListener('change', toggleMedicoFields);
                toggleMedicoFields();
            }
        });
    </script>
</x-app-layout>
