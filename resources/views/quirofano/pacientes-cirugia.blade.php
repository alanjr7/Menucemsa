@extends('layouts.app')

@section('content')
<div class="p-6 bg-gray-50/50 min-h-screen">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Pacientes en Cirugía</h1>
            <p class="text-sm text-gray-500">Gestión de pacientes en quirófano - Derivación a internación</p>
        </div>
        <div class="flex items-center gap-4">
            <!-- Alerta de camas disponibles -->
            <div class="flex items-center px-4 py-2 bg-white border border-gray-200 rounded-xl shadow-sm">
                <div class="w-3 h-3 rounded-full {{ $camasDisponibles > 0 ? 'bg-green-500' : 'bg-red-500' }} mr-2"></div>
                <span class="text-sm text-gray-600">
                    {{ $camasDisponibles }} {{ $camasDisponibles === 1 ? 'cama disponible' : 'camas disponibles' }}
                </span>
            </div>
            <a href="{{ route('quirofano.index') }}" class="flex items-center px-4 py-2 bg-teal-600 text-white font-medium rounded-xl hover:bg-teal-700 transition-all shadow-sm">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Calendario de Quirófanos
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex flex-col items-center justify-center h-28 hover:shadow-md transition">
            <span class="text-gray-500 text-sm font-medium mb-1">Pacientes en Cirugía</span>
            <span class="text-3xl font-bold text-cyan-600" id="stat-total">{{ $pacientesEnCirugia->count() }}</span>
        </div>
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex flex-col items-center justify-center h-28 hover:shadow-md transition">
            <span class="text-gray-500 text-sm font-medium mb-1">Camas Disponibles</span>
            <span class="text-3xl font-bold {{ $camasDisponibles > 0 ? 'text-green-600' : 'text-red-600' }}">{{ $camasDisponibles }}</span>
        </div>
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex flex-col items-center justify-center h-28 hover:shadow-md transition">
            <span class="text-gray-500 text-sm font-medium mb-1">Estado del Sistema</span>
            <span class="text-lg font-bold {{ $camasDisponibles > 0 ? 'text-green-600' : 'text-amber-600' }}">
                {{ $camasDisponibles > 0 ? 'Operativo' : 'Sin camas' }}
            </span>
        </div>
    </div>

    <!-- Lista de Pacientes -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <div class="flex justify-between items-center">
                <h2 class="text-lg font-bold text-gray-800">Pacientes en Quirófano</h2>
                <span class="px-3 py-1 bg-cyan-100 text-cyan-700 text-sm font-semibold rounded-full" id="contador-pacientes">
                    {{ $pacientesEnCirugia->count() }} paciente(s)
                </span>
            </div>
        </div>

        @if($pacientesEnCirugia->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Código</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Paciente</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">N° Cirugía</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hora Ingreso</th>
                        <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                        <th class="px-6 py-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200" id="tbody-pacientes-cirugia">
                    @foreach($pacientesEnCirugia as $paciente)
                    <tr class="hover:bg-gray-50" data-emergency-id="{{ $paciente->id }}">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="font-mono text-sm font-medium text-cyan-600">{{ $paciente->code }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="h-10 w-10 rounded-full bg-cyan-100 flex items-center justify-center mr-3">
                                    <svg class="w-5 h-5 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $paciente->is_temp_id ? 'Paciente Temporal' : ($paciente->paciente?->nombre ?? 'Desconocido') }}
                                    </div>
                                    @if(!$paciente->is_temp_id && $paciente->paciente)
                                    <div class="text-xs text-gray-500">CI: {{ $paciente->patient_id }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="font-mono text-sm text-gray-600">{{ $paciente->nro_cirugia ?? 'N/A' }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full
                                @if($paciente->status === 'cirugia') bg-cyan-100 text-cyan-800
                                @elseif($paciente->status === 'en_evaluacion') bg-yellow-100 text-yellow-800
                                @elseif($paciente->status === 'estabilizado') bg-green-100 text-green-800
                                @else bg-gray-100 text-gray-800 @endif">
                                @if($paciente->status === 'cirugia') En Cirugía
                                @elseif($paciente->status === 'en_evaluacion') En Evaluación
                                @elseif($paciente->status === 'estabilizado') Estabilizado
                                @else {{ $paciente->status }} @endif
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $paciente->admission_date?->format('H:i') ?? $paciente->created_at->format('H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm text-gray-600">{{ $paciente->tipo_ingreso_label ?? 'Emergencia' }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            <div class="flex flex-col gap-2 items-center">
                                <a href="{{ route('emergency-staff.show', $paciente->id) }}" class="text-cyan-600 hover:text-cyan-900 text-sm font-medium">
                                    Ver detalle
                                </a>
                                @if($camasDisponibles > 0)
                                <button onclick="derivarAInternacion({{ $paciente->id }})" class="inline-flex items-center justify-center px-3 py-1 bg-amber-600 text-white text-xs font-medium rounded hover:bg-amber-700 transition-colors">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                    Derivar a Internación
                                </button>
                                @else
                                <span class="text-xs text-red-500 font-medium">Sin camas disponibles</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="p-12 text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-1">No hay pacientes en cirugía</h3>
            <p class="text-gray-500">Actualmente no hay pacientes de emergencia en quirófano.</p>
        </div>
        @endif
    </div>
</div>

<script src="{{ asset('js/auto-refresh.js') }}"></script>
<script>
    let autoRefresh = null;
    let camasDisponibles = {{ $camasDisponibles }};

    // Iniciar auto-refresh al cargar
    document.addEventListener('DOMContentLoaded', function() {
        iniciarAutoRefresh();
    });

    function iniciarAutoRefresh() {
        autoRefresh = new AutoRefresh({
            interval: 3000,
            endpoint: '{{ route('quirofano.api.pacientes-cirugia') }}',
            onData: (data) => {
                if (data.success) {
                    actualizarTabla(data.pacientes);
                    actualizarStats(data.stats);
                }
            },
            onError: (err) => {
                console.warn('Error al actualizar pacientes:', err);
            }
        });
        autoRefresh.start();
    }

    function actualizarStats(stats) {
        document.getElementById('stat-total').textContent = stats.total;
        document.getElementById('contador-pacientes').textContent = stats.total + ' paciente(s)';
        camasDisponibles = stats.camas_disponibles;
    }

    function actualizarTabla(pacientes) {
        const tbody = document.getElementById('tbody-pacientes-cirugia');

        if (pacientes.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="7" class="px-6 py-8 text-center text-gray-400">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-1">No hay pacientes en cirugía</h3>
                        <p class="text-gray-500">Actualmente no hay pacientes de emergencia en quirófano.</p>
                    </td>
                </tr>
            `;
            return;
        }

        tbody.innerHTML = pacientes.map(p => {
            const estadoClass = {
                'cirugia': 'bg-cyan-100 text-cyan-800',
                'en_evaluacion': 'bg-yellow-100 text-yellow-800',
                'estabilizado': 'bg-green-100 text-green-800'
            }[p.status] || 'bg-gray-100 text-gray-800';

            const botonDerivar = camasDisponibles > 0
                ? `<button onclick="derivarAInternacion(${p.id})" class="inline-flex items-center justify-center px-3 py-1 bg-amber-600 text-white text-xs font-medium rounded hover:bg-amber-700 transition-colors">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        Derivar a Internación
                   </button>`
                : `<span class="text-xs text-red-500 font-medium">Sin camas disponibles</span>`;

            return `
                <tr class="hover:bg-gray-50" data-emergency-id="${p.id}">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="font-mono text-sm font-medium text-cyan-600">${p.code}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="h-10 w-10 rounded-full bg-cyan-100 flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <div>
                                <div class="text-sm font-medium text-gray-900">${p.paciente_nombre}</div>
                                ${!p.is_temp_id ? `<div class="text-xs text-gray-500">CI: ${p.paciente_ci}</div>` : ''}
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="font-mono text-sm text-gray-600">${p.nro_cirugia || 'N/A'}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full ${estadoClass}">${p.status_label}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${p.hora_ingreso}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-sm text-gray-600">${p.tipo_ingreso}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        <div class="flex flex-col gap-2 items-center">
                            <a href="/emergency-staff/${p.id}" class="text-cyan-600 hover:text-cyan-900 text-sm font-medium">Ver detalle</a>
                            ${botonDerivar}
                        </div>
                    </td>
                </tr>
            `;
        }).join('');
    }

    async function derivarAInternacion(emergencyId) {
        if (!confirm('¿Está seguro de derivar este paciente a Internación?')) {
            return;
        }

        try {
            const response = await fetch(`/quirofano/api/emergencia/${emergencyId}/derivar-internacion`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            const data = await response.json();

            if (data.success) {
                alert('Paciente derivado a Internación correctamente. Nro: ' + data.hospitalizacion.nro_hospitalizacion);
                // Recargar datos sin refrescar página
                if (autoRefresh) {
                    autoRefresh.fetchData();
                }
            } else {
                alert('Error: ' + data.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error al derivar a Internación');
        }
    }
</script>
@endsection
