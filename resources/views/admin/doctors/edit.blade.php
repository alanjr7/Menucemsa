@extends('layouts.app')

@section('content')
    <div class="p-6 bg-gray-50 min-h-screen">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Editar Doctor</h1>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 max-w-3xl">
            <form method="POST" action="{{ route('admin.doctors.update', $doctor) }}">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre *</label>
                        <input type="text" name="name" value="{{ old('name', $doctor->name) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2" required>
                        @error('name') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                        <input type="email" name="email" value="{{ old('email', $doctor->email) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2" required>
                        @error('email') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">CI</label>
                        <input type="text" name="ci" value="{{ old('ci', $doctor->medico->ci ?? '') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2" readonly>
                        <p class="text-xs text-gray-500 mt-1">El CI no se puede modificar</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                        <input type="text" name="telefono" value="{{ old('telefono', $doctor->medico->telefono ?? '') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                        @error('telefono') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Especialidad *</label>
                        <select name="codigo_especialidad" class="w-full border border-gray-300 rounded-lg px-3 py-2" required>
                            <option value="">Seleccione...</option>
                            @foreach($especialidades as $especialidad)
                                <option value="{{ $especialidad->codigo }}" {{ old('codigo_especialidad', $doctor->medico->codigo_especialidad ?? '') == $especialidad->codigo ? 'selected' : '' }}>
                                    {{ $especialidad->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('codigo_especialidad') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Estado *</label>
                        <select name="estado" class="w-full border border-gray-300 rounded-lg px-3 py-2" required>
                            <option value="Activo" {{ old('estado', $doctor->medico->estado ?? '') == 'Activo' ? 'selected' : '' }}>Activo</option>
                            <option value="Inactivo" {{ old('estado', $doctor->medico->estado ?? '') == 'Inactivo' ? 'selected' : '' }}>Inactivo</option>
                        </select>
                        @error('estado') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="mt-6 flex gap-3 justify-end">
                    <a href="{{ route('admin.doctors.index') }}" class="px-4 py-2 border rounded-lg">Cancelar</a>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
@endsection
