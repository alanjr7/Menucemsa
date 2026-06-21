<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Comprobante de Retención #{{ $egreso->id }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, Helvetica, sans-serif; font-size: 11px; color: #000; background: #f3f4f6; }

        .hoja { background: #fff; width: 210mm; max-width: 100%; margin: 0 auto; padding: 14mm 12mm; }

        .cab { display: flex; justify-content: space-between; align-items: flex-start; gap: 16px; }
        .cab-clinica { font-size: 14px; font-weight: 800; letter-spacing: .3px; }
        .cab-linea { font-size: 10px; font-weight: 700; line-height: 1.5; }
        .cab-der { text-align: right; font-size: 11px; line-height: 1.6; }
        .cab-der .lbl { font-weight: 700; }

        .titulo { text-align: center; margin: 16px 0 4px; }
        .titulo h1 { font-size: 22px; font-weight: 800; letter-spacing: 1px; }
        .titulo p  { font-size: 10px; color: #333; margin-top: 2px; }

        .datos { display: grid; grid-template-columns: 1fr 1fr; gap: 3px 24px; font-size: 11px; margin-top: 14px; }
        .datos .row { display: flex; gap: 6px; }
        .datos .k { font-weight: 700; white-space: nowrap; }

        table.det { width: 100%; border-collapse: collapse; margin-top: 16px; }
        table.det th, table.det td { border: 1px solid #000; padding: 6px 8px; }
        table.det thead th { font-size: 10px; font-weight: 700; text-align: left; background: #f3f4f6; }
        table.det td { font-size: 11px; }
        table.det td.r, table.det th.r { text-align: right; }

        .tot { margin-top: 8px; margin-left: auto; width: 320px; border-collapse: collapse; font-size: 11px; }
        .tot td { padding: 3px 6px; }
        .tot td.k { text-align: right; font-weight: 700; }
        .tot td.v { text-align: right; width: 110px; }
        .tot tr.grande td { font-size: 13px; font-weight: 800; border-top: 1px solid #000; }
        .tot tr.ret td { color: #b45309; }

        .son { font-size: 11px; font-weight: 700; margin-top: 10px; text-transform: uppercase; }

        .legal { margin-top: 22px; border-top: 1px solid #000; padding-top: 8px; text-align: center; font-size: 9px; color: #333; line-height: 1.6; }
        .legal strong { font-size: 10px; }

        .firmas { display: flex; justify-content: space-between; gap: 40px; margin-top: 40px; font-size: 10px; }
        .firma { flex: 1; text-align: center; border-top: 1px solid #000; padding-top: 4px; }

        .no-print { text-align: center; margin-top: 18px; }
        .no-print button { padding: 9px 26px; background: #1e40af; color: #fff; border: none;
                           border-radius: 6px; cursor: pointer; font-size: 13px; font-weight: 600; }

        @media print {
            body { background: #fff; }
            .hoja { width: auto; margin: 0; padding: 8mm; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
@php
    $CLINICA = [
        'nombre'    => 'Clínica Santa Cruz',
        'sucursal'  => 'Sucursal Principal',
        'direccion' => 'Calle Bumberque esq. MJ Santiestevan 591',
        'telefono'  => '75662703',
        'ciudad'    => 'Santa Cruz de la Sierra - Bolivia',
        'nit'       => '',   // NIT del agente de retención (completar al tenerlo)
    ];

    $tasas = \App\Models\Egreso::RETENCION_TASAS[$egreso->retencion_tipo] ?? \App\Models\Egreso::RETENCION_TASAS['servicios'];
    $fmtPct = fn ($t) => str_replace('.', ',', rtrim(rtrim(number_format($t * 100, 1, '.', ''), '0'), '.'));
@endphp

    <div class="hoja">
        <div class="cab">
            <div>
                <div class="cab-clinica">{{ mb_strtoupper($CLINICA['nombre'], 'UTF-8') }}</div>
                <div class="cab-linea">{{ mb_strtoupper($CLINICA['sucursal'], 'UTF-8') }}</div>
                <div class="cab-linea">{{ $CLINICA['direccion'] }}</div>
                <div class="cab-linea">{{ $CLINICA['telefono'] }}</div>
                <div class="cab-linea">{{ mb_strtoupper($CLINICA['ciudad'], 'UTF-8') }}</div>
            </div>
            <div class="cab-der">
                @if($CLINICA['nit'])<div><span class="lbl">NIT:</span> {{ $CLINICA['nit'] }}</div>@endif
                <div><span class="lbl">N°:</span> RET-{{ str_pad((string) $egreso->id, 6, '0', STR_PAD_LEFT) }}</div>
                <div><span class="lbl">FECHA:</span> {{ $egreso->fecha->format('d/m/Y') }}</div>
            </div>
        </div>

        <div class="titulo">
            <h1>COMPROBANTE DE RETENCIÓN</h1>
            <p>Agente de retención — retención de impuestos por pago sin factura</p>
        </div>

        <div class="datos">
            <div class="row"><span class="k">Beneficiario:</span><span class="v">{{ $egreso->proveedor ?: '—' }}</span></div>
            <div class="row"><span class="k">NIT/CI:</span><span class="v">{{ $egreso->nit_proveedor ?: '—' }}</span></div>
            <div class="row"><span class="k">Concepto:</span><span class="v">{{ $egreso->categoria_label }}</span></div>
            <div class="row"><span class="k">Tipo:</span><span class="v">{{ $egreso->retencion_tipo_label }}</span></div>
            <div class="row" style="grid-column: 1 / -1;"><span class="k">Detalle:</span><span class="v">{{ $egreso->descripcion }}</span></div>
        </div>

        <table class="det">
            <thead>
                <tr>
                    <th>Detalle</th>
                    <th class="r">Importe (Bs)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Monto bruto del servicio/bien</td>
                    <td class="r">{{ number_format($egreso->monto, 2) }}</td>
                </tr>
                <tr class="ret-row">
                    <td>Retención IUE ({{ $fmtPct($tasas['iue']) }}%)</td>
                    <td class="r">- {{ number_format($egreso->retencion_iue, 2) }}</td>
                </tr>
                <tr>
                    <td>Retención IT ({{ $fmtPct($tasas['it']) }}%)</td>
                    <td class="r">- {{ number_format($egreso->retencion_it, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <table class="tot">
            <tr><td class="k">Monto bruto:</td><td class="v">Bs {{ number_format($egreso->monto, 2) }}</td></tr>
            <tr class="ret"><td class="k">Total retenido:</td><td class="v">Bs {{ number_format($egreso->retencion_total, 2) }}</td></tr>
            <tr class="grande"><td class="k">Neto pagado al beneficiario:</td><td class="v">Bs {{ number_format($egreso->neto_pagado, 2) }}</td></tr>
        </table>

        <div class="son">SON : {{ \App\Support\NumeroALetras::moneda($egreso->retencion_total) }} (TOTAL RETENIDO)</div>

        <div class="firmas">
            <div class="firma">Agente de retención</div>
            <div class="firma">Beneficiario — {{ $egreso->proveedor ?: '' }}</div>
        </div>

        <div class="legal">
            <strong>Comprobante interno de retención.</strong><br>
            La clínica retiene IUE e IT por el pago a una persona natural sin factura y los empoza al SIN
            (declaración Form. 570 IUE / IT). Documento de respaldo para el beneficiario y el control interno.
        </div>

        <div class="no-print">
            <button onclick="window.print()">Imprimir / Guardar PDF</button>
        </div>
    </div>
</body>
</html>
