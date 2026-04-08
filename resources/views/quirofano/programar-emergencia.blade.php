@extends('layouts.app')

@section('title', 'Programar Cirugía de Emergencia')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Programar Cirugía</h1>
            <p class="text-sm text-slate-500 mt-1">Paciente: <span class="font-semibold text-purple-700">{{ $emergencia->is_temp_id ? 'Paciente Temporal' : ($emergencia->paciente?->nombre ?? 'Desconocido') }}</span></p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('quirofano.index') }}" class="inline-flex items-center px-4 py-2 bg-slate-200 text-slate-700 rounded-lg hover:bg-slate-300 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Volver
            </a>
        </div>
    </div>

    <!-- Info de Emergencia -->
    <div class="bg-purple-50 border border-purple-200 rounded-xl p-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-purple-800">Emergencia {{ $emergencia->code }}</p>
                <p class="text-xs text-purple-600">Tipo: {{ $emergencia->tipo_ingreso_label }} | N° Cirugía: {{ $emergencia->nro_cirugia ?? 'Sin asignar' }}</p>
            </div>
        </div>
    </div>

    <!-- Formulario -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-6 py-4 bg-slate-50 border-b border-slate-200">
            <h2 class="font-semibold text-slate-800">Datos de la Cirugía</h2>
        </div>
        <form id="programarForm" class="p-6 space-y-6">
            @csrf
            <input type="hidden" name="emergency_id" value="{{ $emergencia->id }}">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Quirofano -->
                <div>
                    <label for="nro_quirofano" class="block text-sm font-medium text-slate-700 mb-2">
                        Quirófano <span class="text-red-500">*</span>
                    </label>
                    <select name="nro_quirofano" id="nro_quirofano" required
                        class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Seleccionar quirófano</option>
                        @foreach($quirofanos as $quirofano)
                            <option value="{{ $quirofano->id }}" {{ $quirofano->estado !== 'disponible' ? 'disabled' : '' }}>
                                {{ $quirofano->nombre }} - {{ $quirofano->ubicacion }} ({{ $quirofano->estado }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Tipo de Cirugía -->
                <div>
                    <label for="tipo_cirugia" class="block text-sm font-medium text-slate-700 mb-2">
                        Tipo de Cirugía <span class="text-red-500">*</span>
                    </label>
                    <select name="tipo_cirugia" id="tipo_cirugia" required
                        class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Seleccionar tipo</option>
                        <option value="menor">Menor (45-60 min)</option>
                        <option value="ambulatoria">Ambulatoria (45 min)</option>
                        <option value="mediana">Mediana (90 min)</option>
                        <option value="mayor">Mayor (90-120 min)</option>
                    </select>
                </div>

                <!-- Fecha -->
                <div>
                    <label for="fecha" class="block text-sm font-medium text-slate-700 mb-2">
                        Fecha <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="fecha" id="fecha" required min="{{ date('Y-m-d') }}"
                        class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                </div>

                <!-- Hora -->
                <div>
                    <label for="hora_inicio_estimada" class="block text-sm font-medium text-slate-700 mb-2">
                        Hora de Inicio <span class="text-red-500">*</span>
                    </label>
                    <input type="time" name="hora_inicio_estimada" id="hora_inicio_estimada" required
                        class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                </div>

                <!-- Cirujano -->
                <div>
                    <label for="ci_cirujano" class="block text-sm font-medium text-slate-700 mb-2">
                        Cirujano <span class="text-red-500">*</span>
                    </label>
                    <select name="ci_cirujano" id="ci_cirujano" required
                        class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Seleccionar cirujano</option>
                        @foreach($medicos as $medico)
                            <option value="{{ $medico->ci }}">
                                {{ $medico->user->name ?? 'Sin nombre' }} ({{ $medico->especialidad ?? 'Sin especialidad' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Instrumentista -->
                <div>
                    <label for="ci_instrumentista" class="block text-sm font-medium text-slate-700 mb-2">
                        Instrumentista
                    </label>
                    <select name="ci_instrumentista" id="ci_instrumentista"
                        class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Seleccionar instrumentista</option>
                        @foreach($medicos as $medico)
                            <option value="{{ $medico->ci }}">
                                {{ $medico->user->name ?? 'Sin nombre' }} ({{ $medico->especialidad ?? 'Sin especialidad' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Anestesiólogo -->
                <div>
                    <label for="ci_anestesiologo" class="block text-sm font-medium text-slate-700 mb-2">
                        Anestesiólogo
                    </label>
                    <select name="ci_anestesiologo" id="ci_anestesiologo"
                        class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Seleccionar anestesiólogo</option>
                        @foreach($medicos as $medico)
                            <option value="{{ $medico->ci }}">
                                {{ $medico->user->name ?? 'Sin nombre' }} ({{ $medico->especialidad ?? 'Sin especialidad' }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Descripción -->
            <div>
                <label for="descripcion_cirugia" class="block text-sm font-medium text-slate-700 mb-2">
                    Descripción de la Cirugía
                </label>
                <textarea name="descripcion_cirugia" id="descripcion_cirugia" rows="3"
                    class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500"
                    placeholder="Describa el procedimiento a realizar...">Cirugía derivada desde emergencia {{ $emergencia->code }}</textarea>
            </div>

            <!-- Botones -->
            <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t border-slate-200">
                <button type="submit" class="inline-flex items-center justify-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Programar Cirugía
                </button>

                <button type="button" onclick="iniciarAhora()" class="inline-flex items-center justify-center px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors font-medium">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    Iniciar Ahora (Urgente)
                </button>

                <a href="{{ route('quirofano.index') }}" class="inline-flex items-center justify-center px-6 py-3 bg-slate-200 text-slate-700 rounded-lg hover:bg-slate-300 transition-colors font-medium">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('programarForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const data = Object.fromEntries(formData.entries());

    try {
        const response = await fetch('{{ route('quirofano.store-emergencia') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        if (result.success) {
            alert('Cirugía programada exitosamente');
            window.location.href = result.redirect || '{{ route('quirofano.index') }}';
        } else {
            alert('Error: ' + (result.message || 'No se pudo programar la cirugía'));
        }
    } catch (error) {
        alert('Error de conexión: ' + error.message);
    }
});

async function iniciarAhora() {
    if (!confirm('¿Iniciar cirugía de emergencia inmediatamente? Se buscará el primer quirófano disponible.')) {
        return;
    }

    try {
        const response = await fetch('{{ route('quirofano.iniciar-emergencia', $emergencia->id) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        const result = await response.json();

        if (result.success) {
            alert('Cirugía iniciada: ' + result.message);
            window.location.href = result.redirect || '{{ route('quirofano.index') }}';
        } else {
            alert('Error: ' + (result.message || 'No se pudo iniciar la cirugía'));
        }
    } catch (error) {
        alert('Error de conexión: ' + error.message);
    }
}
</script>
@endsection
