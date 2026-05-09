@extends('layouts.app')

@section('content')
<div class="w-full p-4 bg-gray-50/50 min-h-screen">

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-800">Detalles de Cirugía</h1>
            <p class="text-sm text-gray-500">Información completa de la intervención quirúrgica</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('quirofano.historial') }}" class="flex items-center px-3 py-2 border border-gray-300 text-gray-600 rounded-lg hover:bg-gray-50 font-medium transition-colors text-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Volver al Historial
            </a>
        </div>
    </div>

    <!-- Estado de la Cirugía -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <span class="text-sm text-gray-500">Estado de la Cirugía</span>
                <div class="mt-1">
                    @switch($cita->estado)
                        @case('programada')
                            <span class="px-3 py-1 text-sm font-semibold rounded-full bg-blue-100 text-blue-800">Programada</span>
                            @break
                        @case('en_curso')
                            <span class="px-3 py-1 text-sm font-semibold rounded-full bg-amber-100 text-amber-800">En Curso</span>
                            @break
                        @case('finalizada')
                            <span class="px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800">Finalizada</span>
                            @break
                        @case('cancelada')
                            <span class="px-3 py-1 text-sm font-semibold rounded-full bg-red-100 text-red-800">Cancelada</span>
                            @break
                    @endswitch
                </div>
            </div>
            @if($cita->estado === 'finalizada' && $cita->costo_final)
                <div class="text-right">
                    <span class="text-sm text-gray-500">Costo Total</span>
                    <div class="text-2xl font-bold text-green-600">${{ number_format($cita->costo_final, 2) }}</div>
                </div>
            @endif
        </div>
    </div>

    <!-- Sección 1: Información del Paciente -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
        <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            Información del Paciente
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label class="text-sm font-medium text-gray-500">Nombre</label>
                <p class="font-semibold text-gray-900">{{ $cita->paciente->nombre }}</p>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-500">CI</label>
                <p class="font-semibold text-gray-900">{{ $cita->paciente->ci }}</p>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-500">Seguro</label>
                <p class="font-semibold text-gray-900">{{ $cita->paciente->seguro->nombre ?? 'N/A' }}</p>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-500">Edad</label>
                <p class="font-semibold text-gray-900">
                    @if($cita->paciente->fecha_nacimiento)
                        {{ $cita->paciente->fecha_nacimiento->age }} años
                    @else
                        N/A
                    @endif
                </p>
            </div>
        </div>
    </div>

    <!-- Sección 2: Datos de la Cirugía -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
        <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            Datos de la Cirugía
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div>
                <label class="text-sm font-medium text-gray-500">Fecha de Ejecución</label>
                <p class="font-semibold text-gray-900">{{ \Carbon\Carbon::parse($cita->fecha)->format('d/m/Y') }}</p>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-500">Hora Inicio</label>
                <p class="font-semibold text-gray-900">{{ $cita->hora_inicio_real ?? $cita->hora_inicio_estimada->format('H:i') }}</p>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-500">Hora Fin</label>
                <p class="font-semibold text-gray-900">{{ $cita->hora_fin_real ?? '-' }}</p>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-500">Duración</label>
                <p class="font-semibold text-gray-900">
                    @if($cita->duracion_real)
                        @php
                            $horas = floor($cita->duracion_real / 60);
                            $minutos = $cita->duracion_real % 60;
                        @endphp
                        {{ $horas > 0 ? $horas . 'h ' : '' }}{{ $minutos }}min
                    @else
                        -
                    @endif
                </p>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-500">Tipo de Cirugía</label>
                <p class="font-semibold text-gray-900 capitalize">{{ $cita->tipo_cirugia }}</p>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-500">Quirófano</label>
                <p class="font-semibold text-gray-900">Quirófano {{ $cita->quirofano->id }}</p>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-500">Cirujano</label>
                <p class="font-semibold text-gray-900">{{ optional($cita->cirujano->user)->name ?? 'N/A' }}</p>
            </div>
            @if($cita->nombre_instrumentista)
            <div>
                <label class="text-sm font-medium text-gray-500">Instrumentista</label>
                <p class="font-semibold text-gray-900">{{ $cita->nombre_instrumentista }}</p>
            </div>
            @endif
            @if($cita->nombre_anestesiologo)
            <div>
                <label class="text-sm font-medium text-gray-500">Anestesiólogo</label>
                <p class="font-semibold text-gray-900">{{ $cita->nombre_anestesiologo }}</p>
            </div>
            @endif
        </div>
        
        @if($cita->descripcion_cirugia)
        <div class="mt-4">
            <label class="text-sm font-medium text-gray-500">Descripción de la Cirugía</label>
            <p class="text-gray-700 mt-1">{{ $cita->descripcion_cirugia }}</p>
        </div>
        @endif
        
        @if($cita->observaciones)
        <div class="mt-4">
            <label class="text-sm font-medium text-gray-500">Observaciones</label>
            <p class="text-gray-700 mt-1">{{ $cita->observaciones }}</p>
        </div>
        @endif
    </div>

    <!-- Sección 3: Medicamentos Utilizados -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
        <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
            </svg>
            Medicamentos Utilizados
        </h2>
        
        @if($medicamentosUsados && count($medicamentosUsados) > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Medicamento</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cantidad</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Precio Unitario</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($medicamentosUsados as $med)
                            <tr>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $med->descripcion }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $med->cantidad }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">${{ number_format($med->precio_unitario, 2) }}</td>
                                <td class="px-4 py-3 text-sm font-semibold text-gray-900">${{ number_format($med->subtotal, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-gray-500 text-sm text-center py-4">No se registraron medicamentos</p>
        @endif
    </div>

    <!-- Sección 4: Equipos y Procedimientos -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
        <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/>
            </svg>
            Equipos y Procedimientos
        </h2>
        
        @if($equiposUsados && count($equiposUsados) > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Equipo/Procedimiento</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cantidad</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Precio Unitario</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($equiposUsados as $eq)
                            <tr>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ str_replace('Cirugía - Equipo/Procedimiento: ', '', $eq->descripcion) }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $eq->cantidad }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">${{ number_format($eq->precio_unitario, 2) }}</td>
                                <td class="px-4 py-3 text-sm font-semibold text-gray-900">${{ number_format($eq->subtotal, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-gray-500 text-sm text-center py-4">No se registraron equipos o procedimientos</p>
        @endif
    </div>

    <!-- Sección 5: Resumen de Costos -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Resumen de Costos
        </h2>
        
        @php
            $totalMedicamentos = $medicamentosUsados->sum('subtotal');
            $totalEquipos = $equiposUsados->sum('subtotal');
            $costoCirugia = $cita->costo_final - $totalMedicamentos - $totalEquipos;
        @endphp
        
        <div class="space-y-3">
            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                <span class="text-sm text-gray-600">Costo de Cirugía</span>
                <span class="font-semibold text-gray-900">${{ number_format($costoCirugia, 2) }}</span>
            </div>
            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                <span class="text-sm text-gray-600">Medicamentos</span>
                <span class="font-semibold text-green-600">${{ number_format($totalMedicamentos, 2) }}</span>
            </div>
            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                <span class="text-sm text-gray-600">Equipos y Procedimientos</span>
                <span class="font-semibold text-cyan-600">${{ number_format($totalEquipos, 2) }}</span>
            </div>
            <div class="flex justify-between items-center pt-3">
                <span class="text-lg font-bold text-gray-900">Total</span>
                <span class="text-2xl font-bold text-green-600">${{ number_format($cita->costo_final, 2) }}</span>
            </div>
        </div>
    </div>

</div>
@endsection