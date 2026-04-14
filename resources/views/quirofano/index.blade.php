@extends('layouts.app')

@section('content')
<div class="w-full p-4 bg-gray-50/50 min-h-screen">

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-800">Horario Semanal de Quirófanos</h1>
            <p class="text-sm text-gray-500">Programación quirúrgica de la semana</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('quirofano.historial') }}" class="flex items-center px-3 py-2 border border-purple-600 text-purple-600 rounded-lg hover:bg-purple-50 font-medium transition-colors text-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <span class="hidden sm:inline">Historial</span>
                <span class="sm:hidden">Hist.</span>
            </a>
            <a href="{{ route('quirofano.create') }}" class="flex items-center px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium transition-colors text-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span class="hidden sm:inline">Nueva Cita</span>
                <span class="sm:hidden">+</span>
            </a>
            @if(Auth::user()->isAdmin())
            <a href="{{ route('quirofanos.management.index') }}" class="flex items-center px-4 py-2 border border-purple-600 text-purple-600 rounded-lg hover:bg-purple-50 font-medium transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                Gestionar Quirófanos
            </a>
            <a href="{{ route('quirofano.medicamentos.index') }}" class="flex items-center px-4 py-2 border border-green-600 text-green-600 rounded-lg hover:bg-green-50 font-medium transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                </svg>
                Gestionar Medicamentos
            </a>
            @endif
            <button onclick="window.print()" class="flex items-center px-4 py-2 border border-gray-200 rounded-lg text-gray-600 bg-white hover:bg-gray-50 font-medium transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Imprimir Horario
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-3 lg:gap-6 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 lg:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs lg:text-sm font-medium text-gray-500">Total Semana</p>
                    <p class="text-lg lg:text-2xl font-bold text-gray-900">{{ $stats['total_semana'] }}</p>
                </div>
                <div class="w-8 h-8 lg:w-12 lg:h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 lg:w-6 lg:h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 lg:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs lg:text-sm font-medium text-gray-500">Citas Hoy</p>
                    <p class="text-lg lg:text-2xl font-bold text-amber-600">{{ $stats['hoy'] }}</p>
                </div>
                <div class="w-8 h-8 lg:w-12 lg:h-12 bg-amber-100 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 lg:w-6 lg:h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 lg:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs lg:text-sm font-medium text-gray-500">En Curso</p>
                    <p class="text-lg lg:text-2xl font-bold text-red-600">{{ $stats['en_curso'] }}</p>
                </div>
                <div class="w-8 h-8 lg:w-12 lg:h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 lg:w-6 lg:h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 lg:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs lg:text-sm font-medium text-gray-500">Finalizadas Hoy</p>
                    <p class="text-lg lg:text-2xl font-bold text-green-600">{{ $stats['finalizadas'] }}</p>
                </div>
                <div class="w-8 h-8 lg:w-12 lg:h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 lg:w-6 lg:h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-6m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Emergencias en Quirófano -->
        <div class="bg-white rounded-xl shadow-sm border border-purple-200 p-4 lg:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs lg:text-sm font-medium text-purple-600">Emergencias</p>
                    <p class="text-lg lg:text-2xl font-bold text-purple-700">{{ $stats['emergencias'] }}</p>
                </div>
                <div class="w-8 h-8 lg:w-12 lg:h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 lg:w-6 lg:h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Emergencias en Quirófano -->
    @if($emergenciasEnQuirofano->count() > 0)
    <div class="bg-white rounded-xl shadow-sm border border-purple-200 mb-6 overflow-hidden">
        <div class="p-4 bg-purple-50 border-b border-purple-200 flex items-center justify-between">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-purple-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                <h3 class="font-bold text-purple-800">Pacientes de Emergencia en Quirófano</h3>
            </div>
            <span class="px-3 py-1 bg-purple-100 text-purple-700 text-sm font-semibold rounded-full">
                {{ $emergenciasEnQuirofano->count() }} paciente(s)
            </span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Código</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Paciente</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">N° Cirugía</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hora Ingreso</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($emergenciasEnQuirofano as $emg)
                    <tr class="hover:bg-purple-50/50">
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="font-mono text-sm font-medium text-purple-600">{{ $emg['code'] }}</span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="h-8 w-8 rounded-full bg-purple-100 flex items-center justify-center mr-3">
                                    <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                                <span class="text-sm font-medium text-gray-900">{{ $emg['paciente_nombre'] }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="font-mono text-sm text-gray-600">{{ $emg['nro_cirugia'] ?? 'N/A' }}</span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">
                                {{ $emg['status_label'] }}
                            </span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                            {{ $emg['hora_ingreso'] }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="text-sm text-gray-600">{{ $emg['tipo_ingreso'] }}</span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-center">
                            <div class="flex flex-col gap-2">
                                <a href="{{ route('emergency-staff.show', $emg['id']) }}" class="text-purple-600 hover:text-purple-900 text-sm font-medium">
                                    Ver detalle
                                </a>
                                <a href="{{ route('quirofano.programar-emergencia', $emg['id']) }}" class="inline-flex items-center justify-center px-3 py-1 bg-blue-600 text-white text-xs font-medium rounded hover:bg-blue-700 transition-colors">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    Programar
                                </a>
                                <button onclick="iniciarEmergencia({{ $emg['id'] }})" class="inline-flex items-center justify-center px-3 py-1 bg-purple-600 text-white text-xs font-medium rounded hover:bg-purple-700 transition-colors">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                    </svg>
                                    Iniciar Ahora
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Vista Móvil: Tarjetas por Día -->
    <div class="lg:hidden space-y-4 mb-8">
        @foreach($diasSemana as $dia)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-4 bg-gray-50 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <h3 class="font-bold text-gray-800">{{ $dia['nombre'] }}</h3>
                        <div class="text-sm text-gray-500">
                            {{ $dia['dia_mes'] }}
                            @if($dia['fecha']->isToday())
                                <span class="ml-2 px-2 py-1 bg-blue-100 text-blue-600 text-xs font-semibold rounded-full">Hoy</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="p-4">
                    @php
                        $citasDelDia = [];
                        foreach($horasDia as $hora) {
                            if(isset($citasPorDiaHora[$dia['fecha_key']][$hora])) {
                                foreach($quirofanos as $quirofano) {
                                    if(isset($citasPorDiaHora[$dia['fecha_key']][$hora][$quirofano->nro])) {
                                        $citasDelDia = array_merge($citasDelDia, $citasPorDiaHora[$dia['fecha_key']][$hora][$quirofano->nro]);
                                    }
                                }
                            }
                        }
                    @endphp
                    
                    @if(!empty($citasDelDia))
                        <div class="space-y-3">
                            @foreach($citasDelDia as $cita)
                                <div class="border border-gray-200 rounded-lg p-3 {{ $cita->estado === 'programada' ? 'bg-blue-50' : ($cita->estado === 'en_curso' ? 'bg-amber-50' : ($cita->estado === 'finalizada' ? 'bg-green-50' : 'bg-red-50')) }}">
                                    <div class="flex justify-between items-start mb-2">
                                        <div>
                                            <div class="font-semibold text-gray-900 text-sm">{{ $cita->paciente->nombre }}</div>
                                            <div class="text-xs text-gray-600">{{ optional($cita->cirujano->user)->name ?? 'N/A' }}</div>
                                        </div>
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $cita->estado === 'programada' ? 'bg-blue-100 text-blue-800' : ($cita->estado === 'en_curso' ? 'bg-amber-100 text-amber-800' : ($cita->estado === 'finalizada' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800')) }}">
                                            {{ ucfirst($cita->estado) }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between items-center text-xs text-gray-500">
                                        <div>Q{{ $cita->quirofano->id }} • {{ $cita->hora_inicio_estimada->format('H:i') }}</div>
                                        <button onclick="verDetalles({{ $cita->id }})" class="text-blue-600 hover:text-blue-800 font-medium">
                                            Ver →
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-400">
                            <svg class="w-12 h-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <p class="text-sm">No hay cirugías programadas</p>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <!-- Vista Desktop: Tabla Horario -->
    <div class="hidden lg:block bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <div class="flex justify-between items-center">
                <h2 class="text-lg font-bold text-gray-800">Horario Semanal Detallado</h2>
                <div class="text-sm text-gray-500">
                    {{ $diasSemana[0]['fecha']->format('d/m/Y') }} - {{ $diasSemana[6]['fecha']->format('d/m/Y') }}
                </div>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <!-- Tabla de Horario por Horas -->
            <table class="w-full text-xs">
                <thead class="bg-gray-50 border-b border-gray-200 sticky top-0 z-10">
                    <tr>
                        <th class="px-3 py-2 text-left font-medium text-gray-500 uppercase tracking-wider w-24 border-r border-gray-200">
                            Día
                        </th>
                        @foreach($horasDia as $hora)
                            <th class="px-2 py-2 text-center font-medium text-gray-500 uppercase tracking-wider min-w-[80px] border-r border-gray-200">
                                {{ $hora }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($diasSemana as $dia)
                        <tr class="{{ $dia['fecha']->isToday() ? 'bg-blue-50' : 'hover:bg-gray-50' }}">
                            <td class="px-3 py-2 font-medium text-gray-900 border-r border-gray-200 sticky left-0 bg-white z-5">
                                <div>{{ $dia['nombre'] }}</div>
                                <div class="text-gray-500 text-xs">{{ $dia['dia_mes'] }}</div>
                                @if($dia['fecha']->isToday())
                                    <div class="text-blue-600 font-semibold text-xs">Hoy</div>
                                @endif
                            </td>
                            
                            @foreach($horasDia as $hora)
                                <td class="px-2 py-2 border-r border-gray-200 align-top h-20">
                                    <div class="space-y-1">
                                        @if(isset($citasPorDiaHora[$dia['fecha_key']][$hora]))
                                            @foreach($quirofanos as $quirofano)
                                                @if(isset($citasPorDiaHora[$dia['fecha_key']][$hora][$quirofano->id]))
                                                    @foreach($citasPorDiaHora[$dia['fecha_key']][$hora][$quirofano->id] as $cita)
                                                        <div class="p-1 rounded text-xs cursor-pointer hover:shadow-md transition-all {{ $cita->estado === 'programada' ? 'bg-blue-100 border border-blue-300 hover:bg-blue-200' : ($cita->estado === 'en_curso' ? 'bg-amber-100 border border-amber-300 hover:bg-amber-200' : ($cita->estado === 'finalizada' ? 'bg-green-100 border border-green-300 hover:bg-green-200' : 'bg-red-100 border border-red-300 hover:bg-red-200')) }}"
                                                             onclick="verDetalles({{ $cita->id }})"
                                                             title="{{ $cita->paciente->nombre }} - {{ optional($cita->cirujano->user)->name ?? 'N/A' }}">
                                                            <div class="font-semibold text-gray-900 truncate">Q{{ $quirofano->id }}</div>
                                                            <div class="text-gray-700 truncate">{{ $cita->paciente->nombre }}</div>
                                                            <div class="text-gray-500">{{ $cita->hora_inicio_estimada->format('H:i') }}</div>
                                                            @if($cita->duracion_real && $cita->duracion_real > $cita->duracion_estimada)
                                                                <div class="text-amber-700 font-bold">+{{ $cita->duracion_real - $cita->duracion_estimada }}min</div>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                @endif
                                            @endforeach
                                        @endif
                                    </div>
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Vista Alternativa: Por Quirófano -->
    <div class="mt-8 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <h2 class="text-lg font-bold text-gray-800">Vista por Quirófano</h2>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-3 py-2 text-left font-medium text-gray-500 uppercase tracking-wider w-32 border-r border-gray-200">
                            Quirófano
                        </th>
                        @foreach($diasSemana as $dia)
                            <th class="px-3 py-2 text-center font-medium text-gray-500 uppercase tracking-wider min-w-[120px] border-r border-gray-200">
                                <div>{{ $dia['nombre'] }}</div>
                                <div class="text-gray-400">{{ $dia['dia_mes'] }}</div>
                                @if($dia['fecha']->isToday())
                                    <div class="text-blue-600 font-semibold">Hoy</div>
                                @endif
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($quirofanos as $quirofano)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2 font-medium text-gray-900 border-r border-gray-200">
                                <div>Q{{ $quirofano->id }}</div>
                                <div class="text-gray-500">{{ $quirofano->tipo }}</div>
                            </td>
                            
                            @foreach($diasSemana as $dia)
                                <td class="px-3 py-2 border-r border-gray-200">
                                    <div class="space-y-1 min-h-[60px]">
                                        @php
                                            $citasDelDia = [];
                                            foreach($horasDia as $hora) {
                                                if(isset($citasPorDiaHora[$dia['fecha_key']][$hora][$quirofano->id])) {
                                                    $citasDelDia = array_merge($citasDelDia, $citasPorDiaHora[$dia['fecha_key']][$hora][$quirofano->id]);
                                                }
                                            }
                                        @endphp
                                        
                                        @if(!empty($citasDelDia))
                                            @foreach($citasDelDia as $cita)
                                                <div class="p-1 rounded text-xs cursor-pointer hover:shadow-md transition-all {{ $cita->estado === 'programada' ? 'bg-blue-50 border border-blue-200 hover:bg-blue-100' : ($cita->estado === 'en_curso' ? 'bg-amber-50 border border-amber-200 hover:bg-amber-100' : ($cita->estado === 'finalizada' ? 'bg-green-50 border border-green-200 hover:bg-green-100' : 'bg-red-50 border border-red-200 hover:bg-red-100')) }}"
                                                     onclick="verDetalles({{ $cita->id }})">
                                                    <div class="font-semibold text-gray-900 truncate">{{ $cita->paciente->nombre }}</div>
                                                    <div class="text-gray-600">{{ $cita->hora_inicio_estimada->format('H:i') }}-{{ $cita->hora_fin_estimada->format('H:i') }}</div>
                                                    <div class="text-gray-500 truncate">{{ optional($cita->cirujano->user)->name ?? 'N/A' }}</div>
                                                    <div class="capitalize text-gray-500">{{ $cita->tipo_cirugia }}</div>
                                                    @if($cita->duracion_real && $cita->duracion_real > $cita->duracion_estimada)
                                                        <div class="text-amber-600 font-semibold">+{{ $cita->duracion_real - $cita->duracion_estimada }}min</div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="text-gray-400 text-xs text-center py-4">
                                                Libre
                                            </div>
                                        @endif
                                    </div>
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Leyenda -->
    <div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <h3 class="text-sm font-semibold text-gray-700 mb-3">Leyenda de Estados</h3>
        <div class="flex flex-wrap gap-4 text-xs">
            <div class="flex items-center gap-2">
                <div class="w-4 h-4 bg-blue-50 border border-blue-200 rounded"></div>
                <span class="text-gray-600">Programada</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-4 h-4 bg-amber-50 border border-amber-200 rounded"></div>
                <span class="text-gray-600">En Curso</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-4 h-4 bg-green-50 border border-green-200 rounded"></div>
                <span class="text-gray-600">Finalizada</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-4 h-4 bg-red-50 border border-red-200 rounded"></div>
                <span class="text-gray-600">Cancelada</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-4 h-4 bg-amber-100 rounded"></div>
                <span class="text-amber-600 font-semibold">+Tiempo extra</span>
            </div>
        </div>
    </div>
</div>

<script>
function verDetalles(citaId) {
    window.location.href = `/quirofano/${citaId}`;
}

async function iniciarEmergencia(emergencyId) {
    if (!confirm('¿Iniciar cirugía de emergencia inmediatamente? Esta acción buscará el primer quirófano disponible.')) {
        return;
    }

    try {
        const response = await fetch(`/quirofano/emergencia/${emergencyId}/iniciar`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        const result = await response.json();

        if (result.success) {
            alert('Cirugía iniciada: ' + result.message);
            if (result.redirect) {
                window.location.href = result.redirect;
            } else {
                location.reload();
            }
        } else {
            alert('Error: ' + (result.message || 'No se pudo iniciar la cirugía'));
        }
    } catch (error) {
        alert('Error de conexión: ' + error.message);
    }
}

// Actualizar cada 30 segundos para mostrar cambios en tiempo real
setInterval(() => {
    location.reload();
}, 30000);
</script>
@endsection
