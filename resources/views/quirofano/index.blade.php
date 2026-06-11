@extends('layouts.app')

@section('content')
<div class="w-full p-4 bg-slate-50/50 min-h-screen">

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-800">Calendario de Quirófanos</h1>
            <p class="text-sm text-slate-500">Programación quirúrgica mensual</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('quirofano.historial') }}" class="flex items-center px-3 py-2 border border-slate-600 text-slate-600 rounded-lg hover:bg-slate-50 font-medium transition-colors text-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <span class="hidden sm:inline">Historial</span>
                <span class="sm:hidden">Hist.</span>
            </a>
            @if(Auth::user()->isAdmin()||Auth::user()->hasRole('administrador'))
            <a href="{{ route('quirofano.create') }}" class="flex items-center px-3 py-2 bg-slate-700 text-white rounded-lg hover:bg-slate-800 font-medium transition-colors text-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span class="hidden sm:inline">Nueva Cita</span>
                <span class="sm:hidden">+</span>
            </a>
            <a href="{{ route('quirofanos.management.index') }}" class="flex items-center px-4 py-2 border border-slate-600 text-slate-600 rounded-lg hover:bg-slate-50 font-medium transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                Gestionar Quirófanos
            </a>
            <a href="{{ route('quirofano.medicamentos.index') }}" class="flex items-center px-4 py-2 border border-emerald-600 text-emerald-600 rounded-lg hover:bg-emerald-50 font-medium transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                </svg>
                Gestionar Medicamentos
            </a>
            @endif
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-3 lg:gap-6 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 lg:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs lg:text-sm font-medium text-slate-500">Total Mes</p>
                    <p class="text-lg lg:text-2xl font-bold text-slate-900" id="stat-total">{{ $stats['total_mes'] }}</p>
                </div>
                <div class="w-8 h-8 lg:w-12 lg:h-12 bg-slate-100 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 lg:w-6 lg:h-6 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 lg:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs lg:text-sm font-medium text-slate-500">Citas Hoy</p>
                    <p class="text-lg lg:text-2xl font-bold text-orange-600" id="stat-hoy">{{ $stats['hoy'] }}</p>
                </div>
                <div class="w-8 h-8 lg:w-12 lg:h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 lg:w-6 lg:h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 lg:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs lg:text-sm font-medium text-slate-500">En Curso</p>
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
                    <p class="text-xs lg:text-sm font-medium text-slate-500">Finalizadas Hoy</p>
                    <p class="text-lg lg:text-2xl font-bold text-emerald-600" id="stat-finalizadas">{{ $stats['finalizadas'] }}</p>
                </div>
                <div class="w-8 h-8 lg:w-12 lg:h-12 bg-emerald-100 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 lg:w-6 lg:h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-6m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Emergencias en Quirófano -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 lg:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs lg:text-sm font-medium text-slate-600">Emergencias</p>
                    <p class="text-lg lg:text-2xl font-bold text-slate-700" id="stat-emergencias">{{ $stats['emergencias'] }}</p>
                </div>
                <div class="w-8 h-8 lg:w-12 lg:h-12 bg-slate-100 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 lg:w-6 lg:h-6 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Emergencias en Quirófano -->
    @if($emergenciasEnQuirofano->count() > 0)
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 mb-6 overflow-hidden">
        <div class="p-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-slate-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                <h3 class="font-bold text-slate-800">Pacientes en Quirófano</h3>
            </div>
            <span class="px-3 py-1 bg-slate-100 text-slate-700 text-sm font-semibold rounded-full">
                {{ $emergenciasEnQuirofano->count() }} paciente(s)
            </span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Código</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Paciente</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">N° Cirugía</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Estado</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Hora Ingreso</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Origen</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-slate-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200" id="tbody-emergencias">
                    @foreach($emergenciasEnQuirofano as $emg)
                    <tr class="hover:bg-slate-50/50">
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="font-mono text-sm font-medium text-slate-600">{{ $emg['code'] }}</span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="h-8 w-8 rounded-full bg-slate-100 flex items-center justify-center mr-3">
                                    <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                                <span class="text-sm font-medium text-slate-900">{{ $emg['paciente_nombre'] }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="font-mono text-sm text-slate-600">{{ $emg['nro_cirugia'] ?? 'N/A' }}</span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-slate-100 text-slate-800">
                                {{ $emg['status_label'] }}
                            </span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-slate-500">
                            {{ $emg['hora_ingreso'] }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            @php
                                $origenColor = match($emg['origen_label'] ?? '') {
                                    'Derivado desde Internación' => 'bg-slate-100 text-slate-800',
                                    'Ingreso desde Recepción' => 'bg-slate-100 text-slate-800',
                                    'Derivado desde Emergencia' => 'bg-red-100 text-red-800',
                                    'Derivado desde UTI' => 'bg-orange-100 text-orange-800',
                                    default => 'bg-slate-100 text-slate-800',
                                };
                            @endphp
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $origenColor }}">
                                {{ $emg['origen_label'] ?? $emg['tipo_ingreso'] }}
                            </span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-center">
                            <div class="flex flex-col gap-2">
                                <a href="{{ route('emergency-staff.show', $emg['id']) }}" class="text-slate-600 hover:text-slate-900 text-sm font-medium">
                                    Ver detalle
                                </a>
                                <a href="{{ route('quirofano.programar-emergencia', $emg['id']) }}" class="inline-flex items-center justify-center px-3 py-1 bg-slate-700 text-white text-xs font-medium rounded hover:bg-slate-800 transition-colors">
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

    <!-- Calendario Mensual -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <!-- Barra de mes + navegación -->
        <div class="p-4 sm:p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('quirofano.index', ['mes' => $mesAnterior]) }}"
                   class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50 transition-colors"
                   aria-label="Mes anterior">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <h2 class="text-lg sm:text-xl font-bold text-slate-800 capitalize min-w-[180px] text-center">{{ $mesLabel }}</h2>
                <a href="{{ route('quirofano.index', ['mes' => $mesSiguiente]) }}"
                   class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50 transition-colors"
                   aria-label="Mes siguiente">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
                <a href="{{ route('quirofano.index') }}"
                   class="ml-1 px-3 py-1.5 text-sm font-medium border border-slate-200 text-slate-600 rounded-lg hover:bg-slate-50 transition-colors">
                    Hoy
                </a>
            </div>

            <!-- Leyenda de estados -->
            <div class="flex flex-wrap items-center gap-3 text-xs text-slate-500">
                <span class="inline-flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-slate-400"></span>Programada</span>
                <span class="inline-flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-orange-400"></span>En curso</span>
                <span class="inline-flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-emerald-400"></span>Finalizada</span>
                <span class="inline-flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-red-400"></span>Cancelada</span>
            </div>
        </div>

        <!-- Encabezado días de la semana -->
        <div class="grid grid-cols-7 bg-slate-700 text-white text-xs sm:text-sm font-semibold uppercase tracking-wide">
            @foreach(['Dom.', 'Lun.', 'Mar.', 'Mié.', 'Jue.', 'Vie.', 'Sáb.'] as $diaNombre)
                <div class="px-2 py-3 text-center border-r border-slate-600 last:border-r-0">{{ $diaNombre }}</div>
            @endforeach
        </div>

        <!-- Grilla del mes -->
        <div id="calendario-grid" class="divide-y divide-slate-200">
            @foreach($semanas as $semana)
                <div class="grid grid-cols-7 divide-x divide-slate-200">
                    @foreach($semana as $celda)
                        @php $citasDia = $citasPorDia[$celda['fecha_key']] ?? []; @endphp
                        <div class="dia-celda min-h-[96px] sm:min-h-[120px] p-1.5 align-top transition-colors
                                    {{ $celda['es_otro_mes'] ? 'bg-slate-50/70' : 'bg-white' }}
                                    {{ $celda['es_hoy'] ? 'ring-2 ring-inset ring-slate-400 bg-slate-50' : '' }}
                                    {{ count($citasDia) > 0 ? 'cursor-pointer hover:bg-slate-50' : '' }}"
                             @if(count($citasDia) > 0) onclick="abrirDia('{{ $celda['fecha_key'] }}')" @endif>
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-xs sm:text-sm font-semibold
                                    {{ $celda['es_otro_mes'] ? 'text-slate-300' : 'text-slate-700' }}
                                    {{ $celda['es_hoy'] ? 'flex items-center justify-center w-6 h-6 rounded-full bg-slate-700 text-white' : '' }}">
                                    {{ $celda['dia'] }}
                                </span>
                                @if(count($citasDia) > 0)
                                    <span class="text-[10px] font-semibold px-1.5 py-0.5 rounded-full bg-slate-100 text-slate-600">{{ count($citasDia) }}</span>
                                @endif
                            </div>

                            <div class="space-y-1">
                                @foreach(array_slice($citasDia, 0, 3) as $cita)
                                    @php
                                        $estadoChip = match($cita['estado']) {
                                            'programada' => 'bg-slate-100 text-slate-700 border-slate-300',
                                            'en_curso' => 'bg-orange-100 text-orange-800 border-orange-300',
                                            'finalizada' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
                                            'cancelada' => 'bg-red-100 text-red-800 border-red-300',
                                            default => 'bg-slate-100 text-slate-700 border-slate-300',
                                        };
                                    @endphp
                                    <div class="text-[10px] sm:text-xs px-1.5 py-0.5 rounded border truncate {{ $estadoChip }}"
                                         title="{{ $cita['hora_inicio'] }} · {{ $cita['paciente'] }} · Q{{ $cita['quirofano'] }}">
                                        <span class="font-semibold">{{ $cita['hora_inicio'] }}</span>
                                        <span class="hidden sm:inline">· {{ \Illuminate\Support\Str::limit($cita['paciente'], 12) }}</span>
                                    </div>
                                @endforeach
                                @if(count($citasDia) > 3)
                                    <div class="text-[10px] sm:text-xs text-slate-500 font-medium pl-1">+{{ count($citasDia) - 3 }} más</div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Modal: detalle de cirugías del día -->
<div id="modalDia" class="fixed inset-0 z-50 hidden overflow-y-auto" role="dialog" aria-modal="true">
    <div class="flex items-end sm:items-center justify-center min-h-screen px-4 py-6 text-center">
        <div class="fixed inset-0 bg-slate-500/75 transition-opacity" aria-hidden="true" onclick="cerrarModalDia()"></div>
        <div class="relative inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50">
                <div class="flex items-center gap-3">
                    <div class="flex items-center justify-center h-10 w-10 rounded-full bg-slate-100">
                        <svg class="h-5 w-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900" id="modalDiaTitulo">Cirugías del día</h3>
                        <p class="text-xs text-slate-500" id="modalDiaSubtitulo"></p>
                    </div>
                </div>
                <button type="button" onclick="cerrarModalDia()" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div id="modalDiaLista" class="px-5 py-4 space-y-3 max-h-[60vh] overflow-y-auto"></div>
        </div>
    </div>
</div>

<script>
    // Datos del mes para el modal de día (clave Y-m-d => array de citas)
    let citasPorDia = @json($citasPorDia);
    const mesActual = @json($mesActual);

    const estadoColores = {
        'programada': { chip: 'bg-slate-100 text-slate-700 border-slate-300', dot: 'bg-slate-400' },
        'en_curso':   { chip: 'bg-orange-100 text-orange-800 border-orange-300', dot: 'bg-orange-400' },
        'finalizada': { chip: 'bg-emerald-100 text-emerald-800 border-emerald-300', dot: 'bg-emerald-400' },
        'cancelada':  { chip: 'bg-red-100 text-red-800 border-red-300', dot: 'bg-red-400' },
    };

    function colorEstado(estado) {
        return estadoColores[estado] || estadoColores['programada'];
    }

    function formatearFechaLarga(ymd) {
        const [y, m, d] = ymd.split('-').map(Number);
        const fecha = new Date(y, m - 1, d);
        return fecha.toLocaleDateString('es-ES', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
    }

    function abrirDia(ymd) {
        const citas = citasPorDia[ymd] || [];
        if (citas.length === 0) return;

        document.getElementById('modalDiaTitulo').textContent = citas.length + (citas.length === 1 ? ' cirugía programada' : ' cirugías programadas');
        document.getElementById('modalDiaSubtitulo').textContent = formatearFechaLarga(ymd);

        const lista = document.getElementById('modalDiaLista');
        lista.innerHTML = citas.map(cita => {
            const c = colorEstado(cita.estado);
            const rango = cita.hora_fin ? `${cita.hora_inicio} - ${cita.hora_fin}` : cita.hora_inicio;
            return `
                <a href="/quirofano/${cita.id}" class="block p-3 rounded-xl border border-slate-200 hover:border-slate-300 hover:shadow-sm transition-all">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-start gap-3 min-w-0">
                            <div class="text-center flex-shrink-0">
                                <div class="text-sm font-bold text-slate-800">${cita.hora_inicio}</div>
                                <div class="text-[10px] text-slate-400">${cita.hora_fin || ''}</div>
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-slate-900 truncate">${cita.paciente}</p>
                                <p class="text-xs text-slate-500 truncate">Dr. ${cita.cirujano} · Q${cita.quirofano}</p>
                                <p class="text-xs text-slate-400 capitalize">${cita.tipo_cirugia || ''}</p>
                            </div>
                        </div>
                        <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-full text-[10px] font-semibold border ${c.chip} flex-shrink-0">
                            <span class="w-1.5 h-1.5 rounded-full ${c.dot}"></span>${cita.estado_label}
                        </span>
                    </div>
                    <div class="mt-2 text-[11px] text-slate-400 flex items-center gap-1">
                        <span>Horario:</span><span class="font-medium text-slate-500">${rango}</span>
                    </div>
                </a>
            `;
        }).join('');

        document.getElementById('modalDia').classList.remove('hidden');
    }

    function cerrarModalDia() {
        document.getElementById('modalDia').classList.add('hidden');
    }

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') cerrarModalDia();
    });

    // Auto-refresh: actualiza stats, emergencias y conteos del calendario sin recargar
    let autoRefresh = null;

    document.addEventListener('DOMContentLoaded', function () {
        let endpoint = '{{ route('quirofano.api.dashboard') }}?mes=' + encodeURIComponent(mesActual);
        autoRefresh = new AutoRefresh({
            interval: 5000,
            endpoint: endpoint,
            onData: (data) => {
                if (!data.success) return;
                actualizarStats(data.stats);
                actualizarEmergencias(data.emergencias);
                if (data.citasPorDia) {
                    citasPorDia = data.citasPorDia;
                    actualizarConteosCalendario();
                }
            },
            onError: (err) => console.warn('Error al actualizar calendario:', err),
        });
        autoRefresh.start();
    });

    function actualizarStats(stats) {
        document.getElementById('stat-total').textContent = stats.total_mes || 0;
        document.getElementById('stat-hoy').textContent = stats.hoy || 0;
        document.getElementById('stat-en-curso').textContent = stats.en_curso || 0;
        document.getElementById('stat-finalizadas').textContent = stats.finalizadas || 0;
        document.getElementById('stat-emergencias').textContent = stats.emergencias || 0;
    }

    // Re-renderiza los chips de cada día a partir del payload actualizado
    function actualizarConteosCalendario() {
        document.querySelectorAll('.dia-celda').forEach(celda => {
            const onclick = celda.getAttribute('onclick');
            if (!onclick) return;
            const match = onclick.match(/abrirDia\('([^']+)'\)/);
            if (!match) return;
            const ymd = match[1];
            const citas = citasPorDia[ymd] || [];
            const chipsContainer = celda.querySelector('.space-y-1');
            const badge = celda.querySelector('.rounded-full.bg-slate-100');
            if (badge) badge.textContent = citas.length;
            if (!chipsContainer) return;

            chipsContainer.innerHTML = citas.slice(0, 3).map(cita => {
                const c = colorEstado(cita.estado);
                const nombre = (cita.paciente || '').length > 12 ? cita.paciente.substring(0, 12) + '…' : (cita.paciente || '');
                return `<div class="text-[10px] sm:text-xs px-1.5 py-0.5 rounded border truncate ${c.chip}" title="${cita.hora_inicio} · ${cita.paciente} · Q${cita.quirofano}">
                            <span class="font-semibold">${cita.hora_inicio}</span><span class="hidden sm:inline">· ${nombre}</span>
                        </div>`;
            }).join('') + (citas.length > 3
                ? `<div class="text-[10px] sm:text-xs text-slate-500 font-medium pl-1">+${citas.length - 3} más</div>`
                : '');
        });
    }

    function actualizarEmergencias(emergencias) {
        const tbody = document.getElementById('tbody-emergencias');
        if (!tbody) return;
        if (!emergencias || emergencias.length === 0) {
            tbody.innerHTML = '';
            return;
        }
        tbody.innerHTML = emergencias.map(emg => `
            <tr class="hover:bg-slate-50/50">
                <td class="px-4 py-3 whitespace-nowrap"><span class="font-mono text-sm font-medium text-slate-600">${emg.code}</span></td>
                <td class="px-4 py-3 whitespace-nowrap">
                    <div class="flex items-center">
                        <div class="h-8 w-8 rounded-full bg-slate-100 flex items-center justify-center mr-3">
                            <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <span class="text-sm font-medium text-slate-900">${emg.paciente_nombre}</span>
                    </div>
                </td>
                <td class="px-4 py-3 whitespace-nowrap"><span class="font-mono text-sm text-slate-600">${emg.nro_cirugia || 'N/A'}</span></td>
                <td class="px-4 py-3 whitespace-nowrap"><span class="px-2 py-1 text-xs font-semibold rounded-full bg-slate-100 text-slate-800">${emg.status_label}</span></td>
                <td class="px-4 py-3 whitespace-nowrap text-sm text-slate-500">${emg.hora_ingreso}</td>
                <td class="px-4 py-3 whitespace-nowrap"><span class="px-2 py-1 text-xs font-semibold rounded-full bg-slate-100 text-slate-800">${emg.origen_label || emg.tipo_ingreso}</span></td>
                <td class="px-4 py-3 whitespace-nowrap text-center">
                    <div class="flex flex-col gap-2">
                        <a href="/emergency-staff/${emg.id}" class="text-slate-600 hover:text-slate-900 text-sm font-medium">Ver detalle</a>
                        <a href="/quirofano/emergencia/${emg.id}/programar" class="inline-flex items-center justify-center px-3 py-1 bg-slate-700 text-white text-xs font-medium rounded hover:bg-slate-800 transition-colors">Programar</a>
                    </div>
                </td>
            </tr>
        `).join('');
    }

    window.addEventListener('beforeunload', () => {
        if (autoRefresh) autoRefresh.stop();
    });
</script>

<script src="{{ asset('js/auto-refresh.js') }}"></script>
@endsection
