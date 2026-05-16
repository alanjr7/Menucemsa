<?php

namespace App\Http\Controllers\Reception;

use App\Http\Controllers\Controller;
use App\Models\AlmacenCatalogo;
use App\Models\AlmacenStock;
use App\Models\Evaluacion;
use App\Models\EvaluacionItem;
use App\Models\Paciente;
use App\Models\Procedimiento;
use App\Services\AlmacenEntregaService;
use App\Services\CuentaCobroService;
use App\Services\EpisodioService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class EvaluacionPacienteController extends Controller
{
    private static array $roleAreaMap = [
        'emergencia'            => 'emergencia',
        'enfermera-emergencia'  => 'emergencia',
        'uti'                   => 'uti',
        'internacion'           => 'internacion',
        'enfermera-internacion' => 'internacion',
        'cirujano'              => 'cirugia',
        'neonato'               => 'neonato',
    ];

    private function areaFromRole(string $role): string
    {
        return self::$roleAreaMap[$role] ?? 'emergencia';
    }

    private function resolvePaciente(string $identifier): Paciente
    {
        if (str_starts_with($identifier, 'TEMP-') || str_starts_with($identifier, 'RN-')) {
            return Paciente::where('temp_code', $identifier)->firstOrFail();
        }

        return Paciente::where('ci', (int) $identifier)->firstOrFail();
    }

    public function show(string $ci): View
    {
        $paciente = $this->resolvePaciente($ci);
        $area     = $this->areaFromRole(auth()->user()->role);

        return view('evaluacion.show', compact('paciente', 'area'));
    }

    public function store(Request $request, string $ci): RedirectResponse|JsonResponse
    {
        $request->validate([
            'observaciones'                          => ['nullable', 'string'],
            'signos_vitales'                         => ['nullable', 'array'],
            'signos_vitales.presion_arterial'        => ['nullable', 'string', 'max:20'],
            'signos_vitales.frecuencia_cardiaca'     => ['nullable', 'string', 'max:20'],
            'signos_vitales.frecuencia_respiratoria' => ['nullable', 'string', 'max:20'],
            'signos_vitales.temperatura'             => ['nullable', 'string', 'max:20'],
            'signos_vitales.saturacion_o2'           => ['nullable', 'string', 'max:20'],
            'signos_vitales.glucosa'                 => ['nullable', 'string', 'max:20'],
            'signos_vitales.peso'                    => ['nullable', 'numeric', 'min:0', 'max:500'],
            'signos_vitales.altura'                  => ['nullable', 'numeric', 'min:0', 'max:300'],
            'items'            => ['nullable', 'array'],
            'items.*.tipo'     => ['required', 'in:medicamento,insumo,procedimiento'],
            'items.*.item_id'  => ['required', 'integer'],
            'items.*.nombre'   => ['required', 'string'],
            'items.*.cantidad' => ['required', 'integer', 'min:1'],
            'items.*.precio'   => ['nullable', 'numeric', 'min:0'],
        ]);

        $paciente = $this->resolvePaciente($ci);
        $area     = $this->areaFromRole(auth()->user()->role);

        DB::transaction(function () use ($request, $paciente, $area) {
            $signosVitales = null;
            if ($request->filled('signos_vitales')) {
                $sv     = $request->input('signos_vitales', []);
                $peso   = isset($sv['peso'])   && $sv['peso']   > 0 ? (float) $sv['peso']   : null;
                $altura = isset($sv['altura']) && $sv['altura'] > 0 ? (float) $sv['altura'] : null;
                $signosVitales = array_filter([
                    'presion_arterial'        => $sv['presion_arterial']        ?? null,
                    'frecuencia_cardiaca'     => $sv['frecuencia_cardiaca']     ?? null,
                    'frecuencia_respiratoria' => $sv['frecuencia_respiratoria'] ?? null,
                    'temperatura'             => $sv['temperatura']             ?? null,
                    'saturacion_o2'           => $sv['saturacion_o2']           ?? null,
                    'glucosa'                 => $sv['glucosa']                 ?? null,
                    'peso'                    => $peso,
                    'altura'                  => $altura,
                    'imc'                     => ($peso && $altura) ? round($peso / (($altura / 100) ** 2), 2) : null,
                    'fecha_registro'          => now()->toDateTimeString(),
                ], fn($v) => $v !== null && $v !== '');
                if (empty($signosVitales)) $signosVitales = null;
            }

            $episodioId = $paciente->is_temp
                ? null
                : EpisodioService::getEpisodioAbierto($paciente->id)?->id;

            $evaluacion = Evaluacion::create([
                'paciente_id'    => $paciente->id,
                'area'           => $area,
                'user_id'        => auth()->id(),
                'observaciones'  => $request->observaciones,
                'signos_vitales' => $signosVitales,
                'episodio_id'    => $episodioId,
            ]);

            $items = $request->input('items', []);
            if (empty($items)) return;

            $cuenta = CuentaCobroService::obtenerOCrearCuentaMaestra($paciente->id, $area);

            foreach ($items as $item) {
                $precio   = (float) ($item['precio'] ?? 0);
                $cantidad = (int) $item['cantidad'];
                $tipo     = $item['tipo'];

                if (in_array($tipo, ['medicamento', 'insumo'])) {
                    $stock = AlmacenStock::whereHas('lote', fn($q) => $q->where('catalogo_id', $item['item_id']))
                        ->where('ubicacion', $area)
                        ->where('cantidad_actual', '>=', $cantidad)
                        ->first();

                    if (!$stock) continue;

                    $stock->decrement('cantidad_actual', $cantidad);

                    AlmacenEntregaService::registrarEntrega(
                        $paciente->id,
                        (int) $item['item_id'],
                        $cantidad,
                        $area,
                        null,
                        'Aplicado en evaluación - ' . $area
                    );
                }

                $evaluacionItem = EvaluacionItem::create([
                    'evaluacion_id'   => $evaluacion->id,
                    'tipo'            => $tipo,
                    'item_id'         => $item['item_id'],
                    'nombre_snapshot' => $item['nombre'],
                    'cantidad'        => $cantidad,
                    'precio_snapshot' => $precio ?: null,
                ]);

                if ($precio <= 0) continue;

                $tipoItem = match ($tipo) {
                    'medicamento' => 'medicamento',
                    'insumo'      => 'material',
                    default       => 'procedimiento',
                };

                CuentaCobroService::agregarCargoConDeduplicacion(
                    $cuenta->id,
                    $tipoItem,
                    ucfirst($tipo) . ' - ' . $item['nombre'],
                    $precio,
                    $cantidad,
                    $area,
                    EvaluacionItem::class,
                    (string) $evaluacionItem->id,
                );
            }
        });

        $redirectUrl = route('patients.index');

        if ($request->wantsJson()) {
            return response()->json(['redirect' => $redirectUrl]);
        }

        return redirect($redirectUrl)->with('success', 'Evaluación guardada correctamente.');
    }

    public function historial(string $ci): View
    {
        $paciente = $this->resolvePaciente($ci);

        $episodio = $paciente->is_temp
            ? null
            : EpisodioService::getEpisodioAbierto($paciente->id);

        $evaluaciones = Evaluacion::with(['user', 'items'])
            ->where('paciente_id', $paciente->id)
            ->when($episodio, fn($q) => $q->where('episodio_id', $episodio->id))
            ->when(!$episodio && !$paciente->is_temp, fn($q) => $q->whereRaw('1=0'))
            ->orderByDesc('created_at')
            ->paginate(15);

        $camillaUsos = \App\Models\CamillaUso::with('camilla', 'registradoPor')
            ->where('paciente_id', $paciente->id)
            ->when($episodio, fn($q) => $q->where('created_at', '>=', $episodio->fecha_apertura))
            ->when(!$episodio && !$paciente->is_temp, fn($q) => $q->whereRaw('1=0'))
            ->orderByDesc('fecha_inicio')
            ->get();

        return view('evaluacion.historial', compact('paciente', 'evaluaciones', 'camillaUsos', 'episodio'));
    }

    public function destroy(int $pacienteId, int $evaluacionId): RedirectResponse
    {
        $paciente   = Paciente::findOrFail($pacienteId);
        $evaluacion = Evaluacion::where('id', $evaluacionId)
            ->where('paciente_id', $paciente->id)
            ->firstOrFail();

        $evaluacion->items()->delete();
        $evaluacion->delete();

        $identifier = $paciente->ci ?? $paciente->temp_code;
        return redirect()->route('evaluacion.historial', $identifier)
            ->with('success', 'Evaluación eliminada correctamente.');
    }

    public function print(int $pacienteId, int $evaluacionId): View
    {
        $paciente   = Paciente::findOrFail($pacienteId);
        $evaluacion = Evaluacion::with(['user', 'items'])
            ->where('id', $evaluacionId)
            ->where('paciente_id', $paciente->id)
            ->firstOrFail();

        return view('evaluacion.print', compact('paciente', 'evaluacion'));
    }

    private function resolveArea(Request $request): string
    {
        $role = auth()->user()->role;
        if (in_array($role, ['admin', 'administrador', 'dirmedico']) && $request->filled('area')) {
            return $request->input('area');
        }
        return $this->areaFromRole($role);
    }

    public function buscarMedicamentos(Request $request): JsonResponse
    {
        $area = $this->resolveArea($request);
        $q    = $request->input('q', '');

        $items = AlmacenCatalogo::where('tipo', 'medicamento')
            ->where('activo', true)
            ->where('nombre', 'like', "%{$q}%")
            ->with(['lotes.stocks' => fn($query) => $query->where('ubicacion', $area)])
            ->get()
            ->map(function ($catalogo) {
                $stock = $catalogo->lotes->flatMap->stocks->first();
                if (!$stock) return null;
                return [
                    'id'              => $catalogo->id,
                    'nombre'          => $catalogo->nombre,
                    'unidad_medida'   => $catalogo->unidad_medida,
                    'cantidad_actual' => $stock->cantidad_actual,
                    'precio'          => $stock->lote->precio_venta ?? 0,
                ];
            })
            ->filter()
            ->values();

        return response()->json($items);
    }

    public function buscarInsumos(Request $request): JsonResponse
    {
        $area = $this->resolveArea($request);
        $q    = $request->input('q', '');

        $items = AlmacenCatalogo::where('tipo', 'insumo')
            ->where('activo', true)
            ->where('nombre', 'like', "%{$q}%")
            ->with(['lotes.stocks' => fn($query) => $query->where('ubicacion', $area)])
            ->get()
            ->map(function ($catalogo) {
                $stock = $catalogo->lotes->flatMap->stocks->first();
                if (!$stock) return null;
                return [
                    'id'              => $catalogo->id,
                    'nombre'          => $catalogo->nombre,
                    'unidad_medida'   => $catalogo->unidad_medida,
                    'cantidad_actual' => $stock->cantidad_actual,
                    'precio'          => $stock->lote->precio_venta ?? 0,
                ];
            })
            ->filter()
            ->values();

        return response()->json($items);
    }

    public function buscarProcedimientos(Request $request): JsonResponse
    {
        $area = $this->resolveArea($request);
        $q    = $request->input('q', '');

        $procedimientos = Procedimiento::activos()
            ->porArea($area)
            ->where('nombre', 'like', "%{$q}%")
            ->select('id', 'nombre', 'precio')
            ->get();

        return response()->json($procedimientos);
    }
}
