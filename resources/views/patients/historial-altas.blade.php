@extends('layouts.app')

@section('content')
<div class="w-full p-6 bg-gray-50/50 min-h-screen">

    <!-- Page Header -->
    <div class="flex justify-between items-end mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Historial de Altas</h1>
            <p class="text-sm text-gray-500">Registro histórico de pacientes dados de alta del sistema</p>
        </div>
        <a href="{{ route('patients.dar-de-alta.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2 border border-gray-200 text-gray-600 bg-white rounded-xl hover:bg-gray-50 font-medium transition-colors shadow-sm text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver a Dar de Alta
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total Altas</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $statsAltas['total'] }}</p>
                </div>
                <div class="p-2 bg-purple-100 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Este Mes</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $statsAltas['este_mes'] }}</p>
                </div>
                <div class="p-2 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Alta Médica</p>
                    <p class="text-2xl font-bold text-emerald-600">{{ $statsAltas['alta_medica'] }}</p>
                </div>
                <div class="p-2 bg-emerald-100 rounded-lg">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Alta Voluntaria</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ $statsAltas['voluntaria'] }}</p>
                </div>
                <div class="p-2 bg-yellow-100 rounded-lg">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <form method="GET" class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 mb-6 flex flex-col md:flex-row gap-4 flex-wrap">
        <div class="relative flex-1 min-w-[200px]">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <input type="text"
                   name="search"
                   value="{{ request('search') }}"
                   class="block w-full pl-10 pr-3 py-2.5 border border-gray-200 rounded-xl leading-5 bg-gray-50 placeholder-gray-400 focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 sm:text-sm transition-colors"
                   placeholder="Buscar por nombre o CI...">
        </div>

        <select name="motivo" class="px-4 py-2.5 border border-gray-200 rounded-xl text-gray-600 bg-white focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 sm:text-sm">
            <option value="">Todos los motivos</option>
            <option value="alta_medica" {{ request('motivo') == 'alta_medica' ? 'selected' : '' }}>Alta Médica</option>
            <option value="voluntaria" {{ request('motivo') == 'voluntaria' ? 'selected' : '' }}>Alta Voluntaria</option>
            <option value="traslado" {{ request('motivo') == 'traslado' ? 'selected' : '' }}>Traslado</option>
            <option value="fallecimiento" {{ request('motivo') == 'fallecimiento' ? 'selected' : '' }}>Fallecimiento</option>
        </select>

        <input type="date" name="desde" value="{{ request('desde') }}"
               class="px-4 py-2.5 border border-gray-200 rounded-xl text-gray-600 bg-white focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 sm:text-sm">

        <input type="date" name="hasta" value="{{ request('hasta') }}"
               class="px-4 py-2.5 border border-gray-200 rounded-xl text-gray-600 bg-white focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 sm:text-sm">

        <button type="submit" class="flex items-center justify-center px-4 py-2.5 bg-purple-600 text-white rounded-xl hover:bg-purple-700 font-medium transition-colors shadow-sm">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
            </svg>
            Filtrar
        </button>

        @if(request('search') || request('motivo') || request('desde') || request('hasta'))
            <a href="{{ route('patients.historial-altas') }}"
               class="flex items-center justify-center px-4 py-2.5 border border-gray-200 rounded-xl text-gray-600 bg-white hover:bg-gray-50 font-medium transition-colors shadow-sm">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                Limpiar
            </a>
        @endif
    </form>

    <!-- Altas Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-white flex justify-between items-center">
            <h3 class="text-gray-800 font-bold text-sm">
                Registro de Altas ({{ $altas->total() }})
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50/50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Fecha Alta</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Paciente</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">CI</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Seguro</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Motivo</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Dado de Alta por</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Observaciones</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($altas as $alta)
                        @php
                            $motivoColor = match($alta->motivo_alta) {
                                'alta_medica'   => 'emerald',
                                'voluntaria'    => 'yellow',
                                'traslado'      => 'blue',
                                'fallecimiento' => 'gray',
                                default         => 'gray',
                            };
                            $motivoLabel = match($alta->motivo_alta) {
                                'alta_medica'   => 'Alta Médica',
                                'voluntaria'    => 'Voluntaria',
                                'traslado'      => 'Traslado',
                                'fallecimiento' => 'Fallecimiento',
                                default         => ucfirst($alta->motivo_alta),
                            };
                        @endphp
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $alta->fecha_alta->format('d/m/Y') }}</div>
                                <div class="text-xs text-gray-500">{{ $alta->fecha_alta->format('H:i') }} · {{ $alta->fecha_alta->diffForHumans() }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $alta->paciente->nombre ?? '—' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-mono">
                                {{ $alta->paciente?->ci ?? $alta->paciente?->temp_code ?? '—' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $alta->paciente->seguro->nombre_empresa ?? 'Particular' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $motivoColor }}-100 text-{{ $motivoColor }}-800 border border-{{ $motivoColor }}-200">
                                    {{ $motivoLabel }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $alta->usuario->name ?? '—' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 max-w-xs">
                                <span class="line-clamp-2">{{ $alta->observaciones ?: '—' }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                @if($alta->paciente)
                                    <a href="{{ route('patients.show', $alta->paciente_id) }}"
                                       class="inline-flex items-center px-3 py-1.5 border border-gray-200 shadow-sm text-xs font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition-all">
                                        Ver Datos
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                    <p class="text-lg font-medium text-gray-600 mb-2">No hay registros de altas</p>
                                    <p class="text-sm text-gray-400">No se encontraron registros que cumplan con los filtros seleccionados.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($altas->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/30">
                {{ $altas->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
