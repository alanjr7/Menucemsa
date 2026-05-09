@extends('layouts.app')

@section('content')
<div class="w-full p-4 bg-gray-50/50 min-h-screen">

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-800">Ejecutar Cirugía</h1>
            <p class="text-sm text-gray-500">Complete los datos de la intervención quirúrgica</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('quirofano.index') }}" class="flex items-center px-3 py-2 border border-gray-200 rounded-lg text-gray-600 bg-white hover:bg-gray-50 font-medium transition-colors text-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                <span class="hidden sm:inline">Volver al Panel</span>
                <span class="sm:hidden">←</span>
            </a>
            <button onclick="cancelarCita({{ $cita->id }})" class="flex items-center px-3 py-2 border border-red-300 text-red-700 rounded-lg hover:bg-red-50 font-medium transition-colors text-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                Cancelar Cirugía
            </button>
        </div>
    </div>

    <!-- Mensajes de Error -->
    <div id="errorMessages" class="hidden bg-red-50 border border-red-200 rounded-xl p-4 mb-6">
        <ul class="text-red-700 text-sm list-disc list-inside" id="errorList"></ul>
    </div>

    <form id="ejecutarCirugiaForm" class="space-y-6">
        @csrf

        <!-- Sección 1: Información del Paciente -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                Información del Paciente
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="text-sm font-medium text-gray-500">Nombre</label>
                    <p class="font-semibold text-gray-900">{{ $cita->paciente->nombre }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-500">CI</label>
                    <p class="font-semibold text-gray-900">{{ $cita->paciente->ci }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-500">Seguro</label>
                    <p class="font-semibold text-gray-900">{{ $cita->paciente->seguro->nombre ?? 'N/A' }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-500">Edad</label>
                    <p class="font-semibold text-gray-900">
                        @if($cita->paciente->fecha_nacimiento)
                            {{ $cita->paciente->fecha_nacimiento->age }} años
                        @else
                            N/A
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <!-- Sección 2: Datos de la Cirugía -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                Datos de la Cirugía
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div>
                    <label for="fecha" class="block text-sm font-medium text-gray-700 mb-2">Fecha de Ejecución *</label>
                    <input type="date" id="fecha" name="fecha" value="{{ $cita->fecha->format('Y-m-d') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           required>
                </div>
                <div>
                    <label for="hora_inicio" class="block text-sm font-medium text-gray-700 mb-2">Hora Inicio *</label>
                    <input type="time" id="hora_inicio" name="hora_inicio"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           required>
                </div>
                <div>
                    <label for="hora_fin" class="block text-sm font-medium text-gray-700 mb-2">Hora Fin *</label>
                    <input type="time" id="hora_fin" name="hora_fin"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Duración Calculada</label>
                    <div id="duracionCalculada" class="text-lg font-semibold text-blue-600">-- min</div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tipo Final Estimado</label>
                    <div id="tipoFinalEstimado" class="text-lg font-semibold text-amber-600">--</div>
                </div>
                <div>
                    <label for="tipo_cirugia" class="block text-sm font-medium text-gray-700 mb-2">Tipo de Cirugía *</label>
                    <select id="tipo_cirugia" name="tipo_cirugia" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Seleccione...</option>
                        @foreach($tiposCirugia as $tipo)
                            <option value="{{ $tipo->nombre }}" {{ $cita->tipo_cirugia === $tipo->nombre ? 'selected' : '' }}>
                                {{ ucfirst($tipo->nombre) }} ({{ $tipo->duracionFormateada }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="descripcion_cirugia" class="block text-sm font-medium text-gray-700 mb-2">Descripción de la Cirugía</label>
                    <textarea id="descripcion_cirugia" name="descripcion_cirugia" rows="3"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Descripción detallada de la cirugía realizada...">{{ $cita->descripcion_cirugia }}</textarea>
                </div>
                <div>
                    <label for="observaciones" class="block text-sm font-medium text-gray-700 mb-2">Observaciones</label>
                    <textarea id="observaciones" name="observaciones" rows="3"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Observaciones adicionales...">{{ $cita->observaciones }}</textarea>
                </div>
            </div>
        </div>

        <!-- Sección 3: Medicamentos -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
    <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
        <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
        </svg>
        Medicamentos Utilizados
    </h2>

    <!-- Lista de medicamentos agregados -->
    <div id="listaMedicamentos" class="space-y-2 mb-4 max-h-64 overflow-y-auto">
        <p class="text-gray-500 text-sm text-center py-4">No hay medicamentos agregados</p>
    </div>

    <!-- Buscador de medicamentos -->
    <div class="border-t border-gray-200 pt-4">
        <h4 class="text-sm font-medium text-gray-700 mb-3">Agregar Medicamento</h4>
        
        <!-- Buscador -->
        <div class="relative mb-3">
            <div class="relative">
                <input type="text" 
                       id="buscadorMedicamento" 
                       placeholder="🔍 Buscar medicamento por nombre, presentación o concentración..."
                       autocomplete="off"
                       class="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <div id="loadingMedicamentos" class="absolute right-3 top-3 hidden">
                    <svg class="animate-spin h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
            </div>
            
            <!-- Resultados de búsqueda -->
            <div id="resultadosBusqueda" class="absolute z-10 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg hidden max-h-60 overflow-y-auto">
                <!-- Los resultados se llenarán dinámicamente -->
            </div>
        </div>
        
        <!-- Cantidad y botón agregar (se muestra cuando se selecciona un medicamento) -->
        <div id="seleccionContainer" class="hidden bg-blue-50 rounded-lg p-3 mt-3">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p id="medicamentoSeleccionadoNombre" class="font-medium text-gray-900"></p>
                    <p class="text-xs text-gray-500">Stock disponible: <span id="stockDisponible">0</span> unidades</p>
                    <p class="text-xs text-gray-500">Precio unitario: Bs. <span id="precioUnitario">0</span></p>
                </div>
                <div class="flex gap-2">
                    <input type="number" id="medicamentoCantidad" min="1" value="1" 
                           class="w-24 border border-gray-300 rounded-lg px-3 py-2 text-sm text-center">
                    <button type="button" onclick="agregarMedicamentoSeleccionado()"
                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        Agregar
                    </button>
                    <button type="button" onclick="limpiarSeleccion()"
                            class="border border-gray-300 text-gray-600 hover:bg-gray-50 px-3 py-2 rounded-lg text-sm">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

        <!-- Sección 4: Equipos y Procedimientos -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/>
                </svg>
                Equipos y Procedimientos
            </h2>

            <!-- Lista de equipos agregados -->
            <div id="listaEquipos" class="space-y-2 mb-4">
                <p class="text-gray-500 text-sm text-center py-4">No hay equipos agregados</p>
            </div>

            <!-- Formulario para agregar equipo -->
            <div class="border-t border-gray-200 pt-4">
                <h4 class="text-sm font-medium text-gray-700 mb-3">Agregar Equipo/Procedimiento</h4>
                <div class="grid grid-cols-1 md:grid-cols-12 gap-3">
                    <div class="md:col-span-4">
                        <input type="text" id="equipoNombre" placeholder="Nombre del equipo"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    </div>
                    <div class="md:col-span-3">
                        <input type="number" id="equipoPrecio" min="0" step="0.01" placeholder="Precio (Bs.)"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    </div>
                    <div class="md:col-span-2">
                        <input type="number" id="equipoCantidad" min="1" value="1"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"
                               placeholder="Cantidad">
                    </div>
                    <div class="md:col-span-2">
                        <button type="button" onclick="agregarEquipo()"
                                class="w-full bg-cyan-600 hover:bg-cyan-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                            Agregar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección 5: Resumen de Costos -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Resumen de Costos
            </h2>
            <div class="space-y-3">
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <span class="text-sm text-gray-600">Costo Base</span>
                    <span class="font-semibold text-gray-900">Bs. {{ number_format($cita->costo_base, 2) }}</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <span class="text-sm text-gray-600">Costo Extra (por tiempo adicional)</span>
                    <span class="font-semibold text-amber-600" id="costoExtraPreview">Bs. 0.00</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <span class="text-sm text-gray-600">Medicamentos</span>
                    <span class="font-semibold text-green-600" id="costoMedicamentosPreview">Bs. 0.00</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <span class="text-sm text-gray-600">Equipos y Procedimientos</span>
                    <span class="font-semibold text-cyan-600" id="costoEquiposPreview">Bs. 0.00</span>
                </div>
                <div class="flex justify-between items-center pt-3">
                    <span class="text-lg font-bold text-gray-900">Total Estimado</span>
                    <span class="text-2xl font-bold text-green-600" id="costoTotalPreview">Bs. {{ number_format($cita->costo_base, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Sección 6: Botones de Acción -->
        <div class="flex flex-col sm:flex-row gap-4 justify-end">
            <a href="{{ route('quirofano.index') }}"
               class="px-6 py-3 border border-gray-300 text-gray-700 rounded-xl font-medium hover:bg-gray-50 transition-colors text-center">
                Cancelar
            </a>
            <button type="submit"
                    class="px-6 py-3 bg-green-600 text-white rounded-xl font-medium hover:bg-green-700 transition-colors flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Guardar y Finalizar Cirugía
            </button>
        </div>
    </form>

    <!-- Inputs ocultos para arrays -->
    <input type="hidden" id="medicamentosInput" name="medicamentos" value="[]">
    <input type="hidden" id="equiposInput" name="equipos" value="[]">
</div>

<!-- Modal de Cancelación -->
<div id="cancelModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Cancelar Cita Quirúrgica</h3>
        <form id="cancelForm">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Motivo de Cancelación *</label>
                <textarea name="motivo_cancelacion" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required placeholder="Describir el motivo de la cancelación..."></textarea>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeCancelModal()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                    Volver
                </button>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                    Confirmar Cancelación
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Variables globales
let medicamentosAgregados = [];
let equiposAgregados = [];
let costoBase = {{ $cita->costo_base }};

// Tipos de cirugía con duraciones
const tiposCirugia = {};
@foreach($tiposCirugia as $tipo)
    tiposCirugia['{{ $tipo->nombre }}'] = {
        duracion: {{ $tipo->duracion_minutos }},
        costoMinutoExtra: {{ $tipo->costo_minuto_extra }}
    };
@endforeach

// Calcular duración y costos
function calcularDuracion() {
    const horaInicio = document.getElementById('hora_inicio').value;
    const horaFin = document.getElementById('hora_fin').value;

    if (!horaInicio || !horaFin) {
        document.getElementById('duracionCalculada').textContent = '-- min';
        document.getElementById('tipoFinalEstimado').textContent = '--';
        return 0;
    }

    const [h1, m1] = horaInicio.split(':').map(Number);
    const [h2, m2] = horaFin.split(':').map(Number);

    let minutos = (h2 * 60 + m2) - (h1 * 60 + m1);
    if (minutos < 0) minutos += 24 * 60; // Cruzó medianoche

    // Determinar tipo final
    let tipoFinal = '';
    if (minutos <= 45) tipoFinal = 'ambulatoria';
    else if (minutos <= 60) tipoFinal = 'menor';
    else if (minutos <= 90) tipoFinal = 'mediana';
    else tipoFinal = 'mayor';

    document.getElementById('duracionCalculada').textContent = minutos + ' min';
    document.getElementById('tipoFinalEstimado').textContent = tipoFinal.charAt(0).toUpperCase() + tipoFinal.slice(1);

    actualizarCostoExtra(minutos, tipoFinal);
    return minutos;
}

function actualizarCostoExtra(duracion, tipoFinal) {
    const tipoCirugia = tiposCirugia[tipoFinal];
    let costoExtra = 0;

    if (tipoCirugia && duracion > tipoCirugia.duracion) {
        const minutosExtra = duracion - tipoCirugia.duracion;
        costoExtra = minutosExtra * tipoCirugia.costoMinutoExtra;
    }

    document.getElementById('costoExtraPreview').textContent = 'Bs. ' + costoExtra.toFixed(2);
    actualizarCostoTotal();
}

// ========== GESTIÓN DE MEDICAMENTOS CON BUSCADOR ==========

let todosLosMedicamentos = [];
let medicamentoSeleccionado = null;
let busquedaTimeout = null;

// Cargar medicamentos al iniciar
async function cargarMedicamentos() {
    const loadingIcon = document.getElementById('loadingMedicamentos');
    loadingIcon.classList.remove('hidden');
    
    try {
        const response = await fetch(`/quirofano/{{ $cita->id }}/medicamentos-disponibles`);
        const data = await response.json();
        
        if (data.success) {
            todosLosMedicamentos = data.medicamentos;
        } else {
            console.error('Error cargando medicamentos:', data.message);
        }
    } catch (error) {
        console.error('Error:', error);
    } finally {
        loadingIcon.classList.add('hidden');
    }
}

// Función para resaltar texto
function highlightText(text, query) {
    if (!query || query.length < 2) return text;
    const regex = new RegExp(`(${query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');
    return text.replace(regex, '<mark class="highlight">$1</mark>');
}

// Buscar medicamentos
function buscarMedicamentos(query) {
    const resultadosDiv = document.getElementById('resultadosBusqueda');
    
    if (!query || query.length < 2) {
        resultadosDiv.classList.add('hidden');
        return;
    }
    
    const queryLower = query.toLowerCase();
    const resultados = todosLosMedicamentos.filter(med => {
        return med.nombre.toLowerCase().includes(queryLower) ||
               (med.presentacion && med.presentacion.toLowerCase().includes(queryLower)) ||
               (med.concentracion && med.concentracion.toLowerCase().includes(queryLower));
    });
    
    if (resultados.length === 0) {
        resultadosDiv.innerHTML = '<div class="p-3 text-center text-gray-500">No se encontraron medicamentos</div>';
        resultadosDiv.classList.remove('hidden');
        return;
    }
    
    resultadosDiv.innerHTML = resultados.map(med => `
        <div class="resultado-item p-3 cursor-pointer border-b border-gray-100 hover:bg-gray-50" 
             data-id="${med.id}"
             data-stock-id="${med.stock_id}"
             data-nombre="${med.nombre}"
             data-precio="${med.precio}"
             data-cantidad="${med.cantidad}"
             onclick="seleccionarMedicamento(${med.id}, '${med.nombre.replace(/'/g, "\\'")}', ${med.precio}, ${med.cantidad})">
            <div class="font-medium text-gray-900">${highlightText(med.nombre, query)}</div>
            <div class="text-xs text-gray-500 flex gap-3 mt-1">
                <span>📦 Stock: ${med.cantidad} ${med.unidad_medida}</span>
                ${med.presentacion ? `<span>💊 ${med.presentacion}</span>` : ''}
                ${med.concentracion ? `<span>⚖️ ${med.concentracion}</span>` : ''}
                <span>💰 Bs. ${med.precio.toFixed(2)}</span>
            </div>
        </div>
    `).join('');
    
    resultadosDiv.classList.remove('hidden');
}

function seleccionarMedicamento(id, nombre, precio, stock) {
    medicamentoSeleccionado = { id, nombre, precio, stock };
    
    document.getElementById('medicamentoSeleccionadoNombre').textContent = nombre;
    document.getElementById('stockDisponible').textContent = stock;
    document.getElementById('precioUnitario').textContent = precio.toFixed(2);
    document.getElementById('medicamentoCantidad').value = 1;
    document.getElementById('medicamentoCantidad').max = stock;
    document.getElementById('seleccionContainer').classList.remove('hidden');
    
    // Limpiar buscador
    document.getElementById('buscadorMedicamento').value = '';
    document.getElementById('resultadosBusqueda').classList.add('hidden');
}

function limpiarSeleccion() {
    medicamentoSeleccionado = null;
    document.getElementById('seleccionContainer').classList.add('hidden');
    document.getElementById('buscadorMedicamento').value = '';
    document.getElementById('buscadorMedicamento').focus();
}

function agregarMedicamentoSeleccionado() {
    if (!medicamentoSeleccionado) {
        alert('Seleccione un medicamento primero');
        return;
    }
    
    const cantidad = parseInt(document.getElementById('medicamentoCantidad').value);
    
    if (isNaN(cantidad) || cantidad < 1) {
        alert('Ingrese una cantidad válida');
        return;
    }
    
    if (cantidad > medicamentoSeleccionado.stock) {
        alert(`Stock insuficiente. Solo hay ${medicamentoSeleccionado.stock} unidades disponibles.`);
        return;
    }
    
    const medicamento = {
        id: medicamentoSeleccionado.id,
        nombre: medicamentoSeleccionado.nombre,
        precio: medicamentoSeleccionado.precio,
        cantidad: cantidad,
        subtotal: medicamentoSeleccionado.precio * cantidad
    };
    
    medicamentosAgregados.push(medicamento);
    renderizarMedicamentos();
    actualizarCostosMedicamentos();
    
    // Reducir stock localmente
    medicamentoSeleccionado.stock -= cantidad;
    document.getElementById('stockDisponible').textContent = medicamentoSeleccionado.stock;
    document.getElementById('medicamentoCantidad').value = 1;
    
    // Si el stock llegó a 0, limpiar selección
    if (medicamentoSeleccionado.stock === 0) {
        limpiarSeleccion();
    }
}

// Función original agregarMedicamento (para compatibilidad, pero usaremos la nueva)
function agregarMedicamento() {
    agregarMedicamentoSeleccionado();
}

function eliminarMedicamento(index) {
    medicamentosAgregados.splice(index, 1);
    renderizarMedicamentos();
    actualizarCostosMedicamentos();
}

function renderizarMedicamentos() {
    const container = document.getElementById('listaMedicamentos');
    
    if (medicamentosAgregados.length === 0) {
        container.innerHTML = '<p class="text-gray-500 text-sm text-center py-4">No hay medicamentos agregados</p>';
        return;
    }
    
    container.innerHTML = medicamentosAgregados.map((med, index) => `
        <div class="flex justify-between items-center bg-gray-50 rounded-lg p-3">
            <div class="flex-1">
                <span class="font-medium text-gray-900">${med.nombre}</span>
                <span class="text-sm text-gray-500 ml-2">x${med.cantidad}</span>
                <div class="text-xs text-gray-500">Bs. ${med.precio.toFixed(2)} c/u</div>
            </div>
            <div class="flex items-center gap-3">
                <span class="font-semibold text-green-600">Bs. ${med.subtotal.toFixed(2)}</span>
                <button type="button" onclick="eliminarMedicamento(${index})"
                        class="text-red-500 hover:text-red-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    `).join('');
}

// Evento de búsqueda con debounce
document.getElementById('buscadorMedicamento').addEventListener('input', function(e) {
    clearTimeout(busquedaTimeout);
    const query = e.target.value;
    busquedaTimeout = setTimeout(() => buscarMedicamentos(query), 300);
});

// Cerrar resultados al hacer clic fuera
document.addEventListener('click', function(e) {
    const buscador = document.getElementById('buscadorMedicamento');
    const resultados = document.getElementById('resultadosBusqueda');
    
    if (!buscador.contains(e.target) && !resultados.contains(e.target)) {
        resultados.classList.add('hidden');
    }
});

// Validar cantidad máxima
document.getElementById('medicamentoCantidad').addEventListener('change', function() {
    const max = parseInt(this.max);
    const val = parseInt(this.value);
    if (val > max) {
        this.value = max;
        alert(`La cantidad no puede exceder el stock disponible (${max})`);
    }
    if (val < 1) this.value = 1;
});

// Cargar medicamentos al inicio
cargarMedicamentos();

function actualizarCostosMedicamentos() {
    const total = medicamentosAgregados.reduce((sum, med) => sum + med.subtotal, 0);
    document.getElementById('costoMedicamentosPreview').textContent = 'Bs. ' + total.toFixed(2);
    actualizarCostoTotal();
}

// Gestión de Equipos
function agregarEquipo() {
    const nombreInput = document.getElementById('equipoNombre');
    const precioInput = document.getElementById('equipoPrecio');
    const cantidadInput = document.getElementById('equipoCantidad');

    if (!nombreInput.value.trim()) {
        alert('Ingrese el nombre del equipo');
        return;
    }

    if (!precioInput.value || parseFloat(precioInput.value) <= 0) {
        alert('Ingrese un precio válido');
        return;
    }

    const equipo = {
        nombre: nombreInput.value.trim(),
        precio: parseFloat(precioInput.value),
        cantidad: parseInt(cantidadInput.value) || 1
    };

    equipo.subtotal = equipo.precio * equipo.cantidad;
    equiposAgregados.push(equipo);

    renderizarEquipos();
    actualizarCostosEquipos();

    // Resetear campos
    nombreInput.value = '';
    precioInput.value = '';
    cantidadInput.value = '1';
}

function eliminarEquipo(index) {
    equiposAgregados.splice(index, 1);
    renderizarEquipos();
    actualizarCostosEquipos();
}

function renderizarEquipos() {
    const container = document.getElementById('listaEquipos');

    if (equiposAgregados.length === 0) {
        container.innerHTML = '<p class="text-gray-500 text-sm text-center py-4">No hay equipos agregados</p>';
        return;
    }

    container.innerHTML = equiposAgregados.map((eq, index) => `
        <div class="flex justify-between items-center bg-gray-50 rounded-lg p-3">
            <div>
                <span class="font-medium text-gray-900">${eq.nombre}</span>
                <span class="text-sm text-gray-500">x${eq.cantidad}</span>
            </div>
            <div class="flex items-center gap-3">
                <span class="font-semibold text-cyan-600">Bs. ${eq.subtotal.toFixed(2)}</span>
                <button type="button" onclick="eliminarEquipo(${index})"
                        class="text-red-500 hover:text-red-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    `).join('');
}

function actualizarCostosEquipos() {
    const total = equiposAgregados.reduce((sum, eq) => sum + eq.subtotal, 0);
    document.getElementById('costoEquiposPreview').textContent = 'Bs. ' + total.toFixed(2);
    actualizarCostoTotal();
}

// Cálculo del total
function actualizarCostoTotal() {
    const costoExtra = parseFloat(document.getElementById('costoExtraPreview').textContent.replace('Bs. ', '')) || 0;
    const costoMedicamentos = parseFloat(document.getElementById('costoMedicamentosPreview').textContent.replace('Bs. ', '')) || 0;
    const costoEquipos = parseFloat(document.getElementById('costoEquiposPreview').textContent.replace('Bs. ', '')) || 0;

    const total = costoBase + costoExtra + costoMedicamentos + costoEquipos;
    document.getElementById('costoTotalPreview').textContent = 'Bs. ' + total.toFixed(2);
}

// Event Listeners
document.getElementById('hora_inicio').addEventListener('change', calcularDuracion);
document.getElementById('hora_fin').addEventListener('change', calcularDuracion);
document.getElementById('tipo_cirugia').addEventListener('change', calcularDuracion);

// Envío del formulario
document.getElementById('ejecutarCirugiaForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const duracion = calcularDuracion();
    if (duracion <= 0) {
        alert('La duración debe ser mayor a 0 minutos');
        return;
    }

    const formData = {
        fecha: document.getElementById('fecha').value,
        hora_inicio: document.getElementById('hora_inicio').value,
        hora_fin: document.getElementById('hora_fin').value,
        tipo_cirugia: document.getElementById('tipo_cirugia').value,
        descripcion_cirugia: document.getElementById('descripcion_cirugia').value,
        observaciones: document.getElementById('observaciones').value,
        medicamentos: medicamentosAgregados.map(m => ({
            id: m.id,
            cantidad: m.cantidad
        })),
        equipos: equiposAgregados.map(e => ({
            nombre: e.nombre,
            precio: e.precio,
            cantidad: e.cantidad
        }))
    };

    // Deshabilitar botón de envío
    const submitBtn = e.target.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="animate-spin">↻</span> Guardando...';

    fetch(`/quirofano/{{ $cita->id }}/ejecutar`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = data.redirect;
        } else {
            submitBtn.disabled = false;
            submitBtn.innerHTML = `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Guardar y Finalizar Cirugía`;

            if (data.errors) {
                const errorList = document.getElementById('errorList');
                errorList.innerHTML = Object.values(data.errors).map(e => `<li>${e}</li>`).join('');
                document.getElementById('errorMessages').classList.remove('hidden');
            } else {
                alert(data.message || 'Error al guardar la cirugía');
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        submitBtn.disabled = false;
        submitBtn.innerHTML = `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Guardar y Finalizar Cirugía`;
        alert('Error de conexión. Intente nuevamente.');
    });
});

// Cancelar cita
let currentCitaId = null;

function cancelarCita(citaId) {
    currentCitaId = citaId;
    document.getElementById('cancelModal').style.display = 'flex';
}

function closeCancelModal() {
    document.getElementById('cancelModal').style.display = 'none';
    currentCitaId = null;
}

document.getElementById('cancelForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const motivo = formData.get('motivo_cancelacion');

    fetch(`/quirofano/${currentCitaId}/cancelar`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ motivo_cancelacion: motivo })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = '{{ route("quirofano.index") }}';
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al cancelar la cita');
    });
});

// Cerrar modal al hacer clic fuera
document.getElementById('cancelModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeCancelModal();
    }
});

// Inicializar
window.addEventListener('DOMContentLoaded', calcularDuracion);
</script>
@endsection
