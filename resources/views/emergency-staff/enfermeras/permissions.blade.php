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
        <h1 class="text-2xl font-bold text-gray-800">Gestionar Permisos</h1>
        <p class="text-sm text-gray-500">Configurar permisos para: <span class="font-semibold text-gray-700">{{ $enfermera->user?->name ?? 'Enfermera' }}</span></p>
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

    <!-- Información de la Enfermera -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center">
                <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <div>
                <h2 class="text-lg font-semibold text-gray-800">{{ $enfermera->user?->name ?? 'Enfermera' }}</h2>
                <div class="flex gap-3 text-sm text-gray-500 mt-1">
                    <span class="flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0H9m4 0h1"/>
                        </svg>
                        CI: {{ $enfermera->ci }}
                    </span>
                    <span class="flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        {{ $enfermera->turno_label }}
                    </span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $enfermera->estado === 'activo' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $enfermera->estado === 'activo' ? 'Activa' : 'Inactiva' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulario de Permisos -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
        <form action="{{ route('emergency-staff.enfermeras.permissions.update', $enfermera) }}" method="POST">
            @csrf

            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Permisos Disponibles</h3>
                <p class="text-sm text-gray-500">Selecciona los permisos que esta enfermera tendrá en el sistema. Los permisos determinan qué acciones puede realizar en el dashboard de emergencia.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($availablePermissions as $key => $permission)
                <div class="relative flex items-start p-4 border border-gray-200 rounded-xl hover:bg-gray-50 transition cursor-pointer"
                     onclick="document.getElementById('perm_{{ $key }}').click()">
                    <div class="flex items-center h-5">
                        <input id="perm_{{ $key }}" name="permissions[]" type="checkbox" value="{{ $key }}"
                            {{ in_array($key, $currentPermissions) ? 'checked' : '' }}
                            class="h-4 w-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                    </div>
                    <div class="ml-3 flex-1">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $permission['icon'] }}"/>
                            </svg>
                            <label for="perm_{{ $key }}" class="font-medium text-gray-900 cursor-pointer">
                                {{ $permission['label'] }}
                            </label>
                            @if($permission['default'])
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                    Por defecto
                                </span>
                            @endif
                        </div>
                        <p class="text-sm text-gray-500 mt-1">{{ $permission['description'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Leyenda -->
            <div class="mt-6 p-4 bg-blue-50 rounded-xl">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div class="text-sm text-blue-800">
                        <p class="font-medium mb-1">Información sobre permisos:</p>
                        <ul class="list-disc list-inside space-y-1 text-blue-700">
                            <li><strong>Por defecto:</strong> Se asignan automáticamente al crear una enfermera</li>
                            <li><strong>Permisos avanzados:</strong> Aplicar medicamentos, derivar pacientes y dar de alta requieren autorización explícita</li>
                            <li>Los cambios se aplican inmediatamente en el dashboard de la enfermera</li>
                        </ul>
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
                    class="flex-1 px-6 py-3 bg-purple-600 text-white font-medium rounded-xl hover:bg-purple-700 transition shadow-sm">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Guardar Permisos
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
