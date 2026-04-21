@extends('layouts.app')

@section('content')
<div class="p-6 bg-slate-50 min-h-screen">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Historial de Internaciones</h1>
            <p class="text-sm text-slate-500">Registro completo de todos los pacientes dados de alta</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('internacion-staff.dashboard') }}" class="flex items-center px-4 py-2 bg-white border border-slate-300 rounded-lg text-slate-700 hover:bg-slate-50 transition-all shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Volver al Dashboard
            </a>
            <a href="{{ route('internacion-staff.export-historial', request()->all()) }}" class="flex items-center px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-all shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Exportar Excel
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-slate-500 uppercase">Total Internaciones</p>
                    <p class="text-2xl font-bold text-slate-800">{{ $stats['total'] }}</p>
                </div>
                <div class="w-10 h-10 bg-slate-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-slate-500 uppercase">Activos</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $stats['activos'] }}</p>
                </div>
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-slate-500 uppercase">Hoy Ingresados</p>
                    <p class="text-2xl font-bold text-amber-600">{{ $stats['hoy'] }}</p>
                </div>
                <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-slate-500 uppercase">Este Mes</p>
                    <p class="text-2xl font-bold text-emerald-600">{{ $stats['mes'] }}</p>
                </div>
                <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 mb-6">
        <form method="GET" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-medium text-slate-500 mb-1">Buscar paciente</label>
                <input type="text" name="busqueda" value="{{ request('busqueda') }}" placeholder="Nombre o CI..." class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">Desde</label>
                <input type="date" name="fecha_desde" value="{{ request('fecha_desde') }}" class="border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-500 mb-1">Hasta</label>
                <input type="date" name="fecha_hasta" value="{{ request('fecha_hasta') }}" class="border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-blue-500">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
                    Filtrar
                </button>
                <a href="{{ route('internacion-staff.historial-general') }}" class="px-4 py-2 bg-slate-200 text-slate-700 rounded-lg hover:bg-slate-300 transition-colors text-sm font-medium">
                    Limpiar
                </a>
            </div>
        </form>
    </div>

    <!-- Tabla de Internaciones -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-slate-500 uppercase bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-3">ID</th>
                        <th class="px-6 py-3">Paciente</th>
                        <th class="px-6 py-3">CI</th>
                        <th class="px-6 py-3">Habitación</th>
                        <th class="px-6 py-3">Ingreso</th>
                        <th class="px-6 py-3">Alta</th>
                        <th class="px-6 py-3">Días</th>
                        <th class="px-6 py-3">Médico</th>
                        <th class="px-6 py-3">Total</th>
                        <th class="px-6 py-3">Estado</th>
                        <th class="px-6 py-3">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($hospitalizaciones as $hosp)
                    @php
                        $dias = $hosp->fecha_ingreso && $hosp->fecha_alta
                            ? ceil($hosp->fecha_ingreso->diffInDays($hosp->fecha_alta))
                            : 0;
                    @endphp
                    <tr class="border-b border-slate-100 hover:bg-slate-50">
                        <td class="px-6 py-4 font-mono text-xs text-slate-500">{{ $hosp->id }}</td>
                        <td class="px-6 py-4">
                            <p class="font-medium text-slate-800">{{ $hosp->paciente?->nombre ?? 'N/A' }}</p>
                            @if($hosp->diagnostico)
                            <p class="text-xs text-slate-500 truncate max-w-[200px]">{{ $hosp->diagnostico }}</p>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-slate-600">{{ $hosp->ci_paciente }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-block px-2 py-1 bg-violet-100 text-violet-700 rounded text-xs font-medium">
                                {{ $hosp->habitacion_id ?? 'N/A' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-slate-600">{{ $hosp->fecha_ingreso?->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 text-slate-600">{{ $hosp->fecha_alta?->format('d/m/Y') }}</td>
                        <td class="px-6 py-4">
                            <span class="font-medium text-slate-700">{{ $dias }} {{ $dias == 1 ? 'día' : 'días' }}</span>
                        </td>
                        <td class="px-6 py-4 text-slate-600">{{ $hosp->medico?->user?->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 font-medium text-slate-800">
                            Bs. {{ number_format($hosp->total_estancia ?? 0, 2) }}
                        </td>
                        <td class="px-6 py-4">
                            @if($hosp->fecha_alta)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Alta
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    Activo
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('internacion-staff.historial', $hosp->id) }}" class="inline-flex items-center px-3 py-1.5 bg-indigo-100 text-indigo-700 rounded-lg hover:bg-indigo-200 transition-colors text-xs font-medium">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                Ver Historial
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="px-6 py-12 text-center text-slate-500">
                            <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <p>No hay registros de internaciones</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($hospitalizaciones->hasPages())
        <div class="px-6 py-4 border-t border-slate-200">
            {{ $hospitalizaciones->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
