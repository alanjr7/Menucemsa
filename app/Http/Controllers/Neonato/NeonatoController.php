<?php

namespace App\Http\Controllers\Neonato;

use App\Http\Controllers\Controller;
use App\Models\AlmacenStock;
use App\Models\Camilla;
use App\Models\CamillaUso;
use App\Models\CuentaCobroDetalle;
use App\Models\Evaluacion;
use App\Models\EvaluacionItem;
use App\Models\Neonato;
use App\Models\Paciente;
use App\Services\AlmacenEntregaService;
use App\Services\CuentaCobroService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class NeonatoController extends Controller
{
    // -------------------------------------------------------------------------
    // Recién nacidos — lista (index / dashboard del rol)
    // -------------------------------------------------------------------------

    public function index(Request $request): View
    {
        $query = Neonato::with('user')->orderBy('admission_date', 'desc');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('madre_nombre', 'like', "%{$search}%");
            });
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        } else {
            $query->whereNotIn('status', ['alta', 'fallecido']);
        }

        $neonatos = $query->paginate(20)->withQueryString();

        $statuses = Neonato::statuses();

        $statsHoy = [
            'ingresados_hoy' => Neonato::whereDate('admission_date', now()->toDateString())->count(),
            'activos'        => Neonato::whereNotIn('status', ['alta', 'fallecido'])->count(),
        ];

        return view('neonato.index', compact('neonatos', 'statuses', 'statsHoy'));
    }

    // -------------------------------------------------------------------------
    // Añadir recién nacido
    // -------------------------------------------------------------------------

    public function create(): View
    {
        return view('neonato.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nombre'               => ['nullable', 'string', 'max:150'],
            'sexo'                 => ['nullable', 'in:M,F'],
            'madre_ci'             => ['nullable', 'string', 'max:20'],
            'peso'                 => ['nullable', 'numeric', 'min:0'],
            'talla'                => ['nullable', 'numeric', 'min:0'],
            'perimetro_cefalico'   => ['nullable', 'numeric', 'min:0'],
            'apgar1'               => ['nullable', 'integer', 'min:0', 'max:10'],
            'apgar5'               => ['nullable', 'integer', 'min:0', 'max:10'],
            'tipo_parto'           => ['nullable', 'in:normal,cesarea,instrumentado'],
            'fecha_hora_nacimiento'=> ['nullable', 'date'],
            'observaciones'        => ['nullable', 'string'],
        ]);

        $madre = null;
        if (!empty($data['madre_ci'])) {
            $madre = Paciente::where('ci', $data['madre_ci'])->first();
        }

        $neonato = null;

        DB::transaction(function () use ($data, $madre, &$neonato) {
            $paciente = Paciente::create([
                'nombre'    => $data['nombre'] ?: 'Recién Nacido',
                'temp_code' => Neonato::generateTempCode(),
                'is_temp'   => true,
            ]);

            $neonato = Neonato::create([
                'nombre'               => $data['nombre'] ?? null,
                'sexo'                 => $data['sexo'] ?? null,
                'peso'                 => $data['peso'] ?? null,
                'talla'                => $data['talla'] ?? null,
                'perimetro_cefalico'   => $data['perimetro_cefalico'] ?? null,
                'apgar1'               => $data['apgar1'] ?? null,
                'apgar5'               => $data['apgar5'] ?? null,
                'tipo_parto'           => $data['tipo_parto'] ?? null,
                'fecha_hora_nacimiento'=> $data['fecha_hora_nacimiento'] ?? null,
                'observaciones'        => $data['observaciones'] ?? null,
                'madre_nombre'         => $madre?->nombre,
                'madre_id'             => $madre?->id,
                'paciente_id'          => $paciente->id,
                'code'                 => Neonato::generateCode(),
                'status'               => 'recibido',
                'admission_date'       => now(),
                'user_id'              => auth()->id(),
            ]);
        });

        return redirect()->route('neonato.show', $neonato->id)
            ->with('success', 'Recién nacido registrado. Código: ' . $neonato->code);
    }

    // -------------------------------------------------------------------------
    // Datos del RN
    // -------------------------------------------------------------------------

    public function show(Neonato $neonato): View
    {
        $neonato->load('user');
        $statuses = Neonato::statuses();

        return view('neonato.show', compact('neonato', 'statuses'));
    }

    public function updateStatus(Request $request, Neonato $neonato): RedirectResponse
    {
        $request->validate([
            'status' => ['required', 'in:recibido,en_observacion,estable,uti_neonatal,alta,fallecido'],
        ]);

        $logs = $neonato->status_logs ?? [];
        $logs[] = [
            'status_anterior' => $neonato->status,
            'status_nuevo'    => $request->status,
            'user_id'         => auth()->id(),
            'user_name'       => auth()->user()->name,
            'changed_at'      => now()->setTimezone('America/La_Paz')->toDateTimeString(),
        ];

        $neonato->update([
            'status'         => $request->status,
            'status_logs'    => $logs,
            'discharge_date' => in_array($request->status, ['alta', 'fallecido']) ? now() : null,
        ]);

        return back()->with('success', 'Estado actualizado.');
    }

    // -------------------------------------------------------------------------
    // Historial
    // -------------------------------------------------------------------------

    public function historial(Neonato $neonato): View
    {
        $evaluaciones = Evaluacion::where('area', 'neonato')
            ->where('paciente_id', $neonato->paciente_id)
            ->with(['user', 'items'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $usosCunas = CamillaUso::where('paciente_id', $neonato->paciente_id)
            ->whereHas('camilla', fn($q) => $q->where('area', 'neonato'))
            ->with('camilla')
            ->orderBy('fecha_inicio', 'desc')
            ->get();

        return view('neonato.historial', compact('neonato', 'evaluaciones', 'usosCunas'));
    }

    // -------------------------------------------------------------------------
    // Evaluación neonatal
    // -------------------------------------------------------------------------

    public function evaluar(Neonato $neonato): View
    {
        return view('neonato.evaluacion', compact('neonato'));
    }

    public function storeEvaluacion(Request $request, Neonato $neonato): RedirectResponse|\Illuminate\Http\JsonResponse
    {
        $request->validate([
            'observaciones'                     => ['nullable', 'string'],
            'signos_vitales'                    => ['nullable', 'array'],
            'signos_vitales.temperatura'        => ['nullable', 'string', 'max:20'],
            'signos_vitales.frecuencia_cardiaca'=> ['nullable', 'string', 'max:20'],
            'signos_vitales.frecuencia_respiratoria' => ['nullable', 'string', 'max:20'],
            'signos_vitales.saturacion_o2'      => ['nullable', 'string', 'max:20'],
            'signos_vitales.glucosa'            => ['nullable', 'string', 'max:20'],
            'signos_vitales.peso_actual'        => ['nullable', 'numeric', 'min:0'],
            'signos_vitales.color_piel'         => ['nullable', 'string', 'max:30'],
            'signos_vitales.tono_muscular'      => ['nullable', 'string', 'max:30'],
            'items'                             => ['nullable', 'array'],
            'items.*.tipo'                      => ['required', 'in:medicamento,insumo,procedimiento'],
            'items.*.item_id'                   => ['required', 'integer'],
            'items.*.nombre'                    => ['required', 'string'],
            'items.*.cantidad'                  => ['required', 'integer', 'min:1'],
            'items.*.precio'                    => ['nullable', 'numeric', 'min:0'],
        ]);

        DB::transaction(function () use ($request, $neonato) {
            $sv = $request->input('signos_vitales', []);
            $signosVitales = array_filter([
                'temperatura'             => $sv['temperatura'] ?? null,
                'frecuencia_cardiaca'     => $sv['frecuencia_cardiaca'] ?? null,
                'frecuencia_respiratoria' => $sv['frecuencia_respiratoria'] ?? null,
                'saturacion_o2'           => $sv['saturacion_o2'] ?? null,
                'glucosa'                 => $sv['glucosa'] ?? null,
                'peso_actual'             => isset($sv['peso_actual']) && $sv['peso_actual'] > 0 ? (float)$sv['peso_actual'] : null,
                'color_piel'              => $sv['color_piel'] ?? null,
                'tono_muscular'           => $sv['tono_muscular'] ?? null,
            ]);

            $evaluacion = Evaluacion::create([
                'paciente_id'    => $neonato->paciente_id,
                'area'           => 'neonato',
                'user_id'        => auth()->id(),
                'observaciones'  => $request->observaciones,
                'signos_vitales' => !empty($signosVitales) ? $signosVitales : null,
            ]);

            $billingPacienteId = $neonato->madre_id ?? $neonato->paciente_id;
            $cuenta = CuentaCobroService::obtenerOCrearCuentaMaestra($billingPacienteId, 'neonato');

            foreach ($request->input('items', []) as $item) {
                $precio   = (float)($item['precio'] ?? 0);
                $cantidad = (int)$item['cantidad'];
                $tipoItem = $item['tipo'] === 'procedimiento' ? 'procedimiento' : 'medicamento';

                $ei = EvaluacionItem::create([
                    'evaluacion_id'   => $evaluacion->id,
                    'tipo'            => $item['tipo'],
                    'item_id'         => $item['item_id'],
                    'nombre_snapshot' => $item['nombre'],
                    'cantidad'        => $cantidad,
                    'precio_snapshot' => $precio,
                ]);

                if ($item['tipo'] === 'medicamento' || $item['tipo'] === 'insumo') {
                    $stock = AlmacenStock::whereHas('lote', fn($q) => $q->where('catalogo_id', $item['item_id']))
                        ->where('ubicacion', 'neonato')
                        ->where('cantidad_actual', '>=', $cantidad)
                        ->first();
                    if ($stock) {
                        $stock->decrement('cantidad_actual', $cantidad);
                        AlmacenEntregaService::registrarEntrega(
                            $neonato->paciente_id,
                            (int) $item['item_id'],
                            $cantidad,
                            'neonato',
                            $neonato->id
                        );
                    }
                }

                if ($precio > 0) {
                    CuentaCobroService::agregarCargoConDeduplicacion(
                        cuentaCobroId:  $cuenta->id,
                        tipoItem:       $tipoItem,
                        descripcion:    $item['nombre'] . ' ×' . $cantidad,
                        precioUnitario: $precio,
                        cantidad:       $cantidad,
                        areaOrigen:     'neonato',
                        origenType:     EvaluacionItem::class,
                        origenId:       (string)$ei->id,
                    );
                }
            }

            $cuenta->recalcularTotales();
        });

        if ($request->wantsJson()) {
            return response()->json(['redirect' => route('neonato.historial', $neonato->id)]);
        }

        return redirect()->route('neonato.historial', $neonato->id)
            ->with('success', 'Evaluación registrada correctamente.');
    }

    // -------------------------------------------------------------------------
    // Cunas — registrar uso
    // -------------------------------------------------------------------------

    public function cunas(Request $request): View
    {
        $cunas = Camilla::where('area', 'neonato')->where('activa', true)->get();

        $query = Neonato::whereNotIn('status', ['alta', 'fallecido']);

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('madre_nombre', 'like', "%{$search}%");
            });
        }

        $neonatos = $query->orderBy('admission_date', 'desc')->paginate(20)->withQueryString();

        $preciosCunas = $cunas->pluck('precio_por_hora', 'id')->toArray();

        return view('neonato.cunas', compact('neonatos', 'cunas', 'preciosCunas'));
    }

    public function storeCunaUso(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'neonato_id'   => ['required', 'exists:neonatos,id'],
            'camilla_id'   => ['required', 'exists:camillas,id'],
            'fecha_inicio' => ['required', 'date'],
            'fecha_fin'    => ['required', 'date', 'after:fecha_inicio'],
        ]);

        $neonato = Neonato::findOrFail($data['neonato_id']);
        $cuna    = Camilla::where('id', $data['camilla_id'])->where('area', 'neonato')->firstOrFail();

        $inicio = \Carbon\Carbon::parse($data['fecha_inicio']);
        $fin    = \Carbon\Carbon::parse($data['fecha_fin']);

        $horasCalc = bcdiv((string)$inicio->diffInMinutes($fin), '60', 4);
        $horas     = bccomp($horasCalc, '0.5', 4) < 0 ? '0.5' : $horasCalc;
        $costo     = bcmul($horas, (string)$cuna->precio_por_hora, 2);

        $hh = (int)bcfloor($horas);
        $mm = (int)bcround(bcmul(bcsub($horas, (string)$hh, 4), '60', 4), 0);
        $tiempoLabel = $mm > 0 ? "{$hh}h {$mm}min" : "{$hh}h";

        $billingPacienteId = $neonato->madre_id ?? $neonato->paciente_id;
        $cuenta = CuentaCobroService::obtenerOCrearCuentaMaestra($billingPacienteId, 'neonato');

        $detalle = CuentaCobroDetalle::create([
            'cuenta_cobro_id' => $cuenta->id,
            'tipo_item'       => 'equipo_medico',
            'descripcion'     => 'Uso de Cuna: ' . $cuna->nombre . ' (' . $cuna->codigo . ') — ' . $tiempoLabel,
            'cantidad'        => $horas,
            'precio_unitario' => $cuna->precio_por_hora,
            'area_origen'     => 'neonato',
            'user_id'         => auth()->id(),
        ]);

        $uso = CamillaUso::create([
            'camilla_id'              => $cuna->id,
            'paciente_id'             => $neonato->paciente_id,
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

        return redirect()->route('neonato.cunas')
            ->with('success', 'Uso de cuna registrado. Costo: Bs. ' . $costo);
    }

    // -------------------------------------------------------------------------
    // Medicamentos del área — solo lectura
    // -------------------------------------------------------------------------

    public function medicamentos(): View
    {
        $stocks = AlmacenStock::with(['lote.catalogo'])
            ->where('ubicacion', 'neonato')
            ->orderBy('id')
            ->paginate(30);

        return view('admin.neonato.medicamentos', compact('stocks'));
    }

    public function destroyEvaluacion(Neonato $neonato, Evaluacion $evaluacion): RedirectResponse
    {
        abort_unless(
            $evaluacion->area === 'neonato' && $evaluacion->paciente_id === $neonato->paciente_id,
            403
        );

        $evaluacion->items()->delete();
        $evaluacion->delete();

        return redirect()->route('neonato.historial', $neonato->id)
            ->with('success', 'Evaluación eliminada.');
    }

    // -------------------------------------------------------------------------

    public function procedimientos(): View
    {
        $procedimientos = \App\Models\Procedimiento::where('area', 'neonato')
            ->where('activo', true)
            ->orderBy('nombre')
            ->paginate(30);

        return view('neonato.procedimientos', compact('procedimientos'));
    }

    // -------------------------------------------------------------------------
    // API — buscar madre por CI
    // -------------------------------------------------------------------------

    public function buscarMadre(Request $request): JsonResponse
    {
        $ci = $request->get('ci', '');

        $paciente = Paciente::where('ci', $ci)->first();

        if (!$paciente) {
            return response()->json(['found' => false]);
        }

        return response()->json([
            'found'  => true,
            'nombre' => $paciente->nombre,
            'ci'     => $paciente->ci,
        ]);
    }
}
