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
