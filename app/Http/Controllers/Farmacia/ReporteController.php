<?php

namespace App\Http\Controllers\Farmacia;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\VentaFarmacia;
use App\Models\DetalleVentaFarmacia;
use App\Models\Medicamentos;
use Carbon\Carbon;

class ReporteController extends Controller
{
    public function index()
    {
        // Obtener estadísticas generales
        $totalVentas = VentaFarmacia::count();
        $ingresosTotales = VentaFarmacia::sum('TOTAL');
        $promedioPorVenta = $totalVentas > 0 ? $ingresosTotales / $totalVentas : 0;

        // Obtener ventas del día
        $hoy = Carbon::today();
        $ventasHoy = VentaFarmacia::whereDate('FECHA_VENTA', $hoy)->count();
        $ingresosHoy = VentaFarmacia::whereDate('FECHA_VENTA', $hoy)->sum('TOTAL');

        // Obtener productos más vendidos
        $productosMasVendidos = DetalleVentaFarmacia::selectRaw('
                NOMBRE_PRODUCTO,
                SUM(CANTIDAD) as total_vendido,
                SUM(SUBTOTAL) as total_ingresos
            ')
            ->groupBy('NOMBRE_PRODUCTO')
            ->orderBy('total_vendido', 'desc')
            ->take(10)
            ->get();

        // Obtener ventas por período (últimos 7 días)
        $ventasPorDia = VentaFarmacia::whereDate('FECHA_VENTA', '>=', Carbon::now()->subDays(7))
            ->selectRaw('DATE(FECHA_VENTA) as fecha, COUNT(*) as total_ventas, SUM(TOTAL) as total_ingresos')
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get();

        // Obtener ventas recientes para la tabla
        $ventasRecientes = VentaFarmacia::with(['detalles'])
            ->orderBy('FECHA_VENTA', 'desc')
            ->take(10)
            ->get();

        return view('farmacia.reporte', compact(
            'totalVentas',
            'ingresosTotales',
            'promedioPorVenta',
            'ventasHoy',
            'ingresosHoy',
            'productosMasVendidos',
            'ventasPorDia',
            'ventasRecientes'
        ));
    }

    public function filtrar(Request $request)
    {
        $periodo = $request->get('periodo', 'todo');
        $fechaInicio = null;
        $fechaFin = null;

        switch ($periodo) {
            case 'hoy':
                $fechaInicio = Carbon::today();
                $fechaFin = Carbon::today()->endOfDay();
                break;
            case '7dias':
                $fechaInicio = Carbon::now()->subDays(7);
                $fechaFin = Carbon::now();
                break;
            case 'mes':
                $fechaInicio = Carbon::now()->startOfMonth();
                $fechaFin = Carbon::now()->endOfMonth();
                break;
        }

        $query = VentaFarmacia::with(['detalles']);

        if ($fechaInicio && $fechaFin) {
            $query->whereBetween('FECHA_VENTA', [$fechaInicio, $fechaFin]);
        }

        $ventas = $query->orderBy('FECHA_VENTA', 'desc')->get();

        $totalVentas = $ventas->count();
        $ingresosTotales = $ventas->sum('TOTAL');
        $promedioPorVenta = $totalVentas > 0 ? $ingresosTotales / $totalVentas : 0;

        return response()->json([
            'totalVentas' => $totalVentas,
            'ingresosTotales' => $ingresosTotales,
            'promedioPorVenta' => $promedioPorVenta,
            'ventas' => $ventas
        ]);
    }
}
