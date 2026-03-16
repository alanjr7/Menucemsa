@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-900">Crear Nuevo Usuario</h1>
                <p class="text-gray-600 mt-1">Completa el formulario para crear un nuevo usuario</p>
            </div>

            <form action="{{ route('user-management.store') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">
                            Nombre Completo
                        </label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               value="{{ old('name') }}"
                               required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('name') border-red-500 @enderror">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">
                            Correo Electrónico
                        </label>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               value="{{ old('email') }}"
                               required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('email') border-red-500 @enderror">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">
                            Contraseña
                        </label>
                        <input type="password" 
                               id="password" 
                               name="password" 
                               required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('password') border-red-500 @enderror">
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
                            Confirmar Contraseña
                        </label>
                        <input type="password" 
                               id="password_confirmation" 
                               name="password_confirmation" 
                               required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('password_confirmation') border-red-500 @enderror">
                        @error('password_confirmation')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="role" class="block text-sm font-medium text-gray-700">
                            Rol del Usuario
                        </label>
                        <select id="role" 
                                name="role" 
                                required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('role') border-red-500 @enderror">
                            <option value="">Selecciona un rol</option>
                            @foreach($roles as $role)
                                <option value="{{ $role }}" {{ old('role') == $role ? 'selected' : '' }}>
                                    {{ ucfirst($role) }}
                                    @if($role == 'admin') - Acceso total al sistema
                                    @elseif($role == 'dirmedico') - Todas las áreas médicas
                                    @elseif($role == 'emergencia') - Solo módulo de emergencias
                                    @elseif($role == 'caja') - Todas las áreas financieras
                                    @elseif($role == 'gerente') - Dashboard y reportes gerenciales
                                    @elseif($role == 'reception') - Recepción y admisión
                                    @elseif($role == 'doctor') - Médico con especialidad asignada
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        @error('role')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Campos adicionales para doctores y directores médicos -->
                    <div id="doctor-fields" class="md:col-span-2">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Información Médica (solo para roles Doctor y Director Médico)</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4 bg-blue-50 rounded-lg">
                            <div>
                                <label for="ci" class="block text-sm font-medium text-gray-700">
                                    Cédula de Identidad *
                                </label>
                                <input type="text" 
                                       id="ci" 
                                       name="ci" 
                                       value="{{ old('ci') }}"
                                       placeholder="Ej: 12345678"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('ci')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="telefono" class="block text-sm font-medium text-gray-700">
                                    Teléfono
                                </label>
                                <input type="text" 
                                       id="telefono" 
                                       name="telefono" 
                                       value="{{ old('telefono') }}"
                                       placeholder="Ej: 098765432"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('telefono')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label for="codigo_especialidad" class="block text-sm font-medium text-gray-700">
                                    Especialidad *
                                </label>
                                <select id="codigo_especialidad" 
                                        name="codigo_especialidad" 
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Seleccione una especialidad</option>
                                    @foreach(\App\Models\Especialidad::where('estado', 'activo')->orderBy('nombre')->get() as $especialidad)
                                        <option value="{{ $especialidad->codigo }}" {{ old('codigo_especialidad') == $especialidad->codigo ? 'selected' : '' }}>
                                            {{ $especialidad->nombre }}
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

                <div class="mt-6 flex items-center justify-end gap-3">
                    <a href="{{ route('user-management.index') }}" 
                       class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Cancelar
                    </a>
                    <button type="submit" 
                            class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Crear Usuario
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const roleSelect = document.getElementById('role');
    const doctorFields = document.getElementById('doctor-fields');
    
    function toggleDoctorFields() {
        console.log('Rol seleccionado:', roleSelect.value); // Debug
        
        if (roleSelect.value === 'doctor' || roleSelect.value === 'dirmedico') {
            doctorFields.classList.remove('hidden');
            // Hacer obligatorios los campos de doctor
            const ciField = document.getElementById('ci');
            const especialidadField = document.getElementById('codigo_especialidad');
            
            if (ciField) ciField.required = true;
            if (especialidadField) especialidadField.required = true;
            
            console.log('Campos de doctor habilitados'); // Debug
        } else {
            doctorFields.classList.add('hidden');
            // Quitar obligatoriedad
            const ciField = document.getElementById('ci');
            const especialidadField = document.getElementById('codigo_especialidad');
            
            if (ciField) ciField.required = false;
            if (especialidadField) especialidadField.required = false;
            
            console.log('Campos de doctor deshabilitados'); // Debug
        }
    }
    
    roleSelect.addEventListener('change', toggleDoctorFields);
    
    // Verificar estado inicial
    toggleDoctorFields();
    
    // Debug: mostrar el valor inicial del rol
    console.log('Valor inicial del rol:', roleSelect.value);
});
</script>
@endsection
