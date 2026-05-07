@extends('layouts.app')

@php
// Obtener permisos del usuario actual
$user = auth()->user();
$userPermissions = [];

if ($user->isEnfermeraInternacion()) {
    $enfermera = \App\Models\Enfermera::where('user_id', $user->id)->first();
    if ($enfermera) {
        $userPermissions = $enfermera->getPermissionKeys();
    }
} else {
    // Roles internacion, admin, dirmedico tienen todos los permisos
    $userPermissions = array_keys(\App\Models\EnfermeraPermission::AVAILABLE_PERMISSIONS);
}

// Helper function to check permission
$hasPermission = function($permission) use ($userPermissions) {
    return in_array($permission, $userPermissions);
};
@endphp

@section('content')
<div class="p-6 bg-gray-50/50 min-h-screen">
    <!-- Header -->
    <div class="flex justify-between items-end mb-8">
        <div>
            @if(auth()->user()->isEnfermeraInternacion())
                <h1 class="text-2xl font-bold text-gray-800">Panel de Enfermería Internación</h1>
                <p class="text-sm text-gray-500">Enfermera Internación - Atención de pacientes</p>
            @else
                <h1 class="text-2xl font-bold text-gray-800">Dashboard de Internación</h1>
                <p class="text-sm text-gray-500">Personal de Internación - Gestión de pacientes</p>
            @endif
        </div>
        <div class="flex gap-3">
           
           
            @if(auth()->user()->isInternacion() || auth()->user()->isAdmin())
            <a href="{{ route('internacion-staff.medicamentos.index') }}" class="flex items-center px-4 py-2 bg-indigo-600 text-white font-medium rounded-xl hover:bg-indigo-700 transition-all shadow-sm">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                </svg>
                Gestionar Medicamentos
            </a>
            @endif
            @if(auth()->user()->isInternacion() || auth()->user()->isAdmin())
            <a href="{{ route('internacion-staff.enfermeras.index') }}" class="flex items-center px-4 py-2 bg-purple-600 text-white font-medium rounded-xl hover:bg-purple-700 transition-all shadow-sm">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                Gestionar Enfermeras
            </a>
            @endif
           
            
        
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex flex-col items-center justify-center h-28 hover:shadow-md transition">
            <span class="text-gray-500 text-sm font-medium mb-1">Pacientes Activos</span>
            <span class="text-3xl font-bold text-blue-600" id="stat-activos">0</span>
        </div>
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex flex-col items-center justify-center h-28 hover:shadow-md transition">
            <span class="text-gray-500 text-sm font-medium mb-1">En Espera</span>
            <span class="text-3xl font-bold text-yellow-600" id="stat-espera">0</span>
        </div>
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex flex-col items-center justify-center h-28 hover:shadow-md transition">
            <span class="text-gray-500 text-sm font-medium mb-1">En Atención</span>
            <span class="text-3xl font-bold text-green-600" id="stat-atencion">0</span>
        </div>
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex flex-col items-center justify-center h-28 hover:shadow-md transition">
            <span class="text-gray-500 text-sm font-medium mb-1">Hoy Ingresados</span>
            <span class="text-3xl font-bold text-indigo-600" id="stat-hoy">0</span>
        </div>
    </div>

    <!-- Historial de Operaciones -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <h2 class="text-lg font-bold text-gray-800">Historial del Área - {{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}</h2>
            <form method="GET" action="{{ route('internacion-staff.dashboard') }}" class="flex items-center gap-2">
                <label class="text-sm text-gray-500 whitespace-nowrap">Fecha:</label>
                <input type="date" name="fecha" value="{{ $fecha }}"
                    class="border border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:border-blue-500"
                    onchange="this.form.submit()">
            </form>
        </div>

        @php
            // Combinar evaluaciones y habitaciones en una sola lista
            $operaciones = collect();

            foreach ($evaluaciones as $ev) {
                $operaciones->push([
                    'tipo' => 'evaluacion',
                    'fecha' => $ev->created_at,
                    'usuario' => $ev->user?->name ?? '—',
                    'paciente' => $ev->paciente?->nombre ?? '—',
                    'detalle' => 'Realizó evaluación',
                    'hora' => $ev->created_at->setTimezone('America/La_Paz')->format('H:i'),
                ]);
            }

            foreach ($habitacionesRegistradas as $h) {
                $operaciones->push([
                    'tipo' => 'habitacion',
                    'fecha' => $h->created_at,
                    'usuario' => $h->user?->name ?? 'Sin usuario',
                    'paciente' => $h->cuentaCobro?->paciente?->nombre ?? '—',
                    'detalle' => 'Registró ' . $h->descripcion,
                    'hora' => $h->created_at->setTimezone('America/La_Paz')->format('H:i'),
                ]);
            }

            $operaciones = $operaciones->sortBy('fecha')->values();
        @endphp

        @if($operaciones->isEmpty())
            <p class="text-sm text-gray-400 py-6 text-center">Sin operaciones registradas el {{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}</p>
        @else
            <div class="space-y-3">
                @foreach($operaciones as $op)
                    <div class="flex items-center gap-4 p-3 rounded-lg {{ $op['tipo'] === 'evaluacion' ? 'bg-blue-50' : 'bg-indigo-50' }} border {{ $op['tipo'] === 'evaluacion' ? 'border-blue-100' : 'border-indigo-100' }}">
                        <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center {{ $op['tipo'] === 'evaluacion' ? 'bg-blue-100 text-blue-600' : 'bg-indigo-100 text-indigo-600' }}">
                            @if($op['tipo'] === 'evaluacion')
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                                </svg>
                            @else
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-800">
                                <span class="font-semibold">{{ $op['hora'] }}</span> -
                                <span class="font-medium">{{ $op['usuario'] }}</span>
                                {{ $op['detalle'] }} a
                                <span class="font-medium">{{ $op['paciente'] }}</span>
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

 

<!-- Modal de Acciones Profesional -->
<div id="modalAcciones" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 transition-all duration-300">
    <div class="bg-white shadow-2xl rounded-2xl max-w-2xl w-full overflow-hidden transform transition-all duration-300 scale-100">
        <!-- Header Médico Profesional -->
        <div class="bg-gradient-to-r from-indigo-700 via-indigo-800 to-indigo-900 px-6 py-4 rounded-t-2xl relative overflow-hidden">
            <!-- Decoración sutil -->
            <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/2"></div>
            <div class="absolute bottom-0 left-0 w-32 h-32 bg-indigo-500/20 rounded-full translate-y-1/2 -translate-x-1/2"></div>

            <div class="flex justify-between items-center relative z-10">
                <div class="flex items-center gap-4">
                    <div class="w-11 h-11 bg-white/15 backdrop-blur rounded-lg flex items-center justify-center border border-white/20">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-white tracking-tight">Acciones del Paciente</h3>
                        <p class="text-sm text-indigo-100 mt-1 flex items-center gap-2">
                            <span class="w-1.5 h-1.5 bg-emerald-400 rounded-full animate-pulse"></span>
                            <span id="modal-paciente-nombre" class="font-medium"></span>
                        </p>
                    </div>
                </div>
                <button onclick="cerrarModal()" class="w-9 h-9 bg-white/10 hover:bg-white/20 rounded-lg flex items-center justify-center transition-all duration-200 border border-white/20 hover:border-white/30 group">
                    <svg class="w-5 h-5 text-white/80 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Secciones Organizadas -->
        <div class="p-4 bg-slate-50/70">
            <!-- Sección: Gestión de Estado -->
            @if($hasPermission('cambiar_estados_internacion'))
            <div class="mb-4">
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-1 h-4 bg-amber-500 rounded-full"></div>
                    <h4 class="text-sm font-semibold text-slate-700 uppercase tracking-wider">Cambiar Estado</h4>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <button onclick="cambiarEstado('estable')" class="group relative flex flex-col items-center p-3 bg-white border border-emerald-200 rounded-lg hover:border-emerald-400 hover:shadow-md hover:shadow-emerald-500/10 transition-all duration-200 text-center">
                        <div class="w-9 h-9 bg-emerald-50 group-hover:bg-emerald-100 rounded-md flex items-center justify-center mb-1.5 transition-colors duration-200">
                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <span class="font-semibold text-slate-800 text-sm block mb-0.5">Marcar Estable</span>
                        <span class="text-xs text-slate-500">Paciente estable</span>
                    </button>

                    <button onclick="cambiarEstado('critico')" class="group relative flex flex-col items-center p-3 bg-white border border-red-200 rounded-lg hover:border-red-400 hover:shadow-md hover:shadow-red-500/10 transition-all duration-200 text-center">
                        <div class="w-9 h-9 bg-red-50 group-hover:bg-red-100 rounded-md flex items-center justify-center mb-1.5 transition-colors duration-200">
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <span class="font-semibold text-slate-800 text-sm block mb-0.5">Marcar Crítico</span>
                        <span class="text-xs text-slate-500">Estado crítico</span>
                    </button>
                </div>
            </div>
            @endif

            <!-- Sección: Derivaciones -->
            @if($hasPermission('derivar_a_uti') || $hasPermission('derivar_a_quirofano'))
            <div class="mb-4">
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-1 h-4 bg-rose-500 rounded-full"></div>
                    <h4 class="text-sm font-semibold text-slate-700 uppercase tracking-wider">Derivaciones</h4>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    @if($hasPermission('derivar_a_uti'))
                    <button onclick="derivarAUti()" class="group relative flex flex-col items-center p-3 bg-white border border-red-200 rounded-lg hover:border-red-400 hover:shadow-md hover:shadow-red-500/10 transition-all duration-200 text-center">
                        <div class="w-9 h-9 bg-red-50 group-hover:bg-red-100 rounded-md flex items-center justify-center mb-1.5 transition-colors duration-200">
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                        </div>
                        <span class="font-semibold text-slate-800 text-sm block mb-0.5">Enviar a UTI</span>
                        <span class="text-xs text-slate-500">Terapia Intensiva</span>
                    </button>
                    @endif

                    @if($hasPermission('derivar_a_quirofano'))
                    <button onclick="derivarAQuirofano()" class="group relative flex flex-col items-center p-3 bg-white border border-cyan-200 rounded-lg hover:border-cyan-400 hover:shadow-md hover:shadow-cyan-500/10 transition-all duration-200 text-center">
                        <div class="w-9 h-9 bg-cyan-50 group-hover:bg-cyan-100 rounded-md flex items-center justify-center mb-1.5 transition-colors duration-200">
                            <svg class="w-5 h-5 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.121 14.121L19 19m-7-7l7-7m-7 7l-2.879 2.879M12 12L9.121 9.121m0 5.758a3 3 0 10-4.243 4.243 3 3 0 004.243-4.243zm0-5.758a3 3 0 10-4.243-4.243 3 3 0 004.243 4.243z"/>
                            </svg>
                        </div>
                        <span class="font-semibold text-slate-800 text-sm block mb-0.5">Enviar a Quirófano</span>
                        <span class="text-xs text-slate-500">Derivar a cirugía</span>
                    </button>
                    @endif
                </div>
            </div>
            @endif

            <!-- Sección: Acciones Principales -->
            <div class="mb-4">
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-1 h-4 bg-blue-600 rounded-full"></div>
                    <h4 class="text-sm font-semibold text-slate-700 uppercase tracking-wider">Atención</h4>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <a id="linkEvaluar" href="#" class="group relative flex flex-col items-center p-3 bg-white border border-blue-200 rounded-lg hover:border-blue-400 hover:shadow-md hover:shadow-blue-500/10 transition-all duration-200 text-center">
                        <div class="w-9 h-9 bg-blue-50 group-hover:bg-blue-100 rounded-md flex items-center justify-center mb-1.5 transition-colors duration-200">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                            </svg>
                        </div>
                        <span class="font-semibold text-slate-800 text-sm block mb-0.5">Evaluar Paciente</span>
                        <span class="text-xs text-slate-500">Medicamentos y drenajes</span>
                    </a>

                    @if($hasPermission('ver_historial_internacion'))
                    <a id="linkHistorial" href="#" class="group relative flex flex-col items-center p-3 bg-white border border-violet-200 rounded-lg hover:border-violet-400 hover:shadow-md hover:shadow-violet-500/10 transition-all duration-200 text-center">
                        <div class="w-9 h-9 bg-violet-50 group-hover:bg-violet-100 rounded-md flex items-center justify-center mb-1.5 transition-colors duration-200">
                            <svg class="w-5 h-5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <span class="font-semibold text-slate-800 text-sm block mb-0.5">Ver Historial</span>
                        <span class="text-xs text-slate-500">Historial completo</span>
                    </a>
                    @endif
                </div>
            </div>

            <!-- Sección: Egreso -->
            @if($hasPermission('dar_alta_internacion'))
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-1 h-4 bg-emerald-500 rounded-full"></div>
                    <h4 class="text-sm font-semibold text-slate-700 uppercase tracking-wider">Egreso</h4>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <button onclick="darAlta()" class="group relative flex items-center justify-center gap-2 p-3 bg-gradient-to-r from-emerald-50 to-emerald-100/50 border-2 border-emerald-200 rounded-lg hover:border-emerald-400 hover:shadow-md hover:shadow-emerald-500/15 transition-all duration-200">
                        <div class="w-9 h-9 bg-emerald-500 group-hover:bg-emerald-600 rounded-md flex items-center justify-center transition-colors duration-200 shadow-sm">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                        </div>
                        <div class="text-left">
                            <span class="font-bold text-emerald-800 text-base block">Dar de Alta</span>
                            <span class="text-xs text-emerald-600">Paciente egresado</span>
                        </div>
                    </button>
                </div>
            </div>
            @endif
        </div>

        <!-- Footer del Modal -->
        <div class="px-4 py-2 bg-slate-100 border-t border-slate-200 rounded-b-2xl">
            <div class="flex items-center justify-between text-xs text-slate-500">
                <div class="flex items-center gap-4">
                    <span class="flex items-center gap-1.5">
                        <span class="w-2 h-2 bg-amber-500 rounded-full"></span>
                        Estado
                    </span>
                    <span class="flex items-center gap-1.5">
                        <span class="w-2 h-2 bg-rose-500 rounded-full"></span>
                        Derivación
                    </span>
                    <span class="flex items-center gap-1.5">
                        <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                        Atención
                    </span>
                    <span class="flex items-center gap-1.5">
                        <span class="w-2 h-2 bg-emerald-500 rounded-full"></span>
                        Egreso
                    </span>
                </div>
                <span>Presione ESC para cerrar</span>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('js/auto-refresh.js') }}"></script>
<script>
    let internacionSeleccionada = null;
    let datosInternaciones = [];
    let autoRefresh = null;

    // Cargar internaciones al iniciar
    document.addEventListener('DOMContentLoaded', function() {
        cargarInternaciones();
        iniciarAutoRefresh();
    });

    function iniciarAutoRefresh() {
        const filtro = document.getElementById('filtro-estado').value;
        let endpoint = '/internacion-staff/api/internaciones';
        if (filtro !== 'todos') {
            endpoint += `?estado=${filtro}`;
        }

        autoRefresh = new AutoRefresh({
            interval: 3000,
            endpoint: endpoint,
            onData: (data) => {
                if (data.success) {
                    datosInternaciones = data.internaciones;
                    mostrarInternaciones(data.internaciones);
                    actualizarStats(data.stats);
                }
            },
            onError: (err) => {
                console.warn('Error al actualizar datos:', err);
            }
        });
        autoRefresh.start();
    }

    async function cargarInternaciones() {
        // Reiniciar auto-refresh con el nuevo filtro
        if (autoRefresh) {
            autoRefresh.stop();
        }

        try {
            const filtro = document.getElementById('filtro-estado').value;
            let url = '/internacion-staff/api/internaciones';
            if (filtro !== 'todos') {
                url += `?estado=${filtro}`;
            }

            const response = await fetch(url);
            const data = await response.json();

            if (data.success) {
                datosInternaciones = data.internaciones;
                mostrarInternaciones(data.internaciones);
                actualizarStats(data.stats);
            }

            // Reiniciar auto-refresh con el nuevo filtro
            iniciarAutoRefresh();
        } catch (error) {
            console.error('Error al cargar internaciones:', error);
            document.getElementById('tabla-internaciones').innerHTML = `
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-gray-400">
                        <p>Error al cargar pacientes</p>
                    </td>
                </tr>
            `;
        }
    }

    function mostrarInternaciones(internaciones) {
        const tbody = document.getElementById('tabla-internaciones');

        if (internaciones.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-gray-400">
                        <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p>No hay pacientes en internación</p>
                    </td>
                </tr>
            `;
            return;
        }

        tbody.innerHTML = internaciones.map(int => {
            const estadoClass = {
                'activo': 'bg-blue-100 text-blue-800',
                'en_observacion': 'bg-yellow-100 text-yellow-800',
                'estable': 'bg-green-100 text-green-800',
                'critico': 'bg-red-100 text-red-800',
                'alta': 'bg-gray-100 text-gray-800',
                'trasladado': 'bg-purple-100 text-purple-800'
            }[int.estado] || 'bg-gray-100 text-gray-800';

            const estadoLabel = {
                'activo': 'Activo',
                'en_observacion': 'En Observación',
                'estable': 'Estable',
                'critico': 'Crítico',
                'alta': 'Alta',
                'trasladado': 'Trasladado'
            }[int.estado] || int.estado;

            return `
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4">
                        <div class="font-medium text-gray-900">${int.paciente_nombre}</div>
                        <div class="text-xs text-gray-500">CI: ${int.paciente_id}</div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="${int.habitacion === 'Por asignar' ? 'text-yellow-600 font-medium' : 'text-gray-900'}">
                            ${int.habitacion}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-gray-700">${int.medico}</td>
                    <td class="px-6 py-4">
                        <div class="text-gray-900">${int.fecha_ingreso}</div>
                        <div class="text-xs text-gray-500">${int.hora_ingreso}</div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs font-medium rounded-full ${estadoClass}">
                            ${estadoLabel}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <button onclick="abrirModal('${int.id}')" class="text-blue-600 hover:text-blue-800 font-medium text-sm">
                            Acciones
                        </button>
                    </td>
                </tr>
            `;
        }).join('');
    }

    function actualizarStats(stats) {
        document.getElementById('stat-activos').textContent = stats.activos;
        document.getElementById('stat-espera').textContent = stats.espera;
        document.getElementById('stat-atencion').textContent = stats.atencion;
        document.getElementById('stat-hoy').textContent = stats.hoy;
    }

    function abrirModal(id) {
        internacionSeleccionada = datosInternaciones.find(i => i.id === id);
        if (internacionSeleccionada) {
            document.getElementById('modal-paciente-nombre').textContent = internacionSeleccionada.paciente_nombre;
            document.getElementById('modalAcciones').classList.remove('hidden');

            // Actualizar link de evaluar
            document.getElementById('linkEvaluar').href = `/internacion-staff/evaluar/${internacionSeleccionada.id}`;

            // Actualizar link de historial
            document.getElementById('linkHistorial').href = `/internacion-staff/historial/${internacionSeleccionada.id}`;
        }
    }

    function cerrarModal() {
        document.getElementById('modalAcciones').classList.add('hidden');
        internacionSeleccionada = null;
    }

    async function cambiarEstado(nuevoEstado) {
        if (!internacionSeleccionada) return;

        try {
            const response = await fetch(`/internacion-staff/api/internacion/${internacionSeleccionada.id}/update-status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ status: nuevoEstado })
            });

            const data = await response.json();

            if (data.success) {
                cerrarModal();
                cargarInternaciones();
            } else {
                alert('Error: ' + data.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error al cambiar estado');
        }
    }

    async function derivarAUti() {
        if (!internacionSeleccionada) return;

        if (!confirm('¿Está seguro de enviar este paciente a UTI?')) return;

        try {
            const response = await fetch(`/internacion-staff/api/internacion/${internacionSeleccionada.id}/derivar-uti`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            const data = await response.json();

            if (data.success) {
                alert('Paciente derivado a UTI correctamente. Nro de ingreso: ' + data.admission.nro_ingreso);
                cerrarModal();
                cargarInternaciones();
            } else {
                alert('Error: ' + data.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error al derivar a UTI');
        }
    }

    async function derivarAQuirofano() {
        if (!internacionSeleccionada) return;

        if (!confirm('¿Está seguro de enviar este paciente a Quirófano?')) return;

        try {
            const response = await fetch(`/internacion-staff/api/internacion/${internacionSeleccionada.id}/derivar-quirofano`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            const data = await response.json();

            if (data.success) {
                alert('Paciente derivado a Quirófano correctamente. Nro de cirugía: ' + data.cirugia.nro_cirugia);
                cerrarModal();
                cargarInternaciones();
            } else {
                console.error('Error completo al derivar:', data);
                alert('Error: ' + data.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error al derivar a Quirófano');
        }
    }

    async function darAlta() {
        if (!internacionSeleccionada) return;

        const motivo = prompt('Ingrese el motivo del alta (opcional):');
        if (motivo === null) return; // Usuario canceló

        try {
            const response = await fetch(`/internacion-staff/api/internacion/${internacionSeleccionada.id}/alta`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ motivo_alta: motivo })
            });

            const data = await response.json();

            if (data.success) {
                alert('Paciente dado de alta correctamente');
                cerrarModal();
                cargarInternaciones();
            } else {
                alert('Error: ' + data.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error al dar alta');
        }
    }

    // Cerrar modal al hacer clic fuera
    document.getElementById('modalAcciones').addEventListener('click', function(e) {
        if (e.target === this) {
            cerrarModal();
        }
    });

    // Cerrar modal con tecla ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !document.getElementById('modalAcciones').classList.contains('hidden')) {
            cerrarModal();
        }
    });

</script>
@endsection
