@extends('layouts.app')

@section('content')
    <div class="mb-6 sm:mb-8">
        <a href="{{ route('menus.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-800 flex items-center gap-1 mb-2">
            &larr; Volver a menús
        </a>
        <h2 class="text-2xl font-bold text-slate-800">Crear Nuevo Menú</h2>
    </div>

    <div class="bg-white border shadow-sm border-slate-200 rounded-xl overflow-hidden max-w-4xl">
        <form action="{{ route('menus.store') }}" method="POST" class="p-6 sm:p-8">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nombre -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Nombre del Menú <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 border-slate-300" placeholder="Ej: Pacientes">
                    @error('name') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Menú Padre -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Pertenece a (Menú Padre)</label>
                    <select name="parent_id" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 border-slate-300">
                        <option value="">-- Es un menú principal --</option>
                        @foreach($parents as $parent)
                            <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>{{ $parent->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Ruta -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Nombre de la Ruta</label>
                    <input type="text" name="route" value="{{ old('route') }}" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 border-slate-300 font-mono text-sm" placeholder="Ej: patients.index">
                </div>

                <!-- Patrón Activo -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Patrón de Ruta (Para mantener abierto)</label>
                    <input type="text" name="active_pattern" value="{{ old('active_pattern') }}" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 border-slate-300 font-mono text-sm" placeholder="Ej: patients*,consulta*">
                </div>

                <!-- Orden -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Orden (Posición) <span class="text-red-500">*</span></label>
                    <input type="number" name="order" value="{{ old('order', 0) }}" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 border-slate-300">
                </div>

                <!-- Color -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Color del Icono</label>
                    <select name="color" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 border-slate-300">
                        <option value="blue">Azul</option>
                        <option value="emerald">Esmeralda (Verde)</option>
                        <option value="red">Rojo</option>
                        <option value="purple">Morado</option>
                        <option value="yellow">Amarillo</option>
                        <option value="cyan">Cyan</option>
                        <option value="orange">Naranja</option>
                        <option value="slate">Gris</option>
                    </select>
                </div>

                <!-- Roles -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Roles permitidos (Separados por coma)</label>
                    <input type="text" name="roles" value="{{ old('roles') }}" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 border-slate-300" placeholder="Ej: admin,doctor,caja (Dejar vacío para todos)">
                </div>

                <!-- Icono SVG -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Código del Icono (Solo el atributo 'd' del SVG)</label>
                    <textarea name="icon_path" rows="3" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 border-slate-300 font-mono text-sm" placeholder="Ej: M3 12l2-2m0 0l7-7 77..."></textarea>
                </div>
            </div>

            <div class="mt-8 flex justify-end gap-3 pt-6 border-t border-slate-100">
                <a href="{{ route('menus.index') }}" class="px-5 py-2.5 text-sm font-semibold text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors">Cancelar</a>
                <button type="submit" class="px-5 py-2.5 text-sm font-semibold text-white bg-blue-600 rounded-lg hover:bg-blue-700 shadow-sm transition-colors">Guardar Menú</button>
            </div>
        </form>
    </div>
@endsection