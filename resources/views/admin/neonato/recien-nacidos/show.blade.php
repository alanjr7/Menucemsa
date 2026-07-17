@extends('layouts.app')

@section('content')
<div class="p-6 bg-gray-50/50 min-h-screen">

    <div class="flex justify-between items-start mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">{{ $neonato->nombre_display }}</h1>
            <p class="text-sm text-gray-500">{{ $neonato->code }} · {{ $neonato->paciente?->temp_code }}</p>
        </div>
        <a href="{{ route('admin.neonato.recien-nacidos') }}"
            class="px-4 py-2 border border-gray-200 rounded-xl text-sm text-gray-600 hover:bg-gray-50">← Volver</a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Datos clínicos --}}
        <div class="lg:col-span-1 space-y-4">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-bold text-gray-800 text-sm mb-4">Datos del nacimiento</h3>
                <dl class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Estado</dt>
                        <dd>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                bg-{{ $neonato->status_color }}-100 text-{{ $neonato->status_color }}-700">
                                {{ $neonato->status_label }}
                            </span>
                        </dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Sexo</dt>
                        <dd class="font-medium text-gray-800">{{ $neonato->sexo === 'M' ? 'Masculino' : ($neonato->sexo === 'F' ? 'Femenino' : '—') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Nacimiento</dt>
                        <dd class="font-medium text-gray-800">{{ $neonato->fecha_hora_nacimiento?->setTimezone('America/La_Paz')->format('d/m/Y H:i') ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Tipo de parto</dt>
                        <dd class="font-medium capitalize text-gray-800">{{ $neonato->tipo_parto ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Peso</dt>
                        <dd class="font-medium text-gray-800">{{ $neonato->peso ? $neonato->peso . ' g' : '—' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Talla</dt>
                        <dd class="font-medium text-gray-800">{{ $neonato->talla ? $neonato->talla . ' cm' : '—' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">P. Cefálico</dt>
                        <dd class="font-medium text-gray-800">{{ $neonato->perimetro_cefalico ? $neonato->perimetro_cefalico . ' cm' : '—' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Apgar 1'/5'</dt>
                        <dd class="font-medium text-gray-800">{{ $neonato->apgar1 ?? '—' }} / {{ $neonato->apgar5 ?? '—' }}</dd>
                    </div>
                </dl>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-bold text-gray-800 text-sm mb-4">Vínculo materno</h3>
                <p class="text-sm text-gray-700 font-medium">{{ $neonato->madre_nombre ?? 'Sin madre registrada' }}</p>
                @if($neonato->madre_ci)
                    <p class="text-xs text-gray-400">CI: {{ $neonato->madre_ci }}</p>
                @endif
            </div>
        </div>

        {{-- Evaluaciones + Cunas --}}
        <div class="lg:col-span-2 space-y-6">

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="font-bold text-gray-800 text-sm">Evaluaciones ({{ $evaluaciones->count() }})</h3>
                </div>
                <div class="divide-y divide-gray-50">
                    @forelse($evaluaciones as $ev)
                        <div class="px-6 py-3">
                            <div class="flex justify-between">
                                <p class="text-sm text-gray-700 font-medium">Evaluación — {{ $ev->user?->name }}</p>
                                <p class="text-xs text-gray-400">{{ $ev->created_at->setTimezone('America/La_Paz')->format('d/m/Y H:i') }}</p>
                            </div>
                            @if($ev->observaciones)
                                <p class="text-xs text-gray-500 mt-1">{{ $ev->observaciones }}</p>
                            @endif
                            @if($ev->items->isNotEmpty())
                                <p class="text-xs text-gray-400 mt-1">{{ $ev->items->count() }} ítem(s)</p>
                            @endif
                        </div>
                    @empty
                        <p class="px-6 py-6 text-sm text-center text-gray-400">Sin evaluaciones</p>
                    @endforelse
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="font-bold text-gray-800 text-sm">Uso de cunas ({{ $usosCunas->count() }})</h3>
                </div>
                <div class="divide-y divide-gray-50">
                    @forelse($usosCunas as $uso)
                        <div class="px-6 py-3 flex justify-between text-sm">
                            <div>
                                <p class="font-medium text-gray-800">{{ $uso->camilla?->nombre }}</p>
                                <p class="text-xs text-gray-400">
                                    {{ \Carbon\Carbon::parse($uso->fecha_inicio)->setTimezone('America/La_Paz')->format('d/m/Y H:i') }}
                                    → {{ \Carbon\Carbon::parse($uso->fecha_fin)->setTimezone('America/La_Paz')->format('H:i') }}
                                </p>
                            </div>
                            <p class="font-bold text-gray-700">Bs. {{ $uso->costo_calculado }}</p>
                        </div>
                    @empty
                        <p class="px-6 py-6 text-sm text-center text-gray-400">Sin uso de cunas</p>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
