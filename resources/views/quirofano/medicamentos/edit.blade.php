@extends('layouts.app')
@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-3xl font-bold mb-6">Editar - AREA CIRUGIA</h1>
    <form action="{{ route('quirofano.medicamentos.update', $medicamento) }}" method="POST" class="bg-white p-6 rounded shadow max-w-2xl">
        @csrf @method('PUT')
        <input type="text" name="nombre" value="{{ $medicamento->nombre }}" class="w-full px-3 py-2 border rounded mb-4" required>
        <select name="tipo" class="w-full px-3 py-2 border rounded mb-4" required>
            <option value="medicamento" {{ $medicamento->tipo === 'medicamento' ? 'selected' : '' }}>Medicamento</option>
            <option value="insumo" {{ $medicamento->tipo === 'insumo' ? 'selected' : '' }}>Insumo</option>
        </select>
        <textarea name="descripcion" class="w-full px-3 py-2 border rounded mb-4">{{ $medicamento->descripcion }}</textarea>
        <input type="text" name="unidad_medida" value="{{ $medicamento->unidad_medida }}" class="w-full px-3 py-2 border rounded mb-4" required>
        <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded">Guardar</button>
        <a href="{{ route('quirofano.medicamentos.index') }}" class="bg-gray-500 text-white px-6 py-2 rounded ml-2">Cancelar</a>
    </form>
</div>
@endsection
