<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Episodio #{{ $episodio->numero }} — {{ $episodio->paciente?->nombre }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: sans-serif; font-size: 12px; color: #111; padding: 1.5cm 2cm; }
        h1 { font-size: 16px; font-weight: 700; margin-bottom: 2px; }
        .meta { color: #555; font-size: 11px; margin-bottom: 16px; }
        h2 { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .05em;
             color: #333; border-bottom: 1px solid #ccc; padding-bottom: 3px; margin: 14px 0 6px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        th, td { text-align: left; padding: 4px 6px; border: 1px solid #ddd; vertical-align: top; }
        th { background: #f0f4ff; font-weight: 600; font-size: 11px; }
        td { font-size: 11px; }
        .kv { display: grid; grid-template-columns: 130px 1fr; gap: 2px 8px; margin-bottom: 10px; }
        .kv .label { color: #666; }
        .kv .val { font-weight: 500; }
        .badge-open { display: inline-block; background: #dcfce7; color: #166534; border-radius: 12px; padding: 1px 8px; font-size: 10px; font-weight: 600; }
        .badge-closed { display: inline-block; background: #f3f4f6; color: #6b7280; border-radius: 12px; padding: 1px 8px; font-size: 10px; font-weight: 600; }
        .sv-pill { display: inline-block; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 4px; padding: 1px 6px; font-size: 10px; margin: 1px; }
        .item-row td { background: #fafafa; color: #555; }
        .no-data { color: #999; font-style: italic; font-size: 11px; }
        .print-btn { position: fixed; top: 12px; right: 16px; background: #2563eb; color: #fff; border: none;
                     padding: 6px 14px; border-radius: 6px; font-size: 12px; cursor: pointer; }
        @media print {
            .print-btn { display: none; }
            body { padding: 1cm 1.5cm; }
        }
    </style>
</head>
<body>

<button class="print-btn" onclick="window.print()">Imprimir / Guardar PDF</button>

<h1>Episodio #{{ $episodio->numero }}</h1>
<div class="meta">
    {{ $episodio->fecha_apertura->format('d/m/Y H:i') }}
    @if($episodio->fecha_cierre)
        &rarr; {{ $episodio->fecha_cierre->format('d/m/Y H:i') }} ({{ $episodio->duracion }})
    @else
        &mdash; en curso
    @endif
    &nbsp;
    @if($episodio->estado === 'abierto')
        <span class="badge-open">Abierto</span>
    @else
        <span class="badge-closed">Cerrado</span>
    @endif
</div>

<h2>Paciente</h2>
<div class="kv">
    <span class="label">Nombre</span><span class="val">{{ $episodio->paciente?->nombre ?? '—' }}</span>
    <span class="label">CI</span><span class="val">{{ $episodio->paciente_ci }}</span>
    @if($episodio->tipo_ingreso)
    <span class="label">Tipo ingreso</span><span class="val">{{ ucfirst($episodio->tipo_ingreso) }}</span>
    @endif
    @if($episodio->motivo_cierre)
    <span class="label">Motivo cierre</span><span class="val">{{ $episodio->motivo_cierre }}</span>
    @endif
</div>

@if($episodio->emergencias->isNotEmpty())
<h2>Emergencias</h2>
<table>
    <thead><tr><th>Código</th><th>Fecha admisión</th><th>Ubicación</th></tr></thead>
    <tbody>
        @foreach($episodio->emergencias as $em)
        <tr>
            <td>{{ $em->code }}</td>
            <td>{{ ($em->admission_date ?? $em->created_at)?->format('d/m/Y H:i') ?? '—' }}</td>
            <td>{{ $em->ubicacion_label ?? '—' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

@if($episodio->hospitalizaciones->isNotEmpty())
<h2>Hospitalizaciones</h2>
<table>
    <thead><tr><th>Ingreso</th><th>Alta</th><th>Médico</th></tr></thead>
    <tbody>
        @foreach($episodio->hospitalizaciones as $hosp)
        <tr>
            <td>{{ $hosp->fecha_ingreso->format('d/m/Y') }}</td>
            <td>{{ $hosp->fecha_alta?->format('d/m/Y') ?? 'En curso' }}</td>
            <td>{{ $hosp->medico?->user?->name ?? 'Sin médico' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

<h2>Evaluaciones ({{ $episodio->evaluaciones->count() }})</h2>
@forelse($episodio->evaluaciones as $eval)
<table style="margin-bottom: 10px;">
    <thead>
        <tr>
            <th colspan="2">{{ strtoupper($eval->area) }} — {{ $eval->created_at->format('d/m/Y H:i') }} — {{ $eval->user?->name ?? '—' }}</th>
        </tr>
    </thead>
    <tbody>
        @if($eval->observaciones)
        <tr><td colspan="2">{{ $eval->observaciones }}</td></tr>
        @endif
        @if($eval->signos_vitales)
        <tr>
            <td colspan="2">
                @foreach($eval->signos_vitales as $key => $val)
                    @if($val)<span class="sv-pill">{{ str_replace('_',' ',$key) }}: {{ $val }}</span>@endif
                @endforeach
            </td>
        </tr>
        @endif
        @foreach($eval->items as $item)
        <tr class="item-row">
            <td>{{ ucfirst($item->tipo) }} — {{ $item->nombre_snapshot }}</td>
            <td>x{{ $item->cantidad }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@empty
<p class="no-data">Sin evaluaciones en este episodio.</p>
@endforelse

<h2>Cuentas por Cobrar ({{ $episodio->cuentasCobro->count() }})</h2>
@forelse($episodio->cuentasCobro as $cuenta)
<table style="margin-bottom: 10px;">
    <thead>
        <tr>
            <th colspan="2">{{ $cuenta->tipo_atencion_label }}</th>
            <th>Estado</th>
            <th>Total</th>
            <th>Pagado</th>
            @if($cuenta->saldo_pendiente > 0)<th>Saldo</th>@endif
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="2"></td>
            <td>{{ $cuenta->estado_label }}</td>
            <td>Bs. {{ number_format($cuenta->total_calculado, 2) }}</td>
            <td>Bs. {{ number_format($cuenta->total_pagado, 2) }}</td>
            @if($cuenta->saldo_pendiente > 0)<td>Bs. {{ number_format($cuenta->saldo_pendiente, 2) }}</td>@endif
        </tr>
        @foreach($cuenta->detalles as $det)
        <tr class="item-row">
            <td colspan="2">{{ $det->descripcion }}</td>
            <td></td>
            <td>Bs. {{ number_format($det->subtotal, 2) }}</td>
            <td colspan="{{ $cuenta->saldo_pendiente > 0 ? 2 : 1 }}"></td>
        </tr>
        @endforeach
    </tbody>
</table>
@empty
<p class="no-data">Sin cuentas en este episodio.</p>
@endforelse

<script>
    // Abrir diálogo de impresión automáticamente si viene con ?auto=1
    if (new URLSearchParams(location.search).get('auto') === '1') {
        window.onload = () => window.print();
    }
</script>
</body>
</html>
