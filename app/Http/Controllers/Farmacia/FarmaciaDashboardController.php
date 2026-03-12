<?php
namespace App\Http\Controllers\Farmacia;

use App\Http\Controllers\Controller;
use App\Models\VentaFarmacia;
use App\Models\DetalleMedicamentos;
use App\Models\Medicamentos;
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
        $alertasStock = 0; // Implementar lógica de stock bajo
        
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
            'totalVentas',
            'ultimasVentas'
        ));
    }
}
