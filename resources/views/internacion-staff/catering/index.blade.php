@extends('layouts.app')

@section('content')
<div class="w-full p-6 bg-gray-50/50 min-h-screen" x-data="cateringApp()">

    <div class="flex justify-between items-end mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Catering — Todos los Pacientes</h1>
            <p class="text-sm text-gray-500">Registrar alimentación para todos los pacientes del sistema</p>
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

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total Pacientes</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['total'] }}</p>
                </div>
                <div class="p-2 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Hospitalizados</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ $stats['hospitalizados'] }}</p>
                </div>
                <div class="p-2 bg-yellow-100 rounded-lg">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Emergencias</p>
                    <p class="text-2xl font-bold text-red-600">{{ $stats['emergencias'] }}</p>
                </div>
                <div class="p-2 bg-red-100 rounded-lg">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Precio Catering</p>
                    <p class="text-lg font-bold text-orange-600">Desayuno Bs. {{ number_format($precios['desayuno'] ?? 0, 0) }}</p>
                </div>
                <div class="p-2 bg-orange-100 rounded-lg">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

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
                placeholder="Buscar por nombre, CI o código de registro...">
        </div>
        <button type="submit" class="flex items-center justify-center px-4 py-2.5 bg-blue-600 text-white rounded-xl hover:bg-blue-700 font-medium transition-colors shadow-sm">
            Buscar
        </button>
        @if(request('search'))
            <a href="{{ route('internacion-staff.catering.index') }}" class="flex items-center justify-center px-4 py-2.5 border border-gray-200 rounded-xl text-gray-600 bg-white hover:bg-gray-50 font-medium transition-colors shadow-sm">
                Limpiar
            </a>
        @endif
    </form>

    {{-- Precios de Referencia --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-6">
        <div class="flex items-center justify-center gap-8">
            @foreach(['desayuno' => ['yellow', 'Desayuno'], 'almuerzo' => ['green', 'Almuerzo'], 'merienda' => ['purple', 'Merienda'], 'cena' => ['indigo', 'Cena']] as $tipo => $config)
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-{{ $config[0] }}-400"></span>
                    <span class="text-sm text-gray-600">{{ $config[1] }}:</span>
                    <span class="text-sm font-semibold text-gray-800">Bs. {{ number_format($precios[$tipo] ?? 0, 2) }}</span>
                </div>
            @endforeach
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-white">
            <h3 class="text-gray-800 font-bold text-sm">Pacientes Registrados ({{ $pacientes->total() + count($pacientesTemporales) }})</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Nombre Completo</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Carnet</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Seguro</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Área Actual</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Catering Hoy</th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($todosPacientes as $paciente)
                        @php
                            $esTemporal = isset($paciente->is_temporal) && $paciente->is_temporal;
                            $pacienteId = $paciente->id;
                            $ci = $paciente->ci ?? $paciente->temp_code ?? '—';
                            $catHoy = $cateringHoy[$pacienteId] ?? collect();
                            $estados = [
                                'desayuno' => $catHoy->firstWhere('tipo_comida', 'desayuno')?->estado ?? 'no_dado',
                                'almuerzo' => $catHoy->firstWhere('tipo_comida', 'almuerzo')?->estado ?? 'no_dado',
                                'merienda' => $catHoy->firstWhere('tipo_comida', 'merienda')?->estado ?? 'no_dado',
                                'cena' => $catHoy->firstWhere('tipo_comida', 'cena')?->estado ?? 'no_dado',
                            ];
                            
                            // Configuración de áreas
                            $area = $esTemporal ? 'emergencia' : ($paciente->area_actual ?? 'registrado');
                            $areaConfig = [
                                'emergencia' => ['bg-red-100 text-red-800', 'Emergencia'],
                                'internacion' => ['bg-yellow-100 text-yellow-800', 'Internación'],
                                'consulta' => ['bg-green-100 text-green-800', 'Consulta'],
                                'registrado' => ['bg-gray-100 text-gray-800', 'Registrado'],
                            ][$area] ?? ['bg-gray-100 text-gray-800', 'Registrado'];
                            
                            $coloresEstado = [
                                'dado' => 'bg-green-500',
                                'no_dado' => 'bg-gray-300',
                                'no_aplica' => 'bg-red-400',
                            ];
                        @endphp
                        <tr class="hover:bg-gray-50/50 transition-colors {{ $esTemporal ? 'bg-red-50/30' : '' }}">
                            {{-- Nombre --}}
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                <div class="flex items-center">
                                    @if($esTemporal)
                                        <span class="w-2 h-2 bg-red-500 rounded-full mr-2 animate-pulse"></span>
                                        <span class="font-medium text-red-700">{{ $paciente->nombre }}</span>
                                    @else
                                        <span class="font-medium">{{ $paciente->nombre }}</span>
                                    @endif
                                </div>
                            </td>
                            {{-- CI --}}
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-mono">{{ $ci }}</td>
                            {{-- Seguro --}}
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @if($esTemporal)
                                    <span class="text-xs text-red-500">Emergencia temporal</span>
                                @else
                                    {{ $paciente->seguro->nombre_empresa ?? 'Particular' }}
                                @endif
                            </td>
                            {{-- Área Actual --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $areaConfig[0] }}">
                                    {{ $areaConfig[1] }}
                                </span>
                            </td>
                            {{-- Catering Hoy --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-1">
                                    @foreach(['desayuno' => 'D', 'almuerzo' => 'A', 'merienda' => 'M', 'cena' => 'C'] as $tipo => $letra)
                                        <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold text-white {{ $coloresEstado[$estados[$tipo]] }}" title="{{ ucfirst($tipo) }}">
                                            {{ $letra }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                            {{-- Acciones --}}
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <button @click="abrirModal('{{ $pacienteId }}', '{{ $paciente->nombre }}', {{ json_encode($estados) }})"
                                    class="inline-flex items-center px-3 py-1.5 border border-orange-200 shadow-sm text-xs font-medium rounded-lg text-orange-700 bg-orange-50 hover:bg-orange-100 transition-all">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                    Catering
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
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

    {{-- Modal de Catering --}}
    <div x-show="modalOpen" x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/40"
        @keydown.escape.window="cerrarModal()">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-lg mx-4 p-6" @click.stop>
            <div class="flex justify-between items-center mb-4">
                <div>
                    <h3 class="text-base font-semibold text-gray-800">Registrar Catering</h3>
                    <p class="text-sm text-gray-500" x-text="pacienteNombre"></p>
                </div>
                <button @click="cerrarModal()" class="text-gray-400 hover:text-gray-600">✕</button>
            </div>

            <div class="space-y-3 mb-4">
                {{-- Desayuno --}}
                <div @click="toggleEstado('desayuno')"
                    class="flex items-center justify-between p-3 rounded-lg border cursor-pointer transition-all"
                    :class="{
                        'bg-yellow-50 border-yellow-300': estados.desayuno === 'dado',
                        'bg-gray-50 border-gray-200': estados.desayuno === 'no_dado',
                        'bg-red-50 border-red-200': estados.desayuno === 'no_aplica'
                    }">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center"
                            :class="{
                                'bg-yellow-100 text-yellow-600': estados.desayuno === 'dado',
                                'bg-gray-100 text-gray-400': estados.desayuno === 'no_dado',
                                'bg-red-100 text-red-600': estados.desayuno === 'no_aplica'
                            }">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-gray-800">Desayuno</p>
                            <p class="text-xs text-gray-500">Bs. {{ number_format($precios['desayuno'] ?? 0, 2) }}</p>
                        </div>
                    </div>
                    <span class="px-3 py-1 text-xs font-medium rounded-full"
                        :class="{
                            'bg-yellow-100 text-yellow-800': estados.desayuno === 'dado',
                            'bg-gray-100 text-gray-600': estados.desayuno === 'no_dado',
                            'bg-red-100 text-red-800': estados.desayuno === 'no_aplica'
                        }"
                        x-text="estados.desayuno === 'dado' ? 'Dado' : (estados.desayuno === 'no_aplica' ? 'No Aplica' : 'No Dado')">
                    </span>
                </div>

                {{-- Almuerzo --}}
                <div @click="toggleEstado('almuerzo')"
                    class="flex items-center justify-between p-3 rounded-lg border cursor-pointer transition-all"
                    :class="{
                        'bg-green-50 border-green-300': estados.almuerzo === 'dado',
                        'bg-gray-50 border-gray-200': estados.almuerzo === 'no_dado',
                        'bg-red-50 border-red-200': estados.almuerzo === 'no_aplica'
                    }">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center"
                            :class="{
                                'bg-green-100 text-green-600': estados.almuerzo === 'dado',
                                'bg-gray-100 text-gray-400': estados.almuerzo === 'no_dado',
                                'bg-red-100 text-red-600': estados.almuerzo === 'no_aplica'
                            }">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-gray-800">Almuerzo</p>
                            <p class="text-xs text-gray-500">Bs. {{ number_format($precios['almuerzo'] ?? 0, 2) }}</p>
                        </div>
                    </div>
                    <span class="px-3 py-1 text-xs font-medium rounded-full"
                        :class="{
                            'bg-green-100 text-green-800': estados.almuerzo === 'dado',
                            'bg-gray-100 text-gray-600': estados.almuerzo === 'no_dado',
                            'bg-red-100 text-red-800': estados.almuerzo === 'no_aplica'
                        }"
                        x-text="estados.almuerzo === 'dado' ? 'Dado' : (estados.almuerzo === 'no_aplica' ? 'No Aplica' : 'No Dado')">
                    </span>
                </div>

                {{-- Merienda --}}
                <div @click="toggleEstado('merienda')"
                    class="flex items-center justify-between p-3 rounded-lg border cursor-pointer transition-all"
                    :class="{
                        'bg-purple-50 border-purple-300': estados.merienda === 'dado',
                        'bg-gray-50 border-gray-200': estados.merienda === 'no_dado',
                        'bg-red-50 border-red-200': estados.merienda === 'no_aplica'
                    }">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center"
                            :class="{
                                'bg-purple-100 text-purple-600': estados.merienda === 'dado',
                                'bg-gray-100 text-gray-400': estados.merienda === 'no_dado',
                                'bg-red-100 text-red-600': estados.merienda === 'no_aplica'
                            }">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-gray-800">Merienda</p>
                            <p class="text-xs text-gray-500">Bs. {{ number_format($precios['merienda'] ?? 0, 2) }}</p>
                        </div>
                    </div>
                    <span class="px-3 py-1 text-xs font-medium rounded-full"
                        :class="{
                            'bg-purple-100 text-purple-800': estados.merienda === 'dado',
                            'bg-gray-100 text-gray-600': estados.merienda === 'no_dado',
                            'bg-red-100 text-red-800': estados.merienda === 'no_aplica'
                        }"
                        x-text="estados.merienda === 'dado' ? 'Dado' : (estados.merienda === 'no_aplica' ? 'No Aplica' : 'No Dado')">
                    </span>
                </div>

                {{-- Cena --}}
                <div @click="toggleEstado('cena')"
                    class="flex items-center justify-between p-3 rounded-lg border cursor-pointer transition-all"
                    :class="{
                        'bg-indigo-50 border-indigo-300': estados.cena === 'dado',
                        'bg-gray-50 border-gray-200': estados.cena === 'no_dado',
                        'bg-red-50 border-red-200': estados.cena === 'no_aplica'
                    }">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center"
                            :class="{
                                'bg-indigo-100 text-indigo-600': estados.cena === 'dado',
                                'bg-gray-100 text-gray-400': estados.cena === 'no_dado',
                                'bg-red-100 text-red-600': estados.cena === 'no_aplica'
                            }">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-gray-800">Cena</p>
                            <p class="text-xs text-gray-500">Bs. {{ number_format($precios['cena'] ?? 0, 2) }}</p>
                        </div>
                    </div>
                    <span class="px-3 py-1 text-xs font-medium rounded-full"
                        :class="{
                            'bg-indigo-100 text-indigo-800': estados.cena === 'dado',
                            'bg-gray-100 text-gray-600': estados.cena === 'no_dado',
                            'bg-red-100 text-red-800': estados.cena === 'no_aplica'
                        }"
                        x-text="estados.cena === 'dado' ? 'Dado' : (estados.cena === 'no_aplica' ? 'No Aplica' : 'No Dado')">
                    </span>
                </div>
            </div>

            

            {{-- Botones --}}
            <div class="flex gap-3 justify-end">
                <button type="button" @click="cerrarModal()"
                    class="px-4 py-2 border rounded-lg text-sm text-gray-600 hover:bg-gray-50">
                    Cancelar
                </button>
                <button type="button" @click="guardar()"
                    :disabled="guardando"
                    :class="guardando ? 'px-4 py-2 bg-gray-300 text-gray-500 rounded-lg text-sm cursor-not-allowed' : 'px-4 py-2 bg-orange-600 text-white rounded-lg text-sm hover:bg-orange-700'">
                    <span x-show="!guardando">Guardar</span>
                    <span x-show="guardando">Guardando...</span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function cateringApp() {
        return {
            modalOpen: false,
            guardando: false,
            pacienteId: '',
            pacienteNombre: '',
            observaciones: '',
            estados: {
                desayuno: 'no_dado',
                almuerzo: 'no_dado',
                merienda: 'no_dado',
                cena: 'no_dado'
            },

            abrirModal(id, nombre, estadosActuales) {
                this.pacienteId = id;
                this.pacienteNombre = nombre;
                this.observaciones = '';
                this.estados = {
                    desayuno: estadosActuales?.desayuno || 'no_dado',
                    almuerzo: estadosActuales?.almuerzo || 'no_dado',
                    merienda: estadosActuales?.merienda || 'no_dado',
                    cena: estadosActuales?.cena || 'no_dado'
                };
                this.modalOpen = true;
            },

            cerrarModal() {
                this.modalOpen = false;
                this.pacienteId = '';
                this.pacienteNombre = '';
                this.observaciones = '';
            },

            toggleEstado(tipo) {
                const ciclo = {
                    'no_dado': 'dado',
                    'dado': 'no_aplica',
                    'no_aplica': 'no_dado'
                };
                this.estados[tipo] = ciclo[this.estados[tipo]];
            },

            async guardar() {
                if (!this.pacienteId) return;

                this.guardando = true;

                const registros = [];
                for (const [tipo, estado] of Object.entries(this.estados)) {
                    registros.push({
                        paciente_id: this.pacienteId,
                        tipo_comida: tipo,
                        estado: estado,
                        observaciones: this.observaciones
                    });
                }

                try {
                    const response = await fetch('{{ route('internacion-staff.catering.registrar') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                        },
                        body: JSON.stringify({ registros })
                    });

                    const data = await response.json();

                    if (data.success) {
                        this.cerrarModal();
                        window.location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('Error al guardar el catering');
                } finally {
                    this.guardando = false;
                }
            }
        }
    }
</script>
@endsection
