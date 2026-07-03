<?php

namespace App\Http\Controllers\Farmacia;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\VentaFarmacia;
use App\Models\DetalleVentaFarmacia;
use App\Models\AlmacenStock;
use App\Models\AlmacenCatalogo;
use App\Models\Cliente;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VentasController extends Controller
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
        // Obtener todas las ventas con sus detalles (incl. anuladas, marcadas)
        $ventas = VentaFarmacia::with(['detalles'])
            ->orderBy('fecha_venta', 'desc')
            ->get();

        // Estadísticas: solo las COMPLETADAS cuentan como ingreso (una venta
        // anulada/devuelta no es plata en caja).
        $completadas = $ventas->where('estado', 'COMPLETADA');
        $totalVentas = $completadas->count();
        $ingresosTotales = $completadas->reduce(fn ($acc, $v) => \App\Support\Money::add($acc, $v->total), '0');
        $promedioPorVenta = $totalVentas > 0 ? \App\Support\Money::div($ingresosTotales, $totalVentas) : 0;

        // Ventas de hoy (completadas)
        $ventasHoy = VentaFarmacia::completadas()->whereDate('fecha_venta', Carbon::today())->count();
        $ingresosHoy = VentaFarmacia::completadas()->whereDate('fecha_venta', Carbon::today())->sum('total');

        return view('farmacia.ventas', compact(
            'ventas',
            'totalVentas',
            'ingresosTotales',
            'promedioPorVenta',
            'ventasHoy',
            'ingresosHoy'
        ));
    }

    public function show($codigoVenta)
    {
        $venta = VentaFarmacia::with(['detalles'])
            ->where('codigo_venta', $codigoVenta)
            ->firstOrFail();

        return response()->json($venta);
    }

    /**
     * Anula una venta (devolución con reingreso de stock). La venta y sus
     * detalles NO se borran: pasan a estado ANULADA (auditado) y dejan de
     * contar como ingreso en todos los reportes (que filtran COMPLETADA).
     * Reemplaza al antiguo destroy, que hacía hard-delete del registro.
     */
    public function anular(Request $request, $codigoVenta)
    {
        try {
            // Anular una venta es una operación de dinero (devolución): SOLO
            // admin|administrador. Farmacia vende y consulta; el ajuste financiero
            // es decisión administrativa (mismo criterio que las NC de caja).
            if (!in_array(Auth::user()->role, ['admin', 'administrador'])) {
                return response()->json(['success' => false, 'message' => 'Solo un administrador puede anular ventas'], 403);
            }

            $data = $request->validate(['motivo' => 'required|string|max:255']);

            $venta = VentaFarmacia::where('codigo_venta', $codigoVenta)->firstOrFail();

            if ($venta->estado === 'ANULADA') {
                return response()->json(['success' => false, 'message' => 'La venta ya está anulada'], 422);
            }

            // No se reescribe un período ya declarado: la venta queda firme y la
            // devolución debe resolverse como ajuste del período corriente.
            if (\App\Models\CierreContable::estaCerrado($venta->fecha_venta)) {
                return response()->json(['success' => false, 'message' => 'No se puede anular una venta de un período contable cerrado'], 422);
            }

            DB::beginTransaction();

            $detalles = DetalleVentaFarmacia::where('codigo_venta', $codigoVenta)->get();

            // Restaurar stock en el área farmacia (mismo origen que descuenta el POS)
            foreach ($detalles as $detalle) {
                $catalogo = AlmacenCatalogo::where('codigo_barras', $detalle->codigo_producto)
                    ->orWhere('id', $detalle->codigo_producto)
                    ->first();

                $stock = $catalogo
                    ? AlmacenStock::where('ubicacion', 'farmacia')
                        ->whereHas('lote', fn ($q) => $q->where('catalogo_id', $catalogo->id))
                        ->orderByDesc('id')
                        ->first()
                    : null;

                if ($stock) {
                    $stock->increment('cantidad_actual', $detalle->cantidad);
                } else {
                    \Log::warning("Anulación de venta {$codigoVenta}: no se encontró stock en farmacia para producto {$detalle->codigo_producto}. Stock no restaurado.");
                }
            }

            $venta->update([
                'estado' => 'ANULADA',
                'anulado_at' => now(),
                'anulado_por' => Auth::id(),
                'motivo_anulacion' => $data['motivo'],
            ]);

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Venta '.$codigoVenta.' anulada; el stock fue reingresado a farmacia']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'message' => collect($e->errors())->flatten()->first()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error al anular la venta: '.$e->getMessage()], 500);
        }
    }
}
