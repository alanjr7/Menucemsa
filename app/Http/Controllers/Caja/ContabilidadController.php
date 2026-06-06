<?php

namespace App\Http\Controllers\Caja;

use App\Exports\ContabilidadExport;
use App\Http\Controllers\Controller;
use App\Models\Egreso;
use App\Models\PagoCuenta;
use App\Traits\AuditLoggable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class ContabilidadController extends Controller
{
    use AuditLoggable;

    public function index(): View
    {
        return view('caja.contabilidad.index', [
            'categorias' => Egreso::CATEGORIAS,
        ]);
    }

    /**
     * Resumen del período: ingresos automáticos (PagoCuenta) + egresos manuales.
     */
    public function resumen(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'fecha_inicio' => 'required|date',
                'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            ]);

            [$inicio, $fin] = $this->rango($request);

            // Ingresos: dinero realmente cobrado (excluye fondo de apertura)
            $pagos = PagoCuenta::whereBetween('created_at', [$inicio, $fin])->get();
            $totalIngresos = $pagos->reduce(fn ($acc, $p) => bcadd($acc, $p->monto, 2), '0');

            $ingresosPorMetodo = $pagos->groupBy('metodo_pago')->map(
                fn ($g) => $g->reduce(fn ($acc, $p) => bcadd($acc, $p->monto, 2), '0')
            );

            // Egresos manuales
            $egresos = Egreso::with('user')->entreFechas($inicio->toDateString(), $fin->toDateString())
                ->orderBy('fecha', 'desc')->orderBy('id', 'desc')->get();
            $totalEgresos = $egresos->reduce(fn ($acc, $e) => bcadd($acc, $e->monto, 2), '0');

            $egresosPorCategoria = $egresos->groupBy('categoria')->map(fn ($g) => [
                'label' => $g->first()->categoria_label,
                'total' => $g->reduce(fn ($acc, $e) => bcadd($acc, $e->monto, 2), '0'),
            ])->values();

            // Serie diaria para el gráfico (ingresos vs egresos por día)
            $ingresosPorDia = $pagos->groupBy(fn ($p) => $p->created_at->toDateString())
                ->map(fn ($g) => $g->reduce(fn ($acc, $p) => bcadd($acc, $p->monto, 2), '0'));
            $egresosPorDia = $egresos->groupBy(fn ($e) => $e->fecha->toDateString())
                ->map(fn ($g) => $g->reduce(fn ($acc, $e) => bcadd($acc, $e->monto, 2), '0'));

            $serie = ['labels' => [], 'ingresos' => [], 'egresos' => []];
            for ($cursor = $inicio->copy()->startOfDay(); $cursor <= $fin; $cursor->addDay()) {
                $key = $cursor->toDateString();
                $serie['labels'][] = $cursor->format('d/m');
                $serie['ingresos'][] = $ingresosPorDia[$key] ?? '0';
                $serie['egresos'][] = $egresosPorDia[$key] ?? '0';
            }

            return response()->json([
                'success' => true,
                'totales' => [
                    'ingresos' => $totalIngresos,
                    'egresos' => $totalEgresos,
                    'saldo' => bcsub($totalIngresos, $totalEgresos, 2),
                ],
                'serie' => $serie,
                'ingresos_por_metodo' => $ingresosPorMetodo,
                'egresos_por_categoria' => $egresosPorCategoria,
                'egresos' => $egresos->map(fn ($e) => [
                    'id' => $e->id,
                    'fecha' => $e->fecha->format('d/m/Y'),
                    'categoria' => $e->categoria_label,
                    'descripcion' => $e->descripcion,
                    'monto' => $e->monto,
                    'metodo_pago' => $e->metodo_pago_label,
                    'proveedor' => $e->proveedor,
                    'comprobante_nro' => $e->comprobante_nro,
                    'usuario' => $e->user->name ?? 'N/A',
                ]),
            ]);
        } catch (ValidationException $e) {
            return response()->json(['success' => false, 'message' => $e->validator->errors()->first()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al generar resumen: '.$e->getMessage()], 500);
        }
    }

    public function storeEgreso(Request $request): JsonResponse
    {
        try {
            $request->merge(['monto' => str_replace(',', '.', (string) $request->monto)]);

            $data = $request->validate([
                'fecha' => 'required|date',
                'categoria' => 'required|in:'.implode(',', array_keys(Egreso::CATEGORIAS)),
                'descripcion' => 'required|string|max:255',
                'monto' => 'required|numeric|min:0.01',
                'metodo_pago' => 'required|in:efectivo,transferencia,cheque,tarjeta,qr',
                'proveedor' => 'nullable|string|max:255',
                'comprobante_nro' => 'nullable|string|max:50',
                'observaciones' => 'nullable|string|max:1000',
            ]);

            $data['user_id'] = auth()->id();
            $egreso = Egreso::create($data);

            $this->logActivity(
                'registrar_egreso',
                'Egreso registrado - '.$egreso->categoria_label.': Bs. '.number_format($egreso->monto, 2).' - '.$egreso->descripcion,
                $egreso
            );

            return response()->json(['success' => true, 'message' => 'Egreso registrado correctamente']);
        } catch (ValidationException $e) {
            return response()->json(['success' => false, 'message' => $e->validator->errors()->first()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al registrar egreso: '.$e->getMessage()], 500);
        }
    }

    public function destroyEgreso(int $id): JsonResponse
    {
        try {
            $egreso = Egreso::findOrFail($id);
            $this->logActivity(
                'eliminar_egreso',
                'Egreso eliminado - '.$egreso->categoria_label.': Bs. '.number_format($egreso->monto, 2),
                $egreso
            );
            $egreso->delete();

            return response()->json(['success' => true, 'message' => 'Egreso eliminado']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al eliminar egreso: '.$e->getMessage()], 500);
        }
    }

    public function exportar(Request $request)
    {
        $request->validate([
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
        ]);

        [$inicio, $fin] = $this->rango($request);
        $nombre = 'contabilidad_'.$inicio->format('Ymd').'_'.$fin->format('Ymd').'.xlsx';

        return Excel::download(new ContabilidadExport($inicio, $fin), $nombre);
    }

    private function rango(Request $request): array
    {
        return [
            Carbon::parse($request->fecha_inicio)->startOfDay(),
            Carbon::parse($request->fecha_fin)->endOfDay(),
        ];
    }
}
