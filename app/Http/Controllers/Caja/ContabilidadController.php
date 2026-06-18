<?php

namespace App\Http\Controllers\Caja;

use App\Exports\ContabilidadExport;
use App\Http\Controllers\Controller;
use App\Models\Egreso;
use App\Models\PagoCuenta;
use App\Models\VentaFarmacia;
use App\Support\Money;
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
            $pagos = PagoCuenta::with(['cuentaCobro.paciente', 'user'])
                ->whereBetween('created_at', [$inicio, $fin])
                ->orderBy('created_at', 'desc')->get();
            $totalCaja = $pagos->reduce(fn ($acc, $p) => bcadd($acc, $p->monto, 2), '0');

            // Ingresos de farmacia (ventas completadas)
            $ventasFarmacia = VentaFarmacia::with('usuario')
                ->where('estado', 'COMPLETADA')
                ->whereBetween('fecha_venta', [$inicio, $fin])
                ->orderBy('fecha_venta', 'desc')->get();
            $totalFarmacia = $ventasFarmacia->reduce(fn ($acc, $v) => bcadd($acc, $v->total, 2), '0');

            $totalIngresos = bcadd($totalCaja, $totalFarmacia, 2);

            // Ingresos por método: combina caja + farmacia
            $ingresosPorMetodo = $pagos->groupBy('metodo_pago')->map(
                fn ($g) => $g->reduce(fn ($acc, $p) => bcadd($acc, $p->monto, 2), '0')
            )->toArray();
            foreach ($ventasFarmacia->groupBy('metodo_pago') as $metodo => $g) {
                $sumF = $g->reduce(fn ($acc, $v) => bcadd($acc, $v->total, 2), '0');
                $ingresosPorMetodo[$metodo] = bcadd($ingresosPorMetodo[$metodo] ?? '0', $sumF, 2);
            }

            // Egresos manuales
            $egresos = Egreso::with('user')->entreFechas($inicio->toDateString(), $fin->toDateString())
                ->orderBy('fecha', 'desc')->orderBy('id', 'desc')->get();
            $totalEgresos = $egresos->reduce(fn ($acc, $e) => bcadd($acc, $e->monto, 2), '0');
            $totalCreditoFiscal = $egresos->reduce(fn ($acc, $e) => bcadd($acc, $e->importe_iva, 2), '0');

            $egresosPorCategoria = $egresos->groupBy('categoria')->map(fn ($g) => [
                'label' => $g->first()->categoria_label,
                'total' => $g->reduce(fn ($acc, $e) => bcadd($acc, $e->monto, 2), '0'),
            ])->values();

            // Serie diaria para el gráfico (ingresos caja vs farmacia vs egresos por día)
            $ingresosPorDia = $pagos->groupBy(fn ($p) => $p->created_at->toDateString())
                ->map(fn ($g) => $g->reduce(fn ($acc, $p) => bcadd($acc, $p->monto, 2), '0'));
            $farmaciaPorDia = $ventasFarmacia->groupBy(fn ($v) => $v->fecha_venta->toDateString())
                ->map(fn ($g) => $g->reduce(fn ($acc, $v) => bcadd($acc, $v->total, 2), '0'));
            $egresosPorDia = $egresos->groupBy(fn ($e) => $e->fecha->toDateString())
                ->map(fn ($g) => $g->reduce(fn ($acc, $e) => bcadd($acc, $e->monto, 2), '0'));

            $serie = ['labels' => [], 'ingresos' => [], 'farmacia' => [], 'egresos' => []];
            for ($cursor = $inicio->copy()->startOfDay(); $cursor <= $fin; $cursor->addDay()) {
                $key = $cursor->toDateString();
                $serie['labels'][] = $cursor->format('d/m');
                $serie['ingresos'][] = $ingresosPorDia[$key] ?? '0';
                $serie['farmacia'][] = $farmaciaPorDia[$key] ?? '0';
                $serie['egresos'][] = $egresosPorDia[$key] ?? '0';
            }

            return response()->json([
                'success' => true,
                'totales' => [
                    'ingresos' => $totalIngresos,
                    'ingresos_caja' => $totalCaja,
                    'ingresos_farmacia' => $totalFarmacia,
                    'egresos' => $totalEgresos,
                    'credito_fiscal' => $totalCreditoFiscal,
                    'saldo' => bcsub($totalIngresos, $totalEgresos, 2),
                ],
                'serie' => $serie,
                'ingresos_por_metodo' => $ingresosPorMetodo,
                'ingresos' => $pagos->map(function ($p) {
                    $cuenta = $p->cuentaCobro;
                    $paciente = $cuenta?->paciente?->nombre ?? 'N/A';
                    $atencion = $cuenta?->tipo_atencion_label ?? 'Cobro';
                    return [
                        'id' => 'P-'.$p->id,
                        'origen' => 'Caja',
                        'fecha_orden' => $p->created_at->toDateTimeString(),
                        'fecha' => $p->created_at->format('d/m/Y H:i'),
                        'paciente' => $paciente,
                        'descripcion' => $atencion,
                        'cuenta_id' => $p->cuenta_cobro_id,
                        'metodo_pago' => $p->metodo_pago_label,
                        'referencia' => $p->referencia,
                        'monto' => $p->monto,
                        'usuario' => $p->user->name ?? 'N/A',
                    ];
                })->concat($ventasFarmacia->map(function ($v) {
                    return [
                        'id' => 'F-'.$v->id,
                        'origen' => 'Farmacia',
                        'fecha_orden' => $v->fecha_venta->toDateTimeString(),
                        'fecha' => $v->fecha_venta->format('d/m/Y H:i'),
                        'paciente' => $v->cliente ?: 'Consumidor final',
                        'descripcion' => 'Venta farmacia',
                        'cuenta_id' => $v->codigo_venta,
                        'metodo_pago' => ucfirst($v->metodo_pago),
                        'referencia' => null,
                        'monto' => $v->total,
                        'usuario' => $v->usuario->name ?? 'N/A',
                    ];
                }))->sortByDesc('fecha_orden')->values(),
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
                    'con_credito_fiscal' => $e->con_credito_fiscal,
                    'nit_proveedor' => $e->nit_proveedor,
                    'nro_factura' => $e->nro_factura,
                    'importe_iva' => $e->importe_iva,
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
            $request->merge([
                'monto' => str_replace(',', '.', (string) $request->monto),
                'con_credito_fiscal' => filter_var($request->input('con_credito_fiscal', false), FILTER_VALIDATE_BOOLEAN),
            ]);

            $rules = [
                'fecha' => 'required|date',
                'categoria' => 'required|in:'.implode(',', array_keys(Egreso::CATEGORIAS)),
                'descripcion' => 'required|string|max:255',
                'monto' => Money::rules(min: '0.01'),
                'metodo_pago' => 'required|in:efectivo,transferencia,cheque,tarjeta,qr',
                'proveedor' => 'nullable|string|max:255',
                'comprobante_nro' => 'nullable|string|max:50',
                'observaciones' => 'nullable|string|max:1000',
                'con_credito_fiscal' => 'boolean',
                'nit_proveedor' => 'nullable|string|max:20',
                'nro_factura' => 'nullable|string|max:50',
                'codigo_autorizacion' => 'nullable|string|max:100',
            ];

            // Si declara crédito fiscal, los datos de la factura de compra son obligatorios.
            if ($request->boolean('con_credito_fiscal')) {
                $rules['nit_proveedor'] = 'required|string|max:20';
                $rules['nro_factura'] = 'required|string|max:50';
            }

            $data = $request->validate($rules);
            $data['user_id'] = auth()->id();

            // Crédito fiscal IVA = 13% del total de la factura. Sin factura válida no hay crédito.
            if ($data['con_credito_fiscal']) {
                $data['importe_iva'] = Money::mul($data['monto'], '0.13');
            } else {
                $data['importe_iva'] = '0';
                $data['nit_proveedor'] = null;
                $data['nro_factura'] = null;
                $data['codigo_autorizacion'] = null;
            }

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
