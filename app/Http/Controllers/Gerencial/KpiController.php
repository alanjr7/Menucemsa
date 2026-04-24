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
                                            ->selectRaw("SUM(total_calculado - CASE WHEN seguro_estado = 'autorizado' THEN COALESCE(seguro_monto_cobertura, 0) ELSE 0 END - total_pagado) as total")
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

        // Datos reales para gráficos
        $mesesNombres = [
            1 => 'Ene', 2 => 'Feb', 3 => 'Mar', 4 => 'Abr', 5 => 'May', 6 => 'Jun',
            7 => 'Jul', 8 => 'Ago', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dic'
        ];

        $chartMeses = [];
        $chartPacientes = [];
        $chartIngresos = [];

        for ($i = 5; $i >= 0; $i--) {
            $fecha = Carbon::now()->subMonths($i);
            $mes_inicio = $fecha->copy()->startOfMonth();
            $mes_fin = $fecha->copy()->endOfMonth();
            
            $chartMeses[] = $mesesNombres[$fecha->month];
            
            // Pacientes atendidos (Consultas en el mes)
            $chartPacientes[] = Consulta::whereBetween('fecha', [$mes_inicio, $mes_fin])->count();
            
            // Ingresos en el mes
            $ingresos = PagoCuenta::whereBetween('created_at', [$mes_inicio, $mes_fin])->sum('monto');
            $chartIngresos[] = round($ingresos, 2);
        }

        // Actividad Reciente
        $actividades = collect();

        // Altas recientes
        $altas = Hospitalizacion::with('paciente')->where('estado', 'alta')->orderBy('updated_at', 'desc')->take(3)->get();
        foreach($altas as $a) {
            $actividades->push((object)[
                'tipo' => 'alta',
                'mensaje' => 'Alta médica: ',
                'entidad' => $a->paciente->nombre ?? 'Desconocido',
                'fecha' => $a->updated_at,
                'color' => 'green'
            ]);
        }

        // Nuevas admisiones
        $admisiones = Hospitalizacion::with('paciente')->where('estado', 'activo')->orderBy('created_at', 'desc')->take(3)->get();
        foreach($admisiones as $a) {
            $actividades->push((object)[
                'tipo' => 'admision',
                'mensaje' => 'Nueva admisión: ',
                'entidad' => $a->paciente->nombre ?? 'Desconocido',
                'fecha' => $a->created_at,
                'color' => 'blue'
            ]);
        }

        // Alertas de stock
        $medicamentos = AlmacenMedicamento::whereColumn('cantidad', '<=', 'stock_minimo')->where('activo', true)->orderBy('updated_at', 'desc')->take(3)->get();
        foreach($medicamentos as $m) {
            $actividades->push((object)[
                'tipo' => 'alerta',
                'mensaje' => 'Alerta: Stock bajo en Farmacia - ',
                'entidad' => $m->nombre,
                'fecha' => $m->updated_at,
                'color' => 'orange'
            ]);
        }
        
        $actividades = $actividades->sortByDesc('fecha')->take(5);

        return view('gerencial.kpis', compact('kpis', 'chartMeses', 'chartPacientes', 'chartIngresos', 'actividades'));
    }
}
