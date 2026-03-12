<?php

namespace App\Http\Controllers\Farmacia;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Medicamentos;
use App\Models\DetalleMedicamentos;

class InventarioController extends Controller
{
    public function index()
    {
        // Obtener todos los medicamentos con sus detalles
        $productos = Medicamentos::with('detalleMedicamentos')
            ->get()
            ->map(function ($medicamento) {
                $detalle = $medicamento->detalleMedicamentos->first();
                return [
                    'id' => $medicamento->CODIGO,
                    'nombre' => $medicamento->DESCRIPCION,
                    'precio' => $medicamento->PRECIO,
                    'categoria' => $detalle->TIPO ?? 'Medicamento',
                    'laboratorio' => $detalle->LABORATORIO ?? 'N/A',
                    'fecha_vencimiento' => $detalle->FECHA_VENCIMIENTO ?? 'N/A',
                    'stock' => 100, // Placeholder - necesitaríamos campo de stock en la BD
                    'codigo_barras' => $medicamento->CODIGO,
                    'proveedor' => $detalle->LABORATORIO ?? 'N/A',
                    'requerimiento' => $detalle->REQUERIMIENTO ?? 'Normal'
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
            'codigo_barras' => 'required|string|unique:medicamentos,CODIGO',
            'proveedor' => 'nullable|string',
            'vencimiento' => 'nullable|date',
            'lote' => 'nullable|string',
            'descripcion' => 'nullable|string'
        ]);

        try {
            // Obtener o crear una farmacia por defecto
            $farmacia = \App\Models\Farmacia::first();
            if (!$farmacia) {
                $farmacia = \App\Models\Farmacia::create([
                    'ID' => 'FARM001',
                    'DETALLE' => 'Farmacia Principal'
                ]);
            }

            // Crear medicamento
            $medicamento = Medicamentos::create([
                'CODIGO' => $validated['codigo_barras'],
                'DESCRIPCION' => $validated['nombre'],
                'PRECIO' => $validated['precio']
            ]);

            // Crear detalle del medicamento
            DetalleMedicamentos::create([
                'ID_FARMACIA' => $farmacia->ID,
                'CODIGO_MEDICAMENTOS' => $medicamento->CODIGO,
                'LABORATORIO' => $validated['proveedor'] ?? null,
                'FECHA_VENCIMIENTO' => $validated['vencimiento'],
                'TIPO' => $validated['categoria'],
                'REQUERIMIENTO' => $validated['categoria'] === 'Receta' ? 'Receta' : 'Normal'
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
            'proveedor' => 'nullable|string',
            'vencimiento' => 'nullable|string',
            'lote' => 'nullable|string',
            'descripcion' => 'nullable|string'
        ]);

        try {
            $medicamento->update([
                'DESCRIPCION' => $validated['nombre'],
                'PRECIO' => $validated['precio']
            ]);

            $detalle = DetalleMedicamentos::where('CODIGO_MEDICAMENTOS', $id)->first();
            if ($detalle) {
                $detalle->update([
                    'LABORATORIO' => $validated['proveedor'],
                    'FECHA_VENCIMIENTO' => $validated['vencimiento'],
                    'TIPO' => $validated['categoria'],
                    'REQUERIMIENTO' => $validated['categoria'] === 'Receta' ? 'Receta' : 'Normal'
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
            
            // Eliminar detalles primero
            DetalleMedicamentos::where('CODIGO_MEDICAMENTOS', $id)->delete();
            
            // Eliminar medicamento
            $medicamento->delete();

            return response()->json(['success' => true, 'message' => 'Producto eliminado exitosamente']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al eliminar el producto: ' . $e->getMessage()], 500);
        }
    }
}
