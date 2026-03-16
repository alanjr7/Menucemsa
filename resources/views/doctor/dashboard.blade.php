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
                                        <a href="{{ route('consulta.ver', $consulta->nro) }}" class="px-4 py-2 bg-white border border-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                                            Ver Ficha
                                        </a>
                                        <button data-consulta-nro="{{ $consulta->nro }}" onclick="handleIniciarConsulta(this)" class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white text-sm font-medium rounded-lg transition-colors">
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
                                        <a href="{{ route('consulta.ver', $consulta->nro) }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                                            Continuar Consulta
                                        </a>
                                        <button data-consulta-nro="{{ $consulta->nro }}" onclick="handleCompletarConsulta(this)" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors">
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
                                        <a href="{{ route('consulta.ver', $consulta->nro) }}" class="px-4 py-2 bg-white border border-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                                            Ver Detalles
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

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
                fetch(`/consulta-externa/completar/${consultaId}`, {
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
                    alert('Error al completar la consulta');
                });
            }
        }
    </script>
@endsection
