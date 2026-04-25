@extends('layouts.app')

@section('content')
<div class="p-6 bg-gray-50/50 min-h-screen">
    <div class="max-w-4xl mx-auto">
        {{-- Header --}}
        <div class="flex justify-between items-start mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Evolución Clínica</h1>
                <p class="text-sm text-gray-500">Internación #{{ $hospitalizacion->id }}</p>
            </div>
            <a href="{{ route('medico.dashboard') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition text-sm">
                ← Volver al Dashboard
            </a>
        </div>

        {{-- Datos del Paciente --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
            <h2 class="font-bold text-gray-800 mb-4">Datos del Paciente</h2>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-gray-500">Nombre</p>
                    <p class="font-medium">{{ $hospitalizacion->paciente?->nombre ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-gray-500">CI</p>
                    <p class="font-medium">{{ $hospitalizacion->paciente?->ci ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Habitación</p>
                    <p class="font-medium">{{ $hospitalizacion->habitacion?->numero ?? 'N/A' }} - Cama {{ $hospitalizacion->cama?->numero ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Ingreso</p>
                    <p class="font-medium">{{ $hospitalizacion->fecha_ingreso?->format('d/m/Y') ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        {{-- Formulario de Evolución --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
            <h2 class="font-bold text-gray-800 mb-4">Actualizar Evolución</h2>
            <form id="formEvolucion" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Diagnóstico</label>
                    <textarea name="diagnostico" rows="3" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm" placeholder="Diagnóstico actual...">{{ $hospitalizacion->diagnostico }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tratamiento</label>
                    <textarea name="tratamiento" rows="3" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm" placeholder="Tratamiento indicado...">{{ $hospitalizacion->tratamiento }}</textarea>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="guardarEvolucion()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium">
                        Guardar Cambios
                    </button>
                </div>
            </form>
        </div>

        {{-- Equipos Médicos y Procedimientos --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-bold text-gray-800">Equipos Médicos y Procedimientos</h2>
                @if(auth()->user()->isMedico())
                <button type="button" onclick="agregarEquipoMedico()" class="flex items-center px-3 py-2 bg-cyan-600 text-white rounded-lg hover:bg-cyan-700 transition text-sm font-medium">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Agregar Equipo
                </button>
                @endif
            </div>

            @php
                $equiposMedicos = $hospitalizacion->equipos_medicos ?? [];
                // Filtrar solo los arrays de evolución y sumar equipos
                $equiposList = [];
                foreach ($equiposMedicos as $evolucion) {
                    if (isset($evolucion['equipos_medicos']) && is_array($evolucion['equipos_medicos'])) {
                        foreach ($evolucion['equipos_medicos'] as $equipo) {
                            $equiposList[] = $equipo;
                        }
                    }
                }
            @endphp

            @if(count($equiposList) > 0)
                <div id="listaEquiposMedicos" class="space-y-2">
                    @foreach($equiposList as $equipo)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div>
                            <p class="font-medium text-gray-800">{{ $equipo['nombre'] }}</p>
                            <p class="text-sm text-gray-500">Bs. {{ number_format($equipo['precio_unitario'], 2) }} x {{ $equipo['cantidad'] }}</p>
                        </div>
                        <span class="font-bold text-cyan-600">Bs. {{ number_format($equipo['subtotal'], 2) }}</span>
                    </div>
                    @endforeach
                </div>
                @php
                    $totalEquipos = array_sum(array_column($equiposList, 'subtotal'));
                @endphp
                <div class="mt-4 pt-4 border-t border-gray-200 flex justify-between items-center">
                    <span class="text-gray-600">Total de equipos:</span>
                    <span class="text-xl font-bold text-cyan-600">Bs. {{ number_format($totalEquipos, 2) }}</span>
                </div>
            @else
                <p class="text-gray-400 text-sm italic">No hay equipos médicos registrados</p>
            @endif

            @if(auth()->user()->isMedico())
            <div id="formNuevoEquipo" class="hidden mt-4 pt-4 border-t border-gray-200">
                <h4 class="text-sm font-medium text-gray-700 mb-3">Agregar Nuevo Equipo</h4>
                <div class="space-y-3">
                    <input type="text" id="nombreEquipo" placeholder="Nombre del equipo/procedimiento" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm">
                    <div class="flex gap-3">
                        <input type="number" id="precioEquipo" placeholder="Precio (Bs.)" min="0" step="0.01" class="w-1/2 border border-gray-200 rounded-lg px-3 py-2 text-sm">
                        <input type="number" id="cantidadEquipo" placeholder="Cantidad" min="1" value="1" class="w-1/2 border border-gray-200 rounded-lg px-3 py-2 text-sm">
                    </div>
                    <div class="flex gap-2">
                        <button onclick="guardarEquipoMedico()" class="flex-1 bg-cyan-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-cyan-700">Guardar</button>
                        <button onclick="cancelarEquipo()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm hover:bg-gray-50">Cancelar</button>
                    </div>
                </div>
            </div>
            @endif
        </div>

        {{-- Medicamentos Administrados --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="font-bold text-gray-800 mb-4">Medicamentos Administrados</h2>
            @if($hospitalizacion->medicamentosAdministrados && $hospitalizacion->medicamentosAdministrados->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 text-left">Medicamento</th>
                                <th class="px-3 py-2 text-left">Dosis</th>
                                <th class="px-3 py-2 text-left">Vía</th>
                                <th class="px-3 py-2 text-left">Fecha</th>
                                <th class="px-3 py-2 text-left">Enfermera</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($hospitalizacion->medicamentosAdministrados as $med)
                            <tr>
                                <td class="px-3 py-2">{{ $med->medicamento?->nombre ?? 'N/A' }}</td>
                                <td class="px-3 py-2">{{ $med->dosis }}</td>
                                <td class="px-3 py-2">{{ $med->via }}</td>
                                <td class="px-3 py-2">{{ $med->created_at?->format('d/m/Y H:i') }}</td>
                                <td class="px-3 py-2">{{ $med->enfermera?->nombre ?? 'N/A' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-gray-400 text-sm italic">No hay medicamentos registrados</p>
            @endif
        </div>
    </div>
</div>

<script>
let equiposMedicosTemp = [];

function agregarEquipoMedico() {
    document.getElementById('formNuevoEquipo').classList.remove('hidden');
}

function cancelarEquipo() {
    document.getElementById('formNuevoEquipo').classList.add('hidden');
    document.getElementById('nombreEquipo').value = '';
    document.getElementById('precioEquipo').value = '';
    document.getElementById('cantidadEquipo').value = '1';
}

function guardarEquipoMedico() {
    const nombre = document.getElementById('nombreEquipo').value.trim();
    const precio = parseFloat(document.getElementById('precioEquipo').value);
    const cantidad = parseInt(document.getElementById('cantidadEquipo').value);

    if (!nombre) {
        alert('Ingrese el nombre del equipo');
        return;
    }
    if (isNaN(precio) || precio < 0) {
        alert('Ingrese un precio válido');
        return;
    }
    if (isNaN(cantidad) || cantidad < 1) {
        alert('Ingrese una cantidad válida');
        return;
    }

    const equipo = { nombre, precio, cantidad };
    equiposMedicosTemp.push(equipo);

    // Enviar al servidor junto con la evolución
    guardarEvolucionConEquipos();
}

function guardarEvolucionConEquipos() {
    const form = document.getElementById('formEvolucion');
    const formData = new FormData(form);

    const data = {
        diagnostico: formData.get('diagnostico'),
        tratamiento: formData.get('tratamiento'),
        equipos_medicos: equiposMedicosTemp
    };

    fetch(`/api/internacion/{{ $hospitalizacion->id }}/evolucion`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            alert('Equipo médico guardado correctamente');
            equiposMedicosTemp = [];
            cancelarEquipo();
            location.reload();
        } else {
            alert(data.message || 'Error al guardar');
        }
    })
    .catch(e => {
        console.error(e);
        alert('Error de conexión');
    });
}
</script>

<script>
function guardarEvolucion() {
    const form = document.getElementById('formEvolucion');
    const formData = new FormData(form);

    fetch(`/api/internacion/{{ $hospitalizacion->id }}/receta`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            alert('Evolución guardada correctamente');
        } else {
            alert(data.message || 'Error al guardar');
        }
    })
    .catch(e => {
        alert('Error de conexión');
    });
}
</script>
@endsection
