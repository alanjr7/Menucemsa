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
            
            <button onclick="imprimir()" class="flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium transition-colors">
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
                    <strong>Nº: <span style="margin-left: 20pt;">{{ ($emergencia->paciente && $emergencia->paciente->registro_codigo) ? $emergencia->paciente->registro_codigo : $emergencia->code }}</span></strong>
                </div>
                <div class="notice-box">
                    <p><strong>ESTIMADO PACIENTE:</strong></p>
                    <p>SI DESEA GUARDAR SUS OBJETOS DE VALOR (JOYAS, DINERO, CELULAR U OTROS). UD.</p>
                    <p>PUEDE HACERLO EN LA ADMINISTRACIÓN</p>
                    <p><strong>LA CLINICA MEDICA CEMSA LTDA. NO SE RESPONSABILIZARÁ EN CASO DE PÉRDIDAS.</strong></p>
                </div>
            </div>
        </div>

        <h3 class="main-title">COMPROBANTE DE INGRESO A EMERGENCIA</h3>

        <!-- DATOS DEL PACIENTE -->
        <div class="form-section">
            <div class="f-row">
                <div class="f-field" style="flex: 2.5;">
                    <span class="f-label">Paciente - Sr. (a):</span>
                    <span class="f-value font-bold">{{ strtoupper($emergencia->paciente->nombre ?? 'PACIENTE TEMPORAL') }}</span>
                </div>
                <div class="f-field" style="flex: 1;">
                    <span class="f-label">F. N.</span>
                    <span class="f-value text-center font-bold">{{ ($emergencia->paciente && $emergencia->paciente->fecha_nacimiento) ? \Carbon\Carbon::parse($emergencia->paciente->fecha_nacimiento)->format('Y-m-d') : '' }}</span>
                </div>
                <div class="f-field" style="flex: 1.2;">
                    <span class="f-label">Estado Civil</span>
                    <span class="f-value font-bold">{{ strtoupper($emergencia->paciente->estado_civil ?? '') }}</span>
                </div>
            </div>

            <div class="f-row">
                <div class="f-field" style="flex: 1.5;">
                    <span class="f-label">Nacionalidad</span>
                    <span class="f-value font-bold">{{ strtoupper($emergencia->paciente->nacionalidad ?? '') }}</span>
                </div>
                <div class="f-field" style="flex: 1;">
                    <span class="f-label">Lugar</span>
                    <span class="f-value text-center font-bold">{{ strtoupper($emergencia->paciente->lugar_expedicion ?? '') }}</span>
                </div>
                <div class="f-field" style="flex: 1.5;">
                    <span class="f-label">Teléfono Nº</span>
                    <span class="f-value font-bold">{{ $emergencia->paciente->telefono ?? '' }}</span>
                </div>
            </div>

            <div class="f-row">
                <div class="f-field" style="flex: 1.5;">
                    <span class="f-label">{{ $emergencia->is_temp_id ? 'ID Temp.' : 'C.I. Nº' }}</span>
                    <span class="f-value font-bold">{{ $emergencia->is_temp_id ? $emergencia->temp_id : ($emergencia->paciente->ci ?? '') }}</span>
                </div>
                <div class="f-field" style="flex: 1;">
                    <span class="f-label">Sexo</span>
                    <span class="f-value font-bold">{{ ($emergencia->paciente->sexo ?? '') === 'M' ? 'MASCULINO' : (($emergencia->paciente->sexo ?? '') === 'F' ? 'FEMENINO' : '') }}</span>
                </div>
                <div class="f-field" style="flex: 1.5;">
                    <span class="f-label">Correo</span>
                    <span class="f-value font-bold">{{ strtoupper($emergencia->paciente->correo ?? '') }}</span>
                </div>
            </div>

            <div class="f-row">
                <div class="f-field w-100">
                    <span class="f-label">Dirección</span>
                    <span class="f-value font-bold">{{ strtoupper($emergencia->paciente->direccion ?? '') }}</span>
                </div>
            </div>

            <div class="f-row">
                <div class="f-field" style="flex: 1.5;">
                    <span class="f-label">Profesión</span>
                    <span class="f-value font-bold">{{ strtoupper($emergencia->paciente->profesion ?? '') }}</span>
                </div>
                <div class="f-field" style="flex: 2.5;">
                    <span class="f-label">Empresa</span>
                    <span class="f-value font-bold">{{ strtoupper($emergencia->paciente->empresa_trabajo ?? '') }}</span>
                </div>
            </div>
            
            @if($emergencia->paciente && $emergencia->paciente->seguro)
            <div class="f-row">
                <div class="f-field w-100">
                    <span class="f-label">Seguro</span>
                    <span class="f-value font-bold">{{ strtoupper($emergencia->paciente->seguro->nombre ?? 'SIN SEGURO') }}</span>
                </div>
            </div>
            @endif
        </div>

        <!-- DATOS DEL INGRESO -->
        <h4 class="section-title" style="margin-top: 15pt;">DATOS DE EMERGENCIA</h4>
        <div class="form-section">
            <div class="f-row">
                <div class="f-field" style="flex: 2;">
                    <span class="f-label">Tipo de Ingreso:</span>
                    <span class="f-value font-bold">EMERGENCIA - {{ strtoupper($emergencia->tipo_ingreso ?? '') }}</span>
                </div>
                <div class="f-field" style="flex: 2;">
                    <span class="f-label">Código:</span>
                    <span class="f-value font-bold">{{ $emergencia->code ?? '' }}</span>
                </div>
            </div>

            <div class="f-row">
                <div class="f-field" style="flex: 1.5;">
                    <span class="f-label">Destino Inicial:</span>
                    <span class="f-value font-bold">{{ strtoupper(str_replace('_', ' ', $emergencia->destino_inicial ?? '')) }}</span>
                </div>
                <div class="f-field" style="flex: 1;">
                    <span class="f-label">Fecha:</span>
                    <span class="f-value font-bold">{{ ($emergencia->admission_date ?? $emergencia->created_at)->format('d/m/Y') }}</span>
                </div>
                <div class="f-field" style="flex: 0.8;">
                    <span class="f-label">Hora:</span>
                    <span class="f-value font-bold">{{ ($emergencia->admission_date ?? $emergencia->created_at)->format('H:i') }}</span>
                </div>
            </div>

            <div class="f-row">
                <div class="f-field" style="flex: 2;">
                    <span class="f-label">Ubicación Actual:</span>
                    <span class="f-value font-bold">{{ strtoupper(str_replace('_', ' ', $emergencia->ubicacion_actual ?? '')) }}</span>
                </div>
                <div class="f-field" style="flex: 2;">
                    <span class="f-label">Estado:</span>
                    <span class="f-value font-bold">{{ strtoupper($emergencia->status ?? '') }}</span>
                </div>
            </div>
            
            <div class="f-row">
                <div class="f-field" style="flex: 1;">
                    <span class="f-label">Es Parto:</span>
                    <span class="f-value font-bold">{{ $emergencia->es_parto ? 'SÍ' : 'NO' }}</span>
                </div>
                <div class="f-field" style="flex: 2;">
                    <span class="f-label">Registrado por:</span>
                    <span class="f-value font-bold">{{ strtoupper($emergencia->user->name ?? '') }}</span>
                </div>
            </div>

            <div class="f-row" style="margin-top: 10pt;">
                <div class="f-field w-100">
                    <span class="f-label">Síntomas:</span>
                    <span class="f-value font-bold">{{ strtoupper($emergencia->symptoms ?? 'NO REGISTRADO') }}</span>
                </div>
            </div>

            @if($emergencia->initial_assessment)
            <div class="f-row">
                <div class="f-field w-100">
                    <span class="f-label">Evaluación Inicial:</span>
                    <span class="f-value font-bold">{{ strtoupper($emergencia->initial_assessment) }}</span>
                </div>
            </div>
            @endif

            @if($emergencia->observations)
            <div class="f-row">
                <div class="f-field w-100">
                    <span class="f-label">Observaciones:</span>
                    <span class="f-value font-bold">{{ strtoupper($emergencia->observations) }}</span>
                </div>
            </div>
            @endif
        </div>
        
        @if(!empty($vitalSigns))
        <h4 class="section-title" style="margin-top: 15pt;">SIGNOS VITALES</h4>
        <div class="form-section">
            <div class="f-row">
                <div class="f-field" style="flex: 1;">
                    <span class="f-label">P.A.:</span>
                    <span class="f-value font-bold">{{ $vitalSigns['presion_arterial'] ?? '' }}</span>
                </div>
                <div class="f-field" style="flex: 1;">
                    <span class="f-label">F.C.:</span>
                    <span class="f-value font-bold">{{ $vitalSigns['frecuencia_cardiaca'] ?? '' }}</span>
                </div>
                <div class="f-field" style="flex: 1;">
                    <span class="f-label">F.R.:</span>
                    <span class="f-value font-bold">{{ $vitalSigns['frecuencia_respiratoria'] ?? '' }}</span>
                </div>
                <div class="f-field" style="flex: 1;">
                    <span class="f-label">Temp.:</span>
                    <span class="f-value font-bold">{{ $vitalSigns['temperatura'] ?? '' }}</span>
                </div>
            </div>
        </div>
        @endif

        <div style="margin-top: 30pt;">
            <p style="font-size: 8pt; margin-bottom: 2pt;"><strong>Instrucciones:</strong></p>
            <p style="font-size: 8pt; margin: 2px 0;">1. Este comprobante debe presentarse en el área de emergencias.</p>
            <p style="font-size: 8pt; margin: 2px 0;">2. El paciente será evaluado por el médico de turno según prioridad.</p>
            <p style="font-size: 8pt; margin: 2px 0;">3. Conserve este documento hasta finalizar la atención.</p>
            <p style="font-size: 8pt; margin: 2px 0;">4. Para pacientes con ID temporal: complete los datos personales lo antes posible.</p>
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
            <title>Imprimir Comprobante Emergencia CEMSA</title>
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
