<?php

namespace App\Http\Controllers\Farmacia;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\VentaFarmacia;
use App\Models\DetalleVentaFarmacia;
use Carbon\Carbon;

class VentasController extends Controller
{
    public function index()
    {
        // Obtener todas las ventas con sus detalles
        $ventas = VentaFarmacia::with(['detalles'])
            ->orderBy('FECHA_VENTA', 'desc')
            ->get();

        // Calcular estadísticas
        $totalVentas = $ventas->count();
        $ingresosTotales = $ventas->sum('TOTAL');
        $promedioPorVenta = $totalVentas > 0 ? $ingresosTotales / $totalVentas : 0;

        // Obtener ventas de hoy
        $ventasHoy = VentaFarmacia::whereDate('FECHA_VENTA', Carbon::today())->count();
        $ingresosHoy = VentaFarmacia::whereDate('FECHA_VENTA', Carbon::today())->sum('TOTAL');

        return view('farmacia.ventas', compact(
            'ventas',
            'totalVentas',
            'ingresosTotales',
            'promedioPorVenta',
            'ventasHoy',
            'ingresosHoy'
        ));
    }

    public function show($codigoVenta)
    {
        $venta = VentaFarmacia::with(['detalles'])
            ->where('CODIGO_VENTA', $codigoVenta)
            ->firstOrFail();

        return response()->json($venta);
    }

    public function destroy($codigoVenta)
    {
        try {
            $venta = VentaFarmacia::where('CODIGO_VENTA', $codigoVenta)->firstOrFail();
            
            // Eliminar detalles primero
            DetalleVentaFarmacia::where('CODIGO_VENTA', $codigoVenta)->delete();
            
            // Eliminar venta
            $venta->delete();

            return response()->json(['success' => true, 'message' => 'Venta eliminada exitosamente']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al eliminar la venta'], 500);
        }
    }
}
