@extends('layouts.app')

@section('content')
<div class="p-6 bg-slate-50 min-h-screen">
    <!-- Header Profesional -->
    <div class="flex justify-between items-center mb-6 pb-4 border-b border-slate-200">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center shadow-lg shadow-blue-200">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Historial Médico</h1>
                <p class="text-sm text-slate-500">{{ $emergency->is_temp_id ? 'Paciente Temporal' : ($emergency->paciente?->nombre ?? 'Desconocido') }}</p>
            </div>
        </div>
        <a href="{{ route('emergency-staff.dashboard') }}" class="flex items-center px-4 py-2 bg-white border border-slate-300 rounded-lg text-slate-600 hover:bg-slate-50 hover:border-slate-400 transition-all shadow-sm">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver al Dashboard
        </a>
    </div>

    <!-- Tarjeta de Paciente - Horizontal y Profesional -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 mb-6">
        <div class="flex flex-col lg:flex-row lg:items-center gap-6">
            <!-- Info Principal -->
            <div class="flex items-center gap-4 flex-1">
                <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center shadow-md">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-slate-800">{{ $emergency->is_temp_id ? 'Paciente Temporal' : ($emergency->paciente?->nombre ?? 'Desconocido') }}</h2>
                    <div class="flex items-center gap-3 mt-1">
                        <span class="text-sm text-slate-500 font-mono bg-slate-100 px-2 py-0.5 rounded">{{ $emergency->code }}</span>
                        <span class="text-sm text-slate-400">|</span>
                        <span class="text-sm text-slate-600">{{ $emergency->is_temp_id ? 'ID: ' . $emergency->temp_id : 'CI: ' . $emergency->patient_id }}</span>
                    </div>
                </div>
            </div>

            <!-- Datos en Fila Horizontal -->
            <div class="flex flex-wrap gap-6 lg:gap-8 text-sm">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 uppercase tracking-wide">Estado</p>
                        <span class="font-semibold text-slate-700">{{ ucfirst(str_replace('_', ' ', $emergency->status)) }}</span>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 uppercase tracking-wide">Ingreso</p>
                        <span class="font-semibold text-slate-700">{{ $emergency->admission_date?->format('d/m/Y H:i') ?? $emergency->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 uppercase tracking-wide">Ubicación</p>
                        <span class="font-semibold text-slate-700">{{ $emergency->ubicacion_label }}</span>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-amber-100 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 uppercase tracking-wide">Costo Total</p>
                        <span class="font-bold text-slate-800">Bs. {{ number_format($emergency->cost ?? 0, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Layout de Filas Horizontales -->
    <div class="flex flex-col gap-6">
            
            <!-- Signos Vitales - Tarjeta con Color -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-50 to-blue-100 border-b border-blue-200 px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center shadow-md">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-blue-900">Signos Vitales</h3>
                            <p class="text-xs text-blue-600">Datos fisiológicos del paciente</p>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    @if(!empty($vitalSigns))
                    <div class="grid grid-cols-2 gap-4">
                        @if(isset($vitalSigns['presion_arterial']) && $vitalSigns['presion_arterial'])
                        <div class="bg-slate-50 rounded-lg p-3 border border-slate-100">
                            <p class="text-xs text-slate-400 uppercase">Presión Arterial</p>
                            <p class="text-lg font-bold text-slate-800">{{ $vitalSigns['presion_arterial'] }} <span class="text-xs font-normal text-slate-500">mmHg</span></p>
                        </div>
                        @endif
                        @if(isset($vitalSigns['frecuencia_cardiaca']) && $vitalSigns['frecuencia_cardiaca'])
                        <div class="bg-slate-50 rounded-lg p-3 border border-slate-100">
                            <p class="text-xs text-slate-400 uppercase">Frec. Cardíaca</p>
                            <p class="text-lg font-bold text-slate-800">{{ $vitalSigns['frecuencia_cardiaca'] }} <span class="text-xs font-normal text-slate-500">lpm</span></p>
                        </div>
                        @endif
                        @if(isset($vitalSigns['frecuencia_respiratoria']) && $vitalSigns['frecuencia_respiratoria'])
                        <div class="bg-slate-50 rounded-lg p-3 border border-slate-100">
                            <p class="text-xs text-slate-400 uppercase">Frec. Respiratoria</p>
                            <p class="text-lg font-bold text-slate-800">{{ $vitalSigns['frecuencia_respiratoria'] }} <span class="text-xs font-normal text-slate-500">rpm</span></p>
                        </div>
                        @endif
                        @if(isset($vitalSigns['temperatura']) && $vitalSigns['temperatura'])
                        <div class="bg-slate-50 rounded-lg p-3 border border-slate-100">
                            <p class="text-xs text-slate-400 uppercase">Temperatura</p>
                            <p class="text-lg font-bold text-slate-800">{{ $vitalSigns['temperatura'] }} <span class="text-xs font-normal text-slate-500">°C</span></p>
                        </div>
                        @endif
                        @if(isset($vitalSigns['saturacion_o2']) && $vitalSigns['saturacion_o2'])
                        <div class="bg-slate-50 rounded-lg p-3 border border-slate-100">
                            <p class="text-xs text-slate-400 uppercase">Saturación O₂</p>
                            <p class="text-lg font-bold text-slate-800">{{ $vitalSigns['saturacion_o2'] }} <span class="text-xs font-normal text-slate-500">%</span></p>
                        </div>
                        @endif
                        @if(isset($vitalSigns['glucosa']) && $vitalSigns['glucosa'])
                        <div class="bg-slate-50 rounded-lg p-3 border border-slate-100">
                            <p class="text-xs text-slate-400 uppercase">Glucosa</p>
                            <p class="text-lg font-bold text-slate-800">{{ $vitalSigns['glucosa'] }} <span class="text-xs font-normal text-slate-500">mg/dL</span></p>
                        </div>
                        @endif
                    </div>
                    @if(isset($vitalSigns['fecha_registro']) && $vitalSigns['fecha_registro'])
                    <div class="mt-4 pt-3 border-t border-slate-100 text-right">
                        <span class="text-xs text-slate-400">Registrado: {{ \Carbon\Carbon::parse($vitalSigns['fecha_registro'])->format('d/m/Y H:i') }}</span>
                    </div>
                    @endif
                    @else
                    <div class="text-center py-8">
                        <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <p class="text-slate-400">No hay signos vitales registrados</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Nivel de Gravedad / Triage -->
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
                $triageColores = [
                    'leve' => ['bg' => 'bg-emerald-500', 'light' => 'bg-emerald-50', 'border' => 'border-emerald-200', 'text' => 'text-emerald-800'],
                    'moderado' => 'bg-yellow-500',
                    'grave' => 'bg-orange-500',
                    'critico' => 'bg-red-600',
                ];
                $coloresGravedad = [
                    'leve' => ['bg' => 'bg-emerald-50', 'border' => 'border-emerald-200', 'text' => 'text-emerald-800', 'badge' => 'bg-emerald-100 text-emerald-700'],
                    'moderado' => ['bg' => 'bg-yellow-50', 'border' => 'border-yellow-200', 'text' => 'text-yellow-800', 'badge' => 'bg-yellow-100 text-yellow-700'],
                    'grave' => ['bg' => 'bg-orange-50', 'border' => 'border-orange-200', 'text' => 'text-orange-800', 'badge' => 'bg-orange-100 text-orange-700'],
                    'critico' => ['bg' => 'bg-red-50', 'border' => 'border-red-200', 'text' => 'text-red-800', 'badge' => 'bg-red-100 text-red-700'],
                ];
                $colorActual = $coloresGravedad[$nivelGravedad] ?? ['bg' => 'bg-slate-50', 'border' => 'border-slate-200', 'text' => 'text-slate-600', 'badge' => 'bg-slate-100 text-slate-600'];
            @endphp

            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="bg-gradient-to-r from-amber-50 to-orange-50 border-b border-orange-200 px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-orange-500 rounded-lg flex items-center justify-center shadow-md">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-orange-900">Nivel de Gravedad</h3>
                            <p class="text-xs text-orange-600">Clasificación Triage</p>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    @if($nivelGravedad)
                    <div class="{{ $colorActual['bg'] }} border-2 {{ $colorActual['border'] }} rounded-xl p-5 text-center">
                        <span class="inline-block px-4 py-1 rounded-full text-sm font-bold {{ $colorActual['badge'] }} mb-2">{{ strtoupper($nivelGravedad) }}</span>
                        <p class="{{ $colorActual['text'] }} text-sm">
                            @switch($nivelGravedad)
                                @case('leve') Paciente estable, sin riesgo inmediato @break
                                @case('moderado') Requiere atención médica prioritaria @break
                                @case('grave') Condición seria, monitoreo constante @break
                                @case('critico') Riesgo vital, intervención inmediata @break
                            @endswitch
                        </p>
                        @if($ultimaEvaluacion && isset($ultimaEvaluacion['fecha']))
                        <p class="text-xs text-slate-400 mt-3">Evaluado el {{ \Carbon\Carbon::parse($ultimaEvaluacion['fecha'])->format('d/m/Y \a \l\a\s H:i') }}</p>
                        @endif
                    </div>
                    @else
                    <div class="text-center py-6">
                        <p class="text-slate-400">No se ha registrado nivel de gravedad</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Observaciones Clínicas -->
            @if($emergency->observations || $emergency->initial_assessment || $emergency->symptoms)
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="bg-gradient-to-r from-violet-50 to-purple-50 border-b border-violet-200 px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-violet-500 rounded-lg flex items-center justify-center shadow-md">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-violet-900">Observaciones Clínicas</h3>
                            <p class="text-xs text-violet-600">Notas del personal médico</p>
                        </div>
                    </div>
                </div>
                <div class="p-6 space-y-4">
                    @if($emergency->symptoms)
                    <div class="border-l-4 border-violet-400 pl-4">
                        <h4 class="text-sm font-semibold text-violet-700 mb-1">Síntomas</h4>
                        <p class="text-sm text-slate-700">{{ $emergency->symptoms }}</p>
                    </div>
                    @endif
                    @if($emergency->initial_assessment)
                    <div class="border-l-4 border-blue-400 pl-4">
                        <h4 class="text-sm font-semibold text-blue-700 mb-1">Evaluación Inicial</h4>
                        <p class="text-sm text-slate-700">{{ $emergency->initial_assessment }}</p>
                    </div>
                    @endif
                    @if($emergency->observations)
                    <div class="border-l-4 border-emerald-400 pl-4">
                        <h4 class="text-sm font-semibold text-emerald-700 mb-1">Observaciones Adicionales</h4>
                        <p class="text-sm text-slate-700">{{ $emergency->observations }}</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif
            
            <!-- Medicamentos Aplicados - Con Color y Profesional -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="bg-gradient-to-r from-cyan-50 to-teal-50 border-b border-teal-200 px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-teal-500 rounded-lg flex items-center justify-center shadow-md">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-teal-900">Medicamentos Aplicados</h3>
                            <p class="text-xs text-teal-600">Registro de fármacos administrados</p>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    @php
                        $hayMedicamentos = false;
                        $totalMedicamentos = 0;
                    @endphp

                    @foreach($detalleCostos as $evaluacion)
                        @if(isset($evaluacion['tipo']) && $evaluacion['tipo'] === 'evaluacion' && !empty($evaluacion['medicamentos']))
                            @php
                                $hayMedicamentos = true;
                                $responsable = isset($evaluacion['usuario_id']) && isset($usuariosMedicamentos[$evaluacion['usuario_id']])
                                    ? $usuariosMedicamentos[$evaluacion['usuario_id']]
                                    : 'Sistema';
                            @endphp
                            <div class="mb-5 last:mb-0">
                                <!-- Header de evaluación -->
                                <div class="flex items-center justify-between bg-slate-50 rounded-lg px-3 py-2 mb-3">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span class="text-sm font-medium text-slate-700">{{ \Carbon\Carbon::parse($evaluacion['fecha'])->format('d/m/Y H:i') }}</span>
                                    </div>
                                    @if(isset($evaluacion['nivel_gravedad']))
                                    <span class="px-2 py-0.5 rounded text-xs font-medium
                                        @switch($evaluacion['nivel_gravedad'])
                                            @case('leve') bg-emerald-100 text-emerald-700 @break
                                            @case('moderado') bg-yellow-100 text-yellow-700 @break
                                            @case('grave') bg-orange-100 text-orange-700 @break
                                            @case('critico') bg-red-100 text-red-700 @break
                                        @endswitch
                                    ">{{ ucfirst($evaluacion['nivel_gravedad']) }}</span>
                                    @endif
                                </div>

                                <!-- Tabla de medicamentos -->
                                <div class="overflow-x-auto">
                                    <table class="w-full text-sm">
                                        <thead>
                                            <tr class="border-b border-slate-200">
                                                <th class="text-left py-2 text-xs font-medium text-slate-500 uppercase">Medicamento</th>
                                                <th class="text-center py-2 text-xs font-medium text-slate-500 uppercase">Cantidad</th>
                                                <th class="text-right py-2 text-xs font-medium text-slate-500 uppercase">Costo</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($evaluacion['medicamentos'] as $med)
                                                @php $totalMedicamentos += $med['subtotal'] ?? 0; @endphp
                                                <tr class="border-b border-slate-100 last:border-0">
                                                    <td class="py-2">
                                                        <p class="font-medium text-slate-800">{{ $med['nombre'] }}</p>
                                                    </td>
                                                    <td class="py-2 text-center text-slate-600">{{ $med['cantidad'] }} {{ $med['unidad_medida'] }}</td>
                                                    <td class="py-2 text-right font-medium text-slate-800">Bs. {{ number_format($med['subtotal'] ?? 0, 2) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Responsable -->
                                <div class="mt-2 flex items-center gap-2 text-xs text-slate-500">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    <span>Aplicado por: <span class="font-medium text-slate-700">{{ $responsable }}</span></span>
                                </div>
                            </div>
                        @endif
                    @endforeach

                    @if(!$hayMedicamentos)
                    <div class="text-center py-8">
                        <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                            </svg>
                        </div>
                        <p class="text-slate-400">No hay medicamentos registrados</p>
                    </div>
                    @else
                    <div class="mt-4 pt-4 border-t-2 border-slate-200">
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-slate-600">Total en Medicamentos</span>
                            <span class="text-xl font-bold text-teal-700">Bs. {{ number_format($totalMedicamentos, 2) }}</span>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Cuenta de Cobro -->
            @if($cuenta)
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="bg-gradient-to-r from-amber-50 to-yellow-50 border-b border-yellow-200 px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-amber-500 rounded-lg flex items-center justify-center shadow-md">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-amber-900">Cuenta de Cobro</h3>
                            <p class="text-xs text-amber-600">Resumen de pagos</p>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        <div class="flex justify-between items-center py-2 border-b border-slate-100">
                            <span class="text-slate-600">Total Calculado</span>
                            <span class="font-semibold text-slate-800">Bs. {{ number_format($cuenta->total_calculado, 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-slate-100">
                            <span class="text-slate-600">Total Pagado</span>
                            <span class="font-semibold text-emerald-600">Bs. {{ number_format($cuenta->total_pagado, 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2">
                            <span class="text-slate-600">Saldo Pendiente</span>
                            <span class="font-bold {{ $cuenta->saldo_pendiente > 0 ? 'text-red-600' : 'text-emerald-600' }}">Bs. {{ number_format($cuenta->saldo_pendiente, 2) }}</span>
                        </div>
                    </div>
                    <div class="mt-4">
                        <span class="inline-block px-3 py-1 rounded-full text-xs font-medium bg-{{ $cuenta->estado_color }}-100 text-{{ $cuenta->estado_color }}-800">
                            {{ $cuenta->estado_label }}
                        </span>
                    </div>
                </div>
            </div>
            @endif

            <!-- Historial de Movimientos -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="bg-gradient-to-r from-slate-100 to-slate-200 border-b border-slate-300 px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-slate-600 rounded-lg flex items-center justify-center shadow-md">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-slate-800">Historial de Movimientos</h3>
                            <p class="text-xs text-slate-600">Traza de ubicaciones</p>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    @if(!empty($flujoHistorial))
                    <div class="space-y-4">
                        @foreach(array_reverse($flujoHistorial) as $movimiento)
                        <div class="flex gap-4">
                            <div class="flex flex-col items-center">
                                <div class="w-3 h-3 rounded-full bg-blue-500 ring-4 ring-blue-100"></div>
                                @if(!$loop->last)
                                <div class="w-0.5 flex-1 bg-slate-200 my-1"></div>
                                @endif
                            </div>
                            <div class="flex-1 pb-4">
                                <p class="text-sm font-medium text-slate-800">{{ $movimiento['notas'] ?? 'Movimiento registrado' }}</p>
                                <div class="flex items-center gap-2 text-xs text-slate-500 mt-1">
                                    <span class="bg-slate-100 px-2 py-0.5 rounded">{{ \Carbon\Carbon::parse($movimiento['fecha'])->format('d/m/Y H:i') }}</span>
                                    <span class="text-slate-400">→</span>
                                    <span class="font-medium text-blue-600">{{ $movimiento['desde'] }}</span>
                                    <span class="text-slate-400">→</span>
                                    <span class="font-medium text-emerald-600">{{ $movimiento['hasta'] }}</span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-6">
                        <p class="text-slate-400">No hay movimientos registrados</p>
                    </div>
                    @endif
                </div>
            </div>
    </div>

    <!-- Botones de Acción -->
    <div class="flex justify-between items-center mt-8 pt-6 border-t border-slate-200">
        <a href="{{ route('emergency-staff.dashboard') }}" class="flex items-center px-5 py-2.5 bg-white border border-slate-300 rounded-lg text-slate-700 font-medium hover:bg-slate-50 hover:border-slate-400 transition-all shadow-sm">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver al Dashboard
        </a>
        <a href="{{ route('emergency-staff.evaluacion', $emergency) }}" class="flex items-center px-6 py-2.5 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-all shadow-md shadow-blue-200">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            Nueva Evaluación
        </a>
    </div>
</div>
@endsection
