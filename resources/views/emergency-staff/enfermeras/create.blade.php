@extends('layouts.app')

@section('content')
<div class="p-6 bg-gray-50/50 min-h-screen">
    <!-- Header -->
    <div class="mb-8">
        <a href="{{ route('emergency-staff.enfermeras.index') }}" class="text-gray-500 hover:text-gray-700 flex items-center gap-2 mb-4">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver al listado
        </a>
        <h1 class="text-2xl font-bold text-gray-800">Registrar Nueva Enfermera</h1>
        <p class="text-sm text-gray-500">Complete los datos de la enfermera de emergencia</p>
    </div>

    <!-- Alertas -->
    @if(session('error'))
    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg mb-6">
        <div class="flex items-center">
            <svg class="w-6 h-6 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-red-700">{{ session('error') }}</p>
        </div>
    </div>
    @endif

    @if($errors->any())
    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg mb-6">
        <div class="flex items-start">
            <svg class="w-6 h-6 text-red-500 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div>
                <p class="text-red-700 font-medium mb-2">Por favor corrige los siguientes errores:</p>
                <ul class="text-red-600 text-sm list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif

    <!-- Formulario -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 max-w-2xl">
        <form action="{{ route('emergency-staff.enfermeras.store') }}" method="POST">
            @csrf

            <div class="space-y-6">
                <!-- Nombre -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nombre Completo <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-red-500 focus:ring-2 focus:ring-red-200 transition outline-none"
                        placeholder="Ej: María Elena Gómez">
                </div>

                <!-- CI -->
                <div>
                    <label for="ci" class="block text-sm font-medium text-gray-700 mb-2">
                        Cédula de Identidad <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="ci" id="ci" value="{{ old('ci') }}" required
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-red-500 focus:ring-2 focus:ring-red-200 transition outline-none"
                        placeholder="Ej: 12345678">
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        Correo Electrónico <span class="text-red-500">*</span>
                    </label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-red-500 focus:ring-2 focus:ring-red-200 transition outline-none"
                        placeholder="Ej: enfermera@clinica.com">
                </div>

                <!-- Teléfono -->
                <div>
                    <label for="telefono" class="block text-sm font-medium text-gray-700 mb-2">
                        Teléfono
                    </label>
                    <input type="text" name="telefono" id="telefono" value="{{ old('telefono') }}"
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-red-500 focus:ring-2 focus:ring-red-200 transition outline-none"
                        placeholder="Ej: 0987654321">
                </div>

                <!-- Turno -->
                <div>
                    <label for="turno" class="block text-sm font-medium text-gray-700 mb-2">
                        Turno <span class="text-red-500">*</span>
                    </label>
                    <select name="turno" id="turno" required
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-red-500 focus:ring-2 focus:ring-red-200 transition outline-none bg-white">
                        <option value="mañana" {{ old('turno') == 'mañana' ? 'selected' : '' }}>Turno Mañana</option>
                        <option value="tarde" {{ old('turno') == 'tarde' ? 'selected' : '' }}>Turno Tarde</option>
                        <option value="noche" {{ old('turno') == 'noche' ? 'selected' : '' }}>Turno Noche</option>
                    </select>
                </div>

                <!-- Contraseña -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            Contraseña <span class="text-red-500">*</span>
                        </label>
                        <input type="password" name="password" id="password" required
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-red-500 focus:ring-2 focus:ring-red-200 transition outline-none"
                            placeholder="Mínimo 8 caracteres">
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                            Confirmar Contraseña <span class="text-red-500">*</span>
                        </label>
                        <input type="password" name="password_confirmation" id="password_confirmation" required
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-red-500 focus:ring-2 focus:ring-red-200 transition outline-none"
                            placeholder="Repite la contraseña">
                    </div>
                </div>
            </div>

            <!-- Botones -->
            <div class="flex gap-4 mt-8 pt-6 border-t border-gray-100">
                <a href="{{ route('emergency-staff.enfermeras.index') }}" 
                    class="flex-1 px-6 py-3 bg-gray-100 text-gray-700 font-medium rounded-xl hover:bg-gray-200 transition text-center">
                    Cancelar
                </a>
                <button type="submit" 
                    class="flex-1 px-6 py-3 bg-red-600 text-white font-medium rounded-xl hover:bg-red-700 transition shadow-sm">
                    Registrar Enfermera
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
