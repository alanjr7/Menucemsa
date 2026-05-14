<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8"/>
<title>Reporte Farmacia — {{ $periodoLabel }}</title>
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: DejaVu Sans, sans-serif; font-size: 9px; color: #1e293b; background: #fff; }

.page-header { background: #1d4ed8; color: #fff; padding: 14px 20px; margin-bottom: 16px; }
.page-header h1 { font-size: 16px; font-weight: bold; }
.page-header p { font-size: 10px; opacity: 0.85; margin-top: 2px; }

.section { margin-bottom: 16px; }
.section-title { font-size: 9px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.08em; color: #64748b; border-bottom: 1px solid #e2e8f0; padding-bottom: 4px; margin-bottom: 8px; }

/* KPI grid */
.kpi-grid { display: table; width: 100%; border-collapse: separate; border-spacing: 6px; }
.kpi-row  { display: table-row; }
.kpi-cell { display: table-cell; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 10px 12px; width: 16.6%; vertical-align: top; }
.kpi-cell .label { font-size: 7.5px; font-weight: bold; text-transform: uppercase; color: #94a3b8; margin-bottom: 3px; }
.kpi-cell .value { font-size: 15px; font-weight: bold; color: #1e293b; }
.kpi-cell .value.green { color: #059669; }
.kpi-cell .value.orange { color: #d97706; }
.kpi-cell .value.red { color: #dc2626; }
.kpi-cell .sub { font-size: 7.5px; color: #94a3b8; margin-top: 2px; }

/* Tables */
table { width: 100%; border-collapse: collapse; }
thead tr { background: #1d4ed8; color: #fff; }
thead th { padding: 6px 8px; font-size: 8px; font-weight: bold; text-align: left; }
thead th.r { text-align: right; }
tbody tr:nth-child(even) { background: #f8fafc; }
tbody tr { border-bottom: 1px solid #f1f5f9; }
tbody td { padding: 5px 8px; font-size: 8.5px; vertical-align: middle; }
tbody td.r { text-align: right; }
tbody td.c { text-align: center; }
tfoot tr { background: #dbeafe; }
tfoot td { padding: 6px 8px; font-size: 8.5px; font-weight: bold; }
tfoot td.r { text-align: right; }

.badge { display: inline-block; padding: 1px 6px; border-radius: 9px; font-size: 7.5px; font-weight: bold; }
.badge-green  { background: #d1fae5; color: #065f46; }
.badge-red    { background: #fee2e2; color: #991b1b; }
.badge-yellow { background: #fef3c7; color: #92400e; }
.badge-blue   { background: #dbeafe; color: #1d4ed8; }
.badge-purple { background: #ede9fe; color: #6d28d9; }
.badge-teal   { background: #ccfbf1; color: #0f766e; }

.two-col { display: table; width: 100%; border-collapse: separate; border-spacing: 8px; }
.col { display: table-cell; vertical-align: top; }
.col-60 { width: 60%; }
.col-40 { width: 40%; }

.page-break { page-break-before: always; }

.footer { margin-top: 20px; border-top: 1px solid #e2e8f0; padding-top: 8px; color: #94a3b8; font-size: 7.5px; display: table; width: 100%; }
.footer .left  { display: table-cell; text-align: left; }
.footer .right { display: table-cell; text-align: right; }
</style>
</head>
<body>

{{-- HEADER --}}
<div class="page-header">
    <h1>Reporte de Farmacia</h1>
    <p>Período: {{ $periodoLabel }} &nbsp;·&nbsp; Generado el {{ now()->format('d/m/Y H:i') }}</p>
</div>

{{-- KPI --}}
<div class="section">
    <div class="section-title">Resumen del Período</div>
    <div class="kpi-grid">
        <div class="kpi-row">
            <div class="kpi-cell">
                <div class="label">Total Ventas</div>
                <div class="value">{{ number_format($stats->total_ventas) }}</div>
            </div>
            <div class="kpi-cell">
                <div class="label">Ingresos Totales</div>
                <div class="value green">Bs. {{ number_format($stats->ingresos_totales, 2) }}</div>
                <div class="sub">Solo completadas: Bs. {{ number_format($stats->ingresos_completadas, 2) }}</div>
            </div>
            <div class="kpi-cell">
                <div class="label">Ticket Promedio</div>
                <div class="value">Bs. {{ number_format($stats->promedio, 2) }}</div>
            </div>
            <div class="kpi-cell">
                <div class="label">Completadas</div>
                <div class="value green">{{ number_format($stats->completadas) }}</div>
            </div>
            <div class="kpi-cell">
                <div class="label">Anuladas</div>
                <div class="value red">{{ number_format($stats->anuladas) }}</div>
            </div>
            <div class="kpi-cell">
                <div class="label">Stock Bajo Mínimo</div>
                <div class="value {{ $alertasStock->count() > 0 ? 'orange' : '' }}">{{ $alertasStock->count() }}</div>
                <div class="sub">productos</div>
            </div>
        </div>
    </div>
</div>

{{-- VENTAS POR DÍA + MÉTODO DE PAGO --}}
<div class="two-col">
    <div class="col col-60">
        <div class="section-title">Ventas por Día</div>
        @if($ventasPorDia->count() > 0)
        <table>
            <thead><tr>
                <th>Fecha</th>
                <th class="r">N° Ventas</th>
                <th class="r">Total (Bs.)</th>
            </tr></thead>
            <tbody>
            @foreach($ventasPorDia as $dia)
            <tr>
                <td>{{ \Carbon\Carbon::parse($dia->fecha)->format('d/m/Y') }}</td>
                <td class="r">{{ $dia->total_ventas }}</td>
                <td class="r">{{ number_format($dia->total_ingresos, 2) }}</td>
            </tr>
            @endforeach
            </tbody>
            <tfoot><tr>
                <td><strong>TOTAL</strong></td>
                <td class="r">{{ $ventasPorDia->sum('total_ventas') }}</td>
                <td class="r">{{ number_format($ventasPorDia->sum('total_ingresos'), 2) }}</td>
            </tr></tfoot>
        </table>
        @else
        <p style="color:#94a3b8;font-size:8px;padding:8px 0;">Sin datos en el período</p>
        @endif
    </div>
    <div class="col col-40">
        <div class="section-title">Por Método de Pago</div>
        @if($ventasPorMetodoPago->count() > 0)
        <table>
            <thead><tr>
                <th>Método</th>
                <th class="r">N°</th>
                <th class="r">Total (Bs.)</th>
            </tr></thead>
            <tbody>
            @foreach($ventasPorMetodoPago as $m)
            <tr>
                <td style="text-transform:capitalize">{{ $m->metodo_pago }}</td>
                <td class="r">{{ $m->total_ventas }}</td>
                <td class="r">{{ number_format($m->total_ingresos, 2) }}</td>
            </tr>
            @endforeach
            </tbody>
            <tfoot><tr>
                <td><strong>TOTAL</strong></td>
                <td class="r">{{ $ventasPorMetodoPago->sum('total_ventas') }}</td>
                <td class="r">{{ number_format($ventasPorMetodoPago->sum('total_ingresos'), 2) }}</td>
            </tr></tfoot>
        </table>
        @endif
    </div>
</div>

{{-- VENDEDORES + PRODUCTOS --}}
<div class="two-col" style="margin-top:14px;">
    <div class="col col-40">
        <div class="section-title">Por Vendedor</div>
        @if($ventasPorVendedor->count() > 0)
        <table>
            <thead><tr>
                <th>Vendedor</th>
                <th class="r">N°</th>
                <th class="r">Total (Bs.)</th>
            </tr></thead>
            <tbody>
            @foreach($ventasPorVendedor as $v)
            <tr>
                <td>{{ $v->usuario?->name ?? 'N/A' }}</td>
                <td class="r">{{ $v->total_ventas }}</td>
                <td class="r">{{ number_format($v->total_ingresos, 2) }}</td>
            </tr>
            @endforeach
            </tbody>
            <tfoot><tr>
                <td><strong>TOTAL</strong></td>
                <td class="r">{{ $ventasPorVendedor->sum('total_ventas') }}</td>
                <td class="r">{{ number_format($ventasPorVendedor->sum('total_ingresos'), 2) }}</td>
            </tr></tfoot>
        </table>
        @endif
    </div>
    <div class="col col-60">
        <div class="section-title">Top 20 Productos Más Vendidos</div>
        @if($productosMasVendidos->count() > 0)
        <table>
            <thead><tr>
                <th style="width:18px">#</th>
                <th>Producto</th>
                <th class="r">Cant.</th>
                <th class="r">Total (Bs.)</th>
            </tr></thead>
            <tbody>
            @foreach($productosMasVendidos as $i => $p)
            <tr>
                <td class="c">{{ $i + 1 }}</td>
                <td>{{ $p->nombre_producto }}</td>
                <td class="r">{{ $p->total_vendido }}</td>
                <td class="r">{{ number_format($p->total_ingresos, 2) }}</td>
            </tr>
            @endforeach
            </tbody>
        </table>
        @endif
    </div>
</div>

{{-- DETALLE DE VENTAS --}}
<div class="page-break"></div>

<div class="page-header" style="margin-bottom:14px;">
    <h1>Detalle de Ventas</h1>
    <p>Período: {{ $periodoLabel }} &nbsp;·&nbsp; Total: {{ $ventas->count() }} registros</p>
</div>

<div class="section">
    <table>
        <thead><tr>
            <th>Código</th>
            <th>Fecha</th>
            <th>Hora</th>
            <th>Vendedor</th>
            <th>Cliente</th>
            <th>Productos</th>
            <th class="r">Items</th>
            <th>Método</th>
            <th class="r">Total (Bs.)</th>
            <th class="c">Estado</th>
        </tr></thead>
        <tbody>
        @foreach($ventas as $venta)
        <tr>
            <td style="font-family:monospace;font-size:7.5px;">{{ $venta->codigo_venta }}</td>
            <td>{{ \Carbon\Carbon::parse($venta->fecha_venta)->format('d/m/Y') }}</td>
            <td>{{ \Carbon\Carbon::parse($venta->fecha_venta)->format('H:i') }}</td>
            <td>{{ $venta->usuario?->name ?? 'N/A' }}</td>
            <td>{{ $venta->cliente ?? 'Público' }}</td>
            <td style="font-size:7.5px;max-width:180px;">
                {{ $venta->detalles->map(fn($d) => $d->nombre_producto . ' x' . $d->cantidad)->join(' · ') }}
            </td>
            <td class="r">{{ $venta->detalles->sum('cantidad') }}</td>
            <td style="text-transform:capitalize">{{ $venta->metodo_pago }}</td>
            <td class="r"><strong>{{ number_format($venta->total, 2) }}</strong></td>
            <td class="c">
                @if($venta->estado === 'COMPLETADA')
                    <span class="badge badge-green">COMP.</span>
                @elseif($venta->estado === 'ANULADA')
                    <span class="badge badge-red">ANUL.</span>
                @else
                    <span class="badge badge-yellow">PEND.</span>
                @endif
            </td>
        </tr>
        @endforeach
        </tbody>
        <tfoot><tr>
            <td colspan="8" class="r">TOTAL INGRESOS:</td>
            <td class="r">{{ number_format($ventas->sum('total'), 2) }}</td>
            <td></td>
        </tr></tfoot>
    </table>
</div>

{{-- ALERTAS STOCK --}}
@if($alertasStock->count() > 0)
<div class="page-break"></div>
<div class="page-header" style="background:#d97706;margin-bottom:14px;">
    <h1>Alerta: Productos Bajo Stock Mínimo</h1>
    <p>{{ $alertasStock->count() }} productos requieren reposición</p>
</div>
<div class="section">
    <table>
        <thead><tr>
            <th>Producto</th>
            <th>Laboratorio</th>
            <th>Lote</th>
            <th class="r">Disponible</th>
            <th class="r">Mínimo</th>
            <th class="r">Déficit</th>
            <th class="r">Precio Unit. (Bs.)</th>
        </tr></thead>
        <tbody>
        @foreach($alertasStock as $item)
        <tr>
            <td>{{ $item->medicamento?->nombre ?? $item->codigo_item }}</td>
            <td>{{ $item->laboratorio ?? '—' }}</td>
            <td>{{ $item->lote ?? '—' }}</td>
            <td class="r" style="color:#dc2626;font-weight:bold;">{{ $item->stock_disponible }}</td>
            <td class="r">{{ $item->stock_minimo }}</td>
            <td class="r" style="color:#d97706;font-weight:bold;">{{ $item->stock_minimo - $item->stock_disponible }}</td>
            <td class="r">{{ number_format($item->precio_unitario, 2) }}</td>
        </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endif

<div class="footer">
    <div class="left">Reporte generado por el sistema Menucemsa</div>
    <div class="right">{{ now()->format('d/m/Y H:i:s') }}</div>
</div>

</body>
</html>
