<?php
namespace App\Http\Controllers\Farmacia;

use App\Http\Controllers\Controller;
use App\Models\VentaFarmacia;
use App\Models\AlmacenStock;
use Carbon\Carbon;

class FarmaciaDashboardController extends Controller
{
    public function index()
    {
        $hoy = Carbon::today();
        $ventasHoy = VentaFarmacia::whereDate('fecha_venta', $hoy)->count();
        $ingresosHoy = VentaFarmacia::whereDate('fecha_venta', $hoy)->sum('total');

        $totalMedicamentos = (int) $this->stockFarmaciaMedicamentos()->sum('cantidad_actual');
        $medicamentosDistintos = AlmacenStock::where('almacen_stocks.ubicacion', 'farmacia')
            ->join('almacen_lotes', 'almacen_stocks.lote_id', '=', 'almacen_lotes.id')
            ->join('almacen_catalogo', 'almacen_lotes.catalogo_id', '=', 'almacen_catalogo.id')
            ->where('almacen_catalogo.tipo', 'medicamento')
            ->where('almacen_stocks.cantidad_actual', '>', 0)
            ->distinct('almacen_catalogo.id')
            ->count('almacen_catalogo.id');

        $alertasStock = $this->getAlertasStock();
        $alertasVencimiento = $this->getAlertasVencimiento();

        $totalVentas = VentaFarmacia::count();

        $ultimasVentas = VentaFarmacia::with(['detalles'])
            ->orderBy('fecha_venta', 'desc')
            ->take(5)
            ->get();

        $ventasPorDia7 = VentaFarmacia::where('fecha_venta', '>=', Carbon::now()->subDays(6)->startOfDay())
            ->selectRaw('DATE(fecha_venta) as fecha, SUM(total) as total_ingresos, COUNT(*) as total_ventas')
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get();

        $ventasPorDia30 = VentaFarmacia::where('fecha_venta', '>=', Carbon::now()->subDays(29)->startOfDay())
            ->selectRaw('DATE(fecha_venta) as fecha, SUM(total) as total_ingresos, COUNT(*) as total_ventas')
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get();

        return view('farmacia.index', compact(
            'ventasHoy',
            'ingresosHoy',
            'totalMedicamentos',
            'medicamentosDistintos',
            'alertasStock',
            'alertasVencimiento',
            'totalVentas',
            'ultimasVentas',
            'ventasPorDia7',
            'ventasPorDia30'
        ));
    }
    
    /** Stocks de medicamentos en el área farmacia. */
    private function stockFarmaciaMedicamentos()
    {
        return AlmacenStock::with('lote.catalogo')
            ->where('ubicacion', 'farmacia')
            ->whereHas('lote.catalogo', fn ($q) => $q->where('tipo', 'medicamento'))
            ->get();
    }

    private function getAlertasStock()
    {
        return AlmacenStock::with('lote.catalogo')
            ->where('ubicacion', 'farmacia')
            ->where('stock_minimo', '>', 0)
            ->whereColumn('cantidad_actual', '<=', 'stock_minimo')
            ->whereHas('lote.catalogo', fn ($q) => $q->where('tipo', 'medicamento'))
            ->get()
            ->map(fn ($s) => [
                'id' => $s->id,
                'nombre' => $s->nombre,
                'stock_actual' => (int) $s->cantidad_actual,
                'stock_minimo' => (int) $s->stock_minimo,
                'tipo' => 'stock_bajo',
            ]);
    }

    private function getAlertasVencimiento()
    {
        $fechaLimite = Carbon::now()->addDays(30);

        return AlmacenStock::with('lote.catalogo')
            ->where('ubicacion', 'farmacia')
            ->where('cantidad_actual', '>', 0)
            ->whereHas('lote.catalogo', fn ($q) => $q->where('tipo', 'medicamento'))
            ->whereHas('lote', fn ($q) => $q
                ->whereNotNull('fecha_vencimiento')
                ->whereDate('fecha_vencimiento', '<=', $fechaLimite)
                ->whereDate('fecha_vencimiento', '>=', Carbon::now()))
            ->get()
            ->map(function ($s) {
                $fechaVenc = $s->lote->fecha_vencimiento;
                return [
                    'id' => $s->id,
                    'nombre' => $s->nombre,
                    'fecha_vencimiento' => $fechaVenc,
                    'dias_para_vencer' => (int) Carbon::today()->diffInDays($fechaVenc, false),
                    'tipo' => 'vencimiento',
                ];
            });
    }
}
