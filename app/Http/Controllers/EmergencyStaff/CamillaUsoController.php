<?php

namespace App\Http\Controllers\EmergencyStaff;

use App\Http\Controllers\Controller;
use App\Models\Camilla;
use App\Models\CamillaUso;
use App\Models\CuentaCobroDetalle;
use App\Models\Paciente;
use App\Services\CuentaCobroService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CamillaUsoController extends Controller
{
    public function index(Request $request): View
    {
        $query = Paciente::with([
                'seguro',
                'consultas'         => fn($q) => $q->with('caja')->orderBy('created_at', 'desc')->limit(1),
                'hospitalizaciones' => fn($q) => $q->orderBy('created_at', 'desc')->limit(1),
                'emergencias'       => fn($q) => $q->orderBy('created_at', 'desc')->limit(1),
            ])
            ->whereHas('registro');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'LIKE', "%{$search}%")
                  ->orWhere('ci', 'LIKE', "%{$search}%")
                  ->orWhereHas('registro', fn($rq) => $rq->where('codigo', 'LIKE', "%{$search}%"));
            });
        }

        $tempQuery = \App\Models\Emergency::with('paciente')
            ->whereHas('paciente', fn($q) => $q->where('is_temp', true))
            ->whereIn('status', ['recibido', 'en_evaluacion', 'estabilizado']);

        if ($request->filled('search')) {
            $search = $request->search;
            $tempQuery->where(fn($q) => $q->where('code', 'LIKE', "%{$search}%")
                ->orWhereHas('paciente', fn($q2) => $q2->where('temp_code', 'LIKE', "%{$search}%")));
        }

        $pacientesTemporales = $tempQuery->orderBy('created_at', 'desc')->get()->map(fn($emergency) => (object)[
            'id'               => $emergency->paciente_id,
            'ci'               => null,
            'temp_code'        => $emergency->paciente?->temp_code,
            'nombre'           => 'Paciente Temporal - Emergencia',
            'is_temp'          => true,
            'emergency_id'     => $emergency->id,
            'emergency_code'   => $emergency->code,
            'emergency_status' => $emergency->status,
            'created_at'       => $emergency->created_at,
            'tipo_ingreso'     => 'emergencia',
            'seguro'           => null,
            'consultas'        => collect(),
        ]);

        $todosPacientes = $query->orderBy('created_at', 'desc')->get()
            ->merge($pacientesTemporales)
            ->sortByDesc('created_at');

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

        $camillas = Camilla::where('area', 'emergencia')->where('activa', true)->orderBy('nombre')->get();

        return view('emergency-staff.camillas.index', compact('pacientes', 'camillas'));
    }

    private function determinarTipoIngreso(Paciente $paciente): string
    {
        $consulta        = $paciente->consultas->first();
        $emergencia      = $paciente->emergencias->first();
        $hospitalizacion = $paciente->hospitalizaciones->first();

        $fechaMasReciente = null;
        $tipoIngreso      = 'otro';

        if ($consulta) {
            $fecha = $consulta->created_at ?? $consulta->fecha;
            if ($fechaMasReciente === null || $fecha > $fechaMasReciente) {
                $fechaMasReciente = $fecha;
                $tipoIngreso      = $consulta->tipo === 'enfermeria' ? 'enfermeria' : 'consulta_externa';
            }
        }
        if ($emergencia) {
            $fecha = $emergencia->created_at;
            if ($fechaMasReciente === null || $fecha > $fechaMasReciente) {
                $fechaMasReciente = $fecha;
                $tipoIngreso      = 'emergencia';
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

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'camilla_id'   => ['required', 'exists:camillas,id'],
            'paciente_id'  => ['required', 'exists:pacientes,id'],
            'fecha_inicio' => ['required', 'date'],
            'fecha_fin'    => ['required', 'date', 'after:fecha_inicio'],
        ]);

        $camilla   = Camilla::findOrFail($data['camilla_id']);
        $paciente  = Paciente::findOrFail($data['paciente_id']);
        $inicio    = Carbon::parse($data['fecha_inicio']);
        $fin       = Carbon::parse($data['fecha_fin']);
        $totalMin  = (string) $inicio->diffInMinutes($fin);
        $horasCalc = bcdiv($totalMin, '60', 4);

        if (bccomp($horasCalc, '3', 4) <= 0) {
            return redirect()->back()
                ->with('info', 'La camilla no se cobra: el uso no superó las 3 horas.');
        }

        $horas = $horasCalc;
        $costo = bcmul($horas, (string) $camilla->precio_por_hora, 2);

        $hh          = (int) bcfloor($horas);
        $mm          = (int) bcround(bcmul(bcsub($horas, (string) $hh, 4), '60', 4), 0);
        $tiempoLabel = $mm > 0 ? "{$hh}h {$mm}min" : "{$hh}h";

        $cuenta = CuentaCobroService::obtenerOCrearCuentaMaestra($paciente->id, 'emergencia');

        $detalle = CuentaCobroDetalle::create([
            'cuenta_cobro_id' => $cuenta->id,
            'tipo_item'       => 'equipo_medico',
            'descripcion'     => 'Uso de Camilla: ' . $camilla->nombre . ' (' . $camilla->codigo . ') — ' . $tiempoLabel,
            'cantidad'        => $horas,
            'precio_unitario' => $camilla->precio_por_hora,
            'area_origen'     => 'emergencia',
            'user_id'         => auth()->id(),
        ]);

        $uso = CamillaUso::create([
            'camilla_id'              => $camilla->id,
            'paciente_id'             => $paciente->id,
            'fecha_inicio'            => $inicio,
            'fecha_fin'               => $fin,
            'costo_calculado'         => $costo,
            'cuenta_cobro_detalle_id' => $detalle->id,
            'registrado_por'          => auth()->id(),
        ]);

        $detalle->update([
            'origen_type' => CamillaUso::class,
            'origen_id'   => $uso->id,
        ]);

        $cuenta->recalcularTotales();

        return redirect()->route('emergency-staff.camillas.index')
            ->with('success', 'Uso de camilla registrado correctamente.');
    }
}
