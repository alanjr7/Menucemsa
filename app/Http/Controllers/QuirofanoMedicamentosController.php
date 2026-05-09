<?php

namespace App\Http\Controllers;

use App\Models\AlmacenCatalogo;
use App\Models\AlmacenLote;
use App\Models\AlmacenStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class QuirofanoMedicamentosController extends Controller
{
    protected $ubicacion = 'cirugia';

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin|cirujano|administrador');
    }

    public function index(Request $request)
    {
        $ubicacion = $this->ubicacion;

        $query = AlmacenCatalogo::activos()
            ->with(['lotes' => function($q) use ($ubicacion) {
                $q->with(['stocks' => function($sq) use ($ubicacion) {
                    $sq->where('ubicacion', $ubicacion);
                }]);
            }])
            ->whereHas('lotes.stocks', function($q) use ($ubicacion) {
                $q->where('ubicacion', $ubicacion);
            });

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        if ($request->filled('buscar')) {
            $query->where(function($q) use ($request) {
                $q->where('nombre', 'like', '%' . $request->buscar . '%')
                  ->orWhere('descripcion', 'like', '%' . $request->buscar . '%');
            });
        }

        $medicamentos = $query->orderBy('nombre')->paginate(10);

        $stats = [
            'total' => AlmacenCatalogo::activos()->whereHas('lotes.stocks', function($q) use ($ubicacion) {
                $q->where('ubicacion', $ubicacion);
            })->count(),
            'medicamentos' => AlmacenCatalogo::activos()->where('tipo', 'medicamento')->whereHas('lotes.stocks', function($q) use ($ubicacion) {
                $q->where('ubicacion', $ubicacion);
            })->count(),
            'insumos' => AlmacenCatalogo::activos()->where('tipo', 'insumo')->whereHas('lotes.stocks', function($q) use ($ubicacion) {
                $q->where('ubicacion', $ubicacion);
            })->count(),
            'bajo_stock' => AlmacenStock::where('ubicacion', $this->ubicacion)->bajoStock()->count(),
            'agotados' => AlmacenStock::where('ubicacion', $this->ubicacion)->agotado()->count(),
            'vencidos' => AlmacenLote::vencidos()->whereHas('stocks', function($q) use ($ubicacion) {
                $q->where('ubicacion', $ubicacion);
            })->count(),
        ];

        return view('quirofano.medicamentos.index', compact('medicamentos', 'stats'));
    }

    public function create()
    {
        $catalogos = AlmacenCatalogo::activos()->orderBy('nombre')->get();
        $tipos = ['medicamento' => 'Medicamento', 'insumo' => 'Insumo'];
        $unidades = [
            'unidades' => 'Unidades',
            'ml' => 'Mililitros (ml)',
            'mg' => 'Miligramos (mg)',
            'gr' => 'Gramos (gr)',
            'cm' => 'Centímetros (cm)',
            'cajas' => 'Cajas',
            'frascos' => 'Frascos',
            'sobres' => 'Sobres'
        ];

        return view('quirofano.medicamentos.create', compact('catalogos', 'tipos', 'unidades'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'catalogo_id' => 'nullable|exists:almacen_catalogo,id',
            'nombre' => 'required_if:catalogo_id,null|nullable|string|max:255',
            'descripcion' => 'nullable|string',
            'unidad_medida' => 'required_if:catalogo_id,null|nullable|string|max:50',
            'tipo' => 'required_if:catalogo_id,null|nullable|in:medicamento,insumo',
            'codigo_lote' => 'required|string|max:100',
            'fecha_vencimiento' => 'nullable|date|after:today',
            'precio_compra' => 'required|numeric|min:0',
            'porcentaje_ganancia' => 'required|numeric|min:0|max:100',
            'cantidad_inicial' => 'required|integer|min:1',
            'stock_minimo' => 'required|integer|min:0',
        ]);

        DB::beginTransaction();
        try {
            if ($request->filled('catalogo_id')) {
                $catalogo = AlmacenCatalogo::findOrFail($request->catalogo_id);
            } else {
                $catalogo = AlmacenCatalogo::create([
                    'nombre' => $request->nombre,
                    'descripcion' => $request->descripcion,
                    'unidad_medida' => $request->unidad_medida,
                    'tipo' => $request->tipo,
                    'activo' => true,
                ]);
            }

            $precio_venta = $request->precio_compra * (1 + $request->porcentaje_ganancia / 100);

            $lote = AlmacenLote::create([
                'catalogo_id' => $catalogo->id,
                'codigo_lote' => $request->codigo_lote,
                'fecha_vencimiento' => $request->fecha_vencimiento,
                'precio_compra' => $request->precio_compra,
                'porcentaje_ganancia' => $request->porcentaje_ganancia,
                'precio_venta' => $precio_venta,
                'cantidad_inicial' => $request->cantidad_inicial,
            ]);

            AlmacenStock::create([
                'lote_id' => $lote->id,
                'ubicacion' => $this->ubicacion,
                'cantidad_actual' => $request->cantidad_inicial,
                'stock_minimo' => $request->stock_minimo,
            ]);

            DB::commit();

            Log::info('Usuario ' . Auth::user()->name . ' creó medicamento/insumo en quirófano: ' . $catalogo->nombre, [
                'user_id' => Auth::id(),
                'catalogo_id' => $catalogo->id,
                'lote_id' => $lote->id,
                'action' => 'create',
                'module' => 'quirofano_medicamentos'
            ]);

            return redirect()->route('quirofano.medicamentos.index')
                ->with('success', 'Medicamento/Insumo agregado correctamente al inventario de quirófano.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al crear medicamento en quirófano: ' . $e->getMessage());
            return back()->with('error', 'Error al crear medicamento: ' . $e->getMessage());
        }
    }

    public function show(AlmacenCatalogo $medicamento)
    {
        $medicamento->load(['lotes' => function($q) {
            $q->with(['stocks' => function($sq) {
                $sq->where('ubicacion', $this->ubicacion);
            }]);
        }]);

        return view('quirofano.medicamentos.show', compact('medicamento'));
    }

    public function edit(AlmacenCatalogo $medicamento)
    {
        $medicamento->load(['lotes' => function($q) {
            $q->with(['stocks' => function($sq) {
                $sq->where('ubicacion', $this->ubicacion);
            }]);
        }]);

        $tipos = ['medicamento' => 'Medicamento', 'insumo' => 'Insumo'];
        $unidades = [
            'unidades' => 'Unidades',
            'ml' => 'Mililitros (ml)',
            'mg' => 'Miligramos (mg)',
            'gr' => 'Gramos (gr)',
            'cm' => 'Centímetros (cm)',
            'cajas' => 'Cajas',
            'frascos' => 'Frascos',
            'sobres' => 'Sobres'
        ];

        return view('quirofano.medicamentos.edit', compact('medicamento', 'tipos', 'unidades'));
    }

    public function update(Request $request, AlmacenCatalogo $medicamento)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'unidad_medida' => 'required|string|max:50',
            'tipo' => 'required|in:medicamento,insumo',
            'observaciones' => 'nullable|string',
        ]);

        $medicamento->update($request->only([
            'nombre', 'descripcion', 'unidad_medida', 'tipo', 'observaciones'
        ]));

        Log::info('Usuario ' . Auth::user()->name . ' actualizó medicamento/insumo en quirófano: ' . $medicamento->nombre, [
            'user_id' => Auth::id(),
            'catalogo_id' => $medicamento->id,
            'action' => 'update',
            'module' => 'quirofano_medicamentos'
        ]);

        return redirect()->route('quirofano.medicamentos.index')
            ->with('success', 'Medicamento/Insumo actualizado correctamente.');
    }

    public function destroy(AlmacenCatalogo $medicamento)
    {
        $nombre = $medicamento->nombre;
        $medicamento->update(['activo' => false]);

        Log::info('Usuario ' . Auth::user()->name . ' desactivó medicamento/insumo en quirófano: ' . $nombre, [
            'user_id' => Auth::id(),
            'catalogo_id' => $medicamento->id,
            'action' => 'deactivate',
            'module' => 'quirofano_medicamentos'
        ]);

        return redirect()->route('quirofano.medicamentos.index')
            ->with('success', 'Medicamento/Insumo eliminado correctamente.');
    }

    public function actualizarStock(Request $request, AlmacenCatalogo $medicamento)
    {
        $stock = AlmacenStock::whereHas('lote', function($q) use ($medicamento) {
            $q->where('catalogo_id', $medicamento->id);
        })->where('ubicacion', $this->ubicacion)->first();

        if (!$stock) {
            return back()->with('error', 'No hay stock de este medicamento en esta ubicación.');
        }

        $request->validate([
            'cantidad' => 'required|integer|min:0',
            'motivo' => 'required|string|max:255',
        ]);

        $cantidadAnterior = $stock->cantidad_actual;
        $stock->update(['cantidad_actual' => $request->cantidad]);

        Log::info('Usuario ' . Auth::user()->name . ' actualizó stock en quirófano de ' . $medicamento->nombre . ': ' . $cantidadAnterior . ' → ' . $request->cantidad . '. Motivo: ' . $request->motivo, [
            'user_id' => Auth::id(),
            'stock_id' => $stock->id,
            'action' => 'update_stock',
            'cantidad_anterior' => $cantidadAnterior,
            'cantidad_nueva' => $request->cantidad,
            'motivo' => $request->motivo,
            'module' => 'quirofano_medicamentos'
        ]);

        return redirect()->back()
            ->with('success', 'Stock actualizado correctamente.');
    }
}
