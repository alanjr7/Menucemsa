<?php

namespace App\Http\Controllers\Gerencial;

use App\Http\Controllers\Controller;
use App\Models\Consulta;
use App\Models\Emergency;
use App\Models\CitaQuirurgica;
use App\Models\CuentaCobro;
use App\Models\PagoCuenta;
use App\Models\Paciente;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportesController extends Controller
{
    public function index(Request $request)
    {
        $desde = $request->get('desde', Carbon::now()->startOfMonth()->toDateString());
        $hasta = $request->get('hasta', Carbon::now()->toDateString());

        // Ingresos por tipo de atención
        $ingresosPorTipo = CuentaCobro::whereIn('estado', ['pagado', 'parcial'])
            ->whereBetween('created_at', [$desde . ' 00:00:00', $hasta . ' 23:59:59'])
            ->selectRaw('tipo_atencion, COUNT(*) as total_atenciones, SUM(total_pagado) as total_ingresos')
            ->groupBy('tipo_atencion')
            ->get();

        // Ingresos por día (últimos 30 días o rango)
        $ingresosPorDia = PagoCuenta::whereBetween('created_at', [$desde . ' 00:00:00', $hasta . ' 23:59:59'])
            ->selectRaw('DATE(created_at) as fecha, SUM(monto) as total')
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get();

        // Top 5 médicos por consultas
        $topMedicos = Consulta::whereBetween('fecha', [$desde, $hasta])
            ->selectRaw('ci_medico, COUNT(*) as total')
            ->groupBy('ci_medico')
            ->orderByDesc('total')
            ->limit(5)
            ->with('medico.user')
            ->get();

        // Emergencias por estado
        $emergenciasPorEstado = Emergency::whereBetween('created_at', [$desde . ' 00:00:00', $hasta . ' 23:59:59'])
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->get();

        // Resumen financiero
        $resumen = [
            'total_cobrado'   => PagoCuenta::whereBetween('created_at', [$desde . ' 00:00:00', $hasta . ' 23:59:59'])->sum('monto'),
            'total_pendiente' => CuentaCobro::whereIn('estado', ['pendiente', 'parcial'])
                                    ->selectRaw('SUM(total_calculado - total_pagado) as total')
                                    ->value('total') ?? 0,
            'total_pacientes' => Paciente::whereBetween('created_at', [$desde . ' 00:00:00', $hasta . ' 23:59:59'])->count(),
            'total_cirugias'  => CitaQuirurgica::whereBetween('fecha', [$desde, $hasta])->count(),
        ];

        return view('gerencial.reportes', compact(
            'ingresosPorTipo', 'ingresosPorDia', 'topMedicos',
            'emergenciasPorEstado', 'resumen', 'desde', 'hasta'
        ));
    }
}
