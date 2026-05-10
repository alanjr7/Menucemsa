@extends('layouts.app')

@section('content')
<div class="w-full p-6 bg-gray-50/50 min-h-screen">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Detalles de la Cita</h1>
            <p class="text-sm text-gray-500">Editando información de la cita #{{ $cita->id }}</p>
        </div>
        <a href="{{ route('reception') }}" class="px-4 py-2 bg-white border border-gray-200 text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition-all shadow-sm">
            Volver a Recepción
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 max-w-4xl mx-auto">
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 text-green-800 rounded-xl border border-green-200">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 text-red-800 rounded-xl border border-red-200">
                {{ session('error') }}
            </div>
        @endif
        @if($errors->any())
            <div class="mb-6 p-4 bg-red-50 text-red-800 rounded-xl border border-red-200">
                <ul class="list-disc pl-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form id="form-editar-cita" action="{{ route('reception.citas.update', $cita->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <!-- Datos del Paciente (Solo Lectura) -->
            <div class="mb-8">
                <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center border-b pb-2">
                    <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Datos del Paciente
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-gray-50 p-4 rounded-xl">
                    <div>
                        <span class="block text-xs font-medium text-gray-500">Nombre del Paciente</span>
                        <span class="text-gray-900 font-medium">{{ $cita->paciente->nombre }}</span>
                    </div>
                    <div>
                        <span class="block text-xs font-medium text-gray-500">Documento de Identidad (C.I.)</span>
                        <span class="text-gray-900 font-medium">{{ $cita->paciente->ci }}</span>
                    </div>
                </div>
            </div>

            <!-- Datos de la Cita -->
            <div class="mb-8">
                <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center border-b pb-2">
                    <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Información de la Cita
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Especialidad *</label>
                        <select id="codigo_especialidad" name="codigo_especialidad" class="campo-editable w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 disabled:bg-gray-100 disabled:cursor-not-allowed" required disabled>
                            <option value="">Seleccione...</option>
                            @foreach($especialidades as $esp)
                                <option value="{{ $esp->codigo }}" {{ $cita->codigo_especialidad == $esp->codigo ? 'selected' : '' }}>
                                    {{ $esp->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Médico *</label>
                        <select id="ci_medico" name="ci_medico" class="campo-editable w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 disabled:bg-gray-100 disabled:cursor-not-allowed" required disabled>
                            <option value="">Seleccione...</option>
                            @foreach($medicos as $med)
                                <option value="{{ $med->ci }}" {{ $cita->ci_medico == $med->ci ? 'selected' : '' }}>
                                    Dr. {{ $med->user ? $med->user->name : $med->ci }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fecha *</label>
                        <input type="date" id="fecha" name="fecha" value="{{ $cita->fecha->format('Y-m-d') }}" class="campo-editable w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 disabled:bg-gray-100 disabled:cursor-not-allowed" required disabled>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Hora *</label>
                        <input type="time" id="hora" name="hora" value="{{ is_string($cita->hora) ? substr($cita->hora, 0, 5) : $cita->hora->format('H:i') }}" class="campo-editable w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 disabled:bg-gray-100 disabled:cursor-not-allowed" required disabled>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Motivo de Consulta *</label>
                        <input type="text" name="motivo" value="{{ $cita->motivo }}" class="campo-editable w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 disabled:bg-gray-100 disabled:cursor-not-allowed" required disabled>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Estado de la Cita</label>
                        <select name="estado" class="campo-editable w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 disabled:bg-gray-100 disabled:cursor-not-allowed" required disabled>
                            <option value="programado" {{ $cita->estado == 'programado' ? 'selected' : '' }}>Programado</option>
                            <option value="confirmado" {{ $cita->estado == 'confirmado' ? 'selected' : '' }}>Confirmado</option>
                            <option value="en_atencion" {{ $cita->estado == 'en_atencion' ? 'selected' : '' }}>En Atención</option>
                            <option value="atendido" {{ $cita->estado == 'atendido' ? 'selected' : '' }}>Atendido</option>
                            <option value="cancelado" {{ $cita->estado == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                            <option value="no_asistio" {{ $cita->estado == 'no_asistio' ? 'selected' : '' }}>No Asistió</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Observaciones</label>
                        <textarea name="observaciones" rows="3" class="campo-editable w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-white focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100 disabled:bg-gray-100 disabled:cursor-not-allowed" disabled>{{ $cita->observaciones }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Botones -->
            <div class="flex justify-end gap-3 pt-6 border-t border-gray-200">
                <a href="{{ route('reception') }}" class="px-6 py-2.5 border border-gray-200 rounded-xl text-gray-700 font-medium hover:bg-gray-50 transition-colors text-sm">
                    Volver
                </a>
                <button type="button" id="btn-editar" onclick="habilitarEdicion()" class="px-6 py-2.5 bg-indigo-600 text-white rounded-xl font-medium hover:bg-indigo-700 transition-colors flex items-center text-sm shadow-md">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                    </svg>
                    Editar
                </button>
                <button type="submit" id="btn-guardar" class="hidden px-6 py-2.5 bg-blue-600 text-white rounded-xl font-medium hover:bg-blue-700 transition-colors flex items-center text-sm shadow-md">
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const especialidadSelect = document.getElementById('codigo_especialidad');
        const fechaInput = document.getElementById('fecha');
        const horaInput = document.getElementById('hora');
        const medicoSelect = document.getElementById('ci_medico');
        const medicoInicial = '{{ $cita->ci_medico }}';

        async function cargarMedicosDisponibles() {
            const especialidad = especialidadSelect.value;
            const fecha = fechaInput.value;
            const hora = horaInput.value;
            
            if (!especialidad) return;
            
            try {
                const params = new URLSearchParams({ especialidad, fecha, hora });
                const response = await fetch(`/api/medicos-disponibles?${params}`);
                const data = await response.json();
                
                // Guardar selección actual si existe
                const seleccionActual = medicoSelect.value;
                
                medicoSelect.innerHTML = '<option value="">Seleccione...</option>';
                
                if (data.success) {
                    let medicoEncontrado = false;
                    data.medicos.forEach(medico => {
                        const isSelected = (medico.ci === seleccionActual) || (medico.ci === medicoInicial);
                        if (isSelected) medicoEncontrado = true;
                        
                        medicoSelect.innerHTML += `<option value="${medico.ci}" ${isSelected ? 'selected' : ''}>Dr. ${medico.nombre}</option>`;
                    });
                    
                    // Si el médico que estaba seleccionado o el inicial no está en la lista de disponibles
                    // (por ejemplo, porque la fecha/hora es la misma de la cita actual y el endpoint lo filtra),
                    // lo agregamos manualmente a la lista si es el médico original
                    if (!medicoEncontrado && medicoInicial) {
                        // Podríamos hacer un fetch para obtener el nombre de este médico, 
                        // o simplemente dejarlo para que no se pierda la selección actual
                        // Por simplicidad, si no se cambia la fecha/hora/especialidad original,
                        // mantenemos la lista inicial cargada desde el backend de Laravel
                    }
                }
            } catch (error) {
                console.error('Error al cargar médicos:', error);
            }
        }

        especialidadSelect.addEventListener('change', cargarMedicosDisponibles);
        fechaInput.addEventListener('change', cargarMedicosDisponibles);
        horaInput.addEventListener('change', cargarMedicosDisponibles);
    });

    function habilitarEdicion() {
        const campos = document.querySelectorAll('.campo-editable');
        campos.forEach(campo => {
            campo.disabled = false;
        });
        
        document.getElementById('btn-editar').classList.add('hidden');
        document.getElementById('btn-guardar').classList.remove('hidden');
        
        // Poner el foco en el primer campo
        const primerCampo = document.querySelector('.campo-editable');
        if(primerCampo) primerCampo.focus();
    }
</script>
@endsection
