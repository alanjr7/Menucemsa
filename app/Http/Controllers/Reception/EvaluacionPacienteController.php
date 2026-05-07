<?php

namespace App\Http\Controllers\Reception;

use App\Http\Controllers\Controller;
use App\Models\AlmacenCatalogo;
use App\Models\AlmacenLote;
use App\Models\AlmacenStock;
use App\Models\Evaluacion;
use App\Models\EvaluacionItem;
use App\Models\Emergency;
use App\Models\Paciente;
use App\Models\Procedimiento;
use App\Services\AlmacenEntregaService;
use App\Services\CuentaCobroService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class EvaluacionPacienteController extends Controller
{
    private static array $roleAreaMap = [
        'emergencia'          => 'emergencia',
        'enfermera-emergencia' => 'emergencia',
        'uti'                 => 'uti',
        'internacion'         => 'internacion',
        'enfermera-internacion' => 'internacion',
        'cirujano'            => 'cirugia',
    ];

    private function areaFromRole(string $role): string
    {
        return self::$roleAreaMap[$role] ?? 'emergencia';
    }

    private function resolveIdentifier(string $ci): object
    {
        if (str_starts_with($ci, 'TEMP-')) {
            $emergency = Emergency::where('temp_id', $ci)->firstOrFail();
            return (object)[
                'ci'         => $ci,
                'nombre'     => 'Paciente Temporal - Emergencia',
                'is_temporal'=> true,
                'emergency'  => $emergency,
            ];
        }

        return Paciente::where('ci', $ci)->firstOrFail();
    }

    public function show(string $ci): View
    {
        $paciente = $this->resolveIdentifier($ci);
        $area = $this->areaFromRole(auth()->user()->role);

        return view('evaluacion.show', compact('paciente', 'area'));
    }

    public function store(Request $request, string $ci): RedirectResponse|JsonResponse
    {
        $request->validate([
            'observaciones'        => ['nullable', 'string'],
            'items'                => ['nullable', 'array'],
            'items.*.tipo'         => ['required', 'in:medicamento,insumo,procedimiento'],
            'items.*.item_id'      => ['required', 'integer'],
            'items.*.nombre'       => ['required', 'string'],
            'items.*.cantidad'     => ['required', 'integer', 'min:1'],
            'items.*.precio'       => ['nullable', 'numeric', 'min:0'],
        ]);

        $area = $this->areaFromRole(auth()->user()->role);

        // Para pacientes TEMP-, la cuenta se registró con el temp_id como string
        $cuentaCi = $ci;

        // Para registrar entregas en almacén necesitamos un int; en TEMP- usamos el emergency id
        $emergencyForTemp = null;
        if (str_starts_with($ci, 'TEMP-')) {
            $emergencyForTemp = Emergency::where('temp_id', $ci)->first();
        }

        DB::transaction(function () use ($request, $ci, $area, $cuentaCi, $emergencyForTemp) {
            // Solo guardar Evaluacion si el paciente existe en la tabla pacientes (no temporales)
            $evaluacion = null;
            if (!str_starts_with($ci, 'TEMP-')) {
                $evaluacion = Evaluacion::create([
                    'paciente_ci'   => $ci,
                    'area'          => $area,
                    'user_id'       => auth()->id(),
                    'observaciones' => $request->observaciones,
                ]);
            }

            $items = $request->input('items', []);
            if (empty($items)) return;

            // Obtener o crear la cuenta maestra del paciente
            $cuenta = CuentaCobroService::obtenerOCrearCuentaMaestra($cuentaCi, $area);

            foreach ($items as $item) {
                $precio   = (float) ($item['precio'] ?? 0);
                $cantidad = (int) $item['cantidad'];
                $tipo     = $item['tipo'];

                // Medicamentos e insumos requieren stock; si no hay, no se entrega ni cobra
                if (in_array($tipo, ['medicamento', 'insumo'])) {
                    $stock = AlmacenStock::whereHas('lote', fn($q) => $q->where('catalogo_id', $item['item_id']))
                        ->where('ubicacion', $area)
                        ->where('cantidad_actual', '>=', $cantidad)
                        ->first();

                    if (!$stock) {
                        continue;
                    }

                    $stock->decrement('cantidad_actual', $cantidad);

                    $pacienteCiInt = str_starts_with($ci, 'TEMP-')
                        ? ($emergencyForTemp?->id)
                        : (int) $ci;

                    if ($pacienteCiInt) {
                        AlmacenEntregaService::registrarEntrega(
                            $pacienteCiInt,
                            (int) $item['item_id'],
                            $cantidad,
                            $area,
                            $emergencyForTemp?->id,
                            'Aplicado en evaluación - ' . $area
                        );
                    }
                }

                // Guardar el item de evaluación (solo para pacientes registrados)
                $evaluacionItem = null;
                if ($evaluacion) {
                    $evaluacionItem = EvaluacionItem::create([
                        'evaluacion_id'   => $evaluacion->id,
                        'tipo'            => $tipo,
                        'item_id'         => $item['item_id'],
                        'nombre_snapshot' => $item['nombre'],
                        'cantidad'        => $cantidad,
                        'precio_snapshot' => $precio ?: null,
                    ]);
                }

                if ($precio <= 0) continue;

                $tipoItem = match ($tipo) {
                    'medicamento' => 'medicamento',
                    'insumo'      => 'insumo',
                    default       => 'procedimiento',
                };

                $origenId = $evaluacionItem
                    ? (string) $evaluacionItem->id
                    : $cuentaCi . '-' . $item['item_id'] . '-' . now()->timestamp;

                CuentaCobroService::agregarCargoConDeduplicacion(
                    $cuenta->id,
                    $tipoItem,
                    ucfirst($tipo) . ' - ' . $item['nombre'],
                    $precio,
                    $cantidad,
                    $area,
                    EvaluacionItem::class,
                    $origenId,
                );
            }
        });

        $redirectUrl = route('patients.index');

        if ($request->wantsJson()) {
            return response()->json(['redirect' => $redirectUrl]);
        }

        return redirect($redirectUrl)->with('success', 'Evaluación guardada correctamente.');
    }

    public function historial(int $ci): View
    {
        $paciente = Paciente::with('consultas.caja')->where('ci', $ci)->firstOrFail();
        $evaluaciones = Evaluacion::with(['user', 'items'])
            ->where('paciente_ci', $ci)
            ->orderByDesc('created_at')
            ->paginate(15);

        $camillaUsos = \App\Models\CamillaUso::with('camilla', 'registradoPor')
            ->where('paciente_ci', $ci)
            ->orderByDesc('fecha_inicio')
            ->get();

        return view('evaluacion.historial', compact('paciente', 'evaluaciones', 'camillaUsos'));
    }

    public function print(int $ci, int $evaluacionId): View
    {
        $paciente = Paciente::where('ci', $ci)->firstOrFail();
        $evaluacion = Evaluacion::with(['user', 'items'])
            ->where('id', $evaluacionId)
            ->where('paciente_ci', $ci)
            ->firstOrFail();

        return view('evaluacion.print', compact('paciente', 'evaluacion'));
    }

    public function buscarMedicamentos(Request $request): JsonResponse
    {
        $area = $this->areaFromRole(auth()->user()->role);
        $q = $request->input('q', '');

        $items = AlmacenCatalogo::where('tipo', 'medicamento')
            ->where('activo', true)
            ->where('nombre', 'like', "%{$q}%")
            ->with(['lotes.stocks' => fn($query) => $query->where('ubicacion', $area)])
            ->get()
            ->map(function ($catalogo) use ($area) {
                $stock = $catalogo->lotes->flatMap->stocks->first();
                if (! $stock) return null;
                return [
                    'id'            => $catalogo->id,
                    'nombre'        => $catalogo->nombre,
                    'unidad_medida' => $catalogo->unidad_medida,
                    'cantidad_actual' => $stock->cantidad_actual,
                    'precio'        => $stock->lote->precio_venta ?? 0,
                ];
            })
            ->filter()
            ->values();

        return response()->json($items);
    }

    public function buscarInsumos(Request $request): JsonResponse
    {
        $area = $this->areaFromRole(auth()->user()->role);
        $q = $request->input('q', '');

        $items = AlmacenCatalogo::where('tipo', 'insumo')
            ->where('activo', true)
            ->where('nombre', 'like', "%{$q}%")
            ->with(['lotes.stocks' => fn($query) => $query->where('ubicacion', $area)])
            ->get()
            ->map(function ($catalogo) use ($area) {
                $stock = $catalogo->lotes->flatMap->stocks->first();
                if (! $stock) return null;
                return [
                    'id'            => $catalogo->id,
                    'nombre'        => $catalogo->nombre,
                    'unidad_medida' => $catalogo->unidad_medida,
                    'cantidad_actual' => $stock->cantidad_actual,
                    'precio'        => $stock->lote->precio_venta ?? 0,
                ];
            })
            ->filter()
            ->values();

        return response()->json($items);
    }

    public function buscarProcedimientos(Request $request): JsonResponse
    {
        $area = $this->areaFromRole(auth()->user()->role);
        $q = $request->input('q', '');

        $procedimientos = Procedimiento::activos()
            ->porArea($area)
            ->where('nombre', 'like', "%{$q}%")
            ->select('id', 'nombre', 'precio')
            ->get();

        return response()->json($procedimientos);
    }
}
