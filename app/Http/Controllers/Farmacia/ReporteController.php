<?php

namespace App\Http\Controllers\Farmacia;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\VentaFarmacia;
use App\Models\DetalleVentaFarmacia;
use App\Models\InventarioFarmacia;
use App\Models\Medicamentos;
use App\Models\Cliente;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ReporteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // Verificar que el usuario tenga rol farmacia o admin
        $this->middleware(function ($request, $next) {
            if (!Auth::user() || !in_array(Auth::user()->role, ['farmacia', 'admin', 'administrador'])) {
                abort(403, 'No tienes permisos para acceder a este módulo.');
            }
            return $next($request);
        });
    }

    public function index()
    {
        // Obtener estadísticas generales
        $totalVentas = VentaFarmacia::count();
        $ingresosTotales = VentaFarmacia::sum('total');
        $promedioPorVenta = $totalVentas > 0 ? $ingresosTotales / $totalVentas : 0;

        // Obtener ventas del día
        $hoy = Carbon::today();
        $ventasHoy = VentaFarmacia::whereDate('fecha_venta', $hoy)->count();
        $ingresosHoy = VentaFarmacia::whereDate('fecha_venta', $hoy)->sum('total');

        // Obtener productos más vendidos
        $productosMasVendidos = DetalleVentaFarmacia::selectRaw('
                nombre_producto,
                SUM(cantidad) as total_vendido,
                SUM(subtotal) as total_ingresos
            ')
            ->groupBy('nombre_producto')
            ->orderBy('total_vendido', 'desc')
            ->take(10)
            ->get();

        // Obtener ventas por período (últimos 7 días)
        $ventasPorDia = VentaFarmacia::whereDate('fecha_venta', '>=', Carbon::now()->subDays(7))
            ->selectRaw('DATE(fecha_venta) as fecha, COUNT(*) as total_ventas, SUM(total) as total_ingresos')
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get();

        // Obtener ventas recientes para la tabla
        $ventasRecientes = VentaFarmacia::with(['detalles'])
            ->orderBy('fecha_venta', 'desc')
            ->take(10)
            ->get();

        // Obtener alertas de stock
        $alertasStock = InventarioFarmacia::with('medicamento')
            ->where('tipo_item', 'medicamento')
            ->whereColumn('stock_disponible', '<=', 'stock_minimo')
            ->where('stock_minimo', '>', 0)
            ->get();

        // Obtener ventas por método de pago
        $ventasPorMetodoPago = VentaFarmacia::selectRaw('metodo_pago, COUNT(*) as total_ventas, SUM(total) as total_ingresos')
            ->groupBy('metodo_pago')
            ->orderBy('total_ingresos', 'desc')
            ->get();

        return view('farmacia.reporte', compact(
            'totalVentas',
            'ingresosTotales',
            'promedioPorVenta',
            'ventasHoy',
            'ingresosHoy',
            'productosMasVendidos',
            'ventasPorDia',
            'ventasRecientes',
            'alertasStock',
            'ventasPorMetodoPago'
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
            $query->whereBetween('fecha_venta', [$fechaInicio, $fechaFin]);
        }

        $ventas = $query->orderBy('fecha_venta', 'desc')->get();

        $totalVentas = $ventas->count();
        $ingresosTotales = $ventas->sum('total');
        $promedioPorVenta = $totalVentas > 0 ? $ingresosTotales / $totalVentas : 0;

        return response()->json([
            'totalVentas' => $totalVentas,
            'ingresosTotales' => $ingresosTotales,
            'promedioPorVenta' => $promedioPorVenta,
            'ventas' => $ventas
        ]);
    }
}
