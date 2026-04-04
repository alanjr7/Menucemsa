@extends('layouts.app')

@section('content')
<div class="p-6 bg-gray-50/50 min-h-screen">
    <!-- Header -->
    <div class="flex justify-between items-end mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Historial de Emergencia</h1>
            <p class="text-sm text-gray-500">Paciente: <span class="font-medium text-gray-700">{{ $emergency->is_temp_id ? 'Paciente Temporal' : ($emergency->paciente?->nombre ?? 'Desconocido') }}</span></p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('emergency-staff.dashboard') }}" class="flex items-center px-4 py-2 bg-white border border-gray-200 text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition-all shadow-sm">
                <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Volver al Dashboard
            </a>
        </div>
    </div>

    <!-- Información General del Paciente -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
        <div class="flex items-center gap-4 mb-4">
            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <div class="flex-1">
                <h2 class="text-lg font-bold text-gray-800">{{ $emergency->is_temp_id ? 'Paciente Temporal' : ($emergency->paciente?->nombre ?? 'Desconocido') }}</h2>
                <p class="text-sm text-gray-500">Código: <span class="font-mono font-medium">{{ $emergency->code }}</span> | {{ $emergency->is_temp_id ? 'ID: ' . $emergency->temp_id : 'CI: ' . $emergency->patient_id }}</p>
            </div>
            <div class="text-right">
                <span class="px-3 py-1 rounded-full text-sm font-medium bg-{{ $emergency->status_color }}-100 text-{{ $emergency->status_color }}-800">
                    {{ ucfirst(str_replace('_', ' ', $emergency->status)) }}
                </span>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 text-sm">
            <div class="bg-gray-50 rounded-lg p-3">
                <span class="text-gray-500 block text-xs">Tipo Ingreso</span>
                <span class="font-medium text-gray-800">{{ $emergency->tipo_ingreso_label }}</span>
            </div>
            <div class="bg-gray-50 rounded-lg p-3">
                <span class="text-gray-500 block text-xs">Destino Inicial</span>
                <span class="font-medium text-gray-800 capitalize">{{ $emergency->destino_inicial ?? 'Pendiente' }}</span>
            </div>
            <div class="bg-gray-50 rounded-lg p-3">
                <span class="text-gray-500 block text-xs">Ingreso</span>
                <span class="font-medium text-gray-800">{{ $emergency->admission_date?->format('d/m/Y H:i') ?? $emergency->created_at->format('d/m/Y H:i') }}</span>
            </div>
            <div class="bg-gray-50 rounded-lg p-3">
                <span class="text-gray-500 block text-xs">Ubicación Actual</span>
                <span class="font-medium text-gray-800">{{ $emergency->ubicacion_label }}</span>
            </div>
            <div class="bg-gray-50 rounded-lg p-3">
                <span class="text-gray-500 block text-xs">Costo Total</span>
                <span class="font-bold text-red-600">Bs. {{ number_format($emergency->cost ?? 0, 2) }}</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Columna Izquierda: Signos Vitales y Triage -->
        <div class="space-y-6">
            <!-- Signos Vitales -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800">Signos Vitales</h3>
                </div>

                @if(!empty($vitalSigns))
                <div class="space-y-3">
                    @if(isset($vitalSigns['presion_arterial']) && $vitalSigns['presion_arterial'])
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                        <span class="text-gray-600">Presión Arterial</span>
                        <span class="font-semibold text-gray-800">{{ $vitalSigns['presion_arterial'] }} <span class="text-xs text-gray-400">mmHg</span></span>
                    </div>
                    @endif
                    @if(isset($vitalSigns['frecuencia_cardiaca']) && $vitalSigns['frecuencia_cardiaca'])
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                        <span class="text-gray-600">Frecuencia Cardíaca</span>
                        <span class="font-semibold text-gray-800">{{ $vitalSigns['frecuencia_cardiaca'] }} <span class="text-xs text-gray-400">lpm</span></span>
                    </div>
                    @endif
                    @if(isset($vitalSigns['frecuencia_respiratoria']) && $vitalSigns['frecuencia_respiratoria'])
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                        <span class="text-gray-600">Frecuencia Respiratoria</span>
                        <span class="font-semibold text-gray-800">{{ $vitalSigns['frecuencia_respiratoria'] }} <span class="text-xs text-gray-400">rpm</span></span>
                    </div>
                    @endif
                    @if(isset($vitalSigns['temperatura']) && $vitalSigns['temperatura'])
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                        <span class="text-gray-600">Temperatura</span>
                        <span class="font-semibold text-gray-800">{{ $vitalSigns['temperatura'] }} <span class="text-xs text-gray-400">°C</span></span>
                    </div>
                    @endif
                    @if(isset($vitalSigns['saturacion_o2']) && $vitalSigns['saturacion_o2'])
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                        <span class="text-gray-600">Saturación O2</span>
                        <span class="font-semibold text-gray-800">{{ $vitalSigns['saturacion_o2'] }} <span class="text-xs text-gray-400">%</span></span>
                    </div>
                    @endif
                    @if(isset($vitalSigns['glucosa']) && $vitalSigns['glucosa'])
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                        <span class="text-gray-600">Glucosa</span>
                        <span class="font-semibold text-gray-800">{{ $vitalSigns['glucosa'] }} <span class="text-xs text-gray-400">mg/dL</span></span>
                    </div>
                    @endif
                    @if(isset($vitalSigns['fecha_registro']) && $vitalSigns['fecha_registro'])
                    <div class="text-xs text-gray-400 text-center pt-2">
                        Registrado: {{ \Carbon\Carbon::parse($vitalSigns['fecha_registro'])->format('d/m/Y H:i') }}
                    </div>
                    @endif
                </div>
                @else
                <div class="text-center py-6 text-gray-400">
                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <p class="text-sm">No hay signos vitales registrados</p>
                </div>
                @endif
            </div>

            <!-- Triage/Gravedad -->
            @php
                $ultimaEvaluacion = null;
                $nivelGravedad = null;
                foreach($detalleCostos as $evaluacion) {
                    if(isset($evaluacion['tipo']) && $evaluacion['tipo'] === 'evaluacion' && isset($evaluacion['nivel_gravedad'])) {
                        $ultimaEvaluacion = $evaluacion;
                        $nivelGravedad = $evaluacion['nivel_gravedad'];
                        break;
                    }
                }
                $coloresGravedad = [
                    'leve' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'border' => 'border-green-200', 'icon' => 'text-green-600'],
                    'moderado' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800', 'border' => 'border-yellow-200', 'icon' => 'text-yellow-600'],
                    'grave' => ['bg' => 'bg-orange-100', 'text' => 'text-orange-800', 'border' => 'border-orange-200', 'icon' => 'text-orange-600'],
                    'critico' => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'border' => 'border-red-200', 'icon' => 'text-red-600'],
                ];
                $colorActual = $coloresGravedad[$nivelGravedad] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'border' => 'border-gray-200', 'icon' => 'text-gray-600'];
            @endphp

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 {{ $colorActual['bg'] }} rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 {{ $colorActual['icon'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800">Nivel de Gravedad / Triage</h3>
                </div>

                @if($nivelGravedad)
                <div class="p-4 {{ $colorActual['bg'] }} {{ $colorActual['border'] }} border-2 rounded-xl text-center">
                    <span class="text-2xl font-bold {{ $colorActual['text'] }} uppercase">{{ $nivelGravedad }}</span>
                    <p class="text-sm {{ $colorActual['text'] }} mt-1">
                        @switch($nivelGravedad)
                            @case('leve') Paciente estable, sin riesgo inmediato @break
                            @case('moderado') Requiere atención médica prioritaria @break
                            @case('grave') Condición seria, monitoreo constante @break
                            @case('critico') Riesgo vital, intervención inmediata @break
                        @endswitch
                    </p>
                    @if($ultimaEvaluacion && isset($ultimaEvaluacion['fecha']))
                    <p class="text-xs {{ $colorActual['text'] }} mt-2 opacity-75">
                        Evaluado: {{ \Carbon\Carbon::parse($ultimaEvaluacion['fecha'])->format('d/m/Y H:i') }}
                    </p>
                    @endif
                </div>
                @else
                <div class="text-center py-6 text-gray-400">
                    <p class="text-sm">No se ha registrado nivel de gravedad</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Columna Central: Medicamentos Aplicados -->
        <div class="space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800">Medicamentos Aplicados</h3>
                </div>

                @php
                    $hayMedicamentos = false;
                    $totalMedicamentos = 0;
                @endphp

                <div class="space-y-4">
                    @foreach($detalleCostos as $evaluacion)
                        @if(isset($evaluacion['tipo']) && $evaluacion['tipo'] === 'evaluacion' && !empty($evaluacion['medicamentos']))
                            @php $hayMedicamentos = true; @endphp
                            <div class="border-l-4 border-purple-400 pl-4 py-2">
                                <p class="text-xs text-gray-500 mb-2">
                                    {{ \Carbon\Carbon::parse($evaluacion['fecha'])->format('d/m/Y H:i') }}
                                    @if(isset($evaluacion['nivel_gravedad']))
                                    <span class="ml-2 px-2 py-0.5 rounded text-xs font-medium
                                        @switch($evaluacion['nivel_gravedad'])
                                            @case('leve') bg-green-100 text-green-700 @break
                                            @case('moderado') bg-yellow-100 text-yellow-700 @break
                                            @case('grave') bg-orange-100 text-orange-700 @break
                                            @case('critico') bg-red-100 text-red-700 @break
                                        @endswitch
                                    ">{{ ucfirst($evaluacion['nivel_gravedad']) }}</span>
                                    @endif
                                </p>

                                @foreach($evaluacion['medicamentos'] as $med)
                                    @php $totalMedicamentos += $med['subtotal'] ?? 0; @endphp
                                    <div class="flex justify-between items-center py-2 border-b border-gray-100 last:border-0">
                                        <div>
                                            <p class="font-medium text-gray-800">{{ $med['nombre'] }}</p>
                                            <p class="text-xs text-gray-500">{{ $med['cantidad'] }} {{ $med['unidad_medida'] }} x Bs. {{ number_format($med['precio_unitario'] ?? 0, 2) }}</p>
                                        </div>
                                        <span class="font-semibold text-purple-600">Bs. {{ number_format($med['subtotal'] ?? 0, 2) }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    @endforeach
                </div>

                @if(!$hayMedicamentos)
                <div class="text-center py-6 text-gray-400">
                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                    </svg>
                    <p class="text-sm">No hay medicamentos registrados</p>
                </div>
                @else
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Total en Medicamentos:</span>
                        <span class="text-xl font-bold text-purple-600">Bs. {{ number_format($totalMedicamentos, 2) }}</span>
                    </div>
                </div>
                @endif
            </div>

            <!-- Cuenta de Cobro -->
            @if($cuenta)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">Cuenta de Cobro</h3>
                        <p class="text-xs text-gray-500">ID: {{ $cuenta->id }}</p>
                    </div>
                </div>

                <div class="space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Total Calculado:</span>
                        <span class="font-semibold">Bs. {{ number_format($cuenta->total_calculado, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Total Pagado:</span>
                        <span class="font-semibold text-green-600">Bs. {{ number_format($cuenta->total_pagado, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Saldo Pendiente:</span>
                        <span class="font-semibold {{ $cuenta->saldo_pendiente > 0 ? 'text-red-600' : 'text-green-600' }}">Bs. {{ number_format($cuenta->saldo_pendiente, 2) }}</span>
                    </div>
                </div>

                <div class="mt-4">
                    <span class="px-3 py-1 rounded-full text-xs font-medium bg-{{ $cuenta->estado_color }}-100 text-{{ $cuenta->estado_color }}-800">
                        {{ $cuenta->estado_label }}
                    </span>
                </div>
            </div>
            @endif
        </div>

        <!-- Columna Derecha: Flujo de Historial -->
        <div class="space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800">Historial de Movimientos</h3>
                </div>

                @if(!empty($flujoHistorial))
                <div class="space-y-3">
                    @foreach(array_reverse($flujoHistorial) as $movimiento)
                    <div class="flex gap-3">
                        <div class="flex flex-col items-center">
                            <div class="w-3 h-3 rounded-full bg-blue-500"></div>
                            @if(!$loop->last)
                            <div class="w-0.5 flex-1 bg-gray-200 my-1"></div>
                            @endif
                        </div>
                        <div class="flex-1 pb-4">
                            <p class="text-sm font-medium text-gray-800">{{ $movimiento['notas'] ?? 'Movimiento registrado' }}</p>
                            <div class="flex gap-2 text-xs text-gray-500 mt-1">
                                <span>{{ \Carbon\Carbon::parse($movimiento['fecha'])->format('d/m/Y H:i') }}</span>
                                <span>|</span>
                                <span>{{ $movimiento['desde'] }} → {{ $movimiento['hasta'] }}</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-6 text-gray-400">
                    <p class="text-sm">No hay movimientos registrados</p>
                </div>
                @endif
            </div>

            <!-- Observaciones -->
            @if($emergency->observations || $emergency->initial_assessment || $emergency->symptoms)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800">Observaciones Clínicas</h3>
                </div>

                @if($emergency->symptoms)
                <div class="mb-4">
                    <h4 class="text-sm font-semibold text-gray-700 mb-1">Síntomas</h4>
                    <p class="text-sm text-gray-600 bg-gray-50 p-3 rounded-lg">{{ $emergency->symptoms }}</p>
                </div>
                @endif

                @if($emergency->initial_assessment)
                <div class="mb-4">
                    <h4 class="text-sm font-semibold text-gray-700 mb-1">Evaluación Inicial</h4>
                    <p class="text-sm text-gray-600 bg-gray-50 p-3 rounded-lg">{{ $emergency->initial_assessment }}</p>
                </div>
                @endif

                @if($emergency->observations)
                <div>
                    <h4 class="text-sm font-semibold text-gray-700 mb-1">Observaciones Adicionales</h4>
                    <p class="text-sm text-gray-600 bg-gray-50 p-3 rounded-lg">{{ $emergency->observations }}</p>
                </div>
                @endif
            </div>
            @endif
        </div>
    </div>

    <!-- Botones de Acción -->
    <div class="flex justify-between items-center mt-8 pt-6 border-t border-gray-200">
        <a href="{{ route('emergency-staff.dashboard') }}" class="px-6 py-3 border border-gray-200 rounded-xl text-gray-700 font-medium hover:bg-gray-50 transition-colors">
            Volver al Dashboard
        </a>
        <div class="flex gap-3">
            <a href="{{ route('emergency-staff.evaluacion', $emergency) }}" class="px-6 py-3 bg-blue-600 text-white rounded-xl font-medium hover:bg-blue-700 transition-colors flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Nueva Evaluación
            </a>
        </div>
    </div>
</div>
@endsection
