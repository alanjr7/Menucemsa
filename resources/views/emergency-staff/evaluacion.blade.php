@extends('layouts.app')

@section('content')

@php
// Obtener permisos del usuario actual
$user = auth()->user();
$userPermissions = [];

if ($user->isEnfermeraEmergencia()) {
    $enfermera = \App\Models\Enfermera::where('user_id', $user->id)->first();
    if ($enfermera) {
        $userPermissions = $enfermera->getPermissionKeys();
    }
} else {
    // Roles emergencia, admin, dirmedico tienen todos los permisos
    $userPermissions = array_keys(\App\Models\EnfermeraPermission::AVAILABLE_PERMISSIONS);
}

// Helper function to check permission
$hasPermission = function($permission) use ($userPermissions) {
    return in_array($permission, $userPermissions);
};

// Inicializar array de medicamentos (vacío si no tiene permisos)
$medsArray = [];
@endphp

<div class="p-6 bg-gray-50/50 min-h-screen">
    <!-- Header -->
    <div class="flex justify-between items-end mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Evaluación de Emergencia</h1>
            <p class="text-sm text-gray-500">Paciente: <span class="font-medium text-gray-700">{{ $emergency->is_temp_id ? 'Paciente Temporal' : ($emergency->paciente?->nombre ?? 'Desconocido') }}</span></p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('emergency-staff.dashboard') }}" class="flex items-center px-4 py-2 bg-white border border-gray-200 text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition-all shadow-sm">
                <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Volver al Dashboard
            </a>
        </div>
    </div>

    <!-- Información del Paciente -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
        <div class="flex items-center gap-4 mb-4">
            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <div>
                <h2 class="text-lg font-bold text-gray-800">{{ $emergency->is_temp_id ? 'Paciente Temporal' : ($emergency->paciente?->nombre ?? 'Desconocido') }}</h2>
                <p class="text-sm text-gray-500">Código: <span class="font-mono font-medium">{{ $emergency->code }}</span> | {{ $emergency->is_temp_id ? 'ID: ' . $emergency->temp_id : 'CI: ' . $emergency->patient_id }}</p>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
            <div class="bg-gray-50 rounded-lg p-3">
                <span class="text-gray-500 block text-xs">Tipo Ingreso</span>
                <span class="font-medium text-gray-800">{{ $emergency->tipo_ingreso_label }}</span>
            </div>
            <div class="bg-gray-50 rounded-lg p-3">
                <span class="text-gray-500 block text-xs">Destino Inicial</span>
                <span class="font-medium text-gray-800 capitalize">{{ $emergency->destino_inicial ?? 'Pendiente' }}</span>
            </div>
            <div class="bg-gray-50 rounded-lg p-3">
                <span class="text-gray-500 block text-xs">Hora Ingreso</span>
                <span class="font-medium text-gray-800">{{ $emergency->admission_date?->format('H:i') ?? $emergency->created_at->format('H:i') }}</span>
            </div>
            <div class="bg-gray-50 rounded-lg p-3">
                <span class="text-gray-500 block text-xs">Estado Actual</span>
                <span class="px-2 py-1 rounded-full text-xs font-medium {{ $emergency->status_color }}-100 text-{{ $emergency->status_color }}-800">
                    {{ $emergency->status }}
                </span>
            </div>
        </div>
    </div>

    <form id="formEvaluacion" class="space-y-6">
        @csrf

        <!-- Sección 1: Nivel de Gravedad -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-800">Nivel de Gravedad</h3>
                <span class="text-red-500">*</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <label class="cursor-pointer">
                    <input type="radio" name="nivel_gravedad" value="leve" class="sr-only peer" required>
                    <div class="p-4 border-2 border-gray-200 rounded-xl hover:border-green-400 peer-checked:border-green-500 peer-checked:bg-green-50 transition-all">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-3 h-3 rounded-full bg-green-500"></div>
                            <span class="font-semibold text-gray-800">Leve</span>
                        </div>
                        <p class="text-xs text-gray-500">Paciente estable, sin riesgo inmediato</p>
                    </div>
                </label>

                <label class="cursor-pointer">
                    <input type="radio" name="nivel_gravedad" value="moderado" class="sr-only peer">
                    <div class="p-4 border-2 border-gray-200 rounded-xl hover:border-yellow-400 peer-checked:border-yellow-500 peer-checked:bg-yellow-50 transition-all">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
                            <span class="font-semibold text-gray-800">Moderado</span>
                        </div>
                        <p class="text-xs text-gray-500">Requiere atención médica prioritaria</p>
                    </div>
                </label>

                <label class="cursor-pointer">
                    <input type="radio" name="nivel_gravedad" value="grave" class="sr-only peer">
                    <div class="p-4 border-2 border-gray-200 rounded-xl hover:border-orange-400 peer-checked:border-orange-500 peer-checked:bg-orange-50 transition-all">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-3 h-3 rounded-full bg-orange-500"></div>
                            <span class="font-semibold text-gray-800">Grave</span>
                        </div>
                        <p class="text-xs text-gray-500">Condición seria, monitoreo constante</p>
                    </div>
                </label>

                <label class="cursor-pointer">
                    <input type="radio" name="nivel_gravedad" value="critico" class="sr-only peer">
                    <div class="p-4 border-2 border-gray-200 rounded-xl hover:border-red-400 peer-checked:border-red-500 peer-checked:bg-red-50 transition-all">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-3 h-3 rounded-full bg-red-500"></div>
                            <span class="font-semibold text-gray-800">Crítico</span>
                        </div>
                        <p class="text-xs text-gray-500">Riesgo vital, intervención inmediata</p>
                    </div>
                </label>
            </div>
        </div>

        <!-- Sección 2: Signos Vitales -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-800">Signos Vitales</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Presión Arterial</label>
                    <div class="relative">
                        <input type="text" name="presion_arterial" value="{{ $vitalSigns['presion_arterial'] ?? '' }}"
                            placeholder="120/80"
                            class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all">
                        <span class="absolute right-3 top-3 text-xs text-gray-400">mmHg</span>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Frecuencia Cardíaca</label>
                    <div class="relative">
                        <input type="text" name="frecuencia_cardiaca" value="{{ $vitalSigns['frecuencia_cardiaca'] ?? '' }}"
                            placeholder="80"
                            class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all">
                        <span class="absolute right-3 top-3 text-xs text-gray-400">lpm</span>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Frecuencia Respiratoria</label>
                    <div class="relative">
                        <input type="text" name="frecuencia_respiratoria" value="{{ $vitalSigns['frecuencia_respiratoria'] ?? '' }}"
                            placeholder="16"
                            class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all">
                        <span class="absolute right-3 top-3 text-xs text-gray-400">rpm</span>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Temperatura</label>
                    <div class="relative">
                        <input type="text" name="temperatura" value="{{ $vitalSigns['temperatura'] ?? '' }}"
                            placeholder="37.0"
                            class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all">
                        <span class="absolute right-3 top-3 text-xs text-gray-400">°C</span>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Saturación O2</label>
                    <div class="relative">
                        <input type="text" name="saturacion_o2" value="{{ $vitalSigns['saturacion_o2'] ?? '' }}"
                            placeholder="98"
                            class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all">
                        <span class="absolute right-3 top-3 text-xs text-gray-400">%</span>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Glucosa</label>
                    <div class="relative">
                        <input type="text" name="glucosa"
                            placeholder="100"
                            class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all">
                        <span class="absolute right-3 top-3 text-xs text-gray-400">mg/dL</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección 3: Medicamentos e Insumos -->
        @if($hasPermission('aplicar_medicamentos'))
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">Medicamentos e Insumos</h3>
                        <p class="text-sm text-gray-500">Seleccione los medicamentos a aplicar (desde almacén de emergencia)</p>
                    </div>
                </div>
                <button type="button" onclick="agregarMedicamento()" class="flex items-center px-4 py-2 bg-purple-600 text-white rounded-xl hover:bg-purple-700 transition-colors text-sm font-medium">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Agregar Medicamento
                </button>
            </div>

            <!-- Lista de medicamentos seleccionados -->
            <div id="listaMedicamentos" class="space-y-3">
                <div id="mensajeVacio" class="text-center py-8 bg-gray-50 rounded-xl">
                    <svg class="w-12 h-12 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                    </svg>
                    <p class="text-gray-500 text-sm">No hay medicamentos seleccionados</p>
                    <p class="text-gray-400 text-xs mt-1">Haga clic en "Agregar Medicamento" para seleccionar</p>
                </div>
            </div>

            <!-- Total estimado -->
            <div id="resumenCostos" class="hidden mt-6 pt-6 border-t border-gray-200">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Total estimado de medicamentos:</span>
                    <span class="text-2xl font-bold text-purple-600" id="totalMedicamentos">Bs. 0.00</span>
                </div>
            </div>
        </div>
        @endif

        <!-- Sección 4: Motivo y Observaciones -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-800">Observaciones Clínicas</h3>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Motivo de Consulta / Síntomas</label>
                    <textarea name="motivo_consulta" rows="3" placeholder="Describa el motivo de consulta y síntomas principales..."
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all">{{ $emergency->initial_assessment ?? $emergency->symptoms ?? '' }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Observaciones Adicionales</label>
                    <textarea name="observaciones" rows="3" placeholder="Alergias, antecedentes relevantes, notas adicionales..."
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-all">{{ $emergency->observations ?? '' }}</textarea>
                </div>
            </div>
        </div>

        <!-- Botones de Acción -->
        <div class="flex justify-between items-center pt-6">
            <a href="{{ route('emergency-staff.dashboard') }}" class="px-6 py-3 border border-gray-200 rounded-xl text-gray-700 font-medium hover:bg-gray-50 transition-colors">
                Cancelar
            </a>
            <button type="submit" id="btnGuardar" class="px-8 py-3 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-xl font-medium hover:from-red-600 hover:to-red-700 transition-all shadow-lg flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Guardar Evaluación
            </button>
        </div>
    </form>
</div>

@if($hasPermission('aplicar_medicamentos'))
<!-- Modal de selección de medicamentos -->
<div id="modalMedicamentos" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[80vh] overflow-hidden">
        <div class="bg-gradient-to-r from-purple-500 to-purple-600 text-white p-6">
            <div class="flex justify-between items-center">
                <h3 class="text-xl font-bold">Seleccionar Medicamento</h3>
                <button onclick="cerrarModalMedicamentos()" class="bg-white/20 hover:bg-white/30 p-2 rounded-full transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="mt-4">
                <input type="text" id="buscarMedicamento" placeholder="Buscar medicamento..."
                    class="w-full px-4 py-2 rounded-lg bg-white/20 border border-white/30 text-white placeholder-white/70 focus:outline-none focus:bg-white/30"
                    onkeyup="filtrarMedicamentos()">
            </div>
        </div>

        <div class="p-6 overflow-y-auto" style="height: 50vh; min-height: 400px;">
            <div id="listaMedicamentosDisponibles" class="space-y-2">
                @foreach($medicamentos as $medicamento)
                <div class="medicamento-item p-4 border border-gray-200 rounded-xl hover:bg-purple-50 hover:border-purple-300 cursor-pointer transition-all"
                     onclick="seleccionarMedicamento({{ $medicamento->id }}, '{{ $medicamento->nombre }}', {{ $medicamento->precio ?? 0 }}, '{{ $medicamento->unidad_medida }}', {{ $medicamento->cantidad }})"
                     data-nombre="{{ strtolower($medicamento->nombre) }}">
                    <div class="flex justify-between items-start">
                        <div>
                            <h4 class="font-semibold text-gray-800">{{ $medicamento->nombre }}</h4>
                            <p class="text-sm text-gray-500">{{ $medicamento->descripcion ?? 'Sin descripción' }}</p>
                            <div class="flex gap-3 mt-2 text-xs">
                                <span class="px-2 py-1 bg-gray-100 rounded">{{ $medicamento->tipo_label }}</span>
                                <span class="px-2 py-1 {{ $medicamento->estaBajoStock() ? 'bg-red-100 text-red-600' : 'bg-green-100 text-green-600' }} rounded">
                                    Stock: {{ $medicamento->cantidad }} {{ $medicamento->unidad_medida }}
                                </span>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="block text-lg font-bold text-purple-600">Bs. {{ number_format($medicamento->precio ?? 0, 2) }}</span>
                            <span class="text-xs text-gray-400">por {{ $medicamento->unidad_medida }}</span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <div id="mensajeSinResultados" class="hidden text-center py-8 text-gray-500" style="min-height: 200px;">
                <svg class="w-12 h-12 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p>No se encontraron medicamentos con esa búsqueda.</p>
            </div>
        </div>
    </div>
</div>

@php
    $medsArray = $medicamentos->map(function($med) {
        return [
            'id' => $med->id,
            'nombre' => $med->nombre,
            'precio' => $med->precio ?? 0,
            'unidad_medida' => $med->unidad_medida,
            'stock' => $med->cantidad
        ];
    })->values();
@endphp
@endif

<script>
    let medicamentosSeleccionados = [];
    let medicamentosDisponibles = @json($medsArray);

    function agregarMedicamento() {
        document.getElementById('modalMedicamentos').classList.remove('hidden');
    }

    function cerrarModalMedicamentos() {
        document.getElementById('modalMedicamentos').classList.add('hidden');
        document.getElementById('buscarMedicamento').value = '';
        filtrarMedicamentos();
    }

    function filtrarMedicamentos() {
        const busqueda = document.getElementById('buscarMedicamento').value.toLowerCase().trim();
        const items = document.querySelectorAll('#listaMedicamentosDisponibles .medicamento-item');
        const mensajeSinResultados = document.getElementById('mensajeSinResultados');
        const listaMedicamentos = document.getElementById('listaMedicamentosDisponibles');

        let encontrados = 0;
        items.forEach(item => {
            const nombre = item.getAttribute('data-nombre');
            if (!busqueda || nombre.includes(busqueda)) {
                item.style.display = 'block';
                encontrados++;
            } else {
                item.style.display = 'none';
            }
        });

        // Mostrar/ocultar mensaje sin resultados
        if (encontrados === 0 && busqueda.length > 0) {
            listaMedicamentos.classList.add('hidden');
            mensajeSinResultados.classList.remove('hidden');
        } else {
            listaMedicamentos.classList.remove('hidden');
            mensajeSinResultados.classList.add('hidden');
        }
    }

    function seleccionarMedicamento(id, nombre, precio, unidad, stock) {
        // Verificar si ya está seleccionado
        if (medicamentosSeleccionados.find(m => m.id === id)) {
            alert('Este medicamento ya está en la lista');
            return;
        }

        const medicamento = {
            id: id,
            nombre: nombre,
            precio: precio,
            unidad_medida: unidad,
            stock: stock,
            cantidad: 1
        };

        medicamentosSeleccionados.push(medicamento);
        cerrarModalMedicamentos();
        renderizarMedicamentos();
    }

    function eliminarMedicamento(index) {
        medicamentosSeleccionados.splice(index, 1);
        renderizarMedicamentos();
    }

    function actualizarCantidad(index, cantidad) {
        cantidad = parseInt(cantidad);
        if (cantidad < 1) cantidad = 1;
        if (cantidad > medicamentosSeleccionados[index].stock) {
            cantidad = medicamentosSeleccionados[index].stock;
            alert('La cantidad no puede superar el stock disponible');
        }
        medicamentosSeleccionados[index].cantidad = cantidad;
        renderizarMedicamentos();
    }

    function renderizarMedicamentos() {
        const contenedor = document.getElementById('listaMedicamentos');
        const mensajeVacio = document.getElementById('mensajeVacio');
        const resumenCostos = document.getElementById('resumenCostos');

        if (medicamentosSeleccionados.length === 0) {
            mensajeVacio.style.display = 'block';
            resumenCostos.classList.add('hidden');
            // Limpiar items excepto el mensaje vacío
            Array.from(contenedor.children).forEach(child => {
                if (child.id !== 'mensajeVacio') {
                    child.remove();
                }
            });
            return;
        }

        mensajeVacio.style.display = 'none';
        resumenCostos.classList.remove('hidden');

        // Limpiar items excepto el mensaje vacío
        Array.from(contenedor.children).forEach(child => {
            if (child.id !== 'mensajeVacio') {
                child.remove();
            }
        });

        let total = 0;

        medicamentosSeleccionados.forEach((med, index) => {
            const subtotal = med.precio * med.cantidad;
            total += subtotal;

            const item = document.createElement('div');
            item.className = 'flex items-center gap-4 p-4 bg-gray-50 rounded-xl';
            item.innerHTML = `
                <div class="flex-1">
                    <h4 class="font-semibold text-gray-800">${med.nombre}</h4>
                    <p class="text-sm text-gray-500">Bs. ${med.precio.toFixed(2)} / ${med.unidad_medida}</p>
                </div>
                <div class="flex items-center gap-3">
                    <button type="button" onclick="actualizarCantidad(${index}, ${med.cantidad - 1})"
                        class="w-8 h-8 rounded-full bg-white border border-gray-300 flex items-center justify-center hover:bg-gray-100 transition-colors"
                        ${med.cantidad <= 1 ? 'disabled' : ''}>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                        </svg>
                    </button>
                    <input type="number" value="${med.cantidad}" min="1" max="${med.stock}"
                        onchange="actualizarCantidad(${index}, this.value)"
                        class="w-16 text-center border border-gray-200 rounded-lg py-1 text-sm">
                    <button type="button" onclick="actualizarCantidad(${index}, ${med.cantidad + 1})"
                        class="w-8 h-8 rounded-full bg-white border border-gray-300 flex items-center justify-center hover:bg-gray-100 transition-colors"
                        ${med.cantidad >= med.stock ? 'disabled' : ''}>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                    </button>
                </div>
                <div class="text-right min-w-[100px]">
                    <span class="block font-bold text-purple-600">Bs. ${subtotal.toFixed(2)}</span>
                </div>
                <button type="button" onclick="eliminarMedicamento(${index})"
                    class="w-8 h-8 rounded-full bg-red-100 text-red-600 flex items-center justify-center hover:bg-red-200 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            `;
            contenedor.appendChild(item);
        });

        document.getElementById('totalMedicamentos').textContent = 'Bs. ' + total.toFixed(2);
    }

    // Cerrar modal al hacer click fuera
    document.getElementById('modalMedicamentos').addEventListener('click', function(e) {
        if (e.target === this) {
            cerrarModalMedicamentos();
        }
    });

    // Manejar envío del formulario
    document.getElementById('formEvaluacion').addEventListener('submit', async function(e) {
        e.preventDefault();

        const btnGuardar = document.getElementById('btnGuardar');
        btnGuardar.disabled = true;
        btnGuardar.innerHTML = `
            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Guardando...
        `;

        const formData = new FormData(this);
        const data = {
            nivel_gravedad: formData.get('nivel_gravedad'),
            presion_arterial: formData.get('presion_arterial'),
            frecuencia_cardiaca: formData.get('frecuencia_cardiaca'),
            frecuencia_respiratoria: formData.get('frecuencia_respiratoria'),
            temperatura: formData.get('temperatura'),
            saturacion_o2: formData.get('saturacion_o2'),
            glucosa: formData.get('glucosa'),
            motivo_consulta: formData.get('motivo_consulta'),
            observaciones: formData.get('observaciones'),
            medicamentos: medicamentosSeleccionados.map(m => ({
                id: m.id,
                cantidad: m.cantidad
            }))
        };

        try {
            const response = await fetch('{{ route("emergency-staff.guardar-evaluacion", $emergency) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (result.success) {
                alert('Evaluación guardada correctamente');
                window.location.href = result.redirect || '{{ route("emergency-staff.dashboard") }}';
            } else {
                alert('Error: ' + result.message);
                btnGuardar.disabled = false;
                btnGuardar.innerHTML = `
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Guardar Evaluación
                `;
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error al guardar la evaluación');
            btnGuardar.disabled = false;
            btnGuardar.innerHTML = `
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Guardar Evaluación
            `;
        }
    });
</script>
@endsection
