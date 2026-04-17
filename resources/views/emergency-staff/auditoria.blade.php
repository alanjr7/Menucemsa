@extends('layouts.app')

@section('content')
<div class="p-6 bg-gray-50/50 min-h-screen">
    <!-- Header -->
    <div class="mb-8">
        <a href="{{ route('emergency-staff.enfermeras.index') }}" class="text-gray-500 hover:text-gray-700 flex items-center gap-2 mb-4">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver a Enfermeras
        </a>
        <div class="flex justify-between items-end">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Auditoría de Emergencias</h1>
                <p class="text-sm text-gray-500">Seguimiento de todas las acciones del personal de enfermería</p>
            </div>
            <div class="flex gap-3">
                <button onclick="exportarDatos()" class="flex items-center px-4 py-2 bg-green-600 text-white font-medium rounded-xl hover:bg-green-700 transition-all shadow-sm">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Exportar
                </button>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
        <form action="{{ route('emergency-staff.auditoria') }}" method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
            <div>
                <label for="enfermera_id" class="block text-sm font-medium text-gray-700 mb-2">Enfermera</label>
                <select name="enfermera_id" id="enfermera_id" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-red-500 focus:ring-2 focus:ring-red-200 transition outline-none">
                    <option value="">Todas</option>
                    @foreach($enfermeras as $enf)
                        <option value="{{ $enf->user_id }}" {{ request('enfermera_id') == $enf->user_id ? 'selected' : '' }}>
                            {{ $enf->user?->name ?? 'Enfermera #' . $enf->user_id }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="action" class="block text-sm font-medium text-gray-700 mb-2">Tipo de Acción</label>
                <select name="action" id="action" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-red-500 focus:ring-2 focus:ring-red-200 transition outline-none">
                    <option value="">Todas</option>
                    @foreach($tiposAcciones as $tipo)
                        <option value="{{ $tipo }}" {{ request('action') == $tipo ? 'selected' : '' }}>
                            {{ str_replace('_', ' ', $tipo) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="fecha_desde" class="block text-sm font-medium text-gray-700 mb-2">Fecha Desde</label>
                <input type="date" name="fecha_desde" id="fecha_desde" value="{{ request('fecha_desde') }}"
                    class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-red-500 focus:ring-2 focus:ring-red-200 transition outline-none">
            </div>

            <div>
                <label for="fecha_hasta" class="block text-sm font-medium text-gray-700 mb-2">Fecha Hasta</label>
                <input type="date" name="fecha_hasta" id="fecha_hasta" value="{{ request('fecha_hasta') }}"
                    class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-red-500 focus:ring-2 focus:ring-red-200 transition outline-none">
            </div>

            <div class="flex gap-2">
                <button type="submit" class="flex-1 px-4 py-2.5 bg-red-600 text-white font-medium rounded-xl hover:bg-red-700 transition">
                    Filtrar
                </button>
                <a href="{{ route('emergency-staff.auditoria') }}" class="px-4 py-2.5 bg-gray-100 text-gray-700 font-medium rounded-xl hover:bg-gray-200 transition">
                    Limpiar
                </a>
            </div>
        </form>
    </div>

    <!-- Resumen -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
            <span class="text-gray-500 text-sm font-medium">Total Actividades</span>
            <span class="text-3xl font-bold text-gray-800 block mt-1">{{ $actividades->total() }}</span>
        </div>
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
            <span class="text-gray-500 text-sm font-medium">Enfermeras Activas</span>
            <span class="text-3xl font-bold text-blue-600 block mt-1">{{ $enfermeras->where('estado', 'activo')->count() }}</span>
        </div>
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
            <span class="text-gray-500 text-sm font-medium">Hoy</span>
            <span class="text-3xl font-bold text-green-600 block mt-1">
                {{ $actividades->where('created_at', '>=', now()->startOfDay())->count() }}
            </span>
        </div>
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100">
            <span class="text-gray-500 text-sm font-medium">Evaluaciones</span>
            <span class="text-3xl font-bold text-purple-600 block mt-1">
                {{ $actividades->where('action', 'evaluacion_paciente')->count() }}
            </span>
        </div>
    </div>

    <!-- Tabla de Actividades -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center">
            <h2 class="text-lg font-bold text-gray-800">Registro de Actividades</h2>
            @if($actividades->count() > 0)
            <span class="text-sm text-gray-500">Mostrando {{ $actividades->firstItem() }} - {{ $actividades->lastItem() }} de {{ $actividades->total() }}</span>
            @endif
        </div>

        @if($actividades->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500" id="tabla-auditoria">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-4">Fecha y Hora</th>
                        <th scope="col" class="px-6 py-4">Enfermera</th>
                        <th scope="col" class="px-6 py-4">Acción</th>
                        <th scope="col" class="px-6 py-4">Descripción</th>
                        <th scope="col" class="px-6 py-4">Paciente/Ref.</th>
                        <th scope="col" class="px-6 py-4">Detalles</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($actividades as $log)
                    <tr class="bg-white border-b hover:bg-gray-50 transition">
                        <td class="px-6 py-4 whitespace-nowrap">
                            {{ $log->created_at->format('d/m/Y H:i:s') }}
                        </td>
                        <td class="px-6 py-4 font-medium text-gray-900">
                            {{ $log->user?->name ?? 'Usuario #' . $log->user_id }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @switch($log->action)
                                    @case('evaluacion_paciente') bg-blue-100 text-blue-800 @break
                                    @case('cambio_estado_paciente') bg-yellow-100 text-yellow-800 @break
                                    @case('dar_alta_paciente') bg-green-100 text-green-800 @break
                                    @case('derivar_paciente') bg-purple-100 text-purple-800 @break
                                    @case('medicamento_aplicado') bg-orange-100 text-orange-800 @break
                                    @case('crear_enfermera') bg-pink-100 text-pink-800 @break
                                    @case('actualizar_enfermera') bg-indigo-100 text-indigo-800 @break
                                    @default bg-gray-100 text-gray-800
                                @endswitch
                            ">
                                {{ str_replace('_', ' ', $log->action) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 max-w-md">
                            {{ $log->description }}
                        </td>
                        <td class="px-6 py-4">
                            @if($log->model_type === 'App\Models\Emergency' && $log->model_id)
                                <a href="{{ route('emergency-staff.show', $log->model_id) }}" class="text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 px-3 py-1 rounded-lg transition text-xs">
                                    Emergencia #{{ $log->model_id }}
                                </a>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <button onclick="verDetalles({{ $log->id }})" class="text-gray-600 hover:text-gray-900 bg-gray-50 hover:bg-gray-100 px-3 py-1 rounded-lg transition text-xs">
                                Ver detalles
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-100">
            {{ $actividades->links() }}
        </div>
        @else
        <div class="p-12 text-center">
            <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <p class="text-gray-500 text-lg">No hay actividades registradas</p>
            <p class="text-gray-400 text-sm mt-1">Las acciones del personal aparecerán aquí</p>
        </div>
        @endif
    </div>
</div>

<script>
function verDetalles(logId) {
    // Aquí se puede implementar un modal o redirección para ver detalles completos
    alert('Detalles del registro #' + logId);
}

function exportarDatos() {
    // Función simple para exportar a CSV
    const tabla = document.getElementById('tabla-auditoria');
    if (!tabla) {
        alert('No hay datos para exportar');
        return;
    }
    
    let csv = [];
    const filas = tabla.querySelectorAll('tr');
    
    filas.forEach(fila => {
        const celdas = fila.querySelectorAll('th, td');
        const datos = Array.from(celdas).map(celda => {
            // Limpiar texto y manejar comas
            let texto = celda.innerText.replace(/,/g, ';').replace(/\n/g, ' ');
            return '"' + texto + '"';
        });
        csv.push(datos.join(','));
    });
    
    const csvContent = "data:text/csv;charset=utf-8," + csv.join('\n');
    const encodedUri = encodeURI(csvContent);
    const link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", "auditoria_emergencias_{{ now()->format('Y-m-d') }}.csv");
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}
</script>
@endsection
