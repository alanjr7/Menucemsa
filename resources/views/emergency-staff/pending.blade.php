@extends('layouts.app')

@section('title', 'Emergencias Pendientes')

@section('content')
<div class="p-6 bg-gray-50/50 min-h-screen">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end mb-8 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Emergencias Pendientes</h1>
            <p class="text-sm text-gray-500">Pacientes en espera de asignación</p>
        </div>
        <div class="flex gap-3">
            <button onclick="location.reload()" class="flex items-center px-4 py-2 bg-white border border-gray-200 text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition-all shadow-sm">
                <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Actualizar
            </button>
            <a href="{{ route('emergency-staff.dashboard') }}" class="flex items-center px-4 py-2 bg-white border border-gray-200 text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition-all shadow-sm">
                <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Volver al Panel
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-14 h-14 bg-yellow-100 rounded-2xl flex items-center justify-center">
                <svg class="w-7 h-7 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <span class="text-gray-500 text-sm font-medium">Pendientes</span>
                <p class="text-2xl font-bold text-gray-800">{{ $emergencies->count() }}</p>
            </div>
        </div>
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-14 h-14 bg-red-100 rounded-2xl flex items-center justify-center">
                <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <div>
                <span class="text-gray-500 text-sm font-medium">Críticos (&lt;30 min)</span>
                <p class="text-2xl font-bold text-red-600">
                    {{ $emergencies->filter(fn($e) => $e->created_at->diffInMinutes(now()) < 30)->count() }}
                </p>
            </div>
        </div>
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-14 h-14 bg-blue-100 rounded-2xl flex items-center justify-center">
                <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <span class="text-gray-500 text-sm font-medium">Costo Total</span>
                <p class="text-2xl font-bold text-blue-600">Bs. {{ number_format($emergencies->sum('cost'), 2) }}</p>
            </div>
        </div>
    </div>

    @if($emergencies->count() > 0)
        <!-- Alerta de emergencias críticas -->
        @php
            $criticas = $emergencies->filter(fn($e) => $e->created_at->diffInMinutes(now()) < 30);
        @endphp
        @if($criticas->count() > 0)
        <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-xl mb-6">
            <div class="flex items-start gap-3">
                <svg class="w-6 h-6 text-red-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <div>
                    <h3 class="text-red-800 font-bold">¡Atención Inmediata Requerida!</h3>
                    <p class="text-red-700 text-sm">Hay {{ $criticas->count() }} emergencia(s) con menos de 30 minutos de espera.</p>
                </div>
            </div>
        </div>
        @endif

        <!-- Lista de Emergencias -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100">
                <h2 class="text-lg font-bold text-gray-800">Lista de Pacientes</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                        <tr>
                            <th class="px-6 py-4 text-left font-medium">Estado / Código</th>
                            <th class="px-6 py-4 text-left font-medium">Paciente</th>
                            <th class="px-6 py-4 text-left font-medium">Síntomas</th>
                            <th class="px-6 py-4 text-left font-medium">Tiempo Espera</th>
                            <th class="px-6 py-4 text-left font-medium">Costo Est.</th>
                            <th class="px-6 py-4 text-center font-medium">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($emergencies as $emergency)
                        @php
                            $esCritico = $emergency->created_at->diffInMinutes(now()) < 30;
                            $minutos = $emergency->created_at->diffInMinutes(now());
                        @endphp
                        <tr class="hover:bg-gray-50/50 transition {{ $esCritico ? 'bg-red-50/50' : '' }}">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    @if($esCritico)
                                        <span class="w-2.5 h-2.5 bg-red-500 rounded-full animate-pulse"></span>
                                    @else
                                        <span class="w-2.5 h-2.5 bg-yellow-400 rounded-full"></span>
                                    @endif
                                    <div>
                                        <p class="font-semibold text-gray-800">{{ $emergency->code }}</p>
                                        @if($esCritico)
                                            <span class="text-xs text-red-600 font-medium">CRÍTICO</span>
                                        @else
                                            <span class="text-xs text-yellow-600 font-medium">PENDIENTE</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-800">
                                            {{ $emergency->is_temp_id ? 'Paciente Temporal' : ($emergency->paciente?->nombre ?? 'Desconocido') }}
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            {{ $emergency->is_temp_id ? 'ID: '.$emergency->temp_id : 'CI: '.$emergency->patient_id }}
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-gray-600 max-w-xs truncate" title="{{ $emergency->symptoms }}">
                                    {{ $emergency->symptoms ?: 'Sin síntomas registrados' }}
                                </p>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 {{ $esCritico ? 'text-red-500' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span class="font-medium {{ $esCritico ? 'text-red-600' : 'text-gray-700' }}">
                                        @if($minutos < 60)
                                            {{ $minutos }} min
                                        @else
                                            {{ floor($minutos/60) }}h {{ $minutos%60 }}m
                                        @endif
                                    </span>
                                </div>
                                <p class="text-xs text-gray-400 mt-0.5">{{ $emergency->created_at->format('d/m/Y H:i') }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-semibold text-gray-800">Bs. {{ number_format($emergency->cost ?? 0, 2) }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex justify-center gap-2">
                                    <button onclick="showDetails({{ $emergency->id }})" 
                                            class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition" title="Ver detalles">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </button>
                                    <form action="{{ route('emergency-staff.assign-to-me', $emergency) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" 
                                                class="p-2 bg-green-50 text-green-600 rounded-lg hover:bg-green-100 transition" 
                                                title="Asignarme"
                                                onclick="return confirm('¿Deseas asignarte esta emergencia?')">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <!-- Estado Vacío -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-800 mb-2">¡Todo en orden!</h3>
            <p class="text-gray-500 mb-6 max-w-md mx-auto">No hay emergencias pendientes de asignar en este momento. Los nuevos pacientes aparecerán aquí automáticamente.</p>
            <a href="{{ route('emergency-staff.dashboard') }}" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-medium rounded-xl hover:bg-blue-700 transition-all shadow-sm">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                </svg>
                Ir al Panel Principal
            </a>
        </div>
    @endif
</div>

<!-- Modal de detalles -->
<div id="modalDetalles" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full max-h-[90vh] overflow-hidden">
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white p-6">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-xl font-bold">Detalles del Paciente</h3>
                    <p class="text-blue-100 text-sm mt-1" id="modalCodigo"></p>
                </div>
                <button onclick="cerrarModal()" class="bg-white/20 hover:bg-white/30 p-2 rounded-full transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
        <div class="p-6 overflow-y-auto max-h-[60vh]" id="modalContent">
            <!-- Contenido dinámico -->
        </div>
        <div class="p-6 border-t border-gray-100 bg-gray-50 flex gap-3">
            <button onclick="cerrarModal()" class="flex-1 px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-xl hover:bg-gray-50 transition-colors font-medium">
                Cerrar
            </button>
            <button onclick="asignarDesdeModal()" class="flex-1 px-4 py-2 bg-green-600 text-white rounded-xl hover:bg-green-700 transition-colors font-medium flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
                Asignarme
            </button>
        </div>
    </div>
</div>

@php
$emergenciasData = $emergencies->map(function($e) {
    return [
        'id' => $e->id,
        'code' => $e->code,
        'status' => $e->status,
        'created_at' => $e->created_at->format('d/m/Y H:i'),
        'minutos' => $e->created_at->diffInMinutes(now()),
        'symptoms' => $e->symptoms,
        'cost' => $e->cost,
        'is_temp_id' => $e->is_temp_id,
        'temp_id' => $e->temp_id,
        'patient_id' => $e->patient_id,
        'paciente_nombre' => $e->is_temp_id ? 'Paciente Temporal' : ($e->paciente?->nombre ?? 'Desconocido'),
        'paciente_ci' => $e->is_temp_id ? $e->temp_id : $e->patient_id,
        'tipo_ingreso' => $e->tipo_ingreso_label
    ];
});
@endphp

<script>
let currentEmergencyId = null;
const emergencias = @json($emergenciasData);

function showDetails(emergencyId) {
    currentEmergencyId = emergencyId;
    const emergency = emergencias.find(e => e.id === emergencyId);
    if (!emergency) return;
    
    const esCritico = emergency.minutos < 30;
    const tiempoTexto = emergency.minutos < 60 
        ? `${emergency.minutos} minutos` 
        : `${Math.floor(emergency.minutos/60)}h ${emergency.minutos%60}m`;
    
    document.getElementById('modalCodigo').textContent = emergency.code;
    document.getElementById('modalContent').innerHTML = `
        <div class="space-y-4">
            <div class="flex items-center gap-3 p-4 ${esCritico ? 'bg-red-50' : 'bg-yellow-50'} rounded-xl">
                <div class="w-12 h-12 ${esCritico ? 'bg-red-100' : 'bg-yellow-100'} rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 ${esCritico ? 'text-red-600' : 'text-yellow-600'}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Tiempo de espera</p>
                    <p class="font-bold ${esCritico ? 'text-red-600' : 'text-gray-800'}">${tiempoTexto}</p>
                    ${esCritico ? '<p class="text-xs text-red-500 font-medium">¡Requiere atención inmediata!</p>' : ''}
                </div>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div class="p-4 bg-gray-50 rounded-xl">
                    <p class="text-xs text-gray-500 mb-1">Tipo de Ingreso</p>
                    <p class="font-medium text-gray-800">${emergency.tipo_ingreso}</p>
                </div>
                <div class="p-4 bg-gray-50 rounded-xl">
                    <p class="text-xs text-gray-500 mb-1">Costo Estimado</p>
                    <p class="font-medium text-gray-800">Bs. ${parseFloat(emergency.cost || 0).toFixed(2)}</p>
                </div>
            </div>
            
            <div class="p-4 bg-gray-50 rounded-xl">
                <p class="text-xs text-gray-500 mb-2">Síntomas</p>
                <p class="text-gray-800">${emergency.symptoms || 'Sin síntomas registrados'}</p>
            </div>
            
            <div class="flex items-center gap-3 p-4 bg-blue-50 rounded-xl">
                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <div>
                    <p class="font-medium text-gray-800">${emergency.paciente_nombre}</p>
                    <p class="text-sm text-gray-500">CI: ${emergency.paciente_ci}</p>
                </div>
            </div>
        </div>
    `;
    
    document.getElementById('modalDetalles').classList.remove('hidden');
}

function cerrarModal() {
    document.getElementById('modalDetalles').classList.add('hidden');
    currentEmergencyId = null;
}

function asignarDesdeModal() {
    if (!currentEmergencyId) return;
    window.location.href = `/emergency-staff/${currentEmergencyId}/assign-to-me`;
}

// Cerrar modal al hacer click fuera
document.getElementById('modalDetalles').addEventListener('click', function(e) {
    if (e.target === this) cerrarModal();
});

// Auto-refresh cada 30 segundos
setTimeout(function() {
    location.reload();
}, 30000);
</script>
@endsection
