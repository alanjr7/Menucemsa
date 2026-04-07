@extends('layouts.app')

@section('content')
<div class="p-6 bg-gray-50/50 min-h-screen">
        <div class="max-w-6xl mx-auto">
            <!-- Header -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
                <div class="flex justify-between items-center">
                    <div class="flex items-center gap-4">
                        <a href="{{ route('consulta.index') }}" class="text-gray-500 hover:text-gray-700">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </a>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-800">Detalles de Consulta</h1>
                            <p class="text-sm text-gray-500 mt-1">Consulta #{{ $consulta->nro }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-gray-500">Fecha y Hora</p>
                        <p class="font-semibold text-gray-900">{{ $consulta->fecha->format('d/m/Y') }} • {{ $consulta->hora }}</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Información del Paciente -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Datos del Paciente
                        </h3>
                        
                        <div class="text-center mb-4">
                            <div class="bg-blue-100 text-blue-600 rounded-full w-20 h-20 flex items-center justify-center text-2xl font-bold mx-auto mb-3">
                                {{ substr($consulta->paciente->nombre, 0, 1) }}
                            </div>
                            <h4 class="font-semibold text-gray-900 text-lg">{{ $consulta->paciente->nombre }}</h4>
                        </div>

                        <div class="space-y-3">
                            <div class="flex justify-between py-2 border-b border-gray-100">
                                <span class="text-sm text-gray-600">C.I.:</span>
                                <span class="text-sm font-medium font-mono">{{ $consulta->paciente->ci }}</span>
                            </div>
                            <div class="flex justify-between py-2 border-b border-gray-100">
                                <span class="text-sm text-gray-600">Sexo:</span>
                                <span class="text-sm font-medium">{{ $consulta->paciente->sexo }}</span>
                            </div>
                            <div class="flex justify-between py-2 border-b border-gray-100">
                                <span class="text-sm text-gray-600">Teléfono:</span>
                                <span class="text-sm font-medium">{{ $consulta->paciente->telefono ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between py-2 border-b border-gray-100">
                                <span class="text-sm text-gray-600">Correo:</span>
                                <span class="text-sm font-medium">{{ $consulta->paciente->correo ?? 'N/A' }}</span>
                            </div>
                            @if($consulta->paciente->direccion)
                                <div class="py-2">
                                    <span class="text-sm text-gray-600">Dirección:</span>
                                    <p class="text-sm font-medium mt-1">{{ $consulta->paciente->direccion }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Estado de Pago -->
                    <div class="bg-green-50 rounded-2xl p-4 border border-green-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-green-800">Estado de Pago</p>
                                <p class="text-xs text-green-600 mt-1">Pagado y verificado</p>
                            </div>
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="mt-3 pt-3 border-t border-green-200">
                            <p class="text-xs text-green-700">Monto: S/. {{ number_format($consulta->caja->monto_pagado, 2) }}</p>
                            <p class="text-xs text-green-700">Factura: {{ $consulta->caja->nro_factura }}</p>
                        </div>
                    </div>
                </div>

                <!-- Detalles de la Consulta -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            Información de la Consulta
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Especialidad</label>
                                <div class="bg-gray-50 rounded-lg px-4 py-2">
                                    <p class="font-medium text-gray-900">{{ $consulta->especialidad->nombre }}</p>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Médico</label>
                                <div class="bg-gray-50 rounded-lg px-4 py-2">
                                    <p class="font-medium text-gray-900">{{ $consulta->medico->user->name }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Motivo de Consulta</label>
                            <div class="bg-blue-50 rounded-lg px-4 py-3">
                                <p class="text-gray-900">{{ $consulta->motivo }}</p>
                            </div>
                        </div>

                        @if($consulta->observaciones)
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Observaciones</label>
                                <div class="bg-gray-50 rounded-lg px-4 py-3">
                                    <p class="text-gray-900">{{ $consulta->observaciones }}</p>
                                </div>
                            </div>
                        @endif

                        <!-- Estado de la Consulta -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Estado Actual</label>
                            <div class="flex items-center gap-3">
                                @switch($consulta->estado)
                                    @case('pendiente')
                                        <span class="px-3 py-1 bg-orange-100 text-orange-800 text-sm font-medium rounded-full">
                                            Pendiente de Atención
                                        </span>
                                        @break
                                    @case('en_atencion')
                                        <span class="px-3 py-1 bg-blue-100 text-blue-800 text-sm font-medium rounded-full">
                                            En Atención
                                        </span>
                                        @break
                                    @case('completada')
                                        <span class="px-3 py-1 bg-green-100 text-green-800 text-sm font-medium rounded-full">
                                            Completada
                                        </span>
                                        @break
                                    @default
                                        <span class="px-3 py-1 bg-gray-100 text-gray-800 text-sm font-medium rounded-full">
                                            Desconocido
                                        </span>
                                @endswitch
                            </div>
                        </div>

                        <!-- Acciones -->
                        @if($consulta->estado === 'pendiente')
                            <div class="flex gap-3">
                                <button onclick="iniciarConsulta('{{ $consulta->nro }}')" class="px-6 py-3 bg-orange-500 hover:bg-orange-600 text-white font-medium rounded-xl transition-colors">
                                    Iniciar Atención
                                </button>
                            </div>
                        @elseif($consulta->estado === 'en_atencion')
                            <div class="flex gap-3">
                                <button onclick="mostrarFormularioCompletar()" class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-medium rounded-xl transition-colors">
                                    Completar Consulta
                                </button>
                            </div>
                        @endif
                    </div>

                    <!-- Formulario para completar consulta (oculto por defecto) -->
                    <div id="formularioCompletar" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hidden">
                        <h3 class="text-lg font-bold text-gray-800 mb-4">Completar Consulta</h3>
                        
                        <form id="formCompletarConsulta" onsubmit="completarConsulta(event, '{{ $consulta->codigo }}')">
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Diagnóstico</label>
                                <textarea name="diagnostico" rows="3" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition-all" placeholder="Ingrese el diagnóstico..."></textarea>
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Tratamiento</label>
                                <textarea name="tratamiento" rows="3" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition-all" placeholder="Ingrese el tratamiento indicado..."></textarea>
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Indicaciones</label>
                                <textarea name="indicaciones" rows="2" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition-all" placeholder="Indicaciones para el paciente..."></textarea>
                            </div>

                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Receta Médica (opcional)</label>
                                <textarea name="medicamentos" rows="4" class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none focus:border-green-500 focus:ring-2 focus:ring-green-100 transition-all" placeholder="Lista de medicamentos y dosis..."></textarea>
                            </div>

                            <div class="flex gap-3">
                                <button type="button" onclick="ocultarFormularioCompletar()" class="px-6 py-3 border border-gray-200 rounded-xl text-gray-700 font-medium hover:bg-gray-50 transition-colors">
                                    Cancelar
                                </button>
                                <button type="submit" class="px-6 py-3 bg-green-600 text-white rounded-xl font-medium hover:bg-green-700 transition-colors">
                                    Guardar y Completar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function mostrarFormularioCompletar() {
            document.getElementById('formularioCompletar').classList.remove('hidden');
            document.getElementById('formularioCompletar').scrollIntoView({ behavior: 'smooth' });
        }

        function ocultarFormularioCompletar() {
            document.getElementById('formularioCompletar').classList.add('hidden');
        }

        function iniciarConsulta(consultaId) {
            if (confirm('¿Está seguro de iniciar esta consulta?')) {
                fetch(`/consulta-externa/iniciar/${consultaId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al iniciar la consulta');
                });
            }
        }

        function completarConsulta(event, consultaId) {
            event.preventDefault();
            
            const form = document.getElementById('formCompletarConsulta');
            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());
            
            if (confirm('¿Está seguro de completar esta consulta?')) {
                fetch(`/consulta-externa/completar/${consultaId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(data)
                })
                .then(response => {
                    console.log('Response status:', response.status);
                    console.log('Response headers:', response.headers.get('content-type'));
                    return response.json();
                })
                .then(data => {
                    console.log('Response data:', data);
                    if (data.success) {
                        alert('Consulta completada exitosamente');
                        window.location.href = '/consulta-externa';
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    console.error('Error details:', error.message);
                    alert('Error al completar la consulta: ' + error.message);
                });
            }
        }
    </script>
@endsection
