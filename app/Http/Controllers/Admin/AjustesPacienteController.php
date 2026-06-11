<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PatientsController;
use App\Models\CuentaCobro;
use App\Models\CuentaCobroDetalle;
use App\Models\Paciente;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

/**
 * Ajustes de Paciente: sección administrativa para corregir los cargos
 * (gastos) de la cuenta de un paciente. Permite agregar cargos manuales y
 * deshabilitar / restaurar cargos existentes sin borrarlos (auditable).
 */
class AjustesPacienteController extends Controller
{
    /**
     * Listado de pacientes (mismo formato que /patients) con acción "Correcciones".
     */
    public function index(Request $request): View
    {
        $query = Paciente::with([
                'seguro',
                'registro.user',
                'consultas' => fn($q) => $q->with('caja')->orderBy('created_at', 'desc')->limit(1),
                'hospitalizaciones' => fn($q) => $q->orderBy('created_at', 'desc')->limit(1),
                'emergencias' => fn($q) => $q->orderBy('created_at', 'desc')->limit(1),
            ])
            ->whereHas('registro');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'LIKE', "%{$search}%")
                  ->orWhere('ci', 'LIKE', "%{$search}%")
                  ->orWhere('temp_code', 'LIKE', "%{$search}%")
                  ->orWhereHas('registro', fn($rq) => $rq->where('codigo', 'LIKE', "%{$search}%"));
            });
        }

        if ($request->filled('estado')) {
            if ($request->estado === 'hospitalizado') {
                $query->whereHas('hospitalizaciones', fn($q) => $q->where('estado', 'Activo'));
            } elseif ($request->estado === 'emergencia') {
                $query->whereHas('emergencias', fn($q) => $q->whereIn('status', ['recibido', 'en_evaluacion', 'estabilizado']));
            }
        }

        $pacientes = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        $pacientes->getCollection()->transform(function ($paciente) {
            $paciente->tipo_ingreso = PatientsController::determinarTipoIngreso($paciente);
            return $paciente;
        });

        return view('admin.ajustes-pacientes.index', compact('pacientes'));
    }

    /**
     * Vista de correcciones: muestra los gastos del paciente por cuenta.
     */
    public function correcciones($id): View
    {
        $paciente = Paciente::with('seguro')->findOrFail($id);

        $cuentas = CuentaCobro::with([
                'detalles' => fn($q) => $q->orderBy('created_at', 'desc'),
                'detalles.deshabilitadoPor',
                'pagos',
            ])
            ->where('paciente_id', $paciente->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.ajustes-pacientes.correcciones', compact('paciente', 'cuentas'));
    }

    /**
     * Agrega un cargo manual a una cuenta. Formato: fecha + concepto + cantidad + monto.
     */
    public function agregarCargo(Request $request, $cuentaId)
    {
        $cuenta = CuentaCobro::findOrFail($cuentaId);

        $validated = $request->validate([
            'descripcion' => 'required|string|max:255',
            'fecha'       => 'required|date',
            'cantidad'    => 'required|numeric|min:0.01',
            'monto'       => 'required|numeric|min:0',
        ], [], [
            'descripcion' => 'concepto',
            'monto'       => 'monto unitario',
        ]);

        $subtotal = bcmul((string) $validated['cantidad'], (string) $validated['monto'], 2);

        $cuenta->detalles()->create([
            'tipo_item'       => 'servicio',
            'descripcion'     => $validated['descripcion'],
            'cantidad'        => $validated['cantidad'],
            'precio_unitario' => $validated['monto'],
            'subtotal'        => $subtotal,
            'area_origen'     => null,
            'user_id'         => auth()->id(),
            'observaciones'   => 'Cargo manual (ajuste administrativo)',
            'created_at'      => Carbon::parse($validated['fecha']),
        ]);

        $cuenta->load('detalles');
        $cuenta->recalcularTotales();

        return redirect()->route('admin.ajustes-pacientes.correcciones', $cuenta->paciente_id)
            ->with('success', 'Cargo agregado correctamente.');
    }

    /**
     * Deshabilita un cargo (no se elimina; deja de sumar al total).
     */
    public function deshabilitarCargo(Request $request, $detalleId)
    {
        $detalle = CuentaCobroDetalle::findOrFail($detalleId);

        $validated = $request->validate([
            'motivo' => 'nullable|string|max:255',
        ]);

        if (!$detalle->estaDeshabilitado()) {
            $detalle->update([
                'deshabilitado_en'       => now(),
                'deshabilitado_por'      => auth()->id(),
                'motivo_deshabilitacion' => $validated['motivo'] ?? null,
            ]);

            $cuenta = $detalle->cuentaCobro;
            if ($cuenta) {
                $cuenta->load('detalles');
                $cuenta->recalcularTotales();
            }
        }

        return redirect()->back()->with('success', 'Cargo deshabilitado correctamente.');
    }

    /**
     * Restaura un cargo previamente deshabilitado.
     */
    public function restaurarCargo($detalleId)
    {
        $detalle = CuentaCobroDetalle::findOrFail($detalleId);

        if ($detalle->estaDeshabilitado()) {
            $detalle->update([
                'deshabilitado_en'       => null,
                'deshabilitado_por'      => null,
                'motivo_deshabilitacion' => null,
            ]);

            $cuenta = $detalle->cuentaCobro;
            if ($cuenta) {
                $cuenta->load('detalles');
                $cuenta->recalcularTotales();
            }
        }

        return redirect()->back()->with('success', 'Cargo restaurado correctamente.');
    }
}
