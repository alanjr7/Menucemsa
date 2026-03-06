<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CajaFarmacia;
use Illuminate\View\View;

class CajaController extends Controller
{
    public function index(): View
    {
        $fecha = now()->startOfDay();

        $movimientos = CajaFarmacia::query()
            ->whereDate('FECHA', $fecha)
            ->orderByDesc('FECHA')
            ->limit(20)
            ->get();

        $ingresos = $movimientos->where('TOTAL', '>=', 0);
        $egresos = $movimientos->where('TOTAL', '<', 0);

        $metodosPago = [
            'EFECTIVO' => 0,
            'TARJETA' => 0,
            'TRANSFERENCIA' => 0,
        ];

        return view('admin.caja', [
            'fecha' => $fecha,
            'movimientos' => $movimientos,
            'resumen' => [
                'ingresos' => $ingresos->sum('TOTAL'),
                'ingresos_count' => $ingresos->count(),
                'egresos' => abs($egresos->sum('TOTAL')),
                'egresos_count' => $egresos->count(),
                'saldo' => $movimientos->sum('TOTAL'),
                'pendientes_monto' => 0,
                'pendientes_count' => 0,
            ],
            'metodosPago' => $metodosPago,
        ]);
    }
}
