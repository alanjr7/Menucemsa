@extends('layouts.app')

@section('content')
<div class="w-full p-6 bg-gray-50/50 min-h-screen">

    <!-- Page Header -->
    <div class="flex justify-between items-end mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Detalles del Paciente</h1>
            <p class="text-sm text-gray-500">Información completa del paciente</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('patients.index') }}" class="flex items-center px-4 py-2 border border-gray-200 rounded-lg text-gray-600 bg-white hover:bg-gray-50 font-medium transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Volver
            </a>
            <button onclick="window.print()" class="flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Imprimir
            </button>
        </div>
    </div>

    <!-- Patient Info Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
        <div class="flex items-start justify-between mb-6">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-800">{{ $paciente->nombre }}</h2>
                    <p class="text-sm text-gray-500">CI: {{ $paciente->ci }} | Código: {{ $paciente->codigo_registro }}</p>
                </div>
            </div>
            @php
                $estado = 'Activo';
                $estadoColor = 'green';
                if ($paciente->hospitalizaciones()->where('estado', 'Activo')->exists()) {
                    $estado = 'Hospitalizado';
                    $estadoColor = 'yellow';
                } elseif ($paciente->emergencies()->where('status', '!=', 'alta')->exists()) {
                    $estado = 'Emergencia';
                    $estadoColor = 'red';
                }
            @endphp
            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-{{ $estadoColor }}-100 text-{{ $estadoColor }}-800 border border-{{ $estadoColor }}-200">
                <span class="w-2 h-2 bg-{{ $estadoColor }}-500 rounded-full mr-2 {{ $estado === 'Emergencia' ? 'animate-pulse' : '' }}"></span>
                {{ $estado }}
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-3">Información Personal</h3>
                <dl class="space-y-2">
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">Nombre:</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $paciente->nombre }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">CI:</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $paciente->ci }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">Teléfono:</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $paciente->telefono ?? 'No registrado' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">Sexo:</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $paciente->sexo }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">Correo:</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $paciente->correo ?? 'No registrado' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">Dirección:</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $paciente->direccion ?? 'No registrada' }}</dd>
                    </div>
                </dl>
            </div>

            <div>
                <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-3">Información Médica</h3>
                <dl class="space-y-2">
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">Seguro:</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $paciente->seguro->nombre ?? 'Particular' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">Triage:</dt>
                        <dd class="text-sm font-medium text-gray-900">
                            @if($paciente->triage)
                                {{ $paciente->triage->prioridad }} - {{ $paciente->triage->descripcion }}
                            @else
                                No asignado
                            @endif
                        </dd>
                    </div>
                    @php
                // Temporalmente deshabilitado hasta que la tabla exista
                $alergias = 'No registradas';
                $tipoSangre = 'No registrado';
            @endphp
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">Tipo Sangre:</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $tipoSangre }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">Alergias:</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $alergias }}</dd>
                    </div>
                </dl>
            </div>

            <div>
                <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-3">Información de Registro</h3>
                <dl class="space-y-2">
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">Código Registro:</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $paciente->registro_codigo }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">Registrado por:</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $paciente->registro->user->name ?? '-' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">Fecha Registro:</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $paciente->registro->fecha?->format('d/m/Y H:i') ?? '-' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">Motivo:</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $paciente->registro->motivo ?? '-' }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>

    <!-- Tabs for different sections -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px">
                <button onclick="showTab('consultas')" class="tab-btn px-6 py-3 border-b-2 border-blue-500 text-blue-600 font-medium text-sm">
                    Consultas
                </button>
                <button onclick="showTab('emergencies')" class="tab-btn px-6 py-3 border-b-2 border-transparent text-gray-500 hover:text-gray-700 font-medium text-sm">
                    Emergencias
                </button>
                <button onclick="showTab('hospitalizaciones')" class="tab-btn px-6 py-3 border-b-2 border-transparent text-gray-500 hover:text-gray-700 font-medium text-sm">
                    Hospitalizaciones
                </button>
            </nav>
        </div>

        <!-- Consultas Tab -->
        <div id="consultas-tab" class="tab-content p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Historial de Consultas</h3>
            @if($paciente->consultas->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Médico</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Especialidad</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado Pago</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($paciente->consultas as $consulta)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $consulta->fecha->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $consulta->medico->usuario->name ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $consulta->especialidad->nombre ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($consulta->caja && $consulta->caja->nro_factura)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Pagado
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                Pendiente
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <button class="text-blue-600 hover:text-blue-900">Ver detalles</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <p class="mt-2 text-sm text-gray-500">No hay consultas registradas</p>
                </div>
            @endif
        </div>

        <!-- Emergencies Tab -->
        <div id="emergencies-tab" class="tab-content p-6 hidden">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Historial de Emergencias</h3>
            @if($paciente->emergencies->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Médico</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Motivo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($paciente->emergencies as $emergencia)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $emergencia->admission_date->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $emergencia->user->name ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        {{ $emergencia->symptoms ?? $emergencia->initial_assessment ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $emergencia->status !== 'alta' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800' }}">
                                            {{ $emergencia->status }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"/>
                    </svg>
                    <p class="mt-2 text-sm text-gray-500">No hay emergencias registradas</p>
                </div>
            @endif
        </div>

        <!-- Hospitalizaciones Tab -->
        <div id="hospitalizaciones-tab" class="tab-content p-6 hidden">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Historial de Hospitalizaciones</h3>
            @if($paciente->hospitalizaciones->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ingreso</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alta</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Servicio</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($paciente->hospitalizaciones as $hospitalizacion)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $hospitalizacion->fecha_ingreso->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $hospitalizacion->fecha_alta?->format('d/m/Y H:i') ?? 'Activo' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $hospitalizacion->servicio->nombre ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $hospitalizacion->estado === 'Activo' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' }}">
                                            {{ $hospitalizacion->estado }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    <p class="mt-2 text-sm text-gray-500">No hay hospitalizaciones registradas</p>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function showTab(tabName) {
    // Hide all tabs
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.add('hidden');
    });
    
    // Remove active state from all buttons
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('border-blue-500', 'text-blue-600');
        btn.classList.add('border-transparent', 'text-gray-500');
    });
    
    // Show selected tab
    document.getElementById(tabName + '-tab').classList.remove('hidden');
    
    // Add active state to clicked button
    event.target.classList.remove('border-transparent', 'text-gray-500');
    event.target.classList.add('border-blue-500', 'text-blue-600');
}
</script>
@endsection
