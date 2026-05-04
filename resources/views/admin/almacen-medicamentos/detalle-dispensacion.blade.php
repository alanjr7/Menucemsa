@extends('layouts.app')

@section('title', 'Detalle de Dispensación #' . $dispensacion->id)

@section('content')
<div class="min-h-screen bg-gray-50 p-6">

    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.almacen-medicamentos.historial') }}" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <h1 class="text-3xl font-bold text-gray-900">Dispensación <span class="text-blue-600">#{{ $dispensacion->id }}</span></h1>
            @php $tieneEntrega = $dispensacion->detalles->flatMap->entregaDetalles->isNotEmpty(); @endphp
            @if($tieneEntrega)
                <span class="px-3 py-1 bg-green-100 text-green-800 text-sm font-semibold rounded-full">Entregado al paciente</span>
            @else
                <span class="px-3 py-1 bg-yellow-100 text-yellow-800 text-sm font-semibold rounded-full">Pendiente de asignar paciente</span>
            @endif
        </div>
        <a href="{{ route('admin.almacen-medicamentos.historial') }}"
           class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 text-sm">
            Volver al Historial
        </a>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg text-green-800 font-medium">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">
            <ul class="list-disc list-inside space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Columna izquierda (2/3) -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Cabecera de dispensación -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-blue-50">
                    <h2 class="text-lg font-semibold text-gray-900">Información de la Dispensación</h2>
                </div>
                <div class="p-6 grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-medium">Fecha</p>
                        <p class="text-sm font-semibold text-gray-900 mt-1">{{ $dispensacion->fecha_dispensacion->format('d/m/Y H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-medium">Origen</p>
                        <p class="text-sm font-semibold text-gray-900 mt-1">{{ ucfirst($dispensacion->ubicacion_origen) }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-medium">Destino</p>
                        <span class="mt-1 inline-block px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">
                            {{ $dispensacion->ubicacion_destino_label }}
                        </span>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-medium">Dispensado por</p>
                        <p class="text-sm font-semibold text-gray-900 mt-1">{{ $dispensacion->dispensadoPor->name ?? 'N/A' }}</p>
                    </div>
                    @if($dispensacion->recibido_por)
                    <div class="col-span-2">
                        <p class="text-xs text-gray-500 uppercase font-medium">Recibido por (área)</p>
                        <p class="text-sm font-semibold text-gray-900 mt-1">{{ $dispensacion->recibido_por }}</p>
                    </div>
                    @endif
                    @if($dispensacion->observaciones)
                    <div class="col-span-4">
                        <p class="text-xs text-gray-500 uppercase font-medium">Observaciones</p>
                        <p class="text-sm text-gray-700 mt-1">{{ $dispensacion->observaciones }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Detalles (lotes dispensados) -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-green-50">
                    <h2 class="text-lg font-semibold text-gray-900">Ítems Dispensados</h2>
                </div>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Medicamento/Insumo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Lote</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vencimiento</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cantidad</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Entregado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($dispensacion->detalles as $detalle)
                        @php
                            $catalogo = $detalle->lote->catalogo;
                            $lote = $detalle->lote;
                            $entregado = $detalle->entregaDetalles->sum('cantidad');
                        @endphp
                        <tr>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $catalogo->nombre ?? 'N/A' }}</div>
                                <span class="text-xs px-1.5 py-0.5 rounded {{ ($catalogo->tipo ?? '') == 'medicamento' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-700' }}">
                                    {{ ucfirst($catalogo->tipo ?? '') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700 font-mono">{{ $lote->codigo_lote ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $lote->fecha_vencimiento?->format('d/m/Y') ?? '-' }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ $detalle->cantidad }} {{ $catalogo->unidad_medida ?? '' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($entregado >= $detalle->cantidad)
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Completo</span>
                                @elseif($entregado > 0)
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Parcial ({{ $entregado }})</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-600">Pendiente</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Entregas a paciente -->
            @php $entregas = $dispensacion->detalles->flatMap->entregaDetalles->map->entrega->unique('id')->filter(); @endphp
            @if($entregas->isNotEmpty())
            <div class="bg-white rounded-xl shadow-sm border border-green-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-green-100 bg-green-50">
                    <h2 class="text-lg font-semibold text-gray-900">Entregas a Paciente</h2>
                </div>
                @foreach($entregas as $entrega)
                <div class="p-6 border-b border-gray-100 last:border-0">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-900">{{ $entrega->paciente->nombre ?? 'N/A' }}</p>
                            <p class="text-xs text-gray-500">C.I. {{ $entrega->paciente_ci }} — {{ $entrega->fecha_entrega->format('d/m/Y H:i') }}</p>
                        </div>
                        <span class="ml-auto text-xs text-gray-500">Por: {{ $entrega->entregadoPor->name ?? 'N/A' }}</span>
                    </div>
                    <div class="space-y-1">
                        @foreach($entrega->detalles as $ed)
                        <div class="text-xs text-gray-600 flex gap-2">
                            <span class="font-medium">{{ $ed->dispensacionDetalle->lote->catalogo->nombre ?? 'N/A' }}:</span>
                            <span>{{ $ed->cantidad }} {{ $ed->dispensacionDetalle->lote->catalogo->unidad_medida ?? '' }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            <!-- Formulario registrar paciente -->
            @if(!$tieneEntrega)
            <div class="bg-white rounded-xl shadow-sm border border-yellow-200 overflow-hidden"
                 x-data="{
                    ciInput: '',
                    paciente: null,
                    buscando: false,
                    error: '',
                    cantidades: {},
                    buscarPaciente() {
                        if (!this.ciInput || String(this.ciInput).length < 3) { this.paciente = null; this.error = ''; return; }
                        this.buscando = true; this.error = ''; this.paciente = null;
                        fetch('/api/buscar-paciente?ci=' + encodeURIComponent(this.ciInput), {
                            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                        })
                        .then(r => r.ok ? r.json() : Promise.reject(r.status))
                        .then(data => { this.buscando = false; if (data && data.ci) { this.paciente = data; } else { this.error = 'No se encontró ningún paciente con ese C.I.'; } })
                        .catch(() => { this.buscando = false; this.error = 'No se encontró ningún paciente con ese C.I.'; });
                    }
                 }">
                <div class="px-6 py-4 border-b border-yellow-100 bg-yellow-50">
                    <h2 class="text-lg font-semibold text-gray-900">Registrar entrega al paciente</h2>
                </div>
                <div class="p-6">
                    <div class="mb-5">
                        <label class="block text-sm font-medium text-gray-700 mb-2">C.I. del paciente</label>
                        <div class="flex gap-3">
                            <input type="number" x-model="ciInput" @input.debounce.500ms="buscarPaciente()"
                                   placeholder="Ej: 12345678"
                                   class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm">
                            <button type="button" @click="buscarPaciente()"
                                    class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm hover:bg-gray-200">Buscar</button>
                        </div>
                    </div>

                    <div x-show="buscando" class="text-sm text-gray-500 mb-4">Buscando...</div>
                    <div x-show="error && !buscando" class="p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700 mb-4" x-text="error"></div>

                    <div x-show="paciente && !buscando" class="p-4 bg-blue-50 border border-blue-200 rounded-lg mb-5">
                        <p class="font-semibold text-gray-900" x-text="paciente ? paciente.nombre : ''"></p>
                        <p class="text-xs text-gray-500">C.I. <span x-text="paciente ? paciente.ci : ''"></span></p>
                    </div>

                    <form method="POST"
                          action="{{ route('admin.almacen-medicamentos.registrar-paciente', $dispensacion->id) }}"
                          x-show="paciente && !buscando">
                        @csrf
                        <input type="hidden" name="paciente_ci" :value="paciente ? paciente.ci : ''">

                        <div class="mb-4">
                            <p class="text-sm font-medium text-gray-700 mb-2">Cantidades a entregar por ítem:</p>
                            @foreach($dispensacion->detalles as $i => $detalle)
                            @php
                                $yaEntregado = $detalle->entregaDetalles->sum('cantidad');
                                $pendiente = max(0, $detalle->cantidad - $yaEntregado);
                                $catalogo = $detalle->lote->catalogo;
                            @endphp
                            <div class="flex items-center gap-3 mb-2">
                                <input type="hidden" name="detalles[{{ $i }}][detalle_id]" value="{{ $detalle->id }}">
                                <span class="text-sm text-gray-700 flex-1">
                                    {{ $catalogo->nombre ?? 'N/A' }}
                                    <span class="text-gray-400">(máx. {{ $pendiente }} {{ $catalogo->unidad_medida ?? '' }})</span>
                                </span>
                                <input type="number" name="detalles[{{ $i }}][cantidad]"
                                       min="1" max="{{ $pendiente }}" value="{{ $pendiente }}"
                                       class="w-24 px-3 py-1.5 border border-gray-300 rounded-lg text-sm"
                                       @if($pendiente == 0) disabled @endif>
                            </div>
                            @endforeach
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Observaciones</label>
                            <textarea name="observaciones" rows="2"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm"></textarea>
                        </div>

                        <button type="submit"
                                class="w-full px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg text-sm">
                            Confirmar entrega al paciente
                        </button>
                    </form>
                </div>
            </div>
            @endif

        </div>

        <!-- Columna derecha (resumen) -->
        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="text-base font-semibold text-gray-900">Resumen</h2>
                </div>
                <div class="p-5 space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">ID</span>
                        <span class="font-mono text-gray-900">#{{ $dispensacion->id }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Fecha</span>
                        <span class="text-gray-900">{{ $dispensacion->fecha_dispensacion->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Destino</span>
                        <span class="font-medium text-purple-700">{{ $dispensacion->ubicacion_destino_label }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Ítems</span>
                        <span class="font-medium text-gray-900">{{ $dispensacion->detalles->count() }}</span>
                    </div>
                    <div class="border-t border-gray-100 pt-3 flex justify-between">
                        <span class="text-gray-500">Estado</span>
                        @if($tieneEntrega)
                            <span class="text-green-700 font-medium">Entregado</span>
                        @else
                            <span class="text-yellow-600 font-medium">Pendiente</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
