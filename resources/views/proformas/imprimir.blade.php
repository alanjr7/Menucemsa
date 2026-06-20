<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Proforma {{ $proforma->numero }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, Helvetica, sans-serif; font-size: 11px; color: #000; background: #f3f4f6; }

        .hoja { background: #fff; width: 210mm; max-width: 100%; margin: 0 auto; padding: 14mm 12mm; }

        /* ── Encabezado ─────────────────────────────────────────────── */
        .cab { display: flex; justify-content: space-between; align-items: flex-start; gap: 16px; }
        .cab-izq { display: flex; gap: 10px; align-items: flex-start; }
        .cab-logo { width: 70px; height: auto; }
        .cab-clinica { font-size: 13px; font-weight: 800; letter-spacing: .3px; }
        .cab-linea { font-size: 10px; font-weight: 700; line-height: 1.5; }
        .cab-der { text-align: right; font-size: 11px; line-height: 1.6; }
        .cab-der .lbl { font-weight: 700; }
        .cab-der .num { font-size: 14px; font-weight: 800; color: #047857; }

        .titulo { text-align: center; margin: 14px 0 4px; }
        .titulo h1 { font-size: 30px; font-weight: 800; letter-spacing: 2px; color: #065f46; }
        .titulo p  { font-size: 11px; color: #333; margin-top: -2px; }

        /* ── Datos del paciente ──────────────────────────────────────── */
        .datos { display: grid; grid-template-columns: 1fr 1fr; gap: 2px 24px; font-size: 11px; margin-top: 10px; }
        .datos .row { display: flex; gap: 6px; }
        .datos .k { font-weight: 700; white-space: nowrap; }
        .datos .v { font-weight: 400; }

        /* ── Tabla de ítems ─────────────────────────────────────────── */
        table.items { width: 100%; border-collapse: collapse; margin-top: 12px; }
        table.items th, table.items td { border: 1px solid #000; padding: 4px 6px; vertical-align: top; }
        table.items thead th { font-size: 10px; font-weight: 700; text-align: center; line-height: 1.25; background: #ecfdf5; }
        table.items td { font-size: 10px; }
        .c-cant   { width: 9%;  text-align: center; }
        .c-unidad { width: 15%; text-align: center; }
        .c-desc   { width: 45%; }
        .c-punit  { width: 15%; text-align: right; }
        .c-sub    { width: 16%; text-align: right; }
        .u-sub { display: block; font-size: 9px; color: #333; }

        /* ── Totales ────────────────────────────────────────────────── */
        .pie { display: flex; justify-content: space-between; align-items: flex-start; gap: 20px; margin-top: 6px; }
        .son { font-size: 11px; font-weight: 700; padding-top: 6px; max-width: 55%; text-transform: uppercase; }
        table.tot { border-collapse: collapse; font-size: 11px; min-width: 250px; }
        table.tot td { padding: 2px 4px; }
        table.tot td.k { text-align: right; font-weight: 700; }
        table.tot td.v { text-align: right; width: 90px; }
        table.tot tr.grande td { font-size: 14px; font-weight: 800; color: #065f46; }

        /* ── Observaciones / pie ─────────────────────────────────────── */
        .obs { margin-top: 14px; border-top: 1px solid #000; padding-top: 6px; font-size: 10px; }
        .obs h4 { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 3px; }
        .obs p { white-space: pre-line; line-height: 1.5; }

        .firmas { display: flex; justify-content: space-around; gap: 40px; margin-top: 46px; }
        .firma { text-align: center; font-size: 10px; width: 45%; }
        .firma .linea { border-top: 1px solid #000; padding-top: 4px; }

        .legal { margin-top: 18px; text-align: center; font-size: 9px; color: #333; line-height: 1.6; }
        .legal strong { font-size: 10px; }

        .no-print { text-align: center; margin-top: 18px; }
        .no-print button { padding: 9px 26px; background: #047857; color: #fff; border: none;
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
    // ── Identidad de la clínica (mismo bloque que el comprobante de caja) ──
    $CLINICA = [
        'nombre'    => 'Clínica Santa Cruz',
        'sucursal'  => 'Sucursal Principal',
        'direccion' => 'Calle Bumberque esq. MJ Santiestevan 591',
        'telefono'  => '75662703',
        'ciudad'    => 'Santa Cruz de la Sierra - Bolivia',
        'nit'       => '',
    ];

    $bienes  = \App\Models\ProformaItem::TIPOS_BIENES;
    $cantFmt = fn ($c) => fmod((float) $c, 1.0) == 0.0 ? (string) (int) $c : rtrim(rtrim(number_format((float) $c, 2), '0'), '.');
    $subtotalBruto = $proforma->items->sum('subtotal');
@endphp

    <div class="hoja">

        {{-- ════════ Encabezado ════════ --}}
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
                <div class="num">{{ $proforma->numero }}</div>
                <div><span class="lbl">FECHA:</span> {{ $proforma->created_at->format('d/m/Y') }}</div>
                <div><span class="lbl">VÁLIDA HASTA:</span> {{ optional($proforma->fecha_vencimiento)->format('d/m/Y') }}</div>
            </div>
        </div>

        {{-- ════════ Título ════════ --}}
        <div class="titulo">
            <h1>PROFORMA</h1>
            <p>(Cotización de servicios — documento informativo, no es factura)</p>
        </div>

        {{-- ════════ Datos del paciente ════════ --}}
        <div class="datos">
            <div class="row"><span class="k">Señor(a):</span><span class="v">{{ $proforma->paciente_nombre }}</span></div>
            <div class="row"><span class="k">CI/NIT:</span><span class="v">{{ $proforma->paciente_documento ?: '—' }}</span></div>
            <div class="row"><span class="k">Teléfono:</span><span class="v">{{ $proforma->paciente_telefono ?: '—' }}</span></div>
            <div class="row"><span class="k">Validez:</span><span class="v">{{ $proforma->validez_dias }} días</span></div>
        </div>

        {{-- ════════ Tabla de ítems ════════ --}}
        <table class="items">
            <thead>
                <tr>
                    <th class="c-cant">CANTIDAD</th>
                    <th class="c-unidad">UNIDAD DE<br>MEDIDA</th>
                    <th class="c-desc">DESCRIPCIÓN</th>
                    <th class="c-punit">PRECIO<br>UNITARIO</th>
                    <th class="c-sub">SUBTOTAL</th>
                </tr>
            </thead>
            <tbody>
                @forelse($proforma->items as $d)
                <tr>
                    <td class="c-cant">{{ $cantFmt($d->cantidad) }}</td>
                    <td class="c-unidad">UNIDAD<span class="u-sub">({{ in_array($d->tipo_item, $bienes) ? 'BIENES' : 'SERVICIOS' }})</span></td>
                    <td class="c-desc">{{ mb_strtoupper($d->descripcion, 'UTF-8') }}</td>
                    <td class="c-punit">{{ number_format($d->precio_unitario, 2) }}</td>
                    <td class="c-sub">{{ number_format($d->subtotal, 2) }}</td>
                </tr>
                @empty
                <tr><td colspan="5" style="text-align:center; color:#666;">Sin ítems.</td></tr>
                @endforelse
            </tbody>
        </table>

        {{-- ════════ Totales ════════ --}}
        <div class="pie">
            <div class="son">SON : {{ \App\Support\NumeroALetras::moneda($proforma->total) }}</div>
            <table class="tot">
                <tr><td class="k">SUB TOTAL Bs:</td><td class="v">{{ number_format($subtotalBruto, 2) }}</td></tr>
                <tr><td class="k">DESCUENTO Bs:</td><td class="v">{{ number_format($proforma->descuento, 2) }}</td></tr>
                <tr class="grande"><td class="k">TOTAL Bs:</td><td class="v">{{ number_format($proforma->total, 2) }}</td></tr>
            </table>
        </div>

        {{-- ════════ Observaciones ════════ --}}
        @if($proforma->observaciones)
        <div class="obs">
            <h4>Observaciones</h4>
            <p>{{ $proforma->observaciones }}</p>
        </div>
        @endif

        {{-- ════════ Firmas ════════ --}}
        <div class="firmas">
            <div class="firma"><div class="linea">Firma y sello — Clínica</div></div>
            <div class="firma"><div class="linea">Recibí conforme — Paciente</div></div>
        </div>

        {{-- ════════ Pie legal ════════ --}}
        <div class="legal">
            <strong>Esta proforma es un documento informativo de cotización.</strong><br>
            No constituye factura ni compromiso de pago. Los precios son referenciales y pueden variar; válida por {{ $proforma->validez_dias }} días desde su emisión.
        </div>

        <div class="no-print">
            <button onclick="window.print()">Imprimir / Guardar PDF</button>
        </div>

    </div>

</body>
</html>
