<?php
namespace App\Http\Controllers\Farmacia;

use App\Http\Controllers\Controller;
use App\Models\VentaFarmacia;
use App\Models\InventarioFarmacia;
use Carbon\Carbon;

class FarmaciaDashboardController extends Controller
{
    public function index()
    {
        $hoy = Carbon::today();
        $ventasHoy = VentaFarmacia::whereDate('fecha_venta', $hoy)->count();
        $ingresosHoy = VentaFarmacia::whereDate('fecha_venta', $hoy)->sum('total');

        $totalMedicamentos = InventarioFarmacia::where('tipo_item', 'medicamento')->count();
        $medicamentosDistintos = InventarioFarmacia::where('tipo_item', 'medicamento')->distinct('codigo_item')->count();

        $alertasStock = $this->getAlertasStock();
        $alertasVencimiento = $this->getAlertasVencimiento();

        $totalVentas = VentaFarmacia::count();

        $ultimasVentas = VentaFarmacia::with(['detalles'])
            ->orderBy('fecha_venta', 'desc')
            ->take(5)
            ->get();
        
        return view('farmacia.index', compact(
            'ventasHoy',
            'ingresosHoy',
            'totalMedicamentos',
            'medicamentosDistintos',
            'alertasStock',
            'alertasVencimiento',
            'totalVentas',
            'ultimasVentas'
        ));
    }
    
    private function getAlertasStock()
    {
        $alertas = InventarioFarmacia::with('medicamento')
            ->where('tipo_item', 'medicamento')
            ->whereColumn('stock_disponible', '<=', 'stock_minimo')
            ->where('stock_minimo', '>', 0)
            ->get();

        return $alertas->map(function ($inventario) {
            return [
                'id' => $inventario->codigo_item,
                'nombre' => $inventario->medicamento->descripcion ?? 'Producto desconocido',
                'stock_actual' => (int) $inventario->stock_disponible,
                'stock_minimo' => (int) $inventario->stock_minimo,
                'tipo' => 'stock_bajo'
            ];
        });
    }
    
    private function getAlertasVencimiento()
    {
        // Obtener productos que vencen en los próximos 30 días
        $fechaLimite = Carbon::now()->addDays(30);
        
        return InventarioFarmacia::with('medicamento')
            ->where('tipo_item', 'medicamento')
            ->whereNotNull('fecha_vencimiento')
            ->where('fecha_vencimiento', '<=', $fechaLimite)
            ->where('fecha_vencimiento', '>=', Carbon::now())
            ->get()
            ->map(function ($inventario) {
                $diasParaVencer = Carbon::parse($inventario->fecha_vencimiento)->diffInDays(Carbon::now());
                
                return [
                    'id' => $inventario->codigo_item,
                    'nombre' => $inventario->medicamento->descripcion ?? 'Producto desconocido',
                    'fecha_vencimiento' => $inventario->fecha_vencimiento,
                    'dias_para_vencer' => $diasParaVencer,
                    'tipo' => 'vencimiento'
                ];
            });
    }
}
