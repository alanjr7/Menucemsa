<?php

namespace App\Http\Controllers;

use App\Models\Cama;
use App\Models\Emergency;
use App\Models\Habitacion;
use App\Models\Paciente;
use App\Services\CuentaCobroService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InternacionHabitacionUsoController extends Controller
{
    public function index(Request $request): View
    {
        $query = Paciente::with([
                'seguro',
                'consultas'       => fn($q) => $q->with('caja')->orderBy('created_at', 'desc')->limit(1),
                'hospitalizaciones' => fn($q) => $q->orderBy('created_at', 'desc')->limit(1),
                'emergencias'     => fn($q) => $q->orderBy('created_at', 'desc')->limit(1),
            ])
            ->whereHas('registro');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(fn($q) => $q
                ->where('nombre', 'LIKE', "%{$search}%")
                ->orWhere('ci', 'LIKE', "%{$search}%")
                ->orWhereHas('registro', fn($rq) => $rq->where('codigo', 'LIKE', "%{$search}%"))
            );
        }

        $pacientes = $query->orderBy('created_at', 'desc')->paginate(15);

        $emergencyQuery = Emergency::where('is_temp_id', true)
            ->whereIn('status', ['recibido', 'en_evaluacion', 'estabilizado']);

        if ($request->filled('search')) {
            $search = $request->search;
            $emergencyQuery->where(fn($q) => $q
                ->where('temp_id', 'LIKE', "%{$search}%")
                ->orWhere('code', 'LIKE', "%{$search}%")
            );
        }

        $pacientesTemporales = $emergencyQuery->orderBy('created_at', 'desc')->get()
            ->map(fn($emergency) => (object)[
                'ci'               => $emergency->temp_id,
                'nombre'           => 'Paciente Temporal - Emergencia',
                'is_temporal'      => true,
                'emergency_id'     => $emergency->id,
                'emergency_code'   => $emergency->code,
                'emergency_status' => $emergency->status,
                'created_at'       => $emergency->created_at,
                'seguro'           => null,
                'consultas'        => collect(),
                'hospitalizaciones'=> collect(),
            ]);

        $todosPacientes = collect($pacientes->items())->merge($pacientesTemporales)->sortByDesc('created_at');

        $page    = $request->get('page', 1);
        $perPage = 15;
        $pacientes = new \Illuminate\Pagination\LengthAwarePaginator(
            $todosPacientes->forPage($page, $perPage)->values(),
            $todosPacientes->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $pacientes->getCollection()->transform(function ($paciente) {
            if (!($paciente instanceof Paciente)) {
                return $paciente;
            }
            $paciente->tipo_ingreso = $this->determinarTipoIngreso($paciente);
            return $paciente;
        });

        $habitaciones = Habitacion::with(['camas' => fn($q) => $q->orderBy('nro')])
            ->orderBy('id')
            ->get();

        return view('internacion-staff.habitaciones.registro-uso', compact('pacientes', 'habitaciones'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'paciente_ci'  => ['required'],
            'habitacion_id'=> ['required', 'exists:habitaciones,id'],
            'cama_id'      => ['required', 'exists:camas,id'],
            'fecha_inicio' => ['required', 'date'],
            'fecha_fin'    => ['required', 'date', 'after:fecha_inicio'],
        ]);

        $cama       = Cama::with('habitacion')->findOrFail($data['cama_id']);
        $inicio     = Carbon::parse($data['fecha_inicio']);
        $fin        = Carbon::parse($data['fecha_fin']);
        $totalMin   = (string) $inicio->diffInMinutes($fin);
        $horasExact = bcdiv($totalMin, '60', 6);
        $diasExact  = bcdiv($horasExact, '24', 6);
        $dias       = bccomp($horasExact, '8', 6) < 0 ? 0 : max(1, (int) bcceil($diasExact));
        $precio     = (string) $cama->precio_por_dia;
        $costo      = bcmul((string) $dias, $precio, 2);

        if ($dias === 0) {
            return redirect()->back()
                ->with('error', 'Se requieren al menos 8 horas para registrar un día de estadía.');
        }

        $diasLabel = $dias === 1 ? '1 día' : "{$dias} días";

        $paciente = Paciente::where('ci', $data['paciente_ci'])->first();
        $seguroId = $paciente?->seguro_id ?? null;

        $cuenta = CuentaCobroService::obtenerOCrearCuentaMaestra(
            $data['paciente_ci'],
            'internacion',
            $seguroId
        );

        $descripcion = 'Estadía — Habitación ' . $cama->habitacion->id . ', Cama ' . $cama->nro
            . ($cama->tipo ? ' (' . $cama->tipo . ')' : '')
            . ' — ' . $diasLabel;

        CuentaCobroService::agregarCargo(
            $cuenta->id,
            'estadia',
            $descripcion,
            $precio,
            $dias,
            null,
            null,
            null,
            'internacion',
            auth()->id()
        );

        return redirect()->route('internacion-staff.habitaciones.registro-uso')
            ->with('success', "Estadía registrada: {$dias} día(s)");
    }

    private function determinarTipoIngreso(Paciente $paciente): string
    {
        $consulta       = $paciente->consultas->first();
        $emergencia     = $paciente->emergencias->first();
        $hospitalizacion = $paciente->hospitalizaciones->first();

        $fechaMasReciente = null;
        $tipoIngreso      = 'otro';

        if ($consulta) {
            $fecha = $consulta->created_at ?? $consulta->fecha;
            if ($fechaMasReciente === null || $fecha > $fechaMasReciente) {
                $fechaMasReciente = $fecha;
                $tipoIngreso = $consulta->tipo === 'enfermeria' ? 'enfermeria' : 'consulta_externa';
            }
        }
        if ($emergencia) {
            $fecha = $emergencia->created_at;
            if ($fechaMasReciente === null || $fecha > $fechaMasReciente) {
                $fechaMasReciente = $fecha;
                $tipoIngreso = 'emergencia';
            }
        }
        if ($hospitalizacion) {
            $fecha = $hospitalizacion->created_at ?? $hospitalizacion->fecha_ingreso;
            if ($fechaMasReciente === null || $fecha > $fechaMasReciente) {
                $tipoIngreso = 'internacion';
            }
        }

        return $tipoIngreso;
    }
}
