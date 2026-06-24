<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Comprobante {{ $cuenta->id }}</title>
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

        .titulo { text-align: center; margin: 14px 0 4px; }
        .titulo h1 { font-size: 30px; font-weight: 800; letter-spacing: 1px; }
        .titulo p  { font-size: 11px; color: #333; margin-top: -2px; }

        /* ── Datos del comprobante / paciente ──────────────────────── */
        .pagina { font-size: 10px; font-weight: 700; margin: 10px 0 4px; }
        .datos { display: grid; grid-template-columns: 1fr 1fr; gap: 2px 24px; font-size: 11px; }
        .datos .row { display: flex; gap: 6px; }
        .datos .k { font-weight: 700; white-space: nowrap; }
        .datos .v { font-weight: 400; }

        /* ── Tabla de ítems ─────────────────────────────────────────── */
        table.items { width: 100%; border-collapse: collapse; margin-top: 12px; }
        table.items th, table.items td { border: 1px solid #000; padding: 4px 6px; vertical-align: top; }
        table.items thead th { font-size: 10px; font-weight: 700; text-align: center; line-height: 1.25; }
        table.items td { font-size: 10px; }
        .c-codigo { width: 12%; }
        .c-cant   { width: 7%;  text-align: center; }
        .c-unidad { width: 13%; text-align: center; }
        .c-desc   { width: 36%; }
        .c-punit  { width: 11%; text-align: right; }
        .c-desc2  { width: 10%; text-align: right; }
        .c-sub    { width: 11%; text-align: right; }
        .u-sub { display: block; font-size: 9px; color: #333; }

        /* ── Totales ────────────────────────────────────────────────── */
        .pie { display: flex; justify-content: space-between; align-items: flex-start; gap: 20px; margin-top: 6px; }
        .son { font-size: 11px; font-weight: 700; padding-top: 6px; max-width: 55%; text-transform: uppercase; }
        table.tot { border-collapse: collapse; font-size: 11px; min-width: 250px; }
        table.tot td { padding: 2px 4px; }
        table.tot td.k { text-align: right; font-weight: 700; }
        table.tot td.v { text-align: right; width: 90px; }
        table.tot tr.grande td { font-size: 13px; font-weight: 800; }
        table.tot tr.saldo td { color: #b91c1c; }

        /* ── Forma de pago / pie legal ─────────────────────────────── */
        .pagos { margin-top: 14px; border-top: 1px solid #000; padding-top: 6px; }
        .pagos h4 { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 3px; }
        .pago-row { display: flex; justify-content: space-between; font-size: 11px; padding: 1px 0; }
        .pago-row .fecha { color: #666; font-size: 9px; }

        .legal { margin-top: 18px; text-align: center; font-size: 9px; color: #333; line-height: 1.6; }
        .legal strong { font-size: 10px; }

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
    // ── Identidad de la clínica (editar aquí cuando se tengan los datos definitivos) ──
    $CLINICA = [
        'nombre'    => 'Clínica de Especialidades Medicas Santa Cruz S.R.L.',
        'sucursal'  => 'Sucursal Central', // ej: 'Sucursal - Santa Cruz'
        'punto'     => '',   // ej: 'No. Punto de Venta - 0'
        'direccion' => 'Av. Monseñor Santistevan #591 Esquina Caller Bumberque',   // ej: 'Av. 26 de febrero N° 510'
        'telefono'  => '75662703',   // ej: 'Telf. 352-0444'
        'ciudad'    => 'Santa Cruz de la Sierra - Bolivia',
        'nit'       => '497970026',  
    ];

    // ── Alcance del recibo = ciclo de cobro actual ──
    // Si la cuenta está saldada, el recibo muestra los cargos liquidados por el
    // último pago (el ciclo recién cerrado). Si aún hay saldo, muestra todo lo
    // pendiente. Nunca re-lista cargos ya pagados en un ciclo anterior.
    $pagosOrdenados = $cuenta->pagos->sortBy('created_at')->values();
    $ultimoPago     = $pagosOrdenados->last();
    $cuentaSaldada  = bccomp((string) $cuenta->saldo_pendiente, '0', 2) <= 0 && $ultimoPago;

    if ($cuentaSaldada) {
        $detallesRecibo = $cuenta->detalles->where('liquidado_pago_id', $ultimoPago->id);
        if ($detallesRecibo->isEmpty()) {
            $detallesRecibo = $cuenta->detalles;
        }
        $cortePrevio = $cuenta->detalles
            ->whereNotNull('liquidado_pago_id')
            ->where('liquidado_pago_id', '!=', $ultimoPago->id)
            ->map(fn($d) => optional($d->liquidadoPago)->created_at)
            ->filter()->max();
    } else {
        $detallesRecibo = $cuenta->detalles->whereNull('liquidado_en');
        $cortePrevio = $cuenta->detalles
            ->whereNotNull('liquidado_pago_id')
            ->map(fn($d) => optional($d->liquidadoPago)->created_at)
            ->filter()->max();
    }

    $pagosRecibo = $cortePrevio
        ? $pagosOrdenados->filter(fn($p) => $p->created_at->gt($cortePrevio))->values()
        : $pagosOrdenados;

    $totalRecibo  = $detallesRecibo->sum('subtotal');
    $pagadoRecibo = $pagosRecibo->sum('monto');
    $saldoRecibo  = bcsub((string) $totalRecibo, (string) $pagadoRecibo, 2);

    // ── Unidad de medida: BIENES vs SERVICIOS según el tipo de ítem ──
    $bienes = ['medicamento', 'material', 'equipo_medico', 'farmacia'];
    $cantFmt = fn($c) => fmod((float) $c, 1.0) == 0.0 ? (string) (int) $c : rtrim(rtrim(number_format((float) $c, 2), '0'), '.');

    $pacienteNombre = $cuenta->paciente?->nombre ?? 'N/A';
    // Receptor fiscal: solo se muestra cuando el cliente pidió factura con datos.
    // Sin crédito fiscal NO se rellena con datos del paciente: las filas se omiten.
    $documento   = trim($cuenta->ci_nit_facturacion . ($cuenta->factura_complemento ? '-' . $cuenta->factura_complemento : ''));
    $razonSocial = $cuenta->razon_social;
    $tipoDocLbl  = $cuenta->tipo_documento_label ?: 'NIT/CI/CEX';
@endphp

    <div class="hoja">

        {{-- ════════ Encabezado ════════ --}}
        <div class="cab">
            <div class="cab-izq">
                <img class="cab-logo" src="{{ asset('images/logocelular.png') }}" alt="" onerror="this.style.display='none'">
                <div>
                    <div class="cab-clinica">{{ mb_strtoupper($CLINICA['nombre'], 'UTF-8') }}</div>
                    @if($CLINICA['sucursal'])<div class="cab-linea">{{ mb_strtoupper($CLINICA['sucursal'], 'UTF-8') }}</div>@endif
                    @if($CLINICA['punto'])<div class="cab-linea">{{ $CLINICA['punto'] }}</div>@endif
                    @if($CLINICA['direccion'])<div class="cab-linea">{{ $CLINICA['direccion'] }}</div>@endif
                    @if($CLINICA['telefono'])<div class="cab-linea">{{ $CLINICA['telefono'] }}</div>@endif
                    @if($CLINICA['ciudad'])<div class="cab-linea">{{ mb_strtoupper($CLINICA['ciudad'], 'UTF-8') }}</div>@endif
                </div>
            </div>
            <div class="cab-der">
                @if($CLINICA['nit'])<div><span class="lbl">NIT:</span> {{ $CLINICA['nit'] }}</div>@endif
                <div><span class="lbl">COMPROBANTE Nº:</span> {{ $cuenta->id }}</div>
                <div><span class="lbl">FECHA:</span> {{ now()->format('d/m/Y') }}</div>
            </div>
        </div>

        {{-- ════════ Título ════════ --}}
        <div class="titulo">
            <h1>COMPROBANTE</h1>
            <p>(Comprobante interno de pago — no válido como factura)</p>
        </div>

        {{-- ════════ Datos del comprobante / paciente ════════ --}}
        <div class="pagina">Página 1 de 1</div>
        <div class="datos">
            <div class="row"><span class="k">Fecha:</span><span class="v">{{ now()->format('d/m/Y  h:i a') }}</span></div>
            @if($cuenta->con_credito_fiscal)
            <div class="row"><span class="k">{{ $tipoDocLbl }}:</span><span class="v">{{ $documento }}</span></div>
            <div class="row"><span class="k">Nombre/Razón Social:</span><span class="v">{{ $razonSocial }}</span></div>
            @endif
            <div class="row"><span class="k">Tipo de atención:</span><span class="v">{{ $cuenta->tipo_atencion_label }}</span></div>
            <div class="row"><span class="k">Paciente:</span><span class="v">{{ $pacienteNombre }}</span></div>
            @if($cuenta->cajaSession?->user)
            <div class="row"><span class="k">Cajero:</span><span class="v">{{ $cuenta->cajaSession->user->name }}</span></div>
            @endif
        </div>

        {{-- ════════ Tabla de ítems ════════ --}}
        <table class="items">
            <thead>
                <tr>
                    <th class="c-codigo">CÓDIGO<br>PRODUCTO/<br>SERVICIO</th>
                    <th class="c-cant">CANTIDAD</th>
                    <th class="c-unidad">UNIDAD DE<br>MEDIDA</th>
                    <th class="c-desc">DESCRIPCIÓN</th>
                    <th class="c-punit">PRECIO<br>UNITARIO</th>
                    <th class="c-desc2">DESCUENTO</th>
                    <th class="c-sub">SUBTOTAL</th>
                </tr>
            </thead>
            <tbody>
                @forelse($detallesRecibo as $d)
                <tr>
                    <td class="c-codigo">{{ $d->codigo_item ?? '—' }}</td>
                    <td class="c-cant">{{ $cantFmt($d->cantidad) }}</td>
                    <td class="c-unidad">UNIDAD<span class="u-sub">({{ in_array($d->tipo_item, $bienes) ? 'BIENES' : 'SERVICIOS' }})</span></td>
                    <td class="c-desc">{{ mb_strtoupper($d->descripcion, 'UTF-8') }}</td>
                    <td class="c-punit">{{ number_format($d->precio_unitario, 2) }}</td>
                    <td class="c-desc2">0.00</td>
                    <td class="c-sub">{{ number_format($d->subtotal, 2) }}</td>
                </tr>
                @empty
                <tr><td colspan="7" style="text-align:center; color:#666;">Sin cargos en este comprobante.</td></tr>
                @endforelse
            </tbody>
        </table>

        {{-- ════════ Totales ════════ --}}
        <div class="pie">
            <div class="son">SON : {{ \App\Support\NumeroALetras::moneda($totalRecibo) }}</div>
            <table class="tot">
                <tr><td class="k">SUB TOTAL Bs:</td><td class="v">{{ number_format($totalRecibo, 2) }}</td></tr>
                <tr><td class="k">DESCUENTO Bs:</td><td class="v">0.00</td></tr>
                <tr><td class="k">TOTAL Bs:</td><td class="v">{{ number_format($totalRecibo, 2) }}</td></tr>
                <tr class="grande"><td class="k">MONTO PAGADO Bs:</td><td class="v">{{ number_format($pagadoRecibo, 2) }}</td></tr>
                @if(bccomp((string) $saldoRecibo, '0', 2) > 0)
                <tr class="saldo"><td class="k">SALDO PENDIENTE Bs:</td><td class="v">{{ number_format($saldoRecibo, 2) }}</td></tr>
                @endif
            </table>
        </div>

        {{-- ════════ Forma(s) de pago ════════ --}}
        <div class="pagos">
            <h4>Forma de pago</h4>
            @forelse($pagosRecibo as $p)
            <div class="pago-row">
                <span>{{ $p->metodo_pago_label }}
                    @if($p->created_at)<span class="fecha">{{ $p->created_at->setTimezone('America/La_Paz')->format('d/m/Y H:i') }}</span>@endif
                </span>
                <span>Bs {{ number_format($p->monto, 2) }}</span>
            </div>
            @empty
            <div class="pago-row"><span style="color:#666;">Sin pagos registrados.</span></div>
            @endforelse
        </div>

        {{-- ════════ Pie legal ════════ --}}
        <div class="legal">
            <strong>Este documento es un comprobante interno de pago.</strong><br>
            No constituye factura ni documento fiscal válido. Gracias por su preferencia.
        </div>

        <div class="no-print">
            <button onclick="window.print()">Imprimir / Guardar PDF</button>
        </div>

    </div>

</body>
</html>
