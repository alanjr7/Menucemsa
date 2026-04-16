@extends('layouts.app')

@section('content')
<div class="w-full p-4 bg-gray-50/50 min-h-screen">

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-800">Historial de Cirugías</h1>
            <p class="text-sm text-gray-500">Registro completo de todas las cirugías programadas y realizadas</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('quirofano.index') }}" class="flex items-center px-3 py-2 border border-gray-300 text-gray-600 rounded-lg hover:bg-gray-50 font-medium transition-colors text-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                <span class="hidden sm:inline">Volver</span>
                <span class="sm:hidden">←</span>
            </a>
            <a href="{{ route('quirofano.create') }}" class="flex items-center px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium transition-colors text-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span class="hidden sm:inline">Nueva Cita</span>
                <span class="sm:hidden">+</span>
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-3 lg:gap-6 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 lg:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs lg:text-sm font-medium text-gray-500">Total</p>
                    <p class="text-lg lg:text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
                </div>
                <div class="w-8 h-8 lg:w-12 lg:h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 lg:w-6 lg:h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 lg:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs lg:text-sm font-medium text-gray-500">Programadas</p>
                    <p class="text-lg lg:text-2xl font-bold text-blue-600">{{ $stats['programadas'] }}</p>
                </div>
                <div class="w-8 h-8 lg:w-12 lg:h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 lg:w-6 lg:h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 lg:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs lg:text-sm font-medium text-gray-500">En Curso</p>
                    <p class="text-lg lg:text-2xl font-bold text-amber-600">{{ $stats['en_curso'] }}</p>
                </div>
                <div class="w-8 h-8 lg:w-12 lg:h-12 bg-amber-100 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 lg:w-6 lg:h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 lg:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs lg:text-sm font-medium text-gray-500">Finalizadas</p>
                    <p class="text-lg lg:text-2xl font-bold text-green-600">{{ $stats['finalizadas'] }}</p>
                </div>
                <div class="w-8 h-8 lg:w-12 lg:h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 lg:w-6 lg:h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-6m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 lg:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs lg:text-sm font-medium text-gray-500">Canceladas</p>
                    <p class="text-lg lg:text-2xl font-bold text-red-600">{{ $stats['canceladas'] }}</p>
                </div>
                <div class="w-8 h-8 lg:w-12 lg:h-12 bg-red-100 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 lg:w-6 lg:h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6">
        <div class="flex flex-wrap gap-4 items-center">
            <div class="flex items-center gap-2">
                <label class="text-sm font-medium text-gray-700">Estado:</label>
                <select id="filtroEstado" class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Todos</option>
                    <option value="programada">Programadas</option>
                    <option value="en_curso">En Curso</option>
                    <option value="finalizada">Finalizadas</option>
                    <option value="cancelada">Canceladas</option>
                </select>
            </div>
            <div class="flex items-center gap-2">
                <label class="text-sm font-medium text-gray-700">Desde:</label>
                <input type="date" id="filtroFechaDesde" class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="flex items-center gap-2">
                <label class="text-sm font-medium text-gray-700">Hasta:</label>
                <input type="date" id="filtroFechaHasta" class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <button onclick="aplicarFiltros()" class="px-4 py-1.5 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">
                Aplicar Filtros
            </button>
            <button onclick="limpiarFiltros()" class="px-4 py-1.5 border border-gray-300 text-gray-600 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors">
                Limpiar
            </button>
            <a href="{{ route('quirofano.historial.export', request()->all()) }}" class="flex items-center px-4 py-1.5 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Exportar Excel
            </a>
        </div>
    </div>

    <!-- Vista Móvil: Tarjetas de Cirugías -->
    <div class="lg:hidden space-y-4 mb-8">
        @forelse($citas as $cita)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-4">
                    <div class="flex justify-between items-start mb-3">
                        <div class="flex-1">
                            <h3 class="font-bold text-gray-900 text-sm">{{ $cita->paciente->nombre }}</h3>
                            <p class="text-xs text-gray-600 mt-1">CI: {{ $cita->paciente->ci }}</p>
                        </div>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $cita->estado === 'programada' ? 'bg-blue-100 text-blue-800' : ($cita->estado === 'en_curso' ? 'bg-amber-100 text-amber-800' : ($cita->estado === 'finalizada' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800')) }}">
                            {{ ucfirst($cita->estado) }}
                        </span>
                    </div>
                    
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Fecha:</span>
                            <span class="font-medium">{{ $cita->fecha->format('d/m/Y') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Hora:</span>
                            <span class="font-medium">{{ $cita->hora_inicio_estimada->format('H:i') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Cirujano:</span>
                            <span class="font-medium text-xs">{{ optional($cita->cirujano->user)->name ?? 'N/A' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Quirófano:</span>
                            <span class="font-medium">Q{{ $cita->quirofano->id }}</span>
                        </div>
                        @if($cita->costo_final)
                            <div class="flex justify-between pt-2 border-t">
                                <span class="text-gray-500">Costo:</span>
                                <span class="font-bold text-green-600">${{ number_format($cita->costo_final, 2) }}</span>
                            </div>
                        @endif
                    </div>
                    
                    <div class="flex gap-2 mt-4">
                        <a href="{{ route('quirofano.show', $cita) }}" class="flex-1 text-center px-3 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">
                            Ver Detalles
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 text-center">
                <div class="text-gray-400">
                    <svg class="w-12 h-12 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <p class="text-lg font-medium">No se encontraron cirugías</p>
                    <p class="text-sm mt-1">No hay cirugías registradas en el sistema</p>
                </div>
            </div>
        @endforelse
        
        <!-- Paginación móvil -->
        @if($citas->hasPages())
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
                {{ $citas->links('pagination::tailwind') }}
            </div>
        @endif
    </div>

    <!-- Vista Desktop: Tabla -->
    <div class="hidden lg:block bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <h2 class="text-lg font-bold text-gray-800">Registro de Cirugías</h2>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Fecha/Hora
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Paciente
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Cirujano
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Quirófano
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Tipo
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Estado
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Duración
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Costo
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($citas as $cita)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <div>{{ $cita->fecha->format('d/m/Y') }}</div>
                                <div class="text-gray-500">{{ $cita->hora_inicio_estimada->format('H:i') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $cita->paciente->nombre }}</div>
                                <div class="text-sm text-gray-500">CI: {{ $cita->paciente->ci }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ optional($cita->cirujano->user)->name ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                Q{{ $cita->quirofano->id }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 capitalize">
                                    {{ $cita->tipo_cirugia }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @switch($cita->estado)
                                    @case('programada')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            Programada
                                        </span>
                                        @break
                                    @case('en_curso')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-amber-100 text-amber-800">
                                            En Curso
                                        </span>
                                        @break
                                    @case('finalizada')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Finalizada
                                        </span>
                                        @break
                                    @case('cancelada')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            Cancelada
                                        </span>
                                        @break
                                @endswitch
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if($cita->duracion_real)
                                    @php
                                        $totalMinutos = round($cita->duracion_real);
                                        $horas = floor($totalMinutos / 60);
                                        $minutos = $totalMinutos % 60;
                                    @endphp
                                    @if($horas > 0)
                                        {{ $horas }}h {{ $minutos }}min
                                    @else
                                        {{ $minutos }}min
                                    @endif
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if($cita->costo_final)
                                    ${{ number_format($cita->costo_final, 2) }}
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('quirofano.show', $cita) }}" class="text-blue-600 hover:text-blue-900">
                                    Ver
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center">
                                <div class="text-gray-500">
                                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                    <p class="text-lg font-medium">No se encontraron cirugías</p>
                                    <p class="text-sm mt-1">No hay cirugías registradas en el sistema</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Paginación -->
        @if($citas->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $citas->links() }}
            </div>
        @endif
    </div>
</div>

<script>
function aplicarFiltros() {
    const estado = document.getElementById('filtroEstado').value;
    const fechaDesde = document.getElementById('filtroFechaDesde').value;
    const fechaHasta = document.getElementById('filtroFechaHasta').value;
    
    let url = new URL(window.location.href);
    url.searchParams.delete('estado');
    url.searchParams.delete('fecha_desde');
    url.searchParams.delete('fecha_hasta');
    
    if (estado) url.searchParams.set('estado', estado);
    if (fechaDesde) url.searchParams.set('fecha_desde', fechaDesde);
    if (fechaHasta) url.searchParams.set('fecha_hasta', fechaHasta);
    
    window.location.href = url.toString();
}

function limpiarFiltros() {
    let url = new URL(window.location.href);
    url.searchParams.delete('estado');
    url.searchParams.delete('fecha_desde');
    url.searchParams.delete('fecha_hasta');
    window.location.href = url.toString();
}

// Restaurar filtros desde URL
document.addEventListener('DOMContentLoaded', function() {
    const params = new URLSearchParams(window.location.search);
    if (params.get('estado')) {
        document.getElementById('filtroEstado').value = params.get('estado');
    }
    if (params.get('fecha_desde')) {
        document.getElementById('filtroFechaDesde').value = params.get('fecha_desde');
    }
    if (params.get('fecha_hasta')) {
        document.getElementById('filtroFechaHasta').value = params.get('fecha_hasta');
    }
});
</script>
@endsection
