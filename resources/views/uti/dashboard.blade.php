@extends('layouts.app')

@section('content')
<div class="p-6 bg-gray-50/50 min-h-screen">
    <!-- Header -->
    <div class="flex justify-between items-end mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Panel UTI - Terapia Intensiva</h1>
            <p class="text-sm text-gray-500">Unidad de Terapia Intensiva - Registro de movimientos</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('uti.operativa.medicamentos.readonly') }}" class="flex items-center px-4 py-2 bg-cyan-600 text-white font-medium rounded-xl hover:bg-cyan-700 transition-all shadow-sm">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                </svg>
                Ver Medicamentos
            </a>
            <a href="{{ route('emergency-staff.camillas.index') }}" class="flex items-center px-4 py-2 bg-blue-600 text-white font-medium rounded-xl hover:bg-blue-700 transition-all shadow-sm">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18M10 4v16M14 4v16"/>
                </svg>
                Camillas
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex flex-col items-center justify-center h-28 hover:shadow-md transition">
            <span class="text-gray-500 text-sm font-medium mb-1">Evaluaciones Hoy</span>
            <span class="text-3xl font-bold text-cyan-600">{{ $evaluaciones->count() }}</span>
        </div>
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex flex-col items-center justify-center h-28 hover:shadow-md transition">
            <span class="text-gray-500 text-sm font-medium mb-1">Registros de Camillas</span>
            <span class="text-3xl font-bold text-blue-600">{{ $camillaRegistradas->count() }}</span>
        </div>
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex flex-col items-center justify-center h-28 hover:shadow-md transition">
            <span class="text-gray-500 text-sm font-medium mb-1">Total Movimientos</span>
            <span class="text-3xl font-bold text-green-600">{{ $evaluaciones->count() + $camillaRegistradas->count() }}</span>
        </div>
    </div>

    <!-- Historial de Operaciones -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <h2 class="text-lg font-bold text-gray-800">Historial del Área UTI - {{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}</h2>
            <form method="GET" action="{{ route('uti.dashboard') }}" class="flex items-center gap-2">
                <label class="text-sm text-gray-500 whitespace-nowrap">Fecha:</label>
                <input type="date" name="fecha" value="{{ $fecha }}"
                    class="border border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:border-cyan-500"
                    onchange="this.form.submit()">
            </form>
        </div>

        @php
            $operaciones = collect();

            foreach ($evaluaciones as $ev) {
                $operaciones->push([
                    'tipo' => 'evaluacion',
                    'fecha' => $ev->created_at,
                    'usuario' => $ev->user?->name ?? '—',
                    'paciente' => $ev->paciente?->nombre ?? '—',
                    'detalle' => 'Realizó evaluación en UTI',
                    'hora' => $ev->created_at->setTimezone('America/La_Paz')->format('H:i'),
                ]);
            }

            foreach ($camillaRegistradas as $c) {
                $operaciones->push([
                    'tipo' => 'camilla',
                    'fecha' => $c->created_at,
                    'usuario' => $c->user?->name ?? 'Sin usuario',
                    'paciente' => $c->cuentaCobro?->paciente?->nombre ?? '—',
                    'detalle' => 'Registró ' . $c->descripcion,
                    'hora' => $c->created_at->setTimezone('America/La_Paz')->format('H:i'),
                ]);
            }

            $operaciones = $operaciones->sortByDesc('fecha')->values();
        @endphp

        @if($operaciones->isEmpty())
            <p class="text-sm text-gray-400 py-6 text-center">Sin operaciones registradas el {{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}</p>
        @else
            <div class="space-y-3">
                @foreach($operaciones as $op)
                    <div class="flex items-center gap-4 p-3 rounded-lg {{ $op['tipo'] === 'evaluacion' ? 'bg-cyan-50' : 'bg-blue-50' }} border {{ $op['tipo'] === 'evaluacion' ? 'border-cyan-100' : 'border-blue-100' }}">
                        <div class="flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center {{ $op['tipo'] === 'evaluacion' ? 'bg-cyan-100 text-cyan-600' : 'bg-blue-100 text-blue-600' }}">
                            @if($op['tipo'] === 'evaluacion')
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                                </svg>
                            @else
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18M10 4v16M14 4v16"/>
                                </svg>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-800">
                                <span class="font-semibold">{{ $op['hora'] }}</span> -
                                <span class="font-medium">{{ $op['usuario'] }}</span>
                                {{ $op['detalle'] }} a
                                <span class="font-medium">{{ $op['paciente'] }}</span>
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
