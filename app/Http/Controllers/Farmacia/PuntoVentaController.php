<?php

namespace App\Http\Controllers\Farmacia;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AlmacenStock;
use App\Models\VentaFarmacia;
use App\Models\DetalleVentaFarmacia;
use App\Models\Cliente;
use App\Models\CajaDiaria;
use App\Support\Money;
use App\Support\TipoDocumento;
use Illuminate\Validation\Rule;
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
        // Obtener productos para el punto de venta desde el stock de farmacia
        $items = AlmacenStock::with('lote.catalogo')
            ->where('ubicacion', 'farmacia')
            ->where('cantidad_actual', '>', 0)
            ->get();
            
        $productos = $items->map(function ($item) {
            $catalogo = $item->lote?->catalogo;
            return [
                'id' => $item->id,
                'nombre' => $catalogo->nombre ?? 'Producto desconocido',
                'precio' => $item->precio,
                'categoria' => $catalogo->categoria ?: ($catalogo?->tipo_label ?? 'Medicamento'),
                'laboratorio' => $item->lote->laboratorio ?? 'N/A',
                'vencimiento' => $item->lote->fecha_vencimiento?->format('Y-m-d') ?? 'N/A',
                'stock' => $item->cantidad_actual,
                'codigo_barras' => $catalogo->codigo_barras ?? (string) ($catalogo->id ?? $item->id),
                'lote' => $item->lote->codigo_lote ?? 'LOT-' . ($item->lote_id ?? $item->id),
                'requerimiento' => $catalogo->requiere_receta ? 'Receta' : 'Normal',
                'requiere_receta' => (bool) ($catalogo->requiere_receta ?? false),
            ];
        });

        // Obtener clientes para el select (con datos fiscales para autocompletar la factura)
        $clientes = Cliente::orderBy('nombre')
            ->get(['id', 'nombre', 'telefono', 'tipo_documento', 'numero_documento', 'complemento']);

        $tiposDocumento = \App\Support\TipoDocumento::options();

        return view('farmacia.punto-venta', compact('productos', 'clientes', 'tiposDocumento'));
    }

    public function procesarVenta(Request $request)
    {
        try {
            $validated = $request->validate([
                'items' => 'required|array|min:1',
                'items.*.id' => 'required|integer',
                'items.*.cantidad' => 'required|integer|min:1',
                'items.*.precio' => Money::rules(),
                'cliente_id' => 'nullable|exists:clientes,id',
                'metodo_pago' => 'required|string|in:efectivo,tarjeta,transferencia,qr,credito',
                'requiere_receta' => 'boolean',
                'observaciones' => 'nullable|string',
                // Datos fiscales del receptor (factura SFE)
                'con_credito_fiscal' => 'boolean',
                'factura_razon_social' => 'nullable|required_if:con_credito_fiscal,true|string|max:255',
                'factura_tipo_documento' => ['nullable', 'required_if:con_credito_fiscal,true', Rule::in(TipoDocumento::codigos())],
                'factura_numero_documento' => 'nullable|required_if:con_credito_fiscal,true|string|max:20',
                'factura_complemento' => 'nullable|string|max:5',
            ]);

            DB::beginTransaction();

            $ids = collect($validated['items'])->pluck('id');

            // Lock rows for update to prevent race conditions on stock
            $inventarios = AlmacenStock::with('lote.catalogo')
                ->where('ubicacion', 'farmacia')
                ->whereIn('id', $ids)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

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

                if ($inventario->cantidad_actual < $item['cantidad']) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Stock insuficiente para "' . ($inventario->lote?->catalogo->nombre ?? $item['id']) . '". Disponible: ' . $inventario->cantidad_actual . ', Solicitado: ' . $item['cantidad']
                    ], 400);
                }

                if (($inventario->lote?->catalogo->requiere_receta ?? false) && $inventario->lote->catalogo->requiere_receta) {
                    $productosRequierenReceta[] = $inventario->lote?->catalogo->nombre ?? $item['id'];
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

            $total = collect($validated['items'])
                ->reduce(fn($acc, $item) => Money::add($acc, Money::mul($item['cantidad'], $item['precio'])), '0');

            $clienteNombre = 'Cliente General';
            if ($validated['cliente_id']) {
                $clienteNombre = Cliente::find($validated['cliente_id'])->nombre ?? 'Cliente General';
            }

            $receptor = $this->resolverReceptor($validated);

            $venta = VentaFarmacia::create([
                'codigo_venta' => $codigoVenta,
                'farmacia_id' => $farmacia->id,
                'usuario_id' => Auth::id(),
                'cliente_id' => $validated['cliente_id'] ?? null,
                'cliente' => $clienteNombre,
                'con_credito_fiscal' => $receptor['con_credito_fiscal'],
                'factura_razon_social' => $receptor['razon_social'],
                'factura_tipo_documento' => $receptor['tipo_documento'],
                'factura_numero_documento' => $receptor['numero_documento'],
                'factura_complemento' => $receptor['complemento'],
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
                $catalogo = $inventario->lote?->catalogo;

                DetalleVentaFarmacia::create([
                    'codigo_venta' => $codigoVenta,
                    'codigo_producto' => $catalogo->codigo_barras ?? (string) ($catalogo->id ?? $item['id']),
                    'tipo_producto' => $catalogo->tipo ?? 'medicamento',
                    'nombre_producto' => $catalogo->nombre ?? $item['id'],
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $item['precio'],
                    'subtotal' => Money::mul($item['cantidad'], $item['precio']),
                ]);

                $inventario->decrement('cantidad_actual', $item['cantidad']);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Venta procesada exitosamente',
                'codigo_venta' => $codigoVenta,
                'total' => $total,
                'factura' => [
                    'con_credito_fiscal' => $receptor['con_credito_fiscal'],
                    'razon_social' => $receptor['razon_social'],
                    'tipo_documento' => TipoDocumento::labelFor($receptor['tipo_documento']),
                    'numero_documento' => $receptor['numero_documento'],
                    'complemento' => $receptor['complemento'],
                ],
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

    /**
     * Resuelve el snapshot fiscal del receptor.
     * Con crédito fiscal → usa los datos capturados; sin datos → S/N (norma SIN).
     */
    private function resolverReceptor(array $validated): array
    {
        if (empty($validated['con_credito_fiscal'])) {
            return [
                'con_credito_fiscal' => false,
                'razon_social' => TipoDocumento::SIN_NOMBRE_RAZON,
                'tipo_documento' => TipoDocumento::SIN_NOMBRE_TIPO->value,
                'numero_documento' => TipoDocumento::SIN_NOMBRE_DOC,
                'complemento' => null,
            ];
        }

        return [
            'con_credito_fiscal' => true,
            'razon_social' => trim($validated['factura_razon_social']),
            'tipo_documento' => (int) $validated['factura_tipo_documento'],
            'numero_documento' => trim($validated['factura_numero_documento']),
            'complemento' => isset($validated['factura_complemento'])
                ? (trim($validated['factura_complemento']) ?: null)
                : null,
        ];
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
