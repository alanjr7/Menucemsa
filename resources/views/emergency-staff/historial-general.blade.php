@extends('layouts.app')

@section('content')
<div class="p-6 bg-slate-50 min-h-screen">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Historial de Emergencias</h1>
            <p class="text-sm text-slate-500">Registro completo de todos los pacientes atendidos en emergencia</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('emergency-staff.dashboard') }}" class="flex items-center px-4 py-2 bg-white border border-slate-300 rounded-lg text-slate-700 hover:bg-slate-50 transition-all shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Volver al Dashboard
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-6 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-slate-500 uppercase">Total</p>
                    <p class="text-2xl font-bold text-slate-800">{{ $stats['total'] }}</p>
                </div>
                <div class="w-10 h-10 bg-slate-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-slate-500 uppercase">Hoy</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $stats['hoy'] }}</p>
                </div>
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-slate-500 uppercase">Activos</p>
                    <p class="text-2xl font-bold text-amber-600">{{ $stats['activos'] }}</p>
                </div>
                <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-slate-500 uppercase">De Alta</p>
                    <p class="text-2xl font-bold text-emerald-600">{{ $stats['alta'] }}</p>
                </div>
                <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-slate-500 uppercase">En Cirugía</p>
                    <p class="text-2xl font-bold text-purple-600">{{ $stats['cirugia'] }}</p>
                </div>
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-medium text-slate-500 uppercase">En UTI</p>
                    <p class="text-2xl font-bold text-rose-600">{{ $stats['uti'] }}</p>
                </div>
                <div class="w-10 h-10 bg-rose-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 mb-6">
        <form method="GET" action="{{ route('emergency-staff.historial.general') }}" class="flex flex-wrap gap-4 items-end">
            <div class="flex items-center gap-2">
                <label class="text-sm font-medium text-slate-700">Estado:</label>
                <select name="estado" class="px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="todos" {{ request('estado') == 'todos' ? 'selected' : '' }}>Todos</option>
                    <option value="recibido" {{ request('estado') == 'recibido' ? 'selected' : '' }}>Recibido</option>
                    <option value="en_evaluacion" {{ request('estado') == 'en_evaluacion' ? 'selected' : '' }}>En Evaluación</option>
                    <option value="estabilizado" {{ request('estado') == 'estabilizado' ? 'selected' : '' }}>Estabilizado</option>
                    <option value="cirugia" {{ request('estado') == 'cirugia' ? 'selected' : '' }}>En Cirugía</option>
                    <option value="uti" {{ request('estado') == 'uti' ? 'selected' : '' }}>En UTI</option>
                    <option value="alta" {{ request('estado') == 'alta' ? 'selected' : '' }}>De Alta</option>
                </select>
            </div>
            <div class="flex items-center gap-2">
                <label class="text-sm font-medium text-slate-700">Desde:</label>
                <input type="date" name="fecha_desde" value="{{ request('fecha_desde') }}" class="px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="flex items-center gap-2">
                <label class="text-sm font-medium text-slate-700">Hasta:</label>
                <input type="date" name="fecha_hasta" value="{{ request('fecha_hasta') }}" class="px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">
                Aplicar Filtros
            </button>
            <a href="{{ route('emergency-staff.historial.general') }}" class="px-4 py-2 border border-slate-300 text-slate-600 rounded-lg text-sm font-medium hover:bg-slate-50 transition-colors">
                Limpiar
            </a>
            <a href="{{ route('emergency-staff.historial.export', request()->all()) }}" class="flex items-center px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-medium hover:bg-emerald-700 transition-colors ml-auto">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Exportar Excel
            </a>
        </form>
    </div>

    <!-- Tabla -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-slate-500 uppercase bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th scope="col" class="px-6 py-4 font-semibold">Código</th>
                        <th scope="col" class="px-6 py-4 font-semibold">Paciente</th>
                        <th scope="col" class="px-6 py-4 font-semibold">Fecha Ingreso</th>
                        <th scope="col" class="px-6 py-4 font-semibold">Tipo Ingreso</th>
                        <th scope="col" class="px-6 py-4 font-semibold">Estado</th>
                        <th scope="col" class="px-6 py-4 font-semibold">Destino</th>
                        <th scope="col" class="px-6 py-4 font-semibold">Costo</th>
                        <th scope="col" class="px-6 py-4 font-semibold text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($emergencias as $emg)
                    <tr class="bg-white border-b border-slate-100 hover:bg-slate-50 transition-colors">
                        <td class="px-6 py-4">
                            <span class="font-mono font-medium text-slate-800">{{ $emg->code }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-medium text-slate-800">
                                {{ $emg->is_temp_id ? 'Paciente Temporal' : ($emg->paciente?->nombre ?? 'Desconocido') }}
                            </div>
                            <div class="text-xs text-slate-500 mt-1">
                                {{ $emg->is_temp_id ? 'ID: ' . $emg->temp_id : 'CI: ' . $emg->patient_id }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-slate-800">
                                {{ $emg->admission_date?->format('d/m/Y') ?? $emg->created_at->format('d/m/Y') }}
                            </div>
                            <div class="text-xs text-slate-500 mt-1">
                                {{ $emg->admission_date?->format('H:i') ?? $emg->created_at->format('H:i') }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs font-medium rounded-full
                                @switch($emg->tipo_ingreso)
                                    @case('soat') bg-orange-100 text-orange-700 @break
                                    @case('parto') bg-pink-100 text-pink-700 @break
                                    @case('general') bg-blue-100 text-blue-700 @break
                                    @default bg-slate-100 text-slate-700 @break
                                @endswitch
                            ">
                                {{ $emg->tipo_ingreso_label }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs font-medium rounded-full
                                @switch($emg->status)
                                    @case('recibido') bg-yellow-100 text-yellow-700 @break
                                    @case('en_evaluacion') bg-blue-100 text-blue-700 @break
                                    @case('estabilizado') bg-emerald-100 text-emerald-700 @break
                                    @case('cirugia') bg-purple-100 text-purple-700 @break
                                    @case('uti') bg-rose-100 text-rose-700 @break
                                    @case('alta') bg-slate-100 text-slate-700 @break
                                    @case('fallecido') bg-gray-800 text-white @break
                                    @default bg-slate-100 text-slate-700 @break
                                @endswitch
                            ">
                                {{ ucfirst(str_replace('_', ' ', $emg->status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-slate-600 capitalize">{{ $emg->destino_inicial ?? 'Pendiente' }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="font-medium text-slate-800">Bs. {{ number_format($emg->cost ?? 0, 2) }}</span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('emergency-staff.historial', $emg) }}" class="inline-flex items-center px-3 py-1.5 bg-blue-100 text-blue-700 rounded-lg text-sm font-medium hover:bg-blue-200 transition-colors">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                Ver Historial
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center">
                            <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <p class="text-slate-500 mb-2">No se encontraron registros de emergencias</p>
                            <p class="text-slate-400 text-sm">Intenta ajustar los filtros o agregar nuevas emergencias</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        @if($emergencias->hasPages())
        <div class="px-6 py-4 border-t border-slate-200">
            {{ $emergencias->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
