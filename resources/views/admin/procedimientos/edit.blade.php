@extends('layouts.app')

@section('content')
    <div class="p-6 bg-gray-50 min-h-screen">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Editar Procedimiento</h1>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 max-w-lg">
            <form action="{{ route('admin.procedimientos.update', $procedimiento) }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                    <input type="text" name="nombre" value="{{ old('nombre', $procedimiento->nombre) }}" required
                        class="w-full border rounded-lg px-3 py-2 text-sm @error('nombre') border-red-500 @enderror">
                    @error('nombre') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Área</label>
                    <select name="area" required class="w-full border rounded-lg px-3 py-2 text-sm @error('area') border-red-500 @enderror">
                        @foreach(['emergencia','uti','internacion','cirugia','hospitalizacion','neonato'] as $area)
                            <option value="{{ $area }}" {{ old('area', $procedimiento->area) === $area ? 'selected' : '' }}>{{ ucfirst($area) }}</option>
                        @endforeach
                    </select>
                    @error('area') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Precio (Bs.)</label>
                    <input type="number" name="precio" value="{{ old('precio', $procedimiento->precio) }}" step="0.01" min="0" required
                        class="w-full border rounded-lg px-3 py-2 text-sm @error('precio') border-red-500 @enderror">
                    @error('precio') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Descripción (opcional)</label>
                    <textarea name="descripcion" rows="3"
                        class="w-full border rounded-lg px-3 py-2 text-sm">{{ old('descripcion', $procedimiento->descripcion) }}</textarea>
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" name="activo" value="1" id="activo" {{ old('activo', $procedimiento->activo) ? 'checked' : '' }}>
                    <label for="activo" class="text-sm text-gray-700">Activo</label>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm">Actualizar</button>
                    <a href="{{ route('admin.procedimientos.index') }}" class="px-4 py-2 border rounded-lg text-sm text-gray-700">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
@endsection
