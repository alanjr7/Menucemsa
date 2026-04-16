@extends('layouts.app')

@section('title', 'Editar Habitación ' . $habitacion->id . ' - Internación')

@section('content')
<div class="min-h-screen bg-gray-50 p-6">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Editar Habitación {{ $habitacion->id }}</h1>
                <p class="text-gray-600 mt-1">Modificar información de la habitación</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('internacion-staff.habitaciones.show', $habitacion) }}" 
                   class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    Ver Detalle
                </a>
                <a href="{{ route('internacion-staff.habitaciones.index') }}" 
                   class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Volver
                </a>
            </div>
        </div>
    </div>

    <!-- Mensajes de Error -->
    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Formulario -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <form action="{{ route('internacion-staff.habitaciones.update', $habitacion) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Código (solo lectura) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Código de Habitación</label>
                    <input type="text" value="{{ $habitacion->id }}" disabled
                           class="w-full px-3 py-2 border border-gray-300 bg-gray-100 text-gray-500 rounded-lg">
                    <p class="text-xs text-gray-500 mt-1">El código no se puede modificar</p>
                </div>

                <!-- Estado -->
                <div>
                    <label for="estado" class="block text-sm font-medium text-gray-700 mb-1">
                        Estado <span class="text-red-500">*</span>
                    </label>
                    <select name="estado" id="estado" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="disponible" {{ old('estado', $habitacion->estado) === 'disponible' ? 'selected' : '' }}>Disponible</option>
                        <option value="ocupada" {{ old('estado', $habitacion->estado) === 'ocupada' ? 'selected' : '' }}>Ocupada</option>
                        <option value="mantenimiento" {{ old('estado', $habitacion->estado) === 'mantenimiento' ? 'selected' : '' }}>Mantenimiento</option>
                    </select>
                </div>

                <!-- Capacidad -->
                <div>
                    <label for="capacidad" class="block text-sm font-medium text-gray-700 mb-1">
                        Capacidad (N° de camas) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="capacidad" id="capacidad" 
                           value="{{ old('capacidad', $habitacion->capacidad) }}" required 
                           min="1" max="10"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <p class="text-xs text-gray-500 mt-1">Nota: Cambiar la capacidad no afecta las camas existentes</p>
                </div>

                <!-- Detalle -->
                <div>
                    <label for="detalle" class="block text-sm font-medium text-gray-700 mb-1">
                        Detalle/Descripción
                    </label>
                    <input type="text" name="detalle" id="detalle" 
                           value="{{ old('detalle', $habitacion->detalle) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                           placeholder="Ej: Habitación individual con baño">
                </div>
            </div>

            <!-- Camas Actuales - Editar Precios -->
            <div class="mb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b border-gray-200">Camas Actuales - Editar Precios</h2>
                <p class="text-sm text-gray-500 mb-4">Puedes modificar el precio por día de cada cama. Las camas ocupadas no pueden cambiar de precio.</p>
                
                <div class="space-y-4">
                    @foreach($habitacion->camas as $cama)
                        <div class="p-4 border rounded-lg {{ $cama->disponibilidad === 'disponible' ? 'border-green-200 bg-green-50' : ($cama->disponibilidad === 'ocupada' ? 'border-red-200 bg-red-50' : 'border-gray-200 bg-gray-50') }}">
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                                <!-- N° Cama (solo lectura) -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">N° Cama</label>
                                    <input type="text" value="Cama {{ $cama->nro }}" disabled
                                           class="w-full px-3 py-2 border border-gray-300 bg-gray-100 rounded-lg">
                                    <input type="hidden" name="camas[{{ $cama->id }}][id]" value="{{ $cama->id }}">
                                </div>
                                
                                <!-- Tipo (solo lectura) -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipo</label>
                                    <input type="text" value="{{ $cama->tipo }}" disabled
                                           class="w-full px-3 py-2 border border-gray-300 bg-gray-100 rounded-lg">
                                </div>
                                
                                <!-- Estado (solo lectura) -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                                    <input type="text" value="{{ ucfirst($cama->disponibilidad) }}" disabled
                                           class="w-full px-3 py-2 border border-gray-300 bg-gray-100 rounded-lg
                                                  {{ $cama->disponibilidad === 'disponible' ? 'text-green-700' : ($cama->disponibilidad === 'ocupada' ? 'text-red-700' : 'text-gray-700') }}">
                                </div>
                                
                                <!-- Precio por Día (editable) -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Precio por Día (Bs) <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" name="camas[{{ $cama->id }}][precio_por_dia]" 
                                           value="{{ old('camas.' . $cama->id . '.precio_por_dia', $cama->precio_por_dia) }}" 
                                           required min="0" step="0.01"
                                           {{ $cama->disponibilidad === 'ocupada' ? 'disabled' : '' }}
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500
                                                  {{ $cama->disponibilidad === 'ocupada' ? 'bg-gray-100 text-gray-500' : '' }}">
                                    @if($cama->disponibilidad === 'ocupada')
                                        <p class="text-xs text-red-500 mt-1">No editable - Cama ocupada</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($habitacion->camas->isEmpty())
                    <p class="text-gray-500 text-center py-4">No hay camas registradas en esta habitación</p>
                @endif
            </div>

            <!-- Botones -->
            <div class="flex items-center justify-end gap-4 pt-6 border-t border-gray-200">
                <a href="{{ route('internacion-staff.habitaciones.index') }}" 
                   class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2 rounded-lg transition-colors">
                    Cancelar
                </a>
                <button type="submit" 
                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg transition-colors">
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
