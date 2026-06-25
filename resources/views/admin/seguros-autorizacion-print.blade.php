<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Autorización de Seguro · {{ $d['cuenta']['id'] }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, Helvetica, sans-serif; color: #1f2937; font-size: 12px; background: #f3f4f6; }
        .sheet { width: 210mm; min-height: 297mm; margin: 0 auto; background: #fff; padding: 14mm 12mm; }

        /* ── Cabecera de clínica (mismo formato que el comprobante/recibo) ── */
        .cab { display: flex; justify-content: space-between; align-items: flex-start; gap: 16px; border-bottom: 3px solid #047857; padding-bottom: 12px; }
        .cab-izq { display: flex; gap: 10px; align-items: flex-start; }
        .cab-logo { width: 70px; height: auto; }
        .cab-clinica { font-size: 13px; font-weight: 800; letter-spacing: .3px; color: #065f46; }
        .cab-linea { font-size: 10px; font-weight: 700; line-height: 1.5; color: #374151; }
        .cab-der { text-align: right; font-size: 11px; line-height: 1.6; }
        .cab-der .lbl { font-weight: 700; }
        .cab-der .badge { display: inline-block; margin-top: 4px; padding: 3px 12px; border-radius: 8px; font-weight: 700; font-size: 11px; }
        .badge.ok { background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; }
        .badge.no { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }

        .titulo { text-align: center; margin: 16px 0 6px; }
        .titulo h1 { font-size: 24px; font-weight: 800; letter-spacing: 1px; color: #065f46; }
        .titulo p { font-size: 10px; color: #6b7280; margin-top: -2px; }

        .section { margin-bottom: 16px; }
        .section h2 { font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: #047857; border-bottom: 1px solid #e5e7eb; padding-bottom: 5px; margin-bottom: 8px; }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 4px 24px; }
        .row { display: flex; justify-content: space-between; gap: 12px; padding: 3px 0; border-bottom: 1px dotted #eef2f7; }
        .row .l { color: #6b7280; font-weight: 600; }
        .row .v { color: #111827; text-align: right; }
        table { width: 100%; border-collapse: collapse; margin-top: 4px; }
        th, td { text-align: left; padding: 6px 8px; font-size: 11px; }
        thead th { background: #f0fdf4; color: #065f46; border-bottom: 1px solid #d1fae5; text-transform: uppercase; font-size: 10px; letter-spacing: .5px; }
        tbody td { border-bottom: 1px solid #f3f4f6; }
        .totales { margin-top: 10px; display: flex; justify-content: flex-end; }
        .totales .box { width: 56%; }
        .totales .row .v { font-weight: 700; }
        .totales .grand { background: #f0fdf4; border-radius: 8px; padding: 6px 10px; margin-top: 4px; }
        .foot { margin-top: 40px; display: flex; justify-content: space-between; gap: 40px; }
        .firma { flex: 1; text-align: center; border-top: 1px solid #9ca3af; padding-top: 6px; color: #6b7280; font-size: 11px; }
        .legal { margin-top: 18px; text-align: center; font-size: 9px; color: #6b7280; line-height: 1.6; }
        .print-actions { text-align: center; padding: 16px; }
        .print-actions button { background: #047857; color: #fff; border: 0; padding: 10px 22px; border-radius: 8px; font-weight: 700; cursor: pointer; font-size: 13px; }
        @media print {
            body { background: #fff; }
            .sheet { width: auto; min-height: auto; padding: 8mm; margin: 0; }
            .print-actions { display: none; }
        }
    </style>
</head>
<body>
    @php
        // Identidad de la clínica — mismos datos que el comprobante/recibo de caja.
        $CLINICA = [
            'nombre'    => 'Clínica de Especialidades Medicas Santa Cruz S.R.L.',
            'sucursal'  => 'Sucursal Central',
            'direccion' => 'Av. Monseñor Santistevan #591 Esquina Caller Bumberque',
            'telefono'  => '75662703',
            'ciudad'    => 'Santa Cruz de la Sierra - Bolivia',
            'nit'       => '497970026',
        ];
        $esAut = $d['autorizacion']['estado'] === 'autorizado';
    @endphp

    <div class="print-actions">
        <button onclick="window.print()">Imprimir</button>
    </div>

    <div class="sheet">

        {{-- ════════ Cabecera de clínica ════════ --}}
        <div class="cab">
            <div class="cab-izq">
                <img class="cab-logo" src="{{ asset('images/logocelular.png') }}" alt="" onerror="this.style.display='none'">
                <div>
                    <div class="cab-clinica">{{ mb_strtoupper($CLINICA['nombre'], 'UTF-8') }}</div>
                    @if($CLINICA['sucursal'])<div class="cab-linea">{{ mb_strtoupper($CLINICA['sucursal'], 'UTF-8') }}</div>@endif
                    @if($CLINICA['direccion'])<div class="cab-linea">{{ $CLINICA['direccion'] }}</div>@endif
                    @if($CLINICA['telefono'])<div class="cab-linea">{{ $CLINICA['telefono'] }}</div>@endif
                    @if($CLINICA['ciudad'])<div class="cab-linea">{{ mb_strtoupper($CLINICA['ciudad'], 'UTF-8') }}</div>@endif
                </div>
            </div>
            <div class="cab-der">
                @if($CLINICA['nit'])<div><span class="lbl">NIT:</span> {{ $CLINICA['nit'] }}</div>@endif
                <div><span class="lbl">CUENTA Nº:</span> {{ $d['cuenta']['id'] }}</div>
                <div><span class="lbl">FECHA:</span> {{ now()->format('d/m/Y') }}</div>
                <div><span class="badge {{ $esAut ? 'ok' : 'no' }}">{{ $esAut ? 'AUTORIZADO' : 'RECHAZADO' }}</span></div>
            </div>
        </div>

        {{-- ════════ Título ════════ --}}
        <div class="titulo">
            <h1>AUTORIZACIÓN DE SEGURO</h1>
            <p>Comprobante interno · {{ $d['cuenta']['servicio'] }} · Registro {{ $d['cuenta']['fecha'] }}</p>
        </div>

        <div class="section">
            <h2>Datos del paciente</h2>
            <div class="grid">
                <div class="row"><span class="l">Nombre</span><span class="v">{{ $d['paciente']['nombre'] }}</span></div>
                <div class="row"><span class="l">CI / Código</span><span class="v">{{ $d['paciente']['ci'] }}</span></div>
                <div class="row"><span class="l">Sexo</span><span class="v">{{ $d['paciente']['sexo'] }}</span></div>
                <div class="row"><span class="l">Teléfono</span><span class="v">{{ $d['paciente']['telefono'] }}</span></div>
                <div class="row" style="grid-column: 1 / -1;"><span class="l">Dirección</span><span class="v">{{ $d['paciente']['direccion'] }}</span></div>
            </div>
        </div>

        <div class="section">
            <h2>Datos del seguro</h2>
            <div class="grid">
                <div class="row"><span class="l">Aseguradora</span><span class="v">{{ $d['seguro']['nombre'] }}</span></div>
                <div class="row"><span class="l">NIT</span><span class="v">{{ $d['seguro']['nit'] }}</span></div>
                <div class="row"><span class="l">Tipo</span><span class="v">{{ $d['seguro']['tipo'] }}</span></div>
                <div class="row"><span class="l">Teléfono aseguradora</span><span class="v">{{ $d['seguro']['telefono'] }}</span></div>
                <div class="row"><span class="l">N° Póliza / Carnet</span><span class="v">{{ $d['seguro']['poliza'] }}</span></div>
                <div class="row"><span class="l">Vigencia</span><span class="v">{{ $d['seguro']['vigencia_desde'] }} a {{ $d['seguro']['vigencia_hasta'] }}</span></div>
                <div class="row" style="grid-column: 1 / -1;"><span class="l">Cobertura</span><span class="v">{{ $d['seguro']['tipo_cobertura'] }}</span></div>
            </div>
        </div>

        <div class="section">
            <h2>Autorización</h2>
            <div class="grid">
                <div class="row"><span class="l">Estado</span><span class="v">{{ $d['autorizacion']['estado_label'] }}</span></div>
                <div class="row"><span class="l">N° autorización aseguradora</span><span class="v">{{ $d['autorizacion']['nro_autorizacion'] }}</span></div>
                <div class="row"><span class="l">Autorizado por</span><span class="v">{{ $d['autorizacion']['autorizado_por'] }}</span></div>
                <div class="row"><span class="l">Fecha autorización</span><span class="v">{{ $d['autorizacion']['fecha'] }}</span></div>
                <div class="row"><span class="l">Venta a la aseguradora</span><span class="v">{{ $d['autorizacion']['venta_id'] }}</span></div>
                <div class="row"><span class="l">Estado de cobro al seguro</span><span class="v">{{ $d['autorizacion']['estado_cobro'] }}</span></div>
                <div class="row" style="grid-column: 1 / -1;"><span class="l">Observaciones</span><span class="v">{{ $d['autorizacion']['observaciones'] }}</span></div>
            </div>
        </div>

        <div class="section">
            <h2>Cargos de la cuenta</h2>
            <table>
                <thead>
                    <tr><th>Descripción</th><th style="text-align:center;">Cant.</th><th style="text-align:right;">Subtotal (Bs)</th></tr>
                </thead>
                <tbody>
                    @forelse($d['cargos'] as $c)
                        <tr>
                            <td>{{ $c['descripcion'] }}</td>
                            <td style="text-align:center;">{{ $c['cantidad'] }}</td>
                            <td style="text-align:right;">{{ $c['subtotal'] }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" style="text-align:center; color:#9ca3af;">Sin cargos</td></tr>
                    @endforelse
                </tbody>
            </table>

            <div class="totales">
                <div class="box">
                    <div class="row"><span class="l">Monto total</span><span class="v">Bs. {{ $d['cuenta']['monto_total'] }}</span></div>
                    <div class="row"><span class="l">Cubierto por el seguro</span><span class="v">Bs. {{ $d['autorizacion']['cobertura'] }}</span></div>
                    <div class="row"><span class="l">Débito fiscal (IVA)</span><span class="v">Bs. {{ $d['autorizacion']['debito_fiscal'] }}</span></div>
                    <div class="row grand"><span class="l">Copago del paciente</span><span class="v">Bs. {{ $d['autorizacion']['copago'] }}</span></div>
                </div>
            </div>
        </div>

        <div class="foot">
            <div class="firma">Firma y sello aseguradora</div>
            <div class="firma">Firma responsable clínica</div>
        </div>

        <div class="legal">
            Documento interno de control de autorización de seguro. No constituye factura.
        </div>
    </div>

    <script>
        window.addEventListener('load', function () { setTimeout(function () { window.print(); }, 350); });
    </script>
</body>
</html>
