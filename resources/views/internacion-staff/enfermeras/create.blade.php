@extends('layouts.app')

@section('content')
<div class="p-6 bg-gray-50/50 min-h-screen">
    <!-- Header -->
    <div class="flex justify-between items-end mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Nueva Enfermera</h1>
            <p class="text-sm text-gray-500">Crear enfermera de internación</p>
        </div>
        <a href="{{ route('internacion-staff.enfermeras.index') }}" class="flex items-center px-4 py-2 bg-white border border-gray-200 text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition-all shadow-sm">
            <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver
        </a>
    </div>

    @if(session('success'))
    <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-lg mb-6">
        <div class="flex items-start">
            <svg class="w-6 h-6 text-green-500 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div>
                <h3 class="text-green-800 font-medium">{{ session('success') }}</h3>
            </div>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg mb-6">
        <div class="flex items-start">
            <svg class="w-6 h-6 text-red-500 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div>
                <h3 class="text-red-800 font-medium">{{ session('error') }}</h3>
            </div>
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
                <h3 class="text-red-800 font-medium">Error al crear la enfermera</h3>
                <ul class="mt-1 text-sm text-red-700 list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif

    <form action="{{ route('internacion-staff.enfermeras.store') }}" method="POST" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Nombre -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nombre Completo *</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="Ej: María García López">
            </div>

            <!-- CI -->
            <div>
                <label for="ci" class="block text-sm font-medium text-gray-700 mb-2">Cédula de Identidad (CI) *</label>
                <input type="text" name="ci" id="ci" value="{{ old('ci') }}" required
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="Ej: 1234567">
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Correo Electrónico *</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="Ej: enfermera@clinica.com">
            </div>

            <!-- Teléfono -->
            <div>
                <label for="telefono" class="block text-sm font-medium text-gray-700 mb-2">Teléfono</label>
                <input type="text" name="telefono" id="telefono" value="{{ old('telefono') }}"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="Ej: 77712345">
            </div>

            <!-- Turno -->
            <div>
                <label for="turno" class="block text-sm font-medium text-gray-700 mb-2">Turno *</label>
                <select name="turno" id="turno" required
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="mañana" {{ old('turno') == 'mañana' ? 'selected' : '' }}>Turno Mañana</option>
                    <option value="tarde" {{ old('turno') == 'tarde' ? 'selected' : '' }}>Turno Tarde</option>
                    <option value="noche" {{ old('turno') == 'noche' ? 'selected' : '' }}>Turno Noche</option>
                </select>
            </div>

            <!-- Contraseña -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Contraseña *</label>
                <input type="password" name="password" id="password" required
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="Mínimo 8 caracteres">
            </div>

            <!-- Confirmar Contraseña -->
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirmar Contraseña *</label>
                <input type="password" name="password_confirmation" id="password_confirmation" required
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="Repita la contraseña">
            </div>
        </div>

        <div class="mt-8 flex justify-end gap-3">
            <a href="{{ route('internacion-staff.enfermeras.index') }}" class="px-6 py-2.5 border border-gray-300 text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition-all">
                Cancelar
            </a>
            <button type="submit" class="px-6 py-2.5 bg-indigo-600 text-white font-medium rounded-xl hover:bg-indigo-700 transition-all shadow-sm">
                Crear Enfermera
            </button>
        </div>
    </form>
</div>
@endsection
