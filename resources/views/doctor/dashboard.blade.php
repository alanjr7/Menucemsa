@extends('layouts.app')

@section('content')
<div class="p-6 bg-gray-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Panel Médico</h1>
                        <p class="text-sm text-gray-500 mt-1">
                            Dr. {{ Auth::user()->name }} - {{ $medico->especialidad->nombre ?? 'Médico General' }}
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-gray-500">Fecha</p>
                        <p class="font-semibold text-gray-900">{{ now()->format('d/m/Y') }}</p>
                    </div>
                </div>
            </div>

            <!-- Estadísticas -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex flex-col items-center justify-center h-28 hover:shadow-md transition">
                    <span class="text-gray-500 text-sm font-medium mb-1">Pendientes</span>
                    <span class="text-3xl font-bold text-orange-500">{{ $consultasPendientes->count() }}</span>
                </div>
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex flex-col items-center justify-center h-28 hover:shadow-md transition">
                    <span class="text-gray-500 text-sm font-medium mb-1">En Atención</span>
                    <span class="text-3xl font-bold text-blue-600">{{ $consultasEnAtencion->count() }}</span>
                </div>
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex flex-col items-center justify-center h-28 hover:shadow-md transition">
                    <span class="text-gray-500 text-sm font-medium mb-1">Completadas</span>
                    <span class="text-3xl font-bold text-green-600">{{ $consultasCompletadas->count() }}</span>
                </div>
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex flex-col items-center justify-center h-28 hover:shadow-md transition">
                    <span class="text-gray-500 text-sm font-medium mb-1">Total del Día</span>
                    <span class="text-3xl font-bold text-gray-800">{{ $consultasPendientes->count() + $consultasEnAtencion->count() + $consultasCompletadas->count() }}</span>
                </div>
            </div>

            <!-- Consultas Pendientes -->
            @if($consultasPendientes->count() > 0)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 mb-6">
                    <div class="px-6 py-4 border-b border-gray-100 bg-orange-50 rounded-t-2xl">
                        <h3 class="text-lg font-bold text-orange-800 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Pacientes en Espera ({{ $consultasPendientes->count() }})
                        </h3>
                    </div>
                    <div class="divide-y divide-gray-100">
                        @foreach($consultasPendientes as $consulta)
                            <div class="p-4 hover:bg-gray-50 transition-colors">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3 mb-2">
                                            <div class="bg-orange-100 text-orange-600 rounded-full w-10 h-10 flex items-center justify-center font-bold">
                                                {{ substr($consulta->paciente->nombre, 0, 1) }}
                                            </div>
                                            <div>
                                                <h4 class="font-semibold text-gray-900">{{ $consulta->paciente->nombre }}</h4>
                                                <p class="text-sm text-gray-500">CI: {{ $consulta->paciente->ci }} • Hora: {{ $consulta->hora }}</p>
                                            </div>
                                        </div>
                                        <div class="ml-13">
                                            <p class="text-sm text-gray-600">
                                                <span class="font-medium">Motivo:</span> {{ $consulta->motivo }}
                                            </p>
                                            @if($consulta->observaciones)
                                                <p class="text-sm text-gray-500 mt-1">
                                                    <span class="font-medium">Obs:</span> {{ $consulta->observaciones }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('consulta.ver', $consulta->codigo) }}" class="px-4 py-2 bg-white border border-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                                            Ver Ficha
                                        </a>
                                        <button data-consulta-nro="{{ $consulta->codigo }}" onclick="handleIniciarConsulta(this)" class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white text-sm font-medium rounded-lg transition-colors">
                                            Iniciar Atención
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Consultas en Atención -->
            @if($consultasEnAtencion->count() > 0)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 mb-6">
                    <div class="px-6 py-4 border-b border-gray-100 bg-blue-50 rounded-t-2xl">
                        <h3 class="text-lg font-bold text-blue-800 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            En Atención ({{ $consultasEnAtencion->count() }})
                        </h3>
                    </div>
                    <div class="divide-y divide-gray-100">
                        @foreach($consultasEnAtencion as $consulta)
                            <div class="p-4 bg-blue-50 border-l-4 border-blue-500">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3 mb-2">
                                            <div class="bg-blue-100 text-blue-600 rounded-full w-10 h-10 flex items-center justify-center font-bold">
                                                {{ substr($consulta->paciente->nombre, 0, 1) }}
                                            </div>
                                            <div>
                                                <h4 class="font-semibold text-gray-900">{{ $consulta->paciente->nombre }}</h4>
                                                <p class="text-sm text-gray-500">CI: {{ $consulta->paciente->ci }} • Hora: {{ $consulta->hora }}</p>
                                            </div>
                                        </div>
                                        <div class="ml-13">
                                            <p class="text-sm text-gray-600">
                                                <span class="font-medium">Motivo:</span> {{ $consulta->motivo }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('consulta.ver', $consulta->codigo) }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                                            Continuar Consulta
                                        </a>
                                        <button data-consulta-nro="{{ $consulta->codigo }}" onclick="handleCompletarConsulta(this)" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors">
                                            Completar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Consultas Completadas -->
            @if($consultasCompletadas->count() > 0)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
                    <div class="px-6 py-4 border-b border-gray-100 bg-green-50 rounded-t-2xl">
                        <h3 class="text-lg font-bold text-green-800 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Consultas Completadas ({{ $consultasCompletadas->count() }})
                        </h3>
                    </div>
                    <div class="divide-y divide-gray-100">
                        @foreach($consultasCompletadas as $consulta)
                            <div class="p-4 opacity-75">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3 mb-2">
                                            <div class="bg-green-100 text-green-600 rounded-full w-10 h-10 flex items-center justify-center font-bold">
                                                {{ substr($consulta->paciente->nombre, 0, 1) }}
                                            </div>
                                            <div>
                                                <h4 class="font-semibold text-gray-900">{{ $consulta->paciente->nombre }}</h4>
                                                <p class="text-sm text-gray-500">CI: {{ $consulta->paciente->ci }} • Hora: {{ $consulta->hora }}</p>
                                            </div>
                                        </div>
                                        <div class="ml-13">
                                            <p class="text-sm text-gray-600">
                                                <span class="font-medium">Motivo:</span> {{ $consulta->motivo }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="px-3 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full">
                                            Completada
                                        </span>
                                        <a href="{{ route('consulta.ver', $consulta->codigo) }}" class="px-4 py-2 bg-white border border-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                                            Ver Detalles
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Historial de Pacientes -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 mb-6">
                <div class="px-6 py-4 border-b border-gray-100 bg-purple-50 rounded-t-2xl">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-bold text-purple-800 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Historial de Pacientes
                        </h3>
                        <a href="{{ route('consulta.pacientes-medicos', $medico->ci) }}" class="text-sm text-purple-600 hover:text-purple-800 font-medium">
                            Ver todos →
                        </a>
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <!-- Pacientes Recientes -->
                        <div class="bg-gray-50 rounded-xl p-4">
                            <h4 class="font-semibold text-gray-800 mb-3">Pacientes Recientes</h4>
                            <div class="space-y-2">
                                @php
                                    $pacientesRecientes = DB::table('consultas')
                                        ->join('pacientes', 'consultas.ci_paciente', '=', 'pacientes.ci')
                                        ->where('consultas.ci_medico', $medico->ci)
                                        ->where('consultas.estado', 'completada')
                                        ->orderBy('consultas.fecha', 'desc')
                                        ->orderBy('consultas.hora', 'desc')
                                        ->limit(5)
                                        ->get();
                                @endphp
                                @forelse($pacientesRecientes as $paciente)
                                    <div class="flex items-center justify-between p-2 bg-white rounded-lg">
                                        <div>
                                            <p class="font-medium text-sm text-gray-900">{{ $paciente->nombre }}</p>
                                            <p class="text-xs text-gray-500">{{ $paciente->ci }}</p>
                                        </div>
                                        <span class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($paciente->fecha)->format('d/m') }}</span>
                                    </div>
                                @empty
                                    <p class="text-sm text-gray-500">No hay pacientes recientes</p>
                                @endforelse
                            </div>
                        </div>
                        
                        <!-- Estadísticas -->
                        <div class="bg-gray-50 rounded-xl p-4">
                            <h4 class="font-semibold text-gray-800 mb-3">Estadísticas</h4>
                            <div class="space-y-2">
                                @php
                                    $totalPacientes = DB::table('consultas')
                                        ->where('ci_medico', $medico->ci)
                                        ->distinct('ci_paciente')
                                        ->count();
                                    $pacientesMes = DB::table('consultas')
                                        ->where('ci_medico', $medico->ci)
                                        ->whereMonth('fecha', now()->month)
                                        ->whereYear('fecha', now()->year)
                                        ->distinct('ci_paciente')
                                        ->count();
                                @endphp
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Total Pacientes:</span>
                                    <span class="font-semibold text-sm">{{ $totalPacientes }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Este Mes:</span>
                                    <span class="font-semibold text-sm">{{ $pacientesMes }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Acciones Rápidas -->
                        <div class="bg-gray-50 rounded-xl p-4">
                            <h4 class="font-semibold text-gray-800 mb-3">Acciones Rápidas</h4>
                            <div class="space-y-2">
                                <a href="{{ route('consulta.historial-medico', $medico->ci) }}" class="block w-full text-center px-3 py-2 bg-purple-100 text-purple-700 rounded-lg hover:bg-purple-200 text-sm font-medium transition-colors">
                                    Ver Historial Completo
                                </a>
                                <a href="{{ route('consulta.pacientes-medicos', $medico->ci) }}" class="block w-full text-center px-3 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 text-sm font-medium transition-colors">
                                    Lista de Pacientes
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mensaje si no hay consultas -->
            @if($consultasPendientes->count() == 0 && $consultasEnAtencion->count() == 0 && $consultasCompletadas->count() == 0)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
                    <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No hay consultas programadas</h3>
                    <p class="text-gray-500">No hay pacientes registrados para hoy o aún no han realizado el pago.</p>
                </div>
            @endif
        </div>
    </div>

    <script>
        function handleIniciarConsulta(button) {
            const consultaId = button.getAttribute('data-consulta-nro');
            iniciarConsulta(consultaId);
        }

        function handleCompletarConsulta(button) {
            const consultaId = button.getAttribute('data-consulta-nro');
            completarConsulta(consultaId);
        }

        function iniciarConsulta(consultaId) {
            if (confirm('¿Está seguro de iniciar esta consulta?')) {
                fetch(`/consulta-externa/iniciar/${consultaId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al iniciar la consulta');
                });
            }
        }

        function completarConsulta(consultaId) {
            if (confirm('¿Está seguro de completar esta consulta?')) {
                const observaciones = prompt('Observaciones de la consulta (opcional):');
                
                fetch(`/consulta-externa/completar/${consultaId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        observaciones: observaciones
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al completar la consulta');
                });
            }
        }
    </script>
@endsection
