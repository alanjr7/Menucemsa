<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Evaluación — {{ $paciente->nombre }}</title>
    <style>
        body { font-family: sans-serif; font-size: 13px; color: #111; margin: 2cm; }
        h1 { font-size: 18px; margin-bottom: 4px; }
        .meta { color: #555; font-size: 12px; margin-bottom: 16px; }
        h2 { font-size: 13px; font-weight: 600; border-bottom: 1px solid #ddd; padding-bottom: 3px; margin-top: 16px; margin-bottom: 8px; }
        table { width: 100%; border-collapse: collapse; font-size: 12px; }
        th, td { text-align: left; padding: 4px 8px; border: 1px solid #ddd; }
        th { background: #f5f5f5; }
        .obs { white-space: pre-wrap; background: #f9f9f9; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        @media print { button { display: none; } }
    </style>
</head>
<body>
    <h1>Evaluación Clínica</h1>
    <div class="meta">
        <strong>Paciente:</strong> {{ $paciente->nombre }} &nbsp;|&nbsp;
        <strong>CI:</strong> {{ $paciente->ci }} &nbsp;|&nbsp;
        <strong>Área:</strong> {{ ucfirst($evaluacion->area) }} &nbsp;|&nbsp;
        <strong>Fecha:</strong> {{ $evaluacion->created_at->format('d/m/Y H:i') }} &nbsp;|&nbsp;
        <strong>Evaluado por:</strong> {{ $evaluacion->user->name ?? '-' }}
    </div>

    @if($evaluacion->items->where('tipo','medicamento')->count())
        <h2>Medicamentos</h2>
        <table>
            <thead><tr><th>Nombre</th><th>Cantidad</th></tr></thead>
            <tbody>
                @foreach($evaluacion->items->where('tipo','medicamento') as $item)
                    <tr><td>{{ $item->nombre_snapshot }}</td><td>{{ $item->cantidad }}</td></tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @if($evaluacion->items->where('tipo','insumo')->count())
        <h2>Insumos</h2>
        <table>
            <thead><tr><th>Nombre</th><th>Cantidad</th></tr></thead>
            <tbody>
                @foreach($evaluacion->items->where('tipo','insumo') as $item)
                    <tr><td>{{ $item->nombre_snapshot }}</td><td>{{ $item->cantidad }}</td></tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @if($evaluacion->items->where('tipo','procedimiento')->count())
        <h2>Procedimientos</h2>
        <table>
            <thead><tr><th>Nombre</th><th>Cantidad</th><th>Precio unit.</th><th>Subtotal</th></tr></thead>
            <tbody>
                @foreach($evaluacion->items->where('tipo','procedimiento') as $item)
                    <tr>
                        <td>{{ $item->nombre_snapshot }}</td>
                        <td>{{ $item->cantidad }}</td>
                        <td>Bs. {{ number_format($item->precio_snapshot, 2) }}</td>
                        <td>Bs. {{ number_format($item->precio_snapshot * $item->cantidad, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @if($evaluacion->observaciones)
        <h2>Observaciones</h2>
        <div class="obs">{{ $evaluacion->observaciones }}</div>
    @endif

    <br>
    <button onclick="window.print()">Imprimir</button>
</body>
</html>
