@extends('layouts.app')

@section('content')
<div class="w-full p-4 sm:p-6 bg-slate-100 min-h-screen text-slate-800">

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 pb-4 border-b-2 border-slate-300 gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2 py-0.5 bg-blue-900 text-white text-xs font-bold uppercase tracking-wider rounded-[1px]">Pacientes</span>
                <span class="text-xs text-slate-500 font-semibold tracking-wide">Ficha de Edición y Actualización</span>
                @if($paciente->is_temp)
                    <span class="px-2 py-0.5 bg-amber-600 text-white text-xs font-bold uppercase tracking-wider rounded-[1px]">
                        Paciente Temporal ({{ $paciente->temp_code }})
                    </span>
                @endif
            </div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight mt-1">
                EDITAR DATOS: {{ strtoupper($paciente->nombre) }}
            </h1>   
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ $backUrl ?? route('patients.show', $paciente->id) }}" class="inline-flex items-center px-4 py-2 bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 text-xs font-bold uppercase tracking-wider transition-colors shadow-xs rounded-[1px]">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Volver
            </a>
        </div>
    </div>

    <!-- Alert / Banner si es paciente temporal -->
    @if($paciente->is_temp)
        <div class="mb-6 p-4 bg-amber-50 border border-amber-300 border-l-4 border-l-amber-600 rounded-[1px] text-amber-950 flex items-start gap-3 shadow-xs">
            <svg class="w-5 h-5 text-amber-700 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div class="text-xs">
                <p class="font-bold text-sm uppercase tracking-wide">Paciente Registrado Temporalmente</p>
              
            </div>
        </div>
    @endif

    <form action="{{ route('admin.patients.update', $paciente->id) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')
        <input type="hidden" name="back_url" value="{{ $backUrl }}">

        <!-- SECCIÓN 1: DATOS PERSONALES Y DE IDENTIFICACIÓN -->
        <div class="bg-white border border-slate-300 shadow-xs rounded-[1px] p-5">
            <div class="border-b border-slate-200 pb-3 mb-4 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="w-6 h-6 bg-blue-700 text-white text-xs font-bold flex items-center justify-center rounded-full">1</span>
                    <h2 class="text-sm font-black text-slate-900 uppercase tracking-wide">Datos Personales y de Identificación</h2>
                </div>
                <span class="text-xs text-slate-500 font-mono">ID Paciente: #{{ $paciente->id }}</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <!-- Nombre Completo -->
                <div class="lg:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Nombre Completo <span class="text-red-600">*</span>
                    </label>
                    <input type="text" 
                           name="nombre" 
                           value="{{ old('nombre', $paciente->nombre) }}"
                           class="w-full px-3 py-2.5 text-sm bg-white border border-slate-300 text-slate-900 focus:border-blue-700 focus:outline-hidden rounded-[1px]" 
                           required>
                    @error('nombre')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Carnet de Identidad -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Carnet de Identidad 
                    </label>
                    <input type="text" 
                           name="ci" 
                           value="{{ old('ci', $paciente->ci) }}"
                           class="w-full px-3 py-2.5 text-sm bg-white border border-slate-300 text-slate-900 focus:border-blue-700 focus:outline-hidden rounded-[1px]">
                    @error('ci')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Lugar de Expedición -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Lugar de Expedición
                    </label>
                    @php $expSel = old('lugar_expedicion', $paciente->lugar_expedicion); @endphp
                    <select name="lugar_expedicion" 
                            class="w-full px-3 py-2.5 text-sm bg-white border border-slate-300 text-slate-900 focus:border-blue-700 focus:outline-hidden rounded-[1px]">
                        <option value="">Seleccionar departamento...</option>
                        <option value="LP" {{ $expSel == 'LP' ? 'selected' : '' }}>LP - La Paz</option>
                        <option value="CB" {{ $expSel == 'CB' ? 'selected' : '' }}>CB - Cochabamba</option>
                        <option value="SC" {{ $expSel == 'SC' ? 'selected' : '' }}>SC - Santa Cruz</option>
                        <option value="SU" {{ $expSel == 'SU' ? 'selected' : '' }}>SU - Sucre</option>
                        <option value="PT" {{ $expSel == 'PT' ? 'selected' : '' }}>PT - Potosí</option>
                        <option value="OR" {{ $expSel == 'OR' ? 'selected' : '' }}>OR - Oruro</option>
                        <option value="TJ" {{ $expSel == 'TJ' ? 'selected' : '' }}>TJ - Tarija</option>
                        <option value="CH" {{ $expSel == 'CH' ? 'selected' : '' }}>CH - Chuquisaca</option>
                        <option value="BN" {{ $expSel == 'BN' ? 'selected' : '' }}>BN - Beni</option>
                        <option value="PD" {{ $expSel == 'PD' ? 'selected' : '' }}>PD - Pando</option>
                    </select>
                    @error('lugar_expedicion')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Sexo -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Sexo
                    </label>
                    @php $sexoSel = old('sexo', $paciente->sexo); @endphp
                    <select name="sexo" 
                            class="w-full px-3 py-2.5 text-sm bg-white border border-slate-300 text-slate-900 focus:border-blue-700 focus:outline-hidden rounded-[1px]">
                        <option value="">Seleccionar sexo...</option>
                        <option value="M" {{ $sexoSel == 'M' ? 'selected' : '' }}>Masculino</option>
                        <option value="F" {{ $sexoSel == 'F' ? 'selected' : '' }}>Femenino</option>
                    </select>
                    @error('sexo')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Fecha de Nacimiento -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Fecha de Nacimiento
                    </label>
                    @php
                        $fechaNac = old('fecha_nacimiento', $paciente->fecha_nacimiento ? \Carbon\Carbon::parse($paciente->fecha_nacimiento)->format('Y-m-d') : '');
                    @endphp
                    <input type="date" 
                           name="fecha_nacimiento" 
                           value="{{ $fechaNac }}"
                           class="w-full px-3 py-2.5 text-sm bg-white border border-slate-300 text-slate-900 focus:border-blue-700 focus:outline-hidden rounded-[1px]">
                    @error('fecha_nacimiento')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Estado Civil -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Estado Civil
                    </label>
                    @php $ecSel = old('estado_civil', $paciente->estado_civil); @endphp
                    <select name="estado_civil" 
                            class="w-full px-3 py-2.5 text-sm bg-white border border-slate-300 text-slate-900 focus:border-blue-700 focus:outline-hidden rounded-[1px]">
                        <option value="">Seleccionar...</option>
                        <option value="Soltero/a" {{ $ecSel == 'Soltero/a' ? 'selected' : '' }}>Soltero/a</option>
                        <option value="Casado/a" {{ $ecSel == 'Casado/a' ? 'selected' : '' }}>Casado/a</option>
                        <option value="Divorciado/a" {{ $ecSel == 'Divorciado/a' ? 'selected' : '' }}>Divorciado/a</option>
                        <option value="Viudo/a" {{ $ecSel == 'Viudo/a' ? 'selected' : '' }}>Viudo/a</option>
                        <option value="Unión Libre" {{ $ecSel == 'Unión Libre' ? 'selected' : '' }}>Unión Libre</option>
                    </select>
                    @error('estado_civil')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nacionalidad -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Nacionalidad
                    </label>
                    @php $nacSel = old('nacionalidad', $paciente->nacionalidad ?? 'Boliviana'); @endphp
                    <select name="nacionalidad" 
                            class="w-full px-3 py-2.5 text-sm bg-white border border-slate-300 text-slate-900 focus:border-blue-700 focus:outline-hidden rounded-[1px]">
                        <option value="Boliviana" {{ $nacSel == 'Boliviana' ? 'selected' : '' }}>Boliviana</option>
                        <option value="Argentina" {{ $nacSel == 'Argentina' ? 'selected' : '' }}>Argentina</option>
                        <option value="Brasilera" {{ $nacSel == 'Brasilera' ? 'selected' : '' }}>Brasilera</option>
                        <option value="Chilena" {{ $nacSel == 'Chilena' ? 'selected' : '' }}>Chilena</option>
                        <option value="Colombiana" {{ $nacSel == 'Colombiana' ? 'selected' : '' }}>Colombiana</option>
                        <option value="Española" {{ $nacSel == 'Española' ? 'selected' : '' }}>Española</option>
                        <option value="Estadounidense" {{ $nacSel == 'Estadounidense' ? 'selected' : '' }}>Estadounidense</option>
                        <option value="Paraguaya" {{ $nacSel == 'Paraguaya' ? 'selected' : '' }}>Paraguaya</option>
                        <option value="Peruana" {{ $nacSel == 'Peruana' ? 'selected' : '' }}>Peruana</option>
                        <option value="Uruguaya" {{ $nacSel == 'Uruguaya' ? 'selected' : '' }}>Uruguaya</option>
                        <option value="Venezolana" {{ $nacSel == 'Venezolana' ? 'selected' : '' }}>Venezolana</option>
                        <option value="Otra" {{ $nacSel == 'Otra' ? 'selected' : '' }}>Otra</option>
                    </select>
                    @error('nacionalidad')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- SECCIÓN 2: CONTACTO Y DOMICILIO -->
        <div class="bg-white border border-slate-300 shadow-xs rounded-[1px] p-5">
            <div class="border-b border-slate-200 pb-3 mb-4">
                <div class="flex items-center gap-2">
                    <span class="w-6 h-6 bg-purple-700 text-white text-xs font-bold flex items-center justify-center rounded-full">2</span>
                    <h2 class="text-sm font-black text-slate-900 uppercase tracking-wide">Ubicación y Contacto</h2>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <!-- Teléfono -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Teléfono / Celular
                    </label>
                    <input type="text" 
                           name="telefono" 
                           value="{{ old('telefono', $paciente->telefono) }}"
                           placeholder="Ej. 70012345"
                           class="w-full px-3 py-2.5 text-sm bg-white border border-slate-300 text-slate-900 focus:border-purple-700 focus:outline-hidden rounded-[1px]">
                    @error('telefono')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Correo Electrónico -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Correo Electrónico
                    </label>
                    <input type="email" 
                           name="correo" 
                           value="{{ old('correo', $paciente->correo) }}"
                           placeholder="correo@ejemplo.com"
                           class="w-full px-3 py-2.5 text-sm bg-white border border-slate-300 text-slate-900 focus:border-purple-700 focus:outline-hidden rounded-[1px]">
                    @error('correo')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Dirección -->
                <div class="sm:col-span-2 lg:col-span-1">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Dirección Domiciliaria
                    </label>
                    <input type="text" 
                           name="direccion" 
                           value="{{ old('direccion', $paciente->direccion) }}"
                           placeholder="Zona, calle, nro..."
                           class="w-full px-3 py-2.5 text-sm bg-white border border-slate-300 text-slate-900 focus:border-purple-700 focus:outline-hidden rounded-[1px]">
                    @error('direccion')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Profesión -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Profesión / Ocupación
                    </label>
                    <input type="text" 
                           name="profesion" 
                           value="{{ old('profesion', $paciente->profesion) }}"
                           placeholder="Ej. Ingeniero, Comerciante..."
                           class="w-full px-3 py-2.5 text-sm bg-white border border-slate-300 text-slate-900 focus:border-purple-700 focus:outline-hidden rounded-[1px]">
                    @error('profesion')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Empresa de Trabajo -->
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Empresa / Lugar de Trabajo
                    </label>
                    <input type="text" 
                           name="empresa_trabajo" 
                           value="{{ old('empresa_trabajo', $paciente->empresa_trabajo) }}"
                           placeholder="Nombre de la empresa o institución"
                           class="w-full px-3 py-2.5 text-sm bg-white border border-slate-300 text-slate-900 focus:border-purple-700 focus:outline-hidden rounded-[1px]">
                    @error('empresa_trabajo')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

       

        <!-- BOTONES DE ACCIÓN -->
        <div class="flex items-center justify-end gap-3 pt-2">
            <a href="{{ $backUrl ?? route('patients.show', $paciente->id) }}" 
               class="px-6 py-2.5 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 text-xs font-bold uppercase tracking-wider transition-colors rounded-[1px] shadow-xs">
                Cancelar
            </a>
            <button type="submit" 
                    class="px-7 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold uppercase tracking-wider border border-blue-600 transition-colors flex items-center gap-2 rounded-[1px] shadow-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span>Guardar Cambios</span>
            </button>
        </div>
    </form>
</div>
@endsection
