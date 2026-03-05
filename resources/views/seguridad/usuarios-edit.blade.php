<x-app-layout>
    <div class="p-6 bg-gray-50/50 min-h-screen">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Editar Usuario</h1>
                    <p class="text-sm text-gray-500">Modificar información del usuario</p>
                </div>
                <a href="{{ route('seguridad.usuarios.index') }}" class="px-4 py-2 text-gray-600 hover:text-gray-800 font-medium flex items-center gap-2 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Volver
                </a>
            </div>

            <!-- Advertencia si es el propio usuario -->
            @if(auth()->user()->id === $user->id)
            <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <h3 class="text-sm font-medium text-yellow-800">Advertencia de Seguridad</h3>
                        <p class="text-sm text-yellow-700 mt-1">Estás editando tu propio usuario. Por seguridad, no podrás modificar tu propio rol. Si necesitas cambiar tu rol, contacta a otro administrador.</p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Form -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8">
                <form action="{{ route('seguridad.usuarios.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Nombre -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Nombre Completo
                            </label>
                            <input type="text" 
                                   name="name" 
                                   value="{{ old('name', $user->name) }}"
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
                                   value="{{ old('email', $user->email) }}"
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
                                @if(auth()->user()->id === $user->id)
                                    <span class="text-xs text-yellow-600 ml-2">(No editable para tu propio usuario)</span>
                                @endif
                            </label>
                            @if(auth()->user()->id === $user->id)
                                <input type="text" 
                                       value="{{ $user->role }}" 
                                       disabled
                                       class="w-full px-4 py-2 border border-gray-200 rounded-lg bg-gray-50 text-gray-500 cursor-not-allowed">
                                <input type="hidden" name="role" value="{{ $user->role }}">
                            @else
                                <select name="role" 
                                        class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        required>
                                    <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Administrador</option>
                                    <option value="dirmedico" {{ old('role', $user->role) == 'dirmedico' ? 'selected' : '' }}>Médico</option>
                                    <option value="reception" {{ old('role', $user->role) == 'reception' ? 'selected' : '' }}>Recepción</option>
                                    <option value="emergencia" {{ old('role', $user->role) == 'emergencia' ? 'selected' : '' }}>Emergencia</option>
                                    <option value="caja" {{ old('role', $user->role) == 'caja' ? 'selected' : '' }}>Caja</option>
                                </select>
                            @endif
                            @error('role')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Contraseña (opcional) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Nueva Contraseña <span class="text-gray-400">(dejar en blanco para mantener actual)</span>
                            </label>
                            <input type="password" 
                                   name="password" 
                                   class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="••••••••">
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
                                   placeholder="••••••••">
                            @error('password_confirmation')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Información del usuario -->
                    <div class="mt-8 p-4 bg-gray-50 rounded-lg">
                        <h3 class="text-sm font-medium text-gray-700 mb-2">Información Actual</h3>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-gray-500">ID:</span>
                                <span class="ml-2 font-medium">{{ $user->id }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Creado:</span>
                                <span class="ml-2 font-medium">{{ $user->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Última actualización:</span>
                                <span class="ml-2 font-medium">{{ $user->updated_at->format('d/m/Y H:i') }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Rol actual:</span>
                                <span class="ml-2 font-medium">{{ $user->role }}</span>
                            </div>
                        </div>
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
