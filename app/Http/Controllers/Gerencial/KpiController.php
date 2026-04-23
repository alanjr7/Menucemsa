<?php

namespace App\Http\Controllers\Gerencial;

use App\Http\Controllers\Controller;
use App\Models\Paciente;
use App\Models\Consulta;
use App\Models\Emergency;
use App\Models\Hospitalizacion;
use App\Models\CitaQuirurgica;
use App\Models\UtiAdmission;
use App\Models\PagoCuenta;
use App\Models\CuentaCobro;
use App\Models\AlmacenMedicamento;
use Carbon\Carbon;

class KpiController extends Controller
{
    public function index()
    {
        $hoy = Carbon::today();
        $mesActual = Carbon::now()->startOfMonth();
        $mesAnterior = Carbon::now()->subMonth()->startOfMonth();
        $finMesAnterior = Carbon::now()->subMonth()->endOfMonth();

        $kpis = [
            'pacientes_activos'       => Paciente::count(),
            'consultas_hoy'           => Consulta::whereDate('fecha', $hoy)->count(),
            'consultas_mes'           => Consulta::whereDate('fecha', '>=', $mesActual)->count(),
            'emergencias_activas'     => Emergency::whereNotIn('status', ['alta', 'fallecido'])->count(),
            'emergencias_hoy'         => Emergency::whereDate('created_at', $hoy)->count(),
            'hospitalizados'          => Hospitalizacion::where('estado', 'activo')->count(),
            'cirugias_hoy'            => CitaQuirurgica::whereDate('fecha', $hoy)->count(),
            'cirugias_mes'            => CitaQuirurgica::whereDate('fecha', '>=', $mesActual)->count(),
            'pacientes_uti'           => UtiAdmission::where('estado', 'activo')->count(),
            'ingresos_hoy'            => PagoCuenta::whereDate('created_at', $hoy)->sum('monto'),
            'ingresos_mes'            => PagoCuenta::whereDate('created_at', '>=', $mesActual)->sum('monto'),
            'ingresos_mes_anterior'   => PagoCuenta::whereBetween('created_at', [$mesAnterior, $finMesAnterior])->sum('monto'),
            'cuentas_pendientes'      => CuentaCobro::whereIn('estado', ['pendiente', 'parcial'])->count(),
            'monto_pendiente'         => CuentaCobro::whereIn('estado', ['pendiente', 'parcial'])
                                            ->selectRaw('SUM(total_calculado - total_pagado) as total')
                                            ->value('total') ?? 0,
            'medicamentos_bajo_stock' => AlmacenMedicamento::where('activo', true)
                                            ->whereColumn('cantidad', '<=', 'stock_minimo')->count(),
            'medicamentos_vencidos'   => AlmacenMedicamento::where('activo', true)
                                            ->whereDate('fecha_vencimiento', '<', $hoy)->count(),
        ];

        // Calcular variación % ingresos mes vs mes anterior
        $kpis['variacion_ingresos'] = $kpis['ingresos_mes_anterior'] > 0
            ? round((($kpis['ingresos_mes'] - $kpis['ingresos_mes_anterior']) / $kpis['ingresos_mes_anterior']) * 100, 1)
            : 0;

        return view('gerencial.kpis', compact('kpis'));
    }
}
