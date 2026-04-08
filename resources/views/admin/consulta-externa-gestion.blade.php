@extends('layouts.app')
@section('content')
    <div x-data="{ tab: 'medicos', selectedMedico: null }" class="p-6">

      

        <div class="bg-gray-100 p-1 rounded-xl flex mb-6">
            <button @click="tab = 'medicos'"
                :class="tab === 'medicos' ? 'bg-white shadow text-gray-800' : 'text-gray-500 hover:text-gray-700'"
                class="flex-1 py-2 text-sm font-bold rounded-lg transition-all">
                Todos los Médicos
            </button>
            <button @click="tab = 'historial'"
                :class="tab === 'historial' ? 'bg-white shadow text-gray-800' : 'text-gray-500 hover:text-gray-700'"
                class="flex-1 py-2 text-sm font-bold rounded-lg transition-all">
                Historial General
            </button>
            <button @click="tab = 'estadisticas'"
                :class="tab === 'estadisticas' ? 'bg-white shadow text-gray-800' : 'text-gray-500 hover:text-gray-700'"
                class="flex-1 py-2 text-sm font-bold rounded-lg transition-all">
                Estadísticas
            </button>
        </div>

        <!-- Vista de Todos los Médicos -->
        <div x-show="tab === 'medicos'" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-4 border-b border-gray-100 flex items-center justify-between">
                <div class="flex items-center gap-2 text-gray-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                    <span class="font-bold">Médicos Disponibles - {{ now()->format('d/m/Y') }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <input type="text" placeholder="Buscar médico..." class="px-3 py-1 border border-gray-300 rounded-lg text-sm">
                    <button class="px-3 py-1 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700 transition">
                        Filtrar
                    </button>
                </div>
            </div>

            <div class="divide-y divide-gray-100">
                @forelse ($medicos as $medico)
                    <?php 
                    $consultasMedico = $consultasPorMedico[$medico->ci] ?? [];
                    $totalConsultas = $consultasMedico['totalConsultas'] ?? 0;
                    ?>
                    <div class="p-6 hover:bg-gray-50 transition">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-800">Dr. {{ $medico->user->name ?? 'Sin nombre' }}</h4>
                                    <p class="text-sm text-gray-600">{{ $medico->especialidad->nombre ?? 'Sin especialidad' }}</p>
                                    <p class="text-xs text-gray-400">CI: {{ $medico->ci }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-4">
                                <div class="text-right">
                                    <p class="text-sm font-medium text-gray-700">Estado</p>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $totalConsultas > 0 ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ $totalConsultas > 0 ? 'Activo' : 'Disponible' }}
                                    </span>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-medium text-gray-700">Consultas Hoy</p>
                                    <p class="text-lg font-bold text-blue-600">{{ $totalConsultas }}</p>
                                </div>
                                <div class="flex gap-2">
                                    <a href="{{ route('consulta.historial-medico', $medico->ci) }}" class="px-4 py-2 text-sm font-semibold text-gray-600 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 hover:text-gray-900 shadow-sm transition">
                                        Historial
                                    </a>
                                    <a href="{{ route('consulta.pacientes-medicos', $medico->ci) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-bold hover:bg-blue-700 transition shadow-sm">
                                        Pacientes
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-gray-500">
                        <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <p class="text-lg font-medium">No hay médicos registrados</p>
                        <p class="text-sm mt-2">No se encontraron médicos en el sistema</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Vista de Historial General -->
        <div x-show="tab === 'historial'" x-cloak class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-4 border-b border-gray-100 flex items-center justify-between">
                <div class="flex items-center gap-2 text-gray-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="font-bold">Historial General de Consultas</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-sm text-gray-500">Mostrando {{ $historialConsultas->count() }} de {{ $historialConsultas->total() }} consultas</span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 text-gray-600 text-xs uppercase font-bold">
                        <tr>
                            <th class="px-4 py-3">Código</th>
                            <th class="px-4 py-3">Fecha/Hora</th>
                            <th class="px-4 py-3">Paciente</th>
                            <th class="px-4 py-3">Médico</th>
                            <th class="px-4 py-3">Especialidad</th>
                            <th class="px-4 py-3">Estado</th>
                            <th class="px-4 py-3 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($historialConsultas as $consulta)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $consulta->codigo }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    {{ $consulta->fecha?->format('d/m/Y') }}<br>
                                    <span class="text-xs text-gray-400">{{ $consulta->hora }}</span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-700">
                                    {{ $consulta->paciente->nombre ?? 'N/A' }}<br>
                                    <span class="text-xs text-gray-400">CI: {{ $consulta->paciente->ci ?? '-' }}</span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-700">
                                    Dr. {{ $consulta->medico->user->name ?? 'Sin nombre' }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    {{ $consulta->especialidad->nombre ?? 'Sin especialidad' }}
                                </td>
                                <td class="px-4 py-3">
                                    @php
                                        $estadoColor = match($consulta->estado) {
                                            'pendiente' => 'yellow',
                                            'en_atencion' => 'blue',
                                            'atendido' => 'green',
                                            'cancelado' => 'red',
                                            default => 'gray'
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-{{ $estadoColor }}-100 text-{{ $estadoColor }}-800">
                                        {{ ucfirst(str_replace('_', ' ', $consulta->estado)) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('consulta.ver', $consulta->codigo) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                        Ver Detalle
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <p class="text-sm">No hay consultas registradas</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($historialConsultas->hasPages())
                <div class="p-4 border-t border-gray-100">
                    {{ $historialConsultas->links() }}
                </div>
            @endif
        </div>

        <!-- Vista de Estadísticas -->
        <div x-show="tab === 'estadisticas'" x-cloak class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-4 border-b border-gray-100 flex items-center gap-2 text-gray-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                <span class="font-bold">Estadísticas de Consulta Externa</span>
            </div>

            <div class="p-8">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="bg-blue-50 rounded-xl p-6 border border-blue-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-blue-600">Total Consultas Hoy</p>
                                <p class="text-2xl font-bold text-blue-900">{{ $stats['totalConsultasHoy'] ?? 0 }}</p>
                            </div>
                            <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="bg-green-50 rounded-xl p-6 border border-green-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-green-600">Completadas</p>
                                <p class="text-2xl font-bold text-green-900">{{ $stats['consultasCompletadas'] ?? 0 }}</p>
                            </div>
                            <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                    </div>
                    <div class="bg-yellow-50 rounded-xl p-6 border border-yellow-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-yellow-600">En Atención</p>
                                <p class="text-2xl font-bold text-yellow-900">{{ $stats['consultasEnAtencion'] ?? 0 }}</p>
                            </div>
                            <svg class="w-8 h-8 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="bg-purple-50 rounded-xl p-6 border border-purple-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-purple-600">Médicos Activos</p>
                                <p class="text-2xl font-bold text-purple-900">{{ $stats['medicosActivos'] ?? 0 }}</p>
                            </div>
                            <svg class="w-8 h-8 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
