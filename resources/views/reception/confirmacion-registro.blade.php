@extends('layouts.app')
@section('content')
<!-- VISTA EN PANTALLA -->
<div class="screen-only p-6 bg-gray-50/50 min-h-screen">
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
                    <div class="mb-6">
                        <div class="bg-gray-50 rounded-xl p-4">
                            <h4 class="font-semibold text-gray-800 mb-3 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                Datos del Paciente
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-2">
                                <div class="flex justify-between border-b border-gray-200 pb-1 md:col-span-2">
                                    <span class="text-sm font-bold text-gray-800">Nombre:</span>
                                    <span class="text-sm text-gray-900">{{ $caja->consulta->paciente->nombre ?? 'N/A' }}</span>
                                </div>
                                <div class="flex justify-between border-b border-gray-200 pb-1 px-2">
                                    <span class="text-sm font-bold text-gray-800">CI:</span>
                                    <span class="text-sm font-mono text-gray-900">{{ $caja->consulta->paciente->ci ?? 'N/A' }} {{ $caja->consulta->paciente->lugar_expedicion ? '- '.$caja->consulta->paciente->lugar_expedicion : '' }}</span>
                                </div>
                                <div class="flex justify-between border-b border-gray-200 pb-1 px-2">
                                    <span class="text-sm font-bold text-gray-800">Edad:</span>
                                    <span class="text-sm text-gray-900">{{ $caja->consulta->paciente->fecha_nacimiento ? \Carbon\Carbon::parse($caja->consulta->paciente->fecha_nacimiento)->age.' años' : 'No registrada' }}</span>
                                </div>
                                <div class="flex justify-between border-b border-gray-200 pb-1 px-2">
                                    <span class="text-sm font-bold text-gray-800">Fecha de Nacimiento:</span>
                                    <span class="text-sm text-gray-900">{{ $caja->consulta->paciente->fecha_nacimiento ? \Carbon\Carbon::parse($caja->consulta->paciente->fecha_nacimiento)->format('d/m/Y') : 'No registrada' }}</span>
                                </div>
                                <div class="flex justify-between border-b border-gray-200 pb-1 px-2">
                                    <span class="text-sm font-bold text-gray-800">Sexo:</span>
                                    <span class="text-sm text-gray-900">{{ $caja->consulta->paciente->sexo === 'M' ? 'Masculino' : ($caja->consulta->paciente->sexo === 'F' ? 'Femenino' : 'N/A') }}</span>
                                </div>
                                <div class="flex justify-between border-b border-gray-200 pb-1 px-2">
                                    <span class="text-sm font-bold text-gray-800">Estado civil:</span>
                                    <span class="text-sm text-gray-900">{{ $caja->consulta->paciente->estado_civil ?? 'No registrado' }}</span>
                                </div>
                                <div class="flex justify-between border-b border-gray-200 pb-1 px-2">
                                    <span class="text-sm font-bold text-gray-800">Nacionalidad:</span>
                                    <span class="text-sm text-gray-900">{{ $caja->consulta->paciente->nacionalidad ?? 'No registrada' }}</span>
                                </div>
                                <div class="flex justify-between border-b border-gray-200 pb-1 px-2">
                                    <span class="text-sm font-bold text-gray-800">Teléfono:</span>
                                    <span class="text-sm text-gray-900">{{ $caja->consulta->paciente->telefono ?? 'N/A' }}</span>
                                </div>
                                <div class="flex justify-between border-b border-gray-200 pb-1 px-2">
                                    <span class="text-sm font-bold text-gray-800">Correo:</span>
                                    <span class="text-sm text-gray-900">{{ $caja->consulta->paciente->correo ?? 'No registrado' }}</span>
                                </div>
                                <div class="flex justify-between border-b border-gray-200 pb-1 px-2">
                                    <span class="text-sm font-bold text-gray-800">Profesión:</span>
                                    <span class="text-sm text-gray-900">{{ $caja->consulta->paciente->profesion ?? 'No registrada' }}</span>
                                </div>
                                <div class="flex justify-between border-b border-gray-200 pb-1 px-2">
                                    <span class="text-sm font-bold text-gray-800">Empresa:</span>
                                    <span class="text-sm text-gray-900">{{ $caja->consulta->paciente->empresa_trabajo ?? 'No registrada' }}</span>
                                </div>
                                <div class="flex justify-between border-b border-gray-200 pb-1 px-2">
                                    <span class="text-sm font-bold text-gray-800">Seguro:</span>
                                    <span class="text-sm text-gray-900">{{ $caja->consulta->paciente->seguro->nombre ?? 'Sin seguro' }}</span>
                                </div>
                                <div class="flex justify-between md:col-span-2 border-b border-gray-200 pb-1 px-2">
                                    <span class="text-sm font-bold text-gray-800">Dirección:</span>
                                    <span class="text-sm text-gray-900 text-right max-w-xs">{{ $caja->consulta->paciente->direccion ?? 'No registrada' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Garante (condicional) -->
                    @if($caja->consulta->paciente->garante)
                    <div class="mb-6">
                        <div class="bg-purple-50 rounded-xl p-4 border border-purple-100">
                            <h4 class="font-semibold text-gray-800 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                Datos del Garante / Responsable
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-2">
                                <div class="flex justify-between border-b border-purple-200 pb-1 md:col-span-2">
                                    <span class="text-sm font-bold text-gray-800">Nombre Completo:</span>
                                    <span class="text-sm text-gray-900">{{ $caja->consulta->paciente->garante->nombre ?? 'N/A' }}</span>
                                </div>
                                <div class="flex justify-between border-b border-purple-200 pb-1 px-2">
                                    <span class="text-sm font-bold text-gray-800">CI:</span>
                                    <span class="text-sm font-mono text-gray-900">{{ $caja->consulta->paciente->garante->ci ?? 'N/A' }} {{ $caja->consulta->paciente->garante->lugar_expedicion ? '- '.$caja->consulta->paciente->garante->lugar_expedicion : '' }}</span>
                                </div>
                                <div class="flex justify-between border-b border-purple-200 pb-1 px-2">
                                    <span class="text-sm font-bold text-gray-800">Edad:</span>
                                    <span class="text-sm text-gray-900">{{ $caja->consulta->paciente->garante->fecha_nacimiento ? \Carbon\Carbon::parse($caja->consulta->paciente->garante->fecha_nacimiento)->age.' años' : 'No registrada' }}</span>
                                </div>
                                <div class="flex justify-between border-b border-purple-200 pb-1 px-2">
                                    <span class="text-sm font-bold text-gray-800">Sexo:</span>
                                    <span class="text-sm text-gray-900">{{ $caja->consulta->paciente->garante->sexo === 'M' ? 'Masculino' : ($caja->consulta->paciente->garante->sexo === 'F' ? 'Femenino' : 'N/A') }}</span>
                                </div>
                                <div class="flex justify-between border-b border-purple-200 pb-1 px-2">
                                    <span class="text-sm font-bold text-gray-800">Estado civil:</span>
                                    <span class="text-sm text-gray-900">{{ $caja->consulta->paciente->garante->estado_civil ?? 'No registrado' }}</span>
                                </div>
                                <div class="flex justify-between border-b border-purple-200 pb-1 px-2">
                                    <span class="text-sm font-bold text-gray-800">Nacionalidad:</span>
                                    <span class="text-sm text-gray-900">{{ $caja->consulta->paciente->garante->nacionalidad ?? 'No registrada' }}</span>
                                </div>
                                <div class="flex justify-between border-b border-purple-200 pb-1 px-2">
                                    <span class="text-sm font-bold text-gray-800">Teléfono:</span>
                                    <span class="text-sm text-gray-900">{{ $caja->consulta->paciente->garante->telefono ?? 'No registrado' }}</span>
                                </div>
                                <div class="flex justify-between border-b border-purple-200 pb-1 px-2">
                                    <span class="text-sm font-bold text-gray-800">Correo:</span>
                                    <span class="text-sm text-gray-900">{{ $caja->consulta->paciente->garante->correo ?? 'No registrado' }}</span>
                                </div>
                                <div class="flex justify-between border-b border-purple-200 pb-1 px-2">
                                    <span class="text-sm font-bold text-gray-800">Profesión:</span>
                                    <span class="text-sm text-gray-900">{{ $caja->consulta->paciente->garante->profesion ?? 'No registrada' }}</span>
                                </div>
                                <div class="flex justify-between border-b border-purple-200 pb-1 px-2">
                                    <span class="text-sm font-bold text-gray-800">Empresa:</span>
                                    <span class="text-sm text-gray-900">{{ $caja->consulta->paciente->garante->empresa_trabajo ?? 'No registrada' }}</span>
                                </div>
                                <div class="flex justify-between md:col-span-2 border-b border-purple-200 pb-1 px-2">
                                    <span class="text-sm font-bold text-gray-800">Dirección:</span>
                                    <span class="text-sm text-gray-900 text-right max-w-xs">{{ $caja->consulta->paciente->garante->direccion ?? 'No registrada' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Consulta -->
                    <div class="bg-blue-50 rounded-xl p-4 mb-6">
                        <h4 class="font-semibold text-gray-800 mb-3 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            Detalles de la Consulta
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-2">
                                <div class="flex justify-between px-2">
                                    <span class="text-sm font-bold text-gray-800">Médico:</span>
                                    <span class="text-sm text-gray-900">{{ $caja->consulta->medico->user->name ?? 'N/A' }}</span>
                                </div>
                                <div class="flex justify-between px-2">
                                    <span class="text-sm font-bold text-gray-800">Especialidad:</span>
                                    <span class="text-sm text-gray-900">{{ $caja->consulta->especialidad->nombre ?? 'N/A' }}</span>
                                </div>
                                <div class="flex justify-between px-2">
                                    <span class="text-sm font-bold text-gray-800">Fecha:</span>
                                    <span class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($caja->consulta->fecha)->format('d/m/Y') ?? 'N/A' }}</span>
                                </div>
                                <div class="flex justify-between px-2">
                                    <span class="text-sm font-bold text-gray-800">Hora:</span>
                                    <span class="text-sm text-gray-900">{{ $caja->consulta->hora ?? 'N/A' }}</span>
                                </div>
                                <div class="flex justify-between border-t border-blue-200 pt-2 mt-2 md:col-span-2 px-2">
                                    <span class="text-sm font-bold text-gray-800">Motivo:</span>
                                    <span class="text-sm text-gray-900 text-right max-w-xs">{{ $caja->consulta->motivo ?? 'No registrado' }}</span>
                                </div>
                                <div class="flex justify-between md:col-span-2 px-2">
                                    <span class="text-sm font-bold text-gray-800">Observaciones:</span>
                                    <span class="text-sm text-gray-900 text-right max-w-xs">{{ $caja->consulta->observaciones ?? 'Sin observaciones' }}</span>
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
                                <p class="text-xl font-bold text-yellow-600">Bs. {{ number_format($caja->monto_pagado, 2) }}</p>
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
         <button onclick="imprimir()" 
            class="px-6 py-3 bg-blue-600 text-white rounded-xl font-medium hover:bg-blue-700 transition-colors flex items-center text-sm">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
            </svg>
            Imprimir Registro
        </button>
    </div>
</div>

<!-- FORMATO DE IMPRESION - Oculto en pantalla, visible solo al imprimir -->
<div class="print-only" style="display: none;">
    <div class="doc-container">
        
        <!-- ENCABEZADO -->
        <div class="header-section">
            <div class="logo-area">
                <div style="font-family: Arial, sans-serif;">
                    <h1 style="font-size: 16pt; font-style: italic; font-weight: normal; margin: 0; color: #444;">Clínica Médica</h1>
                    <h2 style="font-size: 20pt; font-weight: bold; margin: -5pt 0 0 0; letter-spacing: 1px;">CEMSA</h2>
                    <div style="border-bottom: 2px solid #000; width: 100%; margin-top: 2px;"></div>
                </div>
            </div>
            
            <div class="info-area">
                <div class="doc-number text-right">
                    <strong>Nº: <span style="margin-left: 20pt;">{{ $caja->id ?? '926010073' }}</span></strong>
                </div>
                <div class="notice-box">
                    <p><strong>ESTIMADO PACIENTE:</strong></p>
                    <p>SI DESEA GUARDAR SUS OBJETOS DE VALOR (JOYAS, DINERO, CELULAR U OTROS). UD.</p>
                    <p>PUEDE HACERLO EN LA ADMINISTRACIÓN</p>
                    <p><strong>LA CLINICA MEDICA CEMSA LTDA. NO SE RESPONSABILIZARÁ EN CASO DE PÉRDIDAS.</strong></p>
                </div>
            </div>
        </div>

        <h3 class="main-title">REGISTRO DE ADMISIÓN Y CONTRATO DE SERVICIOS</h3>

        @if($caja->consulta)
        <!-- DATOS DEL PACIENTE -->
        <div class="form-section">
            <div class="f-row">
                <div class="f-field" style="flex: 2.5;">
                    <span class="f-label">Paciente - Sr. (a):</span>
                    <span class="f-value font-bold">{{ strtoupper($caja->consulta->paciente->nombre ?? '') }}</span>
                </div>
                <div class="f-field" style="flex: 1;">
                    <span class="f-label">F. N.</span>
                    <span class="f-value text-center font-bold">{{ $caja->consulta->paciente->fecha_nacimiento ? \Carbon\Carbon::parse($caja->consulta->paciente->fecha_nacimiento)->format('Y-m-d') : '' }}</span>
                </div>
                <div class="f-field" style="flex: 1.2;">
                    <span class="f-label">Estado Civil</span>
                    <span class="f-value font-bold">{{ strtoupper($caja->consulta->paciente->estado_civil ?? 'SOLTERO(A)') }}</span>
                </div>
            </div>

            <div class="f-row">
                <div class="f-field" style="flex: 1.5;">
                    <span class="f-label">Nacionalidad</span>
                    <span class="f-value font-bold">{{ strtoupper($caja->consulta->paciente->nacionalidad ?? 'BOLIVIANA') }}</span>
                </div>
                <div class="f-field" style="flex: 1;">
                    <span class="f-label">Lugar</span>
                    <span class="f-value text-center font-bold">{{ strtoupper($caja->consulta->paciente->lugar_expedicion ?? 'SC') }}</span>
                </div>
                <div class="f-field" style="flex: 1.5;">
                    <span class="f-label">Teléfono Nº</span>
                    <span class="f-value font-bold">{{ $caja->consulta->paciente->telefono ?? '' }}</span>
                </div>
            </div>

            <div class="f-row">
                <div class="f-field" style="flex: 1.5;">
                    <span class="f-label">C.I. Nº</span>
                    <span class="f-value font-bold">{{ $caja->consulta->paciente->ci ?? '' }}</span>
                </div>
                <div class="f-field" style="flex: 1;">
                    <span class="f-label">Sexo</span>
                    <span class="f-value font-bold">{{ ($caja->consulta->paciente->sexo ?? '') === 'M' ? 'MASCULINO' : (($caja->consulta->paciente->sexo ?? '') === 'F' ? 'FEMENINO' : '') }}</span>
                </div>
                <div class="f-field" style="flex: 1.5;">
                    <span class="f-label">Correo</span>
                    <span class="f-value font-bold">{{ strtoupper($caja->consulta->paciente->correo ?? '') }}</span>
                </div>
            </div>

            <div class="f-row">
                <div class="f-field w-100">
                    <span class="f-label">Dirección</span>
                    <span class="f-value font-bold">{{ strtoupper($caja->consulta->paciente->direccion ?? '') }}</span>
                </div>
            </div>

            <div class="f-row">
                <div class="f-field" style="flex: 1.5;">
                    <span class="f-label">Profesión</span>
                    <span class="f-value font-bold">{{ strtoupper($caja->consulta->paciente->profesion ?? '') }}</span>
                </div>
                <div class="f-field" style="flex: 2.5;">
                    <span class="f-label">Empresa</span>
                    <span class="f-value font-bold">{{ strtoupper($caja->consulta->paciente->empresa_trabajo ?? '') }}</span>
                </div>
            </div>

            <div class="f-row">
                <div class="f-field w-100">
                    <span class="f-label">Seguro</span>
                    <span class="f-value font-bold">{{ strtoupper($caja->consulta->paciente->seguro->nombre ?? 'SIN SEGURO') }}</span>
                </div>
            </div>
        </div>

        <!-- DATOS DEL INGRESO -->
        <h4 class="section-title" style="margin-top: 15pt;">DATOS DEL INGRESO</h4>
        <div class="form-section">
            <div class="f-row">
                <div class="f-field" style="flex: 2;">
                    <span class="f-label">Tipo de Ingreso:</span>
                    <span class="f-value font-bold">CONSULTA EXTERNA</span>
                </div>
                <div class="f-field" style="flex: 2;">
                    <span class="f-label">Código:</span>
                    <span class="f-value font-bold">{{ $caja->consulta->codigo ?? '' }}</span>
                </div>
            </div>

            <div class="f-row">
                <div class="f-field w-100">
                    <span class="f-label">Médico:</span>
                    <span class="f-value font-bold">Dr. {{ strtoupper($caja->consulta->medico->user->name ?? '') }}</span>
                </div>
            </div>

            <div class="f-row">
                <div class="f-field" style="flex: 1.5;">
                    <span class="f-label">Especialidad:</span>
                    <span class="f-value font-bold">{{ strtoupper($caja->consulta->especialidad->nombre ?? '') }}</span>
                </div>
                <div class="f-field" style="flex: 1;">
                    <span class="f-label">Fecha:</span>
                    <span class="f-value font-bold">{{ $caja->consulta->fecha ? \Carbon\Carbon::parse($caja->consulta->fecha)->format('d/m/Y') : '' }}</span>
                </div>
                <div class="f-field" style="flex: 0.8;">
                    <span class="f-label">Hora:</span>
                    <span class="f-value font-bold">{{ substr($caja->consulta->hora ?? '', 0, 5) }}</span>
                </div>
            </div>

            <div class="f-row">
                <div class="f-field w-100">
                    <span class="f-label">Motivo:</span>
                    <span class="f-value font-bold">{{ strtoupper($caja->consulta->motivo ?? 'CONSULTA GENERAL') }}</span>
                </div>
            </div>
        </div>
        @endif

    </div>
</div>

<script>
function imprimir() {
    const contenido = document.querySelector('.print-only').innerHTML;
    const ventana = window.open('', '_blank', 'width=900,height=700');

    ventana.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Imprimir Registro CEMSA</title>
            <style>
                /* Estilos base y de reseteo para impresión */
                @page {
                    size: letter; /* Tamaño carta estándar */
                    margin: 1.5cm; 
                }
                
                body {
                    font-family: Arial, Helvetica, sans-serif;
                    font-size: 8.5pt;
                    color: #000;
                    margin: 0;
                    padding: 0;
                    background: #fff;
                }

                * {
                    box-sizing: border-box;
                }

                .doc-container {
                    width: 100%;
                    max-width: 800px;
                    margin: 0 auto;
                }

                /* Encabezado */
                .header-section {
                    display: flex;
                    justify-content: space-between;
                    margin-bottom: 15pt;
                }

                .logo-area {
                    width: 40%;
                }

                .info-area {
                    width: 55%;
                }

                .doc-number {
                    font-size: 10pt;
                    margin-bottom: 10pt;
                }

                .notice-box {
                    font-size: 7.5pt;
                    line-height: 1.3;
                }
                .notice-box p { margin: 2px 0; }

                /* Títulos */
                .main-title {
                    text-align: center;
                    font-size: 11pt;
                    font-weight: bold;
                    margin: 20pt 0 15pt 0;
                }

                .section-title {
                    font-size: 8.5pt;
                    font-weight: bold;
                    margin: 10pt 0 5pt 0;
                    text-transform: uppercase;
                }

                /* Sistema de filas tipo formulario (Líneas punteadas) */
                .form-section {
                    margin-bottom: 10pt;
                }

                .f-row {
                    display: flex;
                    align-items: flex-end;
                    margin-bottom: 6pt;
                    width: 100%;
                    gap: 10pt;
                }

                .f-field {
                    display: flex;
                    align-items: flex-end;
                }

                .f-label {
                    white-space: nowrap;
                    margin-right: 5pt;
                    font-size: 8pt;
                    color: #111;
                }

                .f-value {
                    flex-grow: 1;
                    border-bottom: 1.5px dotted #000; /* La línea punteada clave */
                    min-height: 12pt;
                    font-weight: normal;
                    font-family: monospace, sans-serif;
                    font-size: 8.5pt;
                    padding-bottom: 1px;
                }

                .w-100 { width: 100%; }
                .text-center { text-align: center; }
                .text-right { text-align: right; }
                .text-justify { text-align: justify; }

                /* Textos en línea (Para el Acta) */
                .inline-val {
                    display: inline-block;
                    border-bottom: 1.5px dotted #000;
                    min-height: 10pt;
                    text-align: center;
                    font-family: monospace, sans-serif;
                }

                /* Firmas */
                .signatures {
                    display: flex;
                    justify-content: space-between;
                    align-items: flex-end;
                    margin-top: 40pt;
                }

                .sig-box {
                    width: 30%;
                    text-align: center;
                    font-size: 8pt;
                }

                .sig-line {
                    border-bottom: 1px solid #000;
                    margin-bottom: 5pt;
                    width: 100%;
                    height: 20pt;
                }

                .stamp-area {
                    position: relative;
                }

                .stamp-text {
                    color: #4a5568; /* Color gris oscuro simulando sello */
                    font-family: 'Times New Roman', serif;
                    font-style: italic;
                    font-size: 10pt;
                    line-height: 1.2;
                }
            </style>
        </head>
        <body onload="setTimeout(() => { window.print(); window.close(); }, 250);">
            ${contenido}
        </body>
        </html>
    `);

    ventana.document.close();
}
</script>
@endsection