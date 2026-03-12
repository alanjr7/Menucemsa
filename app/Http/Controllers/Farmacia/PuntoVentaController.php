<?php

namespace App\Http\Controllers\Farmacia;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Medicamentos;
use App\Models\DetalleMedicamentos;
use App\Models\VentaFarmacia;
use App\Models\DetalleVentaFarmacia;
use App\Models\Cliente;
use App\Models\CajaDiaria;
use Carbon\Carbon;

class PuntoVentaController extends Controller
{
    public function index()
    {
        // Obtener productos para el punto de venta
        $productos = Medicamentos::with('detalleMedicamentos')
            ->get()
            ->map(function ($medicamento) {
                $detalle = $medicamento->detalleMedicamentos->first();
                return [
                    'id' => $medicamento->CODIGO,
                    'nombre' => $medicamento->DESCRIPCION,
                    'precio' => $medicamento->PRECIO,
                    'categoria' => $detalle?->TIPO ?? 'Medicamento',
                    'laboratorio' => $detalle?->LABORATORIO ?? 'N/A',
                    'fecha_vencimiento' => $detalle?->FECHA_VENCIMIENTO ?? 'N/A',
                    'stock' => 100, // Placeholder - necesitaríamos campo de stock en la BD
                    'codigo_barras' => $medicamento->CODIGO,
                    'lote' => 'LOT-' . $medicamento->CODIGO,
                    'requerimiento' => $detalle?->REQUERIMIENTO ?? 'Normal'
                ];
            });

        // Obtener clientes para el select
        $clientes = Cliente::orderBy('nombre')->get(['id', 'nombre', 'telefono']);

        return view('farmacia.punto-venta', compact('productos', 'clientes'));
    }

    public function procesarVenta(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|string',
            'items.*.cantidad' => 'required|integer|min:1',
            'items.*.precio' => 'required|numeric|min:0',
            'cliente_id' => 'nullable|exists:clientes,id',
            'metodo_pago' => 'required|string|in:Efectivo,Tarjeta,Transferencia',
            'requiere_receta' => 'boolean',
            'observaciones' => 'nullable|string'
        ]);

        try {
            \DB::beginTransaction();

            // Obtener o crear una farmacia por defecto
            $farmacia = \App\Models\Farmacia::first();
            if (!$farmacia) {
                $farmacia = \App\Models\Farmacia::create([
                    'ID' => 'FARM001',
                    'DETALLE' => 'Farmacia Principal'
                ]);
            }

            // Generar código de venta
            $codigoVenta = VentaFarmacia::generarCodigoVenta();

            // Calcular total
            $total = collect($validated['items'])->sum(function($item) {
                return $item['cantidad'] * $item['precio'];
            });

            // Obtener información del cliente
            $clienteNombre = 'Cliente General';
            if ($validated['cliente_id']) {
                $cliente = Cliente::find($validated['cliente_id']);
                $clienteNombre = $cliente->nombre;
            }

            // Crear venta
            $venta = VentaFarmacia::create([
                'CODIGO_VENTA' => $codigoVenta,
                'ID_FARMACIA' => $farmacia->ID,
                'CLIENTE' => $clienteNombre,
                'TOTAL' => $total,
                'METODO_PAGO' => $validated['metodo_pago'],
                'REQUIERE_RECETA' => $validated['requiere_receta'] ?? false,
                'FECHA_VENTA' => Carbon::now(),
                'ESTADO' => 'COMPLETADA',
                'OBSERVACIONES' => $validated['observaciones'] ?? null,
                'caja_diaria_id' => $this->obtenerOCrearCajaDiaria()->id
            ]);

            // Crear detalles de venta
            foreach ($validated['items'] as $item) {
                $medicamento = Medicamentos::find($item['id']);
                
                DetalleVentaFarmacia::create([
                    'CODIGO_VENTA' => $codigoVenta,
                    'CODIGO_PRODUCTO' => $item['id'],
                    'TIPO_PRODUCTO' => 'Medicamento',
                    'NOMBRE_PRODUCTO' => $medicamento->DESCRIPCION,
                    'CANTIDAD' => $item['cantidad'],
                    'PRECIO_UNITARIO' => $item['precio'],
                    'SUBTOTAL' => $item['cantidad'] * $item['precio']
                ]);

                // Aquí deberíamos actualizar el stock del producto
                // $this->actualizarStock($item['id'], $item['cantidad']);
            }

            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Venta procesada exitosamente',
                'codigo_venta' => $codigoVenta,
                'total' => $total
            ]);

        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar la venta: ' . $e->getMessage()
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
