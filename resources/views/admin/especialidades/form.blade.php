@csrf

<div class="space-y-4">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Código</label>
        <input
            type="text"
            name="codigo"
            value="{{ old('codigo', optional($especialidad)->codigo) }}"
            {{ isset($especialidad) ? 'readonly' : '' }}
            class="w-full border border-gray-300 rounded-lg px-3 py-2"
            placeholder="Opcional, se genera automáticamente"
        >
        @error('codigo') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre *</label>
        <input
            type="text"
            name="nombre"
            value="{{ old('nombre', optional($especialidad)->nombre) }}"
            class="w-full border border-gray-300 rounded-lg px-3 py-2"
            required
        >
        @error('nombre') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
        <textarea name="descripcion" class="w-full border border-gray-300 rounded-lg px-3 py-2" rows="3">{{ old('descripcion', optional($especialidad)->descripcion) }}</textarea>
        @error('descripcion') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Estado *</label>
        <select name="estado" class="w-full border border-gray-300 rounded-lg px-3 py-2" required>
            @php($estadoActual = old('estado', optional($especialidad)->estado ?? 'activo'))
            <option value="activo" @selected($estadoActual === 'activo')>Activo</option>
            <option value="inactivo" @selected($estadoActual === 'inactivo')>Inactivo</option>
        </select>
        @error('estado') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
    </div>
</div>
