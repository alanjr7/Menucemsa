@extends('layouts.app')

@section('content')
<div class="w-full p-6 bg-gray-50/50 min-h-screen">

    <div class="mb-6">
        <a href="{{ route('admin.episodios.index') }}" class="text-sm text-blue-600 hover:underline">&larr; Volver a episodios</a>
        <h1 class="text-2xl font-bold text-gray-800 mt-1">{{ $paciente->nombre }}</h1>
        <p class="text-sm text-gray-500">CI: {{ $paciente->ci }} &mdash; {{ $episodios->count() }} episodio{{ $episodios->count() !== 1 ? 's' : '' }} registrado{{ $episodios->count() !== 1 ? 's' : '' }}</p>
    </div>

    @if($episodios->isEmpty())
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center text-gray-400">
            Este paciente no tiene episodios registrados.
        </div>
    @else
    <div class="space-y-4">
        @foreach($episodios as $episodio)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="flex-shrink-0 w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center">
                        <span class="text-blue-700 font-bold text-lg">{{ $episodio->numero }}</span>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-semibold text-gray-800">Episodio {{ $episodio->numero }}</span>
                            @if($episodio->estado === 'abierto')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>
                                    Abierto
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500">
                                    Cerrado
                                </span>
                            @endif
                            @if($episodio->tipo_ingreso)
                                <span class="px-2 py-0.5 rounded text-xs bg-blue-50 text-blue-600">
                                    {{ ucfirst($episodio->tipo_ingreso) }}
                                </span>
                            @endif
                        </div>
                        <div class="text-sm text-gray-500 mt-0.5">
                            <span>{{ $episodio->fecha_apertura->format('d/m/Y H:i') }}</span>
                            @if($episodio->fecha_cierre)
                                <span class="mx-1">&rarr;</span>
                                <span>{{ $episodio->fecha_cierre->format('d/m/Y H:i') }}</span>
                                <span class="ml-2 text-gray-400">({{ $episodio->duracion }})</span>
                            @else
                                <span class="ml-2 text-green-600">(en curso)</span>
                            @endif
                        </div>
                        <div class="text-xs text-gray-400 mt-0.5">
                            Abierto por: {{ $episodio->creadoPor?->name ?? '—' }}
                            @if($episodio->cerradoPor)
                                &nbsp;&bull;&nbsp; Cerrado por: {{ $episodio->cerradoPor->name }}
                            @endif
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-6 text-center text-sm">
                    <div>
                        <p class="text-2xl font-bold text-gray-700">{{ $episodio->evaluaciones_count }}</p>
                        <p class="text-xs text-gray-400">Evaluaciones</p>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-700">{{ $episodio->historial_medico_count }}</p>
                        <p class="text-xs text-gray-400">Historial</p>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-700">{{ $episodio->cuentas_cobro_count }}</p>
                        <p class="text-xs text-gray-400">Cuentas</p>
                    </div>
                    <a href="{{ route('admin.episodios.show', $episodio->id) }}"
                       class="ml-4 px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors whitespace-nowrap">
                        Ver detalle
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>
@endsection
