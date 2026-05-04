<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AlmacenCatalogo;
use App\Models\AlmacenDispensacion;
use App\Models\AlmacenDispensacionDetalle;
use App\Models\AlmacenEntregaDetalle;
use App\Models\AlmacenEntregaPaciente;
use App\Models\AlmacenLote;
use App\Models\AlmacenStock;
use App\Models\Paciente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AlmacenMedicamentosController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin|administrador');
    }

    public function index(Request $request)
    {
        $query = AlmacenCatalogo::query()->with([
            'lotes.stocks' => fn ($q) => $q->where('ubicacion', 'central'),
        ]);

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        if ($request->filled('buscar')) {
            $query->where(function ($q) use ($request) {
                $q->where('nombre', 'like', '%' . $request->buscar . '%')
                  ->orWhere('descripcion', 'like', '%' . $request->buscar . '%');
            });
        }

        $catalogo = $query->activos()->orderBy('nombre')->paginate(20);

        // Calcular estado_stock para filtro DESPUÉS de paginar (aplica en colección)
        if ($request->filled('estado_stock')) {
            $catalogo->setCollection(
                $catalogo->getCollection()->filter(function ($item) use ($request) {
                    return $item->estado_stock === $request->estado_stock;
                })->values()
            );
        }

        $stats = $this->calcularStats();

        return view('admin.almacen-medicamentos.index', compact('catalogo', 'stats'));
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
            'nombre'               => 'required|string|max:255',
            'descripcion'          => 'nullable|string',
            'unidad_medida'        => 'required|string|max:50',
            'tipo'                 => 'required|in:medicamento,insumo',
            'observaciones'        => 'nullable|string',
            // Primer lote (opcional)
            'codigo_lote'          => 'nullable|string|max:100',
            'fecha_vencimiento'    => 'nullable|date|after:today',
            'precio_compra'        => 'nullable|numeric|min:0',
            'porcentaje_ganancia'  => 'nullable|numeric|min:0|max:999',
            'precio_venta'         => 'nullable|numeric|min:0',
            'cantidad_inicial'     => 'required|integer|min:0',
            'stock_minimo'         => 'required|integer|min:0',
        ]);

        DB::transaction(function () use ($request) {
            $catalogo = AlmacenCatalogo::create([
                'nombre'        => $request->nombre,
                'descripcion'   => $request->descripcion,
                'unidad_medida' => $request->unidad_medida,
                'tipo'          => $request->tipo,
                'observaciones' => $request->observaciones,
            ]);

            // Crear primer lote + stock en central
            $lote = AlmacenLote::create([
                'catalogo_id'          => $catalogo->id,
                'codigo_lote'          => $request->codigo_lote,
                'fecha_vencimiento'    => $request->fecha_vencimiento,
                'precio_compra'        => $request->precio_compra,
                'porcentaje_ganancia'  => $request->porcentaje_ganancia,
                'precio_venta'         => $request->precio_venta,
                'cantidad_inicial'     => $request->cantidad_inicial,
            ]);

            AlmacenStock::create([
                'lote_id'        => $lote->id,
                'ubicacion'      => 'central',
                'cantidad_actual' => $request->cantidad_inicial,
                'stock_minimo'   => $request->stock_minimo,
            ]);

            Log::info('Almacén: nuevo ítem creado: ' . $catalogo->nombre, [
                'user_id'     => Auth::id(),
                'catalogo_id' => $catalogo->id,
                'lote_id'     => $lote->id,
                'action'      => 'create',
                'module'      => 'almacen',
            ]);
        });

        return redirect()->route('admin.almacen-medicamentos.index')
            ->with('success', 'Medicamento/Insumo agregado correctamente al almacén.');
    }

    public function show(AlmacenCatalogo $almacenMedicamento)
    {
        $almacenMedicamento->load(['lotes.stocks']);

        return view('admin.almacen-medicamentos.show', ['catalogo' => $almacenMedicamento]);
    }

    public function edit(AlmacenCatalogo $almacenMedicamento)
    {
        $tipos = ['medicamento' => 'Medicamento', 'insumo' => 'Insumo'];
        $unidades = ['unidades', 'ml', 'mg', 'gr', 'cm', 'cajas', 'frascos', 'sobres'];

        return view('admin.almacen-medicamentos.edit', [
            'catalogo' => $almacenMedicamento,
            'tipos'    => $tipos,
            'unidades' => $unidades,
        ]);
    }

    public function update(Request $request, AlmacenCatalogo $almacenMedicamento)
    {
        $request->validate([
            'nombre'        => 'required|string|max:255',
            'descripcion'   => 'nullable|string',
            'unidad_medida' => 'required|string|max:50',
            'tipo'          => 'required|in:medicamento,insumo',
            'observaciones' => 'nullable|string',
        ]);

        $almacenMedicamento->update($request->only([
            'nombre', 'descripcion', 'unidad_medida', 'tipo', 'observaciones',
        ]));

        Log::info('Almacén: ítem actualizado: ' . $almacenMedicamento->nombre, [
            'user_id'     => Auth::id(),
            'catalogo_id' => $almacenMedicamento->id,
            'action'      => 'update',
            'module'      => 'almacen',
        ]);

        return redirect()->route('admin.almacen-medicamentos.show', $almacenMedicamento->id)
            ->with('success', 'Medicamento/Insumo actualizado correctamente.');
    }

    public function destroy(AlmacenCatalogo $almacenMedicamento)
    {
        $almacenMedicamento->update(['activo' => false]);

        Log::info('Almacén: ítem desactivado: ' . $almacenMedicamento->nombre, [
            'user_id'     => Auth::id(),
            'catalogo_id' => $almacenMedicamento->id,
            'action'      => 'deactivate',
            'module'      => 'almacen',
        ]);

        return redirect()->route('admin.almacen-medicamentos.index')
            ->with('success', 'Medicamento/Insumo desactivado correctamente.');
    }

    public function actualizarStock(Request $request, AlmacenCatalogo $almacenMedicamento)
    {
        $request->validate([
            'lote_id'      => 'required|integer|exists:almacen_lotes,id',
            'ubicacion'    => 'required|in:central,emergencia,cirugia,hospitalizacion,uti,usi,neonato,internacion',
            'cantidad'     => 'required|integer|min:0',
            'stock_minimo' => 'nullable|integer|min:0',
            'motivo'       => 'required|string|max:255',
        ]);

        $stock = AlmacenStock::firstOrNew([
            'lote_id'   => $request->lote_id,
            'ubicacion' => $request->ubicacion,
        ]);

        $cantidadAnterior = $stock->cantidad_actual ?? 0;
        $stock->cantidad_actual = $request->cantidad;

        if ($request->filled('stock_minimo')) {
            $stock->stock_minimo = $request->stock_minimo;
        }

        $stock->save();

        Log::info('Almacén: stock actualizado para ' . $almacenMedicamento->nombre . ': ' . $cantidadAnterior . ' → ' . $request->cantidad . '. Motivo: ' . $request->motivo, [
            'user_id'     => Auth::id(),
            'catalogo_id' => $almacenMedicamento->id,
            'lote_id'     => $request->lote_id,
            'ubicacion'   => $request->ubicacion,
            'action'      => 'update_stock',
            'module'      => 'almacen',
        ]);

        return redirect()->back()->with('success', 'Stock actualizado correctamente.');
    }

    public function reporteBajoStock()
    {
        $stocks = AlmacenStock::with(['lote.catalogo'])
            ->bajoStock()
            ->whereHas('lote.catalogo', fn ($q) => $q->where('activo', true))
            ->orderBy('cantidad_actual')
            ->get();

        return view('admin.almacen-medicamentos.reporte-bajo-stock', compact('stocks'));
    }

    public function reporteVencimiento()
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

        return view('admin.almacen-medicamentos.reporte-vencimiento', compact('vencidos', 'porVencer'));
    }

    public function porArea(string $area)
    {
        $stocks = AlmacenStock::with(['lote.catalogo'])
            ->porUbicacion($area)
            ->whereHas('lote.catalogo', fn ($q) => $q->where('activo', true))
            ->orderByDesc('cantidad_actual')
            ->paginate(20);

        $stats = [
            'total'        => $stocks->total(),
            'medicamentos' => $stocks->getCollection()->filter(fn ($s) => $s->lote->catalogo->tipo === 'medicamento')->count(),
            'insumos'      => $stocks->getCollection()->filter(fn ($s) => $s->lote->catalogo->tipo === 'insumo')->count(),
            'bajo_stock'   => $stocks->getCollection()->filter(fn ($s) => $s->estado_stock === 'bajo')->count(),
            'agotados'     => $stocks->getCollection()->filter(fn ($s) => $s->estado_stock === 'agotado')->count(),
        ];

        return view('admin.almacen-medicamentos.por-area', compact('stocks', 'area', 'stats'));
    }

    public function dispensar(Request $request, AlmacenCatalogo $almacenMedicamento)
    {
        $request->validate([
            'lote_id'        => 'required|integer|exists:almacen_lotes,id',
            'cantidad'       => 'required|integer|min:1',
            'ubicacion_destino' => 'required|in:emergencia,cirugia,hospitalizacion,uti,usi,neonato,internacion',
            'recibido_por'   => 'nullable|string|max:150',
            'observaciones'  => 'nullable|string|max:1000',
        ]);

        $stockCentral = AlmacenStock::where('lote_id', $request->lote_id)
            ->where('ubicacion', 'central')
            ->lockForUpdate()
            ->first();

        if (!$stockCentral || $stockCentral->cantidad_actual < $request->cantidad) {
            $disponible = $stockCentral->cantidad_actual ?? 0;
            return redirect()->back()
                ->with('error', "Stock insuficiente. Disponible en central: {$disponible}.");
        }

        DB::transaction(function () use ($request, $almacenMedicamento, $stockCentral) {
            // 1. Descontar del stock central
            $stockCentral->decrement('cantidad_actual', $request->cantidad);

            // 2. Incrementar (o crear) stock en destino
            $stockDestino = AlmacenStock::firstOrNew([
                'lote_id'   => $request->lote_id,
                'ubicacion' => $request->ubicacion_destino,
            ]);
            $stockDestino->cantidad_actual = ($stockDestino->cantidad_actual ?? 0) + $request->cantidad;
            $stockDestino->stock_minimo = $stockDestino->stock_minimo ?? 0;
            $stockDestino->save();

            // 3. Registrar dispensación con su detalle
            $dispensacion = AlmacenDispensacion::create([
                'ubicacion_origen'   => 'central',
                'ubicacion_destino'  => $request->ubicacion_destino,
                'dispensado_por'     => Auth::id(),
                'recibido_por'       => $request->recibido_por,
                'observaciones'      => $request->observaciones,
                'fecha_dispensacion' => now(),
            ]);

            AlmacenDispensacionDetalle::create([
                'dispensacion_id' => $dispensacion->id,
                'lote_id'         => $request->lote_id,
                'cantidad'        => $request->cantidad,
            ]);

            Log::info('Almacén: dispensación ' . $almacenMedicamento->nombre . ' x' . $request->cantidad . ' → ' . $request->ubicacion_destino, [
                'user_id'        => Auth::id(),
                'catalogo_id'    => $almacenMedicamento->id,
                'lote_id'        => $request->lote_id,
                'dispensacion_id' => $dispensacion->id,
                'action'         => 'dispensar',
                'module'         => 'almacen',
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
            $query->where('fecha_dispensacion', '>=', $request->fecha_desde . ' 00:00:00');
        }

        if ($request->filled('fecha_hasta')) {
            $query->where('fecha_dispensacion', '<=', $request->fecha_hasta . ' 23:59:59');
        }

        if ($request->filled('buscar')) {
            $query->whereHas('detalles.lote.catalogo', function ($q) use ($request) {
                $q->where('nombre', 'like', '%' . $request->buscar . '%');
            });
        }

        $dispensaciones = $query->recientes()->paginate(20);

        $stats = [
            'total'          => AlmacenDispensacion::count(),
            'ultimos_30_dias' => AlmacenDispensacion::where('fecha_dispensacion', '>=', now()->subDays(30))->count(),
            'areas_activas'  => AlmacenDispensacion::distinct('ubicacion_destino')->count('ubicacion_destino'),
        ];

        $areas = [
            'emergencia'      => 'Emergencia',
            'cirugia'         => 'Cirugía',
            'hospitalizacion' => 'Hospitalización',
            'uti'             => 'UTI',
            'usi'             => 'USI',
            'neonato'         => 'Neonato',
            'internacion'     => 'Internación',
        ];

        return view('admin.almacen-medicamentos.historial-dispensaciones', compact('dispensaciones', 'stats', 'areas'));
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
            'paciente_ci'              => 'required|integer|exists:pacientes,ci',
            'observaciones'            => 'nullable|string|max:1000',
            'detalles'                 => 'required|array|min:1',
            'detalles.*.detalle_id'    => 'required|integer|exists:almacen_dispensacion_detalles,id',
            'detalles.*.cantidad'      => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($request, $dispensacion) {
            $entrega = AlmacenEntregaPaciente::create([
                'paciente_ci'   => $request->paciente_ci,
                'entregado_por' => Auth::id(),
                'observaciones' => $request->observaciones,
                'fecha_entrega' => now(),
            ]);

            foreach ($request->detalles as $item) {
                AlmacenEntregaDetalle::create([
                    'entrega_id'               => $entrega->id,
                    'dispensacion_detalle_id'  => $item['detalle_id'],
                    'cantidad'                 => $item['cantidad'],
                ]);
            }

            Log::info('Almacén: entrega registrada a paciente CI:' . $request->paciente_ci, [
                'user_id'        => Auth::id(),
                'dispensacion_id' => $dispensacion->id,
                'entrega_id'     => $entrega->id,
                'action'         => 'registrar_paciente',
                'module'         => 'almacen',
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
            'catalogo'       => $almacenMedicamento,
            'dispensaciones' => $dispensaciones,
        ]);
    }

    public function transferirForm()
    {
        $lotes = AlmacenLote::with(['catalogo', 'stocks' => fn ($q) => $q->where('ubicacion', 'central')])
            ->whereHas('catalogo', fn ($q) => $q->activos())
            ->whereHas('stocks', fn ($q) => $q->where('ubicacion', 'central')->where('cantidad_actual', '>', 0))
            ->get();

        return view('admin.almacen-medicamentos.transferir', compact('lotes'));
    }

    public function procesarTransferencia(Request $request)
    {
        $request->validate([
            'ubicacion_destino' => 'required|in:emergencia,cirugia,hospitalizacion,uti,usi,neonato,internacion',
            'recibido_por'      => 'nullable|string|max:150',
            'items'             => 'required|string',
        ]);

        $items = json_decode($request->items, true);

        if (!is_array($items) || count($items) === 0) {
            return redirect()->back()
                ->with('error', 'Debe seleccionar al menos un ítem para transferir.')
                ->withInput();
        }

        foreach ($items as $index => $item) {
            if (!isset($item['lote_id']) || !isset($item['cantidad'])) {
                return redirect()->back()
                    ->with('error', 'Datos inválidos en ítem #' . ($index + 1))
                    ->withInput();
            }
        }

        try {
            DB::transaction(function () use ($items, $request) {
                $dispensacion = AlmacenDispensacion::create([
                    'ubicacion_origen'   => 'central',
                    'ubicacion_destino'  => $request->ubicacion_destino,
                    'dispensado_por'     => Auth::id(),
                    'recibido_por'       => $request->recibido_por,
                    'fecha_dispensacion' => now(),
                ]);

                foreach ($items as $item) {
                    $stockCentral = AlmacenStock::where('lote_id', $item['lote_id'])
                        ->where('ubicacion', 'central')
                        ->lockForUpdate()
                        ->first();

                    if (!$stockCentral || $stockCentral->cantidad_actual < $item['cantidad']) {
                        $disponible = $stockCentral->cantidad_actual ?? 0;
                        throw new \Exception("Stock insuficiente para lote #{$item['lote_id']}. Disponible: {$disponible}");
                    }

                    $stockCentral->decrement('cantidad_actual', $item['cantidad']);

                    $stockDestino = AlmacenStock::firstOrNew([
                        'lote_id'   => $item['lote_id'],
                        'ubicacion' => $request->ubicacion_destino,
                    ]);
                    $stockDestino->cantidad_actual = ($stockDestino->cantidad_actual ?? 0) + $item['cantidad'];
                    $stockDestino->stock_minimo = $stockDestino->stock_minimo ?? 0;
                    $stockDestino->save();

                    AlmacenDispensacionDetalle::create([
                        'dispensacion_id' => $dispensacion->id,
                        'lote_id'         => $item['lote_id'],
                        'cantidad'        => $item['cantidad'],
                    ]);
                }

                Log::info('Almacén: transferencia masiva a ' . $request->ubicacion_destino . ', ' . count($items) . ' ítems', [
                    'user_id'         => Auth::id(),
                    'dispensacion_id' => $dispensacion->id,
                    'action'          => 'transferencia_masiva',
                    'module'          => 'almacen',
                ]);
            });

            return redirect()->route('admin.almacen-medicamentos.historial')
                ->with('success', 'Transferencia completada. ' . count($items) . ' ítems enviados a ' . ucfirst($request->ubicacion_destino) . '.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error en la transferencia: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function buscarPacienteApi(Request $request)
    {
        $ci = $request->input('ci');

        if (!$ci || !is_numeric($ci)) {
            return response()->json(['error' => 'C.I. inválido'], 422);
        }

        $paciente = Paciente::where('ci', (int) $ci)
            ->select('ci', 'nombre', 'sexo', 'fecha_nacimiento', 'telefono')
            ->first();

        if (!$paciente) {
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
        $agotados  = $stocksCentral->filter(fn ($s) => $s->estado_stock === 'agotado')->count();

        $vencidos   = AlmacenLote::vencidos()->whereHas('catalogo', fn ($q) => $q->activos())->count();
        $porVencer  = AlmacenLote::porVencer()->whereHas('catalogo', fn ($q) => $q->activos())->count();

        return [
            'total'        => $totalCatalogo,
            'medicamentos' => AlmacenCatalogo::activos()->medicamentos()->count(),
            'insumos'      => AlmacenCatalogo::activos()->insumos()->count(),
            'bajo_stock'   => $bajoStock,
            'agotados'     => $agotados,
            'vencidos'     => $vencidos,
            'por_vencer'   => $porVencer,
        ];
    }
}
