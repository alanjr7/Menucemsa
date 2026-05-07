@extends('layouts.app')

@section('content')
<div class="w-full p-6 bg-gray-50/50 min-h-screen" x-data>

    <div class="flex justify-between items-end mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Camillas — Emergencia</h1>
            <p class="text-sm text-gray-500">Registrar uso de camilla por paciente</p>
        </div>
        <a href="{{ route('emergency-staff.dashboard') }}"
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

    @php $preciosCamillas = $camillas->pluck('precio_por_hora', 'id')->toArray(); @endphp

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
            <a href="{{ route('emergency-staff.camillas.index') }}" class="flex items-center justify-center px-4 py-2.5 border border-gray-200 rounded-xl text-gray-600 bg-white hover:bg-gray-50 font-medium transition-colors shadow-sm">
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
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Código</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Nombre Completo</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Carnet</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Seguro</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Ingreso</th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($pacientes as $paciente)
                        @php
                            $esTemporal = isset($paciente->is_temporal) && $paciente->is_temporal;

                            if (!$esTemporal) {
                                $tipoIngreso = $paciente->tipo_ingreso ?? 'otro';
                                switch($tipoIngreso) {
                                    case 'enfermeria':      $ingresoColor = 'purple'; $ingresoLabel = 'Enfermería'; break;
                                    case 'consulta_externa': $ingresoColor = 'green';  $ingresoLabel = 'Consulta'; break;
                                    case 'emergencia':      $ingresoColor = 'red';    $ingresoLabel = 'Emergencia'; break;
                                    case 'internacion':     $ingresoColor = 'yellow'; $ingresoLabel = 'Internación'; break;
                                    default:                $ingresoColor = 'gray';   $ingresoLabel = 'Otro'; break;
                                }
                                $cajaId = $paciente->consultas->first()?->caja?->id;
                            }
                        @endphp
                        <tr class="hover:bg-gray-50/50 transition-colors {{ $esTemporal ? 'bg-red-50/30' : '' }}"
                            x-data="{
                                open: false,
                                camilla_id: '',
                                fecha_inicio: '',
                                fecha_fin: '',
                                precios: {{ json_encode($preciosCamillas) }},
                                get precio_hora() { return this.camilla_id ? (parseFloat(this.precios[this.camilla_id]) || 0) : 0; },
                                get horas() {
                                    if (!this.fecha_inicio || !this.fecha_fin) return 0;
                                    const diff = (new Date(this.fecha_fin) - new Date(this.fecha_inicio)) / 3600000;
                                    return diff > 0 ? Math.max(0.5, Math.round(diff * 100) / 100) : 0;
                                },
                                get costo() { return (this.horas * this.precio_hora).toFixed(2); }
                            }">
                            {{-- Código --}}
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                                @if($esTemporal)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                        {{ $paciente->emergency_code }}
                                    </span>
                                @else
                                    {{ $cajaId ?? $paciente->registro_codigo }}
                                @endif
                            </td>
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
                            {{-- Ingreso --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($esTemporal)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 border border-red-200">
                                        <span class="w-1.5 h-1.5 bg-red-500 rounded-full mr-1.5 animate-pulse"></span>
                                        Emergencia
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $ingresoColor }}-100 text-{{ $ingresoColor }}-800 border border-{{ $ingresoColor }}-200">
                                        <span class="w-1.5 h-1.5 bg-{{ $ingresoColor }}-500 rounded-full mr-1.5 {{ $ingresoColor === 'red' ? 'animate-pulse' : '' }}"></span>
                                        {{ $ingresoLabel }}
                                    </span>
                                @endif
                            </td>
                            {{-- Acciones --}}
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                @if($camillas->isEmpty())
                                    <span class="text-xs text-gray-400">Sin camillas activas</span>
                                @elseif($esTemporal)
                                    <span class="text-xs text-gray-400 cursor-not-allowed" title="Completar datos del paciente primero">Sin CI registrado</span>
                                @else
                                    <button @click="open = !open"
                                        class="inline-flex items-center px-3 py-1.5 border border-orange-200 shadow-sm text-xs font-medium rounded-lg text-orange-700 bg-orange-50 hover:bg-orange-100 transition-all">
                                        Registrar uso de camilla
                                    </button>
                                @endif

                                {{-- Modal inline --}}
                                <div x-show="open" x-cloak
                                    class="fixed inset-0 z-50 flex items-center justify-center bg-black/40"
                                    @keydown.escape.window="open = false">
                                    <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4 p-6" @click.stop>
                                        <div class="flex justify-between items-center mb-4">
                                            <h3 class="text-base font-semibold text-gray-800">Registrar uso de camilla</h3>
                                            <button @click="open = false" class="text-gray-400 hover:text-gray-600">✕</button>
                                        </div>
                                        <p class="text-sm text-gray-500 mb-4">Paciente: <strong>{{ $paciente->nombre }}</strong> ({{ $paciente->ci }})</p>

                                        <form method="POST" action="{{ route('emergency-staff.camillas.store') }}">
                                            @csrf
                                            <input type="hidden" name="paciente_ci" value="{{ $paciente->ci }}">

                                            <div class="space-y-4">
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">Camilla <span class="text-red-500">*</span></label>
                                                    <select name="camilla_id" x-model="camilla_id" required
                                                        class="w-full border rounded-lg px-3 py-2 text-sm">
                                                        <option value="">Seleccionar camilla...</option>
                                                        @foreach($camillas as $camilla)
                                                            <option value="{{ $camilla->id }}">
                                                                {{ $camilla->nombre }} ({{ $camilla->codigo }}) — Bs. {{ number_format($camilla->precio_por_hora, 2) }}/hr
                                                            </option>
                                                        @endforeach
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
                                                <div x-show="horas > 0" class="rounded-lg bg-blue-50 border border-blue-100 px-4 py-3 text-sm">
                                                    <div class="flex justify-between text-blue-800">
                                                        <span>Horas: <strong x-text="horas"></strong></span>
                                                        <span>Costo estimado: <strong>Bs. <span x-text="costo"></span></strong></span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="mt-6 flex gap-3 justify-end">
                                                <button type="button" @click="open = false"
                                                    class="px-4 py-2 border rounded-lg text-sm text-gray-600">Cancelar</button>
                                                <button type="submit"
                                                    class="px-4 py-2 bg-orange-600 text-white rounded-lg text-sm hover:bg-orange-700">Guardar</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
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
