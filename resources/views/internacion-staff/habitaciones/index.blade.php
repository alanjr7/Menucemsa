@extends('layouts.app')

@section('title', 'Gestión de Habitaciones - Internación')

@section('content')
<div class="min-h-screen bg-gray-50 p-6">
    <!-- Header -->
    <div class="mb-6">
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
                    Volver
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

    <!-- Estadísticas -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total</p>
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

    <!-- Split View: Lista + Detalle -->
    <div class="flex gap-6 h-[calc(100vh-280px)] min-h-[500px]">
        <!-- Panel Izquierdo: Lista de Habitaciones -->
        <div class="w-1/3 bg-white rounded-xl shadow-sm border border-gray-200 flex flex-col">
            <!-- Filtros -->
            <div class="p-4 border-b border-gray-200">
                <div class="flex gap-2 overflow-x-auto">
                    <button data-filtro="todas" class="tab-btn bg-indigo-600 text-white px-3 py-1.5 rounded-lg text-sm font-medium transition whitespace-nowrap">
                        Todas
                    </button>
                    <button data-filtro="disponible" class="tab-btn bg-gray-200 text-gray-700 px-3 py-1.5 rounded-lg text-sm font-medium transition whitespace-nowrap">
                        Disponibles
                    </button>
                    <button data-filtro="ocupada" class="tab-btn bg-gray-200 text-gray-700 px-3 py-1.5 rounded-lg text-sm font-medium transition whitespace-nowrap">
                        Ocupadas
                    </button>
                    <button data-filtro="mantenimiento" class="tab-btn bg-gray-200 text-gray-700 px-3 py-1.5 rounded-lg text-sm font-medium transition whitespace-nowrap">
                        Mantenimiento
                    </button>
                </div>
            </div>
            <!-- Lista -->
            <div id="habitaciones-lista" class="flex-1 overflow-y-auto">
                <div class="p-8 text-center text-gray-500">
                    <div class="animate-spin w-8 h-8 border-4 border-indigo-600 border-t-transparent rounded-full mx-auto mb-3"></div>
                    <p class="text-sm">Cargando habitaciones...</p>
                </div>
            </div>
        </div>

        <!-- Panel Derecho: Detalle de Habitación -->
        <div class="w-2/3 bg-white rounded-xl shadow-sm border border-gray-200 overflow-y-auto">
            <div id="habitacion-detalle" data-estado="">
                <div class="h-full min-h-[400px] flex items-center justify-center bg-slate-50">
                    <div class="text-center p-8">
                        <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-10 h-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-slate-700 mb-2">Selecciona una habitación</h3>
                        <p class="text-sm text-slate-500 max-w-xs">Haz clic en una habitación de la lista para ver sus detalles, camas disponibles y asignar pacientes.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Módulos JavaScript optimizados -->
<script src="{{ asset('js/habitaciones/cache.js') }}" defer></script>
<script src="{{ asset('js/habitaciones/api.js') }}" defer></script>
<script src="{{ asset('js/habitaciones/ui-notificaciones.js') }}" defer></script>
<script src="{{ asset('js/habitaciones/ui-modal.js') }}" defer></script>
<script src="{{ asset('js/habitaciones/ui-lista.js') }}" defer></script>
<script src="{{ asset('js/habitaciones/ui-detalle.js') }}" defer></script>
<script src="{{ asset('js/habitaciones/app.js') }}" defer></script>
@endsection
