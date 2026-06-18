@extends('layouts.app')

@section('content')
<div class="w-full p-6 bg-gray-50/50 min-h-screen">

    <!-- Page Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 capitalize">Editar Tipo: {{ $tipo->nombre }}</h1>
            <p class="text-sm text-gray-500">Configurar el precio y la duración por defecto</p>
        </div>
        <a href="{{ route('tipos-cirugia.index') }}" class="flex items-center px-4 py-2 border border-gray-200 rounded-lg text-gray-600 bg-white hover:bg-gray-50 font-medium transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Volver
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 max-w-2xl">
        <form id="tipoCirugiaForm" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <!-- Nombre (solo lectura) -->
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Cirugía</label>
                <p class="text-lg font-bold text-gray-900 capitalize">{{ $tipo->nombre }}</p>
                <p class="text-xs text-gray-500 mt-1">El nombre del tipo no se puede cambiar (está vinculado a las cirugías existentes).</p>
            </div>

            <!-- Duración por defecto -->
            <div>
                <label for="duracion_minutos" class="block text-sm font-medium text-gray-700 mb-2">Duración por defecto (minutos) *</label>
                <input type="number" name="duracion_minutos" id="duracion_minutos" min="1" step="1"
                       value="{{ $tipo->duracion_minutos }}" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Precio por defecto -->
            <div>
                <label for="costo_base" class="block text-sm font-medium text-gray-700 mb-2">Precio por defecto (Bs) *</label>
                <input type="text" inputmode="decimal" data-decimal name="costo_base" id="costo_base"
                       value="{{ number_format($tipo->costo_base, 2, '.', '') }}" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Descripción -->
            <div>
                <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-2">Descripción</label>
                <input type="text" name="descripcion" id="descripcion" maxlength="255"
                       value="{{ $tipo->descripcion }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Activo -->
            <div class="flex items-center gap-3">
                <input type="checkbox" name="activo" id="activo" value="1" {{ $tipo->activo ? 'checked' : '' }}
                       class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                <label for="activo" class="text-sm font-medium text-gray-700">Activo (disponible al programar cirugías)</label>
            </div>

            <!-- Aviso -->
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                <p class="text-sm text-blue-800">
                    Este es el valor por defecto que se precarga al programar una cirugía;
                    cambiarlo <strong>no modifica las cirugías ya programadas o cobradas</strong>.
                </p>
            </div>

            <!-- Botones -->
            <div class="flex justify-end gap-4 pt-6 border-t border-gray-200">
                <a href="{{ route('tipos-cirugia.index') }}" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition-colors">
                    Cancelar
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium transition-colors">
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Saneador de input monetario (coma->punto, máx 2 decimales) — patrón establecido
    function inicializarInputDecimal(input) {
        input.addEventListener('keypress', (e) => {
            if (!/[0-9.,]/.test(e.key)) e.preventDefault();
        });
        input.addEventListener('input', function () {
            const pos = this.selectionStart;
            let val = this.value.replace(',', '.').replace(/[^0-9.]/g, '');
            const parts = val.split('.');
            if (parts.length > 2) val = parts[0] + '.' + parts.slice(1).join('');
            const dot = val.indexOf('.');
            if (dot !== -1) val = val.slice(0, dot + 1) + val.slice(dot + 1, dot + 3);
            if (this.value !== val) { this.value = val; this.setSelectionRange(pos, pos); }
        });
        input.addEventListener('blur', function () {
            const num = parseFloat(this.value);
            this.value = isNaN(num) ? '' : num.toFixed(2);
        });
    }
    document.querySelectorAll('input[data-decimal]').forEach(inicializarInputDecimal);

    const form = document.getElementById('tipoCirugiaForm');
    form.addEventListener('submit', function (e) {
        e.preventDefault();

        const payload = {
            _method: 'PUT',
            duracion_minutos: document.getElementById('duracion_minutos').value,
            costo_base: document.getElementById('costo_base').value,
            descripcion: document.getElementById('descripcion').value,
            activo: document.getElementById('activo').checked,
        };

        if (!payload.duracion_minutos || !payload.costo_base) {
            alert('Por favor completa la duración y el precio por defecto');
            return;
        }

        fetch('{{ route("tipos-cirugia.update", $tipo->id) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(payload)
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(errorData => {
                    throw new Error(errorData.message || `Error ${response.status}`);
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                alert(data.message || 'Tipo de cirugía actualizado exitosamente');
                window.location.href = '{{ route("tipos-cirugia.index") }}';
            } else {
                alert(data.message || 'Error al actualizar el tipo de cirugía');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error: ' + error.message);
        });
    });
});
</script>
@endsection
