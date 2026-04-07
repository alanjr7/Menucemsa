<?php
namespace App\Http\Controllers\Farmacia;

use App\Http\Controllers\Controller;
use App\Models\VentaFarmacia;
use App\Models\InventarioFarmacia;
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
        
        // Obtener total de items en inventario_farmacia
        $totalMedicamentos = InventarioFarmacia::where('tipo_item', 'medicamento')->count();
        $medicamentosDistintos = InventarioFarmacia::where('tipo_item', 'medicamento')->distinct('codigo_item')->count();
        
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
        // Debug: Obtener todos los productos para ver qué datos hay
        $todos = InventarioFarmacia::where('tipo_item', 'medicamento')->get();
        \Log::info('Total productos: ' . $todos->count());
        \Log::info('Productos sample: ', $todos->take(3)->map(fn($p) => [
            'codigo' => $p->codigo_item,
            'stock' => $p->stock_disponible,
            'minimo' => $p->stock_minimo
        ])->toArray());
        
        // Obtener productos con stock bajo (stock actual <= stock mínimo)
        $alertas = InventarioFarmacia::with('medicamento')
            ->where('tipo_item', 'medicamento')
            ->whereColumn('stock_disponible', '<=', 'stock_minimo')
            ->where('stock_minimo', '>', 0) // Solo si tiene stock mínimo configurado
            ->get();
            
        \Log::info('Alertas encontradas: ' . $alertas->count());
        
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
