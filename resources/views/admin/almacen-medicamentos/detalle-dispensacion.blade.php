@extends('layouts.app')

@section('title', 'Detalle de Dispensación #' . $dispensacion->id)

@section('content')
<div class="min-h-screen bg-gray-50 p-6">

    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <div class="flex items-center gap-3 mb-1">
                    <a href="{{ route('admin.almacen-medicamentos.historial') }}"
                       class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </a>
                    <h1 class="text-3xl font-bold text-gray-900">Detalle de Dispensación</h1>
                    <span class="px-3 py-1 bg-blue-100 text-blue-800 text-sm font-semibold rounded-full">#{{ $dispensacion->id }}</span>
                    @if($dispensacion->paciente_ci)
                        <span class="px-3 py-1 bg-green-100 text-green-800 text-sm font-semibold rounded-full flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Entregado al paciente
                        </span>
                    @else
                        <span class="px-3 py-1 bg-yellow-100 text-yellow-800 text-sm font-semibold rounded-full flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Pendiente de asignar paciente
                        </span>
                    @endif
                </div>
                <p class="text-gray-500 ml-8">
                    Registrada el {{ $dispensacion->fecha_dispensacion->format('d/m/Y') }}
                    a las {{ $dispensacion->fecha_dispensacion->format('H:i') }}
                </p>
            </div>
            <a href="{{ route('admin.almacen-medicamentos.historial') }}"
               class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-colors shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Volver al Historial
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg flex items-center gap-3">
            <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <p class="text-green-800 font-medium">{{ session('success') }}</p>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
            <ul class="text-red-700 text-sm space-y-1">
                @foreach($errors->all() as $error)
                    <li class="flex items-center gap-2">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        {{ $error }}
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Columna izquierda -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Medicamento -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-green-50 flex items-center gap-3">
                    <div class="p-2 bg-green-100 rounded-lg">
                        <svg class="w-5 h-5 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                        </svg>
                    </div>
                    <h2 class="text-lg font-semibold text-gray-900">Medicamento / Insumo</h2>
                </div>
                <div class="p-6">
                    @if($dispensacion->medicamento)
                        <div class="flex items-start justify-between mb-4">
                            <div>
                                <h3 class="text-2xl font-bold text-gray-900">{{ $dispensacion->medicamento->nombre }}</h3>
                                @if($dispensacion->medicamento->descripcion)
                                    <p class="text-gray-500 mt-1">{{ $dispensacion->medicamento->descripcion }}</p>
                                @endif
                            </div>
                            <span class="ml-4 px-3 py-1 inline-flex text-sm font-semibold rounded-full {{ $dispensacion->medicamento->tipo === 'medicamento' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-700' }}">
                                {{ ucfirst($dispensacion->medicamento->tipo) }}
                            </span>
                        </div>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mt-4">
                            <div class="bg-gray-50 rounded-lg p-3">
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Unidad de medida</p>
                                <p class="text-sm font-semibold text-gray-900 mt-1">{{ $dispensacion->medicamento->unidad_medida }}</p>
                            </div>
                            @if($dispensacion->medicamento->lote)
                            <div class="bg-gray-50 rounded-lg p-3">
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Lote</p>
                                <p class="text-sm font-semibold text-gray-900 mt-1">{{ $dispensacion->medicamento->lote }}</p>
                            </div>
                            @endif
                            @if($dispensacion->medicamento->fecha_vencimiento)
                            <div class="bg-gray-50 rounded-lg p-3">
                                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Vencimiento</p>
                                <p class="text-sm font-semibold text-gray-900 mt-1">
                                    {{ \Carbon\Carbon::parse($dispensacion->medicamento->fecha_vencimiento)->format('d/m/Y') }}
                                </p>
                            </div>
                            @endif
                        </div>
                    @else
                        <p class="text-gray-400 italic">Medicamento eliminado del sistema.</p>
                    @endif
                </div>
            </div>

            <!-- Detalle de la dispensación -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-blue-50 flex items-center gap-3">
                    <div class="p-2 bg-blue-100 rounded-lg">
                        <svg class="w-5 h-5 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <h2 class="text-lg font-semibold text-gray-900">Detalle de la Dispensación</h2>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Fecha y hora</p>
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <p class="text-base font-semibold text-gray-900">
                                {{ $dispensacion->fecha_dispensacion->format('d/m/Y') }}
                                <span class="text-gray-500 font-normal text-sm">{{ $dispensacion->fecha_dispensacion->format('H:i') }}</span>
                            </p>
                        </div>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Cantidad dispensada</p>
                        <div class="flex items-center gap-2">
                            <span class="text-2xl font-bold text-blue-700">{{ $dispensacion->cantidad }}</span>
                            @if($dispensacion->medicamento)
                                <span class="text-sm text-gray-500">{{ $dispensacion->medicamento->unidad_medida }}</span>
                            @endif
                        </div>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Área destino</p>
                        <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full bg-purple-100 text-purple-800">
                            {{ $dispensacion->area_destino_label }}
                        </span>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">ID de registro</p>
                        <p class="text-sm font-mono text-gray-700">#{{ $dispensacion->id }}</p>
                    </div>
                </div>
            </div>

            <!-- Paciente — ya registrado -->
            @if($dispensacion->paciente)
            <div class="bg-white rounded-xl shadow-sm border border-green-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-green-100 bg-green-50 flex items-center gap-3">
                    <div class="p-2 bg-green-100 rounded-lg">
                        <svg class="w-5 h-5 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <h2 class="text-lg font-semibold text-gray-900">Paciente que recibió el medicamento</h2>
                    <span class="ml-auto px-2 py-0.5 bg-green-100 text-green-700 text-xs font-semibold rounded-full">Registrado</span>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Nombre del paciente</p>
                            <p class="text-lg font-bold text-gray-900">{{ $dispensacion->paciente->nombre }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">C.I. del paciente</p>
                            <p class="text-base font-semibold text-gray-700 font-mono">{{ $dispensacion->paciente_ci }}</p>
                        </div>
                        @if($dispensacion->entregadoPor)
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Registrado por</p>
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center flex-shrink-0">
                                    <span class="text-indigo-700 font-bold text-xs">
                                        {{ strtoupper(substr($dispensacion->entregadoPor->name, 0, 2)) }}
                                    </span>
                                </div>
                                <p class="text-sm font-semibold text-gray-900">{{ $dispensacion->entregadoPor->name }}</p>
                            </div>
                        </div>
                        @endif
                        @if($dispensacion->fecha_entrega_paciente)
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">Fecha de entrega al paciente</p>
                            <p class="text-sm font-semibold text-gray-900">
                                {{ $dispensacion->fecha_entrega_paciente->format('d/m/Y') }}
                                <span class="text-gray-500 font-normal">{{ $dispensacion->fecha_entrega_paciente->format('H:i') }}</span>
                            </p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @else
            <!-- Formulario para registrar paciente -->
            <div class="bg-white rounded-xl shadow-sm border border-yellow-200 overflow-hidden" x-data="{
                ciInput: '',
                paciente: null,
                buscando: false,
                error: '',
                buscarPaciente() {
                    if (!this.ciInput || String(this.ciInput).length < 3) {
                        this.paciente = null;
                        this.error = '';
                        return;
                    }
                    this.buscando = true;
                    this.error = '';
                    this.paciente = null;
                    fetch('/api/buscar-paciente?ci=' + encodeURIComponent(this.ciInput), {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                    })
                    .then(r => r.ok ? r.json() : Promise.reject(r.status))
                    .then(data => {
                        this.buscando = false;
                        if (data && data.ci) {
                            this.paciente = data;
                        } else {
                            this.error = 'No se encontró ningún paciente con ese C.I.';
                        }
                    })
                    .catch(() => {
                        this.buscando = false;
                        this.error = 'No se encontró ningún paciente con ese C.I.';
                    });
                }
            }">
                <div class="px-6 py-4 border-b border-yellow-100 bg-yellow-50 flex items-center gap-3">
                    <div class="p-2 bg-yellow-100 rounded-lg">
                        <svg class="w-5 h-5 text-yellow-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                        </svg>
                    </div>
                    <h2 class="text-lg font-semibold text-gray-900">Registrar paciente que recibió el medicamento</h2>
                </div>
                <div class="p-6">
                    <p class="text-sm text-gray-500 mb-5">
                        Ingresa el <strong>C.I. del paciente</strong> al que se le entregó este medicamento.
                        El sistema registrará quién realizó la entrega y la fecha/hora automáticamente.
                    </p>

                    <!-- Buscador de paciente por CI -->
                    <div class="mb-5">
                        <label class="block text-sm font-medium text-gray-700 mb-2">C.I. del paciente</label>
                        <div class="flex gap-3">
                            <input
                                type="number"
                                x-model="ciInput"
                                @input.debounce.500ms="buscarPaciente()"
                                placeholder="Ej: 12345678"
                                class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                            >
                            <button
                                type="button"
                                @click="buscarPaciente()"
                                class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium transition-colors flex items-center gap-2"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                Buscar
                            </button>
                        </div>
                    </div>

                    <!-- Resultado de búsqueda -->
                    <div x-show="buscando" class="flex items-center gap-2 text-gray-500 text-sm mb-4">
                        <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Buscando paciente...
                    </div>

                    <div x-show="error && !buscando" class="p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700 mb-4" x-text="error"></div>

                    <div x-show="paciente && !buscando" class="p-4 bg-blue-50 border border-blue-200 rounded-lg mb-5">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900" x-text="paciente ? paciente.nombre : ''"></p>
                                <p class="text-xs text-gray-500">C.I. <span x-text="paciente ? paciente.ci : ''"></span></p>
                            </div>
                            <span class="ml-auto px-2 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-full">Paciente encontrado</span>
                        </div>
                    </div>

                    <!-- Formulario de confirmación -->
                    <form
                        method="POST"
                        action="{{ route('admin.almacen-medicamentos.registrar-paciente', $dispensacion->id) }}"
                        x-show="paciente && !buscando"
                    >
                        @csrf
                        <input type="hidden" name="paciente_ci" :value="paciente ? paciente.ci : ''">

                        <div class="bg-gray-50 rounded-lg p-4 mb-5 text-sm text-gray-600 space-y-1">
                            <p>
                                <span class="font-medium">Medicamento:</span>
                                {{ $dispensacion->medicamento->nombre ?? 'N/A' }}
                                ({{ $dispensacion->cantidad }} {{ $dispensacion->medicamento->unidad_medida ?? '' }})
                            </p>
                            <p><span class="font-medium">Área:</span> {{ $dispensacion->area_destino_label }}</p>
                            <p><span class="font-medium">Registrado por:</span> {{ Auth::user()->name }}</p>
                            <p><span class="font-medium">Fecha:</span> {{ now()->format('d/m/Y H:i') }}</p>
                        </div>

                        <button
                            type="submit"
                            class="w-full px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors flex items-center justify-center gap-2 shadow-sm"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Confirmar entrega al paciente
                        </button>
                    </form>
                </div>
            </div>
            @endif

            <!-- Observaciones -->
            @if($dispensacion->observaciones)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-yellow-50 flex items-center gap-3">
                    <div class="p-2 bg-yellow-100 rounded-lg">
                        <svg class="w-5 h-5 text-yellow-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </div>
                    <h2 class="text-lg font-semibold text-gray-900">Observaciones</h2>
                </div>
                <div class="p-6">
                    <p class="text-gray-700 leading-relaxed whitespace-pre-wrap">{{ $dispensacion->observaciones }}</p>
                </div>
            </div>
            @endif

        </div>

        <!-- Columna derecha -->
        <div class="space-y-6">

            <!-- Dispensado por (desde almacén central) -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-indigo-50 flex items-center gap-3">
                    <div class="p-2 bg-indigo-100 rounded-lg">
                        <svg class="w-5 h-5 text-indigo-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <h2 class="text-base font-semibold text-gray-900">Dispensado desde almacén</h2>
                </div>
                <div class="p-5">
                    @if($dispensacion->dispensadoPor)
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <span class="text-indigo-700 font-bold text-sm">
                                    {{ strtoupper(substr($dispensacion->dispensadoPor->name, 0, 2)) }}
                                </span>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">{{ $dispensacion->dispensadoPor->name }}</p>
                                <p class="text-xs text-gray-500">{{ $dispensacion->dispensadoPor->email }}</p>
                            </div>
                        </div>
                        <p class="text-xs text-gray-400 mt-3">Autorizó la salida del medicamento del almacén central.</p>
                    @else
                        <p class="text-sm text-gray-400 italic">Usuario no disponible.</p>
                    @endif
                </div>
            </div>

            <!-- Recibido por (personal del área) -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-teal-50 flex items-center gap-3">
                    <div class="p-2 bg-teal-100 rounded-lg">
                        <svg class="w-5 h-5 text-teal-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <h2 class="text-base font-semibold text-gray-900">Recibido por (área)</h2>
                </div>
                <div class="p-5">
                    @if($dispensacion->recibido_por)
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-teal-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-teal-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-900">{{ $dispensacion->recibido_por }}</p>
                                <p class="text-xs text-gray-500">{{ $dispensacion->area_destino_label }}</p>
                            </div>
                        </div>
                        <p class="text-xs text-gray-400 mt-3">Personal que recibió el medicamento en el área.</p>
                    @else
                        <div class="flex items-center gap-2 text-gray-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                            </svg>
                            <p class="text-sm italic">No registrado.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Resumen rápido -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="text-base font-semibold text-gray-900">Resumen</h2>
                </div>
                <div class="p-5 space-y-3">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500">Medicamento</span>
                        <span class="font-medium text-gray-900 text-right max-w-32 truncate" title="{{ $dispensacion->medicamento->nombre ?? 'N/A' }}">{{ $dispensacion->medicamento->nombre ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500">Cantidad</span>
                        <span class="font-bold text-blue-700">
                            {{ $dispensacion->cantidad }}
                            @if($dispensacion->medicamento) {{ $dispensacion->medicamento->unidad_medida }} @endif
                        </span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500">Área</span>
                        <span class="font-medium text-purple-700">{{ $dispensacion->area_destino_label }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500">Dispensado</span>
                        <span class="font-medium text-gray-900">{{ $dispensacion->fecha_dispensacion->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="border-t border-gray-100 pt-3 flex justify-between items-center text-sm">
                        <span class="text-gray-500">Paciente</span>
                        @if($dispensacion->paciente)
                            <span class="font-semibold text-green-700 truncate max-w-32" title="{{ $dispensacion->paciente->nombre }}">{{ $dispensacion->paciente->nombre }}</span>
                        @else
                            <span class="text-yellow-600 font-medium text-xs">Pendiente</span>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>


@endsection
