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
                                    <span class="text-sm font-medium">{{ $caja->consulta->medico->user->name ?? 'N/A' }}</span>
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

<!-- FORMATO DE IMPRESION - Solo visible al imprimir -->
<div class="print-only" style="display: none;">
    <!-- Encabezado -->
    <div class="print-header">
        <h1>CEMSA - COMPROBANTE DE CONSULTA EXTERNA</h1>
        <p>Código: {{ $caja->id }}</p>
    </div>

    <!-- Línea separadora -->
    <div class="print-line"></div>

    @if($caja->consulta)
    <!-- Datos del Paciente -->
    <div class="print-section">
        <h2>DATOS DEL PACIENTE</h2>
        <table class="print-table">
            <tr>
                <td><strong>Nombre:</strong> {{ $caja->consulta->paciente->nombre ?? 'N/A' }}</td>
                <td><strong>CI:</strong> {{ $caja->consulta->paciente->ci ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td><strong>Teléfono:</strong> {{ $caja->consulta->paciente->telefono ?? 'No registrado' }}</td>
                <td><strong>Sexo:</strong> {{ $caja->consulta->paciente->sexo ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td colspan="2"><strong>Dirección:</strong> {{ $caja->consulta->paciente->direccion ?? 'No registrada' }}</td>
            </tr>
        </table>
    </div>

    <!-- Detalles de la Consulta -->
    <div class="print-section">
        <h2>DETALLES DE LA CONSULTA</h2>
        <table class="print-table">
            <tr>
                <td><strong>Médico:</strong> Dr. {{ $caja->consulta->medico->user->name ?? 'No asignado' }}</td>
                <td><strong>Especialidad:</strong> {{ $caja->consulta->especialidad->nombre ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td><strong>Fecha:</strong> {{ \Carbon\Carbon::parse($caja->consulta->fecha)->format('d/m/Y') ?? 'N/A' }}</td>
                <td><strong>Hora:</strong> {{ $caja->consulta->hora ?? 'N/A' }}</td>
            </tr>
        </table>
    </div>

    <!-- Información de Pago -->
    <div class="print-section">
        <h2>INFORMACIÓN DE PAGO</h2>
        <table class="print-table">
            <tr>
                <td><strong>Monto a Pagar:</strong> S/. {{ number_format($caja->monto_pagado, 2) }}</td>
                <td><strong>Estado:</strong> PENDIENTE DE PAGO</td>
            </tr>
            <tr>
                <td><strong>Tipo:</strong> {{ $caja->tipo }}</td>
                <td><strong>Impreso:</strong> {{ now()->format('d/m/Y H:i') }}</td>
            </tr>
        </table>
    </div>

    <!-- Instrucciones -->
    <div class="print-section">
        <h2>INSTRUCCIONES</h2>
        <div class="print-instructions">
            <p><strong>1.</strong> El paciente debe pasar a módulo de caja para realizar el pago.</p>
            <p><strong>2.</strong> Una vez pagado, el paciente aparecerá en el dashboard del médico.</p>
            <p><strong>3.</strong> Conserve este comprobante hasta ser atendido.</p>
        </div>
    </div>
    @endif

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
    #app nav, .bg-gray-50, .bg-gray-100, .shadow-lg, .shadow-sm {
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
