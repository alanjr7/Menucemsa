@extends('layouts.app')

@section('title', 'Gestión de Habitaciones - Internación')

@section('content')
<div class="min-h-screen bg-gray-50 p-4 sm:p-6"
     x-data="habitacionesRegistro(@js($pacientes))">

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="mb-4 rounded-lg bg-green-100 px-4 py-3 text-green-800 text-sm">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 rounded-lg bg-red-100 px-4 py-3 text-red-800 text-sm">{{ session('error') }}</div>
    @endif

    <!-- Header -->
    <div class="mb-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Gestión de Habitaciones</h1>
                <p class="text-gray-600 mt-1 text-sm sm:text-base">Administrar habitaciones y registrar estadías de pacientes</p>
            </div>
            <div class="flex flex-col xs:flex-row gap-2 sm:flex-none">
                <a href="{{ route('internacion-staff.dashboard') }}"
                   class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center justify-center gap-2 transition-colors shadow-sm text-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Volver
                </a>
                <a href="{{ route('internacion-staff.habitaciones.create') }}"
                   class="bg-blue-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg flex items-center justify-center gap-2 transition-colors shadow-sm text-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Nueva Habitación
                </a>
            </div>
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-400 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_habitaciones'] }}</p>
                </div>
                <div class="p-3 bg-blue-100 rounded-full hidden sm:block">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-400 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Disponibles</p>
                    <p class="text-2xl font-bold text-green-600">{{ $stats['habitaciones_disponibles'] }}</p>
                </div>
                <div class="p-3 bg-green-100 rounded-full hidden sm:block">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-400 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Mantenimiento</p>
                    <p class="text-2xl font-bold text-red-600">{{ $stats['habitaciones_mantenimiento'] }}</p>
                </div>
                <div class="p-3 bg-red-100 rounded-full hidden sm:block">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-400 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Camas</p>
                    <p class="text-2xl font-bold text-indigo-600">{{ $stats['total_camas'] }}</p>
                </div>
                <div class="p-3 bg-indigo-100 rounded-full hidden sm:block">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Split View: Lista + Detalle (stack en móvil, split en desktop) -->
    <div class="flex flex-col lg:flex-row gap-4 lg:gap-6 lg:h-[calc(100vh-280px)] lg:min-h-[500px]">
        <!-- Panel Izquierdo: Lista de Habitaciones -->
        <div class="w-full lg:w-1/3 bg-white rounded-xl shadow-sm border border-gray-400 flex flex-col">
            <!-- Filtros -->
            <div class="p-4 border-b border-gray-400">
                <div class="flex gap-2 overflow-x-auto">
                    <button data-filtro="todas" class="tab-btn bg-indigo-600 text-white px-3 py-1.5 rounded-lg text-sm font-medium transition whitespace-nowrap">
                        Todas
                    </button>
                    <button data-filtro="disponible" class="tab-btn bg-gray-200 text-gray-700 px-3 py-1.5 rounded-lg text-sm font-medium transition whitespace-nowrap">
                        Disponibles
                    </button>
                    <button data-filtro="mantenimiento" class="tab-btn bg-gray-200 text-gray-700 px-3 py-1.5 rounded-lg text-sm font-medium transition whitespace-nowrap">
                        Mantenimiento
                    </button>
                </div>
            </div>
            <!-- Lista -->
            <div id="habitaciones-lista" class="flex-1 overflow-y-auto max-h-[40vh] lg:max-h-full">
                @forelse($habitaciones as $habitacion)
                    @php
                        $total = $habitacion->camas_count ?? 0;
                        $color = match($habitacion->estado) {
                            'disponible' => 'green',
                            'mantenimiento' => 'red',
                            default => 'gray',
                        };
                    @endphp
                    <div class="habitacion-item cursor-pointer border-b border-gray-200 hover:bg-gray-50 transition border-l-4 border-l-transparent"
                         data-id="{{ $habitacion->id }}"
                         data-estado="{{ $habitacion->estado }}"
                         onclick="window.habitacionApp.seleccionarHabitacion('{{ $habitacion->id }}')">
                        <div class="p-4">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="font-semibold text-gray-900">Habitación {{ $habitacion->id }}</h4>
                                <span class="px-2 py-0.5 text-xs rounded-full bg-{{ $color }}-100 text-{{ $color }}-800">
                                    {{ ucfirst($habitacion->estado) }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-500 truncate">{{ $habitacion->detalle ?: 'Sin detalle' }}</p>
                            <div class="mt-2 flex items-center text-xs text-gray-500 gap-3">
                                <span class="flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"/>
                                    </svg>
                                    {{ $total }} {{ $total === 1 ? 'cama' : 'camas' }}
                                </span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-gray-500">
                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <p>No hay habitaciones registradas</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Panel Derecho: Detalle de Habitación (renderizado server-side) -->
        <div class="w-full lg:w-2/3 bg-white rounded-xl shadow-sm border border-gray-400 overflow-y-auto relative min-h-[400px]">
            <!-- Estado vacío -->
            <div id="detalle-vacio" class="h-full min-h-[400px] flex items-center justify-center bg-slate-50">
                <div class="text-center p-8">
                    <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-slate-700 mb-2">Selecciona una habitación</h3>
                    <p class="text-sm text-slate-500 max-w-xs">Haz clic en una habitación de la lista para ver sus camas y registrar una estadía.</p>
                </div>
            </div>

            <!-- Detalles de todas las habitaciones (ocultos) -->
            <div id="habitacion-detalle-container">
                @foreach($habitaciones as $habitacion)
                    @php
                        $colorDetalle = match($habitacion->estado) {
                            'disponible' => 'green',
                            'mantenimiento' => 'red',
                            default => 'gray',
                        };
                        $isMantenimiento = $habitacion->estado === 'mantenimiento';
                    @endphp
                    <div id="detalle-{{ $habitacion->id }}" class="habitacion-detalle-panel hidden p-4 sm:p-6" data-estado="{{ $habitacion->estado }}">
                        <div class="flex items-center justify-between mb-4 pb-4 border-b">
                            <h2 class="text-lg sm:text-xl font-bold text-gray-900">Habitación {{ $habitacion->id }}</h2>
                            <span class="px-3 py-1 text-sm font-semibold rounded-full bg-{{ $colorDetalle }}-100 text-{{ $colorDetalle }}-800">
                                {{ ucfirst($habitacion->estado) }}
                            </span>
                        </div>

                        @if($isMantenimiento)
                            <div class="rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700 mb-4">
                                Habitación en mantenimiento. No se pueden registrar estadías hasta activarla.
                            </div>
                        @endif

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @forelse($habitacion->camas as $cama)
                                <div class="border-2 border-gray-200 bg-gray-50 rounded-lg p-4">
                                    <div class="flex items-center justify-between mb-3">
                                        <span class="font-semibold text-gray-900">Cama {{ $cama->nro }}</span>
                                        @if($cama->tipo)
                                            <span class="px-2 py-1 text-xs rounded-full bg-indigo-100 text-indigo-800">{{ $cama->tipo }}</span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-gray-500 mb-3">Bs. {{ number_format((float) $cama->precio_por_dia, 2) }} / día</p>
                                    <button
                                        @disabled($isMantenimiento)
                                        @click="abrir('{{ $habitacion->id }}', {{ $cama->id }}, 'Cama {{ $cama->nro }}{{ $cama->tipo ? ' (' . $cama->tipo . ')' : '' }}', {{ (float) $cama->precio_por_dia }})"
                                        class="w-full px-3 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-indigo-700 transition disabled:bg-gray-300 disabled:cursor-not-allowed">
                                        Registrar habitación
                                    </button>
                                </div>
                            @empty
                                <div class="col-span-full text-sm text-gray-500 py-6 text-center">
                                    Esta habitación no tiene camas registradas.
                                </div>
                            @endforelse
                        </div>

                        <div class="flex flex-col sm:flex-row gap-3 mt-6 pt-6 border-t">
                            <a href="/internacion-staff/habitaciones/{{ $habitacion->id }}/edit"
                               class="flex-1 flex items-center justify-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-indigo-700 transition text-sm">
                                Editar Habitación
                            </a>
                            <button onclick="window.habitacionApp.toggleMantenimiento('{{ $habitacion->id }}')"
                                    class="flex-1 flex items-center justify-center px-4 py-2 {{ $isMantenimiento ? 'bg-green-600 hover:bg-green-700' : 'bg-red-600 hover:bg-red-700' }} text-white rounded-lg transition text-sm">
                                {{ $isMantenimiento ? 'Activar' : 'Mantenimiento' }}
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Modal único: registrar estadía (cargo a cuenta, sin ocupación) --}}
    <div x-show="open" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4"
         @keydown.escape.window="cerrar()">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-md p-6 max-h-[90vh] overflow-y-auto" @click.stop>
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-base font-semibold text-gray-800">Registrar uso de habitación</h3>
                <button @click="cerrar()" class="text-gray-400 hover:text-gray-600">✕</button>
            </div>

            <p class="text-sm text-gray-500 mb-4">
                Habitación <strong x-text="habitacionId"></strong> — <span x-text="camaLabel"></span>
            </p>

            <form method="POST" action="{{ route('internacion-staff.habitaciones.registro-uso.store') }}">
                @csrf
                <input type="hidden" name="habitacion_id" :value="habitacionId">
                <input type="hidden" name="cama_id" :value="camaId">
                <input type="hidden" name="paciente_id" :value="pacienteId">

                <div class="space-y-4">
                    {{-- Selector de paciente (buscable) --}}
                    <div x-data="{ openList: false }" @click.outside="openList = false" class="relative">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Paciente <span class="text-red-500">*</span></label>
                        <input type="text" x-model="pacienteSearch" @focus="openList = true" @input="openList = true; pacienteId = ''"
                            placeholder="Buscar por nombre o carnet..."
                            class="w-full border rounded-lg px-3 py-2 text-sm">
                        <div x-show="openList && pacientesFiltrados.length" x-cloak
                             class="absolute z-10 mt-1 w-full bg-white border border-gray-200 rounded-lg shadow-lg max-h-48 overflow-y-auto">
                            <template x-for="p in pacientesFiltrados" :key="p.id">
                                <button type="button"
                                    @click="seleccionarPaciente(p); openList = false"
                                    class="w-full text-left px-3 py-2 text-sm hover:bg-indigo-50">
                                    <span class="font-medium text-gray-800" x-text="p.nombre"></span>
                                    <span class="text-gray-400 font-mono ml-1" x-text="'· ' + p.ci"></span>
                                </button>
                            </template>
                        </div>
                        <p x-show="pacienteSearch && !pacienteId" x-cloak class="text-xs text-amber-600 mt-1">
                            Selecciona un paciente de la lista.
                        </p>
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

                    <div x-show="dias > 0" x-cloak class="rounded-lg bg-indigo-50 border border-indigo-100 px-4 py-3 text-sm">
                        <div class="flex justify-between text-indigo-800">
                            <span>Días: <strong x-text="dias"></strong></span>
                        </div>
                    </div>

                    <div x-show="fechasInvalidas" x-cloak
                        class="rounded-lg bg-red-50 border border-red-200 px-4 py-2 text-sm text-red-700">
                        La fecha de fin debe ser posterior a la fecha de inicio.
                    </div>
                </div>

                <div class="mt-4 flex gap-3 justify-end">
                    <button type="button" @click="cerrar()"
                        class="px-4 py-2 border rounded-lg text-sm text-gray-600">Cancelar</button>
                    <button type="submit"
                        :disabled="!puedeGuardar"
                        :class="puedeGuardar
                            ? 'px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700'
                            : 'px-4 py-2 bg-gray-300 text-gray-500 rounded-lg text-sm cursor-not-allowed'">
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Scripts necesarios -->
<script>
    function habitacionesRegistro(pacientes) {
        return {
            open: false,
            habitacionId: '',
            camaId: '',
            camaLabel: '',
            precioDia: 0,
            pacientes: pacientes || [],
            pacienteId: '',
            pacienteSearch: '',
            fecha_inicio: '',
            fecha_fin: '',

            abrir(habitacionId, camaId, camaLabel, precio) {
                this.habitacionId = habitacionId;
                this.camaId = camaId;
                this.camaLabel = camaLabel;
                this.precioDia = precio;
                this.pacienteId = '';
                this.pacienteSearch = '';
                this.fecha_inicio = '';
                this.fecha_fin = '';
                this.open = true;
            },
            cerrar() {
                this.open = false;
            },
            seleccionarPaciente(p) {
                this.pacienteId = p.id;
                this.pacienteSearch = `${p.nombre} · ${p.ci}`;
            },
            get pacientesFiltrados() {
                const q = this.pacienteSearch.trim().toLowerCase();
                if (!q) return this.pacientes.slice(0, 50);
                return this.pacientes.filter(p =>
                    (p.nombre || '').toLowerCase().includes(q) ||
                    (p.ci || '').toLowerCase().includes(q)
                ).slice(0, 50);
            },
            get dias() {
                if (!this.fecha_inicio || !this.fecha_fin) return 0;
                const diff = (new Date(this.fecha_fin) - new Date(this.fecha_inicio)) / 3600000;
                if (diff < 8) return 0;
                return Math.max(1, Math.ceil(diff / 24));
            },
            get fechasInvalidas() {
                return this.fecha_inicio && this.fecha_fin &&
                    new Date(this.fecha_fin) <= new Date(this.fecha_inicio);
            },
            get puedeGuardar() {
                return this.pacienteId && this.camaId &&
                    this.fecha_inicio && this.fecha_fin &&
                    !this.fechasInvalidas && this.dias > 0;
            },
        };
    }
</script>
<script src="{{ asset('js/habitaciones/app.js') }}" defer></script>
@endsection
