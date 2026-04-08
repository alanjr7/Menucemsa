@extends('layouts.app')

@section('title', 'Detalles de Emergencia')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Emergencia {{ $emergency->code }}</h1>
            <p class="text-sm text-slate-500 mt-1">Detalles del caso de emergencia</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('emergency-staff.edit', $emergency) }}" class="inline-flex items-center px-4 py-2 bg-amber-500 text-white rounded-lg hover:bg-amber-600 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Editar
            </a>
            <a href="{{ route('quirofano.index') }}" class="inline-flex items-center px-4 py-2 bg-slate-200 text-slate-700 rounded-lg hover:bg-slate-300 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Volver a Quirófano
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Columna Principal -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Información del Paciente -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-6 py-4 bg-slate-50 border-b border-slate-200">
                    <h2 class="font-semibold text-slate-800 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Información del Paciente
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-3">
                            @if($emergency->is_temp_id)
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-medium text-slate-600">Nombre:</span>
                                    <span class="px-2 py-1 bg-amber-100 text-amber-700 text-xs font-semibold rounded-full">Paciente Temporal</span>
                                </div>
                                <p class="text-sm"><span class="font-medium text-slate-600">ID Temporal:</span> <span class="text-slate-800">{{ $emergency->temp_id ?? 'N/A' }}</span></p>
                            @elseif($emergency->paciente)
                                <p class="text-sm"><span class="font-medium text-slate-600">Nombre:</span> <span class="text-slate-800 font-semibold">{{ $emergency->paciente->nombre }}</span></p>
                                <p class="text-sm"><span class="font-medium text-slate-600">CI:</span> <span class="text-slate-800">{{ $emergency->paciente->ci }}</span></p>
                                <p class="text-sm"><span class="font-medium text-slate-600">Teléfono:</span> <span class="text-slate-800">{{ $emergency->paciente->telefono ?? 'No registrado' }}</span></p>
                            @else
                                <p class="text-sm"><span class="font-medium text-slate-600">Nombre:</span> <span class="text-slate-500">Paciente no encontrado (ID: {{ $emergency->patient_id }})</span></p>
                            @endif
                        </div>
                        <div class="space-y-3">
                            @if($emergency->paciente && !$emergency->is_temp_id)
                                <p class="text-sm"><span class="font-medium text-slate-600">Edad:</span> <span class="text-slate-800">{{ $emergency->paciente->edad ?? 'No registrado' }}</span></p>
                                <p class="text-sm"><span class="font-medium text-slate-600">Sexo:</span> <span class="text-slate-800">{{ $emergency->paciente->sexo ?? 'No registrado' }}</span></p>
                                <p class="text-sm"><span class="font-medium text-slate-600">Dirección:</span> <span class="text-slate-800">{{ $emergency->paciente->direccion ?? 'No registrado' }}</span></p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información Médica -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-6 py-4 bg-slate-50 border-b border-slate-200">
                    <h2 class="font-semibold text-slate-800 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                        Información Médica
                    </h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-3">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-medium text-slate-600">Estado Actual:</span>
                                <span class="px-2 py-1 bg-{{ $emergency->status_color }}-100 text-{{ $emergency->status_color }}-700 text-xs font-semibold rounded-full">
                                    {{ ucfirst($emergency->status) }}
                                </span>
                            </div>
                            <p class="text-sm"><span class="font-medium text-slate-600">Personal a Cargo:</span> <span class="text-slate-800">{{ $emergency->user->name ?? 'No asignado' }}</span></p>
                            <p class="text-sm"><span class="font-medium text-slate-600">Fecha Ingreso:</span> <span class="text-slate-800">{{ $emergency->admission_date ? $emergency->admission_date->format('d/m/Y H:i') : '-' }}</span></p>
                            @if($emergency->discharge_date)
                            <p class="text-sm"><span class="font-medium text-slate-600">Fecha Alta:</span> <span class="text-slate-800">{{ $emergency->discharge_date->format('d/m/Y H:i') }}</span></p>
                            @endif
                        </div>
                        <div class="space-y-3">
                            <p class="text-sm"><span class="font-medium text-slate-600">Destino:</span> <span class="text-slate-800">{{ ucfirst($emergency->destination ?? 'No definido') }}</span></p>
                            <p class="text-sm"><span class="font-medium text-slate-600">Costo:</span> <span class="text-slate-800 font-semibold">S/ {{ number_format($emergency->cost, 2) }}</span></p>
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-medium text-slate-600">Estado de Pago:</span>
                                @if($emergency->paid)
                                    <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-full">Pagado</span>
                                @else
                                    <span class="px-2 py-1 bg-red-100 text-red-700 text-xs font-semibold rounded-full">Pendiente</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detalles Clínicos -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-6 py-4 bg-slate-50 border-b border-slate-200">
                    <h2 class="font-semibold text-slate-800 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Detalles Clínicos
                    </h2>
                </div>
                <div class="p-6 space-y-6">
                    @if($emergency->symptoms)
                    <div class="border-l-4 border-red-400 pl-4">
                        <h3 class="text-sm font-bold text-slate-800 mb-2 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            Síntomas
                        </h3>
                        <div class="bg-red-50 border border-red-100 rounded-lg p-3">
                            <p class="text-sm text-slate-700 font-medium">{{ $emergency->symptoms }}</p>
                        </div>
                    </div>
                    @endif

                    @if($emergency->initial_assessment)
                    <div class="border-l-4 border-blue-400 pl-4">
                        <h3 class="text-sm font-bold text-slate-800 mb-2 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            Valoración Inicial
                        </h3>
                        <div class="bg-blue-50 border border-blue-100 rounded-lg p-3">
                            <p class="text-sm text-slate-700 font-medium">{{ $emergency->initial_assessment }}</p>
                        </div>
                    </div>
                    @endif

                    @if($emergency->vital_signs)
                    <div class="border-l-4 border-green-400 pl-4">
                        <h3 class="text-sm font-bold text-slate-800 mb-3 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                            Signos Vitales
                        </h3>
                        @php
                            $vitalSigns = is_array($emergency->vital_signs) ? $emergency->vital_signs : json_decode($emergency->vital_signs, true);
                        @endphp
                        @if(is_array($vitalSigns))
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                                @foreach($vitalSigns as $key => $value)
                                    @if($key !== 'fecha_registro' && $value)
                                        <div class="bg-green-50 border border-green-100 rounded-lg p-3">
                                            <p class="text-xs text-green-700 uppercase tracking-wider font-semibold">
                                                {{ match($key) {
                                                    'presion_arterial' => 'Presión Arterial',
                                                    'frecuencia_cardiaca' => 'Frec. Cardiaca',
                                                    'frecuencia_respiratoria' => 'Frec. Respiratoria',
                                                    'temperatura' => 'Temperatura',
                                                    'saturacion_o2' => 'Sat. O₂',
                                                    'glucosa' => 'Glucosa',
                                                    default => str_replace('_', ' ', $key)
                                                } }}
                                            </p>
                                            <p class="text-lg font-bold text-slate-800 mt-1">{{ $value }}</p>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                            @if(isset($vitalSigns['fecha_registro']))
                                <p class="text-xs text-slate-500 mt-2">Registrado: {{ $vitalSigns['fecha_registro'] }}</p>
                            @endif
                        @else
                            <div class="bg-green-50 border border-green-100 rounded-lg p-3">
                                <p class="text-sm text-slate-700">{{ $emergency->vital_signs }}</p>
                            </div>
                        @endif
                    </div>
                    @endif

                    @if($emergency->treatment)
                    <div class="border-l-4 border-purple-400 pl-4">
                        <h3 class="text-sm font-bold text-slate-800 mb-2 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                            </svg>
                            Tratamiento Aplicado
                        </h3>
                        <div class="bg-purple-50 border border-purple-100 rounded-lg p-3">
                            <p class="text-sm text-slate-700 font-medium">{{ $emergency->treatment }}</p>
                        </div>
                    </div>
                    @endif

                    @if($emergency->observations)
                    <div class="border-l-4 border-amber-400 pl-4">
                        <h3 class="text-sm font-bold text-slate-800 mb-2 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Observaciones
                        </h3>
                        <div class="bg-amber-50 border border-amber-100 rounded-lg p-3">
                            <p class="text-sm text-slate-700 font-medium whitespace-pre-line">{{ $emergency->observations }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Acciones Rápidas -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-6 py-4 bg-slate-50 border-b border-slate-200">
                    <h2 class="font-semibold text-slate-800">Acciones Rápidas</h2>
                </div>
                <div class="p-6 space-y-3">
                    <a href="{{ route('emergency-staff.edit', $emergency) }}" class="w-full inline-flex items-center justify-center px-4 py-2 bg-amber-500 text-white rounded-lg hover:bg-amber-600 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Editar Información
                    </a>

                    @if($emergency->status !== 'alta')
                    <button type="button" onclick="updateStatus('alta')" class="w-full inline-flex items-center justify-center px-4 py-2 bg-cyan-600 text-white rounded-lg hover:bg-cyan-700 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Dar de Alta
                    </button>
                    @endif

                    @if($emergency->status === 'recibido')
                    <button type="button" onclick="updateStatus('en_evaluacion')" class="w-full inline-flex items-center justify-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Iniciar Evaluación
                    </button>
                    @endif

                    @if($emergency->status === 'en_evaluacion')
                    <button type="button" onclick="updateStatus('estabilizado')" class="w-full inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                        Marcar Estabilizado
                    </button>
                    @endif

                    @if($emergency->ubicacion_actual === 'cirugia')
                    <div class="mt-4 p-3 bg-purple-50 border border-purple-200 rounded-lg">
                        <p class="text-sm text-purple-800 flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                            Paciente en Quirófano
                        </p>
                        <p class="text-xs text-purple-600 mt-1">{{ $emergency->nro_cirugia }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Timeline -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-6 py-4 bg-slate-50 border-b border-slate-200">
                    <h2 class="font-semibold text-slate-800">Historial</h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex gap-3">
                            <div class="flex flex-col items-center">
                                <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                                <div class="w-0.5 h-full bg-slate-200 mt-1"></div>
                            </div>
                            <div>
                                <p class="text-xs text-slate-500">{{ $emergency->created_at->format('d/m/Y H:i') }}</p>
                                <p class="text-sm text-slate-800">Emergencia creada</p>
                            </div>
                        </div>

                        @if($emergency->admission_date)
                        <div class="flex gap-3">
                            <div class="flex flex-col items-center">
                                <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                <div class="w-0.5 h-full bg-slate-200 mt-1"></div>
                            </div>
                            <div>
                                <p class="text-xs text-slate-500">{{ $emergency->admission_date->format('d/m/Y H:i') }}</p>
                                <p class="text-sm text-slate-800">Paciente admitido</p>
                            </div>
                        </div>
                        @endif

                        @if($emergency->discharge_date)
                        <div class="flex gap-3">
                            <div class="flex flex-col items-center">
                                <div class="w-2 h-2 bg-gray-500 rounded-full"></div>
                            </div>
                            <div>
                                <p class="text-xs text-slate-500">{{ $emergency->discharge_date->format('d/m/Y H:i') }}</p>
                                <p class="text-sm text-slate-800">Paciente dado de alta</p>
                            </div>
                        </div>
                        @endif

                        <div class="flex gap-3">
                            <div class="flex flex-col items-center">
                                <div class="w-2 h-2 bg-slate-400 rounded-full"></div>
                            </div>
                            <div>
                                <p class="text-xs text-slate-500">{{ $emergency->updated_at->format('d/m/Y H:i') }}</p>
                                <p class="text-sm text-slate-600">Última actualización</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para actualizar estado -->
<div id="statusModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" onclick="closeModal()"></div>
    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                            <h3 class="text-base font-semibold leading-6 text-slate-900" id="modal-title">Actualizar Estado</h3>
                            <div class="mt-4">
                                <form id="statusForm" method="POST" action="{{ route('emergency-staff.update-status', $emergency) }}">
                                    @csrf
                                    <div>
                                        <label for="status" class="block text-sm font-medium leading-6 text-slate-900">Nuevo Estado</label>
                                        <select name="status" id="status" class="mt-2 block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-slate-900 ring-1 ring-inset ring-slate-300 focus:ring-2 focus:ring-blue-600 sm:text-sm sm:leading-6" required>
                                            <option value="recibido">Recibido</option>
                                            <option value="en_evaluacion">En Evaluación</option>
                                            <option value="estabilizado">Estabilizado</option>
                                            <option value="uti">UTI</option>
                                            <option value="cirugia">Cirugía</option>
                                            <option value="alta">Alta</option>
                                            <option value="fallecido">Fallecido</option>
                                        </select>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-slate-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                    <button type="button" onclick="document.getElementById('statusForm').submit()" class="inline-flex w-full justify-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 sm:ml-3 sm:w-auto">Actualizar</button>
                    <button type="button" onclick="closeModal()" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 hover:bg-slate-50 sm:mt-0 sm:w-auto">Cancelar</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function updateStatus(newStatus) {
    if(confirm('¿Cambiar estado a "' + newStatus + '"?')) {
        document.getElementById('status').value = newStatus;
        document.getElementById('statusModal').classList.remove('hidden');
    }
}

function closeModal() {
    document.getElementById('statusModal').classList.add('hidden');
}
</script>
@endsection
