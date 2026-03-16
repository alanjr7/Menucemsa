@extends('layouts.app')

@section('content')
<div class="w-full p-6 bg-gray-50/50 min-h-screen">

    <!-- Page Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Control Total de Consulta Externa</h1>
            <p class="text-sm text-gray-500">Supervisión de todos los médicos y pacientes del sistema</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-2 border border-gray-300 rounded-lg text-gray-600 bg-white hover:bg-gray-50 font-medium transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Volver al Dashboard
            </a>
            <button onclick="window.print()" class="flex items-center px-4 py-2 border border-gray-200 rounded-lg text-gray-600 bg-white hover:bg-gray-50 font-medium transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Imprimir Reporte
            </button>
        </div>
    </div>

    <!-- Estadísticas Generales -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Médicos</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['totalMedicos'] }}</p>
                    <p class="text-xs text-gray-400 mt-1">{{ $stats['medicosActivos'] }} activos hoy</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Consultas Hoy</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $stats['totalConsultasHoy'] }}</p>
                    <p class="text-xs text-gray-400 mt-1">{{ $stats['pacientesUnicosHoy'] }} pacientes únicos</p>
                </div>
                <div class="w-12 h-12 bg-amber-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">En Atención</p>
                    <p class="text-2xl font-bold text-orange-600">{{ $stats['consultasEnAtencion'] }}</p>
                    <p class="text-xs text-gray-400 mt-1">{{ $stats['consultasPendientes'] }} pendientes</p>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Completadas</p>
                    <p class="text-2xl font-bold text-green-600">{{ $stats['consultasCompletadas'] }}</p>
                    <p class="text-xs text-gray-400 mt-1">{{ round(($stats['consultasCompletadas'] / max($stats['totalConsultasHoy'], 1)) * 100, 1) }}% completado</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-6m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Médicos y sus Consultas -->
    <div class="space-y-6">
        @forelse($medicos as $medico)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <!-- Header del Médico -->
                <div class="p-6 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-gray-200">
                    <div class="flex justify-between items-start">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold text-lg">
                                {{ substr($medico->usuario->name, 0, 1) }}{{ substr(explode(' ', $medico->usuario->name)[1] ?? '', 0, 1) }}
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-800">{{ $medico->usuario->name }}</h3>
                                <p class="text-sm text-gray-600">{{ $medico->especialidad->nombre ?? 'Sin especialidad' }}</p>
                                <p class="text-xs text-gray-500">CI: {{ $medico->ci }}</p>
                            </div>
                        </div>
                        <div class="flex gap-3 text-center">
                            <div class="bg-white rounded-lg px-3 py-2 border border-gray-200">
                                <div class="text-lg font-bold text-blue-600">{{ $consultasPorMedico[$medico->ci]['totalConsultas'] }}</div>
                                <div class="text-xs text-gray-500">Total</div>
                            </div>
                            <div class="bg-white rounded-lg px-3 py-2 border border-gray-200">
                                <div class="text-lg font-bold text-orange-600">{{ $consultasPorMedico[$medico->ci]['consultasEnAtencion']->count() }}</div>
                                <div class="text-xs text-gray-500">En atención</div>
                            </div>
                            <div class="bg-white rounded-lg px-3 py-2 border border-gray-200">
                                <div class="text-lg font-bold text-green-600">{{ $consultasPorMedico[$medico->ci]['consultasCompletadas']->count() }}</div>
                                <div class="text-xs text-gray-500">Completadas</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Consultas del Médico -->
                <div class="p-6">
                    @if($consultasPorMedico[$medico->ci]['totalConsultas'] > 0)
                        <div class="space-y-4">
                            <!-- Consultas Pendientes -->
                            @if($consultasPorMedico[$medico->ci]['consultasPendientes']->count() > 0)
                                <div>
                                    <h4 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                                        <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                                        Consultas Pendientes ({{ $consultasPorMedico[$medico->ci]['consultasPendientes']->count() }})
                                    </h4>
                                    <div class="space-y-2">
                                        @foreach($consultasPorMedico[$medico->ci]['consultasPendientes'] as $consulta)
                                            <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg border border-blue-200">
                                                <div class="flex items-center gap-3">
                                                    <div class="text-blue-600 font-bold text-sm">{{ \Carbon\Carbon::parse($consulta->hora)->format('H:i') }}</div>
                                                    <div>
                                                        <div class="font-medium text-gray-800">{{ $consulta->paciente->nombre }}</div>
                                                        <div class="text-xs text-gray-500">CI: {{ $consulta->paciente->ci }}</div>
                                                        <div class="text-sm text-gray-600 mt-1">{{ $consulta->motivo }}</div>
                                                    </div>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <span class="text-xs font-bold text-blue-600 bg-blue-100 px-2 py-1 rounded">Pagado</span>
                                                    <button data-consulta-nro="{{ $consulta->nro }}" onclick="handleVerDetallesConsulta(this)" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                                        Ver →
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Consultas en Atención -->
                            @if($consultasPorMedico[$medico->ci]['consultasEnAtencion']->count() > 0)
                                <div>
                                    <h4 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                                        <span class="w-2 h-2 bg-orange-500 rounded-full animate-pulse"></span>
                                        En Atención ({{ $consultasPorMedico[$medico->ci]['consultasEnAtencion']->count() }})
                                    </h4>
                                    <div class="space-y-2">
                                        @foreach($consultasPorMedico[$medico->ci]['consultasEnAtencion'] as $consulta)
                                            <div class="flex items-center justify-between p-3 bg-orange-50 rounded-lg border border-orange-200">
                                                <div class="flex items-center gap-3">
                                                    <div class="text-orange-600 font-bold text-sm">{{ \Carbon\Carbon::parse($consulta->hora)->format('H:i') }}</div>
                                                    <div>
                                                        <div class="font-medium text-gray-800">{{ $consulta->paciente->nombre }}</div>
                                                        <div class="text-xs text-gray-500">CI: {{ $consulta->paciente->ci }}</div>
                                                        <div class="text-sm text-gray-600 mt-1">{{ $consulta->motivo }}</div>
                                                    </div>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <span class="px-2 py-1 bg-orange-100 text-orange-700 text-xs font-bold rounded-full">● En atención</span>
                                                    <button data-consulta-nro="{{ $consulta->nro }}" onclick="handleVerDetallesConsulta(this)" class="text-orange-600 hover:text-orange-800 text-sm font-medium">
                                                        Ver →
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Consultas Completadas -->
                            @if($consultasPorMedico[$medico->ci]['consultasCompletadas']->count() > 0)
                                <div>
                                    <h4 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                                        <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                                        Completadas ({{ $consultasPorMedico[$medico->ci]['consultasCompletadas']->count() }})
                                    </h4>
                                    <div class="space-y-2">
                                        @foreach($consultasPorMedico[$medico->ci]['consultasCompletadas'] as $consulta)
                                            <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg border border-green-200">
                                                <div class="flex items-center gap-3">
                                                    <div class="text-green-600 font-bold text-sm">{{ \Carbon\Carbon::parse($consulta->hora)->format('H:i') }}</div>
                                                    <div>
                                                        <div class="font-medium text-gray-800">{{ $consulta->paciente->nombre }}</div>
                                                        <div class="text-xs text-gray-500">CI: {{ $consulta->paciente->ci }}</div>
                                                        <div class="text-sm text-gray-600 mt-1">{{ $consulta->motivo }}</div>
                                                    </div>
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full flex items-center gap-1">
                                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/></svg>
                                                        Completado
                                                    </span>
                                                    <button data-consulta-nro="{{ $consulta->nro }}" onclick="handleVerDetallesConsulta(this)" class="text-green-600 hover:text-green-800 text-sm font-medium">
                                                        Ver →
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-400">
                            <svg class="w-12 h-12 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            <p class="text-sm font-medium">Sin consultas hoy</p>
                            <p class="text-xs mt-1">Este médico no tiene consultas programadas para hoy</p>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 text-center">
                <div class="text-gray-400">
                    <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <p class="text-lg font-medium">No hay médicos registrados</p>
                    <p class="text-sm mt-1">No se encontraron médicos en el sistema</p>
                </div>
            </div>
        @endforelse
    </div>
</div>

<script>
function handleVerDetallesConsulta(button) {
    const consultaId = button.getAttribute('data-consulta-nro');
    verDetallesConsulta(consultaId);
}

function verDetallesConsulta(consultaId) {
    // Redirigir a la vista de detalles de la consulta
    window.open(`/consulta/${consultaId}`, '_blank');
}

// Actualizar cada 30 segundos para mostrar cambios en tiempo real
setInterval(() => {
    location.reload();
}, 30000);
</script>
@endsection
