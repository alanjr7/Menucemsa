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
        // Obtener todas las ventas con sus detalles
        $ventas = VentaFarmacia::with(['detalles'])
            ->orderBy('fecha_venta', 'desc')
            ->get();

        // Calcular estadísticas
        $totalVentas = $ventas->count();
        $ingresosTotales = $ventas->sum('total');
        $promedioPorVenta = $totalVentas > 0 ? $ingresosTotales / $totalVentas : 0;

        // Obtener ventas de hoy
        $ventasHoy = VentaFarmacia::whereDate('fecha_venta', Carbon::today())->count();
        $ingresosHoy = VentaFarmacia::whereDate('fecha_venta', Carbon::today())->sum('total');

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

    public function destroy($codigoVenta)
    {
        try {
            DB::beginTransaction();

            $venta = VentaFarmacia::where('codigo_venta', $codigoVenta)->firstOrFail();
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

            $detalles->each->delete();
            $venta->delete();

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Venta eliminada exitosamente']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error al eliminar la venta'], 500);
        }
    }
}
