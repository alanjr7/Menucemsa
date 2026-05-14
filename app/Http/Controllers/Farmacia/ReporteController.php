<?php

namespace App\Http\Controllers\Farmacia;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\VentaFarmacia;
use App\Models\DetalleVentaFarmacia;
use App\Models\InventarioFarmacia;
use App\Exports\VentasFarmaciaExport;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class ReporteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!Auth::user() || !in_array(Auth::user()->role, ['farmacia', 'admin', 'administrador'])) {
                abort(403);
            }
            return $next($request);
        });
    }

    public function index()
    {
        $alertasStock = InventarioFarmacia::with('medicamento')
            ->whereColumn('stock_disponible', '<=', 'stock_minimo')
            ->where('stock_minimo', '>', 0)
            ->get();

        $alertasVencimiento = InventarioFarmacia::with('medicamento')
            ->whereNotNull('fecha_vencimiento')
            ->whereDate('fecha_vencimiento', '>=', Carbon::today())
            ->whereDate('fecha_vencimiento', '<=', Carbon::now()->addDays(30))
            ->get();

        $valorInventario = InventarioFarmacia::selectRaw('SUM(CAST(stock_disponible AS DECIMAL(10,2)) * CAST(precio_unitario AS DECIMAL(10,2))) as total')
            ->value('total') ?? 0;

        return view('farmacia.reporte', compact('alertasStock', 'alertasVencimiento', 'valorInventario'));
    }

    public function datos(Request $request)
    {
        [$fechaInicio, $fechaFin] = $this->resolverRango($request);

        $ventasQuery = VentaFarmacia::query()
            ->when($fechaInicio, fn($q) => $q->whereBetween('fecha_venta', [$fechaInicio, $fechaFin]));

        $stats = (clone $ventasQuery)
            ->selectRaw('
                COUNT(*) as total_ventas,
                SUM(total) as ingresos_totales,
                AVG(total) as promedio,
                SUM(CASE WHEN estado = "COMPLETADA" THEN 1 ELSE 0 END) as completadas,
                SUM(CASE WHEN estado = "ANULADA" THEN 1 ELSE 0 END) as anuladas,
                SUM(CASE WHEN estado = "COMPLETADA" THEN total ELSE 0 END) as ingresos_completadas
            ')
            ->first();

        $ventasPorDia = (clone $ventasQuery)
            ->selectRaw('DATE(fecha_venta) as fecha, COUNT(*) as total_ventas, SUM(total) as total_ingresos')
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get();

        $ventasPorMetodoPago = (clone $ventasQuery)
            ->selectRaw('metodo_pago, COUNT(*) as total_ventas, SUM(total) as total_ingresos')
            ->groupBy('metodo_pago')
            ->orderBy('total_ingresos', 'desc')
            ->get();

        $ventasPorVendedor = (clone $ventasQuery)
            ->selectRaw('usuario_id, COUNT(*) as total_ventas, SUM(total) as total_ingresos')
            ->groupBy('usuario_id')
            ->with('usuario')
            ->orderBy('total_ingresos', 'desc')
            ->get()
            ->map(fn($v) => [
                'vendedor'       => $v->usuario?->name ?? 'N/A',
                'total_ventas'   => (int) $v->total_ventas,
                'total_ingresos' => (float) $v->total_ingresos,
            ]);

        $productosMasVendidos = DetalleVentaFarmacia::query()
            ->when($fechaInicio, fn($q) => $q->whereHas('venta', fn($q2) =>
                $q2->whereBetween('fecha_venta', [$fechaInicio, $fechaFin])
            ))
            ->selectRaw('nombre_producto, SUM(cantidad) as total_vendido, SUM(subtotal) as total_ingresos')
            ->groupBy('nombre_producto')
            ->orderBy('total_vendido', 'desc')
            ->take(15)
            ->get();

        $ventasDetalladas = (clone $ventasQuery)
            ->with(['detalles', 'usuario'])
            ->orderBy('fecha_venta', 'desc')
            ->take(500)
            ->get()
            ->map(fn($v) => [
                'codigo_venta'   => $v->codigo_venta,
                'fecha'          => Carbon::parse($v->fecha_venta)->format('d/m/Y'),
                'hora'           => Carbon::parse($v->fecha_venta)->format('H:i'),
                'vendedor'       => $v->usuario?->name ?? 'N/A',
                'cliente'        => $v->cliente ?? 'Público en general',
                'productos'      => $v->detalles->map(fn($d) => $d->nombre_producto . ' x' . $d->cantidad)->join(', '),
                'total_items'    => $v->detalles->sum('cantidad'),
                'metodo_pago'    => $v->metodo_pago,
                'total'          => (float) $v->total,
                'estado'         => $v->estado,
                'requiere_receta' => $v->requiere_receta,
            ]);

        $periodoLabel = $fechaInicio
            ? Carbon::parse($fechaInicio)->format('d/m/Y') . ' — ' . Carbon::parse($fechaFin)->format('d/m/Y')
            : 'Todo el tiempo';

        return response()->json([
            'periodo_label'       => $periodoLabel,
            'totalVentas'         => (int) $stats->total_ventas,
            'completadas'         => (int) $stats->completadas,
            'anuladas'            => (int) $stats->anuladas,
            'ingresosTotales'     => (float) $stats->ingresos_totales,
            'ingresosCompletadas' => (float) $stats->ingresos_completadas,
            'promedioPorVenta'    => (float) $stats->promedio,
            'ventasPorDia'        => $ventasPorDia,
            'ventasPorMetodoPago' => $ventasPorMetodoPago,
            'ventasPorVendedor'   => $ventasPorVendedor,
            'productosMasVendidos' => $productosMasVendidos,
            'ventasDetalladas'    => $ventasDetalladas,
        ]);
    }

    public function exportar(Request $request)
    {
        [$fechaInicio, $fechaFin] = $this->resolverRango($request);
        $nombre = 'ventas_farmacia_' . now()->format('Y-m-d_H-i') . '.xlsx';

        return Excel::download(new VentasFarmaciaExport($fechaInicio, $fechaFin), $nombre);
    }

    public function pdf(Request $request)
    {
        [$fechaInicio, $fechaFin] = $this->resolverRango($request);

        $ventasQuery = VentaFarmacia::query()
            ->when($fechaInicio, fn($q) => $q->whereBetween('fecha_venta', [$fechaInicio, $fechaFin]));

        $stats = (clone $ventasQuery)
            ->selectRaw('
                COUNT(*) as total_ventas,
                SUM(total) as ingresos_totales,
                AVG(total) as promedio,
                SUM(CASE WHEN estado = "COMPLETADA" THEN 1 ELSE 0 END) as completadas,
                SUM(CASE WHEN estado = "ANULADA" THEN 1 ELSE 0 END) as anuladas,
                SUM(CASE WHEN estado = "COMPLETADA" THEN total ELSE 0 END) as ingresos_completadas
            ')
            ->first();

        $ventasPorDia = (clone $ventasQuery)
            ->selectRaw('DATE(fecha_venta) as fecha, COUNT(*) as total_ventas, SUM(total) as total_ingresos')
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get();

        $ventasPorMetodoPago = (clone $ventasQuery)
            ->selectRaw('metodo_pago, COUNT(*) as total_ventas, SUM(total) as total_ingresos')
            ->groupBy('metodo_pago')
            ->orderBy('total_ingresos', 'desc')
            ->get();

        $ventasPorVendedor = (clone $ventasQuery)
            ->selectRaw('usuario_id, COUNT(*) as total_ventas, SUM(total) as total_ingresos')
            ->groupBy('usuario_id')
            ->with('usuario')
            ->orderBy('total_ingresos', 'desc')
            ->get();

        $productosMasVendidos = DetalleVentaFarmacia::query()
            ->when($fechaInicio, fn($q) => $q->whereHas('venta', fn($q2) =>
                $q2->whereBetween('fecha_venta', [$fechaInicio, $fechaFin])
            ))
            ->selectRaw('nombre_producto, SUM(cantidad) as total_vendido, SUM(subtotal) as total_ingresos')
            ->groupBy('nombre_producto')
            ->orderBy('total_vendido', 'desc')
            ->take(20)
            ->get();

        $ventas = (clone $ventasQuery)
            ->with(['detalles', 'usuario'])
            ->orderBy('fecha_venta', 'desc')
            ->take(500)
            ->get();

        $alertasStock = InventarioFarmacia::with('medicamento')
            ->whereColumn('stock_disponible', '<=', 'stock_minimo')
            ->where('stock_minimo', '>', 0)
            ->get();

        $periodoLabel = $fechaInicio
            ? Carbon::parse($fechaInicio)->format('d/m/Y') . ' — ' . Carbon::parse($fechaFin)->format('d/m/Y')
            : 'Todo el tiempo';

        $pdf = Pdf::loadView('farmacia.reporte-pdf', compact(
            'stats', 'ventasPorDia', 'ventasPorMetodoPago', 'ventasPorVendedor',
            'productosMasVendidos', 'ventas', 'alertasStock', 'periodoLabel'
        ))->setPaper('a4', 'landscape');

        return $pdf->stream('reporte_farmacia_' . now()->format('Y-m-d') . '.pdf');
    }

    public function filtrar(Request $request)
    {
        return $this->datos($request);
    }

    private function resolverRango(Request $request): array
    {
        $desde = $request->get('desde');
        $hasta = $request->get('hasta');
        $periodo = $request->get('periodo');

        if ($desde && $hasta) {
            return [Carbon::parse($desde)->startOfDay(), Carbon::parse($hasta)->endOfDay()];
        }

        return match ($periodo) {
            'hoy'   => [Carbon::today()->startOfDay(), Carbon::today()->endOfDay()],
            '7dias' => [Carbon::now()->subDays(6)->startOfDay(), Carbon::now()->endOfDay()],
            'mes'   => [Carbon::now()->startOfMonth()->startOfDay(), Carbon::now()->endOfMonth()->endOfDay()],
            'anio'  => [Carbon::now()->startOfYear()->startOfDay(), Carbon::now()->endOfYear()->endOfDay()],
            default => [null, null],
        };
    }
}
