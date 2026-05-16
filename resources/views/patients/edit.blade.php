@extends('layouts.app')

@section('content')
<div class="w-full p-6 bg-gray-50/50 min-h-screen">

        <!-- Page Header -->
        <div class="flex justify-between items-end mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Editar Paciente</h1>
                <p class="text-sm text-gray-500">Modificar información del paciente</p>
            </div>
            <div>
                <a href="{{ route('admin.pacientes.gestionar') }}" class="inline-flex items-center px-4 py-2 border border-gray-200 rounded-xl text-gray-600 bg-white hover:bg-gray-50 font-medium transition-colors shadow-sm">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Volver a Gestión
                </a>
            </div>
        </div>

        <!-- Edit Form -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-white">
                <h3 class="text-gray-800 font-bold text-sm">Información del Paciente</h3>
            </div>

            <form action="{{ route('admin.patients.update', $paciente->id) }}" method="POST" class="p-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Datos Personales -->
                    <div class="lg:col-span-3">
                        <h4 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200">Datos Personales</h4>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Nombre Completo *</label>
                        <input type="text" 
                               name="nombre" 
                               value="{{ old('nombre', $paciente->nombre) }}"
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-gray-600 bg-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 sm:text-sm transition-colors" 
                               required>
                        @error('nombre')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Carnet de Identidad *</label>
                        <input type="text" 
                               name="ci" 
                               value="{{ old('ci', $paciente->ci) }}"
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-gray-600 bg-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 sm:text-sm transition-colors" 
                               required>
                        @error('ci')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Sexo *</label>
                        <select name="sexo" 
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-gray-600 bg-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 sm:text-sm transition-colors" 
                                required>
                            <option value="">Seleccionar...</option>
                            <option value="M" {{ old('sexo', $paciente->sexo) == 'M' ? 'selected' : '' }}>Masculino</option>
                            <option value="F" {{ old('sexo', $paciente->sexo) == 'F' ? 'selected' : '' }}>Femenino</option>
                        </select>
                        @error('sexo')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Fecha de Nacimiento</label>
                        <input type="date" 
                               name="fecha_nacimiento" 
                               value="{{ old('fecha_nacimiento', $paciente->fecha_nacimiento?->format('Y-m-d')) }}"
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-gray-600 bg-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 sm:text-sm transition-colors">
                        @error('fecha_nacimiento')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Estado Civil</label>
                        <select name="estado_civil" 
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-gray-600 bg-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 sm:text-sm transition-colors">
                            <option value="">Seleccionar...</option>
                            <option value="Soltero/a" {{ old('estado_civil', $paciente->estado_civil) == 'Soltero/a' ? 'selected' : '' }}>Soltero/a</option>
                            <option value="Casado/a" {{ old('estado_civil', $paciente->estado_civil) == 'Casado/a' ? 'selected' : '' }}>Casado/a</option>
                            <option value="Divorciado/a" {{ old('estado_civil', $paciente->estado_civil) == 'Divorciado/a' ? 'selected' : '' }}>Divorciado/a</option>
                            <option value="Viudo/a" {{ old('estado_civil', $paciente->estado_civil) == 'Viudo/a' ? 'selected' : '' }}>Viudo/a</option>
                            <option value="Unión Libre" {{ old('estado_civil', $paciente->estado_civil) == 'Unión Libre' ? 'selected' : '' }}>Unión Libre</option>
                        </select>
                        @error('estado_civil')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Lugar de Expedición</label>
                        <select name="lugar_expedicion" 
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-gray-600 bg-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 sm:text-sm transition-colors">
                            <option value="">Seleccionar...</option>
                            <option value="La Paz" {{ old('lugar_expedicion', $paciente->lugar_expedicion) == 'La Paz' ? 'selected' : '' }}>La Paz</option>
                            <option value="Cochabamba" {{ old('lugar_expedicion', $paciente->lugar_expedicion) == 'Cochabamba' ? 'selected' : '' }}>Cochabamba</option>
                            <option value="Santa Cruz" {{ old('lugar_expedicion', $paciente->lugar_expedicion) == 'Santa Cruz' ? 'selected' : '' }}>Santa Cruz</option>
                            <option value="Sucre" {{ old('lugar_expedicion', $paciente->lugar_expedicion) == 'Sucre' ? 'selected' : '' }}>Sucre</option>
                            <option value="Potosí" {{ old('lugar_expedicion', $paciente->lugar_expedicion) == 'Potosí' ? 'selected' : '' }}>Potosí</option>
                            <option value="Oruro" {{ old('lugar_expedicion', $paciente->lugar_expedicion) == 'Oruro' ? 'selected' : '' }}>Oruro</option>
                            <option value="Tarija" {{ old('lugar_expedicion', $paciente->lugar_expedicion) == 'Tarija' ? 'selected' : '' }}>Tarija</option>
                            <option value="Chuquisaca" {{ old('lugar_expedicion', $paciente->lugar_expedicion) == 'Chuquisaca' ? 'selected' : '' }}>Chuquisaca</option>
                            <option value="Beni" {{ old('lugar_expedicion', $paciente->lugar_expedicion) == 'Beni' ? 'selected' : '' }}>Beni</option>
                            <option value="Pando" {{ old('lugar_expedicion', $paciente->lugar_expedicion) == 'Pando' ? 'selected' : '' }}>Pando</option>
                        </select>
                        @error('lugar_expedicion')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Contacto y Ubicación -->
                    <div class="lg:col-span-3 mt-6">
                        <h4 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200">Contacto y Ubicación</h4>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Dirección</label>
                        <input type="text" 
                               name="direccion" 
                               value="{{ old('direccion', $paciente->direccion) }}"
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-gray-600 bg-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 sm:text-sm transition-colors">
                        @error('direccion')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Teléfono</label>
                        <input type="text" 
                               name="telefono" 
                               value="{{ old('telefono', $paciente->telefono) }}"
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-gray-600 bg-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 sm:text-sm transition-colors">
                        @error('telefono')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Correo Electrónico</label>
                        <input type="email" 
                               name="correo" 
                               value="{{ old('correo', $paciente->correo) }}"
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-gray-600 bg-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 sm:text-sm transition-colors">
                        @error('correo')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Información Adicional -->
                    <div class="lg:col-span-3 mt-6">
                        <h4 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b border-gray-200">Información Adicional</h4>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Nacionalidad</label>
                        <select name="nacionalidad" 
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-gray-600 bg-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 sm:text-sm transition-colors">
                            <option value="">Seleccionar...</option>
                            <option value="Bolivia" {{ old('nacionalidad', $paciente->nacionalidad) == 'Bolivia' ? 'selected' : '' }}>Bolivia</option>
                            <option value="Argentina" {{ old('nacionalidad', $paciente->nacionalidad) == 'Argentina' ? 'selected' : '' }}>Argentina</option>
                            <option value="Brasil" {{ old('nacionalidad', $paciente->nacionalidad) == 'Brasil' ? 'selected' : '' }}>Brasil</option>
                            <option value="Chile" {{ old('nacionalidad', $paciente->nacionalidad) == 'Chile' ? 'selected' : '' }}>Chile</option>
                            <option value="Colombia" {{ old('nacionalidad', $paciente->nacionalidad) == 'Colombia' ? 'selected' : '' }}>Colombia</option>
                            <option value="Ecuador" {{ old('nacionalidad', $paciente->nacionalidad) == 'Ecuador' ? 'selected' : '' }}>Ecuador</option>
                            <option value="Paraguay" {{ old('nacionalidad', $paciente->nacionalidad) == 'Paraguay' ? 'selected' : '' }}>Paraguay</option>
                            <option value="Perú" {{ old('nacionalidad', $paciente->nacionalidad) == 'Perú' ? 'selected' : '' }}>Perú</option>
                            <option value="Uruguay" {{ old('nacionalidad', $paciente->nacionalidad) == 'Uruguay' ? 'selected' : '' }}>Uruguay</option>
                            <option value="Venezuela" {{ old('nacionalidad', $paciente->nacionalidad) == 'Venezuela' ? 'selected' : '' }}>Venezuela</option>
                        </select>
                        @error('nacionalidad')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Profesión</label>
                        <input type="text" 
                               name="profesion" 
                               value="{{ old('profesion', $paciente->profesion) }}"
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-gray-600 bg-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 sm:text-sm transition-colors">
                        @error('profesion')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Empresa de Trabajo</label>
                        <input type="text" 
                               name="empresa_trabajo" 
                               value="{{ old('empresa_trabajo', $paciente->empresa_trabajo) }}"
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-gray-600 bg-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 sm:text-sm transition-colors">
                        @error('empresa_trabajo')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">Código de Seguro</label>
                        <input type="text" 
                               name="codigo_seguro" 
                               value="{{ old('codigo_seguro', $paciente->codigo_seguro) }}"
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-gray-600 bg-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 sm:text-sm transition-colors">
                        @error('codigo_seguro')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end gap-3 mt-8 pt-6 border-t border-gray-200">
                    <a href="{{ route('admin.pacientes.gestionar') }}" 
                       class="inline-flex items-center px-6 py-2.5 border border-gray-200 rounded-xl text-gray-600 bg-white hover:bg-gray-50 font-medium transition-colors shadow-sm">
                        Cancelar
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center px-6 py-2.5 bg-blue-600 text-white rounded-xl hover:bg-blue-700 font-medium transition-colors shadow-sm">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Actualizar Paciente
                    </button>
                </div>
            </form>
        </div>

    </div>
@endsection
