@extends('layouts.app')
@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-3xl font-bold mb-6">Agregar Medicamento - Emergencia</h1>
    <form action="{{ route('emergency-staff.medicamentos.store') }}" method="POST" class="bg-white p-6 rounded shadow max-w-2xl">
        @csrf
        <div class="mb-4">
            <label class="block font-bold mb-2">Medicamento</label>
            <select name="catalogo_id" id="catalogo_id" class="w-full px-3 py-2 border rounded">
                <option value="">Crear Nuevo</option>
                @foreach ($catalogos as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div id="new" style="display:block">
            <input type="text" name="nombre" placeholder="Nombre" class="w-full px-3 py-2 border rounded mb-4">
            <select name="tipo" class="w-full px-3 py-2 border rounded mb-4">
                <option>medicamento</option>
                <option>insumo</option>
            </select>
            <select name="unidad_medida" class="w-full px-3 py-2 border rounded mb-4">
                <option>unidades</option><option>ml</option><option>mg</option>
            </select>
        </div>
        <input type="text" name="codigo_lote" placeholder="Código Lote" class="w-full px-3 py-2 border rounded mb-4" required>
        <input type="number" name="precio_compra" step="0.01" placeholder="Precio Compra" class="w-full px-3 py-2 border rounded mb-4" required>
        <input type="number" name="porcentaje_ganancia" step="0.01" placeholder="% Ganancia" class="w-full px-3 py-2 border rounded mb-4" required>
        <input type="number" name="cantidad_inicial" placeholder="Cantidad" class="w-full px-3 py-2 border rounded mb-4" required>
        <input type="number" name="stock_minimo" placeholder="Stock Mínimo" class="w-full px-3 py-2 border rounded mb-4" required>
        <input type="date" name="fecha_vencimiento" class="w-full px-3 py-2 border rounded mb-4">
        <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded">Guardar</button>
        <a href="{{ route('emergency-staff.medicamentos.index') }}" class="bg-gray-500 text-white px-6 py-2 rounded ml-2">Cancelar</a>
    </form>
</div>
<script>
document.getElementById('catalogo_id').addEventListener('change', function() {
    document.getElementById('new').style.display = this.value ? 'none' : 'block';
});
</script>
@endsection
