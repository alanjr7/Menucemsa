@extends('layouts.app')

@section('title', 'Gestión de Habitaciones - Internación')

@section('content')
<div class="min-h-screen bg-gray-50 p-6">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Gestión de Habitaciones</h1>
                <p class="text-gray-600 mt-1">Administrar habitaciones y asignación de pacientes</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('internacion-staff.dashboard') }}" 
                   class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Volver al Dashboard
                </a>
                <a href="{{ route('internacion-staff.habitaciones.create') }}" 
                   class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Nueva Habitación
                </a>
            </div>
        </div>
    </div>

    <!-- Mensajes -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
            {{ session('error') }}
        </div>
    @endif

    <!-- Estadísticas -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Habitaciones</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_habitaciones'] }}</p>
                </div>
                <div class="p-3 bg-blue-100 rounded-full">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Disponibles</p>
                    <p class="text-2xl font-bold text-green-600">{{ $stats['habitaciones_disponibles'] }}</p>
                </div>
                <div class="p-3 bg-green-100 rounded-full">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Ocupadas</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ $stats['habitaciones_ocupadas'] }}</p>
                </div>
                <div class="p-3 bg-yellow-100 rounded-full">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Camas Ocupadas</p>
                    <p class="text-2xl font-bold text-indigo-600">{{ $stats['camas_ocupadas'] }}/{{ $stats['total_camas'] }}</p>
                </div>
                <div class="p-3 bg-indigo-100 rounded-full">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Grid de Habitaciones -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @foreach($habitaciones as $habitacion)
            <div class="bg-white rounded-xl shadow-sm border-2 {{ $habitacion->estado === 'disponible' ? 'border-green-200' : ($habitacion->estado === 'ocupada' ? 'border-yellow-200' : 'border-red-200') }} overflow-hidden hover:shadow-md transition">
                <!-- Header de Habitación -->
                <div class="p-4 {{ $habitacion->estado === 'disponible' ? 'bg-green-50' : ($habitacion->estado === 'ocupada' ? 'bg-yellow-50' : 'bg-red-50') }}">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-lg font-bold text-gray-900">Habitación {{ $habitacion->id }}</h3>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $habitacion->estado === 'disponible' ? 'bg-green-100 text-green-800' : ($habitacion->estado === 'ocupada' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                            {{ ucfirst($habitacion->estado) }}
                        </span>
                    </div>
                    <p class="text-sm text-gray-600">{{ $habitacion->detalle ?? 'Sin detalle' }}</p>
                    <p class="text-xs text-gray-500 mt-1">Capacidad: {{ $habitacion->capacidad }} camas</p>
                </div>

                <!-- Camas -->
                <div class="p-4">
                    <div class="grid grid-cols-2 gap-2 mb-4">
                        @foreach($habitacion->camas as $cama)
                            <div class="flex items-center p-2 rounded-lg {{ $cama->disponibilidad === 'disponible' ? 'bg-green-100' : ($cama->disponibilidad === 'ocupada' ? 'bg-red-100' : 'bg-gray-100') }}">
                                <svg class="w-4 h-4 mr-2 {{ $cama->disponibilidad === 'disponible' ? 'text-green-600' : ($cama->disponibilidad === 'ocupada' ? 'text-red-600' : 'text-gray-600') }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01"/>
                                </svg>
                                <span class="text-xs font-medium {{ $cama->disponibilidad === 'disponible' ? 'text-green-800' : ($cama->disponibilidad === 'ocupada' ? 'text-red-800' : 'text-gray-800') }}">
                                    Cama {{ $cama->nro }}
                                </span>
                            </div>
                        @endforeach
                    </div>

                    <!-- Ocupación -->
                    <div class="flex items-center justify-between text-sm mb-4">
                        <span class="text-gray-600">Ocupación:</span>
                        <span class="font-semibold {{ $habitacion->camas_ocupadas > 0 ? 'text-yellow-600' : 'text-green-600' }}">
                            {{ $habitacion->camas_ocupadas }}/{{ $habitacion->camas->count() }} camas
                        </span>
                    </div>

                    <!-- Botones -->
                    <div class="flex gap-2">
                        <a href="{{ route('internacion-staff.habitaciones.show', $habitacion) }}" 
                           class="flex-1 text-center px-3 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition">
                            Ver Detalle
                        </a>
                        <a href="{{ route('internacion-staff.habitaciones.edit', $habitacion) }}" 
                           class="flex-1 text-center px-3 py-2 bg-gray-200 text-gray-700 text-sm rounded-lg hover:bg-gray-300 transition">
                            Editar
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    @if($habitaciones->isEmpty())
        <div class="text-center py-12">
            <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No hay habitaciones registradas</h3>
            <p class="text-gray-500 mb-4">Comienza creando una nueva habitación para internación.</p>
            <a href="{{ route('internacion-staff.habitaciones.create') }}" 
               class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Crear Habitación
            </a>
        </div>
    @endif
</div>
@endsection
