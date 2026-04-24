<?php

namespace App\Http\Controllers\Gerencial;

use App\Http\Controllers\Controller;
use App\Models\Consulta;
use App\Models\Emergency;
use App\Models\CitaQuirurgica;
use App\Models\CuentaCobro;
use App\Models\PagoCuenta;
use App\Models\Paciente;
use App\Models\Hospitalizacion;
use App\Models\Cama;
use App\Models\Quirofano;
use App\Models\InventarioFarmacia;
use App\Models\Medico;
use App\Models\CajaDiaria;
use App\Models\Especialidad;
use Carbon\Carbon;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

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
                                    ->selectRaw("SUM(total_calculado - CASE WHEN seguro_estado = 'autorizado' THEN COALESCE(seguro_monto_cobertura, 0) ELSE 0 END - total_pagado) as total")
                                    ->value('total') ?? 0,
            'total_pacientes' => Paciente::whereBetween('created_at', [$desde . ' 00:00:00', $hasta . ' 23:59:59'])->count(),
            'total_cirugias'  => CitaQuirurgica::whereBetween('fecha', [$desde, $hasta])->count(),
        ];

        // Datos para Gráfico 1: Atenciones por Mes (Últimos 6 meses hasta la fecha 'hasta')
        $mesesNombres = [
            1 => 'Ene', 2 => 'Feb', 3 => 'Mar', 4 => 'Abr', 5 => 'May', 6 => 'Jun',
            7 => 'Jul', 8 => 'Ago', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dic'
        ];

        $chartAtencionesLabels = [];
        $chartConsultas = [];
        $chartEmergencias = [];
        $chartCirugias = [];

        $fechaHasta = Carbon::parse($hasta);
        for ($i = 5; $i >= 0; $i--) {
            $mes = $fechaHasta->copy()->subMonths($i);
            $mes_inicio = $mes->copy()->startOfMonth();
            $mes_fin = $mes->copy()->endOfMonth();
            
            $chartAtencionesLabels[] = $mesesNombres[$mes->month];
            
            $chartConsultas[] = Consulta::whereBetween('fecha', [$mes_inicio, $mes_fin])->count();
            $chartEmergencias[] = Emergency::whereBetween('created_at', [$mes_inicio, $mes_fin])->count();
            $chartCirugias[] = CitaQuirurgica::whereBetween('fecha', [$mes_inicio, $mes_fin])->count();
        }

        // Datos para Gráfico 2: Consultas por Especialidad (en el rango de fechas)
        $consultasPorEspecialidad = Consulta::whereBetween('fecha', [$desde, $hasta])
            ->join('medicos', 'consultas.ci_medico', '=', 'medicos.ci')
            ->join('especialidades', 'medicos.codigo_especialidad', '=', 'especialidades.codigo')
            ->selectRaw('especialidades.nombre as especialidad, count(*) as total')
            ->groupBy('especialidades.nombre')
            ->get();
            
        $chartEspecialidadesLabels = $consultasPorEspecialidad->pluck('especialidad')->toArray();
        $chartEspecialidadesData = $consultasPorEspecialidad->pluck('total')->toArray();

        // Si no hay datos, ponemos algo por defecto para que el gráfico no se vea vacío o feo, o lo dejamos vacío
        if(empty($chartEspecialidadesLabels)) {
            $chartEspecialidadesLabels = ['Sin datos'];
            $chartEspecialidadesData = [1];
        }

        return view('gerencial.reportes', compact(
            'ingresosPorTipo', 'ingresosPorDia', 'topMedicos',
            'emergenciasPorEstado', 'resumen', 'desde', 'hasta',
            'chartAtencionesLabels', 'chartConsultas', 'chartEmergencias', 'chartCirugias',
            'chartEspecialidadesLabels', 'chartEspecialidadesData'
        ));
    }

    public function data(Request $request)
    {
        $tipo = $request->get('tipo');
        $desde = $request->get('desde', Carbon::now()->startOfMonth()->toDateString());
        $hasta = $request->get('hasta', Carbon::now()->toDateString());

        $data = match ($tipo) {
            'atenciones_especialidad' => $this->atencionesEspecialidadData($desde, $hasta),
            'pacientes_hospitalizados' => $this->pacientesHospitalizadosData($desde, $hasta),
            'cirugias_realizadas' => $this->cirugiasRealizadasData($desde, $hasta),
            'emergencias' => $this->emergenciasData($desde, $hasta),
            'ingresos_servicio' => $this->ingresosServicioData($desde, $hasta),
            'cuentas_cobrar' => $this->cuentasCobrarData($desde, $hasta),
            'morosidad' => $this->morosidadData($desde, $hasta),
            'cierre_caja' => $this->cierreCajaData($desde, $hasta),
            'uso_quirofanos' => $this->usoQuirofanosData($desde, $hasta),
            'ocupacion_camas' => $this->ocupacionCamasData($desde, $hasta),
            'stock_farmacia' => $this->stockFarmaciaData($desde, $hasta),
            'productividad_medica' => $this->productividadMedicaData($desde, $hasta),
            default => null,
        };

        if (!$data) {
            return response()->json(['error' => 'Tipo de reporte no válido'], 400);
        }

        return response()->json($data);
    }

    private function atencionesEspecialidadData($desde, $hasta)
    {
        $rows = Consulta::whereBetween('fecha', [$desde, $hasta])
            ->with(['paciente', 'medico.user', 'especialidad'])
            ->orderBy('fecha', 'desc')
            ->get()
            ->map(fn ($c) => [
                'fecha' => $c->fecha->format('d/m/Y'),
                'hora' => $c->hora,
                'paciente' => $c->paciente?->nombre ?? 'N/A',
                'ci' => $c->paciente?->ci ?? 'N/A',
                'medico' => $c->medico?->nombre ?? 'N/A',
                'especialidad' => $c->especialidad?->nombre ?? 'N/A',
                'estado_pago' => $c->estado_pago ? 'Pagado' : 'Pendiente',
            ])->toArray();

        return [
            'title' => 'Atenciones por Especialidad',
            'columns' => [
                ['key' => 'fecha', 'label' => 'Fecha'],
                ['key' => 'hora', 'label' => 'Hora'],
                ['key' => 'paciente', 'label' => 'Paciente'],
                ['key' => 'ci', 'label' => 'CI'],
                ['key' => 'medico', 'label' => 'Médico'],
                ['key' => 'especialidad', 'label' => 'Especialidad'],
                ['key' => 'estado_pago', 'label' => 'Estado Pago'],
            ],
            'rows' => $rows,
        ];
    }

    private function pacientesHospitalizadosData($desde, $hasta)
    {
        $rows = Hospitalizacion::whereBetween('fecha_ingreso', [$desde . ' 00:00:00', $hasta . ' 23:59:59'])
            ->orWhere(function ($q) use ($desde, $hasta) {
                $q->whereNull('fecha_alta')->whereDate('fecha_ingreso', '<=', $hasta);
            })
            ->with(['paciente', 'medico', 'habitacion', 'cama'])
            ->orderBy('fecha_ingreso', 'desc')
            ->get()
            ->map(fn ($h) => [
                'fecha_ingreso' => $h->fecha_ingreso->format('d/m/Y H:i'),
                'fecha_alta' => $h->fecha_alta?->format('d/m/Y H:i') ?? 'Activo',
                'paciente' => $h->paciente?->nombre ?? 'N/A',
                'ci' => $h->paciente?->ci ?? 'N/A',
                'medico' => $h->medico?->nombre_completo ?? $h->medico?->nombre ?? 'N/A',
                'habitacion' => $h->habitacion?->id ?? $h->cama?->habitacion?->id ?? 'N/A',
                'cama' => $h->cama?->nro ?? 'N/A',
                'diagnostico' => $h->diagnostico ?? 'N/A',
                'estado' => $h->estado ?? 'N/A',
                'dias_estancia' => $h->getDiasEstancia(),
                'costo' => number_format($h->getCostoEstancia(), 2),
            ])->toArray();

        return [
            'title' => 'Pacientes Hospitalizados',
            'columns' => [
                ['key' => 'fecha_ingreso', 'label' => 'Ingreso'],
                ['key' => 'fecha_alta', 'label' => 'Alta'],
                ['key' => 'paciente', 'label' => 'Paciente'],
                ['key' => 'ci', 'label' => 'CI'],
                ['key' => 'medico', 'label' => 'Médico'],
                ['key' => 'habitacion', 'label' => 'Habitación'],
                ['key' => 'cama', 'label' => 'Cama'],
                ['key' => 'diagnostico', 'label' => 'Diagnóstico'],
                ['key' => 'estado', 'label' => 'Estado'],
                ['key' => 'dias_estancia', 'label' => 'Días'],
                ['key' => 'costo', 'label' => 'Costo (Bs)'],
            ],
            'rows' => $rows,
        ];
    }

    private function cirugiasRealizadasData($desde, $hasta)
    {
        $rows = CitaQuirurgica::whereBetween('fecha', [$desde, $hasta])
            ->whereIn('estado', ['finalizada', 'en_curso'])
            ->with(['paciente', 'cirujano.user', 'quirofano'])
            ->orderBy('fecha', 'desc')
            ->get()
            ->map(fn ($c) => [
                'fecha' => $c->fecha->format('d/m/Y'),
                'hora_inicio' => $c->hora_inicio_real ?? $c->hora_inicio_estimada,
                'hora_fin' => $c->hora_fin_real ?? 'N/A',
                'paciente' => $c->paciente?->nombre ?? 'N/A',
                'ci' => $c->paciente?->ci ?? 'N/A',
                'cirujano' => $c->cirujano?->nombre ?? 'N/A',
                'quirofano' => $c->quirofano?->id ?? 'N/A',
                'tipo' => $c->tipo_final ?? $c->tipo_cirugia,
                'estado' => $c->estado,
                'costo' => number_format($c->costo_final ?? $c->costo_base ?? 0, 2),
            ])->toArray();

        return [
            'title' => 'Cirugías Realizadas',
            'columns' => [
                ['key' => 'fecha', 'label' => 'Fecha'],
                ['key' => 'hora_inicio', 'label' => 'Inicio'],
                ['key' => 'hora_fin', 'label' => 'Fin'],
                ['key' => 'paciente', 'label' => 'Paciente'],
                ['key' => 'ci', 'label' => 'CI'],
                ['key' => 'cirujano', 'label' => 'Cirujano'],
                ['key' => 'quirofano', 'label' => 'Quirófano'],
                ['key' => 'tipo', 'label' => 'Tipo'],
                ['key' => 'estado', 'label' => 'Estado'],
                ['key' => 'costo', 'label' => 'Costo (Bs)'],
            ],
            'rows' => $rows,
        ];
    }

    private function emergenciasData($desde, $hasta)
    {
        $rows = Emergency::whereBetween('created_at', [$desde . ' 00:00:00', $hasta . ' 23:59:59'])
            ->with('paciente')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn ($e) => [
                'fecha' => $e->created_at->format('d/m/Y H:i'),
                'codigo' => $e->code,
                'paciente' => $e->paciente?->nombre ?? 'N/A',
                'ci' => $e->paciente?->ci ?? 'N/A',
                'tipo_ingreso' => $e->tipo_ingreso_label ?? 'N/A',
                'status' => $e->status,
                'ubicacion' => $e->ubicacion_label ?? 'N/A',
                'costo' => number_format($e->cost ?? 0, 2),
                'pagado' => $e->paid ? 'Sí' : 'No',
            ])->toArray();

        return [
            'title' => 'Emergencias',
            'columns' => [
                ['key' => 'fecha', 'label' => 'Fecha/Hora'],
                ['key' => 'codigo', 'label' => 'Código'],
                ['key' => 'paciente', 'label' => 'Paciente'],
                ['key' => 'ci', 'label' => 'CI'],
                ['key' => 'tipo_ingreso', 'label' => 'Tipo Ingreso'],
                ['key' => 'status', 'label' => 'Estado'],
                ['key' => 'ubicacion', 'label' => 'Ubicación'],
                ['key' => 'costo', 'label' => 'Costo (Bs)'],
                ['key' => 'pagado', 'label' => 'Pagado'],
            ],
            'rows' => $rows,
        ];
    }

    private function ingresosServicioData($desde, $hasta)
    {
        $rows = PagoCuenta::whereBetween('created_at', [$desde . ' 00:00:00', $hasta . ' 23:59:59'])
            ->with(['cuentaCobro'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn ($p) => [
                'fecha' => $p->created_at->format('d/m/Y H:i'),
                'cuenta' => $p->cuenta_cobro_id,
                'monto' => number_format($p->monto, 2),
                'metodo' => $p->metodo_pago_label ?? $p->metodo_pago,
                'tipo_atencion' => $p->cuentaCobro?->tipo_atencion_label ?? 'N/A',
            ])->toArray();

        $total = PagoCuenta::whereBetween('created_at', [$desde . ' 00:00:00', $hasta . ' 23:59:59'])->sum('monto');

        return [
            'title' => 'Ingresos por Servicio',
            'columns' => [
                ['key' => 'fecha', 'label' => 'Fecha'],
                ['key' => 'cuenta', 'label' => 'Cuenta'],
                ['key' => 'tipo_atencion', 'label' => 'Tipo Atención'],
                ['key' => 'metodo', 'label' => 'Método Pago'],
                ['key' => 'monto', 'label' => 'Monto (Bs)'],
            ],
            'rows' => $rows,
            'summary' => ['label' => 'Total Ingresos', 'value' => 'Bs ' . number_format($total, 2)],
        ];
    }

    private function cuentasCobrarData($desde, $hasta)
    {
        $rows = CuentaCobro::whereIn('estado', ['pendiente', 'parcial'])
            ->whereBetween('created_at', [$desde . ' 00:00:00', $hasta . ' 23:59:59'])
            ->with('paciente')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn ($c) => [
                'fecha' => $c->created_at->format('d/m/Y'),
                'cuenta' => $c->id,
                'paciente' => $c->paciente?->nombre ?? 'N/A',
                'ci' => $c->paciente?->ci ?? 'N/A',
                'tipo' => $c->tipo_atencion_label,
                'total' => number_format($c->total_calculado, 2),
                'pagado' => number_format($c->total_pagado, 2),
                'saldo' => number_format($c->saldo_pendiente, 2),
                'estado' => $c->estado_label,
            ])->toArray();

        $totalSaldo = CuentaCobro::whereIn('estado', ['pendiente', 'parcial'])
            ->whereBetween('created_at', [$desde . ' 00:00:00', $hasta . ' 23:59:59'])
            ->selectRaw("SUM(total_calculado - CASE WHEN seguro_estado = 'autorizado' THEN COALESCE(seguro_monto_cobertura, 0) ELSE 0 END - total_pagado) as total")
            ->value('total') ?? 0;

        return [
            'title' => 'Cuentas por Cobrar',
            'columns' => [
                ['key' => 'fecha', 'label' => 'Fecha'],
                ['key' => 'cuenta', 'label' => 'Cuenta'],
                ['key' => 'paciente', 'label' => 'Paciente'],
                ['key' => 'ci', 'label' => 'CI'],
                ['key' => 'tipo', 'label' => 'Tipo'],
                ['key' => 'total', 'label' => 'Total (Bs)'],
                ['key' => 'pagado', 'label' => 'Pagado (Bs)'],
                ['key' => 'saldo', 'label' => 'Saldo (Bs)'],
                ['key' => 'estado', 'label' => 'Estado'],
            ],
            'rows' => $rows,
            'summary' => ['label' => 'Total Saldo Pendiente', 'value' => 'Bs ' . number_format($totalSaldo, 2)],
        ];
    }

    private function morosidadData($desde, $hasta)
    {
        $rows = CuentaCobro::whereIn('estado', ['pendiente', 'parcial'])
            ->whereDate('created_at', '<', Carbon::now()->subDays(7)->toDateString())
            ->whereBetween('created_at', [$desde . ' 00:00:00', $hasta . ' 23:59:59'])
            ->with('paciente')
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(fn ($c) => [
                'fecha' => $c->created_at->format('d/m/Y'),
                'dias_mora' => $c->created_at->diffInDays(now()),
                'cuenta' => $c->id,
                'paciente' => $c->paciente?->nombre ?? 'N/A',
                'ci' => $c->paciente?->ci ?? 'N/A',
                'tipo' => $c->tipo_atencion_label,
                'saldo' => number_format($c->saldo_pendiente, 2),
                'estado' => $c->estado_label,
            ])->toArray();

        return [
            'title' => 'Análisis de Morosidad',
            'columns' => [
                ['key' => 'fecha', 'label' => 'Fecha'],
                ['key' => 'dias_mora', 'label' => 'Días Mora'],
                ['key' => 'cuenta', 'label' => 'Cuenta'],
                ['key' => 'paciente', 'label' => 'Paciente'],
                ['key' => 'ci', 'label' => 'CI'],
                ['key' => 'tipo', 'label' => 'Tipo'],
                ['key' => 'saldo', 'label' => 'Saldo (Bs)'],
                ['key' => 'estado', 'label' => 'Estado'],
            ],
            'rows' => $rows,
        ];
    }

    private function cierreCajaData($desde, $hasta)
    {
        $rows = CajaDiaria::whereBetween('fecha', [$desde, $hasta])
            ->with('usuario')
            ->orderBy('fecha', 'desc')
            ->get()
            ->map(fn ($c) => [
                'fecha' => $c->fecha->format('d/m/Y'),
                'estado' => $c->estado,
                'monto_inicial' => number_format($c->monto_inicial, 2),
                'ventas_efectivo' => number_format($c->ventas_efectivo, 2),
                'ventas_qr' => number_format($c->ventas_qr, 2),
                'ventas_transferencia' => number_format($c->ventas_transferencia, 2),
                'ventas_tarjeta' => number_format($c->ventas_tarjeta, 2),
                'total_ventas' => number_format($c->total_ventas, 2),
                'monto_final' => number_format($c->monto_final, 2),
                'usuario' => $c->usuario?->name ?? 'N/A',
            ])->toArray();

        return [
            'title' => 'Cierre de Caja',
            'columns' => [
                ['key' => 'fecha', 'label' => 'Fecha'],
                ['key' => 'estado', 'label' => 'Estado'],
                ['key' => 'monto_inicial', 'label' => 'Monto Inicial'],
                ['key' => 'ventas_efectivo', 'label' => 'Efectivo'],
                ['key' => 'ventas_qr', 'label' => 'QR'],
                ['key' => 'ventas_transferencia', 'label' => 'Transferencia'],
                ['key' => 'ventas_tarjeta', 'label' => 'Tarjeta'],
                ['key' => 'total_ventas', 'label' => 'Total Ventas'],
                ['key' => 'monto_final', 'label' => 'Monto Final'],
                ['key' => 'usuario', 'label' => 'Usuario'],
            ],
            'rows' => $rows,
        ];
    }

    private function usoQuirofanosData($desde, $hasta)
    {
        $rows = CitaQuirurgica::whereBetween('fecha', [$desde, $hasta])
            ->with('quirofano')
            ->selectRaw('quirofano_id, COUNT(*) as total_cirugias, SUM(costo_final) as total_recaudado')
            ->groupBy('quirofano_id')
            ->get()
            ->map(fn ($c) => [
                'quirofano' => $c->quirofano?->id ?? 'N/A',
                'tipo' => $c->quirofano?->tipo ?? 'N/A',
                'total_cirugias' => $c->total_cirugias,
                'total_recaudado' => number_format($c->total_recaudado ?? 0, 2),
            ])->toArray();

        return [
            'title' => 'Uso de Quirófanos',
            'columns' => [
                ['key' => 'quirofano', 'label' => 'Quirófano'],
                ['key' => 'tipo', 'label' => 'Tipo'],
                ['key' => 'total_cirugias', 'label' => 'Total Cirugías'],
                ['key' => 'total_recaudado', 'label' => 'Total Recaudado (Bs)'],
            ],
            'rows' => $rows,
        ];
    }

    private function ocupacionCamasData($desde, $hasta)
    {
        $total = Cama::count();
        $ocupadas = Cama::where('disponibilidad', 'ocupada')->count();
        $disponibles = $total - $ocupadas;

        $rows = Cama::with(['habitacion', 'hospitalizacionActiva.paciente'])
            ->orderBy('habitacion_id')
            ->get()
            ->map(fn ($c) => [
                'habitacion' => $c->habitacion?->id ?? 'N/A',
                'cama' => $c->nro,
                'tipo' => $c->tipo,
                'disponibilidad' => $c->disponibilidad,
                'paciente' => $c->hospitalizacionActiva?->paciente?->nombre ?? '-',
                'ci' => $c->hospitalizacionActiva?->paciente?->ci ?? '-',
            ])->toArray();

        return [
            'title' => 'Ocupación de Camas',
            'columns' => [
                ['key' => 'habitacion', 'label' => 'Habitación'],
                ['key' => 'cama', 'label' => 'Cama'],
                ['key' => 'tipo', 'label' => 'Tipo'],
                ['key' => 'disponibilidad', 'label' => 'Estado'],
                ['key' => 'paciente', 'label' => 'Paciente Actual'],
                ['key' => 'ci', 'label' => 'CI'],
            ],
            'rows' => $rows,
            'summary' => ['label' => 'Ocupación', 'value' => "{$ocupadas} / {$total} ({$disponibles} disponibles)"],
        ];
    }

    private function stockFarmaciaData($desde, $hasta)
    {
        $rows = InventarioFarmacia::with('medicamento')
            ->where(function ($q) {
                $q->whereRaw('stock_disponible <= stock_minimo')
                  ->orWhere('fecha_vencimiento', '<=', now()->addDays(30));
            })
            ->orderBy('stock_disponible', 'asc')
            ->get()
            ->map(fn ($i) => [
                'codigo' => $i->codigo_item,
                'nombre' => $i->medicamento?->nombre ?? 'N/A',
                'laboratorio' => $i->laboratorio ?? 'N/A',
                'lote' => $i->lote ?? 'N/A',
                'fecha_vencimiento' => $i->fecha_vencimiento?->format('d/m/Y') ?? 'N/A',
                'stock_minimo' => $i->stock_minimo,
                'stock_disponible' => $i->stock_disponible,
                'reposicion' => $i->reposicion ?? 0,
                'estado' => ($i->stock_disponible <= $i->stock_minimo) ? 'Stock Bajo' : ($i->fecha_vencimiento <= now()->addDays(30) ? 'Por Vencer' : 'OK'),
            ])->toArray();

        return [
            'title' => 'Stock de Farmacia',
            'columns' => [
                ['key' => 'codigo', 'label' => 'Código'],
                ['key' => 'nombre', 'label' => 'Nombre'],
                ['key' => 'laboratorio', 'label' => 'Laboratorio'],
                ['key' => 'lote', 'label' => 'Lote'],
                ['key' => 'fecha_vencimiento', 'label' => 'Vencimiento'],
                ['key' => 'stock_minimo', 'label' => 'Stock Mínimo'],
                ['key' => 'stock_disponible', 'label' => 'Stock Actual'],
                ['key' => 'reposicion', 'label' => 'Reposición'],
                ['key' => 'estado', 'label' => 'Estado'],
            ],
            'rows' => $rows,
        ];
    }

    private function productividadMedicaData($desde, $hasta)
    {
        $rows = Medico::with(['user', 'especialidad'])
            ->whereHas('consultas', function ($q) use ($desde, $hasta) {
                $q->whereBetween('fecha', [$desde, $hasta]);
            })
            ->get()
            ->map(fn ($m) => [
                'medico' => $m->nombre_completo,
                'ci' => $m->ci,
                'especialidad' => $m->especialidad?->nombre ?? 'N/A',
                'total_consultas' => $m->consultas()->whereBetween('fecha', [$desde, $hasta])->count(),
                'total_recetas' => $m->consultas()->whereBetween('fecha', [$desde, $hasta])->withCount('recetas')->get()->sum('recetas_count'),
            ])->toArray();

        return [
            'title' => 'Productividad Médica',
            'columns' => [
                ['key' => 'medico', 'label' => 'Médico'],
                ['key' => 'ci', 'label' => 'CI'],
                ['key' => 'especialidad', 'label' => 'Especialidad'],
                ['key' => 'total_consultas', 'label' => 'Consultas'],
                ['key' => 'total_recetas', 'label' => 'Recetas'],
            ],
            'rows' => $rows,
        ];
    }

    public function export(Request $request)
    {
        $tipo = $request->get('tipo');
        $desde = $request->get('desde', Carbon::now()->startOfMonth()->toDateString());
        $hasta = $request->get('hasta', Carbon::now()->toDateString());

        $data = $this->data($request)->getData(true);

        if (isset($data['error'])) {
            return redirect()->back()->with('error', $data['error']);
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setTitle($data['title']);

        $headers = array_column($data['columns'], 'label');
        $keys = array_column($data['columns'], 'key');

        // Header style
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1e40af']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ];

        foreach ($headers as $colIndex => $header) {
            $cell = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + 1) . '1';
            $sheet->setCellValue($cell, $header);
            $sheet->getStyle($cell)->applyFromArray($headerStyle);
        }

        // Rows
        foreach ($data['rows'] as $rowIndex => $row) {
            foreach ($keys as $colIndex => $key) {
                $cell = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + 1) . ($rowIndex + 2);
                $sheet->setCellValue($cell, $row[$key] ?? '');
            }
        }

        // Auto width
        foreach ($headers as $colIndex => $_) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + 1);
            $sheet->getColumnDimension($colLetter)->setAutoSize(true);
        }

        // Summary if exists
        if (isset($data['summary'])) {
            $nextRow = count($data['rows']) + 3;
            $sheet->setCellValue('A' . $nextRow, $data['summary']['label']);
            $sheet->setCellValue('B' . $nextRow, $data['summary']['value']);
            $sheet->getStyle('A' . $nextRow)->applyFromArray(['font' => ['bold' => true]]);
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = str_replace(' ', '_', $data['title']) . '_' . $desde . '_' . $hasta . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }
}
