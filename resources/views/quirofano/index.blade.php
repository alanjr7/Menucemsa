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
                    <p class="text-lg lg:text-2xl font-bold text-gray-900" id="stat-total">{{ $stats['total_semana'] }}</p>
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
                    <p class="text-lg lg:text-2xl font-bold text-amber-600" id="stat-hoy">{{ $stats['hoy'] }}</p>
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
                    <p class="text-lg lg:text-2xl font-bold text-red-600" id="stat-en-curso">{{ $stats['en_curso'] }}</p>
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
                    <p class="text-lg lg:text-2xl font-bold text-green-600" id="stat-finalizadas">{{ $stats['finalizadas'] }}</p>
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
                    <p class="text-lg lg:text-2xl font-bold text-purple-700" id="stat-emergencias">{{ $stats['emergencias'] }}</p>
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
                <tbody class="divide-y divide-gray-200" id="tbody-emergencias">
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
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Vista: Tabla Horario -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
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
                <tbody class="bg-white divide-y divide-gray-200" id="tbody-horario">
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
                <tbody class="bg-white divide-y divide-gray-200" id="tbody-quirofanos">
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

<!-- Modal para selección de cirujano -->
<div id="modalCirujano" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <!-- Overlay de fondo -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="cerrarModalCirujano()"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <!-- Contenido del modal -->
        <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-purple-100 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                            Seleccionar Cirujano
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500 mb-4">
                                Seleccione el cirujano que realizará la cirugía de emergencia.
                            </p>
                            
                            <!-- Lista de cirujanos -->
                            <div id="listaCirujanos" class="space-y-2 max-h-64 overflow-y-auto">
                                <div class="text-center text-gray-500 py-4">
                                    Cargando médicos...
                                </div>
                            </div>

                            <!-- Cirujano seleccionado -->
                            <div id="cirujanoSeleccionadoInfo" class="mt-4 p-3 bg-green-50 rounded-lg border border-green-200 hidden">
                                <p class="text-sm text-green-800">
                                    <span class="font-semibold">Cirujano seleccionado:</span>
                                    <span id="nombreCirujanoSeleccionado"></span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" id="btnIniciarCirugia" onclick="confirmarIniciarEmergencia()" disabled
                    class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-purple-600 text-base font-medium text-white hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                    Iniciar Cirugía
                </button>
                <button type="button" onclick="cerrarModalCirujano()"
                    class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Cancelar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let currentEmergencyId = null;
let cirujanoSeleccionado = null;

function verDetalles(citaId) {
    window.location.href = `/quirofano/${citaId}`;
}

// Abrir modal de selección de cirujano
async function iniciarEmergencia(emergencyId) {
    currentEmergencyId = emergencyId;
    cirujanoSeleccionado = null;
    
    // Resetear UI
    document.getElementById('btnIniciarCirugia').disabled = true;
    document.getElementById('cirujanoSeleccionadoInfo').classList.add('hidden');
    document.getElementById('listaCirujanos').innerHTML = '<div class="text-center text-gray-500 py-4">Cargando médicos...</div>';
    
    // Mostrar modal
    document.getElementById('modalCirujano').classList.remove('hidden');
    
    // Cargar médicos
    try {
        const response = await fetch('{{ route('quirofano.medicos-disponibles') }}', {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        
        const data = await response.json();
        
        if (data.success && data.medicos.length > 0) {
            const container = document.getElementById('listaCirujanos');
            container.innerHTML = '';
            
            data.medicos.forEach(medico => {
                const div = document.createElement('div');
                div.className = `p-3 rounded-lg border cursor-pointer transition-all hover:shadow-md ${medico.disponible ? 'border-gray-200 hover:border-purple-300 bg-white' : 'border-gray-100 bg-gray-50 opacity-60'}`;
                div.onclick = () => medico.disponible && seleccionarCirujano(medico, div);
                
                div.innerHTML = `
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="h-10 w-10 rounded-full bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center text-white font-semibold text-sm">
                                ${medico.nombre.charAt(0).toUpperCase()}
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">${medico.nombre}</p>
                                <p class="text-xs text-gray-500">${medico.especialidad}</p>
                            </div>
                        </div>
                        <div class="flex items-center">
                            ${medico.disponible 
                                ? `<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Disponible</span>`
                                : `<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">No disponible</span>`
                            }
                        </div>
                    </div>
                `;
                
                container.appendChild(div);
            });
        } else {
            document.getElementById('listaCirujanos').innerHTML = '<div class="text-center text-gray-500 py-4">No hay médicos disponibles</div>';
        }
    } catch (error) {
        console.error('Error cargando médicos:', error);
        document.getElementById('listaCirujanos').innerHTML = '<div class="text-center text-red-500 py-4">Error al cargar médicos</div>';
    }
}

// Seleccionar cirujano
function seleccionarCirujano(medico, elemento) {
    cirujanoSeleccionado = medico;
    
    // Actualizar UI - quitar selección anterior
    document.querySelectorAll('#listaCirujanos > div').forEach(div => {
        div.classList.remove('ring-2', 'ring-purple-500', 'bg-purple-50');
    });
    
    // Marcar seleccionado
    elemento.classList.add('ring-2', 'ring-purple-500', 'bg-purple-50');
    
    // Mostrar info
    document.getElementById('nombreCirujanoSeleccionado').textContent = medico.nombre + ' (' + medico.especialidad + ')';
    document.getElementById('cirujanoSeleccionadoInfo').classList.remove('hidden');
    
    // Habilitar botón
    document.getElementById('btnIniciarCirugia').disabled = false;
}

// Cerrar modal
function cerrarModalCirujano() {
    document.getElementById('modalCirujano').classList.add('hidden');
    currentEmergencyId = null;
    cirujanoSeleccionado = null;
}

// Confirmar e iniciar cirugía
async function confirmarIniciarEmergencia() {
    if (!currentEmergencyId || !cirujanoSeleccionado) {
        alert('Por favor seleccione un cirujano');
        return;
    }
    
    try {
        const response = await fetch(`/quirofano/emergencia/${currentEmergencyId}/iniciar`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                ci_cirujano: cirujanoSeleccionado.ci
            })
        });

        const result = await response.json();

        if (result.success) {
            cerrarModalCirujano();
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

// Auto-refresh usando AJAX (sin recargar página)
let autoRefresh = null;

document.addEventListener('DOMContentLoaded', function() {
    iniciarAutoRefresh();
});

function iniciarAutoRefresh() {
    autoRefresh = new AutoRefresh({
        interval: 5000,
        endpoint: '{{ route('quirofano.api.dashboard') }}',
        onData: (data) => {
            if (data.success) {
                actualizarStats(data.stats);
                actualizarEmergencias(data.emergencias);
                actualizarHorario(data);
                actualizarVistaQuirofanos(data);
            }
        },
        onError: (err) => {
            console.warn('Error al actualizar dashboard:', err);
        }
    });
    autoRefresh.start();
}

function actualizarStats(stats) {
    document.getElementById('stat-total').textContent = stats.total_semana || 0;
    document.getElementById('stat-hoy').textContent = stats.hoy || 0;
    document.getElementById('stat-en-curso').textContent = stats.en_curso || 0;
    document.getElementById('stat-finalizadas').textContent = stats.finalizadas || 0;
    document.getElementById('stat-emergencias').textContent = stats.emergencias || 0;
}

function actualizarEmergencias(emergencias) {
    const tbody = document.getElementById('tbody-emergencias');
    if (!emergencias || emergencias.length === 0) {
        tbody.innerHTML = '';
        return;
    }
    
    tbody.innerHTML = emergencias.map(emg => `
        <tr class="hover:bg-purple-50/50">
            <td class="px-4 py-3 whitespace-nowrap">
                <span class="font-mono text-sm font-medium text-purple-600">${emg.code}</span>
            </td>
            <td class="px-4 py-3 whitespace-nowrap">
                <div class="flex items-center">
                    <div class="h-8 w-8 rounded-full bg-purple-100 flex items-center justify-center mr-3">
                        <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-gray-900">${emg.paciente_nombre}</span>
                </div>
            </td>
            <td class="px-4 py-3 whitespace-nowrap">
                <span class="font-mono text-sm text-gray-600">${emg.nro_cirugia || 'N/A'}</span>
            </td>
            <td class="px-4 py-3 whitespace-nowrap">
                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">${emg.status_label}</span>
            </td>
            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">${emg.hora_ingreso}</td>
            <td class="px-4 py-3 whitespace-nowrap">
                <span class="text-sm text-gray-600">${emg.tipo_ingreso}</span>
            </td>
            <td class="px-4 py-3 whitespace-nowrap text-center">
                <div class="flex flex-col gap-2">
                    <a href="/emergency-staff/${emg.id}" class="text-purple-600 hover:text-purple-900 text-sm font-medium">Ver detalle</a>
                    <a href="/quirofano/emergencia/${emg.id}/programar" class="inline-flex items-center justify-center px-3 py-1 bg-blue-600 text-white text-xs font-medium rounded hover:bg-blue-700 transition-colors">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Programar
                    </a>
                </div>
            </td>
        </tr>
    `).join('');
}

function getEstadoClass(estado) {
    const classes = {
        'programada': 'bg-blue-100 border border-blue-300 hover:bg-blue-200',
        'en_curso': 'bg-amber-100 border border-amber-300 hover:bg-amber-200',
        'finalizada': 'bg-green-100 border border-green-300 hover:bg-green-200',
        'cancelada': 'bg-red-100 border border-red-300 hover:bg-red-200'
    };
    return classes[estado] || 'bg-gray-100 border border-gray-300';
}

function actualizarHorario(data) {
    const tbody = document.getElementById('tbody-horario');
    const { diasSemana, horasDia, quirofanos, citasPorDiaHora } = data;
    
    tbody.innerHTML = diasSemana.map(dia => {
        const hoyClass = dia.is_today ? 'bg-blue-50' : 'hover:bg-gray-50';
        const hoyBadge = dia.is_today ? '<div class="text-blue-600 font-semibold text-xs">Hoy</div>' : '';
        
        const celdasHoras = horasDia.map(hora => {
            let citasHtml = '';
            if (citasPorDiaHora[dia.fecha_key] && citasPorDiaHora[dia.fecha_key][hora]) {
                quirofanos.forEach(qf => {
                    if (citasPorDiaHora[dia.fecha_key][hora][qf.id]) {
                        citasPorDiaHora[dia.fecha_key][hora][qf.id].forEach(cita => {
                            const extraTime = cita.duracion_real && cita.duracion_real > cita.duracion_estimada 
                                ? `<div class="text-amber-700 font-bold">+${cita.duracion_real - cita.duracion_estimada}min</div>` 
                                : '';
                            citasHtml += `
                                <div class="p-1 rounded text-xs cursor-pointer hover:shadow-md transition-all ${getEstadoClass(cita.estado)}"
                                     onclick="verDetalles(${cita.id})"
                                     title="${cita.paciente.nombre} - ${cita.cirujano?.user?.name || 'N/A'}">
                                    <div class="font-semibold text-gray-900 truncate">Q${qf.id}</div>
                                    <div class="text-gray-700 truncate">${cita.paciente.nombre}</div>
                                    <div class="text-gray-500">${cita.hora_inicio_estimada?.substring(0,5) || ''}</div>
                                    ${extraTime}
                                </div>
                            `;
                        });
                    }
                });
            }
            return `<td class="px-2 py-2 border-r border-gray-200 align-top h-20"><div class="space-y-1">${citasHtml}</div></td>`;
        }).join('');
        
        return `
            <tr class="${hoyClass}">
                <td class="px-3 py-2 font-medium text-gray-900 border-r border-gray-200 sticky left-0 bg-white z-5">
                    <div>${dia.nombre}</div>
                    <div class="text-gray-500 text-xs">${dia.dia_mes}</div>
                    ${hoyBadge}
                </td>
                ${celdasHoras}
            </tr>
        `;
    }).join('');
}

function actualizarVistaQuirofanos(data) {
    const tbody = document.getElementById('tbody-quirofanos');
    const { diasSemana, horasDia, quirofanos, citasPorDiaHora } = data;
    
    tbody.innerHTML = quirofanos.map(qf => {
        const celdasDias = diasSemana.map(dia => {
            const citasDelDia = [];
            horasDia.forEach(hora => {
                if (citasPorDiaHora[dia.fecha_key] && citasPorDiaHora[dia.fecha_key][hora] && citasPorDiaHora[dia.fecha_key][hora][qf.id]) {
                    citasDelDia.push(...citasPorDiaHora[dia.fecha_key][hora][qf.id]);
                }
            });
            
            if (citasDelDia.length === 0) {
                return '<td class="px-3 py-2 border-r border-gray-200"><div class="space-y-1 min-h-[60px]"><div class="text-gray-400 text-xs text-center py-4">Libre</div></div></td>';
            }
            
            const citasHtml = citasDelDia.map(cita => {
                const extraTime = cita.duracion_real && cita.duracion_real > cita.duracion_estimada 
                    ? `<div class="text-amber-600 font-semibold">+${cita.duracion_real - cita.duracion_estimada}min</div>` 
                    : '';
                return `
                    <div class="p-1 rounded text-xs cursor-pointer hover:shadow-md transition-all ${getEstadoClass(cita.estado).replace('bg-blue-100', 'bg-blue-50').replace('bg-amber-100', 'bg-amber-50').replace('bg-green-100', 'bg-green-50').replace('bg-red-100', 'bg-red-50')}"
                         onclick="verDetalles(${cita.id})">
                        <div class="font-semibold text-gray-900 truncate">${cita.paciente.nombre}</div>
                        <div class="text-gray-600">${cita.hora_inicio_estimada?.substring(0,5) || ''}-${cita.hora_fin_estimada?.substring(0,5) || ''}</div>
                        <div class="text-gray-500 truncate">${cita.cirujano?.user?.name || 'N/A'}</div>
                        <div class="capitalize text-gray-500">${cita.tipo_cirugia || ''}</div>
                        ${extraTime}
                    </div>
                `;
            }).join('');
            
            return `<td class="px-3 py-2 border-r border-gray-200"><div class="space-y-1 min-h-[60px]">${citasHtml}</div></td>`;
        }).join('');
        
        return `
            <tr class="hover:bg-gray-50">
                <td class="px-3 py-2 font-medium text-gray-900 border-r border-gray-200">
                    <div>Q${qf.id}</div>
                    <div class="text-gray-500">${qf.tipo}</div>
                </td>
                ${celdasDias}
            </tr>
        `;
    }).join('');
}

// Detener auto-refresh al salir
window.addEventListener('beforeunload', () => {
    if (autoRefresh) autoRefresh.stop();
});
</script>

<script src="{{ asset('js/auto-refresh.js') }}"></script>
@endsection
