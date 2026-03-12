<?php
namespace App\Http\Controllers\Farmacia;

use App\Http\Controllers\Controller;
use App\Models\VentaFarmacia;
use App\Models\DetalleMedicamentos;
use App\Models\Medicamentos;
use App\Models\Inventario;
use App\Models\Cliente;
use Carbon\Carbon;

class FarmaciaDashboardController extends Controller
{
    public function index()
    {
        // Obtener estadísticas de ventas del día
        $hoy = Carbon::today();
        $ventasHoy = VentaFarmacia::whereDate('FECHA_VENTA', $hoy)->count();
        $ingresosHoy = VentaFarmacia::whereDate('FECHA_VENTA', $hoy)->sum('TOTAL');
        
        // Obtener total de medicamentos en stock
        $totalMedicamentos = DetalleMedicamentos::count();
        $medicamentosDistintos = Medicamentos::count();
        
        // Obtener alertas de stock (medicamentos con bajo stock)
        $alertasStock = $this->getAlertasStock();
        
        // Obtener alertas de vencimiento próximo
        $alertasVencimiento = $this->getAlertasVencimiento();
        
        // Obtener total de ventas históricas
        $totalVentas = VentaFarmacia::count();
        
        // Obtener últimas ventas
        $ultimasVentas = VentaFarmacia::with(['detalles'])
            ->orderBy('FECHA_VENTA', 'desc')
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
        // Obtener productos con stock bajo (stock actual <= stock mínimo)
        return Inventario::with(['medicamento'])
            ->whereRaw('CAST(STOCK_DISPONIBLE AS UNSIGNED) <= CAST(STOCK_MINIMO AS UNSIGNED)')
            ->get()
            ->map(function ($inventario) {
                return [
                    'id' => $inventario->ID,
                    'nombre' => $inventario->medicamento->DESCRIPCION ?? 'Producto desconocido',
                    'stock_actual' => (int) $inventario->STOCK_DISPONIBLE,
                    'stock_minimo' => (int) $inventario->STOCK_MINIMO,
                    'tipo' => 'stock_bajo'
                ];
            });
    }
    
    private function getAlertasVencimiento()
    {
        // Obtener productos que vencen en los próximos 30 días
        $fechaLimite = Carbon::now()->addDays(30);
        
        return DetalleMedicamentos::with('medicamento')
            ->whereNotNull('FECHA_VENCIMIENTO')
            ->where('FECHA_VENCIMIENTO', '<=', $fechaLimite)
            ->where('FECHA_VENCIMIENTO', '>=', Carbon::now())
            ->get()
            ->map(function ($detalle) {
                $diasParaVencer = Carbon::parse($detalle->FECHA_VENCIMIENTO)->diffInDays(Carbon::now());
                
                return [
                    'id' => $detalle->CODIGO_MEDICAMENTOS,
                    'nombre' => $detalle->medicamento->DESCRIPCION ?? 'Producto desconocido',
                    'fecha_vencimiento' => $detalle->FECHA_VENCIMIENTO,
                    'dias_para_vencer' => $diasParaVencer,
                    'tipo' => 'vencimiento'
                ];
            });
    }
}
