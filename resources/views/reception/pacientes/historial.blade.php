@extends('layouts.app')

@section('content')
<div class="w-full p-6 bg-gray-50/50 min-h-screen">

    <div class="flex justify-between items-end mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Historial Clínico Completo</h1>
            <p class="text-sm text-gray-500">Información médica del paciente desde su ingreso hasta el alta</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('reception.pacientes.index') }}" class="flex items-center px-4 py-2 border border-gray-200 rounded-lg text-gray-600 bg-white hover:bg-gray-50 font-medium transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Volver
            </a>
            <a href="{{ route('reception.pacientes.historial.print', $paciente->ci) }}" target="_blank" class="flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Imprimir Historial
            </a>
        </div>
    </div>

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
                    <p class="text-sm text-gray-500">CI: {{ $paciente->ci }} | Sexo: {{ $paciente->sexo }}</p>
                </div>
            </div>
            @php
                $estado = 'Activo';
                $estadoColor = 'green';
                if ($paciente->hospitalizaciones()->where('estado', 'Activo')->exists()) {
                    $estado = 'Hospitalizado';
                    $estadoColor = 'yellow';
                } elseif ($paciente->emergencies()->where('status', '!=', 'alta')->exists()) {
                    $estado = 'En Emergencia';
                    $estadoColor = 'red';
                }
            @endphp
            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-{{ $estadoColor }}-100 text-{{ $estadoColor }}-800 border border-{{ $estadoColor }}-200">
                <span class="w-2 h-2 bg-{{ $estadoColor }}-500 rounded-full mr-2 {{ $estado === 'En Emergencia' ? 'animate-pulse' : '' }}"></span>
                {{ $estado }}
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div>
                <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-2">Información de Contacto</h3>
                <dl class="space-y-1">
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">Teléfono:</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $paciente->telefono ?? 'No registrado' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">Dirección:</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $paciente->direccion ?? 'No registrada' }}</dd>
                    </div>
                </dl>
            </div>
            <div>
                <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-2">Información Médica</h3>
                <dl class="space-y-1">
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">Seguro:</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $paciente->seguro->nombre_empresa ?? 'Particular' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">Triage:</dt>
                        <dd class="text-sm font-medium text-gray-900">
                            @if($paciente->triage)
                                Nivel {{ $paciente->triage->nivel }} - {{ $paciente->triage->categoria }}
                            @else
                                No asignado
                            @endif
                        </dd>
                    </div>
                </dl>
            </div>
            <div>
                <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-2">Registro</h3>
                <dl class="space-y-1">
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">Registrado por:</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $paciente->registro->user->name ?? '-' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">Fecha:</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $paciente->registro->fecha?->format('d/m/Y H:i') ?? '-' }}</dd>
                    </div>
                </dl>
            </div>
            <div>
                <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-2">Resumen</h3>
                <dl class="space-y-1">
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">Consultas:</dt>
                        <dd class="text-sm font-medium text-blue-600">{{ $paciente->consultas->count() }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">Emergencias:</dt>
                        <dd class="text-sm font-medium text-red-600">{{ $paciente->emergencies->count() }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">Hospitalizaciones:</dt>
                        <dd class="text-sm font-medium text-yellow-600">{{ $paciente->hospitalizaciones->count() }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">Cirugías:</dt>
                        <dd class="text-sm font-medium text-purple-600">{{ $cirugiasHistorial->count() }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>

    <div class="space-y-6" x-data="{ activeTab: 'consultas' }">

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
            <div class="border-b border-gray-200">
                <nav class="flex -mb-px">
                    <button @click="activeTab = 'consultas'" :class="activeTab === 'consultas' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700'" class="px-6 py-3 border-b-2 font-medium text-sm transition-colors">
                        Consultas Externas ({{ $paciente->consultas->count() }})
                    </button>
                    <button @click="activeTab = 'emergencias'" :class="activeTab === 'emergencias' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700'" class="px-6 py-3 border-b-2 font-medium text-sm transition-colors">
                        Emergencias ({{ $paciente->emergencies->count() }})
                    </button>
                    <button @click="activeTab = 'hospitalizaciones'" :class="activeTab === 'hospitalizaciones' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700'" class="px-6 py-3 border-b-2 font-medium text-sm transition-colors">
                        Hospitalizaciones ({{ $paciente->hospitalizaciones->count() }})
                    </button>
                    <button @click="activeTab = 'uti'" :class="activeTab === 'uti' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700'" class="px-6 py-3 border-b-2 font-medium text-sm transition-colors">
                        UTI ({{ $utiHistorial->count() }})
                    </button>
                    <button @click="activeTab = 'cirugias'" :class="activeTab === 'cirugias' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700'" class="px-6 py-3 border-b-2 font-medium text-sm transition-colors">
                        Cirugías ({{ $cirugiasHistorial->count() }})
                    </button>
                </nav>
            </div>

            <div class="p-6">

                <div x-show="activeTab === 'consultas'" x-cloak>
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Historial de Consultas Externas</h3>
                    @if($paciente->consultas->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Médico</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Especialidad</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Motivo</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Diagnóstico</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado Pago</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($paciente->consultas as $consulta)
                                        <tr>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $consulta->fecha?->format('d/m/Y H:i') ?? '-' }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $consulta->medico->usuario->name ?? '-' }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $consulta->especialidad->nombre ?? '-' }}</td>
                                            <td class="px-4 py-3 text-sm text-gray-900">{{ $consulta->motivo ?? 'No registrado' }}</td>
                                            <td class="px-4 py-3 text-sm text-gray-900">{{ $consulta->diagnostico ?? 'Sin diagnóstico' }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                @if($consulta->caja && $consulta->caja->nro_factura)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Pagado</span>
                                                @else
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">Pendiente</span>
                                                @endif
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

                <div x-show="activeTab === 'emergencias'" x-cloak>
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Historial de Emergencias</h3>
                    @if($paciente->emergencies->count() > 0)
                        <div class="space-y-4">
                            @foreach($paciente->emergencies as $emergencia)
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <div class="flex justify-between items-start mb-2">
                                        <div>
                                            <span class="text-sm font-medium text-gray-900">Código: {{ $emergencia->code }}</span>
                                            <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $emergencia->status !== 'alta' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                                {{ ucfirst(str_replace('_', ' ', $emergencia->status)) }}
                                            </span>
                                        </div>
                                        <span class="text-sm text-gray-500">{{ $emergencia->admission_date?->format('d/m/Y H:i') ?? '-' }}</span>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                        <div>
                                            <span class="text-gray-500">Motivo:</span>
                                            <span class="text-gray-900 ml-1">{{ $emergencia->symptoms ?? $emergencia->initial_assessment ?? 'No registrado' }}</span>
                                        </div>
                                        <div>
                                            <span class="text-gray-500">Atendido por:</span>
                                            <span class="text-gray-900 ml-1">{{ $emergencia->user->name ?? '-' }}</span>
                                        </div>
                                        @if($emergencia->discharge_date)
                                            <div>
                                                <span class="text-gray-500">Fecha de Alta:</span>
                                                <span class="text-gray-900 ml-1">{{ $emergencia->discharge_date?->format('d/m/Y H:i') }}</span>
                                            </div>
                                        @endif
                                        @if($emergencia->ubicacion_actual)
                                            <div>
                                                <span class="text-gray-500">Ubicación Final:</span>
                                                <span class="text-gray-900 ml-1">{{ ucfirst($emergencia->ubicacion_actual) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
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

                <div x-show="activeTab === 'hospitalizaciones'" x-cloak>
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Historial de Hospitalizaciones</h3>
                    @if($paciente->hospitalizaciones->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha Ingreso</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha Alta</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Servicio</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Médico</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($paciente->hospitalizaciones as $hospitalizacion)
                                        <tr>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $hospitalizacion->fecha_ingreso?->format('d/m/Y H:i') ?? '-' }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $hospitalizacion->fecha_alta?->format('d/m/Y H:i') ?? 'Activo' }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $hospitalizacion->servicio->nombre ?? '-' }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">{{ $hospitalizacion->medico->usuario->name ?? '-' }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $hospitalizacion->estado === 'Activo' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800' }}">
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

                <div x-show="activeTab === 'uti'" x-cloak>
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Historial de UTI</h3>
                    @if($utiHistorial->count() > 0)
                        <div class="space-y-4">
                            @foreach($utiHistorial as $uti)
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <div class="flex justify-between items-start mb-2">
                                        <div>
                                            <span class="text-sm font-medium text-gray-900">Nro. Ingreso: {{ $uti->nro_ingreso }}</span>
                                            <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $uti->estado === 'activo' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800' }}">
                                                {{ ucfirst($uti->estado) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                                        <div>
                                            <span class="text-gray-500">Fecha Ingreso:</span>
                                            <span class="text-gray-900 ml-1">{{ $uti->fecha_ingreso?->format('d/m/Y H:i') ?? '-' }}</span>
                                        </div>
                                        <div>
                                            <span class="text-gray-500">Fecha Alta:</span>
                                            <span class="text-gray-900 ml-1">{{ $uti->fecha_alta?->format('d/m/Y H:i') ?? 'Sin alta' }}</span>
                                        </div>
                                        <div>
                                            <span class="text-gray-500">Cama:</span>
                                            <span class="text-gray-900 ml-1">{{ $uti->bed->bed_number ?? '-' }}</span>
                                        </div>
                                        <div class="md:col-span-3">
                                            <span class="text-gray-500">Diagnóstico Principal:</span>
                                            <span class="text-gray-900 ml-1">{{ $uti->diagnostico_principal ?? 'No registrado' }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                            <p class="mt-2 text-sm text-gray-500">No hay registros de UTI</p>
                        </div>
                    @endif
                </div>

                <div x-show="activeTab === 'cirugias'" x-cloak>
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Historial de Cirugías</h3>
                    @if($cirugiasHistorial->count() > 0)
                        <div class="space-y-4">
                            @foreach($cirugiasHistorial as $cirugia)
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <div class="flex justify-between items-start mb-2">
                                        <div>
                                            <span class="text-sm font-medium text-gray-900">Cirugía: {{ $cirugia->tipo_cirugia ?? 'No especificado' }}</span>
                                            <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $cirugia->estado === 'completada' ? 'bg-green-100 text-green-800' : ($cirugia->estado === 'en_curso' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                                                {{ ucfirst(str_replace('_', ' ', $cirugia->estado ?? 'pendiente')) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                                        <div>
                                            <span class="text-gray-500">Fecha:</span>
                                            <span class="text-gray-900 ml-1">{{ $cirugia->fecha_cirugia?->format('d/m/Y H:i') ?? '-' }}</span>
                                        </div>
                                        <div>
                                            <span class="text-gray-500">Cirujano:</span>
                                            <span class="text-gray-900 ml-1">{{ $cirugia->medico->usuario->name ?? '-' }}</span>
                                        </div>
                                        <div>
                                            <span class="text-gray-500">Quirófano:</span>
                                            <span class="text-gray-900 ml-1">{{ $cirugia->quirofano->nombre ?? '-' }}</span>
                                        </div>
                                        <div class="md:col-span-3">
                                            <span class="text-gray-500">Procedimiento:</span>
                                            <span class="text-gray-900 ml-1">{{ $cirugia->procedimiento ?? 'No registrado' }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.121 14.121L19 19m-7-7l7-7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                            <p class="mt-2 text-sm text-gray-500">No hay cirugías registradas</p>
                        </div>
                    @endif
                </div>

            </div>
        </div>

    </div>
</div>
@endsection
