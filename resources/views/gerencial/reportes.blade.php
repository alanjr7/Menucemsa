@extends('layouts.app')

@section('content')
<div class="w-full p-6 bg-gray-50/50 min-h-screen">
    <!-- Header -->
    <div class="flex justify-between items-end mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Panel de Reportes</h1>
            <p class="text-sm text-gray-500">Genera y exporta reportes gerenciales del sistema</p>
        </div>
        <div class="text-right">
            <span class="text-[10px] font-bold text-blue-600 bg-blue-50 px-3 py-1 rounded-full uppercase tracking-wider">📊 Gerencial</span>
            <p class="text-[10px] text-gray-400 mt-1 font-medium">{{ now()->format('d M, Y H:i') }}</p>
        </div>
    </div>

    <!-- Selector de Reportes -->
    <div class="bg-white rounded-2xl shadow-sm p-6 mb-6" style="border: 0.5px solid #e5e7eb;">
        <h3 class="text-sm font-semibold text-gray-700 mb-4 flex items-center gap-2">
            <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Selecciona un Reporte
        </h3>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-3">
            @foreach([
                ['id' => 'atenciones_especialidad', 'label' => 'Atenciones por Especialidad', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', 'color' => 'blue'],
                ['id' => 'pacientes_hospitalizados', 'label' => 'Pacientes Hospitalizados', 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4', 'color' => 'green'],
                ['id' => 'cirugias_realizadas', 'label' => 'Cirugías Realizadas', 'icon' => 'M14.121 14.121L19 19m-7-7l7-7m-7 7l-2.879 2.879M12 12L9.121 9.121m0 5.758a3 3 0 10-4.243 4.243 3 3 0 004.243-4.243zm8.486-8.486a3 3 0 10-4.243 4.243 3 3 0 004.243-4.243z', 'color' => 'red'],
                ['id' => 'emergencias', 'label' => 'Emergencias', 'icon' => 'M13 10V3L4 14h7v7l9-11h-7z', 'color' => 'orange'],
                ['id' => 'ingresos_servicio', 'label' => 'Ingresos por Servicio', 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'emerald'],
                ['id' => 'cuentas_cobrar', 'label' => 'Cuentas por Cobrar', 'icon' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z', 'color' => 'purple'],
                ['id' => 'morosidad', 'label' => 'Análisis de Morosidad', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'rose'],
                ['id' => 'cierre_caja', 'label' => 'Cierre de Caja', 'icon' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z', 'color' => 'cyan'],
                ['id' => 'uso_quirofanos', 'label' => 'Uso de Quirófanos', 'icon' => 'M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z', 'color' => 'indigo'],
                ['id' => 'ocupacion_camas', 'label' => 'Ocupación de Camas', 'icon' => 'M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01', 'color' => 'teal'],
                ['id' => 'stock_farmacia', 'label' => 'Stock de Farmacia', 'icon' => 'M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z', 'color' => 'amber'],
                ['id' => 'productividad_medica', 'label' => 'Productividad Médica', 'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z', 'color' => 'pink'],
            ] as $reporte)
            <button onclick="selectReport('{{ $reporte['id'] }}')" id="btn-{{ $reporte['id'] }}" class="report-btn group p-4 rounded-xl border border-gray-200 hover:border-{{ $reporte['color'] }}-400 hover:bg-{{ $reporte['color'] }}-50 transition-all duration-200 text-left">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-{{ $reporte['color'] }}-100 rounded-lg group-hover:bg-{{ $reporte['color'] }}-200 transition">
                        <svg class="w-5 h-5 text-{{ $reporte['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $reporte['icon'] }}"/>
                        </svg>
                    </div>
                    <span class="text-xs font-medium text-gray-700 group-hover:text-{{ $reporte['color'] }}-700 leading-tight">{{ $reporte['label'] }}</span>
                </div>
            </button>
            @endforeach
        </div>
    </div>

    <!-- Filtros -->
    <div class="bg-white rounded-2xl shadow-sm p-4 mb-6 flex flex-wrap items-center gap-4" style="border: 0.5px solid #e5e7eb;">
        <div class="flex items-center gap-2 text-sm font-medium text-gray-700">
            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            Período:
        </div>
        <div class="flex items-center gap-2">
            <input type="date" id="desde" value="{{ $desde }}" class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
            <span class="text-gray-500">-</span>
            <input type="date" id="hasta" value="{{ $hasta }}" class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
        </div>
        <button onclick="generateReport()" id="btn-generar" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition flex items-center gap-2" disabled>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Generar Reporte
        </button>
        <button onclick="exportToExcel()" id="btn-exportar" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition flex items-center gap-2 hidden">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Exportar Excel
        </button>
    </div>

    <!-- Área de Resultados -->
    <div id="report-container" class="bg-white rounded-2xl shadow-sm overflow-hidden" style="border: 0.5px solid #e5e7eb;">
        <!-- Estado Vacío -->
        <div id="empty-state" class="p-12 text-center">
            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-800 mb-2">Selecciona un reporte</h3>
            <p class="text-sm text-gray-500">Elige un tipo de reporte arriba y define el período para generar los datos</p>
        </div>

        <!-- Loading -->
        <div id="loading-state" class="hidden p-12 text-center">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
            <p class="mt-3 text-sm text-gray-500">Generando reporte...</p>
        </div>

        <!-- Resultados -->
        <div id="results-state" class="hidden">
            <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                <h3 id="report-title" class="text-lg font-semibold text-gray-800"></h3>
                <span id="report-count" class="text-sm text-gray-500"></span>
            </div>
            <div class="overflow-x-auto max-h-[500px] overflow-y-auto">
                <table id="report-table" class="w-full text-sm">
                    <thead class="bg-gray-50 sticky top-0">
                        <tr id="report-header"></tr>
                    </thead>
                    <tbody id="report-body" class="divide-y divide-gray-200"></tbody>
                </table>
            </div>
            <div id="report-summary" class="hidden p-4 bg-gray-50 border-t border-gray-200">
                <span id="summary-label" class="text-sm font-medium text-gray-700"></span>
                <span id="summary-value" class="text-sm font-bold text-gray-900 ml-2"></span>
            </div>
        </div>
    </div>
</div>

<script>
let currentReportType = null;
let currentData = null;

const reportNames = {
    'atenciones_especialidad': 'Atenciones por Especialidad',
    'pacientes_hospitalizados': 'Pacientes Hospitalizados',
    'cirugias_realizadas': 'Cirugías Realizadas',
    'emergencias': 'Emergencias',
    'ingresos_servicio': 'Ingresos por Servicio',
    'cuentas_cobrar': 'Cuentas por Cobrar',
    'morosidad': 'Análisis de Morosidad',
    'cierre_caja': 'Cierre de Caja',
    'uso_quirofanos': 'Uso de Quirófanos',
    'ocupacion_camas': 'Ocupación de Camas',
    'stock_farmacia': 'Stock de Farmacia',
    'productividad_medica': 'Productividad Médica'
};

function selectReport(type) {
    // Desactivar botón anterior
    document.querySelectorAll('.report-btn').forEach(btn => {
        btn.classList.remove('ring-2', 'ring-blue-500', 'bg-blue-50', 'border-blue-400');
    });
    
    // Activar nuevo botón
    const btn = document.getElementById('btn-' + type);
    btn.classList.add('ring-2', 'ring-blue-500', 'bg-blue-50', 'border-blue-400');
    
    currentReportType = type;
    document.getElementById('btn-generar').disabled = false;
    
    // Ocultar resultados previos
    hideResults();
}

function hideResults() {
    document.getElementById('empty-state').classList.remove('hidden');
    document.getElementById('loading-state').classList.add('hidden');
    document.getElementById('results-state').classList.add('hidden');
    document.getElementById('btn-exportar').classList.add('hidden');
    currentData = null;
}

function generateReport() {
    if (!currentReportType) return;
    
    const desde = document.getElementById('desde').value;
    const hasta = document.getElementById('hasta').value;
    
    document.getElementById('empty-state').classList.add('hidden');
    document.getElementById('loading-state').classList.remove('hidden');
    document.getElementById('results-state').classList.add('hidden');
    document.getElementById('btn-exportar').classList.add('hidden');
    
    fetch(`/gerencial/reportes/data?tipo=${currentReportType}&desde=${desde}&hasta=${hasta}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        currentData = data;
        renderReport(data);
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al generar el reporte');
        document.getElementById('loading-state').classList.add('hidden');
        document.getElementById('empty-state').classList.remove('hidden');
    });
}

function renderReport(data) {
    document.getElementById('loading-state').classList.add('hidden');
    document.getElementById('results-state').classList.remove('hidden');
    
    // Título
    document.getElementById('report-title').textContent = data.title;
    document.getElementById('report-count').textContent = `${data.rows.length} registros`;
    
    // Headers
    const headerRow = document.getElementById('report-header');
    headerRow.innerHTML = data.columns.map(col => 
        `<th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider bg-gray-50">${col.label}</th>`
    ).join('');
    
    // Body
    const tbody = document.getElementById('report-body');
    if (data.rows.length === 0) {
        tbody.innerHTML = `<tr><td colspan="${data.columns.length}" class="px-4 py-8 text-center text-gray-500">No se encontraron registros para este período</td></tr>`;
    } else {
        tbody.innerHTML = data.rows.map(row => {
            return `<tr class="hover:bg-gray-50 transition">` + 
                data.columns.map(col => {
                    const value = row[col.key] ?? '-';
                    const isNumeric = !isNaN(parseFloat(value)) && value.toString().includes('.');
                    const alignClass = isNumeric ? 'text-right' : 'text-left';
                    return `<td class="px-4 py-3 ${alignClass} text-sm text-gray-700">${value}</td>`;
                }).join('') + 
                `</tr>`;
        }).join('');
    }
    
    // Summary
    const summaryDiv = document.getElementById('report-summary');
    if (data.summary) {
        summaryDiv.classList.remove('hidden');
        document.getElementById('summary-label').textContent = data.summary.label + ':';
        document.getElementById('summary-value').textContent = data.summary.value;
    } else {
        summaryDiv.classList.add('hidden');
    }
    
    // Mostrar botón exportar
    if (data.rows.length > 0) {
        document.getElementById('btn-exportar').classList.remove('hidden');
    }
}

function exportToExcel() {
    if (!currentReportType) return;
    
    const desde = document.getElementById('desde').value;
    const hasta = document.getElementById('hasta').value;
    
    window.location.href = `/gerencial/reportes/export?tipo=${currentReportType}&desde=${desde}&hasta=${hasta}`;
}
</script>
@endsection
