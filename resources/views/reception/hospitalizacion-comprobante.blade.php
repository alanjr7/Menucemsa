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
            <a href="{{ route('reception.hospitalizacion') }}" class="flex items-center px-4 py-2 border border-gray-200 rounded-lg text-gray-600 bg-white hover:bg-gray-50 font-medium transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Volver a Hospitalización
            </a>
            <button onclick="window.print()" class="flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium transition-colors">
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

<!-- FORMATO DE IMPRESION - Solo visible al imprimir -->
<div class="print-only" style="display: none;">
    <!-- Encabezado -->
    <div class="print-header">
        <h1>CEMSA - COMPROBANTE DE HOSPITALIZACIÓN</h1>
        <p>Código: {{ $hospitalizacion->id }}</p>
    </div>

    <!-- Línea separadora -->
    <div class="print-line"></div>

    <!-- Datos del Paciente -->
    <div class="print-section">
        <h2>DATOS DEL PACIENTE</h2>
        <table class="print-table">
            <tr>
                <td><strong>Nombre:</strong> {{ $hospitalizacion->paciente->nombre }}</td>
                <td><strong>CI:</strong> {{ $hospitalizacion->paciente->ci }}</td>
            </tr>
            <tr>
                <td><strong>Teléfono:</strong> {{ $hospitalizacion->paciente->telefono ?? 'No registrado' }}</td>
                <td><strong>Sexo:</strong> {{ $hospitalizacion->paciente->sexo }}</td>
            </tr>
            <tr>
                <td colspan="2"><strong>Dirección:</strong> {{ $hospitalizacion->paciente->direccion ?? 'No registrada' }}</td>
            </tr>
        </table>
    </div>

    <!-- Datos de Ingreso -->
    <div class="print-section">
        <h2>INFORMACIÓN DE INGRESO</h2>
        <table class="print-table">
            <tr>
                <td><strong>Fecha de Ingreso:</strong> {{ $hospitalizacion->fecha_ingreso->format('d/m/Y H:i') }}</td>
                <td><strong>Médico Tratante:</strong> Dr. {{ $hospitalizacion->medico->user->name ?? 'No asignado' }}</td>
            </tr>
            <tr>
                <td colspan="2"><strong>Motivo:</strong> {{ $hospitalizacion->motivo }}</td>
            </tr>
            <tr>
                <td colspan="2"><strong>Diagnóstico Presuntivo:</strong> {{ $hospitalizacion->diagnostico }}</td>
            </tr>
        </table>
    </div>

    <!-- Contacto de Emergencia -->
    <div class="print-section">
        <h2>CONTACTO DE EMERGENCIA</h2>
        <table class="print-table">
            <tr>
                <td><strong>Nombre:</strong> {{ $hospitalizacion->contacto_nombre ?? 'No registrado' }}</td>
                <td><strong>Teléfono:</strong> {{ $hospitalizacion->contacto_telefono ?? 'No registrado' }}</td>
            </tr>
            <tr>
                <td><strong>Parentesco:</strong> {{ $hospitalizacion->contacto_parentesco ?? 'No registrado' }}</td>
                <td><strong>Relación:</strong> {{ $hospitalizacion->contacto_relacion ?? 'No registrada' }}</td>
            </tr>
        </table>
    </div>

    <!-- Triage -->
    <div class="print-section">
        <h2>TRIAGE ASIGNADO</h2>
        <table class="print-table">
            <tr>
                <td style="width: 30%;"><strong>Tipo:</strong> {{ $triage ? strtoupper($triage->color) : 'NO ASIGNADO' }}</td>
                <td><strong>Descripción:</strong> {{ $triage->descripcion ?? 'No asignado' }}</td>
            </tr>
            <tr>
                <td><strong>Prioridad:</strong> {{ $triage ? ucfirst($triage->prioridad) : '-' }}</td>
                <td><strong>Código Triage:</strong> {{ $triage->id ?? '-' }}</td>
            </tr>
        </table>
    </div>

    <!-- Registro -->
    <div class="print-section">
        <h2>INFORMACIÓN DE REGISTRO</h2>
        <table class="print-table">
            <tr>
                <td><strong>Código Registro:</strong> {{ $hospitalizacion->paciente->registro_codigo }}</td>
                <td><strong>Fecha:</strong> {{ $hospitalizacion->paciente->registro->fecha?->format('d/m/Y H:i') ?? '-' }}</td>
            </tr>
            <tr>
                <td><strong>Seguro:</strong> {{ $hospitalizacion->paciente->seguro->nombre_empresa ?? 'Particular' }}</td>
                <td><strong>Impreso:</strong> {{ now()->format('d/m/Y H:i') }}</td>
            </tr>
        </table>
    </div>

    <!-- Firma -->
    <div class="print-signature">
        <p>_______________________________</p>
        <p>Firma del Paciente / Familiar Responsable</p>
    </div>

    <!-- Footer -->
    <div class="print-footer">
        <p>CEMSA - Centro Médico de Especialidades | Documento generado el {{ now()->format('d/m/Y H:i') }}</p>
    </div>
</div>

<style>
/* Estilos para pantalla */
.screen-only { display: block; }
.print-only { display: none; }

/* Estilos para impresión */
@media print {
    /* Ocultar elementos de la UI de la aplicación */
    nav, aside, header:not(.print-header), .screen-only,
    [class*="sidebar"], [class*="navbar"], [class*="menu"],
    #app nav, .bg-gray-50, .bg-gray-100, .shadow-sm {
        display: none !important;
    }
    
    /* Mostrar solo el contenido de impresión */
    .print-only {
        display: block !important;
        position: static !important;
    }
    
    /* Configuración de página */
    @page {
        size: letter; /* Hoja carta 8.5" x 11" */
        margin: 1.5cm 2cm;
    }
    
    /* Reset de estilos */
    body {
        font-family: Arial, Helvetica, sans-serif;
        font-size: 11pt;
        line-height: 1.4;
        color: #000;
        background: white;
        margin: 0;
        padding: 0;
    }
    
    * {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
    
    /* Encabezado */
    .print-header {
        text-align: center;
        margin-bottom: 15pt;
        border-bottom: 2pt solid #000;
        padding-bottom: 10pt;
    }
    
    .print-header h1 {
        font-size: 16pt;
        font-weight: bold;
        margin: 0 0 5pt 0;
        text-transform: uppercase;
    }
    
    .print-header p {
        font-size: 11pt;
        margin: 0;
        font-family: "Courier New", monospace;
    }
    
    /* Línea separadora */
    .print-line {
        border-bottom: 0.5pt solid #000;
        margin: 8pt 0;
    }
    
    /* Secciones */
    .print-section {
        margin-bottom: 12pt;
        page-break-inside: avoid;
    }
    
    .print-section h2 {
        font-size: 12pt;
        font-weight: bold;
        text-transform: uppercase;
        margin: 0 0 6pt 0;
        padding: 3pt 0;
        border-bottom: 0.5pt solid #333;
    }
    
    /* Tablas */
    .print-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 10.5pt;
    }
    
    .print-table td {
        padding: 4pt 6pt;
        vertical-align: top;
        border: 0.5pt solid #666;
    }
    
    .print-table tr:nth-child(even) {
        background-color: #f9f9f9;
    }
    
    /* Área de firma */
    .print-signature {
        margin-top: 40pt;
        text-align: center;
        page-break-inside: avoid;
    }
    
    .print-signature p {
        margin: 3pt 0;
        font-size: 10pt;
    }
    
    .print-signature p:first-child {
        font-size: 12pt;
        letter-spacing: 2pt;
    }
    
    /* Footer */
    .print-footer {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        text-align: center;
        font-size: 8pt;
        color: #555;
        border-top: 0.5pt solid #999;
        padding-top: 5pt;
        margin-top: 15pt;
    }
    
    /* Evitar saltos de página en secciones importantes */
    .print-section, .print-signature {
        page-break-inside: avoid;
    }
    
    h2 {
        page-break-after: avoid;
    }
}
</style>
@endsection
