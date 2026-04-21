@extends('layouts.app')

@section('content')
<div class="p-6 bg-gray-50/50 min-h-screen">
    <!-- Header -->
    <div class="flex justify-between items-end mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Gestionar Permisos</h1>
            <p class="text-sm text-gray-500">Permisos de {{ $enfermera->user?->name ?? 'Enfermera' }}</p>
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
        <div class="flex items-center">
            <svg class="w-6 h-6 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <p class="text-green-700">{{ session('success') }}</p>
        </div>
    </div>
    @endif

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

    <form action="{{ route('internacion-staff.enfermeras.permissions.update', $enfermera) }}" method="POST">
        @csrf

        @php
            // Filtrar permisos según el área de la enfermera que se está editando
            $enfermeraArea = $enfermera->area ?? null;
            $emergenciaPermissions = ['ver_pacientes', 'registrar_signos_vitales', 'cambiar_estados', 'aplicar_medicamentos', 'ver_historial', 'derivar_pacientes', 'dar_alta'];
            $internacionPermissions = ['ver_pacientes_internacion', 'administrar_medicamentos', 'administrar_catering', 'administrar_drenajes', 'cambiar_estados_internacion', 'derivar_a_uti', 'dar_alta_internacion', 'ver_historial_internacion', 'editar_diagnostico'];

            $filteredPermissions = collect($availablePermissions);
            if ($enfermeraArea === 'emergencia') {
                $filteredPermissions = $filteredPermissions->only($emergenciaPermissions);
            } elseif ($enfermeraArea === 'internacion') {
                $filteredPermissions = $filteredPermissions->only($internacionPermissions);
            }
            // Otras áreas ven todos los permisos (sin filtrar)
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($filteredPermissions as $key => $permission)
            @php
                $isInternacion = str_contains($key, 'internacion') || 
                                 in_array($key, ['administrar_medicamentos', 'administrar_catering', 'administrar_drenajes', 'derivar_a_uti', 'editar_diagnostico']);
            @endphp
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 {{ $isInternacion ? 'border-l-4 border-l-indigo-500' : 'opacity-75' }}">
                <div class="flex items-start gap-4">
                    <div class="flex items-center h-5">
                        <input type="checkbox" name="permissions[]" value="{{ $key }}"
                            id="perm_{{ $key }}"
                            {{ in_array($key, $currentPermissions) ? 'checked' : '' }}
                            class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                    </div>
                    <div class="flex-1">
                        <label for="perm_{{ $key }}" class="font-medium text-gray-900 cursor-pointer flex items-center gap-2">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $permission['icon'] }}"/>
                            </svg>
                            {{ $permission['label'] }}
                            @if($isInternacion)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-100 text-indigo-800">
                                    Internación
                                </span>
                            @endif
                        </label>
                        <p class="text-sm text-gray-500 mt-1">{{ $permission['description'] }}</p>
                        @if($permission['default'])
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 mt-2">
                                Por defecto
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-8 flex justify-end gap-3">
            <a href="{{ route('internacion-staff.enfermeras.index') }}" class="px-6 py-2.5 border border-gray-300 text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition-all">
                Cancelar
            </a>
            <button type="submit" class="px-6 py-2.5 bg-indigo-600 text-white font-medium rounded-xl hover:bg-indigo-700 transition-all shadow-sm">
                Guardar Permisos
            </button>
        </div>
    </form>
</div>
@endsection
