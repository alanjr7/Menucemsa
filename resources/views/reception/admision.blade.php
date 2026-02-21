@extends('layouts.app')

@section('content')
<div class="w-full p-6">
        <h2 class="text-sm text-gray-500 uppercase tracking-widest text-blue-600 font-semibold">Admisión de Pacientes</h2>
        <h1 class="text-2xl font-bold text-gray-800">Registro de ingreso y apertura de episodio</h1>
    </div>

    <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl p-8 border border-gray-200">

        <div class="flex items-center justify-between mb-12 relative max-w-4xl mx-auto">
            <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                <div class="w-full h-0.5 bg-gray-200"></div>
            </div>

            <div class="relative flex flex-col items-center group">
                <div class="w-10 h-10 flex items-center justify-center {{ $paso >= 1 ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-500' }} rounded-full font-bold z-10 transition-colors duration-500">
                    @if($paso > 1) ✓ @else 1 @endif
                </div>
                <div class="absolute top-12 text-xs font-semibold {{ $paso >= 1 ? 'text-blue-600' : 'text-gray-400' }}">Búsqueda</div>
            </div>

            <div class="relative flex flex-col items-center group">
                <div class="w-10 h-10 flex items-center justify-center {{ $paso >= 2 ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-500' }} rounded-full font-bold z-10 transition-colors duration-500">
                    @if($paso > 2) ✓ @else 2 @endif
                </div>
                <div class="absolute top-12 text-xs font-semibold {{ $paso >= 2 ? 'text-blue-600' : 'text-gray-400' }}">Datos</div>
            </div>

            <div class="relative flex flex-col items-center group">
                <div class="w-10 h-10 flex items-center justify-center {{ $paso >= 3 ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-500' }} rounded-full font-bold z-10">
                    @if($paso > 3) ✓ @else 3 @endif
                </div>
                <div class="absolute top-12 text-xs font-medium {{ $paso >= 3 ? 'text-blue-600' : 'text-gray-400' }}">Episodio</div>
            </div>

            <div class="relative flex flex-col items-center group">
                <div class="w-10 h-10 flex items-center justify-center {{ $paso >= 4 ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-500' }} rounded-full font-bold z-10">4</div>
                <div class="absolute top-12 text-xs font-medium {{ $paso >= 4 ? 'text-blue-600' : 'text-gray-400' }}">Confirmación</div>
            </div>
        </div>

   @if($paso == 1)
    <div class="mt-10 animate-fade-in">
        <h3 class="text-center text-lg font-bold mb-8 text-gray-800">Paso 1: Buscar o Registrar Paciente</h3>

        <div class="max-w-4xl mx-auto">
            <label class="block text-sm font-semibold text-gray-700 mb-2">Buscar Paciente Existente</label>
            <div class="flex gap-2 mb-6">
                <div class="relative flex-1">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </span>
                    <input type="text" class="block w-full pl-10 border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Buscar por DNI, nombre o historia clínica">
                </div>
                <button class="bg-blue-600 text-white px-8 py-2 rounded-lg font-bold hover:bg-blue-700 transition">Buscar</button>
            </div>

            <div class="relative flex py-5 items-center">
                <div class="flex-grow border-t border-gray-200"></div>
                <span class="flex-shrink mx-4 text-gray-400 text-sm">O</span>
                <div class="flex-grow border-t border-gray-200"></div>
            </div>

            <div class="text-center mb-10">
                <a href="{{ route('admision.index', ['paso' => 2]) }}" class="w-full flex items-center justify-center py-3 border-2 border-dashed border-gray-200 rounded-xl text-gray-600 hover:bg-gray-50 hover:border-blue-300 hover:text-blue-600 transition-all group">
                    <svg class="w-5 h-5 mr-2 text-gray-400 group-hover:text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                    <span class="font-semibold">Registrar Nuevo Paciente</span>
                </a>
            </div>

            <div class="bg-blue-50 border border-blue-200 rounded-xl p-6 flex items-start gap-4">
                <div class="bg-blue-100 p-2 rounded-full">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div class="flex-1">
                    <h4 class="text-blue-800 font-bold text-sm mb-1">Resultado de búsqueda</h4>
                    <p class="text-blue-700 text-sm mb-4">
                        Paciente encontrado: <span class="font-bold">García Mendoza, Juan Carlos</span> - <span class="text-gray-500">HC-001234</span>
                    </p>
                    <a href="{{ route('admision.index', ['paso' => 3]) }}" class="inline-flex items-center px-6 py-2 bg-blue-600 text-white text-sm font-bold rounded-lg hover:bg-blue-700 shadow-md transition">
                        Continuar con este paciente
                    </a>
                </div>
            </div>
        </div>
    </div>
@elseif($paso == 2)
    <div class="mt-10 animate-fade-in">
        <h3 class="text-lg font-bold mb-6 text-gray-700 border-b pb-2">Paso 2: Datos del Paciente</h3>

        <form action="#" class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
            <div>
                <label class="block text-sm font-semibold text-gray-700">Tipo de Documento *</label>
                <select class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option selected disabled>Seleccione</option>
                    <option>DNI - Documento Nacional de Identidad</option>
                    <option>Pasaporte</option>
                    <option>Carnet de Extranjería</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700">Número de Documento *</label>
                <input type="text" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm" placeholder="Ingrese número">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700">Apellido Paterno *</label>
                <input type="text" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm" placeholder="Ingrese apellido paterno">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700">Apellido Materno *</label>
                <input type="text" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm" placeholder="Ingrese apellido materno">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700">Nombres *</label>
                <input type="text" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm" placeholder="Ingrese nombres">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700">Fecha de Nacimiento *</label>
                <input type="date" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm text-gray-500">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700">Sexo *</label>
                <select class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option selected disabled>Seleccione</option>
                    <option>Masculino</option>
                    <option>Femenino</option>
                    <option>Otro</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700">Teléfono</label>
                <input type="tel" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm" placeholder="Ingrese teléfono">
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-gray-700">Dirección</label>
                <input type="text" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm" placeholder="Ingrese dirección completa">
            </div>

            <div class="md:col-span-2 flex justify-between pt-8 border-t mt-4">
                <a href="{{ route('admision.index', ['paso' => 1]) }}"
                   class="inline-flex items-center px-6 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 shadow-sm transition">
                    Anterior
                </a>
                <a href="{{ route('admision.index', ['paso' => 3]) }}"
                   class="inline-flex items-center px-10 py-2 bg-blue-600 border border-transparent text-sm font-bold rounded-lg text-white shadow-md hover:bg-blue-700 transition">
                    Continuar al Paso 3
                </a>
            </div>
        </form>
    </div>

     @elseif($paso == 3)
    <div class="mt-10 animate-fade-in">
        <h3 class="text-lg font-bold mb-6 text-gray-700 border-b pb-2">Paso 3: Datos del Episodio de Atención</h3>

        <form action="#" class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
            <div>
                <label class="block text-sm font-semibold text-gray-700">Tipo de Atención *</label>
                <select class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option selected disabled>Seleccione</option>
                    <option>Consulta Externa</option>
                    <option>Emergencia</option>
                    <option>Hospitalización</option>
                    <option>Cirugía Programada</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700">Especialidad *</label>
                <select class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option selected disabled>Seleccione</option>
                    <option>Medicina General</option>
                    <option>Cardiología</option>
                    <option>Pediatría</option>
                    <option>Traumatología</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700">Médico Tratante *</label>
                <select class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option selected disabled>Seleccione</option>
                    <option>Dr. Carlos Mendoza</option>
                    <option>Dra. María Torres</option>
                    <option>Dr. Juan Rodríguez</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700">Tipo de Seguro *</label>
                <select class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option selected disabled>Seleccione</option>
                    <option>Particular</option>
                    <option>SIS</option>
                    <option>EPS</option>
                    <option>Seguro Privado</option>
                </select>
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-gray-700">Motivo de Consulta *</label>
                <input type="text" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm" placeholder="Describa el motivo de la consulta">
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-gray-700">Observaciones</label>
                <textarea rows="3" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm" placeholder="Observaciones adicionales"></textarea>
            </div>

            <div class="md:col-span-2 flex justify-between pt-8 border-t mt-4">
                <a href="{{ route('admision.index', ['paso' => 2]) }}"
                   class="inline-flex items-center px-6 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 shadow-sm transition">
                    Anterior
                </a>
                <a href="{{ route('admision.index', ['paso' => 4]) }}"
                   class="inline-flex items-center px-10 py-2 bg-blue-600 border border-transparent text-sm font-bold rounded-lg text-white shadow-md hover:bg-blue-700 transition">
                    Continuar al Paso 4
                </a>
            </div>
        </form>
    </div>

 @elseif($paso == 4)
    <div class="mt-10 animate-fade-in">
        <h3 class="text-lg font-bold mb-6 text-gray-700 border-b pb-2">Paso 4: Confirmación de Admisión</h3>

        <div class="bg-blue-50 border border-blue-100 rounded-xl p-6 mb-6">
            <h4 class="text-blue-800 font-bold mb-4 flex items-center">
                Datos del Paciente
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-blue-600 font-medium">Nombre: <span class="text-gray-800 font-bold">García Mendoza, Juan Carlos</span></p>
                    <p class="text-blue-600 font-medium mt-2">Historia Clínica: <span class="text-gray-800 font-bold">HC-001234</span></p>
                </div>
                <div>
                    <p class="text-blue-600 font-medium">DNI: <span class="text-gray-800 font-bold">12345678</span></p>
                    <p class="text-blue-600 font-medium mt-2">Edad: <span class="text-gray-800 font-bold">45 años</span></p>
                </div>
            </div>
        </div>

        <div class="bg-green-50 border border-green-100 rounded-xl p-6 mb-8">
            <h4 class="text-green-800 font-bold mb-4 flex items-center">
                Datos del Episodio
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-green-700 font-medium">Tipo de Atención: <span class="text-gray-800 font-bold">Hospitalización</span></p>
                    <p class="text-green-700 font-medium mt-2">Médico: <span class="text-gray-800 font-bold">Dr. Ramírez, Carlos</span></p>
                    <p class="text-green-700 font-medium mt-2">Motivo: <span class="text-gray-800 font-bold">Descompensación diabética</span></p>
                </div>
                <div>
                    <p class="text-green-700 font-medium">Especialidad: <span class="text-gray-800 font-bold">Medicina General</span></p>
                    <p class="text-green-700 font-medium mt-2">Seguro: <span class="text-gray-800 font-bold">Particular</span></p>
                </div>
            </div>
        </div>

        <div class="space-y-2 mb-8">
            <div class="flex items-center text-xs font-bold text-green-700 bg-green-100 px-3 py-1 rounded-full w-max border border-green-200">
                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"></path></svg>
                Datos del paciente validados
            </div>
            <div class="flex items-center text-xs font-bold text-green-700 bg-green-100 px-3 py-1 rounded-full w-max border border-green-200">
                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"></path></svg>
                Seguro verificado
            </div>
            <div class="flex items-center text-xs font-bold text-green-700 bg-green-100 px-3 py-1 rounded-full w-max border border-green-200">
                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"></path></svg>
                Cama disponible
            </div>
        </div>

        <div class="flex justify-between items-center mt-10">
            <a href="{{ route('admision.index', ['paso' => 3]) }}"
               class="px-6 py-2 border border-gray-300 text-sm font-bold rounded-lg text-gray-700 bg-white hover:bg-gray-50 shadow-sm transition">
                Anterior
            </a>
            <button type="submit"
                    class="flex items-center px-8 py-3 bg-emerald-500 text-white font-bold rounded-lg shadow-lg hover:bg-emerald-600 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Confirmar Admisión
            </button>
        </div>
    </div>
@endif

    </div>
@endsection
