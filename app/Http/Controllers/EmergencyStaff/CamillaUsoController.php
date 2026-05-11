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
                'consultas' => fn($q) => $q->with('caja')->orderBy('created_at', 'desc')->limit(1),
                'hospitalizaciones' => fn($q) => $q->orderBy('created_at', 'desc')->limit(1),
                'emergencias' => fn($q) => $q->orderBy('created_at', 'desc')->limit(1),
            ])
            ->whereHas('registro');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nombre', 'LIKE', "%{$search}%")
                  ->orWhere('ci', 'LIKE', "%{$search}%")
                  ->orWhereHas('registro', fn($rq) => $rq->where('codigo', 'LIKE', "%{$search}%"));
            });
        }

        $pacientes = $query->orderBy('created_at', 'desc')->paginate(15);

        $emergencyQuery = \App\Models\Emergency::where('is_temp_id', true)
            ->whereIn('status', ['recibido', 'en_evaluacion', 'estabilizado']);

        if ($request->filled('search')) {
            $search = $request->search;
            $emergencyQuery->where(fn($q) => $q->where('temp_id', 'LIKE', "%{$search}%")
                ->orWhere('code', 'LIKE', "%{$search}%"));
        }

        $pacientesTemporales = $emergencyQuery->orderBy('created_at', 'desc')->get()->map(fn($emergency) => (object)[
            'ci' => $emergency->temp_id,
            'nombre' => 'Paciente Temporal - Emergencia',
            'is_temporal' => true,
            'emergency_id' => $emergency->id,
            'emergency_code' => $emergency->code,
            'emergency_status' => $emergency->status,
            'created_at' => $emergency->created_at,
            'tipo_ingreso' => 'emergencia',
            'seguro' => null,
            'consultas' => collect(),
        ]);

        $pacientesCollection = collect($pacientes->items());
        $todosPacientes = $pacientesCollection->merge($pacientesTemporales)->sortByDesc('created_at');

        $page = $request->get('page', 1);
        $perPage = 15;
        $pacientes = new \Illuminate\Pagination\LengthAwarePaginator(
            $todosPacientes->forPage($page, $perPage)->values(),
            $todosPacientes->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $pacientes->getCollection()->transform(function($paciente) {
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
        $consulta = $paciente->consultas->first();
        $emergencia = $paciente->emergencias->first();
        $hospitalizacion = $paciente->hospitalizaciones->first();

        $fechaMasReciente = null;
        $tipoIngreso = 'otro';

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

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'camilla_id'   => ['required', 'exists:camillas,id'],
            'paciente_ci'  => ['required', 'exists:pacientes,ci'],
            'fecha_inicio' => ['required', 'date'],
            'fecha_fin'    => ['required', 'date', 'after:fecha_inicio'],
        ]);

        $camilla = Camilla::findOrFail($data['camilla_id']);
        $inicio  = Carbon::parse($data['fecha_inicio']);
        $fin     = Carbon::parse($data['fecha_fin']);
        $horas   = max(0.5, round($inicio->diffInMinutes($fin) / 60, 2));
        $costo   = round($horas * (float)$camilla->precio_por_hora, 2);

        $cuenta = CuentaCobroService::obtenerOCrearCuentaMaestra($data['paciente_ci'], 'emergencia');

        $detalle = CuentaCobroDetalle::create([
            'cuenta_cobro_id' => $cuenta->id,
            'tipo_item'       => 'equipo_medico',
            'descripcion'     => 'Uso de Camilla: ' . $camilla->nombre . ' (' . $camilla->codigo . ')',
            'cantidad'        => $horas,
            'precio_unitario' => $camilla->precio_por_hora,
            'area_origen'     => 'emergencia',
            'user_id'         => auth()->id(),
        ]);

        $uso = CamillaUso::create([
            'camilla_id'              => $camilla->id,
            'paciente_ci'             => $data['paciente_ci'],
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
