@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto px-4 py-6">

    {{-- Header --}}
    <div class="mb-5">
        <a href="{{ route('admin.episodios.paciente', $episodio->paciente_ci) }}" class="text-sm text-blue-600 hover:underline">
            &larr; Episodios de {{ $episodio->paciente?->nombre }}
        </a>
        <div class="flex items-center gap-3 mt-2 flex-wrap">
            <h1 class="text-xl font-semibold text-gray-800">Episodio #{{ $episodio->numero }}</h1>
            @if($episodio->estado === 'abierto')
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">
                    <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>
                    Abierto
                </span>
            @else
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500">
                    Cerrado
                </span>
            @endif
            <div class="ml-auto flex items-center gap-2">
                <a href="{{ route('admin.episodios.pdf', $episodio->id) }}" target="_blank"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-md border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 0 0 2-2v-4a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v4a2 2 0 0 0 2 2h2m2 4h6a2 2 0 0 0 2-2v-4a2 2 0 0 0-2-2H9a2 2 0 0 0-2 2v4a2 2 0 0 0 2 2zm1-4h4v4H10v-4z"/>
                    </svg>
                    PDF
                </a>
                <a href="{{ route('admin.episodios.excel', $episodio->id) }}"
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-md border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0-3-3m3 3 3-3M3 17V7a2 2 0 0 1 2-2h6l2 2h6a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                    </svg>
                    Excel
                </a>
            </div>
        </div>
        <p class="text-sm text-gray-500 mt-1">
            {{ $episodio->fecha_apertura->format('d/m/Y H:i') }}
            @if($episodio->fecha_cierre)
                &rarr; {{ $episodio->fecha_cierre->format('d/m/Y H:i') }}
                <span class="text-gray-400">({{ $episodio->duracion }})</span>
            @else
                <span class="text-gray-400">&mdash; en curso</span>
            @endif
        </p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

        {{-- Columna izquierda --}}
        <div class="space-y-4">

            {{-- Paciente --}}
            <div class="bg-white rounded-lg border border-gray-800 p-4">
                <p class="text-xs font-medium text-gray-800 uppercase tracking-wide mb-2">Paciente</p>
                <p class="text-sm font-semibold text-gray-800">{{ $episodio->paciente?->nombre ?? '—' }}</p>
                <p class="text-sm text-gray-500">CI: {{ $episodio->paciente_ci }}</p>
                @if($episodio->tipo_ingreso)
                    <p class="text-sm text-gray-500 mt-0.5">Tipo: {{ ucfirst($episodio->tipo_ingreso) }}</p>
                @endif
                @if($episodio->motivo_cierre)
                    <p class="text-sm text-gray-500 mt-0.5">Motivo cierre: {{ $episodio->motivo_cierre }}</p>
                @endif
            </div>

            {{-- Emergencias --}}
            @if($episodio->emergencias->isNotEmpty())
            <div class="bg-white rounded-lg border border-gray-800 p-4">
                <p class="text-xs font-medium text-gray-800 uppercase tracking-wide mb-3">
                    Emergencias
                    <span class="ml-1.5 text-blue-600">{{ $episodio->emergencias->count() }}</span>
                </p>
                @foreach($episodio->emergencias as $em)
                <div class="py-2 border-b border-gray-800 last:border-0">
                    <p class="text-sm font-medium text-gray-700">{{ $em->code }}</p>
                    <p class="text-sm text-gray-500">{{ ($em->admission_date ?? $em->created_at)?->format('d/m/Y H:i') }}</p>
                    @if($em->ubicacion_label)
                        <p class="text-xs text-gray-400 mt-0.5">{{ $em->ubicacion_label }}</p>
                    @endif
                </div>
                @endforeach
            </div>
            @endif

            {{-- Hospitalizaciones --}}
            @if($episodio->hospitalizaciones->isNotEmpty())
            <div class="bg-white rounded-lg border border-gray-200 p-4">
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wide mb-3">
                    Hospitalizaciones
                    <span class="ml-1.5 text-blue-600">{{ $episodio->hospitalizaciones->count() }}</span>
                </p>
                @foreach($episodio->hospitalizaciones as $hosp)
                <div class="py-2 border-b border-gray-100 last:border-0">
                    <p class="text-sm font-medium text-gray-700">
                        {{ $hosp->fecha_ingreso->format('d/m/Y') }}
                        @if($hosp->fecha_alta)
                            &rarr; {{ $hosp->fecha_alta->format('d/m/Y') }}
                        @else
                            <span class="text-gray-400">— en curso</span>
                        @endif
                    </p>
                    <p class="text-sm text-gray-500">{{ $hosp->medico?->user?->name ?? 'Sin médico' }}</p>
                </div>
                @endforeach
            </div>
            @endif

        </div>

        {{-- Columna derecha --}}
        <div class="space-y-4">

            {{-- Evaluaciones --}}
            <div class="bg-white rounded-lg border border-gray-200 p-4">
                <p class="text-xs font-medium text-gray-800 uppercase tracking-wide mb-3">
                    Evaluaciones
                    <span class="ml-1.5 text-blue-600">{{ $episodio->evaluaciones->count() }}</span>
                </p>
                @forelse($episodio->evaluaciones as $eval)
                <div class="py-3 border-b border-gray-100 last:border-0">
                    <div class="flex justify-between items-start mb-1">
                        <span class="text-sm font-medium text-gray-700 uppercase">{{ $eval->area }}</span>
                        <span class="text-xs text-gray-400">{{ $eval->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <p class="text-xs text-gray-500 mb-1">{{ $eval->user?->name ?? '—' }}</p>
                    @if($eval->observaciones)
                        <p class="text-sm text-gray-600">{{ $eval->observaciones }}</p>
                    @endif
                    @if($eval->signos_vitales)
                        <div class="mt-2 flex flex-wrap gap-1.5">
                            @foreach($eval->signos_vitales as $key => $val)
                                @if($val)
                                <span class="text-xs bg-gray-50 border border-gray-200 rounded px-2 py-0.5 text-gray-600">
                                    {{ str_replace('_', ' ', $key) }}: {{ $val }}
                                </span>
                                @endif
                            @endforeach
                        </div>
                    @endif
                    @if($eval->items->isNotEmpty())
                        <div class="mt-2 pt-2 border-t border-gray-50 space-y-0.5">
                            @foreach($eval->items as $item)
                            <div class="flex justify-between text-xs text-gray-500">
                                <span>{{ ucfirst($item->tipo) }} — {{ $item->nombre_snapshot }}</span>
                                <span>x{{ $item->cantidad }}</span>
                            </div>
                            @endforeach
                        </div>
                    @endif
                </div>
                @empty
                <p class="text-sm text-gray-400">Sin evaluaciones.</p>
                @endforelse
            </div>

            {{-- Cuentas por Cobrar --}}
            <div class="bg-white rounded-lg border border-gray-200 p-4">
                <p class="text-xs font-medium text-gray-800 uppercase tracking-wide mb-3">
                    Cuentas por Cobrar
                    <span class="ml-1.5 text-blue-600">{{ $episodio->cuentasCobro->count() }}</span>
                </p>
                @forelse($episodio->cuentasCobro as $cuenta)
                <div class="py-3 border-b border-gray-100 last:border-0">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm font-medium text-gray-700">{{ $cuenta->tipo_atencion_label }}</span>
                        <span class="text-xs px-2 py-0.5 rounded-full font-medium
                            @if($cuenta->estado === 'pagado') bg-green-100 text-green-700
                            @elseif($cuenta->estado === 'parcial') bg-yellow-100 text-yellow-700
                            @else bg-red-50 text-red-600 @endif">
                            {{ $cuenta->estado_label }}
                        </span>
                    </div>
                    <div class="space-y-0.5 text-sm">
                        <div class="flex justify-between text-gray-600">
                            <span>Total</span>
                            <span class="font-medium">Bs. {{ number_format($cuenta->total_calculado, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-gray-500">
                            <span>Pagado</span>
                            <span>Bs. {{ number_format($cuenta->total_pagado, 2) }}</span>
                        </div>
                        @if($cuenta->saldo_pendiente > 0)
                        <div class="flex justify-between text-red-600 font-medium pt-1 border-t border-gray-100">
                            <span>Saldo</span>
                            <span>Bs. {{ number_format($cuenta->saldo_pendiente, 2) }}</span>
                        </div>
                        @endif
                    </div>
                    @if($cuenta->detalles->isNotEmpty())
                    <div class="mt-2 pt-2 border-t border-gray-50 space-y-0.5">
                        @foreach($cuenta->detalles->take(5) as $det)
                        <div class="flex justify-between text-xs text-gray-500">
                            <span class="truncate max-w-[200px]">{{ $det->descripcion }}</span>
                            <span class="ml-2 shrink-0">Bs. {{ number_format($det->subtotal, 2) }}</span>
                        </div>
                        @endforeach
                        @if($cuenta->detalles->count() > 5)
                            <p class="text-xs text-gray-400 pt-0.5">+{{ $cuenta->detalles->count() - 5 }} ítems más</p>
                        @endif
                    </div>
                    @endif
                </div>
                @empty
                <p class="text-sm text-gray-400">Sin cuentas.</p>
                @endforelse
            </div>

        </div>
    </div>
</div>
@endsection
