<?php

namespace App\Http\Controllers\Farmacia;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AlmacenCatalogo;
use App\Models\AlmacenLote;
use App\Models\AlmacenStock;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InventarioController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // Verificar que el usuario tenga rol farmacia o admin
        $this->middleware(function ($request, $next) {
            if (!Auth::user() || !in_array(Auth::user()->role, ['farmacia', 'admin', 'administrador'])) {
                abort(403, 'No tienes permisos para acceder a este módulo.');
            }
            return $next($request);
        });
    }

    public function index()
    {
        $stocks = AlmacenStock::with('lote.catalogo')
            ->where('ubicacion', 'farmacia')
            ->get();

        $productos = $stocks->groupBy(fn ($stock) => $stock->lote->catalogo->id)
            ->map(function ($stocks) {
                $catalogo = $stocks->first()->lote->catalogo;
                $primaryLote = $stocks->sortBy('fecha_vencimiento')->first()->lote;

                return [
                    'id' => $catalogo->id,
                    'nombre' => $catalogo->nombre,
                    'precio' => (float) ($primaryLote->precio_venta ?? 0),
                    'categoria' => $catalogo->categoria ?: ($catalogo->tipo_label ?? 'Medicamento'),
                    'tipo' => $catalogo->tipo_label ?? 'Medicamento',
                    'laboratorio' => $primaryLote->laboratorio ?? 'N/A',
                    'vencimiento' => $primaryLote->fecha_vencimiento?->format('Y-m-d') ?? 'N/A',
                    'stock' => $stocks->sum('cantidad_actual'),
                    'stockMinimo' => $stocks->min('stock_minimo') ?? 0,
                    'requerimiento' => $catalogo->requiere_receta ? 'Receta' : 'Normal',
                    'requiere_receta' => (bool) $catalogo->requiere_receta,
                    'codigo_barras' => $catalogo->codigo_barras ?? (string) $catalogo->id,
                    'proveedor' => $primaryLote->proveedor ?? 'N/A',
                    'descripcion' => $catalogo->descripcion ?? '',
                    'lote' => $primaryLote->codigo_lote ?? '',
                ];
            })
            ->values();

        return view('farmacia.inventario', compact('productos'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'precio' => 'required|numeric|decimal:0,2|min:0',
            'categoria' => 'required|string',
            'stock' => 'required|integer|min:0',
            'stockMinimo' => 'required|integer|min:0',
            'codigo_barras' => 'required|string|max:50|unique:almacen_catalogo,codigo_barras',
            'proveedor' => 'nullable|string',
            'vencimiento' => 'nullable|date',
            'lote' => 'nullable|string|max:100',
            'descripcion' => 'nullable|string',
            'requiere_receta' => 'boolean'
        ]);

        try {
            DB::beginTransaction();

            $tipo = strtolower($validated['categoria']) === 'insumo' ? 'insumo' : 'medicamento';

            $catalogo = AlmacenCatalogo::create([
                'nombre' => $validated['nombre'],
                'codigo_barras' => $validated['codigo_barras'],
                'descripcion' => $validated['descripcion'] ?? null,
                'unidad_medida' => 'unidades',
                'tipo' => $tipo,
                'activo' => true,
                'requiere_receta' => $validated['requiere_receta'] ?? false,
                'categoria' => $validated['categoria'],
            ]);

            $lote = AlmacenLote::create([
                'catalogo_id' => $catalogo->id,
                'codigo_lote' => $validated['lote'] ?? null,
                'laboratorio' => $validated['proveedor'] ?? null,
                'fecha_vencimiento' => $validated['vencimiento'] ?? null,
                'precio_venta' => $validated['precio'],
                'cantidad_inicial' => $validated['stock'],
                'cantidad_recibida' => $validated['stock'],
            ]);

            AlmacenStock::create([
                'lote_id' => $lote->id,
                'ubicacion' => 'farmacia',
                'cantidad_actual' => $validated['stock'],
                'stock_minimo' => $validated['stockMinimo'],
            ]);

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Producto creado exitosamente', 'producto' => [
                'id' => $catalogo->id,
                'codigo_barras' => $catalogo->codigo_barras,
            ]]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error al crear el producto: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $catalogo = AlmacenCatalogo::findOrFail($id);

        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'precio' => 'required|numeric|decimal:0,2|min:0',
            'categoria' => 'required|string',
            'stock' => 'required|integer|min:0',
            'stockMinimo' => 'required|integer|min:0',
            'codigo_barras' => 'required|string|max:50|unique:almacen_catalogo,codigo_barras,'.$catalogo->id,
            'proveedor' => 'nullable|string',
            'vencimiento' => 'nullable|date',
            'lote' => 'nullable|string|max:100',
            'descripcion' => 'nullable|string',
            'requiere_receta' => 'boolean'
        ]);

        try {
            DB::transaction(function () use ($catalogo, $validated) {
                $tipo = strtolower($validated['categoria']) === 'insumo' ? 'insumo' : 'medicamento';

                $catalogo->update([
                    'nombre' => $validated['nombre'],
                    'codigo_barras' => $validated['codigo_barras'],
                    'descripcion' => $validated['descripcion'] ?? $catalogo->descripcion,
                    'tipo' => $tipo,
                    'categoria' => $validated['categoria'],
                    'requiere_receta' => $validated['requiere_receta'] ?? false,
                ]);

                $stock = AlmacenStock::where('ubicacion', 'farmacia')
                    ->whereHas('lote', fn ($q) => $q->where('catalogo_id', $catalogo->id))
                    ->orderByDesc('cantidad_actual')
                    ->first();

                if (! $stock) {
                    $lote = AlmacenLote::create([
                        'catalogo_id' => $catalogo->id,
                        'codigo_lote' => $validated['lote'] ?? null,
                        'laboratorio' => $validated['proveedor'] ?? null,
                        'fecha_vencimiento' => $validated['vencimiento'] ?? null,
                        'precio_venta' => $validated['precio'],
                        'cantidad_inicial' => $validated['stock'],
                        'cantidad_recibida' => $validated['stock'],
                    ]);

                    AlmacenStock::create([
                        'lote_id' => $lote->id,
                        'ubicacion' => 'farmacia',
                        'cantidad_actual' => $validated['stock'],
                        'stock_minimo' => $validated['stockMinimo'],
                    ]);
                } else {
                    $stock->update([
                        'cantidad_actual' => $validated['stock'],
                        'stock_minimo' => $validated['stockMinimo'],
                    ]);

                    $stock->lote->update([
                        'codigo_lote' => $validated['lote'] ?? $stock->lote->codigo_lote,
                        'laboratorio' => $validated['proveedor'] ?? $stock->lote->laboratorio,
                        'fecha_vencimiento' => $validated['vencimiento'] ?? $stock->lote->fecha_vencimiento,
                        'precio_venta' => $validated['precio'],
                    ]);
                }
            });

            return response()->json(['success' => true, 'message' => 'Producto actualizado exitosamente', 'producto' => [
                'id' => $catalogo->id,
                'codigo_barras' => $validated['codigo_barras'],
            ]]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al actualizar el producto: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            DB::transaction(function () use ($id) {
                $catalogo = AlmacenCatalogo::findOrFail($id);

                AlmacenStock::where('ubicacion', 'farmacia')
                    ->whereHas('lote', fn ($q) => $q->where('catalogo_id', $catalogo->id))
                    ->delete();

                $catalogo->update(['activo' => false]);
            });

            return response()->json(['success' => true, 'message' => 'Producto eliminado exitosamente']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al eliminar el producto: ' . $e->getMessage()], 500);
        }
    }
}
