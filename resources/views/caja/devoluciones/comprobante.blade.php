<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nota de Crédito {{ $devolucion->id }}</title>
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
        .titulo h1 { font-size: 22px; font-weight: 800; letter-spacing: 1px; color: #92400e; }
        .titulo p  { font-size: 10px; color: #333; margin-top: 2px; }

        .anulada { text-align: center; margin-top: 6px; font-size: 14px; font-weight: 800; color: #b91c1c;
                   border: 2px solid #b91c1c; display: inline-block; padding: 2px 14px; transform: rotate(-2deg); }
        .anulada-wrap { text-align: center; }

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
        .tot tr.grande td { font-size: 13px; font-weight: 800; border-top: 1px solid #000; color: #92400e; }

        .son { font-size: 11px; font-weight: 700; margin-top: 10px; text-transform: uppercase; }

        .legal { margin-top: 22px; border-top: 1px solid #000; padding-top: 8px; text-align: center; font-size: 9px; color: #333; line-height: 1.6; }
        .legal strong { font-size: 10px; }

        .firmas { display: flex; justify-content: space-between; gap: 40px; margin-top: 40px; font-size: 10px; }
        .firma { flex: 1; text-align: center; border-top: 1px solid #000; padding-top: 4px; }

        .no-print { text-align: center; margin-top: 18px; }
        .no-print button { padding: 9px 26px; background: #92400e; color: #fff; border: none;
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
        'nombre'    => 'Clínica de Especialidades Santa Cruz S.R.L.',
        'sucursal'  => 'Sucursal Central',
        'direccion' => 'Av. Monseñor Santiesteban #591 esq. Calle Bumberque',
        'telefono'  => '75662703',
        'ciudad'    => 'Santa Cruz de la Sierra - Bolivia',
        'nit'       => '497970026',
    ];

    $paciente = $devolucion->cuentaCobro?->paciente;
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
                <div><span class="lbl">N°:</span> {{ $devolucion->id }}</div>
                <div><span class="lbl">FECHA:</span> {{ $devolucion->created_at->format('d/m/Y H:i') }}</div>
            </div>
        </div>

        <div class="titulo">
            <h1>NOTA DE CRÉDITO — DEVOLUCIÓN</h1>
            <p>Documento de ajuste: disminución de ingresos sobre un cobro ya realizado</p>
        </div>

        @if($devolucion->anulado)
            <div class="anulada-wrap"><span class="anulada">ANULADA</span></div>
        @endif

        <div class="datos">
            <div class="row"><span class="k">Paciente:</span><span class="v">{{ $paciente?->nombre ?? 'N/A' }}</span></div>
            <div class="row"><span class="k">C.I.:</span><span class="v">{{ $paciente?->ci ?? $paciente?->temp_code ?? '—' }}</span></div>
            <div class="row"><span class="k">Recibo original:</span><span class="v">{{ $devolucion->pago_cuenta_id }}</span></div>
            <div class="row"><span class="k">Cuenta:</span><span class="v">{{ $devolucion->cuenta_cobro_id }}</span></div>
            <div class="row"><span class="k">Método devolución:</span><span class="v">{{ $devolucion->metodo_devolucion_label }}{{ $devolucion->referencia ? ' — '.$devolucion->referencia : '' }}</span></div>
            <div class="row"><span class="k">Emitida por:</span><span class="v">{{ $devolucion->user?->name ?? 'N/A' }}</span></div>
            <div class="row" style="grid-column: 1 / -1;"><span class="k">Motivo:</span><span class="v">{{ $devolucion->motivo }}</span></div>
            <div class="row" style="grid-column: 1 / -1;"><span class="k">Tipo:</span><span class="v">{{ $devolucion->tipo_label }}</span></div>
            @if($devolucion->observaciones)
                <div class="row" style="grid-column: 1 / -1;"><span class="k">Observaciones:</span><span class="v">{{ $devolucion->observaciones }}</span></div>
            @endif
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
                    <td>Monto del recibo {{ $devolucion->pago_cuenta_id }} ({{ $devolucion->pago?->created_at?->format('d/m/Y') }})</td>
                    <td class="r">{{ number_format((float) ($devolucion->pago?->monto ?? 0), 2) }}</td>
                </tr>
                <tr>
                    <td>Devolución según esta Nota de Crédito</td>
                    <td class="r">- {{ number_format((float) $devolucion->monto, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <table class="tot">
            <tr><td class="k">Débito fiscal IVA revertido (13%):</td><td class="v">Bs {{ number_format((float) $devolucion->debito_fiscal, 2) }}</td></tr>
            <tr class="grande"><td class="k">TOTAL DEVUELTO:</td><td class="v">Bs {{ number_format((float) $devolucion->monto, 2) }}</td></tr>
        </table>

        <div class="son">SON : {{ \App\Support\NumeroALetras::moneda($devolucion->monto) }}</div>

        <div class="firmas">
            <div class="firma">Entregué conforme — Caja</div>
            <div class="firma">Recibí conforme — {{ $paciente?->nombre ?? '' }}</div>
        </div>

        <div class="legal">
            <strong>Nota de crédito interna (documento de ajuste).</strong><br>
            Disminuye los ingresos y el débito fiscal IVA del período en que se emite; no modifica el recibo
            de pago original, que se conserva íntegro para auditoría. Respaldo para el paciente y el control interno.
        </div>

        <div class="no-print">
            <button onclick="window.print()">Imprimir / Guardar PDF</button>
        </div>
    </div>
</body>
</html>
