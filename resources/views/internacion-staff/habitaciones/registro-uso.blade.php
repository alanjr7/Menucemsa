@extends('layouts.app')

@section('content')
<div class="w-full p-6 bg-gray-50/50 min-h-screen" x-data>

    <div class="flex justify-between items-end mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Habitaciones — Internación</h1>
            <p class="text-sm text-gray-500">Registrar uso de habitación y cama por paciente</p>
        </div>
        <a href="{{ route('internacion-staff.dashboard') }}"
            class="px-4 py-2 border border-gray-200 rounded-lg text-sm text-gray-600 hover:bg-gray-50">
            ← Volver al panel
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 rounded-lg bg-green-100 px-4 py-3 text-green-800 text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 rounded-lg bg-red-100 px-4 py-3 text-red-800 text-sm">{{ session('error') }}</div>
    @endif

    @php
        $habitacionesData = $habitaciones->mapWithKeys(fn($h) => [
            $h->id => [
                'id'    => $h->id,
                'camas' => $h->camas->map(fn($c) => [
                    'id'     => $c->id,
                    'nro'    => $c->nro,
                    'tipo'   => $c->tipo,
                    'precio' => (float) $c->precio_por_dia,
                ])->values()->toArray(),
            ]
        ])->toArray();
    @endphp

    {{-- Search --}}
    <form method="GET" class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 mb-6 flex flex-col md:flex-row gap-4">
        <div class="relative flex-1">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <input type="text" name="search" value="{{ request('search') }}"
                class="block w-full pl-10 pr-3 py-2.5 border border-gray-200 rounded-xl leading-5 bg-gray-50 placeholder-gray-400 focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 sm:text-sm"
                placeholder="Buscar por nombre, documento o código de registro...">
        </div>
        <button type="submit" class="flex items-center justify-center px-4 py-2.5 bg-blue-600 text-white rounded-xl hover:bg-blue-700 font-medium transition-colors shadow-sm">
            Buscar
        </button>
        @if(request('search'))
            <a href="{{ route('internacion-staff.habitaciones.registro-uso') }}" class="flex items-center justify-center px-4 py-2.5 border border-gray-200 rounded-xl text-gray-600 bg-white hover:bg-gray-50 font-medium transition-colors shadow-sm">
                Limpiar
            </a>
        @endif
    </form>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-white">
            <h3 class="text-gray-800 font-bold text-sm">Pacientes Registrados ({{ $pacientes->total() }})</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Nombre Completo</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Carnet</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Seguro</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Ingreso</th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($pacientes as $paciente)
                        @php $esTemporal = isset($paciente->is_temporal) && $paciente->is_temporal; @endphp
                        <tr class="hover:bg-gray-50/50 transition-colors {{ $esTemporal ? 'bg-red-50/30' : '' }}"
                            x-data="{
                                open: false,
                                habitacion_id: '',
                                cama_id: '',
                                fecha_inicio: '',
                                fecha_fin: '',
                                habitaciones: {{ json_encode($habitacionesData) }},
                                get camas() {
                                    return this.habitacion_id && this.habitaciones[this.habitacion_id]
                                        ? this.habitaciones[this.habitacion_id].camas
                                        : [];
                                },
                                get precioDia() {
                                    const c = this.camas.find(c => c.id == this.cama_id);
                                    return c ? c.precio : 0;
                                },
                                get dias() {
                                    if (!this.fecha_inicio || !this.fecha_fin) return 0;
                                    const diff = (new Date(this.fecha_fin) - new Date(this.fecha_inicio)) / 3600000;
                                    if (diff < 8) return 0;
                                    return Math.max(1, Math.ceil(diff / 24));
                                },
                                get costo() { return (this.dias * this.precioDia).toFixed(2); }
                            }">
                            {{-- Nombre --}}
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                <div class="flex items-center">
                                    @if($esTemporal)
                                        <span class="w-2 h-2 bg-red-500 rounded-full mr-2 animate-pulse"></span>
                                        <span class="font-medium text-red-700">{{ $paciente->nombre }}</span>
                                    @else
                                        {{ $paciente->nombre }}
                                    @endif
                                </div>
                            </td>
                            {{-- CI --}}
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-mono">{{ $paciente->ci }}</td>
                            {{-- Seguro --}}
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @if($esTemporal)
                                    <span class="text-xs text-red-500">Emergencia temporal</span>
                                @else
                                    {{ $paciente->seguro->nombre_empresa ?? 'Particular' }}
                                @endif
                            </td>
                            {{-- Tipo ingreso --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($esTemporal)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 border border-red-200">
                                        <span class="w-1.5 h-1.5 bg-red-500 rounded-full mr-1.5 animate-pulse"></span>
                                        Emergencia
                                    </span>
                                @else
                                    @php
                                        $tipo = $paciente->tipo_ingreso ?? 'otro';
                                        $colores = [
                                            'enfermeria'      => ['purple', 'Enfermería'],
                                            'consulta_externa'=> ['green',  'Consulta'],
                                            'emergencia'      => ['red',    'Emergencia'],
                                            'internacion'     => ['yellow', 'Internación'],
                                            'otro'            => ['gray',   'Otro'],
                                        ];
                                        [$color, $label] = $colores[$tipo] ?? ['gray', 'Otro'];
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $color }}-100 text-{{ $color }}-800 border border-{{ $color }}-200">
                                        <span class="w-1.5 h-1.5 bg-{{ $color }}-500 rounded-full mr-1.5 {{ $color === 'red' ? 'animate-pulse' : '' }}"></span>
                                        {{ $label }}
                                    </span>
                                @endif
                            </td>
                            {{-- Acciones --}}
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                @if($habitaciones->isEmpty())
                                    <span class="text-xs text-gray-400">Sin habitaciones disponibles</span>
                                @elseif($esTemporal)
                                    <span class="text-xs text-gray-400 cursor-not-allowed" title="Completar datos del paciente primero">Sin CI registrado</span>
                                @else
                                    <button @click="open = !open"
                                        class="inline-flex items-center px-3 py-1.5 border border-indigo-200 shadow-sm text-xs font-medium rounded-lg text-indigo-700 bg-indigo-50 hover:bg-indigo-100 transition-all">
                                        Registrar habitación
                                    </button>
                                @endif

                                {{-- Modal inline --}}
                                <div x-show="open" x-cloak
                                    class="fixed inset-0 z-50 flex items-center justify-center bg-black/40"
                                    @keydown.escape.window="open = false">
                                    <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4 p-6" @click.stop>
                                        <div class="flex justify-between items-center mb-4">
                                            <h3 class="text-base font-semibold text-gray-800">Registrar uso de habitación</h3>
                                            <button @click="open = false" class="text-gray-400 hover:text-gray-600">✕</button>
                                        </div>
                                        <p class="text-sm text-gray-500 mb-4">
                                            Paciente: <strong>{{ $paciente->nombre }}</strong> ({{ $paciente->ci }})
                                        </p>

                                        <form method="POST" action="{{ route('internacion-staff.habitaciones.registro-uso.store') }}">
                                            @csrf
                                            <input type="hidden" name="paciente_ci" value="{{ $paciente->ci }}">

                                            <div class="space-y-4">
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Habitación <span class="text-red-500">*</span></label>
                                                    <select name="habitacion_id" x-model="habitacion_id" required
                                                        class="w-full border rounded-lg px-3 py-2 text-sm">
                                                        <option value="">Seleccionar habitación...</option>
                                                        @foreach($habitaciones as $habitacion)
                                                            <option value="{{ $habitacion->id }}">
                                                                {{ $habitacion->id }}{{ $habitacion->detalle ? ' — ' . $habitacion->detalle : '' }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Cama <span class="text-red-500">*</span></label>
                                                    <select name="cama_id" x-model="cama_id" required
                                                        :disabled="!habitacion_id"
                                                        class="w-full border rounded-lg px-3 py-2 text-sm disabled:bg-gray-100">
                                                        <option value="">Seleccionar cama...</option>
                                                        <template x-for="cama in camas" :key="cama.id">
                                                            <option :value="cama.id" x-text="`Cama ${cama.nro}${cama.tipo ? ' (' + cama.tipo + ')' : ''} — Bs. ${cama.precio}/día`"></option>
                                                        </template>
                                                    </select>
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha y hora de inicio <span class="text-red-500">*</span></label>
                                                    <input type="datetime-local" name="fecha_inicio" x-model="fecha_inicio" required
                                                        class="w-full border rounded-lg px-3 py-2 text-sm">
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha y hora de fin <span class="text-red-500">*</span></label>
                                                    <input type="datetime-local" name="fecha_fin" x-model="fecha_fin" required
                                                        class="w-full border rounded-lg px-3 py-2 text-sm">
                                                </div>
                                                <div x-show="dias > 0" class="rounded-lg bg-indigo-50 border border-indigo-100 px-4 py-3 text-sm">
                                                    <div class="flex justify-between text-indigo-800">
                                                        <span>Días: <strong x-text="dias"></strong></span>
                                                        <span>Costo estimado: <strong>Bs. <span x-text="costo"></span></strong></span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="mt-6 flex gap-3 justify-end">
                                                <button type="button" @click="open = false"
                                                    class="px-4 py-2 border rounded-lg text-sm text-gray-600">Cancelar</button>
                                                <button type="submit"
                                                    class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700">Guardar</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                    <p class="text-lg font-medium text-gray-600 mb-2">No se encontraron pacientes</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($pacientes->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/30">
                {{ $pacientes->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
