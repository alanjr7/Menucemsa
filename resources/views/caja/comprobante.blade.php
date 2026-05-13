<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Comprobante {{ $cuenta->id }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 12px; color: #111; padding: 24px; max-width: 480px; }

        .logo-area { text-align: center; border-bottom: 2px solid #111; padding-bottom: 10px; margin-bottom: 12px; }
        .logo-area h1 { font-size: 20px; font-weight: 900; letter-spacing: 3px; }
        .logo-area p  { font-size: 10px; color: #555; margin-top: 2px; }

        .badge { display: inline-block; font-size: 9px; font-weight: 700; letter-spacing: 1px;
                 text-transform: uppercase; background: #111; color: #fff;
                 padding: 2px 8px; border-radius: 20px; }

        .meta-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 4px 12px; margin: 10px 0; }
        .meta-row  { display: flex; justify-content: space-between; padding: 3px 0; }
        .meta-label { color: #666; }
        .meta-value { font-weight: 600; text-align: right; }

        .divider     { border-top: 1px dashed #bbb; margin: 10px 0; }
        .divider-solid { border-top: 1px solid #111; margin: 10px 0; }

        .section-title { font-size: 9px; font-weight: 700; text-transform: uppercase;
                         letter-spacing: 1px; color: #888; margin: 8px 0 4px; }

        table { width: 100%; border-collapse: collapse; }
        .item-row td { padding: 4px 0; vertical-align: top; border-bottom: 1px dotted #e5e5e5; }
        .item-row:last-child td { border-bottom: none; }

        .item-desc  { font-size: 11px; color: #222; max-width: 300px; }
        .item-fecha { font-size: 9px; color: #999; margin-top: 1px; }
        .item-monto { font-size: 11px; font-weight: 600; text-align: right; white-space: nowrap; }

        .pago-row { display: flex; justify-content: space-between; padding: 3px 0; font-size: 11px; }
        .total-row { display: flex; justify-content: space-between; font-size: 15px; font-weight: 900;
                     border-top: 2px solid #111; padding-top: 8px; margin-top: 6px; }
        .saldo-row { display: flex; justify-content: space-between; font-size: 12px; font-weight: 700;
                     color: #dc2626; padding-top: 4px; }

        .footer { text-align: center; font-size: 9px; color: #888; margin-top: 14px; line-height: 1.6; }

        .area-badge { font-size: 8px; font-weight: 700; text-transform: uppercase; color: #fff;
                      padding: 1px 5px; border-radius: 3px; margin-right: 4px; vertical-align: middle; }
        .area-emergencia  { background: #dc2626; }
        .area-internacion { background: #2563eb; }
        .area-consulta_externa { background: #16a34a; }
        .area-enfermeria  { background: #9333ea; }
        .area-general     { background: #6b7280; }

        @media print { .no-print { display: none; } body { padding: 10px; } }
    </style>
</head>
<body>

    {{-- Encabezado --}}
    <div class="logo-area">
        <h1>CEMSA</h1>
        <p>Clinica de Especialidades Santa Cruz</p>
        <p style="margin-top:6px;">
            <span class="badge">Comprobante de Pago</span>
        </p>
    </div>

    {{-- Datos generales --}}
    <div class="meta-row"><span class="meta-label">N° Cuenta:</span><span class="meta-value">{{ $cuenta->id }}</span></div>
    <div class="meta-row"><span class="meta-label">Fecha emisión:</span><span class="meta-value">{{ now()->format('d/m/Y H:i') }}</span></div>
    <div class="meta-row"><span class="meta-label">Paciente:</span><span class="meta-value">{{ $cuenta->paciente?->nombre ?? 'N/A' }}</span></div>
    <div class="meta-row"><span class="meta-label">CI:</span><span class="meta-value">{{ $cuenta->paciente_ci }}</span></div>
    <div class="meta-row"><span class="meta-label">Tipo atención:</span><span class="meta-value">{{ $cuenta->tipo_atencion_label }}</span></div>
    @if($cuenta->cajaSession?->user)
    <div class="meta-row"><span class="meta-label">Cajero:</span><span class="meta-value">{{ $cuenta->cajaSession->user->name }}</span></div>
    @endif

    @if($cuenta->ci_nit_facturacion)
    <div class="divider"></div>
    <div class="meta-row"><span class="meta-label">CI/NIT Factura:</span><span class="meta-value">{{ $cuenta->ci_nit_facturacion }}</span></div>
    <div class="meta-row"><span class="meta-label">Razón Social:</span><span class="meta-value">{{ $cuenta->razon_social ?? 'S/N' }}</span></div>
    @endif

    {{-- Ítems agrupados por área --}}
    <div class="divider"></div>
    <div class="section-title">Detalle de Servicios</div>

    @php
        $grupos = $cuenta->detalles->groupBy('area_origen');
        $areaLabels = [
            'emergencia'      => 'Emergencia',
            'internacion'     => 'Internación',
            'consulta_externa'=> 'Consulta Externa',
            'enfermeria'      => 'Enfermería',
            'uti'             => 'UTI',
            'general'         => 'General',
        ];
    @endphp

    @foreach($grupos as $area => $items)
    <div class="section-title">
        <span class="area-badge area-{{ $area }}">{{ $areaLabels[$area] ?? ucfirst($area) }}</span>
    </div>
    <table>
        @foreach($items as $d)
        <tr class="item-row">
            <td>
                <div class="item-desc">{{ $d->descripcion }}</div>
                <div class="item-fecha">{{ $d->created_at->setTimezone('America/La_Paz')->format('d/m/Y H:i') }}</div>
            </td>
            <td class="item-monto">Bs. {{ number_format($d->subtotal, 2) }}</td>
        </tr>
        @endforeach
    </table>
    @endforeach

    {{-- Pagos --}}
    <div class="divider"></div>
    <div class="section-title">Pagos Realizados</div>
    @forelse($cuenta->pagos as $p)
    <div class="pago-row">
        <span>{{ $p->metodo_pago_label }}
            @if($p->created_at)
                <span style="color:#999; font-size:9px;">{{ $p->created_at->setTimezone('America/La_Paz')->format('d/m/Y H:i') }}</span>
            @endif
        </span>
        <span>Bs. {{ number_format($p->monto, 2) }}</span>
    </div>
    @empty
    <p style="color:#999; font-size:10px;">Sin pagos registrados.</p>
    @endforelse

    {{-- Totales --}}
    <div class="divider-solid"></div>
    <div class="total-row">
        <span>TOTAL PAGADO</span>
        <span>Bs. {{ number_format($cuenta->total_pagado, 2) }}</span>
    </div>
    @if(bccomp((string)$cuenta->saldo_pendiente, '0', 2) > 0)
    <div class="saldo-row">
        <span>SALDO PENDIENTE</span>
        <span>Bs. {{ number_format($cuenta->saldo_pendiente, 2) }}</span>
    </div>
    @endif

    {{-- Footer --}}
    <div class="divider"></div>
    <div class="footer">
        <p>Total de ítems: {{ $cuenta->detalles->count() }}</p>
        <p style="margin-top:6px;">Gracias por su preferencia</p>
        <p>CEMSA — Clinica de Especialidades Santa Cruz</p>
    </div>

    <div class="no-print" style="margin-top: 20px; text-align: center;">
        <button onclick="window.print()"
            style="padding: 8px 24px; background: #1e40af; color: #fff; border: none; border-radius: 6px; cursor: pointer; font-size: 13px; font-weight: 600;">
            Imprimir / Guardar PDF
        </button>
    </div>

</body>
</html>
