<?php

namespace App\Http\Controllers\Farmacia;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AlmacenStock;
use App\Models\VentaFarmacia;
use App\Models\DetalleVentaFarmacia;
use App\Models\Cliente;
use App\Models\Paciente;
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
            if (!Auth::user() || !in_array(Auth::user()->role, ['farmacia', 'admin', 'administrador', 'almacenista'])) {
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

        $tiposDocumento = \App\Support\TipoDocumento::options();

        return view('farmacia.punto-venta', compact('productos', 'tiposDocumento'));
    }

    /**
     * Buscador unificado de receptores para el punto de venta: devuelve clientes
     * registrados Y pacientes del sistema que coincidan por nombre o documento/CI.
     * Permite venderle a un paciente sin re-registrarlo como cliente.
     */
    public function buscarReceptor(Request $request)
    {
        $term = trim((string) $request->get('q', ''));

        if (mb_strlen($term) < 2) {
            return response()->json([]);
        }

        $like = '%' . $term . '%';

        $clientes = Cliente::query()
            ->where(fn ($q) => $q->where('nombre', 'like', $like)
                ->orWhere('numero_documento', 'like', $like))
            ->orderBy('nombre')
            ->limit(15)
            ->get(['id', 'nombre', 'telefono', 'tipo_documento', 'numero_documento', 'complemento'])
            ->map(fn ($c) => [
                'tipo' => 'cliente',
                'id' => $c->id,
                'nombre' => $c->nombre,
                'telefono' => $c->telefono,
                'tipo_documento' => $c->tipo_documento ?? TipoDocumento::NIT->value,
                'numero_documento' => $c->numero_documento,
                'complemento' => $c->complemento,
                'documento_label' => TipoDocumento::labelFor($c->tipo_documento),
            ]);

        $pacientes = Paciente::query()
            ->where(fn ($q) => $q->where('nombre', 'like', $like)
                ->orWhere('ci', 'like', $like))
            ->orderBy('nombre')
            ->limit(15)
            ->get(['id', 'nombre', 'telefono', 'ci'])
            ->map(fn ($p) => [
                'tipo' => 'paciente',
                'id' => $p->id,
                'nombre' => $p->nombre,
                'telefono' => $p->telefono,
                'tipo_documento' => TipoDocumento::CI->value,
                'numero_documento' => $p->ci ? (string) $p->ci : '',
                'complemento' => null,
                'documento_label' => 'CI',
            ]);

        return response()->json($clientes->concat($pacientes)->values());
    }

    public function procesarVenta(Request $request)
    {
        try {
            $validated = $request->validate([
                'items' => 'required|array|min:1',
                'items.*.id' => 'required|integer',
                'items.*.cantidad' => 'required|integer|min:1',
                'items.*.precio' => Money::rules(),
                'receptor_tipo' => 'nullable|in:cliente,paciente',
                'receptor_id' => 'nullable|integer|required_with:receptor_tipo',
                'metodo_pago' => 'required|string|in:efectivo,tarjeta,transferencia,qr,credito',
                'requiere_receta' => 'boolean',
                'observaciones' => 'nullable|string',
                // Datos fiscales del receptor (factura SFE)
                'con_credito_fiscal' => 'boolean',
                'factura_razon_social' => 'nullable|required_if:con_credito_fiscal,true|string|max:255',
                'factura_tipo_documento' => ['nullable', 'required_if:con_credito_fiscal,true', Rule::in(TipoDocumento::codigos())],
                'factura_numero_documento' => 'nullable|required_if:con_credito_fiscal,true|string|max:20',
                'factura_complemento' => 'nullable|string|max:5',
                // Nuevo cliente registrado al vuelo (se guarda en clientes + factura nominativa)
                'nuevo_cliente' => 'boolean',
                'nuevo_cliente_nombre' => 'nullable|required_if:nuevo_cliente,true|string|max:255',
                'nuevo_cliente_telefono' => 'nullable|string|max:20',
                'nuevo_cliente_tipo_documento' => ['nullable', 'required_if:nuevo_cliente,true', Rule::in(TipoDocumento::codigos())],
                'nuevo_cliente_numero_documento' => 'nullable|required_if:nuevo_cliente,true|string|max:20',
                'nuevo_cliente_complemento' => 'nullable|string|max:5',
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

            // Resuelve el receptor: nuevo cliente, cliente registrado, paciente del sistema, o general.
            $clienteId = null;
            $pacienteId = null;
            $clienteNombre = 'Cliente General';
            $receptorTipo = $validated['receptor_tipo'] ?? null;
            $receptorId = $validated['receptor_id'] ?? null;

            if (!empty($validated['nuevo_cliente'])) {
                $datosNuevo = [
                    'nombre' => trim($validated['nuevo_cliente_nombre']),
                    'telefono' => $validated['nuevo_cliente_telefono'] ?? null,
                    'tipo_documento' => (int) $validated['nuevo_cliente_tipo_documento'],
                    'numero_documento' => trim($validated['nuevo_cliente_numero_documento']),
                    'complemento' => isset($validated['nuevo_cliente_complemento'])
                        ? (trim($validated['nuevo_cliente_complemento']) ?: null)
                        : null,
                ];
                // Reusa un cliente con el mismo documento si ya existe (evita duplicados); si no, lo crea.
                $cliente = Cliente::where('numero_documento', $datosNuevo['numero_documento'])->first();
                if ($cliente) {
                    $cliente->fill($datosNuevo)->save();
                } else {
                    $cliente = Cliente::create($datosNuevo);
                }
                $clienteId = $cliente->id;
                $clienteNombre = $cliente->nombre;
            } elseif ($receptorTipo === 'cliente' && $receptorId) {
                $cliente = Cliente::find($receptorId);
                if (!$cliente) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'El cliente seleccionado ya no existe.'
                    ], 400);
                }
                $clienteId = $cliente->id;
                $clienteNombre = $cliente->nombre;
            } elseif ($receptorTipo === 'paciente' && $receptorId) {
                $paciente = Paciente::find($receptorId);
                if (!$paciente) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'El paciente seleccionado ya no existe.'
                    ], 400);
                }
                $pacienteId = $paciente->id;
                $clienteNombre = $paciente->nombre;
            }

            // Snapshot fiscal: el nuevo cliente factura nominativo con sus propios datos.
            if (!empty($validated['nuevo_cliente'])) {
                $receptor = [
                    'con_credito_fiscal' => true,
                    'razon_social' => $datosNuevo['nombre'],
                    'tipo_documento' => $datosNuevo['tipo_documento'],
                    'numero_documento' => $datosNuevo['numero_documento'],
                    'complemento' => $datosNuevo['complemento'],
                ];
            } else {
                $receptor = $this->resolverReceptor($validated);
            }

            $venta = VentaFarmacia::create([
                'codigo_venta' => $codigoVenta,
                'farmacia_id' => $farmacia->id,
                'usuario_id' => Auth::id(),
                'cliente_id' => $clienteId,
                'paciente_id' => $pacienteId,
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
