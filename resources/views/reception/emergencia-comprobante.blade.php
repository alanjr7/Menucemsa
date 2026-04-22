@extends('layouts.app')

@section('content')
<!-- VISTA EN PANTALLA -->
<div class="screen-only w-full p-6 bg-gray-50/50 min-h-screen">

    <!-- Page Header -->
    <div class="flex justify-between items-end mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Comprobante de Emergencia</h1>
            <p class="text-sm text-gray-500">Ficha de ingreso a emergencias</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('reception.emergencia') }}" class="flex items-center px-4 py-2 border border-gray-200 rounded-lg text-gray-600 bg-white hover:bg-gray-50 font-medium transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Volver a Emergencia
            </a>
            <button onclick="window.print()" class="flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Imprimir Ficha
            </button>
        </div>
    </div>

    <!-- Código de Emergencia -->
    <div class="bg-red-600 text-white rounded-t-2xl p-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
                <div>
                    <p class="text-sm opacity-90">Código de Emergencia</p>
                    <p class="text-xl font-bold">{{ $emergencia->code }}</p>
                </div>
            </div>
            <div class="text-right">
                <p class="text-sm opacity-90">Estado</p>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-400 text-yellow-900">
                    <span class="w-2 h-2 bg-yellow-700 rounded-full mr-2 animate-pulse"></span>
                    {{ $emergencia->status }}
                </span>
            </div>
        </div>
    </div>

    <!-- Patient Info Card -->
    <div class="bg-white rounded-b-2xl shadow-sm border border-gray-100 p-6 mb-6">
        <div class="flex items-center gap-4 mb-6">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center">
                <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <div>
                <h2 class="text-xl font-bold text-gray-800">{{ $emergencia->paciente->nombre ?? 'Paciente Temporal' }}</h2>
                <p class="text-sm text-gray-500">
                    @if($emergencia->is_temp_id)
                        ID Temporal: {{ $emergencia->temp_id }}
                    @else
                        CI: {{ $emergencia->paciente->ci }}
                    @endif
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-3">Información del Paciente</h3>
                <dl class="space-y-2">
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">Nombre:</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $emergencia->paciente->nombre ?? 'No registrado' }}</dd>
                    </div>
                    @if(!$emergencia->is_temp_id)
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">CI:</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $emergencia->paciente->ci }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">Teléfono:</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $emergencia->paciente->telefono ?? 'No registrado' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">Sexo:</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $emergencia->paciente->sexo ?? 'N/A' }}</dd>
                    </div>
                    @endif
                </dl>
            </div>

            <div>
                <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-3">Detalles de Ingreso</h3>
                <dl class="space-y-2">
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">Fecha de Ingreso:</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $emergencia->admission_date?->format('d/m/Y H:i') ?? $emergencia->created_at->format('d/m/Y H:i') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">Tipo de Ingreso:</dt>
                        <dd class="text-sm font-medium text-gray-900 uppercase">{{ $emergencia->tipo_ingreso }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">Destino Inicial:</dt>
                        <dd class="text-sm font-medium text-gray-900 uppercase">{{ str_replace('_', ' ', $emergencia->destino_inicial) }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-500">Registrado por:</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $emergencia->user->name ?? 'N/A' }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>

    <!-- Síntomas y Evaluación -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Síntomas y Evaluación Inicial
        </h3>
        <div class="space-y-4">
            <div class="bg-gray-50 rounded-xl p-4">
                <p class="text-sm font-medium text-gray-700 mb-1">Descripción / Síntomas:</p>
                <p class="text-gray-900">{{ $emergencia->symptoms ?? 'No registrado' }}</p>
            </div>
            @if($emergencia->initial_assessment)
            <div class="bg-gray-50 rounded-xl p-4">
                <p class="text-sm font-medium text-gray-700 mb-1">Evaluación Inicial:</p>
                <p class="text-gray-900">{{ $emergencia->initial_assessment }}</p>
            </div>
            @endif
            @if($emergencia->observations)
            <div class="bg-gray-50 rounded-xl p-4">
                <p class="text-sm font-medium text-gray-700 mb-1">Observaciones:</p>
                <p class="text-gray-900 whitespace-pre-line">{{ $emergencia->observations }}</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Signos Vitales -->
    @if(!empty($vitalSigns))
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Signos Vitales</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @if(!empty($vitalSigns['presion_arterial']))
            <div class="bg-blue-50 rounded-xl p-4 text-center">
                <p class="text-xs text-gray-500 uppercase">Presión Arterial</p>
                <p class="text-lg font-bold text-blue-700">{{ $vitalSigns['presion_arterial'] }}</p>
            </div>
            @endif
            @if(!empty($vitalSigns['frecuencia_cardiaca']))
            <div class="bg-red-50 rounded-xl p-4 text-center">
                <p class="text-xs text-gray-500 uppercase">Frec. Cardiaca</p>
                <p class="text-lg font-bold text-red-700">{{ $vitalSigns['frecuencia_cardiaca'] }}</p>
            </div>
            @endif
            @if(!empty($vitalSigns['frecuencia_respiratoria']))
            <div class="bg-green-50 rounded-xl p-4 text-center">
                <p class="text-xs text-gray-500 uppercase">Frec. Respiratoria</p>
                <p class="text-lg font-bold text-green-700">{{ $vitalSigns['frecuencia_respiratoria'] }}</p>
            </div>
            @endif
            @if(!empty($vitalSigns['temperatura']))
            <div class="bg-yellow-50 rounded-xl p-4 text-center">
                <p class="text-xs text-gray-500 uppercase">Temperatura</p>
                <p class="text-lg font-bold text-yellow-700">{{ $vitalSigns['temperatura'] }}</p>
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Información Adicional -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">Información Adicional</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="flex justify-between md:block">
                <dt class="text-sm text-gray-500 md:mb-1">Ubicación Actual:</dt>
                <dd class="text-sm font-medium text-gray-900 uppercase">{{ str_replace('_', ' ', $emergencia->ubicacion_actual) }}</dd>
            </div>
            <div class="flex justify-between md:block">
                <dt class="text-sm text-gray-500 md:mb-1">Es Parto:</dt>
                <dd class="text-sm font-medium text-gray-900">{{ $emergencia->es_parto ? 'Sí' : 'No' }}</dd>
            </div>
        </div>
    </div>

</div>

<!-- FORMATO DE IMPRESION - Solo visible al imprimir -->
<div class="print-only" style="display: none;">
    <!-- Encabezado -->
    <div class="print-header">
        <h1>CEMSA - COMPROBANTE DE EMERGENCIA</h1>
        <p>Código: {{ $emergencia->code }}</p>
    </div>

    <!-- Línea separadora -->
    <div class="print-line"></div>

    <!-- Datos del Paciente -->
    <div class="print-section">
        <h2>DATOS DEL PACIENTE</h2>
        <table class="print-table">
            <tr>
                <td><strong>Nombre:</strong> {{ $emergencia->paciente->nombre ?? 'Paciente Temporal' }}</td>
                <td>
                    <strong>
                        @if($emergencia->is_temp_id)
                            ID Temporal:
                        @else
                            CI:
                        @endif
                    </strong> 
                    {{ $emergencia->is_temp_id ? $emergencia->temp_id : ($emergencia->paciente->ci ?? 'N/A') }}
                </td>
            </tr>
            @if(!$emergencia->is_temp_id)
            <tr>
                <td><strong>Teléfono:</strong> {{ $emergencia->paciente->telefono ?? 'No registrado' }}</td>
                <td><strong>Sexo:</strong> {{ $emergencia->paciente->sexo ?? 'N/A' }}</td>
            </tr>
            @endif
        </table>
    </div>

    <!-- Detalles de Ingreso -->
    <div class="print-section">
        <h2>DETALLES DE INGRESO</h2>
        <table class="print-table">
            <tr>
                <td><strong>Fecha de Ingreso:</strong> {{ $emergencia->admission_date?->format('d/m/Y H:i') ?? $emergencia->created_at->format('d/m/Y H:i') }}</td>
                <td><strong>Tipo:</strong> {{ strtoupper($emergencia->tipo_ingreso) }}</td>
            </tr>
            <tr>
                <td><strong>Destino Inicial:</strong> {{ strtoupper(str_replace('_', ' ', $emergencia->destino_inicial)) }}</td>
                <td><strong>Estado:</strong> {{ strtoupper($emergencia->status) }}</td>
            </tr>
            <tr>
                <td colspan="2"><strong>Registrado por:</strong> {{ $emergencia->user->name ?? 'N/A' }}</td>
            </tr>
        </table>
    </div>

    <!-- Síntomas -->
    <div class="print-section">
        <h2>SÍNTOMAS Y EVALUACIÓN</h2>
        <table class="print-table">
            <tr>
                <td colspan="2"><strong>Descripción / Síntomas:</strong><br>{{ $emergencia->symptoms ?? 'No registrado' }}</td>
            </tr>
            @if($emergencia->initial_assessment)
            <tr>
                <td colspan="2"><strong>Evaluación Inicial:</strong><br>{{ $emergencia->initial_assessment }}</td>
            </tr>
            @endif
            @if($emergencia->observations)
            <tr>
                <td colspan="2"><strong>Observaciones:</strong><br>{{ $emergencia->observations }}</td>
            </tr>
            @endif
        </table>
    </div>

    <!-- Signos Vitales -->
    @if(!empty($vitalSigns))
    <div class="print-section">
        <h2>SIGNOS VITALES</h2>
        <table class="print-table">
            <tr>
                @if(!empty($vitalSigns['presion_arterial']))
                <td><strong>Presión Arterial:</strong> {{ $vitalSigns['presion_arterial'] }}</td>
                @endif
                @if(!empty($vitalSigns['frecuencia_cardiaca']))
                <td><strong>Frec. Cardiaca:</strong> {{ $vitalSigns['frecuencia_cardiaca'] }}</td>
                @endif
            </tr>
            <tr>
                @if(!empty($vitalSigns['frecuencia_respiratoria']))
                <td><strong>Frec. Respiratoria:</strong> {{ $vitalSigns['frecuencia_respiratoria'] }}</td>
                @endif
                @if(!empty($vitalSigns['temperatura']))
                <td><strong>Temperatura:</strong> {{ $vitalSigns['temperatura'] }}</td>
                @endif
            </tr>
        </table>
    </div>
    @endif

    <!-- Instrucciones -->
    <div class="print-section">
        <h2>INSTRUCCIONES</h2>
        <div class="print-instructions">
            <p><strong>1.</strong> Este comprobante debe presentarse en el área de emergencias.</p>
            <p><strong>2.</strong> El paciente será evaluado por el médico de turno según prioridad.</p>
            <p><strong>3.</strong> Conserve este documento hasta finalizar la atención.</p>
            <p><strong>4.</strong> Para pacientes con ID temporal: complete los datos personales lo antes posible.</p>
        </div>
    </div>

    <!-- Firma -->
    <div class="print-signature">
        <p>_______________________________</p>
        <p>Firma del Paciente / Responsable</p>
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

    /* Instrucciones */
    .print-instructions {
        font-size: 10pt;
        line-height: 1.6;
        padding: 8pt;
        border: 0.5pt solid #999;
        background-color: #fafafa;
    }

    .print-instructions p {
        margin: 4pt 0;
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
