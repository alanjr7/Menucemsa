@extends('layouts.app')

@section('content')
<div class="w-full p-6 bg-gray-50/50 min-h-screen">

    <!-- Page Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Pacientes del Dr. {{ $medico->user->name }}</h1>
            <p class="text-sm text-gray-500">{{ $medico->especialidad->nombre ?? 'Sin especialidad' }} | CI: {{ $medico->ci }}</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('consulta.pacientes-medicos') }}" class="flex items-center px-4 py-2 border border-gray-300 rounded-lg text-gray-600 bg-white hover:bg-gray-50 font-medium transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Volver a Médicos
            </a>
            <a href="{{ route('consulta.historial-medico', $medico->ci) }}" class="flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                Ver Historial
            </a>
        </div>
    </div>

    <!-- Estadísticas de Pacientes -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Pacientes</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['totalPacientes'] }}</p>
                    <p class="text-xs text-gray-400 mt-1">Histórico completo</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Nuevos este Mes</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $stats['pacientesNuevosEsteMes'] }}</p>
                    <p class="text-xs text-gray-400 mt-1">{{ now()->format('F') }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Con Seguro</p>
                    <p class="text-2xl font-bold text-amber-600">{{ $stats['pacientesConSeguro'] }}</p>
                    <p class="text-xs text-gray-400 mt-1">{{ round(($stats['pacientesConSeguro'] / max($stats['totalPacientes'], 1)) * 100, 1) }}% del total</p>
                </div>
                <div class="w-12 h-12 bg-amber-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Promedio Consultas</p>
                    <p class="text-2xl font-bold text-indigo-600">{{ round($stats['promedioConsultasPorPaciente'], 1) }}</p>
                    <p class="text-xs text-gray-400 mt-1">Por paciente</p>
                </div>
                <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6">
        <div class="flex flex-wrap gap-4 items-center">
            <div class="flex items-center gap-2">
                <label class="text-sm font-medium text-gray-700">Buscar:</label>
                <input type="text" id="busquedaPaciente" placeholder="Nombre o CI del paciente" class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="flex items-center gap-2">
                <label class="text-sm font-medium text-gray-700">Seguro:</label>
                <select id="filtroSeguro" class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Todos</option>
                    <option value="con_seguro">Con seguro</option>
                    <option value="sin_seguro">Sin seguro</option>
                </select>
            </div>
            <button onclick="aplicarFiltros()" class="px-4 py-1.5 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">
                Aplicar Filtros
            </button>
            <button onclick="limpiarFiltros()" class="px-4 py-1.5 border border-gray-300 text-gray-600 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors">
                Limpiar
            </button>
        </div>
    </div>

    <!-- Tabla de Pacientes -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <h2 class="text-lg font-bold text-gray-800">Lista de Pacientes</h2>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Paciente
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Teléfono
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Seguro
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Total Consultas
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Última Consulta
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($pacientes as $paciente)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $paciente->nombre }}</div>
                                <div class="text-sm text-gray-500">CI: {{ $paciente->ci }}</div>
                                <div class="text-xs text-gray-400">Código: {{ $paciente->codigo_registro }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $paciente->telefono ?? 'No registrado' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($paciente->seguro_id)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        {{ $paciente->seguro->nombre_empresa ?? 'Con seguro' }}
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                        Particular
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <span class="font-medium">{{ $paciente->consultas->count() }}</span>
                                @if($paciente->consultas->count() > 1)
                                    <div class="text-xs text-gray-500">consultas</div>
                                @else
                                    <div class="text-xs text-gray-500">consulta</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if($paciente->consultas->count() > 0)
                                    <div>{{ $paciente->consultas->first()->fecha->format('d/m/Y') }}</div>
                                    <div class="text-gray-500">{{ $paciente->consultas->first()->hora }}</div>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button onclick="verHistorialPaciente('{{ $paciente->ci }}')" class="text-blue-600 hover:text-blue-900 mr-3">
                                    Historial
                                </button>
                                <button onclick="verDetallesPaciente('{{ $paciente->ci }}')" class="text-purple-600 hover:text-purple-900">
                                    Ver
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="text-gray-500">
                                    <svg class="w-12 h-12 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                    <p class="text-lg font-medium">No se encontraron pacientes</p>
                                    <p class="text-sm mt-1">Este médico no tiene pacientes asignados</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Paginación -->
        @if($pacientes->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $pacientes->links() }}
            </div>
        @endif
    </div>
</div>

<script>
function verHistorialPaciente(ciPaciente) {
    // Redirigir al historial del paciente con este médico
    window.open(`/patients/${ciPaciente}`, '_blank');
}

function verDetallesPaciente(ciPaciente) {
    // Redirigir a los detalles del paciente
    window.open(`/patients/${ciPaciente}`, '_blank');
}

function aplicarFiltros() {
    const busqueda = document.getElementById('busquedaPaciente').value;
    const seguro = document.getElementById('filtroSeguro').value;
    
    let url = new URL(window.location.href);
    url.searchParams.delete('busqueda');
    url.searchParams.delete('seguro');
    
    if (busqueda) url.searchParams.set('busqueda', busqueda);
    if (seguro) url.searchParams.set('seguro', seguro);
    
    window.location.href = url.toString();
}

function limpiarFiltros() {
    let url = new URL(window.location.href);
    url.searchParams.delete('busqueda');
    url.searchParams.delete('seguro');
    window.location.href = url.toString();
}

// Restaurar filtros desde URL
document.addEventListener('DOMContentLoaded', function() {
    const params = new URLSearchParams(window.location.search);
    if (params.get('busqueda')) {
        document.getElementById('busquedaPaciente').value = params.get('busqueda');
    }
    if (params.get('seguro')) {
        document.getElementById('filtroSeguro').value = params.get('seguro');
    }
});
</script>
@endsection
