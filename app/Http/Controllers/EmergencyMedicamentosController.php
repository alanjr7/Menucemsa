<?php

namespace App\Http\Controllers;

use App\Models\AlmacenCatalogo;
use App\Models\AlmacenLote;
use App\Models\AlmacenStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class EmergencyMedicamentosController extends Controller
{
    protected $ubicacion = 'emergencia';

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin|emergencia|administrador|enfermera-emergencia');
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

        // Filtros
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
            'total' => AlmacenCatalogo::activos()->whereHas('lotes.stocks', fn($q) => $q->where('ubicacion', $ubicacion))->count(),
            'bajo_stock' => AlmacenStock::where('ubicacion', $ubicacion)->bajoStock()->count(),
            'agotados' => AlmacenStock::where('ubicacion', $ubicacion)->agotado()->count(),
            'vencidos' => AlmacenLote::vencidos()->whereHas('stocks', fn($q) => $q->where('ubicacion', $ubicacion))->count(),
        ];

        return view('emergency-staff.medicamentos.index', compact('medicamentos', 'stats'));
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

        return view('emergency-staff.medicamentos.create', compact('catalogos', 'tipos', 'unidades'));
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
            // Crear o usar catálogo existente
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

            // Calcular precio de venta
            $precio_venta = $request->precio_compra * (1 + $request->porcentaje_ganancia / 100);

            // Crear lote
            $lote = AlmacenLote::create([
                'catalogo_id' => $catalogo->id,
                'codigo_lote' => $request->codigo_lote,
                'fecha_vencimiento' => $request->fecha_vencimiento,
                'precio_compra' => $request->precio_compra,
                'porcentaje_ganancia' => $request->porcentaje_ganancia,
                'precio_venta' => $precio_venta,
                'cantidad_inicial' => $request->cantidad_inicial,
            ]);

            // Crear stock para esta ubicación
            AlmacenStock::create([
                'lote_id' => $lote->id,
                'ubicacion' => $this->ubicacion,
                'cantidad_actual' => $request->cantidad_inicial,
                'stock_minimo' => $request->stock_minimo,
            ]);

            DB::commit();

            Log::info('Usuario ' . Auth::user()->name . ' creó medicamento/insumo en emergencia: ' . $catalogo->nombre, [
                'user_id' => Auth::id(),
                'catalogo_id' => $catalogo->id,
                'lote_id' => $lote->id,
                'action' => 'create',
                'module' => 'emergency_medicamentos'
            ]);

            return redirect()->route('emergency-staff.medicamentos.index')
                ->with('success', 'Medicamento/Insumo agregado correctamente al inventario de emergencia.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al crear medicamento en emergencia: ' . $e->getMessage());
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

        return view('emergency-staff.medicamentos.show', compact('medicamento'));
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

        return view('emergency-staff.medicamentos.edit', compact('medicamento', 'tipos', 'unidades'));
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

        Log::info('Usuario ' . Auth::user()->name . ' actualizó medicamento/insumo en emergencia: ' . $medicamento->nombre, [
            'user_id' => Auth::id(),
            'catalogo_id' => $medicamento->id,
            'action' => 'update',
            'module' => 'emergency_medicamentos'
        ]);

        return redirect()->route('emergency-staff.medicamentos.index')
            ->with('success', 'Medicamento/Insumo actualizado correctamente.');
    }

    public function destroy(AlmacenCatalogo $medicamento)
    {
        $nombre = $medicamento->nombre;
        $medicamento->update(['activo' => false]);

        Log::info('Usuario ' . Auth::user()->name . ' desactivó medicamento/insumo en emergencia: ' . $nombre, [
            'user_id' => Auth::id(),
            'catalogo_id' => $medicamento->id,
            'action' => 'deactivate',
            'module' => 'emergency_medicamentos'
        ]);

        return redirect()->route('emergency-staff.medicamentos.index')
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

        Log::info('Usuario ' . Auth::user()->name . ' actualizó stock en emergencia de ' . $medicamento->nombre . ': ' . $cantidadAnterior . ' → ' . $request->cantidad . '. Motivo: ' . $request->motivo, [
            'user_id' => Auth::id(),
            'stock_id' => $stock->id,
            'action' => 'update_stock',
            'cantidad_anterior' => $cantidadAnterior,
            'cantidad_nueva' => $request->cantidad,
            'motivo' => $request->motivo,
            'module' => 'emergency_medicamentos'
        ]);

        return redirect()->back()
            ->with('success', 'Stock actualizado correctamente.');
    }
}
