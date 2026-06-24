<?php

namespace App\Http\Controllers\Admin;

use App\Exports\AlmacenPlantillaExport;
use App\Http\Controllers\Controller;
use App\Imports\AlmacenStockImport;
use App\Models\AlmacenCatalogo;
use App\Models\AlmacenDispensacion;
use App\Models\AlmacenDispensacionDetalle;
use App\Models\AlmacenEntregaDetalle;
use App\Models\AlmacenEntregaPaciente;
use App\Models\AlmacenLote;
use App\Models\AlmacenStock;
use App\Models\Paciente;
use App\Support\Money;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class AlmacenMedicamentosController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin|administrador|almacenista');
    }

    public function index(Request $request)
    {
        $area = $request->filled('area') ? $request->area : 'central';
        $mostrarTodos = $area === 'todos';
        $ubicacion = $mostrarTodos ? null : $area;

        $query = AlmacenCatalogo::query()->with([
            'lotes' => fn ($q) => $q->with([
                'stocks' => fn ($sq) => $ubicacion ? $sq->where('ubicacion', $ubicacion) : $sq,
            ]),
        ]);

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        if ($request->filled('buscar')) {
            $query->where(function ($q) use ($request) {
                $q->where('nombre', 'like', '%'.$request->buscar.'%')
                    ->orWhere('descripcion', 'like', '%'.$request->buscar.'%');
            });
        }

        if ($ubicacion) {
            $query->whereHas('lotes.stocks', function ($q) use ($ubicacion) {
                $q->where('ubicacion', $ubicacion);
            });
        }

        if ($request->filled('estado_stock')) {
            $areaStock = $ubicacion ?? 'central';
            if ($request->estado_stock === 'con_stock') {
                $query->whereHas('stocks', fn ($q) => $q->where('ubicacion', $areaStock)->where('cantidad_actual', '>', 0));
            } elseif ($request->estado_stock === 'agotado') {
                $query->whereDoesntHave('stocks', fn ($q) => $q->where('ubicacion', $areaStock)->where('cantidad_actual', '>', 0));
            } elseif ($request->estado_stock === 'bajo') {
                $query->whereHas('stocks', fn ($q) => $q->where('ubicacion', $areaStock)->where('cantidad_actual', '>', 0)->whereColumn('cantidad_actual', '<=', 'stock_minimo'));
            }
        }

        $catalogo = $query->activos()->orderBy('nombre')->paginate(10);

        $stats = $this->calcularStats();

        return view('admin.almacen-medicamentos.index', compact('catalogo', 'stats', 'area', 'mostrarTodos'));
    }

    public function create()
    {
        $tipos = ['medicamento' => 'Medicamento', 'insumo' => 'Insumo'];
        $unidades = ['unidades', 'ml', 'mg', 'gr', 'cm', 'cajas', 'frascos', 'sobres'];

        return view('admin.almacen-medicamentos.create', compact('tipos', 'unidades'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'nombre_generico' => 'nullable|string|max:255',
            'concentracion' => 'nullable|string|max:100',
            'forma_farmaceutica' => 'nullable|string|max:100',
            'requiere_receta' => 'nullable|boolean',
            'categoria' => 'nullable|string|max:100',
            'codigo_atc' => 'nullable|string|max:20',
            'codigo_liname' => 'nullable|string|max:20',
            'descripcion' => 'nullable|string',
            'unidad_medida' => 'required|string|max:50',
            'tipo' => 'required|in:medicamento,insumo',
            'observaciones' => 'nullable|string',
            // Primer lote (opcional)
            'codigo_lote' => 'nullable|string|max:100',
            'proveedor' => 'nullable|string|max:150',
            'laboratorio' => 'nullable|string|max:150',
            'fecha_vencimiento' => 'nullable|date|after:today',
            'numero_lote_fabricante' => 'nullable|string|max:150',
            'precio_compra' => 'nullable|numeric|decimal:0,2|min:0',
            'ganancia' => Money::rules(false),
            'precio_venta' => 'nullable|numeric|decimal:0,2|min:0',
            'cantidad_inicial' => 'required|integer|min:0',
            'cantidad_recibida' => 'nullable|integer|min:0',
            'stock_minimo' => 'required|integer|min:0',
        ]);

        DB::transaction(function () use ($request) {
            $catalogo = AlmacenCatalogo::create([
                'nombre' => $request->nombre,
                'nombre_generico' => $request->nombre_generico,
                'concentracion' => $request->concentracion,
                'forma_farmaceutica' => $request->forma_farmaceutica,
                'requiere_receta' => $request->has('requiere_receta') ? (bool) $request->requiere_receta : false,
                'categoria' => $request->categoria,
                'codigo_atc' => $request->codigo_atc,
                'codigo_liname' => $request->codigo_liname,
                'descripcion' => $request->descripcion,
                'unidad_medida' => $request->unidad_medida,
                'tipo' => $request->tipo,
                'observaciones' => $request->observaciones,
            ]);

            // Crear primer lote + stock en central
            $lote = AlmacenLote::create([
                'catalogo_id' => $catalogo->id,
                'codigo_lote' => $request->codigo_lote,
                'numero_lote_fabricante' => $request->numero_lote_fabricante,
                'proveedor' => $request->proveedor,
                'laboratorio' => $request->laboratorio,
                'fecha_vencimiento' => $request->fecha_vencimiento,
                'precio_compra' => $request->precio_compra,
                'ganancia' => $request->ganancia,
                'precio_venta' => $request->precio_venta,
                'cantidad_inicial' => $request->cantidad_inicial,
                'cantidad_recibida' => $request->cantidad_recibida ?? $request->cantidad_inicial,
            ]);

            AlmacenStock::create([
                'lote_id' => $lote->id,
                'ubicacion' => 'central',
                'cantidad_actual' => $request->cantidad_inicial,
                'stock_minimo' => $request->stock_minimo,
            ]);

            Log::info('Almacén: nuevo ítem creado: '.$catalogo->nombre, [
                'user_id' => Auth::id(),
                'catalogo_id' => $catalogo->id,
                'lote_id' => $lote->id,
                'action' => 'create',
                'module' => 'almacen',
            ]);
        });

        return redirect()->route('admin.almacen-medicamentos.index')
            ->with('success', 'Medicamento/Insumo agregado correctamente al almacén.');
    }

    public function show(AlmacenCatalogo $almacenMedicamento)
    {
        $almacenMedicamento->load(['lotes.stocks']);

        $entregas = AlmacenEntregaPaciente::with(['paciente', 'entregadoPor'])
            ->where('catalogo_id', $almacenMedicamento->id)
            ->orderByDesc('fecha_entrega')
            ->paginate(50);

        return view('admin.almacen-medicamentos.show', [
            'catalogo' => $almacenMedicamento,
            'entregas' => $entregas,
        ]);
    }

    public function edit(AlmacenCatalogo $almacenMedicamento)
    {
        $tipos = ['medicamento' => 'Medicamento', 'insumo' => 'Insumo'];
        $unidades = ['unidades', 'ml', 'mg', 'gr', 'cm', 'cajas', 'frascos', 'sobres'];
        $ubicaciones = [
            'central' => 'Central',
            'emergencia' => 'Emergencia',
            'cirugia' => 'Cirugía',
            'uti' => 'UTI',
            'neonato' => 'Neonato',
            'internacion' => 'Internación',
        ];

        // Cargar catálogo con lotes y sus stocks
        $almacenMedicamento->load(['lotes.stocks']);

        return view('admin.almacen-medicamentos.edit', [
            'catalogo' => $almacenMedicamento,
            'tipos' => $tipos,
            'unidades' => $unidades,
            'ubicaciones' => $ubicaciones,
        ]);
    }

    public function update(Request $request, AlmacenCatalogo $almacenMedicamento)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'codigo_barras' => ['nullable', 'string', 'max:50', Rule::unique('almacen_catalogo', 'codigo_barras')->ignore($almacenMedicamento->id)],
            'nombre_generico' => 'nullable|string|max:255',
            'concentracion' => 'nullable|string|max:100',
            'forma_farmaceutica' => 'nullable|string|max:100',
            'requiere_receta' => 'nullable|boolean',
            'categoria' => 'nullable|string|max:100',
            'codigo_atc' => 'nullable|string|max:20',
            'codigo_liname' => 'nullable|string|max:20',
            'descripcion' => 'nullable|string',
            'unidad_medida' => 'required|string|max:50',
            'tipo' => 'required|in:medicamento,insumo',
            'observaciones' => 'nullable|string',
            'lotes' => 'nullable|array',
            'lotes.*.id' => 'nullable|integer|exists:almacen_lotes,id',
            'lotes.*.codigo_lote' => 'nullable|string|max:100',
            'lotes.*.numero_lote_fabricante' => 'nullable|string|max:150',
            'lotes.*.proveedor' => 'nullable|string|max:150',
            'lotes.*.laboratorio' => 'nullable|string|max:150',
            'lotes.*.fecha_vencimiento' => 'nullable|date|after:today',
            'lotes.*.precio_compra' => 'nullable|numeric|decimal:0,2|min:0',
            'lotes.*.ganancia' => Money::rules(false),
            'lotes.*.precio_venta' => 'nullable|numeric|decimal:0,2|min:0',
            'lotes.*.cantidad_inicial' => 'required|integer|min:0',
            'lotes.*.cantidad_recibida' => 'nullable|integer|min:0',
            'lotes.*.stocks' => 'nullable|array',
            'lotes.*.stocks.*.ubicacion' => 'required|string|in:central,emergencia,cirugia,hospitalizacion,uti,usi,neonato,internacion',
            'lotes.*.stocks.*.stock_minimo' => 'required|integer|min:0',
            'lotes.*.stocks.*.cantidad_actual' => 'nullable|integer|min:0',
            'lotes.*.stocks.*.cantidad_a_agregar' => 'nullable|integer|min:0',
        ]);

        try {
            DB::transaction(function () use ($request, $almacenMedicamento) {
                $datosCatalogo = $request->only([
                    'nombre', 'nombre_generico', 'concentracion', 'forma_farmaceutica',
                    'categoria', 'codigo_atc', 'codigo_liname',
                    'descripcion', 'unidad_medida', 'tipo', 'observaciones',
                ]);
                $datosCatalogo['codigo_barras'] = $request->filled('codigo_barras') ? $request->codigo_barras : null;
                $datosCatalogo['requiere_receta'] = $request->boolean('requiere_receta');

                $almacenMedicamento->update($datosCatalogo);

                $dispensacionesPorArea = [];

                if ($request->filled('lotes')) {
                    foreach ($request->lotes as $loteData) {
                        if (! empty($loteData['id'])) {
                            $lote = AlmacenLote::where('catalogo_id', $almacenMedicamento->id)
                                ->findOrFail($loteData['id']);
                            $lote->update([
                                'codigo_lote' => $loteData['codigo_lote'],
                                'numero_lote_fabricante' => $loteData['numero_lote_fabricante'] ?? null,
                                'proveedor' => $loteData['proveedor'] ?? null,
                                'laboratorio' => $loteData['laboratorio'] ?? null,
                                'fecha_vencimiento' => $loteData['fecha_vencimiento'],
                                'precio_compra' => $loteData['precio_compra'],
                                'ganancia' => $loteData['ganancia'],
                                'precio_venta' => $loteData['precio_venta'],
                                'cantidad_inicial' => $loteData['cantidad_inicial'],
                                'cantidad_recibida' => $loteData['cantidad_recibida'] ?? $loteData['cantidad_inicial'],
                            ]);
                        } else {
                            $lote = AlmacenLote::create([
                                'catalogo_id' => $almacenMedicamento->id,
                                'codigo_lote' => $loteData['codigo_lote'],
                                'numero_lote_fabricante' => $loteData['numero_lote_fabricante'] ?? null,
                                'proveedor' => $loteData['proveedor'] ?? null,
                                'laboratorio' => $loteData['laboratorio'] ?? null,
                                'fecha_vencimiento' => $loteData['fecha_vencimiento'],
                                'precio_compra' => $loteData['precio_compra'],
                                'ganancia' => $loteData['ganancia'],
                                'precio_venta' => $loteData['precio_venta'],
                                'cantidad_inicial' => $loteData['cantidad_inicial'],
                                'cantidad_recibida' => $loteData['cantidad_recibida'] ?? $loteData['cantidad_inicial'],
                            ]);
                        }

                        if (empty($loteData['stocks'])) {
                            continue;
                        }

                        // Primer paso: procesar central
                        foreach ($loteData['stocks'] as $stockData) {
                            if ($stockData['ubicacion'] !== 'central') {
                                continue;
                            }

                            $stockCentral = AlmacenStock::firstOrNew([
                                'lote_id' => $lote->id,
                                'ubicacion' => 'central',
                            ]);

                            if (! $stockCentral->exists) {
                                $stockCentral->cantidad_actual = $loteData['cantidad_inicial'];
                            }

                            $stockCentral->stock_minimo = $stockData['stock_minimo'];
                            $stockCentral->save();

                            $cantidadAgregar = (int) ($stockData['cantidad_a_agregar'] ?? 0);
                            if ($cantidadAgregar > 0) {
                                $stockCentral->increment('cantidad_actual', $cantidadAgregar);
                            }
                        }

                        // Segundo paso: otras áreas
                        foreach ($loteData['stocks'] as $stockData) {
                            if ($stockData['ubicacion'] === 'central') {
                                continue;
                            }

                            $stock = AlmacenStock::firstOrNew([
                                'lote_id' => $lote->id,
                                'ubicacion' => $stockData['ubicacion'],
                            ]);

                            if (! $stock->exists) {
                                $stock->cantidad_actual = 0;
                            }

                            $stock->stock_minimo = $stockData['stock_minimo'];
                            $stock->save();

                            $cantidadAgregar = (int) ($stockData['cantidad_a_agregar'] ?? 0);
                            if ($cantidadAgregar <= 0) {
                                continue;
                            }

                            $centralFresh = AlmacenStock::where('lote_id', $lote->id)
                                ->where('ubicacion', 'central')
                                ->lockForUpdate()
                                ->first();

                            if (! $centralFresh || $centralFresh->cantidad_actual < $cantidadAgregar) {
                                $disponible = $centralFresh->cantidad_actual ?? 0;
                                throw new \Exception(
                                    "Stock insuficiente en central para {$stockData['ubicacion']} (lote #{$lote->id}). Disponible: {$disponible}, requerido: {$cantidadAgregar}."
                                );
                            }

                            $centralFresh->decrement('cantidad_actual', $cantidadAgregar);
                            $stock->increment('cantidad_actual', $cantidadAgregar);

                            $area = $stockData['ubicacion'];
                            if (! isset($dispensacionesPorArea[$area])) {
                                $dispensacionesPorArea[$area] = AlmacenDispensacion::create([
                                    'ubicacion_origen' => 'central',
                                    'ubicacion_destino' => $area,
                                    'dispensado_por' => Auth::id(),
                                    'fecha_dispensacion' => now(),
                                ]);
                            }

                            AlmacenDispensacionDetalle::create([
                                'dispensacion_id' => $dispensacionesPorArea[$area]->id,
                                'lote_id' => $lote->id,
                                'cantidad' => $cantidadAgregar,
                            ]);
                        }
                    }
                }

                Log::info('Almacén: ítem actualizado con lotes: '.$almacenMedicamento->nombre, [
                    'user_id' => Auth::id(),
                    'catalogo_id' => $almacenMedicamento->id,
                    'action' => 'update_with_lotes',
                    'module' => 'almacen',
                    'lotes_count' => count($request->lotes ?? []),
                    'distribuciones' => array_keys($dispensacionesPorArea),
                ]);
            });
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }

        return redirect()->route('admin.almacen-medicamentos.show', $almacenMedicamento->id)
            ->with('success', 'Medicamento/Insumo y lotes actualizados correctamente.');
    }

    public function destroy(AlmacenCatalogo $almacenMedicamento)
    {
        $almacenMedicamento->update(['activo' => false]);

        Log::info('Almacén: ítem desactivado: '.$almacenMedicamento->nombre, [
            'user_id' => Auth::id(),
            'catalogo_id' => $almacenMedicamento->id,
            'action' => 'deactivate',
            'module' => 'almacen',
        ]);

        return redirect()->route('admin.almacen-medicamentos.index')
            ->with('success', 'Medicamento/Insumo desactivado correctamente.');
    }

    public function actualizarStock(Request $request, AlmacenCatalogo $almacenMedicamento)
    {
        $request->validate([
            'lote_id' => 'required|integer|exists:almacen_lotes,id',
            'ubicacion' => 'required|in:central,emergencia,cirugia,hospitalizacion,uti,usi,neonato,internacion',
            'cantidad' => 'required|integer|min:0',
            'stock_minimo' => 'nullable|integer|min:0',
            'motivo' => 'required|string|max:255',
        ]);

        $stock = AlmacenStock::firstOrNew([
            'lote_id' => $request->lote_id,
            'ubicacion' => $request->ubicacion,
        ]);

        $cantidadAnterior = $stock->cantidad_actual ?? 0;
        $stock->cantidad_actual = $request->cantidad;

        if ($request->filled('stock_minimo')) {
            $stock->stock_minimo = $request->stock_minimo;
        }

        $stock->save();

        Log::info('Almacén: stock actualizado para '.$almacenMedicamento->nombre.': '.$cantidadAnterior.' → '.$request->cantidad.'. Motivo: '.$request->motivo, [
            'user_id' => Auth::id(),
            'catalogo_id' => $almacenMedicamento->id,
            'lote_id' => $request->lote_id,
            'ubicacion' => $request->ubicacion,
            'action' => 'update_stock',
            'module' => 'almacen',
        ]);

        return redirect()->back()->with('success', 'Stock actualizado correctamente.');
    }

    public function reporteBajoStock()
    {
        $stocks = AlmacenStock::with(['lote.catalogo'])
            ->bajoStock()
            ->whereHas('lote.catalogo', fn ($q) => $q->where('activo', true))
            ->orderBy('cantidad_actual')
            ->paginate(10);

        return view('admin.almacen-medicamentos.reporte-bajo-stock', compact('stocks'));
    }

    public function exportarBajoStock()
    {
        $stocks = AlmacenStock::with(['lote.catalogo'])
            ->bajoStock()
            ->whereHas('lote.catalogo', fn ($q) => $q->where('activo', true))
            ->orderBy('cantidad_actual')
            ->get();

        $nombre = 'bajo-stock-'.now()->format('Y-m-d').'.csv';

        return response()->streamDownload(function () use ($stocks) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($file, ['Medicamento/Insumo', 'Lote', 'Ubicación', 'Stock Actual', 'Mínimo', 'Estado'], ';');
            foreach ($stocks as $stock) {
                fputcsv($file, [
                    $stock->lote->catalogo->nombre ?? 'N/A',
                    $stock->lote->codigo_lote ?? '-',
                    $stock->ubicacion_label,
                    $stock->cantidad_actual,
                    $stock->stock_minimo,
                    $stock->cantidad_actual <= 0 ? 'Agotado' : 'Bajo Stock',
                ], ';');
            }
            fclose($file);
        }, $nombre, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Cache-Control' => 'no-cache',
        ]);
    }

    public function reporteVencimiento(Request $request)
    {
        $vencidos = AlmacenLote::with('catalogo')
            ->vencidos()
            ->whereHas('catalogo', fn ($q) => $q->where('activo', true))
            ->orderBy('fecha_vencimiento')
            ->paginate(10, ['*'], 'pag_vencidos');

        $porVencer = AlmacenLote::with('catalogo')
            ->porVencer()
            ->whereHas('catalogo', fn ($q) => $q->where('activo', true))
            ->orderBy('fecha_vencimiento')
            ->paginate(10, ['*'], 'pag_por_vencer');

        return view('admin.almacen-medicamentos.reporte-vencimiento', compact('vencidos', 'porVencer'));
    }

    public function exportarVencimiento()
    {
        $vencidos = AlmacenLote::with('catalogo')
            ->vencidos()
            ->whereHas('catalogo', fn ($q) => $q->where('activo', true))
            ->orderBy('fecha_vencimiento')
            ->get();

        $porVencer = AlmacenLote::with('catalogo')
            ->porVencer()
            ->whereHas('catalogo', fn ($q) => $q->where('activo', true))
            ->orderBy('fecha_vencimiento')
            ->get();

        $nombre = 'vencimiento-'.now()->format('Y-m-d').'.csv';

        return response()->streamDownload(function () use ($vencidos, $porVencer) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, ['LOTES VENCIDOS'], ';');
            fputcsv($file, ['Medicamento/Insumo', 'Código Lote', 'Fecha Vencimiento', 'Días Vencido'], ';');
            foreach ($vencidos as $lote) {
                fputcsv($file, [
                    $lote->catalogo->nombre ?? 'N/A',
                    $lote->codigo_lote ?? '-',
                    $lote->fecha_vencimiento->format('d/m/Y'),
                    abs($lote->dias_para_vencer).' días',
                ], ';');
            }

            fputcsv($file, [], ';');
            fputcsv($file, ['LOTES POR VENCER (próximos 30 días)'], ';');
            fputcsv($file, ['Medicamento/Insumo', 'Código Lote', 'Fecha Vencimiento', 'Días Restantes'], ';');
            foreach ($porVencer as $lote) {
                fputcsv($file, [
                    $lote->catalogo->nombre ?? 'N/A',
                    $lote->codigo_lote ?? '-',
                    $lote->fecha_vencimiento->format('d/m/Y'),
                    $lote->dias_para_vencer.' días',
                ], ';');
            }

            fclose($file);
        }, $nombre, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Cache-Control' => 'no-cache',
        ]);
    }

    public function porArea(string $area)
    {
        $stocks = AlmacenStock::with(['lote.catalogo'])
            ->porUbicacion($area)
            ->whereHas('lote.catalogo', fn ($q) => $q->where('activo', true))
            ->orderByDesc('cantidad_actual')
            ->paginate(20);

        $stats = [
            'total' => $stocks->total(),
            'medicamentos' => $stocks->getCollection()->filter(fn ($s) => $s->lote->catalogo->tipo === 'medicamento')->count(),
            'insumos' => $stocks->getCollection()->filter(fn ($s) => $s->lote->catalogo->tipo === 'insumo')->count(),
            'bajo_stock' => $stocks->getCollection()->filter(fn ($s) => $s->estado_stock === 'bajo')->count(),
            'agotados' => $stocks->getCollection()->filter(fn ($s) => $s->estado_stock === 'agotado')->count(),
        ];

        return view('admin.almacen-medicamentos.por-area', compact('stocks', 'area', 'stats'));
    }

    public function pacientesPorArea(Request $request, AlmacenCatalogo $catalogo)
    {
        $area = $request->get('area');

        $datos = DB::table('almacen_entrega_detalles as aed')
            ->join('almacen_entregas_paciente as aep', 'aed.entrega_id', '=', 'aep.id')
            ->join('almacen_dispensacion_detalles as addet', 'aed.dispensacion_detalle_id', '=', 'addet.id')
            ->join('almacen_dispensaciones as ad', 'addet.dispensacion_id', '=', 'ad.id')
            ->join('almacen_lotes as al', 'addet.lote_id', '=', 'al.id')
            ->join('pacientes as p', 'aep.paciente_id', '=', 'p.id')
            ->where('al.catalogo_id', $catalogo->id)
            ->where('ad.ubicacion_destino', $area)
            ->selectRaw('p.id, p.ci, p.nombre, SUM(aed.cantidad) as total_cantidad')
            ->groupBy('p.id', 'p.ci', 'p.nombre')
            ->orderByDesc('total_cantidad')
            ->get();

        return response()->json($datos);
    }

    public function dispensar(Request $request, AlmacenCatalogo $almacenMedicamento)
    {
        $request->validate([
            'lote_id' => 'required|integer|exists:almacen_lotes,id',
            'cantidad' => 'required|integer|min:1',
            'ubicacion_destino' => 'required|in:emergencia,cirugia,hospitalizacion,uti,usi,neonato,internacion',
            'recibido_por' => 'nullable|string|max:150',
            'observaciones' => 'nullable|string|max:1000',
        ]);

        $stockCentral = AlmacenStock::where('lote_id', $request->lote_id)
            ->where('ubicacion', 'central')
            ->lockForUpdate()
            ->first();

        if (! $stockCentral || $stockCentral->cantidad_actual < $request->cantidad) {
            $disponible = $stockCentral->cantidad_actual ?? 0;

            return redirect()->back()
                ->with('error', "Stock insuficiente. Disponible en central: {$disponible}.");
        }

        DB::transaction(function () use ($request, $almacenMedicamento, $stockCentral) {
            // 1. Descontar del stock central
            $stockCentral->decrement('cantidad_actual', $request->cantidad);

            // 2. Incrementar (o crear) stock en destino
            $stockDestino = AlmacenStock::firstOrNew([
                'lote_id' => $request->lote_id,
                'ubicacion' => $request->ubicacion_destino,
            ]);
            $stockDestino->cantidad_actual = ($stockDestino->cantidad_actual ?? 0) + $request->cantidad;
            $stockDestino->stock_minimo = $stockDestino->stock_minimo ?? 0;
            $stockDestino->save();

            // 3. Registrar dispensación con su detalle
            $dispensacion = AlmacenDispensacion::create([
                'ubicacion_origen' => 'central',
                'ubicacion_destino' => $request->ubicacion_destino,
                'dispensado_por' => Auth::id(),
                'recibido_por' => $request->recibido_por,
                'observaciones' => $request->observaciones,
                'fecha_dispensacion' => now(),
            ]);

            AlmacenDispensacionDetalle::create([
                'dispensacion_id' => $dispensacion->id,
                'lote_id' => $request->lote_id,
                'cantidad' => $request->cantidad,
            ]);

            Log::info('Almacén: dispensación '.$almacenMedicamento->nombre.' x'.$request->cantidad.' → '.$request->ubicacion_destino, [
                'user_id' => Auth::id(),
                'catalogo_id' => $almacenMedicamento->id,
                'lote_id' => $request->lote_id,
                'dispensacion_id' => $dispensacion->id,
                'action' => 'dispensar',
                'module' => 'almacen',
            ]);
        });

        return redirect()->back()->with('success', 'Dispensación registrada correctamente.');
    }

    public function historialDispensaciones(Request $request)
    {
        $query = AlmacenDispensacion::with([
            'detalles.lote.catalogo',
            'dispensadoPor',
        ]);

        if ($request->filled('ubicacion_destino')) {
            $query->porDestino($request->ubicacion_destino);
        }

        if ($request->filled('fecha_desde')) {
            $query->where('fecha_dispensacion', '>=', $request->fecha_desde.' 00:00:00');
        }

        if ($request->filled('fecha_hasta')) {
            $query->where('fecha_dispensacion', '<=', $request->fecha_hasta.' 23:59:59');
        }

        if ($request->filled('buscar')) {
            $query->whereHas('detalles.lote.catalogo', function ($q) use ($request) {
                $q->where('nombre', 'like', '%'.$request->buscar.'%');
            });
        }

        $dispensaciones = $query->recientes()->paginate(10);

        $stats = [
            'total' => AlmacenDispensacion::count(),
            'ultimos_30_dias' => AlmacenDispensacion::where('fecha_dispensacion', '>=', now()->subDays(30))->count(),
            'areas_activas' => AlmacenDispensacion::distinct('ubicacion_destino')->count('ubicacion_destino'),
        ];

        $areas = [
            'emergencia' => 'Emergencia',
            'cirugia' => 'Cirugía',
            'hospitalizacion' => 'Hospitalización',
            'uti' => 'UTI',
            'usi' => 'USI',
            'neonato' => 'Neonato',
            'internacion' => 'Internación',
        ];

        return view('admin.almacen-medicamentos.historial-dispensaciones', compact('dispensaciones', 'stats', 'areas'));
    }

    public function exportarHistorial(Request $request)
    {
        $query = AlmacenDispensacion::with(['detalles.lote.catalogo', 'dispensadoPor']);

        if ($request->filled('ubicacion_destino')) {
            $query->porDestino($request->ubicacion_destino);
        }
        if ($request->filled('fecha_desde')) {
            $query->where('fecha_dispensacion', '>=', $request->fecha_desde.' 00:00:00');
        }
        if ($request->filled('fecha_hasta')) {
            $query->where('fecha_dispensacion', '<=', $request->fecha_hasta.' 23:59:59');
        }
        if ($request->filled('buscar')) {
            $query->whereHas('detalles.lote.catalogo', fn ($q) => $q->where('nombre', 'like', '%'.$request->buscar.'%'));
        }

        $dispensaciones = $query->recientes()->get();
        $nombre = 'dispensaciones-'.now()->format('Y-m-d').'.csv';

        return response()->streamDownload(function () use ($dispensaciones) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($file, ['#', 'Fecha', 'Hora', 'Ítems', 'Cantidades', 'Área Destino', 'Dispensado por', 'Recibido por'], ';');
            foreach ($dispensaciones as $d) {
                $items = $d->detalles->map(fn ($det) => $det->lote->catalogo->nombre ?? 'N/A')->join(' | ');
                $cantidades = $d->detalles->map(fn ($det) => $det->cantidad)->join(' | ');
                fputcsv($file, [
                    '#'.$d->id,
                    $d->fecha_dispensacion->format('d/m/Y'),
                    $d->fecha_dispensacion->format('H:i'),
                    $items,
                    $cantidades,
                    $d->ubicacion_destino_label,
                    $d->dispensadoPor->name ?? 'N/A',
                    $d->recibido_por ?? '-',
                ], ';');
            }
            fclose($file);
        }, $nombre, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Cache-Control' => 'no-cache',
        ]);
    }

    public function detalleDispensacion(AlmacenDispensacion $dispensacion)
    {
        $dispensacion->load([
            'detalles.lote.catalogo',
            'detalles.entregaDetalles.entrega.paciente',
            'detalles.entregaDetalles.entrega.entregadoPor',
            'dispensadoPor',
        ]);

        return view('admin.almacen-medicamentos.detalle-dispensacion', compact('dispensacion'));
    }

    public function registrarPaciente(Request $request, AlmacenDispensacion $dispensacion)
    {
        $request->validate([
            'paciente_id' => 'required|integer|exists:pacientes,id',
            'observaciones' => 'nullable|string|max:1000',
            'detalles' => 'required|array|min:1',
            'detalles.*.detalle_id' => 'required|integer|exists:almacen_dispensacion_detalles,id',
            'detalles.*.cantidad' => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($request, $dispensacion) {
            $entrega = AlmacenEntregaPaciente::create([
                'paciente_id' => $request->paciente_id,
                'entregado_por' => Auth::id(),
                'observaciones' => $request->observaciones,
                'fecha_entrega' => now(),
            ]);

            foreach ($request->detalles as $item) {
                AlmacenEntregaDetalle::create([
                    'entrega_id' => $entrega->id,
                    'dispensacion_detalle_id' => $item['detalle_id'],
                    'cantidad' => $item['cantidad'],
                ]);
            }

            Log::info('Almacén: entrega registrada a paciente ID:'.$request->paciente_id, [
                'user_id' => Auth::id(),
                'dispensacion_id' => $dispensacion->id,
                'entrega_id' => $entrega->id,
                'action' => 'registrar_paciente',
                'module' => 'almacen',
            ]);
        });

        return redirect()
            ->route('admin.almacen-medicamentos.detalle-dispensacion', $dispensacion->id)
            ->with('success', 'Entrega al paciente registrada correctamente.');
    }

    public function historialItem(AlmacenCatalogo $almacenMedicamento)
    {
        $dispensaciones = AlmacenDispensacion::with(['detalles.lote', 'dispensadoPor'])
            ->whereHas('detalles.lote', fn ($q) => $q->where('catalogo_id', $almacenMedicamento->id))
            ->recientes()
            ->paginate(15);

        return view('admin.almacen-medicamentos.historial-item', [
            'catalogo' => $almacenMedicamento,
            'dispensaciones' => $dispensaciones,
        ]);
    }

    /**
     * Formulario de AJUSTE DE INVENTARIO del almacén central.
     *
     * A diferencia de "Registrar Lote" (ingreso de mercadería), esto NO crea lotes:
     * opera sobre filas de stock central YA existentes para corregir la cantidad
     * tras un conteo físico (mermas/sobrantes). Cada ajuste apunta a un lote
     * concreto, preservando la trazabilidad lote↔compra↔vencimiento.
     */
    public function ajusteInventarioForm()
    {
        $stocks = AlmacenStock::where('ubicacion', 'central')
            ->whereHas('lote.catalogo', fn ($q) => $q->activos())
            ->with(['lote.catalogo'])
            ->get()
            ->map(fn ($s) => [
                'stock_id' => $s->id,
                'nombre' => $s->lote->catalogo->nombre,
                'descripcion' => $s->lote->catalogo->descripcion,
                'unidad' => $s->lote->catalogo->unidad_medida,
                'tipo' => $s->lote->catalogo->tipo,
                'lote' => $s->lote->codigo_lote ?: ('Lote #'.$s->lote->id),
                'proveedor' => $s->lote->proveedor,
                'vencimiento' => optional($s->lote->fecha_vencimiento)->format('d/m/Y'),
                'estado_venc' => $s->lote->estado_vencimiento,
                'stock' => (int) $s->cantidad_actual,
                'stock_minimo' => (int) $s->stock_minimo,
            ])
            ->sortBy(fn ($m) => [
                match (true) {
                    $m['stock'] <= 0 => 0,
                    $m['stock_minimo'] > 0 && $m['stock'] <= $m['stock_minimo'] => 1,
                    default => 2,
                },
                $m['nombre'],
            ])
            ->values();

        return view('admin.almacen-medicamentos.ajuste-inventario', compact('stocks'));
    }

    public function procesarAjusteInventario(Request $request)
    {
        $request->validate([
            'motivo' => 'required|string|max:255',
            'items' => 'required|string',
        ]);

        $items = json_decode($request->items, true);

        if (! is_array($items) || count($items) === 0) {
            return redirect()->back()->with('error', 'Debe registrar el conteo de al menos un ítem.')->withInput();
        }

        foreach ($items as $i => $item) {
            if (! isset($item['stock_id'], $item['contado']) || (int) $item['stock_id'] <= 0 || (int) $item['contado'] < 0) {
                return redirect()->back()->with('error', 'Datos inválidos en ítem #'.($i + 1))->withInput();
            }
        }

        try {
            $ajustados = 0;

            DB::transaction(function () use ($items, $request, &$ajustados) {
                foreach ($items as $item) {
                    $stockId = (int) $item['stock_id'];
                    $contado = (int) $item['contado'];

                    // Solo stock central existente: nunca se crean lotes en un ajuste.
                    $stock = AlmacenStock::where('id', $stockId)
                        ->where('ubicacion', 'central')
                        ->lockForUpdate()
                        ->first();

                    if (! $stock) {
                        continue;
                    }

                    $anterior = (int) $stock->cantidad_actual;

                    if ($contado === $anterior) {
                        continue; // sin cambios reales
                    }

                    $delta = $contado - $anterior;
                    $stock->cantidad_actual = $contado;
                    $stock->save();
                    $ajustados++;

                    Log::info("Almacén: ajuste de inventario lote #{$stock->lote_id} {$anterior}→{$contado} (Δ{$delta}). Motivo: {$request->motivo}", [
                        'user_id' => Auth::id(),
                        'lote_id' => $stock->lote_id,
                        'stock_id' => $stock->id,
                        'cantidad_anterior' => $anterior,
                        'cantidad_nueva' => $contado,
                        'delta' => $delta,
                        'motivo' => $request->motivo,
                        'action' => 'ajuste_inventario',
                        'module' => 'almacen',
                    ]);
                }
            });

            if ($ajustados === 0) {
                return redirect()->back()->with('error', 'Ningún ítem tenía diferencia de stock para ajustar.')->withInput();
            }

            return redirect()->route('admin.almacen-medicamentos.index')
                ->with('success', 'Inventario ajustado correctamente para '.$ajustados.' lote(s).');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al ajustar inventario: '.$e->getMessage())
                ->withInput();
        }
    }

    /**
     * Formulario para registrar un LOTE sobre un medicamento/insumo YA existente.
     * Flujo principal tras la precarga LINAME: el catálogo ya está, solo se ingresa
     * la mercadería (lote + cantidad). Si el producto no existe, se enlaza a "crear".
     */
    public function loteForm(Request $request)
    {
        $catalogos = AlmacenCatalogo::activos()
            ->orderBy('nombre')
            ->get(['id', 'nombre', 'concentracion', 'forma_farmaceutica', 'unidad_medida', 'tipo', 'codigo_atc'])
            ->map(fn ($c) => [
                'id' => $c->id,
                'nombre' => $c->nombre,
                'concentracion' => $c->concentracion,
                'forma' => $c->forma_farmaceutica,
                'unidad' => $c->unidad_medida,
                'tipo' => $c->tipo,
                'atc' => $c->codigo_atc,
            ])
            ->values();

        return view('admin.almacen-medicamentos.lote', [
            'catalogos' => $catalogos,
            'areas' => self::AREAS_IMPORT,
            'preseleccion' => $request->integer('catalogo_id') ?: null,
        ]);
    }

    public function loteStore(Request $request)
    {
        $request->validate([
            'catalogo_id' => 'required|integer|exists:almacen_catalogo,id',
            'ubicacion' => 'required|in:'.implode(',', array_keys(self::AREAS_IMPORT)),
            'codigo_lote' => 'nullable|string|max:100',
            'numero_lote_fabricante' => 'nullable|string|max:150',
            'proveedor' => 'nullable|string|max:150',
            'laboratorio' => 'nullable|string|max:150',
            'fecha_vencimiento' => 'nullable|date|after:today',
            'precio_compra' => 'nullable|numeric|decimal:0,2|min:0',
            'ganancia' => Money::rules(false),
            'precio_venta' => 'nullable|numeric|decimal:0,2|min:0',
            'cantidad' => 'required|integer|min:0',
            'cantidad_recibida' => 'nullable|integer|min:0',
            'stock_minimo' => 'required|integer|min:0',
        ]);

        $catalogo = AlmacenCatalogo::findOrFail($request->catalogo_id);

        DB::transaction(function () use ($request, $catalogo) {
            $lote = AlmacenLote::create([
                'catalogo_id' => $catalogo->id,
                'codigo_lote' => $request->codigo_lote,
                'numero_lote_fabricante' => $request->numero_lote_fabricante,
                'proveedor' => $request->proveedor,
                'laboratorio' => $request->laboratorio,
                'fecha_vencimiento' => $request->fecha_vencimiento,
                'precio_compra' => $request->precio_compra,
                'ganancia' => $request->ganancia,
                'precio_venta' => $request->precio_venta,
                'cantidad_inicial' => $request->cantidad,
                'cantidad_recibida' => $request->cantidad_recibida ?? $request->cantidad,
            ]);

            AlmacenStock::create([
                'lote_id' => $lote->id,
                'ubicacion' => $request->ubicacion,
                'cantidad_actual' => $request->cantidad,
                'stock_minimo' => $request->stock_minimo,
            ]);

            Log::info('Almacén: nuevo lote registrado para '.$catalogo->nombre.' (+'.$request->cantidad.' en '.$request->ubicacion.')', [
                'user_id' => Auth::id(),
                'catalogo_id' => $catalogo->id,
                'lote_id' => $lote->id,
                'ubicacion' => $request->ubicacion,
                'action' => 'crear_lote',
                'module' => 'almacen',
            ]);
        });

        return redirect()->route('admin.almacen-medicamentos.show', $catalogo->id)
            ->with('success', 'Lote registrado correctamente para '.$catalogo->nombre.'.');
    }

    private const AREAS_IMPORT = [
        'central' => 'Central',
        'farmacia' => 'Farmacia',
        'emergencia' => 'Emergencia',
        'cirugia' => 'Cirugía',
        'hospitalizacion' => 'Hospitalización',
        'uti' => 'UTI',
        'usi' => 'USI',
        'neonato' => 'Neonato',
        'internacion' => 'Internación',
    ];

    public function importarForm()
    {
        return view('admin.almacen-medicamentos.importar', [
            'areas' => self::AREAS_IMPORT,
        ]);
    }

    public function descargarPlantilla()
    {
        return Excel::download(new AlmacenPlantillaExport, 'plantilla-importacion-almacen.xlsx');
    }

    public function previsualizarImportacion(Request $request)
    {
        $request->validate([
            'archivo' => 'required|file|mimes:xlsx,xls,csv,txt',
            'area' => 'required|in:'.implode(',', array_keys(self::AREAS_IMPORT)),
            'modo' => 'nullable|in:sumar,reemplazar', // se elige en la previsualización
            'motivo' => 'required|string|max:255',
            'crear_nuevos' => 'nullable|boolean',
        ]);

        $area = $request->area;
        $crearNuevos = $request->boolean('crear_nuevos');

        try {
            $import = new AlmacenStockImport;
            Excel::import($import, $request->file('archivo'));
            $rows = $import->rows;
        } catch (\Throwable $e) {
            return redirect()->back()
                ->with('error', 'No se pudo leer el archivo. Verifica que sea un Excel/CSV con las columnas de la plantilla. '.$e->getMessage())
                ->withInput();
        }

        // Catálogo activo indexado por nombre normalizado, con stock del área seleccionada
        $catalogos = AlmacenCatalogo::activos()
            ->withSum(['stocks as stock_area' => fn ($q) => $q->where('ubicacion', $area)], 'cantidad_actual')
            ->get()
            ->keyBy(fn ($c) => mb_strtolower(trim($c->nombre)));

        $items = [];
        $errores = [];
        $vistos = [];

        foreach ($rows as $i => $row) {
            $linea = $i + 2; // +2: fila 1 = encabezados
            $nombre = trim((string) ($row['nombre'] ?? ''));
            $cantidadRaw = $row['cantidad'] ?? null;

            if ($nombre === '' && ($cantidadRaw === null || $cantidadRaw === '')) {
                continue; // fila vacía
            }

            if ($nombre === '') {
                $errores[] = "Fila {$linea}: falta el nombre.";

                continue;
            }

            if (! is_numeric($cantidadRaw) || (int) $cantidadRaw < 0) {
                $errores[] = "Fila {$linea}: cantidad inválida para \"{$nombre}\".";

                continue;
            }

            $key = mb_strtolower($nombre);

            // Duplicado real = mismo producto + mismo proveedor + laboratorio + código de lote.
            // El mismo nombre con distinto lab/proveedor es válido (son lotes distintos).
            $prov = trim((string) ($row['proveedor'] ?? ''));
            $lab = trim((string) ($row['laboratorio'] ?? ''));
            $cod = trim((string) ($row['codigo_lote'] ?? ''));
            $dedupKey = mb_strtolower("{$nombre}|{$prov}|{$lab}|{$cod}");

            if (isset($vistos[$dedupKey])) {
                $errores[] = "Fila {$linea}: \"{$nombre}\" con el mismo proveedor/laboratorio/lote ya aparece en fila {$vistos[$dedupKey]}.";

                continue;
            }
            $vistos[$dedupKey] = $linea;

            $cantidad = (int) $cantidadRaw;
            $cat = $catalogos->get($key);
            $existe = (bool) $cat;
            $stockActual = $existe ? (int) ($cat->stock_area ?? 0) : 0;
            $candidatos = $this->buscarCandidatos($key, $catalogos);

            $items[] = [
                'linea' => $linea,
                'nombre' => $nombre,
                'catalogo_id' => $cat?->id,
                'existe' => $existe,
                'candidatos' => $candidatos,
                'stock_actual' => $stockActual,
                'cantidad' => $cantidad,
                'tipo' => in_array(strtolower(trim((string) ($row['tipo'] ?? ''))), ['insumo'], true) ? 'insumo' : 'medicamento',
                'unidad' => trim((string) ($row['unidad'] ?? '')) ?: 'unidades',
                'proveedor' => trim((string) ($row['proveedor'] ?? '')) ?: null,
                'laboratorio' => trim((string) ($row['laboratorio'] ?? '')) ?: null,
                'stock_minimo' => is_numeric($row['stock_minimo'] ?? null) ? (int) $row['stock_minimo'] : null,
                'codigo_lote' => trim((string) ($row['codigo_lote'] ?? '')) ?: null,
                'numero_lote_fabricante' => trim((string) ($row['numero_lote_fabricante'] ?? '')) ?: null,
                'cantidad_recibida' => is_numeric($row['cantidad_recibida'] ?? null) ? (int) $row['cantidad_recibida'] : null,
                'fecha_vencimiento' => $this->parseFechaExcel($row['fecha_vencimiento'] ?? null),
                'precio_compra' => is_numeric($row['precio_compra'] ?? null) ? (float) $row['precio_compra'] : null,
                'precio_venta' => is_numeric($row['precio_venta'] ?? null) ? (float) $row['precio_venta'] : null,
                'descripcion' => trim((string) ($row['descripcion'] ?? '')) ?: null,
                'nombre_generico' => trim((string) ($row['nombre_generico'] ?? '')) ?: null,
                'concentracion' => trim((string) ($row['concentracion'] ?? '')) ?: null,
                'forma_farmaceutica' => trim((string) ($row['forma_farmaceutica'] ?? '')) ?: null,
                'categoria' => trim((string) ($row['categoria'] ?? '')) ?: null,
                'codigo_atc' => trim((string) ($row['codigo_atc'] ?? '')) ?: null,
                'codigo_liname' => trim((string) ($row['codigo_liname'] ?? '')) ?: null,
                'requiere_receta' => in_array(strtolower(trim((string) ($row['requiere_receta'] ?? ''))), ['1', 'si', 'sí', 'true', 's'], true) ? 1 : 0,
            ];
        }

        if (count($items) === 0) {
            return redirect()->back()
                ->with('error', 'El archivo no contiene filas válidas. '.(count($errores) ? implode(' ', array_slice($errores, 0, 5)) : ''))
                ->withInput();
        }

        $resumen = [
            'total' => count($items),
            'coinciden' => collect($items)->where('existe', true)->count(),
            'nuevos' => collect($items)->where('existe', false)->count(),
            'a_omitir' => $crearNuevos ? 0 : collect($items)->where('existe', false)->count(),
            'con_errores' => count($errores),
        ];

        return view('admin.almacen-medicamentos.importar-preview', [
            'items' => $items,
            'errores' => $errores,
            'resumen' => $resumen,
            'area' => $area,
            'areaLabel' => self::AREAS_IMPORT[$area],
            'modo' => $request->modo ?? 'sumar',
            'motivo' => $request->motivo,
            'crearNuevos' => $crearNuevos,
        ]);
    }

    public function confirmarImportacion(Request $request)
    {
        $request->validate([
            'area' => 'required|in:'.implode(',', array_keys(self::AREAS_IMPORT)),
            'modo' => 'required|in:sumar,reemplazar',
            'motivo' => 'required|string|max:255',
            'crear_nuevos' => 'nullable|boolean',
            'items' => 'required|string',
        ]);

        $area = $request->area;
        $modo = $request->modo;
        $crearNuevos = $request->boolean('crear_nuevos');
        $items = json_decode($request->items, true);

        if (! is_array($items) || count($items) === 0) {
            return redirect()->route('admin.almacen-medicamentos.importar.form')
                ->with('error', 'No hay ítems para importar.');
        }

        $creados = 0;
        $actualizados = 0;
        $omitidos = 0;

        try {
            DB::transaction(function () use ($items, $area, $modo, $crearNuevos, $request, &$creados, &$actualizados, &$omitidos) {
                $cacheNombre = []; // nombre normalizado => catálogo (evita crear el mismo producto 2 veces en un import)

                foreach ($items as $item) {
                    $nombre = trim((string) ($item['nombre'] ?? ''));
                    $cantidad = (int) ($item['cantidad'] ?? 0);

                    if ($nombre === '' || $cantidad < 0) {
                        continue;
                    }

                    // catalogo_id viene resuelto desde la previsualización (selección/desambiguación)
                    $catId = $item['catalogo_id'] ?? null;
                    $cat = $catId ? AlmacenCatalogo::find($catId) : null;
                    $nkey = mb_strtolower($nombre);

                    if (! $cat) {
                        if (! $crearNuevos) {
                            $omitidos++;

                            continue;
                        }

                        // Reusar si otra fila del mismo import ya creó/ubicó este producto
                        $cat = $cacheNombre[$nkey]
                            ?? AlmacenCatalogo::activos()->whereRaw('LOWER(nombre) = ?', [$nkey])->first();

                        if (! $cat) {
                            $cat = AlmacenCatalogo::create([
                                'nombre' => $nombre,
                                'descripcion' => $item['descripcion'] ?? null,
                                'unidad_medida' => $item['unidad'] ?? 'unidades',
                                'tipo' => ($item['tipo'] ?? 'medicamento') === 'insumo' ? 'insumo' : 'medicamento',
                                'nombre_generico' => $item['nombre_generico'] ?? null,
                                'concentracion' => $item['concentracion'] ?? null,
                                'forma_farmaceutica' => $item['forma_farmaceutica'] ?? null,
                                'categoria' => $item['categoria'] ?? null,
                                'codigo_atc' => $item['codigo_atc'] ?? null,
                                'codigo_liname' => $item['codigo_liname'] ?? null,
                                'requiere_receta' => $item['requiere_receta'] ?? 0,
                            ]);
                            $creados++;
                        } else {
                            $cat->fill([
                                'descripcion' => $item['descripcion'] ?? $cat->descripcion,
                                'unidad_medida' => $item['unidad'] ?? $cat->unidad_medida,
                                'tipo' => ($item['tipo'] ?? $cat->tipo) === 'insumo' ? 'insumo' : 'medicamento',
                                'nombre_generico' => $item['nombre_generico'] ?? $cat->nombre_generico,
                                'concentracion' => $item['concentracion'] ?? $cat->concentracion,
                                'forma_farmaceutica' => $item['forma_farmaceutica'] ?? $cat->forma_farmaceutica,
                                'categoria' => $item['categoria'] ?? $cat->categoria,
                                'codigo_atc' => $item['codigo_atc'] ?? $cat->codigo_atc,
                                'codigo_liname' => $item['codigo_liname'] ?? $cat->codigo_liname,
                                'requiere_receta' => $item['requiere_receta'] ?? $cat->requiere_receta,
                            ]);
                            $cat->save();
                        }
                    }

                    $cacheNombre[$nkey] = $cat;

                    $codigo = $item['codigo_lote'] ?? null;
                    $prov   = $item['proveedor'] ?? null;
                    $lab    = $item['laboratorio'] ?? null;

                    $lote = AlmacenLote::where('catalogo_id', $cat->id)
                        ->when($codigo, fn ($q) => $q->where('codigo_lote', $codigo))
                        ->when(! $codigo && ($prov || $lab), fn ($q) => $q
                            ->when($prov, fn ($q2) => $q2->where('proveedor', $prov))
                            ->when($lab,  fn ($q2) => $q2->where('laboratorio', $lab))
                        )
                        ->first();

                    if (! $lote) {
                        $lote = AlmacenLote::create([
                            'catalogo_id' => $cat->id,
                            'codigo_lote' => $codigo,
                            'proveedor' => $prov,
                            'laboratorio' => $lab,
                            'numero_lote_fabricante' => $item['numero_lote_fabricante'] ?? null,
                            'cantidad_recibida' => $item['cantidad_recibida'] ?? $cantidad,
                            'fecha_vencimiento' => $item['fecha_vencimiento'] ?? null,
                            'precio_compra' => $item['precio_compra'] ?? null,
                            'precio_venta' => $item['precio_venta'] ?? null,
                            'cantidad_inicial' => $cantidad,
                        ]);
                    } else {
                        // Completar datos del lote solo si vienen en el Excel y faltan
                        $loteUpdate = [];
                        if (! empty($item['fecha_vencimiento']) && empty($lote->fecha_vencimiento)) {
                            $loteUpdate['fecha_vencimiento'] = $item['fecha_vencimiento'];
                        }
                        if (isset($item['precio_compra']) && $item['precio_compra'] !== null && (float) $lote->precio_compra <= 0) {
                            $loteUpdate['precio_compra'] = $item['precio_compra'];
                        }
                        if (isset($item['precio_venta']) && $item['precio_venta'] !== null && (float) $lote->precio_venta <= 0) {
                            $loteUpdate['precio_venta'] = $item['precio_venta'];
                        }
                        if ($loteUpdate) {
                            $lote->update($loteUpdate);
                        }
                    }

                    $stock = AlmacenStock::firstOrNew([
                        'lote_id' => $lote->id,
                        'ubicacion' => $area,
                    ]);

                    $anterior = $stock->cantidad_actual ?? 0;
                    $stock->cantidad_actual = $modo === 'reemplazar' ? $cantidad : $anterior + $cantidad;

                    if (isset($item['stock_minimo']) && $item['stock_minimo'] !== null) {
                        $stock->stock_minimo = (int) $item['stock_minimo'];
                    } elseif (! $stock->exists) {
                        $stock->stock_minimo = 0;
                    }

                    $stock->save();

                    Log::info("Almacén: importación Excel \"{$nombre}\" {$modo} {$cantidad} en {$area} ({$anterior} → {$stock->cantidad_actual}). Motivo: {$request->motivo}", [
                        'user_id' => Auth::id(),
                        'catalogo_id' => $cat->id,
                        'lote_id' => $lote->id,
                        'ubicacion' => $area,
                        'modo' => $modo,
                        'action' => 'importar_excel',
                        'module' => 'almacen',
                    ]);
                }
            });
        } catch (\Throwable $e) {
            return redirect()->route('admin.almacen-medicamentos.importar.form')
                ->with('error', 'Error al importar: '.$e->getMessage());
        }

        $msg = "Importación completada en {$this->areaLabel($area)}: {$actualizados} actualizados, {$creados} nuevos"
            .($omitidos ? ", {$omitidos} omitidos (no existían)" : '').'.';

        return redirect()->route('admin.almacen-medicamentos.index', ['area' => $area])
            ->with('success', $msg);
    }

    private function areaLabel(string $area): string
    {
        return self::AREAS_IMPORT[$area] ?? ucfirst($area);
    }

    /**
     * Productos del catálogo parecidos al nombre importado (mismo principio activo / contiene),
     * para el desplegable de desambiguación en la previsualización.
     */
    private function buscarCandidatos(string $key, $catalogos): array
    {
        $primera = explode(' ', $key)[0] ?? $key;
        $out = [];

        foreach ($catalogos as $k => $c) {
            $coincide = $k === $key
                || str_contains($k, $key)
                || str_contains($key, $k)
                || ($primera !== '' && mb_strlen($primera) >= 3 && str_starts_with($k, $primera));

            if (! $coincide) {
                continue;
            }

            $out[] = [
                'id' => $c->id,
                'nombre' => $c->nombre,
                'stock' => (int) ($c->stock_area ?? 0),
                'exacto' => $k === $key,
            ];
        }

        usort($out, fn ($a, $b) => ($b['exacto'] <=> $a['exacto']) ?: strcmp($a['nombre'], $b['nombre']));

        return array_slice($out, 0, 15);
    }

    private function parseFechaExcel($valor): ?string
    {
        if ($valor === null || $valor === '') {
            return null;
        }

        if (is_numeric($valor)) {
            try {
                return Date::excelToDateTimeObject((float) $valor)->format('Y-m-d');
            } catch (\Throwable $e) {
                return null;
            }
        }

        try {
            return Carbon::parse($valor)->format('Y-m-d');
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function transferirForm()
    {
        // Transferencia POR LOTE: cada lote (proveedor/lab/precio distinto) es una fila
        // seleccionable, para no aplastar SAE y Bagó en un único total sin precio.
        $lotes = AlmacenLote::query()
            ->whereHas('catalogo', fn ($q) => $q->activos())
            ->whereHas('stocks', fn ($q) => $q->where('ubicacion', 'central')->where('cantidad_actual', '>', 0))
            ->with([
                'catalogo:id,nombre,unidad_medida',
                'stocks' => fn ($q) => $q->where('ubicacion', 'central'),
            ])
            ->get()
            ->map(fn ($l) => [
                'lote_id'      => $l->id,
                'catalogo_id'  => $l->catalogo_id,
                'nombre'       => $l->catalogo->nombre,
                'unidad'       => $l->catalogo->unidad_medida,
                'codigo'       => $l->codigo_lote ?: ('Lote #'.$l->id),
                'proveedor'    => $l->proveedor,
                'laboratorio'  => $l->laboratorio,
                'precio_venta' => $l->precio_venta !== null ? (float) $l->precio_venta : null,
                'vencimiento'  => optional($l->fecha_vencimiento)->format('d/m/Y'),
                'venc_orden'   => optional($l->fecha_vencimiento)->format('Y-m-d') ?? '9999-12-31',
                'stock'        => (int) $l->stocks->sum('cantidad_actual'),
            ])
            ->sortBy([['nombre', 'asc'], ['venc_orden', 'asc']])
            ->values();

        return view('admin.almacen-medicamentos.transferir', compact('lotes'));
    }

    public function procesarTransferencia(Request $request)
    {
        $request->validate([
            'recibido_por' => 'nullable|string|max:150',
            'data' => 'required|string',
        ]);

        $areasValidas = ['farmacia', 'emergencia', 'cirugia', 'hospitalizacion', 'uti', 'usi', 'neonato', 'internacion'];
        $data = json_decode($request->data, true);

        if (! is_array($data) || count($data) === 0) {
            return redirect()->back()->with('error', 'Debe ingresar cantidades para al menos un área.')->withInput();
        }

        foreach ($data as $area => $items) {
            if (! in_array($area, $areasValidas)) {
                return redirect()->back()->with('error', "Área inválida: {$area}")->withInput();
            }
            if (! is_array($items) || count($items) === 0) {
                return redirect()->back()->with('error', "Sin ítems para área {$area}.")->withInput();
            }
            foreach ($items as $i => $item) {
                if (! isset($item['lote_id'], $item['cantidad']) || (int) $item['cantidad'] <= 0) {
                    return redirect()->back()->with('error', "Datos inválidos en {$area}, ítem #".($i + 1))->withInput();
                }
            }
        }

        try {
            $totalItems = 0;
            $areasAfectadas = [];

            DB::transaction(function () use ($data, $request, &$totalItems, &$areasAfectadas) {
                foreach ($data as $area => $items) {
                    $dispensacion = AlmacenDispensacion::create([
                        'ubicacion_origen' => 'central',
                        'ubicacion_destino' => $area,
                        'dispensado_por' => Auth::id(),
                        'recibido_por' => $request->recibido_por,
                        'fecha_dispensacion' => now(),
                    ]);

                    foreach ($items as $item) {
                        $loteId = (int) $item['lote_id'];
                        $cantidad = (int) $item['cantidad'];

                        // Se mueve el LOTE EXACTO elegido por el operador (no FIFO automático),
                        // para preservar la separación de precios/ganancias por proveedor.
                        $stockCentral = AlmacenStock::where('lote_id', $loteId)
                            ->where('ubicacion', 'central')
                            ->lockForUpdate()
                            ->first();

                        if (! $stockCentral || $stockCentral->cantidad_actual < $cantidad) {
                            $disponible = $stockCentral->cantidad_actual ?? 0;
                            throw new \Exception("Stock insuficiente para lote #{$loteId} → {$area}. Disponible: {$disponible}, requerido: {$cantidad}.");
                        }

                        $stockCentral->decrement('cantidad_actual', $cantidad);

                        $stockDestino = AlmacenStock::firstOrNew([
                            'lote_id' => $loteId,
                            'ubicacion' => $area,
                        ]);
                        $stockDestino->cantidad_actual = ($stockDestino->cantidad_actual ?? 0) + $cantidad;
                        $stockDestino->stock_minimo = $stockDestino->stock_minimo ?? 0;
                        $stockDestino->save();

                        AlmacenDispensacionDetalle::create([
                            'dispensacion_id' => $dispensacion->id,
                            'lote_id' => $loteId,
                            'cantidad' => $cantidad,
                        ]);
                    }

                    $totalItems += count($items);
                    $areasAfectadas[] = ucfirst($area);

                    Log::info('Almacén: distribución masiva → '.$area.', '.count($items).' ítems', [
                        'user_id' => Auth::id(),
                        'dispensacion_id' => $dispensacion->id,
                        'action' => 'distribucion_masiva',
                        'module' => 'almacen',
                    ]);
                }
            });

            $msg = 'Distribución completada. '.$totalItems.' ítems enviados a: '.implode(', ', $areasAfectadas).'.';

            return redirect()->route('admin.almacen-medicamentos.historial')->with('success', $msg);
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error en la distribución: '.$e->getMessage())
                ->withInput();
        }
    }

    public function buscarPacienteApi(Request $request)
    {
        $ci = $request->input('ci');

        if (! $ci || ! is_numeric($ci)) {
            return response()->json(['error' => 'C.I. inválido'], 422);
        }

        $paciente = Paciente::where('ci', (int) $ci)
            ->select('ci', 'nombre', 'sexo', 'fecha_nacimiento', 'telefono')
            ->first();

        if (! $paciente) {
            return response()->json(['error' => 'Paciente no encontrado'], 404);
        }

        return response()->json($paciente);
    }

    private function calcularStats(): array
    {
        $totalCatalogo = AlmacenCatalogo::activos()->count();

        $stocksCentral = AlmacenStock::where('ubicacion', 'central')
            ->whereHas('lote.catalogo', fn ($q) => $q->where('activo', true))
            ->get();

        $bajoStock = $stocksCentral->filter(fn ($s) => $s->estado_stock === 'bajo')->count();
        $agotados = $stocksCentral->filter(fn ($s) => $s->estado_stock === 'agotado')->count();

        $vencidos = AlmacenLote::vencidos()->whereHas('catalogo', fn ($q) => $q->activos())->count();
        $porVencer = AlmacenLote::porVencer()->whereHas('catalogo', fn ($q) => $q->activos())->count();

        return [
            'total' => $totalCatalogo,
            'medicamentos' => AlmacenCatalogo::activos()->medicamentos()->count(),
            'insumos' => AlmacenCatalogo::activos()->insumos()->count(),
            'bajo_stock' => $bajoStock,
            'agotados' => $agotados,
            'vencidos' => $vencidos,
            'por_vencer' => $porVencer,
        ];
    }
}
