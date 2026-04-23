@extends('layouts.app')

@section('content')
<div class="w-full p-6 bg-gray-50/50 min-h-screen">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Editar Cita Quirúrgica</h1>
            <p class="text-sm text-gray-500">Modificar datos de la cita #{{ $cita->id }}</p>
        </div>
        <a href="{{ route('quirofano.show', $cita) }}" class="flex items-center px-4 py-2 border border-gray-200 rounded-lg text-gray-600 bg-white hover:bg-gray-50 font-medium transition-colors">
            ← Volver
        </a>
    </div>

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6">
            <ul class="text-red-700 text-sm list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <form method="POST" action="{{ route('quirofano.update', $cita) }}">
            @csrf
            @method('PUT')

            {{-- Paciente --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">CI Paciente *</label>
                <input type="number" name="ci_paciente" value="{{ old('ci_paciente', $cita->ci_paciente) }}"
                       class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm" required>
            </div>

            {{-- Fecha --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Fecha *</label>
                <input type="date" name="fecha" value="{{ old('fecha', $cita->fecha->format('Y-m-d')) }}"
                       class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm" required>
            </div>

            {{-- Hora estimada --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Hora de Inicio Estimada *</label>
                <input type="time" name="hora_inicio_estimada" value="{{ old('hora_inicio_estimada', $cita->hora_inicio_estimada) }}"
                       class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm" required>
            </div>

            {{-- Cirujano --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">CI Cirujano *</label>
                <input type="number" name="ci_cirujano" value="{{ old('ci_cirujano', $cita->ci_cirujano) }}"
                       class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm" required>
            </div>

            {{-- Tipo cirugía --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Tipo de Cirugía *</label>
                <select name="tipo_cirugia" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm" required>
                    @foreach(['menor' => 'Menor', 'mediana' => 'Mediana', 'mayor' => 'Mayor', 'ambulatoria' => 'Ambulatoria'] as $val => $label)
                        <option value="{{ $val }}" {{ old('tipo_cirugia', $cita->tipo_cirugia) === $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Quirófano --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Quirófano *</label>
                <select name="quirofano_id" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm" required>
                    @foreach($quirofanos as $q)
                        <option value="{{ $q->id }}" {{ old('quirofano_id', $cita->quirofano_id) == $q->id ? 'selected' : '' }}>
                            Quirófano {{ $q->tipo }} ({{ ucfirst($q->estado) }})
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Costo base --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Costo Base (Bs.) *</label>
                <input type="number" step="0.01" name="costo_base" value="{{ old('costo_base', $cita->costo_base) }}"
                       class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm" required>
            </div>

            {{-- Observaciones --}}
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Observaciones</label>
                <textarea name="observaciones" rows="3" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm">{{ old('observaciones', $cita->observaciones) }}</textarea>
            </div>

            <div class="flex gap-3 justify-end">
                <a href="{{ route('quirofano.show', $cita) }}" class="px-6 py-3 border border-gray-200 rounded-xl text-gray-700 font-medium hover:bg-gray-50 transition-colors">
                    Cancelar
                </a>
                <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-xl font-medium hover:bg-blue-700 transition-colors">
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
