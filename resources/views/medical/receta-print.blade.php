<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Receta #{{ $receta->nro }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 13px; padding: 30px; color: #111; }
        .header { border-bottom: 2px solid #1e40af; padding-bottom: 12px; margin-bottom: 16px; }
        .header h2 { font-size: 18px; color: #1e40af; }
        .datos { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-bottom: 16px; font-size: 12px; }
        .label { color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th { background: #f1f5f9; text-align: left; padding: 6px 8px; font-size: 11px; color: #374151; }
        td { padding: 6px 8px; border-bottom: 1px solid #e5e7eb; font-size: 12px; }
        .firma { margin-top: 60px; text-align: right; }
        .indicaciones { margin-top: 16px; padding: 10px; background: #f8fafc; border-left: 3px solid #1e40af; border-radius: 2px; }
        @media print { .no-print { display: none; } @page { margin: 1cm; } }
    </style>
</head>
<body>
    <div class="header">
        <h2>CEMSA — Receta Médica</h2>
        <p style="font-size:11px; color:#666">{{ config('app.url') }}</p>
    </div>

    <div class="datos">
        <div><span class="label">N° Receta:</span> <strong>{{ $receta->nro }}</strong></div>
        <div><span class="label">Fecha:</span> {{ \Carbon\Carbon::parse($receta->fecha)->format('d/m/Y') }}</div>
        <div><span class="label">Paciente:</span> {{ $receta->consulta?->paciente?->nombre ?? 'N/A' }}</div>
        <div><span class="label">CI:</span> {{ $receta->consulta?->paciente?->ci ?? 'N/A' }}</div>
        <div><span class="label">Médico:</span> Dr. {{ $receta->userMedico?->name ?? 'N/A' }}</div>
        <div><span class="label">Especialidad:</span> {{ $receta->consulta?->especialidad?->nombre ?? 'N/A' }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Medicamento</th>
                <th>Dosis / Posología</th>
            </tr>
        </thead>
        <tbody>
            @forelse($receta->detalles as $d)
            <tr>
                <td>{{ $d->medicamento?->descripcion ?? $d->codigo_medicamento }}</td>
                <td>{{ $d->dosis }}</td>
            </tr>
            @empty
            <tr><td colspan="2" style="color:#999; text-align:center;">Sin medicamentos prescritos</td></tr>
            @endforelse
        </tbody>
    </table>

    @if($receta->indicaciones)
    <div class="indicaciones">
        <strong style="font-size:11px; color:#1e40af;">INDICACIONES GENERALES:</strong>
        <p style="margin-top:4px;">{{ $receta->indicaciones }}</p>
    </div>
    @endif

    <div class="firma">
        <div style="border-top: 1px solid #111; display: inline-block; padding-top: 4px; min-width: 180px; text-align: center;">
            <p>Dr. {{ $receta->userMedico?->name ?? 'N/A' }}</p>
            <p style="font-size:11px; color:#666;">Firma y sello</p>
        </div>
    </div>

    <div class="no-print" style="margin-top: 24px; text-align:center;">
        <button onclick="window.print()" style="padding: 8px 20px; background:#1e40af; color:#fff; border:none; border-radius:6px; cursor:pointer;">
            Imprimir Receta
        </button>
        <button onclick="window.close()" style="padding: 8px 20px; margin-left:8px; background:#f1f5f9; border:1px solid #d1d5db; border-radius:6px; cursor:pointer;">
            Cerrar
        </button>
    </div>
</body>
</html>
