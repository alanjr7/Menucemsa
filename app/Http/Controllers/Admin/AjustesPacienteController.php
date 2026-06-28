<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\PatientsController;
use App\Models\CuentaCobro;
use App\Models\Paciente;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
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
                // Opt-in: aquí SÍ queremos ver los cargos deshabilitados (en gris)
                'detalles' => fn($q) => $q->conDeshabilitados()->orderBy('created_at', 'desc'),
                'detalles.deshabilitadoPor',
                // Historial de anulaciones (parciales/totales) por línea, para mostrar
                // el detalle y poder revertir.
                'detalles.anulaciones' => fn($q) => $q->orderBy('eliminado_en', 'desc'),
                'detalles.anulaciones.usuarioEliminacion',
                'detalles.anulaciones.revertidoPor',
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
            'cantidad'    => 'required|numeric|decimal:0,2|min:0.01',
            'monto'       => 'required|numeric|decimal:0,2|min:0',
            // Opcionales: vienen cuando se elige un ítem del buscador de catálogo.
            'tipo_item'   => 'nullable|in:servicio,medicamento,procedimiento,estadia,laboratorio,imagenologia,farmacia,material,equipo_medico',
            'codigo_item' => 'nullable|string|max:12',
            // Token anti-duplicado: único por render del formulario.
            'submit_token' => 'nullable|string|max:64',
        ], [], [
            'descripcion' => 'concepto',
            'monto'       => 'monto unitario',
        ]);

        // Idempotencia: un doble click (o reenvío) manda el MISMO submit_token.
        // Cache::add es atómico (solo el primer request gana la clave), así que
        // el segundo no crea un cargo duplicado. Driver de cache: database.
        $token = (string) ($validated['submit_token'] ?? '');
        if ($token !== '' && ! Cache::add('cargo_submit:'.$token, true, now()->addMinutes(10))) {
            return redirect()->route('admin.ajustes-pacientes.correcciones', $cuenta->paciente_id)
                ->with('info', 'El cargo ya se había agregado; se ignoró el envío duplicado.');
        }

        $subtotal = bcmul((string) $validated['cantidad'], (string) $validated['monto'], 2);

        $cuenta->detalles()->create([
            'tipo_item'       => $validated['tipo_item'] ?? 'servicio',
            // Código explícito (del buscador o tipeado a mano) se respeta; en blanco
            // queda null y el resolver le asigna uno del diccionario (familia 9).
            'codigo_item'     => filled($validated['codigo_item'] ?? null) ? trim($validated['codigo_item']) : null,
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
     * Autocompletado de ítems facturables para el formulario "Agregar cargo".
     * Delega en el buscador único de catálogo (compartido con Proformas) que
     * devuelve concepto + código + precio sugerido + tipo_item.
     */
    public function buscarCatalogo(Request $request): JsonResponse
    {
        return response()->json(
            \App\Support\BuscadorCatalogo::buscar((string) $request->get('q', ''))
        );
    }

    // Anular (deshabilitar parcial/total) y revertir cargos se unificó en
    // App\Http\Controllers\Admin\AjusteCargoController (admin.cargos.anular /
    // admin.anulaciones.revertir), fuente única de eliminaciones seguras.
}
