@extends('layouts.app')
@section('content')
    <div class="p-6 bg-gray-50/50 min-h-screen">
        <div class="max-w-4xl mx-auto">
            <!-- Header de Confirmación -->
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-2xl shadow-lg p-8 mb-6">
                <div class="text-center">
                    <div class="bg-white/20 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h1 class="text-3xl font-bold mb-2">¡Consulta Registrada!</h1>
                    <p class="text-blue-100">El paciente ha sido registrado exitosamente</p>
                </div>
            </div>

            <!-- Información del Registro -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold text-gray-800">Resumen del Registro</h3>
                    <div class="text-right">
                        <p class="text-xs text-gray-500">Código de Registro</p>
                        <p class="font-mono text-sm font-bold text-blue-600">{{ $caja->id }}</p>
                    </div>
                </div>

                @if($caja->consulta)
                    <!-- Paciente -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div class="bg-gray-50 rounded-xl p-4">
                            <h4 class="font-semibold text-gray-800 mb-3 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                Datos del Paciente
                            </h4>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Nombre:</span>
                                    <span class="text-sm font-medium">{{ $caja->consulta->paciente->nombre ?? 'N/A' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">CI:</span>
                                    <span class="text-sm font-medium font-mono">{{ $caja->consulta->paciente->ci ?? 'N/A' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Teléfono:</span>
                                    <span class="text-sm font-medium">{{ $caja->consulta->paciente->telefono ?? 'N/A' }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Consulta -->
                        <div class="bg-blue-50 rounded-xl p-4">
                            <h4 class="font-semibold text-gray-800 mb-3 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                Detalles de la Consulta
                            </h4>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Médico:</span>
                                    <span class="text-sm font-medium">{{ $caja->consulta->medico->usuario->name ?? 'N/A' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Especialidad:</span>
                                    <span class="text-sm font-medium">{{ $caja->consulta->especialidad->nombre ?? 'N/A' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Fecha:</span>
                                    <span class="text-sm font-medium">{{ \Carbon\Carbon::parse($caja->consulta->fecha)->format('d/m/Y') ?? 'N/A' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Hora:</span>
                                    <span class="text-sm font-medium">{{ $caja->consulta->hora ?? 'N/A' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pago Pendiente -->
                    <div class="bg-yellow-50 rounded-xl p-4 mb-6">
                        <h4 class="font-semibold text-gray-800 mb-3 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Información de Pago
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="text-center">
                                <p class="text-xs text-gray-500 uppercase tracking-wide">Monto a Pagar</p>
                                <p class="text-xl font-bold text-yellow-600">S/. {{ number_format($caja->monto_pagado, 2) }}</p>
                            </div>
                            <div class="text-center">
                                <p class="text-xs text-gray-500 uppercase tracking-wide">Estado</p>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    Pendiente de Pago
                                </span>
                            </div>
                            <div class="text-center">
                                <p class="text-xs text-gray-500 uppercase tracking-wide">Tipo</p>
                                <p class="font-mono text-sm font-bold">{{ $caja->tipo }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Próximos Pasos -->
                    <div class="bg-orange-50 rounded-xl p-4">
                        <h4 class="font-semibold text-gray-800 mb-3 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Próximos Pasos
                        </h4>
                        <div class="space-y-2">
                            <div class="flex items-start">
                                <span class="bg-orange-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold mr-3 mt-0.5">1</span>
                                <div>
                                    <p class="text-sm font-medium text-gray-800">Paciente debe pasar a caja</p>
                                    <p class="text-xs text-gray-600">El paciente debe dirigirse a módulo de caja para realizar el pago</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <span class="bg-orange-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold mr-3 mt-0.5">2</span>
                                <div>
                                    <p class="text-sm font-medium text-gray-800">Procesar pago en caja</p>
                                    <p class="text-xs text-gray-600">El personal de caja procesará el pago y emitirá factura</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <span class="bg-green-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold mr-3 mt-0.5">3</span>
                                <div>
                                    <p class="text-sm font-medium text-gray-800">Aparecerá en panel médico</p>
                                    <p class="text-xs text-gray-600">Una vez pagado, el paciente aparecerá en el dashboard del médico</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p class="text-gray-500">No se encontraron detalles de la consulta.</p>
                    </div>
                @endif
            </div>

            <!-- Acciones -->
            <div class="flex justify-center gap-4">
                <a href="{{ route('reception') }}" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl font-medium hover:bg-gray-300 transition-colors flex items-center text-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"/>
                    </svg>
                    Volver a Recepción
                </a>
                <button onclick="window.print()" class="px-6 py-3 bg-blue-600 text-white rounded-xl font-medium hover:bg-blue-700 transition-colors flex items-center text-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    Imprimir Registro
                </button>
            </div>
        </div>
    </div>
@endsection
