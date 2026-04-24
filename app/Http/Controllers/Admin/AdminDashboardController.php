<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Paciente;
use App\Models\Consulta;
use App\Models\CitaQuirurgica;
use App\Models\CuentaCobro;
use App\Models\Emergency;
use App\Models\UtiAdmission;
use App\Models\Hospitalizacion;
use App\Models\UtiBed;
use App\Models\AlmacenMedicamento;
use App\Models\ActivityLog;
use App\Models\InventarioFarmacia;

class AdminDashboardController extends Controller
{
    /**
     * Dashboard principal del administrador con datos reales
     */
    public function index(): View
    {
        $stats = $this->getStats();
        $alertas = $this->getAlertas();
        $actividadReciente = $this->getActividadReciente();
        $chartData = $this->getChartData();

        return view('dashboard', compact('stats', 'alertas', 'actividadReciente', 'chartData'));
    }

    /**
     * Obtener estadísticas principales
     */
    private function getStats(): array
    {
        $hoy = now()->toDateString();
        $inicioMes = now()->startOfMonth()->toDateString();
        $inicioMesAnterior = now()->subMonth()->startOfMonth()->toDateString();
        $finMesAnterior = now()->subMonth()->endOfMonth()->toDateString();

        // Pacientes
        $totalPacientes = Paciente::count();
        $pacientesNuevosMes = Paciente::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        $pacientesNuevosMesAnterior = Paciente::whereBetween('created_at', [$inicioMesAnterior, $finMesAnterior])
            ->count();
        $cambioPacientes = $this->calcularCambioPorcentaje($pacientesNuevosMes, $pacientesNuevosMesAnterior);

        // Consultas
        $consultasHoy = Consulta::whereDate('fecha', $hoy)->count();
        $consultasMes = Consulta::whereMonth('fecha', now()->month)
            ->whereYear('fecha', now()->year)
            ->count();
        $consultasMesAnterior = Consulta::whereBetween('fecha', [$inicioMesAnterior, $finMesAnterior])
            ->count();
        $cambioConsultas = $this->calcularCambioPorcentaje($consultasMes, $consultasMesAnterior);

        // Facturación (ingresos del día)
        $ingresosHoy = CuentaCobro::whereDate('created_at', $hoy)
            ->whereIn('estado', ['pagado', 'parcial'])
            ->sum('total_pagado') ?? 0;
        
        $ingresosMes = CuentaCobro::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->whereIn('estado', ['pagado', 'parcial'])
            ->sum('total_pagado') ?? 0;
            
        $ingresosMesAnterior = CuentaCobro::whereBetween('created_at', [$inicioMesAnterior, $finMesAnterior])
            ->whereIn('estado', ['pagado', 'parcial'])
            ->sum('total_pagado') ?? 0;
        $cambioIngresos = $this->calcularCambioPorcentaje($ingresosMes, $ingresosMesAnterior);

        // Cirugías
        $cirugiasHoy = CitaQuirurgica::whereDate('fecha', $hoy)
            ->whereIn('estado', ['programada', 'en_curso', 'finalizada'])
            ->count();
        $cirugiasMes = CitaQuirurgica::whereMonth('fecha', now()->month)
            ->whereYear('fecha', now()->year)
            ->where('estado', 'finalizada')
            ->count();
        $cirugiasMesAnterior = CitaQuirurgica::whereBetween('fecha', [$inicioMesAnterior, $finMesAnterior])
            ->where('estado', 'finalizada')
            ->count();
        $cambioCirugias = $this->calcularCambioPorcentaje($cirugiasMes, $cirugiasMesAnterior);

        // Estadísticas adicionales
        $emergenciasActivas = Emergency::whereIn('status', ['recibido', 'en_evaluacion', 'estabilizado'])->count();
        $pacientesUTI = UtiAdmission::where('estado', 'activo')->count();
        $pacientesHospitalizados = Hospitalizacion::where('estado', 'activo')->count();
        
        // Ocupación UTI
        $totalCamasUTI = UtiBed::where('activa', true)->count();
        $camasOcupadasUTI = UtiBed::where('status', 'ocupada')->where('activa', true)->count();
        $ocupacionUTI = $totalCamasUTI > 0 ? round(($camasOcupadasUTI / $totalCamasUTI) * 100, 1) : 0;

        return [
            'pacientes' => [
                'total' => $totalPacientes,
                'nuevos_mes' => $pacientesNuevosMes,
                'cambio_porcentaje' => $cambioPacientes,
            ],
            'consultas' => [
                'hoy' => $consultasHoy,
                'mes' => $consultasMes,
                'cambio_porcentaje' => $cambioConsultas,
            ],
            'facturacion' => [
                'hoy' => $ingresosHoy,
                'mes' => $ingresosMes,
                'cambio_porcentaje' => $cambioIngresos,
            ],
            'cirugias' => [
                'hoy' => $cirugiasHoy,
                'mes' => $cirugiasMes,
                'cambio_porcentaje' => $cambioCirugias,
            ],
            'hospitalizacion' => [
                'emergencias_activas' => $emergenciasActivas,
                'pacientes_uti' => $pacientesUTI,
                'pacientes_hospitalizados' => $pacientesHospitalizados,
                'ocupacion_uti' => $ocupacionUTI,
                'camas_uti_total' => $totalCamasUTI,
                'camas_uti_ocupadas' => $camasOcupadasUTI,
            ],
        ];
    }

    /**
     * Calcular cambio porcentual entre dos períodos
     */
    private function calcularCambioPorcentaje(float $actual, float $anterior): float
    {
        if ($anterior == 0) {
            return $actual > 0 ? 100 : 0;
        }
        return round((($actual - $anterior) / $anterior) * 100, 1);
    }

    /**
     * Obtener alertas del sistema
     */
    private function getAlertas(): array
    {
        $alertas = [];

        // Medicamentos con stock bajo (menor al mínimo)
        $stockBajo = InventarioFarmacia::whereColumn('stock_disponible', '<', 'stock_minimo')
            ->orWhere(function ($query) {
                $query->where('stock_disponible', '<', 10)
                    ->whereNull('stock_minimo');
            })
            ->count();

        if ($stockBajo > 0) {
            $alertas[] = [
                'tipo' => 'stock_bajo',
                'nivel' => 'warning',
                'titulo' => 'Stock bajo en Farmacia',
                'mensaje' => "{$stockBajo} productos con stock por debajo del mínimo",
                'icono' => 'exclamation-triangle',
            ];
        }

        // Medicamentos por vencer (próximos 30 días)
        $fechaLimite = now()->addDays(30)->toDateString();
        $porVencer = AlmacenMedicamento::whereNotNull('fecha_vencimiento')
            ->whereDate('fecha_vencimiento', '<=', $fechaLimite)
            ->whereDate('fecha_vencimiento', '>=', now()->toDateString())
            ->count();

        if ($porVencer > 0) {
            $alertas[] = [
                'tipo' => 'vencimiento',
                'nivel' => 'danger',
                'titulo' => 'Vencimientos próximos',
                'mensaje' => "{$porVencer} medicamentos próximos a vencer (30 días)",
                'icono' => 'clock',
            ];
        }

        // Cuentas pendientes de cobro
        $cuentasPendientes = CuentaCobro::whereIn('estado', ['pendiente', 'parcial'])->count();
        $montoPendiente = CuentaCobro::whereIn('estado', ['pendiente', 'parcial'])
            ->selectRaw("SUM(total_calculado - CASE WHEN seguro_estado = 'autorizado' THEN COALESCE(seguro_monto_cobertura, 0) ELSE 0 END - total_pagado) as pendiente")
            ->first()->pendiente ?? 0;

        if ($cuentasPendientes > 0) {
            $alertas[] = [
                'tipo' => 'cuentas_pendientes',
                'nivel' => 'warning',
                'titulo' => 'Cuentas pendientes de cobro',
                'mensaje' => "{$cuentasPendientes} cuentas pendientes ($" . number_format($montoPendiente, 2) . ")",
                'icono' => 'credit-card',
            ];
        }

        // Emergencias sin atención prolongada (más de 30 minutos)
        $tiempoLimite = now()->subMinutes(30);
        $emergenciasDemora = Emergency::whereIn('status', ['recibido', 'en_evaluacion'])
            ->where('created_at', '<', $tiempoLimite)
            ->count();

        if ($emergenciasDemora > 0) {
            $alertas[] = [
                'tipo' => 'emergencias_demora',
                'nivel' => 'danger',
                'titulo' => 'Emergencias con demora',
                'mensaje' => "{$emergenciasDemora} pacientes en espera mas de 30 minutos",
                'icono' => 'ambulance',
            ];
        }

        // Pacientes en UTI sin registro clínico hoy
        $hoy = now()->toDateString();
        $admisionesUTI = UtiAdmission::where('estado', 'activo')->pluck('id');
        $conRegistroHoy = DB::table('uti_daily_records')
            ->where('fecha', $hoy)
            ->whereIn('uti_admission_id', $admisionesUTI)
            ->pluck('uti_admission_id');
        $sinRegistroHoy = $admisionesUTI->diff($conRegistroHoy)->count();

        if ($sinRegistroHoy > 0) {
            $alertas[] = [
                'tipo' => 'uti_sin_registro',
                'nivel' => 'warning',
                'titulo' => 'UTI: Sin registro clínico',
                'mensaje' => "{$sinRegistroHoy} pacientes sin registro del día",
                'icono' => 'heart-pulse',
            ];
        }

        return $alertas;
    }

    /**
     * Obtener actividad reciente del sistema
     */
    private function getActividadReciente(): array
    {
        $actividades = ActivityLog::with('user')
            ->latest()
            ->limit(10)
            ->get();

        $resultado = [];

        foreach ($actividades as $actividad) {
            $resultado[] = [
                'tipo' => $this->getTipoActividad($actividad->action),
                'descripcion' => $actividad->description,
                'usuario' => $actividad->user?->name ?? 'Sistema',
                'tiempo' => $actividad->created_at->diffForHumans(),
                'color' => $this->getColorActividad($actividad->action),
            ];
        }

        // Si no hay actividades, generar algunas del sistema reciente
        if (empty($resultado)) {
            $resultado = $this->getActividadSistemaDefault();
        }

        return $resultado;
    }

    /**
     * Determinar tipo de actividad
     */
    private function getTipoActividad(string $action): string
    {
        return match($action) {
            'created' => 'nuevo',
            'updated' => 'actualizacion',
            'deleted' => 'alerta',
            'login' => 'sesion',
            'pago' => 'pago',
            default => 'info',
        };
    }

    /**
     * Determinar color de actividad
     */
    private function getColorActividad(string $action): string
    {
        return match($action) {
            'created' => 'green',
            'updated' => 'blue',
            'deleted' => 'red',
            'login' => 'purple',
            'pago' => 'emerald',
            default => 'gray',
        };
    }

    /**
     * Actividad por defecto cuando no hay logs
     */
    private function getActividadSistemaDefault(): array
    {
        $hoy = now();

        return [
            [
                'tipo' => 'info',
                'descripcion' => 'Sistema HIS/CEMSA operativo',
                'usuario' => 'Sistema',
                'tiempo' => 'Ahora',
                'color' => 'blue',
            ],
        ];
    }

    /**
     * Obtener datos para gráficos
     */
    private function getChartData(): array
    {
        // Pacientes atendidos por mes (últimos 6 meses)
        $pacientesPorMes = [];
        $ingresosPorMes = [];
        $meses = [];

        for ($i = 5; $i >= 0; $i--) {
            $fecha = now()->subMonths($i);
            $meses[] = $fecha->format('M');

            // Contar pacientes únicos atendidos (consultas + emergencias)
            $pacientesConsultas = Consulta::whereMonth('fecha', $fecha->month)
                ->whereYear('fecha', $fecha->year)
                ->distinct('ci_paciente')
                ->count('ci_paciente');

            $pacientesEmergencias = Emergency::whereMonth('created_at', $fecha->month)
                ->whereYear('created_at', $fecha->year)
                ->distinct('patient_id')
                ->count('patient_id');

            $pacientesPorMes[] = $pacientesConsultas + $pacientesEmergencias;

            // Ingresos por mes
            $ingresos = CuentaCobro::whereMonth('created_at', $fecha->month)
                ->whereYear('created_at', $fecha->year)
                ->whereIn('estado', ['pagado', 'parcial'])
                ->sum('total_pagado') ?? 0;

            $ingresosPorMes[] = round($ingresos, 2);
        }

        return [
            'pacientes' => [
                'labels' => $meses,
                'data' => $pacientesPorMes,
            ],
            'ingresos' => [
                'labels' => $meses,
                'data' => $ingresosPorMes,
            ],
        ];
    }
}
