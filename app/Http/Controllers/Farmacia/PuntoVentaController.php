<?php

namespace App\Http\Controllers\Farmacia;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Medicamentos;
use App\Models\InventarioFarmacia;
use App\Models\VentaFarmacia;
use App\Models\DetalleVentaFarmacia;
use App\Models\Cliente;
use App\Models\CajaDiaria;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PuntoVentaController extends Controller
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
        // Obtener productos para el punto de venta desde inventario_farmacia
        $items = InventarioFarmacia::with('medicamento')
            ->where('tipo_item', 'medicamento')
            ->get();
            
        $productos = $items->map(function ($item) {
            return [
                'id' => $item->codigo_item,
                'nombre' => $item->medicamento->descripcion ?? 'Producto desconocido',
                'precio' => $item->medicamento->precio ?? 0,
                'categoria' => $item->tipo ?? 'Medicamento',
                'laboratorio' => $item->laboratorio ?? 'N/A',
                'vencimiento' => $item->fecha_vencimiento ?? 'N/A',
                'stock' => $item->stock_disponible,
                'codigo_barras' => $item->codigo_item,
                'lote' => $item->lote ?? 'LOT-' . $item->codigo_item,
                'requerimiento' => $item->requerimiento ?? 'Normal'
            ];
        });

        // Obtener clientes para el select
        $clientes = Cliente::orderBy('nombre')->get(['id', 'nombre', 'telefono']);

        return view('farmacia.punto-venta', compact('productos', 'clientes'));
    }

    public function procesarVenta(Request $request)
    {
        try {
            $validated = $request->validate([
                'items' => 'required|array|min:1',
                'items.*.id' => 'required|string',
                'items.*.cantidad' => 'required|integer|min:1',
                'items.*.precio' => 'required|numeric|min:0',
                'cliente_id' => 'nullable|exists:clientes,id',
                'metodo_pago' => 'required|string|in:efectivo,tarjeta,transferencia,qr,credito',
                'requiere_receta' => 'boolean',
                'observaciones' => 'nullable|string'
            ]);

            DB::beginTransaction();

            $ids = collect($validated['items'])->pluck('id');

            // Lock rows for update to prevent race conditions on stock
            $inventarios = InventarioFarmacia::with('medicamento')
                ->where('tipo_item', 'medicamento')
                ->whereIn('codigo_item', $ids)
                ->lockForUpdate()
                ->get()
                ->keyBy('codigo_item');

            $productosRequierenReceta = [];
            foreach ($validated['items'] as $item) {
                $inventario = $inventarios->get($item['id']);

                if (!$inventario) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Producto no encontrado en inventario: ' . $item['id']
                    ], 400);
                }

                if ($inventario->stock_disponible < $item['cantidad']) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Stock insuficiente para "' . ($inventario->medicamento->descripcion ?? $item['id']) . '". Disponible: ' . $inventario->stock_disponible . ', Solicitado: ' . $item['cantidad']
                    ], 400);
                }

                if ($inventario->requerimiento === 'Receta') {
                    $productosRequierenReceta[] = $inventario->medicamento->descripcion ?? $item['id'];
                }
            }

            if (!empty($productosRequierenReceta) && empty($validated['requiere_receta'])) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Los siguientes productos requieren receta médica: ' . implode(', ', $productosRequierenReceta) . '. Por favor, marque la casilla "Venta con receta médica".'
                ], 400);
            }

            $farmacia = \App\Models\Farmacia::first();
            if (!$farmacia) {
                $farmacia = \App\Models\Farmacia::create([
                    'id' => 'FARM001',
                    'detalle' => 'Farmacia Principal'
                ]);
            }

            $codigoVenta = VentaFarmacia::generarCodigoVenta();

            $total = collect($validated['items'])->sum(fn($item) => $item['cantidad'] * $item['precio']);

            $clienteNombre = 'Cliente General';
            if ($validated['cliente_id']) {
                $clienteNombre = Cliente::find($validated['cliente_id'])->nombre ?? 'Cliente General';
            }

            $venta = VentaFarmacia::create([
                'codigo_venta' => $codigoVenta,
                'farmacia_id' => $farmacia->id,
                'usuario_id' => Auth::id(),
                'cliente' => $clienteNombre,
                'total' => $total,
                'metodo_pago' => $validated['metodo_pago'],
                'requiere_receta' => $validated['requiere_receta'] ?? false,
                'fecha_venta' => Carbon::now(),
                'estado' => 'COMPLETADA',
                'observaciones' => $validated['observaciones'] ?? null,
                'caja_diaria_id' => $this->obtenerOCrearCajaDiaria()->id
            ]);

            foreach ($validated['items'] as $item) {
                $inventario = $inventarios->get($item['id']);
                $medicamento = $inventario->medicamento;

                DetalleVentaFarmacia::create([
                    'codigo_venta' => $codigoVenta,
                    'codigo_producto' => $item['id'],
                    'tipo_producto' => 'medicamento',
                    'nombre_producto' => $medicamento->descripcion ?? $item['id'],
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $item['precio'],
                    'subtotal' => $item['cantidad'] * $item['precio'],
                ]);

                $nuevoStock = $inventario->stock_disponible - $item['cantidad'];
                $inventario->update([
                    'stock_disponible' => $nuevoStock,
                    'reposicion' => $nuevoStock <= $inventario->stock_minimo ? 1 : 0
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Venta procesada exitosamente',
                'codigo_venta' => $codigoVenta,
                'total' => $total
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => implode(' ', collect($e->errors())->flatten()->all())
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error al procesar venta en punto de venta: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    private function obtenerOCrearCajaDiaria()
    {
        $hoy = Carbon::today();
        $caja = CajaDiaria::whereDate('fecha', $hoy)->first();
        
        if (!$caja) {
            $caja = CajaDiaria::create([
                'fecha' => $hoy,
                'monto_inicial' => 0,
                'monto_final' => 0,
                'estado' => 'abierta',
                'usuario_id' => auth()->id()
            ]);
        }
        
        return $caja;
    }
}
