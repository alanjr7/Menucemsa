<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Comprobante {{ $cuenta->id }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 12px; padding: 20px; max-width: 380px; }
        .center { text-align: center; }
        .header { border-bottom: 1px solid #ccc; padding-bottom: 10px; margin-bottom: 10px; }
        .fila { display: flex; justify-content: space-between; padding: 3px 0; }
        .total { font-size: 16px; font-weight: bold; border-top: 1px solid #111; padding-top: 8px; margin-top: 8px; }
        table { width: 100%; margin: 10px 0; }
        td, th { font-size: 11px; padding: 3px 0; }
        .divider { border-top: 1px dashed #ccc; margin: 8px 0; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
    <div class="header center">
        <h2 style="font-size:16px;">CEMSA</h2>
        <p>Comprobante de Pago</p>
        <p style="font-size:11px; color:#666;">{{ $cuenta->id }}</p>
    </div>

    <div class="fila"><span>Fecha:</span><span>{{ now()->format('d/m/Y H:i') }}</span></div>
    <div class="fila"><span>Paciente:</span><span>{{ $cuenta->paciente?->nombre ?? 'N/A' }}</span></div>
    <div class="fila"><span>CI:</span><span>{{ $cuenta->paciente_ci }}</span></div>
    <div class="fila"><span>Atención:</span><span>{{ $cuenta->tipo_atencion_label }}</span></div>

    @if($cuenta->ci_nit_facturacion)
    <div class="divider"></div>
    <div class="fila"><span>CI/NIT Factura:</span><span>{{ $cuenta->ci_nit_facturacion }}</span></div>
    <div class="fila"><span>Razón Social:</span><span>{{ $cuenta->razon_social ?? 'S/N' }}</span></div>
    @endif

    <div class="divider"></div>
    <table>
        <tr><th style="text-align:left">Concepto</th><th style="text-align:right">Bs.</th></tr>
        @foreach($cuenta->detalles as $d)
        <tr>
            <td>{{ $d->descripcion }}</td>
            <td style="text-align:right">{{ number_format($d->subtotal, 2) }}</td>
        </tr>
        @endforeach
    </table>
    <div class="divider"></div>

    @foreach($cuenta->pagos as $p)
    <div class="fila">
        <span>Pago ({{ $p->metodo_pago_label }}):</span>
        <span>Bs. {{ number_format($p->monto, 2) }}</span>
    </div>
    @endforeach

    <div class="total fila">
        <span>TOTAL PAGADO:</span>
        <span>Bs. {{ number_format($cuenta->total_pagado, 2) }}</span>
    </div>

    @if($cuenta->saldo_pendiente > 0)
    <div class="fila" style="color: #dc2626; font-weight: bold;">
        <span>SALDO PENDIENTE:</span>
        <span>Bs. {{ number_format($cuenta->saldo_pendiente, 2) }}</span>
    </div>
    @endif

    <div class="divider"></div>
    <div class="center" style="font-size: 10px; color: #666; margin-top: 8px;">
        <p>Cajero: {{ $cuenta->cajaSession?->user?->name ?? 'N/A' }}</p>
        <p>Gracias por su preferencia</p>
    </div>

    <div class="no-print" style="margin-top: 20px; text-align: center;">
        <button onclick="window.print()" style="padding: 8px 20px; background: #1e40af; color: #fff; border: none; border-radius: 6px; cursor: pointer;">
            Imprimir Comprobante
        </button>
    </div>
</body>
</html>
