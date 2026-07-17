@extends('layouts.app')

@section('content')
<div class="p-6 bg-gray-50/50 min-h-screen" x-data="{ editStatus: false }">

    <div class="flex justify-between items-start mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">{{ $neonato->nombre_display }}</h1>
            <p class="text-sm text-gray-500">{{ $neonato->code }} · {{ $neonato->identificador }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('neonato.evaluar', $neonato->id) }}"
                class="px-4 py-2 bg-pink-600 text-white rounded-xl text-sm hover:bg-pink-700 shadow-sm font-medium">Evaluar</a>
            <a href="{{ route('neonato.historial', $neonato->id) }}"
                class="px-4 py-2 border border-gray-200 rounded-xl text-sm text-gray-600 hover:bg-gray-50">Historial</a>
            <a href="{{ route('neonato.index') }}"
                class="px-4 py-2 border border-gray-200 rounded-xl text-sm text-gray-600 hover:bg-gray-50">← Volver</a>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 rounded-xl bg-green-100 px-4 py-3 text-green-800 text-sm">{{ session('success') }}</div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Datos clínicos --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-bold text-gray-800 text-sm mb-4 pb-3 border-b border-gray-100">Datos del nacimiento</h3>
            <dl class="space-y-3">
                @php
                    $filas = [
                        'Sexo'         => $neonato->sexo === 'M' ? 'Masculino' : ($neonato->sexo === 'F' ? 'Femenino' : '—'),
                        'Nacimiento'   => $neonato->fecha_hora_nacimiento?->setTimezone('America/La_Paz')->format('d/m/Y H:i') ?? '—',
                        'Tipo de parto'=> ucfirst($neonato->tipo_parto ?? '—'),
                        'Peso'         => $neonato->peso ? $neonato->peso . ' g' : '—',
                        'Talla'        => $neonato->talla ? $neonato->talla . ' cm' : '—',
                        'P. cefálico'  => $neonato->perimetro_cefalico ? $neonato->perimetro_cefalico . ' cm' : '—',
                        "Apgar 1'/5'"  => ($neonato->apgar1 ?? '—') . ' / ' . ($neonato->apgar5 ?? '—'),
                        'Ingreso'      => $neonato->admission_date?->setTimezone('America/La_Paz')->format('d/m/Y H:i') ?? '—',
                        'Alta'         => $neonato->discharge_date?->setTimezone('America/La_Paz')->format('d/m/Y H:i') ?? '—',
                        'Registrado por' => $neonato->user?->name ?? '—',
                    ];
                @endphp
                @foreach($filas as $label => $valor)
                    <div class="flex justify-between text-sm">
                        <dt class="text-gray-500">{{ $label }}</dt>
                        <dd class="font-medium text-gray-800">{{ $valor }}</dd>
                    </div>
                @endforeach

                @if($neonato->observaciones)
                    <div class="pt-3 border-t border-gray-100">
                        <p class="text-xs text-gray-500 mb-1">Observaciones</p>
                        <p class="text-sm text-gray-700">{{ $neonato->observaciones }}</p>
                    </div>
                @endif
            </dl>
        </div>

        {{-- Madre + Estado --}}
        <div class="space-y-4">

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-bold text-gray-800 text-sm mb-3 pb-3 border-b border-gray-100">Vínculo materno</h3>
                @if($neonato->madre_nombre || $neonato->madre_ci)
                    <p class="text-sm font-semibold text-gray-800">{{ $neonato->madre_nombre ?? 'Sin nombre' }}</p>
                    <p class="text-xs text-gray-400 mt-1">CI: {{ $neonato->madre_ci ?? '—' }}</p>
                @else
                    <p class="text-sm text-gray-400">Sin madre vinculada</p>
                @endif
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex justify-between items-center mb-3 pb-3 border-b border-gray-100">
                    <h3 class="font-bold text-gray-800 text-sm">Estado actual</h3>
                    <button @click="editStatus = !editStatus"
                        class="text-xs text-pink-600 hover:underline">Cambiar</button>
                </div>

                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                    bg-{{ $neonato->status_color }}-100 text-{{ $neonato->status_color }}-700 border border-{{ $neonato->status_color }}-200">
                    {{ $neonato->status_label }}
                </span>

                <div x-show="editStatus" x-cloak class="mt-4">
                    <form method="POST" action="{{ route('neonato.status', $neonato->id) }}">
                        @csrf @method('PATCH')
                        <select name="status" class="w-full border rounded-lg px-3 py-2 text-sm mb-3">
                            @foreach($statuses as $val => $label)
                                <option value="{{ $val }}" {{ $neonato->status === $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        <div class="flex gap-2">
                            <button type="submit" class="px-4 py-2 bg-pink-600 text-white rounded-lg text-sm hover:bg-pink-700">Actualizar</button>
                            <button type="button" @click="editStatus = false" class="px-4 py-2 border rounded-lg text-sm">Cancelar</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
