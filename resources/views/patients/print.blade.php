<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Paciente - {{ $paciente->nombre }}</title>
    <style>
        @page {
            size: letter;
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

        /* Textos en línea */
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
            margin-top: 20pt;
            text-align: center;
        }

        .stamp-box {
            border: 2px solid #000;
            width: 200px;
            height: 80px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 8pt;
        }

    </style>
</head>
<body>


    <!-- FORMATO DE IMPRESIÓN -->
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
                    <strong>Nº: <span style="margin-left: 20pt;">{{ $paciente->registro_codigo ?? 'REG-' . date('ymd-His') }}</span></strong>
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

        <!-- DATOS DEL PACIENTE -->
        <div class="form-section">
            <div class="f-row">
                <div class="f-field" style="flex: 2.5;">
                    <span class="f-label">Paciente - Sr. (a):</span>
                    <span class="f-value font-bold">{{ strtoupper($paciente->nombre ?? '') }}</span>
                </div>
                <div class="f-field" style="flex: 1;">
                    <span class="f-label">F. N.</span>
                    <span class="f-value text-center font-bold">{{ $paciente->fecha_nacimiento ? \Carbon\Carbon::parse($paciente->fecha_nacimiento)->format('Y-m-d') : '' }}</span>
                </div>
                <div class="f-field" style="flex: 1.2;">
                    <span class="f-label">Estado Civil</span>
                    <span class="f-value font-bold">{{ strtoupper($paciente->estado_civil ?? 'SOLTERO(A)') }}</span>
                </div>
            </div>

            <div class="f-row">
                <div class="f-field" style="flex: 1.5;">
                    <span class="f-label">Nacionalidad</span>
                    <span class="f-value font-bold">{{ strtoupper($paciente->nacionalidad ?? 'BOLIVIANA') }}</span>
                </div>
                <div class="f-field" style="flex: 1;">
                    <span class="f-label">Lugar</span>
                    <span class="f-value text-center font-bold">{{ strtoupper($paciente->lugar_expedicion ?? 'SC') }}</span>
                </div>
                <div class="f-field" style="flex: 1.5;">
                    <span class="f-label">Teléfono Nº</span>
                    <span class="f-value font-bold">{{ $paciente->telefono ?? '' }}</span>
                </div>
            </div>

            <div class="f-row">
                <div class="f-field" style="flex: 1.5;">
                    <span class="f-label">C.I. Nº</span>
                    <span class="f-value font-bold">{{ $paciente->ci ?? '' }}</span>
                </div>
                <div class="f-field" style="flex: 1;">
                    <span class="f-label">Sexo</span>
                    <span class="f-value font-bold">{{ ($paciente->sexo ?? '') === 'M' ? 'MASCULINO' : (($paciente->sexo ?? '') === 'F' ? 'FEMENINO' : '') }}</span>
                </div>
                <div class="f-field" style="flex: 1.5;">
                    <span class="f-label">Correo</span>
                    <span class="f-value font-bold">{{ strtoupper($paciente->correo ?? '') }}</span>
                </div>
            </div>

            <div class="f-row">
                <div class="f-field w-100">
                    <span class="f-label">Dirección</span>
                    <span class="f-value font-bold">{{ strtoupper($paciente->direccion ?? '') }}</span>
                </div>
            </div>

            <div class="f-row">
                <div class="f-field" style="flex: 1.5;">
                    <span class="f-label">Profesión</span>
                    <span class="f-value font-bold">{{ strtoupper($paciente->profesion ?? '') }}</span>
                </div>
                <div class="f-field" style="flex: 2.5;">
                    <span class="f-label">Empresa</span>
                    <span class="f-value font-bold">{{ strtoupper($paciente->empresa_trabajo ?? '') }}</span>
                </div>
            </div>

            <div class="f-row">
                <div class="f-field w-100">
                    <span class="f-label">Seguro</span>
                    <span class="f-value font-bold">{{ strtoupper($paciente->seguro->nombre ?? 'SIN SEGURO') }}</span>
                </div>
            </div>
        </div>

        <!-- DATOS DEL GARANTE (SI EXISTE) -->
        @if($garante)
        <h4 class="section-title" style="margin-top: 15pt;">DATOS DEL GARANTE / RESPONSABLE</h4>
        <div class="form-section">
            <div class="f-row">
                <div class="f-field" style="flex: 2.5;">
                    <span class="f-label">Garante - Sr. (a):</span>
                    <span class="f-value font-bold">{{ strtoupper($garante->nombre ?? '') }}</span>
                </div>
                <div class="f-field" style="flex: 1;">
                    <span class="f-label">F. N.</span>
                    <span class="f-value text-center font-bold">{{ $garante->fecha_nacimiento ? \Carbon\Carbon::parse($garante->fecha_nacimiento)->format('Y-m-d') : '' }}</span>
                </div>
                <div class="f-field" style="flex: 1.2;">
                    <span class="f-label">Parentesco</span>
                    <span class="f-value font-bold">{{ strtoupper($paciente->registro->garante_parentesco ?? 'PADRE/MADRE') }}</span>
                </div>
            </div>

            <div class="f-row">
                <div class="f-field" style="flex: 1.5;">
                    <span class="f-label">C.I. Nº</span>
                    <span class="f-value font-bold">{{ $garante->ci ?? '' }}</span>
                </div>
                <div class="f-field" style="flex: 1;">
                    <span class="f-label">Sexo</span>
                    <span class="f-value font-bold">{{ ($garante->sexo ?? '') === 'M' ? 'MASCULINO' : (($garante->sexo ?? '') === 'F' ? 'FEMENINO' : '') }}</span>
                </div>
                <div class="f-field" style="flex: 1.5;">
                    <span class="f-label">Teléfono</span>
                    <span class="f-value font-bold">{{ $garante->telefono ?? '' }}</span>
                </div>
            </div>

            <div class="f-row">
                <div class="f-field w-100">
                    <span class="f-label">Dirección Garante</span>
                    <span class="f-value font-bold">{{ strtoupper($garante->direccion ?? '') }}</span>
                </div>
            </div>
        </div>
        @endif

        <!-- DATOS DEL INGRESO -->
        <h4 class="section-title" style="margin-top: 15pt;">DATOS DEL INGRESO</h4>
        <div class="form-section">
            <div class="f-row">
                <div class="f-field" style="flex: 2;">
                    <span class="f-label">Código Registro:</span>
                    <span class="f-value font-bold">{{ $paciente->registro_codigo ?? '' }}</span>
                </div>
                <div class="f-field" style="flex: 2;">
                    <span class="f-label">Fecha Registro:</span>
                    <span class="f-value font-bold">{{ $paciente->registro ? \Carbon\Carbon::parse($paciente->registro->fecha)->format('d/m/Y') : '' }}</span>
                </div>
            </div>

            <div class="f-row">
                <div class="f-field" style="flex: 1.5;">
                    <span class="f-label">Registrado por:</span>
                    <span class="f-value font-bold">{{ strtoupper($paciente->registro->user->name ?? '') }}</span>
                </div>
                <div class="f-field" style="flex: 1;">
                    <span class="f-label">Hora:</span>
                    <span class="f-value font-bold">{{ $paciente->registro ? \Carbon\Carbon::parse($paciente->registro->hora)->format('H:i') : '' }}</span>
                </div>
                <div class="f-field" style="flex: 1.5;">
                    <span class="f-label">Motivo Ingreso:</span>
                    <span class="f-value font-bold">{{ strtoupper($paciente->registro->motivo ?? 'CONSULTA GENERAL') }}</span>
                </div>
            </div>
        </div>

        <!-- HISTORIAL DE INGRESOS -->
        @if($paciente->consultas->count() > 0 || $paciente->emergencias->count() > 0 || $paciente->hospitalizaciones->count() > 0)
        <h4 class="section-title" style="margin-top: 15pt;">HISTORIAL DE INGRESOS MÉDICOS</h4>
        
        <!-- CONSULTAS -->
        @if($paciente->consultas->count() > 0)
        <div class="form-section">
            <h5 style="font-weight: bold; margin-bottom: 5pt; font-size: 9pt;">CONSULTAS EXTERNAS:</h5>
            @foreach($paciente->consultas as $consulta)
            <div style="margin-bottom: 8pt; padding: 5pt; border: 1px solid #ddd;">
                <div class="f-row">
                    <div class="f-field" style="flex: 1;">
                        <span class="f-label">Fecha:</span>
                        <span class="f-value font-bold">{{ $consulta->fecha->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="f-field" style="flex: 2;">
                        <span class="f-label">Médico:</span>
                        <span class="f-value font-bold">{{ strtoupper($consulta->medico->user->name ?? '') }}</span>
                    </div>
                    <div class="f-field" style="flex: 1.5;">
                        <span class="f-label">Especialidad:</span>
                        <span class="f-value font-bold">{{ strtoupper($consulta->especialidad->nombre ?? '') }}</span>
                    </div>
                </div>
                <div class="f-row">
                    <div class="f-field w-100">
                        <span class="f-label">Motivo:</span>
                        <span class="f-value font-bold">{{ strtoupper($consulta->motivo ?? '') }}</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif

        <!-- EMERGENCIAS -->
        @if($paciente->emergencias->count() > 0)
        <div class="form-section">
            <h5 style="font-weight: bold; margin-bottom: 5pt; font-size: 9pt;">EMERGENCIAS:</h5>
            @foreach($paciente->emergencias as $emergencia)
            <div style="margin-bottom: 8pt; padding: 5pt; border: 1px solid #ddd;">
                <div class="f-row">
                    <div class="f-field" style="flex: 1;">
                        <span class="f-label">Código:</span>
                        <span class="f-value font-bold">{{ $emergencia->code }}</span>
                    </div>
                    <div class="f-field" style="flex: 1.5;">
                        <span class="f-label">Fecha:</span>
                        <span class="f-value font-bold">{{ $emergencia->admission_date?->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="f-field" style="flex: 1.5;">
                        <span class="f-label">Estado:</span>
                        <span class="f-value font-bold">{{ strtoupper($emergencia->status) }}</span>
                    </div>
                </div>
                <div class="f-row">
                    <div class="f-field w-100">
                        <span class="f-label">Motivo/Síntomas:</span>
                        <span class="f-value font-bold">{{ strtoupper($emergencia->symptoms ?? $emergencia->initial_assessment ?? '') }}</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif

        <!-- HOSPITALIZACIONES -->
        @if($paciente->hospitalizaciones->count() > 0)
        <div class="form-section">
            <h5 style="font-weight: bold; margin-bottom: 5pt; font-size: 9pt;">HOSPITALIZACIONES:</h5>
            @foreach($paciente->hospitalizaciones as $hospitalizacion)
            <div style="margin-bottom: 8pt; padding: 5pt; border: 1px solid #ddd;">
                <div class="f-row">
                    <div class="f-field" style="flex: 1;">
                        <span class="f-label">Ingreso:</span>
                        <span class="f-value font-bold">{{ $hospitalizacion->fecha_ingreso->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="f-field" style="flex: 1.5;">
                        <span class="f-label">Servicio:</span>
                        <span class="f-value font-bold">{{ strtoupper($hospitalizacion->servicio->nombre ?? '') }}</span>
                    </div>
                    <div class="f-field" style="flex: 1;">
                        <span class="f-label">Estado:</span>
                        <span class="f-value font-bold">{{ strtoupper($hospitalizacion->estado) }}</span>
                    </div>
                </div>
                <div class="f-row">
                    <div class="f-field w-100">
                        <span class="f-label">Médico Tratante:</span>
                        <span class="f-value font-bold">{{ strtoupper($hospitalizacion->medico->user->name ?? '') }}</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
        @endif

        <!-- ACTA DE RESPONSABILIDAD -->
        <h4 class="section-title" style="margin-top: 20pt;">ACTA DE RESPONSABILIDAD</h4>
        <div style="font-size: 8pt; line-height: 1.4; margin-bottom: 15pt;">
            <p style="margin: 5pt 0;">
                Yo, <span class="inline-val" style="width: 200pt;">{{ strtoupper($paciente->nombre) }}</span> 
                con C.I. Nº <span class="inline-val" style="width: 80pt;">{{ $paciente->ci }}</span>, 
                declaro conocer y aceptar los términos y condiciones del servicio médico proporcionado por Clínica Médica CEMSA Ltda.
            </p>
            <p style="margin: 5pt 0;">
                Me comprometo a cumplir con las normas internas de la institución y a proporcionar información veraz sobre mi estado de salud.
            </p>
            <p style="margin: 5pt 0;">
                Autorizo a que el personal médico realice los procedimientos necesarios para mi diagnóstico y tratamiento.
            </p>
        </div>

        <!-- FIRMAS -->
        <div class="signatures">
            <div class="sig-box">
                <div class="sig-line"></div>
                <p>FIRMA DEL PACIENTE</p>
                <p style="font-size: 7pt; margin-top: 5pt;">{{ $paciente->nombre }}</p>
            </div>
            
            <div class="sig-box">
                <div class="sig-line"></div>
                <p>FIRMA DEL RESPONSABLE</p>
                <p style="font-size: 7pt; margin-top: 5pt;">{{ $garante->nombre ?? 'N/A' }}</p>
            </div>
            
            <div class="sig-box">
                <div class="sig-line"></div>
                <p>FIRMA DEL ADMINISTRATIVO</p>
                <p style="font-size: 7pt; margin-top: 5pt;">{{ $paciente->registro->user->name ?? 'N/A' }}</p>
            </div>
        </div>

        <!-- SELLO Y FECHA -->
        <div class="stamp-area">
            <div class="stamp-box">
                <p><strong>SELLA Y FIRMA</strong></p>
                <p style="font-size: 7pt;">Clínica Médica CEMSA</p>
            </div>
            <p style="margin-top: 15pt; font-size: 8pt;">
                <strong>Fecha de emisión:</strong> {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}
            </p>
        </div>
    </div>
    <script>
        window.onload = function() {
            window.print();
        };
        window.onafterprint = function() {
            window.close();
        };
    </script>
</body>
</html>
