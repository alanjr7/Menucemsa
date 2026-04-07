<?php

namespace App\Http\Controllers\Farmacia;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Inventario;
use App\Models\Medicamentos;
use App\Models\InventarioFarmacia;
use Illuminate\Support\Facades\Auth;

class InventarioController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // Verificar que el usuario tenga rol farmacia o admin
        $this->middleware(function ($request, $next) {
            if (!Auth::user() || !in_array(Auth::user()->role, ['farmacia', 'admin'])) {
                abort(403, 'No tienes permisos para acceder a este módulo.');
            }
            return $next($request);
        });
    }

    public function index()
    {
        // Obtener todos los items del inventario_farmacia con sus medicamentos
        $productos = InventarioFarmacia::with('medicamento')
            ->where('tipo_item', 'medicamento')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->codigo_item,
                    'nombre' => $item->medicamento->descripcion ?? 'Producto desconocido',
                    'precio' => $item->medicamento->precio ?? 0,
                    'categoria' => $item->tipo ?? 'Medicamento',
                    'laboratorio' => $item->laboratorio ?? 'N/A',
                    'vencimiento' => $item->fecha_vencimiento ?? 'N/A',
                    'stock' => $item->stock_disponible,
                    'stockMinimo' => $item->stock_minimo,
                    'requerimiento' => $item->requerimiento ?? 'Normal',
                    'requiere_receta' => $item->requerimiento === 'Receta',
                    'codigo_barras' => $item->codigo_item,
                    'proveedor' => $item->laboratorio ?? 'N/A',
                    'reposicion' => $item->reposicion ? 'Si' : 'No'
                ];
            });

        return view('farmacia.inventario', compact('productos'));
    }

    public function store(Request $request)
    {
        // Lógica para crear nuevo producto
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'precio' => 'required|numeric|min:0',
            'categoria' => 'required|string',
            'stock' => 'required|integer|min:0',
            'stockMinimo' => 'required|integer|min:0',
            'codigo_barras' => 'required|string|unique:medicamentos,codigo',
            'proveedor' => 'nullable|string',
            'vencimiento' => 'nullable|date',
            'lote' => 'nullable|string',
            'descripcion' => 'nullable|string',
            'requiere_receta' => 'boolean'
        ]);

        try {
            // Obtener o crear una farmacia por defecto
            $farmacia = \App\Models\Farmacia::first();
            if (!$farmacia) {
                $farmacia = \App\Models\Farmacia::create([
                    'id' => 'FARM001',
                    'detalle' => 'Farmacia Principal'
                ]);
            }

            // Crear medicamento
            $medicamento = Medicamentos::create([
                'codigo' => $validated['codigo_barras'],
                'descripcion' => $validated['nombre'],
                'precio' => $validated['precio']
            ]);

            // Crear registro en inventario_farmacia
            InventarioFarmacia::create([
                'farmacia_id' => $farmacia->id,
                'tipo_item' => 'medicamento',
                'codigo_item' => $medicamento->codigo,
                'laboratorio' => $validated['proveedor'] ?? null,
                'fecha_vencimiento' => $validated['vencimiento'],
                'tipo' => $validated['categoria'],
                'requerimiento' => $validated['requiere_receta'] ?? false ? 'Receta' : 'Normal',
                'stock_minimo' => $validated['stockMinimo'],
                'stock_disponible' => $validated['stock'],
                'reposicion' => $validated['stock'] <= $validated['stockMinimo'] ? 1 : 0,
                'fecha_ingreso' => now()
            ]);

            return response()->json(['success' => true, 'message' => 'Producto creado exitosamente', 'producto' => $medicamento]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al crear el producto: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        // Lógica para actualizar producto
        $medicamento = Medicamentos::findOrFail($id);
        
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'precio' => 'required|numeric|min:0',
            'categoria' => 'required|string',
            'stock' => 'required|integer|min:0',
            'stockMinimo' => 'required|integer|min:0',
            'proveedor' => 'nullable|string',
            'vencimiento' => 'nullable|string',
            'lote' => 'nullable|string',
            'descripcion' => 'nullable|string',
            'requiere_receta' => 'boolean'
        ]);

        try {
            $medicamento->update([
                'descripcion' => $validated['nombre'],
                'precio' => $validated['precio']
            ]);

            $item = InventarioFarmacia::where('codigo_item', $id)->first();
            if ($item) {
                $item->update([
                    'laboratorio' => $validated['proveedor'],
                    'fecha_vencimiento' => $validated['vencimiento'],
                    'tipo' => $validated['categoria'],
                    'requerimiento' => $validated['requiere_receta'] ?? false ? 'Receta' : 'Normal',
                    'stock_disponible' => $validated['stock'],
                    'stock_minimo' => $validated['stockMinimo'],
                    'reposicion' => $validated['stock'] <= $validated['stockMinimo'] ? 1 : 0
                ]);
            } else {
                // Si no existe, crearlo
                $farmacia = \App\Models\Farmacia::first();
                InventarioFarmacia::create([
                    'farmacia_id' => $farmacia->id,
                    'tipo_item' => 'medicamento',
                    'codigo_item' => $id,
                    'laboratorio' => $validated['proveedor'] ?? null,
                    'fecha_vencimiento' => $validated['vencimiento'] ?? null,
                    'tipo' => $validated['categoria'],
                    'requerimiento' => $validated['requiere_receta'] ?? false ? 'Receta' : 'Normal',
                    'stock_minimo' => $validated['stockMinimo'],
                    'stock_disponible' => $validated['stock'],
                    'reposicion' => $validated['stock'] <= $validated['stockMinimo'] ? 1 : 0,
                    'fecha_ingreso' => now()
                ]);
            }

            return response()->json(['success' => true, 'message' => 'Producto actualizado exitosamente', 'producto' => $medicamento]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al actualizar el producto: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            // Lógica para eliminar producto
            $medicamento = Medicamentos::findOrFail($id);
            
            // Eliminar del inventario_farmacia primero
            InventarioFarmacia::where('codigo_item', $id)->delete();
            
            // Eliminar medicamento
            $medicamento->delete();

            return response()->json(['success' => true, 'message' => 'Producto eliminado exitosamente']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al eliminar el producto: ' . $e->getMessage()], 500);
        }
    }
}
