@extends('layouts.app')

@section('content')
<!-- VISTA EN PANTALLA -->
<div class="screen-only w-full p-6 bg-gray-50/50 min-h-screen">

    <!-- Page Header -->
    <div class="flex justify-between items-end mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Comprobante de Hospitalización</h1>
            <p class="text-sm text-gray-500">Ficha de ingreso del paciente</p>
        </div>
        <div class="flex gap-3">
           
            <button onclick="imprimir()" class="flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Imprimir Ficha
            </button>
        </div>
    </div>

    <!-- Código de Hospitalización -->
    <div class="bg-blue-600 text-white rounded-t-2xl p-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                <div>
                    <p class="text-sm opacity-90">Código de Hospitalización</p>
                    <p class="text-xl font-bold">{{ $hospitalizacion->id }}</p>
                </div>
            </div>
            <div class="text-right">
                <p class="text-sm opacity-90">Estado</p>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-400 text-yellow-900">
                    <span class="w-2 h-2 bg-yellow-700 rounded-full mr-2 animate-pulse"></span>
                    Hospitalizado
                </span>
            </div>
        </div>
    </div>

    <!-- Patient Info Card -->
    <div class="bg-white rounded-b-2xl shadow-sm border border-gray-100 p-6 mb-6">
        <div class="flex items-center gap-4 mb-6">
            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center">
                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <div>
                <h2 class="text-xl font-bold text-gray-800">{{ $hospitalizacion->paciente->nombre }}</h2>
                <p class="text-sm text-gray-500">CI: {{ $hospitalizacion->paciente->ci }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-3">Información Personal</h3>
                <dl class="space-y-2">
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">Nombre:</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $hospitalizacion->paciente->nombre }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">CI:</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $hospitalizacion->paciente->ci }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">Teléfono:</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $hospitalizacion->paciente->telefono ?? 'No registrado' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">Sexo:</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $hospitalizacion->paciente->sexo }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">Correo:</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $hospitalizacion->paciente->correo ?? 'No registrado' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">Dirección:</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $hospitalizacion->paciente->direccion ?? 'No registrada' }}</dd>
                    </div>
                </dl>
            </div>

            <div>
                <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-3">Información de Ingreso</h3>
                <dl class="space-y-2">
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">Fecha de Ingreso:</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $hospitalizacion->fecha_ingreso->format('d/m/Y H:i') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">Médico Tratante:</dt>
                        <dd class="text-sm font-medium text-gray-900">Dr. {{ $hospitalizacion->medico->user->name ?? 'No asignado' }}</dd>
                    </div>
                    <div class="flex flex-col">
                        <dt class="text-sm text-gray-500 mb-1">Motivo:</dt>
                        <dd class="text-sm font-medium text-gray-900 bg-gray-50 p-2 rounded">{{ $hospitalizacion->motivo }}</dd>
                    </div>
                    <div class="flex flex-col">
                        <dt class="text-sm text-gray-500 mb-1">Diagnóstico Presuntivo:</dt>
                        <dd class="text-sm font-medium text-gray-900 bg-gray-50 p-2 rounded">{{ $hospitalizacion->diagnostico }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>

    <!-- Contacto de Emergencia -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
            </svg>
            Contacto de Emergencia
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <dl class="space-y-2">
                <div class="flex justify-between">
                    <dt class="text-sm text-gray-500">Nombre:</dt>
                    <dd class="text-sm font-medium text-gray-900">{{ $hospitalizacion->contacto_nombre ?? 'No registrado' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-sm text-gray-500">Teléfono:</dt>
                    <dd class="text-sm font-medium text-gray-900">{{ $hospitalizacion->contacto_telefono ?? 'No registrado' }}</dd>
                </div>
            </dl>
            <dl class="space-y-2">
                <div class="flex justify-between">
                    <dt class="text-sm text-gray-500">Parentesco:</dt>
                    <dd class="text-sm font-medium text-gray-900">{{ $hospitalizacion->contacto_parentesco ?? 'No registrado' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-sm text-gray-500">Relación:</dt>
                    <dd class="text-sm font-medium text-gray-900">{{ $hospitalizacion->contacto_relacion ?? 'No registrada' }}</dd>
                </div>
            </dl>
        </div>
    </div>

    <!-- Triage Asignado -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Triage Asignado</h3>
        @if($triage)
            <div class="flex items-center gap-4 p-4 bg-{{ $triage->color }}-50 rounded-xl border border-{{ $triage->color }}-200">
                <div class="w-12 h-12 bg-{{ $triage->color }}-500 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="font-bold text-{{ $triage->color }}-800">{{ $triage->descripcion }}</p>
                    <p class="text-sm text-{{ $triage->color }}-600">Prioridad: {{ ucfirst($triage->prioridad) }}</p>
                    <p class="text-xs text-gray-500 mt-1">Código: {{ $triage->id }}</p>
                </div>
            </div>
        @else
            <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-xl border border-gray-200">
                <div class="w-12 h-12 bg-gray-400 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="font-bold text-gray-700">No asignado</p>
                    <p class="text-sm text-gray-500">El triage no ha sido asignado aún</p>
                </div>
            </div>
        @endif
    </div>

    <!-- Información de Registro -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Información de Registro</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="flex justify-between md:block">
                <dt class="text-sm text-gray-500 md:mb-1">Código Registro:</dt>
                <dd class="text-sm font-medium text-gray-900">{{ $hospitalizacion->paciente->registro_codigo }}</dd>
            </div>
            <div class="flex justify-between md:block">
                <dt class="text-sm text-gray-500 md:mb-1">Fecha Registro:</dt>
                <dd class="text-sm font-medium text-gray-900">{{ $hospitalizacion->paciente->registro->fecha?->format('d/m/Y H:i') ?? '-' }}</dd>
            </div>
            <div class="flex justify-between md:block">
                <dt class="text-sm text-gray-500 md:mb-1">Seguro:</dt>
                <dd class="text-sm font-medium text-gray-900">{{ $hospitalizacion->paciente->seguro->nombre_empresa ?? 'Particular' }}</dd>
            </div>
        </div>
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
                    <strong>Nº: <span style="margin-left: 20pt;">{{ $hospitalizacion->paciente->registro_codigo ?? $hospitalizacion->id }}</span></strong>
                </div>
                <div class="notice-box">
                    <p><strong>ESTIMADO PACIENTE:</strong></p>
                    <p>SI DESEA GUARDAR SUS OBJETOS DE VALOR (JOYAS, DINERO, CELULAR U OTROS). UD.</p>
                    <p>PUEDE HACERLO EN LA ADMINISTRACIÓN</p>
                    <p><strong>LA CLINICA MEDICA CEMSA LTDA. NO SE RESPONSABILIZARÁ EN CASO DE PÉRDIDAS.</strong></p>
                </div>
            </div>
        </div>

        <h3 class="main-title">REGISTRO DE ADMISION Y CONTRATO DE SERVICIOS</h3>

        <!-- DATOS DEL PACIENTE -->
        <div class="form-section">
            <div class="f-row">
                <div class="f-field" style="flex: 2.5;">
                    <span class="f-label">Paciente - Sr. (a):</span>
                    <span class="f-value font-bold">{{ strtoupper($hospitalizacion->paciente->nombre ?? '') }}</span>
                </div>
                <div class="f-field" style="flex: 1;">
                    <span class="f-label">F. N.</span>
                    <span class="f-value text-center font-bold">{{ $hospitalizacion->paciente->fecha_nacimiento ? \Carbon\Carbon::parse($hospitalizacion->paciente->fecha_nacimiento)->format('Y-m-d') : '' }}</span>
                </div>
                <div class="f-field" style="flex: 1.2;">
                    <span class="f-label">Estado Civil</span>
                    <span class="f-value font-bold">{{ strtoupper($hospitalizacion->paciente->estado_civil ?? '') }}</span>
                </div>
            </div>

            <div class="f-row">
                <div class="f-field" style="flex: 1.5;">
                    <span class="f-label">Nacionalidad</span>
                    <span class="f-value font-bold">{{ strtoupper($hospitalizacion->paciente->nacionalidad ?? '') }}</span>
                </div>
                <div class="f-field" style="flex: 1;">
                    <span class="f-label">Lugar</span>
                    <span class="f-value text-center font-bold">{{ strtoupper($hospitalizacion->paciente->lugar_expedicion ?? '') }}</span>
                </div>
                <div class="f-field" style="flex: 1.5;">
                    <span class="f-label">Teléfono Nº</span>
                    <span class="f-value font-bold">{{ $hospitalizacion->paciente->telefono ?? '' }}</span>
                </div>
            </div>

            <div class="f-row">
                <div class="f-field" style="flex: 1.5;">
                    <span class="f-label">C.I. Nº</span>
                    <span class="f-value font-bold">{{ $hospitalizacion->paciente->ci ?? '' }}</span>
                </div>
                <div class="f-field" style="flex: 1;">
                    <span class="f-label">Sexo</span>
                    <span class="f-value font-bold">{{ ($hospitalizacion->paciente->sexo ?? '') === 'M' ? 'MASCULINO' : (($hospitalizacion->paciente->sexo ?? '') === 'F' ? 'FEMENINO' : '') }}</span>
                </div>
                <div class="f-field" style="flex: 1.5;">
                    <span class="f-label">Hora de Ingreso</span>
                    <span class="f-value font-bold">{{ $hospitalizacion->fecha_ingreso ? $hospitalizacion->fecha_ingreso->format('H:i') : '' }}</span>
                </div>
            </div>

            <div class="f-row">
                <div class="f-field w-100">
                    <span class="f-label">Dirección</span>
                    <span class="f-value font-bold">{{ strtoupper($hospitalizacion->paciente->direccion ?? '') }}</span>
                </div>
            </div>

            <div class="f-row">
                <div class="f-field" style="flex: 1.5;">
                    <span class="f-label">Profesión</span>
                    <span class="f-value font-bold">{{ strtoupper($hospitalizacion->paciente->profesion ?? '') }}</span>
                </div>
                <div class="f-field" style="flex: 2.5;">
                    <span class="f-label">Empresa</span>
                    <span class="f-value font-bold">{{ strtoupper($hospitalizacion->paciente->empresa_trabajo ?? '') }}</span>
                </div>
            </div>
        </div>

        <h4 class="section-title" style="margin-top: 10pt; font-size: 8.5pt;">GARANTE RESPONSABLE</h4>
        <div class="form-section">
            <div class="f-row">
                <div class="f-field" style="flex: 2.5;">
                    <span class="f-label">Señor (a):</span>
                    <span class="f-value font-bold">{{ strtoupper($hospitalizacion->paciente->garante->nombre ?? $hospitalizacion->contacto_nombre ?? '') }}</span>
                </div>
                <div class="f-field" style="flex: 1;">
                    <span class="f-label">F. N.</span>
                    <span class="f-value text-center font-bold">{{ ($hospitalizacion->paciente->garante && $hospitalizacion->paciente->garante->fecha_nacimiento) ? \Carbon\Carbon::parse($hospitalizacion->paciente->garante->fecha_nacimiento)->format('Y-m-d') : '' }}</span>
                </div>
                <div class="f-field" style="flex: 1.2;">
                    <span class="f-label">Estado Civil</span>
                    <span class="f-value font-bold">{{ strtoupper($hospitalizacion->paciente->garante->estado_civil ?? '') }}</span>
                </div>
            </div>

            <div class="f-row">
                <div class="f-field" style="flex: 1.5;">
                    <span class="f-label">Nacionalidad</span>
                    <span class="f-value font-bold">{{ strtoupper($hospitalizacion->paciente->garante->nacionalidad ?? '') }}</span>
                </div>
                <div class="f-field" style="flex: 1;">
                    <span class="f-label">Lugar</span>
                    <span class="f-value text-center font-bold">{{ strtoupper($hospitalizacion->paciente->garante->lugar_expedicion ?? '') }}</span>
                </div>
                <div class="f-field" style="flex: 1.5;">
                    <span class="f-label">Teléfono Nº</span>
                    <span class="f-value font-bold">{{ $hospitalizacion->paciente->garante->telefono ?? $hospitalizacion->contacto_telefono ?? '' }}</span>
                </div>
            </div>

            <div class="f-row">
                <div class="f-field" style="flex: 1.5;">
                    <span class="f-label">C.I. Nº</span>
                    <span class="f-value font-bold">{{ $hospitalizacion->paciente->garante->ci ?? '' }}</span>
                </div>
                <div class="f-field" style="flex: 2.5;">
                    <span class="f-label">Profesión</span>
                    <span class="f-value font-bold">{{ strtoupper($hospitalizacion->paciente->garante->profesion ?? '') }}</span>
                </div>
            </div>

            <div class="f-row">
                <div class="f-field w-100">
                    <span class="f-label">Dirección</span>
                    <span class="f-value font-bold">{{ strtoupper($hospitalizacion->paciente->garante->direccion ?? '') }}</span>
                </div>
            </div>
            
            <div class="f-row">
                <div class="f-field w-100">
                    <span class="f-label">Empresa</span>
                    <span class="f-value font-bold">{{ strtoupper($hospitalizacion->paciente->garante->empresa_trabajo ?? '') }}</span>
                </div>
            </div>
        </div>

        <div class="form-section" style="margin-top: 15pt;">
            <div class="f-row" style="margin-bottom: 12pt;">
                <div class="f-field" style="flex: 3;">
                    <span class="f-label">CAUSA O MOTIVO DE LA INTERNACION:</span>
                    <span class="f-value font-bold">{{ strtoupper($hospitalizacion->motivo ?? '') }}</span>
                </div>
                <div class="f-field" style="flex: 1;">
                    <span class="f-label">Cirugía ( &nbsp; )</span>
                </div>
                <div class="f-field" style="flex: 1;">
                    <span class="f-label">Tratamiento ( &nbsp; )</span>
                </div>
            </div>

            <div class="f-row" style="margin-bottom: 12pt;">
                <div class="f-field w-100">
                    <span class="f-label">MEDICO QUE DISPONE LA INTERNACION Dr.</span>
                    <span class="f-value font-bold text-center">{{ strtoupper($hospitalizacion->medico->user->name ?? '') }}</span>
                </div>
            </div>

            <div class="f-row" style="margin-bottom: 12pt;">
                <div class="f-field w-100">
                    <span class="f-label">DIAGNOSTICO PRELIMINAR</span>
                    <span class="f-value font-bold text-center">{{ strtoupper($hospitalizacion->diagnostico ?? '') }}</span>
                </div>
            </div>

            <div class="f-row" style="margin-bottom: 12pt;">
                <div class="f-field w-100">
                    <span class="f-label">MEDICO QUE ADMITE AL PACIENTE Dr.</span>
                    <span class="f-value font-bold text-center">{{ strtoupper($hospitalizacion->medico->user->name ?? '') }}</span>
                </div>
            </div>
            
            <div class="f-row" style="margin-bottom: 12pt;">
                <div class="f-field" style="flex: 1;">
                    <span class="f-label">HABITACION ASIGNADA Nº</span>
                    <span class="f-value font-bold text-center">{{ strtoupper($hospitalizacion->habitacion ?? '') }}</span>
                </div>
                <div class="f-field" style="flex: 2;"></div>
            </div>
        </div>

        <h4 class="section-title text-center" style="margin-top: 15pt; text-align: center;">ACEPTACION</h4>
        <div style="font-size: 8pt; text-align: justify; margin-top: 5pt; line-height: 1.3;">
            <strong>NOTA:</strong> Las condiciones del contrato de prestación de servicios, aparecen en el <strong>REVERSO</strong> y los otorgantes declaran haberlas leído atentamente y en señal de conformidad con las mismas, suscriben este documento en doble ejemplar de un mismo tenor y para un solo efecto.
        </div>

        <div style="margin-top: 15pt; display: flex; font-size: 8.5pt;">
            <div style="margin-right: 10pt;">Santa Cruz,</div>
            <div class="inline-val" style="width: 150px;">{{ \Carbon\Carbon::now()->format('d \d\e F \d\e Y') }}</div>
        </div>

        <div class="signatures">
            <div class="sig-box">
                <div class="sig-line"></div>
                PACIENTE
            </div>
            <div class="sig-box">
                <div class="sig-line"></div>
                GARANTE RESPONSABLE
            </div>
            <div class="sig-box stamp-area">
                <div class="sig-line"></div>
                RECEPCIÓN<br>
                Clínica Médica CEMSA Ltda.
            </div>
        </div>

        <h4 class="section-title text-center" style="margin-top: 25pt; text-align: center; font-size: 9pt;">ACTA DE RECONOCIMIENTO DE FIRMAS</h4>
        
        <div style="font-size: 8pt; margin-top: 10pt; line-height: 1.8;">
            <div style="display: flex; justify-content: space-between;">
                <div>En esta Ciudad de Santa Cruz, a horas <span class="inline-val" style="width: 50px;">{{ \Carbon\Carbon::now()->format('H:i') }}</span> del día <span class="inline-val" style="width: 100px;">{{ \Carbon\Carbon::now()->format('d \d\e F') }}</span> de dos</div>
            </div>
            <div style="display: flex; justify-content: space-between;">
                <div>mil <span class="inline-val" style="width: 80px;">{{ \Carbon\Carbon::now()->format('Y') }}</span> ante el suscrito Juez de Mínima</div>
            </div>
            <div style="display: flex; justify-content: space-between;">
                <div>Cuantía Nº <span class="inline-val" style="width: 100px;"></span> de la Capital comparecieron voluntariamente los señores:</div>
            </div>
            <div style="display: flex; justify-content: space-between;">
                <div style="width: 45%;"><span class="inline-val" style="width: 100%;">{{ strtoupper($hospitalizacion->paciente->nombre ?? '') }}</span></div>
                <div style="width: 25%;">con C.I.Nº <span class="inline-val" style="width: 100px;">{{ $hospitalizacion->paciente->ci ?? '' }}</span></div>
                <div style="width: 25%;">y <span class="inline-val" style="width: 100%;"></span></div>
            </div>
            <div style="display: flex; justify-content: space-between;">
                <div style="width: 45%;"><span class="inline-val" style="width: 100%;">{{ strtoupper($hospitalizacion->paciente->garante->nombre ?? $hospitalizacion->contacto_nombre ?? '') }}</span></div>
                <div style="width: 25%;">con C.I.Nº <span class="inline-val" style="width: 100px;">{{ $hospitalizacion->paciente->garante->ci ?? '' }}</span></div>
                <div style="width: 25%;">y <span class="inline-val" style="width: 100%;"></span></div>
            </div>
            <div style="display: flex; justify-content: space-between;">
                <div style="width: 45%;"><span class="inline-val" style="width: 100%;"></span></div>
                <div style="width: 25%;">con C.I.Nº <span class="inline-val" style="width: 100px;"></span></div>
                <div style="width: 25%;">a reconocer sus</div>
            </div>
            <div>
                firmas que tienen estampadas al pie del documento anterior, Juramentos en legal forma y puéstoles de manifiesto sus firmas, los comparecientes expresaron ser las suyas y las reconocen como auténticas a los efectos de Ley, en constancia firman con el suscrito Juez y testigo de Actuación que certifica.
            </div>
        </div>

        <div class="signatures" style="margin-top: 30pt;">
            <div class="sig-box">
                <div class="sig-line"></div>
                PACIENTE
            </div>
            <div class="sig-box">
                <div class="sig-line"></div>
                GARANTE RESPONSABLE
            </div>
            <div class="sig-box stamp-area">
                <div class="sig-line"></div>
                RECEPCIÓN<br>
                Clínica Médica CEMSA Ltda.
            </div>
        </div>

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
            <title>Imprimir Comprobante Hospitalización CEMSA</title>
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
                    border-bottom: 1.5px dotted #000;
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
                    color: #4a5568;
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
