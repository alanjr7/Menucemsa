@extends('layouts.app')

@section('content')
<div class="p-6 bg-slate-50 min-h-screen">
    <!-- Header Profesional -->
    <div class="flex justify-between items-center mb-6 pb-4 border-b border-slate-200">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-indigo-600 rounded-lg flex items-center justify-center shadow-lg shadow-indigo-200">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Historial de Internación</h1>
                <p class="text-sm text-slate-500">Paciente: {{ $hospitalizacion->paciente?->nombre ?? 'Desconocido' }}</p>
            </div>
        </div>
        <a href="{{ route('internacion-staff.dashboard') }}" class="flex items-center px-4 py-2 bg-white border border-slate-300 rounded-lg text-slate-600 hover:bg-slate-50 hover:border-slate-400 transition-all shadow-sm">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver al Dashboard
        </a>
    </div>

    @php
        // Verificar si tiene habitación asignada
        $tieneHabitacion = !empty($hospitalizacion->habitacion_id);

        // Solo calcular días en cama si tiene habitación asignada
        if ($tieneHabitacion) {
            $diasEstancia = $hospitalizacion->fecha_alta
                ? ceil($hospitalizacion->fecha_ingreso->diffInDays($hospitalizacion->fecha_alta))
                : max(1, ceil($hospitalizacion->fecha_ingreso->diffInDays(now())));
            $costoEstancia = $diasEstancia * ($hospitalizacion->precio_cama_dia ?? 0);
        } else {
            $diasEstancia = 0; // Sin cama asignada = 0 días en cama
            $costoEstancia = 0;
        }
    @endphp

    <!-- Tarjeta de Paciente -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 mb-6">
        <div class="flex flex-col lg:flex-row lg:items-center gap-6">
            <!-- Info Principal -->
            <div class="flex items-center gap-4 flex-1">
                <div class="w-16 h-16 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-full flex items-center justify-center shadow-md">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-slate-800">{{ $hospitalizacion->paciente?->nombre ?? 'Desconocido' }}</h2>
                    <div class="flex items-center gap-3 mt-1">
                        <span class="text-sm text-slate-500 font-mono bg-slate-100 px-2 py-0.5 rounded">{{ $hospitalizacion->id }}</span>
                        <span class="text-sm text-slate-400">|</span>
                        <span class="text-sm text-slate-600">CI: {{ $hospitalizacion->ci_paciente }}</span>
                    </div>
                </div>
            </div>

            <!-- Datos en Fila -->
            <div class="flex flex-wrap gap-6 lg:gap-8 text-sm">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 uppercase tracking-wide">Estado</p>
                        <span class="font-semibold text-slate-700">{{ ucfirst($hospitalizacion->estado ?? 'Activo') }}</span>
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
                        <span class="font-semibold text-slate-700">{{ $hospitalizacion->fecha_ingreso?->format('d/m/Y H:i') }}</span>
                    </div>
                </div>

                @if($tieneHabitacion)
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-amber-100 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 uppercase tracking-wide">Días en Cama</p>
                        <span class="font-semibold text-slate-700">{{ $diasEstancia }} {{ $diasEstancia == 1 ? 'día' : 'días' }}</span>
                    </div>
                </div>
                @else
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 uppercase tracking-wide">Días en Cama</p>
                        <span class="font-semibold text-orange-600">Pendiente</span>
                    </div>
                </div>
                @endif

                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 {{ $tieneHabitacion ? 'bg-violet-100' : 'bg-gray-100' }} rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 {{ $tieneHabitacion ? 'text-violet-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 uppercase tracking-wide">Habitación</p>
                        <span class="font-semibold {{ $tieneHabitacion ? 'text-slate-700' : 'text-gray-400' }}">{{ $hospitalizacion->habitacion_id ?? 'Sin asignar' }}</span>
                    </div>
                </div>

                @if($tieneHabitacion)
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 uppercase tracking-wide">Costo Estancia</p>
                        <span class="font-bold text-slate-800">Bs. {{ number_format($costoEstancia, 2) }}</span>
                    </div>
                </div>
                @else
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 uppercase tracking-wide">Sin Habitación</p>
                        <span class="font-semibold text-amber-600">Asignar cama</span>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    @php
        $totalMedicamentos = $medicamentos->sum(function($m) { return $m->medicamento?->precio * $m->cantidad ?? 0; });
        $totalCatering = $catering->where('estado', 'dado')->sum('precio');
        $totalDrenajes = $drenajes->where('realizado', true)->sum('precio');
        $totalGeneral = $costoEstancia + $totalMedicamentos + $totalCatering + $totalDrenajes;
    @endphp

    <!-- Layout Principal: Organizado por Filas -->
    <div class="flex flex-col gap-6">

        <!-- Fila 1: Medicamentos Administrados (Lo más importante) -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="bg-gradient-to-r from-indigo-50 to-purple-50 border-b border-indigo-200 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-indigo-600 rounded-lg flex items-center justify-center shadow-md">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-indigo-900">Medicamentos Administrados</h3>
                            <p class="text-xs text-indigo-600">{{ $medicamentos->count() }} registros - Total: Bs. {{ number_format($totalMedicamentos, 2) }}</p>
                        </div>
                    </div>
                    <span class="bg-indigo-100 text-indigo-700 px-3 py-1 rounded-full text-sm font-medium">
                        {{ $medicamentos->count() }} items
                    </span>
                </div>
            </div>
            <div class="p-6">
                @if($medicamentos->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-slate-200">
                                <th class="text-left py-3 text-xs font-semibold text-slate-500 uppercase">Medicamento</th>
                                <th class="text-center py-3 text-xs font-semibold text-slate-500 uppercase">Cantidad</th>
                                <th class="text-center py-3 text-xs font-semibold text-slate-500 uppercase">Fecha/Hora</th>
                                <th class="text-left py-3 text-xs font-semibold text-slate-500 uppercase">Administrado por</th>
                                <th class="text-right py-3 text-xs font-semibold text-slate-500 uppercase">Costo</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($medicamentos as $med)
                            <tr class="border-b border-slate-100 last:border-0 hover:bg-slate-50">
                                <td class="py-3">
                                    <p class="font-medium text-slate-800">{{ $med->medicamento?->nombre ?? 'Desconocido' }}</p>
                                    @if($med->via_administracion)
                                    <p class="text-xs text-slate-500">Vía: {{ $med->via_administracion }}</p>
                                    @endif
                                </td>
                                <td class="py-3 text-center">
                                    <span class="font-medium text-slate-700">{{ $med->cantidad }} {{ $med->unidad }}</span>
                                </td>
                                <td class="py-3 text-center text-slate-600">
                                    {{ $med->fecha?->format('d/m/Y') }} {{ $med->hora?->format('H:i') }}
                                </td>
                                <td class="py-3 text-slate-600">
                                    {{ $med->administeredBy?->name ?? 'Desconocido' }}
                                </td>
                                <td class="py-3 text-right font-medium text-slate-800">
                                    Bs. {{ number_format($med->medicamento?->precio * $med->cantidad ?? 0, 2) }}
                                    @if($med->cargo_generado)
                                    <span class="ml-2 text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded">Cobrado</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-8">
                    <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                        </svg>
                    </div>
                    <p class="text-slate-400">No hay medicamentos registrados</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Fila 2: Catering y Drenajes (2 columnas) -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Catering -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="bg-gradient-to-r from-orange-50 to-amber-50 border-b border-orange-200 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-orange-500 rounded-lg flex items-center justify-center shadow-md">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-orange-900">Catering / Alimentación</h3>
                                <p class="text-xs text-orange-600">{{ $catering->where('estado', 'dado')->count() }} servicios dados</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    @if($catering->count() > 0)
                    <div class="space-y-3">
                        @foreach($catering->where('estado', 'dado') as $cat)
                        <div class="flex items-center justify-between p-3 bg-green-50 border border-green-200 rounded-lg">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-green-500 rounded-lg flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-medium text-slate-800">{{ $cat->tipo_label }}</p>
                                    <p class="text-xs text-slate-500">{{ $cat->fecha?->format('d/m/Y') }} {{ $cat->hora_registro?->format('H:i') }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="text-sm font-medium text-slate-700">Bs. {{ number_format($cat->precio, 2) }}</span>
                                <p class="text-xs text-slate-500">{{ $cat->registeredBy?->name }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-6">
                        <p class="text-slate-400">No hay registros de catering</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Drenajes -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="bg-gradient-to-r from-cyan-50 to-blue-50 border-b border-cyan-200 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-cyan-500 rounded-lg flex items-center justify-center shadow-md">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-cyan-900">Drenajes Realizados</h3>
                                <p class="text-xs text-cyan-600">{{ $drenajes->where('realizado', true)->count() }} procedimientos</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    @if($drenajes->count() > 0)
                    <div class="space-y-3">
                        @foreach($drenajes->where('realizado', true) as $dren)
                        <div class="flex items-center justify-between p-3 bg-cyan-50 border border-cyan-200 rounded-lg">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-cyan-500 rounded-lg flex items-center justify-center">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-medium text-slate-800">{{ $dren->tipo_drenaje ?: 'Drenaje General' }}</p>
                                    <p class="text-xs text-slate-500">{{ $dren->fecha?->format('d/m/Y') }} {{ $dren->hora?->format('H:i') }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="text-sm font-medium text-slate-700">Bs. {{ number_format($dren->precio, 2) }}</span>
                                <p class="text-xs text-slate-500">{{ $dren->registeredBy?->name }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-6">
                        <p class="text-slate-400">No hay drenajes registrados</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Fila 3: Info Médica y Cuenta (2 columnas) -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Diagnóstico y Tratamiento -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-50 to-blue-100 border-b border-blue-200 px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center shadow-md">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-blue-900">Información Médica</h3>
                            <p class="text-xs text-blue-600">Diagnóstico y tratamiento</p>
                        </div>
                    </div>
                </div>
                <div class="p-6 space-y-4">
                    @if($hospitalizacion->motivo)
                    <div class="border-l-4 border-blue-400 pl-4">
                        <h4 class="text-sm font-semibold text-blue-700 mb-1">Motivo de Internación</h4>
                        <p class="text-sm text-slate-700">{{ $hospitalizacion->motivo }}</p>
                    </div>
                    @endif
                    @if($hospitalizacion->diagnostico)
                    <div class="border-l-4 border-indigo-400 pl-4">
                        <h4 class="text-sm font-semibold text-indigo-700 mb-1">Diagnóstico</h4>
                        <p class="text-sm text-slate-700">{{ $hospitalizacion->diagnostico }}</p>
                    </div>
                    @endif
                    @if($hospitalizacion->tratamiento)
                    <div class="border-l-4 border-violet-400 pl-4">
                        <h4 class="text-sm font-semibold text-violet-700 mb-1">Tratamiento / Indicaciones</h4>
                        <p class="text-sm text-slate-700">{{ $hospitalizacion->tratamiento }}</p>
                    </div>
                    @endif
                    @if(!$hospitalizacion->motivo && !$hospitalizacion->diagnostico && !$hospitalizacion->tratamiento)
                    <div class="text-center py-6">
                        <p class="text-slate-400">No hay información médica registrada</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Datos de Emergencia -->
            @if($emergencia)
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="bg-gradient-to-r from-red-50 to-orange-50 border-b border-red-200 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-red-500 rounded-lg flex items-center justify-center shadow-md">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-red-900">Datos de Emergencia</h3>
                                <p class="text-xs text-red-600">Origen del paciente</p>
                            </div>
                        </div>
                        <a href="{{ route('emergency-staff.historial', $emergencia) }}" target="_blank" class="text-xs bg-red-100 text-red-700 px-3 py-1 rounded-lg hover:bg-red-200 transition">
                            Ver historial →
                        </a>
                    </div>
                </div>
                <div class="p-6 space-y-4">
                    @if($emergencia->symptoms)
                    <div class="border-l-4 border-red-400 pl-4">
                        <h4 class="text-sm font-semibold text-red-700 mb-1">Síntomas</h4>
                        <p class="text-sm text-slate-700">{{ $emergencia->symptoms }}</p>
                    </div>
                    @endif
                    @if($emergencia->initial_assessment)
                    <div class="border-l-4 border-orange-400 pl-4">
                        <h4 class="text-sm font-semibold text-orange-700 mb-1">Evaluación Inicial</h4>
                        <p class="text-sm text-slate-700">{{ $emergencia->initial_assessment }}</p>
                    </div>
                    @endif
                    @if($emergencia->treatment)
                    <div class="border-l-4 border-amber-400 pl-4">
                        <h4 class="text-sm font-semibold text-amber-700 mb-1">Tratamiento en Emergencia</h4>
                        <p class="text-sm text-slate-700">{{ $emergencia->treatment }}</p>
                    </div>
                    @endif
                    @if($emergencia->observations)
                    <div class="border-l-4 border-yellow-400 pl-4">
                        <h4 class="text-sm font-semibold text-yellow-700 mb-1">Observaciones</h4>
                        <p class="text-sm text-slate-700">{{ $emergencia->observations }}</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <!-- Fila 4: Resumen de Cuenta -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="bg-gradient-to-r from-amber-50 to-yellow-50 border-b border-yellow-200 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-amber-500 rounded-lg flex items-center justify-center shadow-md">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-amber-900">Resumen de Cuenta</h3>
                            <p class="text-xs text-amber-600">Detalle de cobros del paciente</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-slate-500">Total General</p>
                        <p class="text-2xl font-bold text-amber-700">Bs. {{ number_format($totalGeneral, 2) }}</p>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-slate-50 rounded-lg p-4 border border-slate-200">
                        <p class="text-xs text-slate-500 uppercase">Estancia</p>
                        <p class="text-lg font-semibold text-slate-800">{{ $diasEstancia }} {{ $diasEstancia == 1 ? 'día' : 'días' }}</p>
                        <p class="text-sm text-slate-600">Bs. {{ number_format($costoEstancia, 2) }}</p>
                    </div>
                    <div class="bg-indigo-50 rounded-lg p-4 border border-indigo-200">
                        <p class="text-xs text-indigo-600 uppercase">Medicamentos</p>
                        <p class="text-lg font-semibold text-indigo-800">{{ $medicamentos->count() }} items</p>
                        <p class="text-sm text-indigo-600">Bs. {{ number_format($totalMedicamentos, 2) }}</p>
                    </div>
                    <div class="bg-orange-50 rounded-lg p-4 border border-orange-200">
                        <p class="text-xs text-orange-600 uppercase">Catering</p>
                        <p class="text-lg font-semibold text-orange-800">{{ $catering->where('estado', 'dado')->count() }} servicios</p>
                        <p class="text-sm text-orange-600">Bs. {{ number_format($totalCatering, 2) }}</p>
                    </div>
                    <div class="bg-cyan-50 rounded-lg p-4 border border-cyan-200">
                        <p class="text-xs text-cyan-600 uppercase">Drenajes</p>
                        <p class="text-lg font-semibold text-cyan-800">{{ $drenajes->where('realizado', true)->count() }} proc.</p>
                        <p class="text-sm text-cyan-600">Bs. {{ number_format($totalDrenajes, 2) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Fila 5: Timeline Cronológico -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="bg-gradient-to-r from-slate-100 to-slate-200 border-b border-slate-300 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-slate-600 rounded-lg flex items-center justify-center shadow-md">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-slate-800">Timeline del Paciente</h3>
                            <p class="text-xs text-slate-600">Historial cronológico completo</p>
                        </div>
                    </div>
                    <span class="text-xs bg-slate-200 text-slate-700 px-3 py-1 rounded-full">
                        {{ $timeline->count() }} eventos
                    </span>
                </div>
            </div>
            <div class="p-6">
                @if($timeline->count() > 0)
                <div class="space-y-6">
                    @foreach($timeline as $evento)
                    <div class="flex gap-4">
                        <div class="flex flex-col items-center">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center ring-4
                                @switch($evento['tipo'])
                                    @case('ingreso') bg-blue-500 ring-blue-100 @break
                                    @case('medicamento') bg-indigo-500 ring-indigo-100 @break
                                    @case('catering') bg-orange-500 ring-orange-100 @break
                                    @case('drenaje') bg-cyan-500 ring-cyan-100 @break
                                    @case('alta') bg-green-500 ring-green-100 @break
                                    @default bg-slate-500 ring-slate-100 @break
                                @endswitch">
                                @switch($evento['tipo'])
                                    @case('ingreso')
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        @break
                                    @case('medicamento')
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                                        </svg>
                                        @break
                                    @case('catering')
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        </svg>
                                        @break
                                    @case('drenaje')
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                                        </svg>
                                        @break
                                    @case('alta')
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        @break
                                    @default
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                @endswitch
                            </div>
                            @if(!$loop->last)
                            <div class="w-0.5 flex-1 bg-slate-200 my-2"></div>
                            @endif
                        </div>
                        <div class="flex-1 pb-6">
                            <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
                                <div class="flex items-start justify-between mb-2">
                                    <div>
                                        <p class="font-semibold text-slate-800">{{ $evento['titulo'] }}</p>
                                        <p class="text-xs text-slate-500 mt-0.5">{{ $evento['fecha'] }} {{ $evento['hora'] }}</p>
                                    </div>
                                    <span class="text-xs font-medium px-2 py-1 rounded-full
                                        @switch($evento['tipo'])
                                            @case('ingreso') bg-blue-100 text-blue-700 @break
                                            @case('medicamento') bg-indigo-100 text-indigo-700 @break
                                            @case('catering') bg-orange-100 text-orange-700 @break
                                            @case('drenaje') bg-cyan-100 text-cyan-700 @break
                                            @case('alta') bg-green-100 text-green-700 @break
                                            @default bg-slate-100 text-slate-700 @break
                                        @endswitch">
                                        {{ ucfirst($evento['tipo']) }}
                                    </span>
                                </div>
                                @if($evento['descripcion'])
                                <p class="text-sm text-slate-600 mb-2">{{ $evento['descripcion'] }}</p>
                                @endif
                                <div class="flex items-center gap-2 text-xs text-slate-500">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                    <span>{{ $evento['responsable'] }}</span>
                                </div>
                                @if(isset($evento['detalles']) && $evento['detalles'])
                                <div class="mt-3 pt-3 border-t border-slate-200">
                                    @if($evento['tipo'] === 'medicamento')
                                        <div class="flex items-center gap-4 text-sm">
                                            <span class="text-slate-600">Cantidad: <strong>{{ $evento['detalles']['cantidad'] }} {{ $evento['detalles']['unidad'] }}</strong></span>
                                            @if($evento['detalles']['via_administracion'])
                                            <span class="text-slate-600">Vía: <strong>{{ $evento['detalles']['via_administracion'] }}</strong></span>
                                            @endif
                                        </div>
                                    @elseif($evento['tipo'] === 'catering')
                                        <span class="inline-block px-2 py-1 rounded text-xs
                                            @if($evento['detalles']['estado'] === 'dado') bg-green-100 text-green-700
                                            @elseif($evento['detalles']['estado'] === 'no_dado') bg-red-100 text-red-700
                                            @else bg-gray-100 text-gray-700 @endif">
                                            {{ $evento['detalles']['estado_label'] }}
                                        </span>
                                    @elseif($evento['tipo'] === 'drenaje')
                                        <div class="text-sm">
                                            <span class="text-slate-600">Tipo: <strong>{{ $evento['detalles']['tipo_drenaje'] ?: 'General' }}</strong></span>
                                            <span class="ml-3 inline-block px-2 py-0.5 rounded text-xs {{ $evento['detalles']['realizado'] ? 'bg-cyan-100 text-cyan-700' : 'bg-gray-100 text-gray-700' }}">
                                                {{ $evento['detalles']['realizado'] ? 'Realizado' : 'No realizado' }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                        @else
                        <div class="text-center py-12">
                            <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <p class="text-slate-400">No hay eventos registrados</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Botones de Acción -->
    <div class="flex justify-between items-center mt-8 pt-6 border-t border-slate-200">
        <a href="{{ route('internacion-staff.dashboard') }}" class="flex items-center px-5 py-2.5 bg-white border border-slate-300 rounded-lg text-slate-700 font-medium hover:bg-slate-50 hover:border-slate-400 transition-all shadow-sm">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver al Dashboard
        </a>
        <a href="{{ route('internacion-staff.evaluar', $hospitalizacion->id) }}" class="flex items-center px-6 py-2.5 bg-indigo-600 text-white rounded-lg font-medium hover:bg-indigo-700 transition-all shadow-md shadow-indigo-200">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            Evaluar Paciente
        </a>
    </div>
</div>
@endsection
