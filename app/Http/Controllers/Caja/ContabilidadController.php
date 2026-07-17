<?php

namespace App\Http\Controllers\Caja;

use App\Exports\ContabilidadExport;
use App\Exports\RegistroComprasVentasExport;
use App\Http\Controllers\Controller;
use App\Models\CierreContable;
use App\Models\Devolucion;
use App\Models\Dosificacion;
use App\Models\Egreso;
use App\Models\PagoCuenta;
use App\Models\VentaFarmacia;
use App\Support\Impuestos;
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

            // Cobertura de seguros del período (DEVENGADO): la porción que paga la
            // aseguradora es una VENTA con débito fiscal IVA, pero NO es caja todavía
            // (es cuenta por cobrar hasta que el seguro liquide). Por eso suma al débito
            // fiscal y a la base imponible IT/IUE (ventas brutas), pero NO al flujo de
            // efectivo (ingresos_caja / saldo).
            $seguroCobros = \App\Models\SeguroCobro::with(['seguro', 'cuentaCobro.paciente'])
                ->vigentes()
                ->whereBetween('created_at', [$inicio, $fin])
                ->orderBy('created_at', 'desc')->get();
            $ventasSeguro = $seguroCobros->reduce(fn ($acc, $s) => bcadd($acc, $s->monto, 2), '0');
            $debitoSeguro = $seguroCobros->reduce(fn ($acc, $s) => bcadd($acc, $s->debito_fiscal, 2), '0');

            // Devoluciones / Notas de Crédito del período (contra-ingreso): se listan
            // todas (incl. anuladas, marcadas) pero solo las vigentes restan de los
            // ingresos, del débito fiscal y de las ventas brutas. NO son egresos.
            $devoluciones = Devolucion::with(['user', 'anuladoPor', 'cuentaCobro.paciente'])
                ->whereBetween('created_at', [$inicio, $fin])
                ->orderBy('created_at', 'desc')->get();
            $devolucionesVigentes = $devoluciones->whereNull('anulado_at');
            $totalDevoluciones = $devolucionesVigentes->reduce(fn ($acc, $d) => bcadd($acc, $d->monto, 2), '0');
            $debitoDevoluciones = $devolucionesVigentes->reduce(fn ($acc, $d) => bcadd($acc, $d->debito_fiscal, 2), '0');

            // Devoluciones de FARMACIA del período (ventas anuladas por anulado_at):
            // se LISTAN junto a las NC para visibilidad, pero NO se restan de los
            // totales — la venta anulada ya quedó excluida de ingresos/débito por el
            // filtro estado=COMPLETADA (restarla aquí sería doble descuento).
            $devolucionesFarmacia = VentaFarmacia::with(['anuladoPor', 'paciente'])
                ->where('estado', 'ANULADA')
                ->whereBetween('anulado_at', [$inicio, $fin])
                ->orderBy('anulado_at', 'desc')->get();
            $totalDevolucionesFarmacia = $devolucionesFarmacia->reduce(fn ($acc, $v) => bcadd($acc, $v->total, 2), '0');

            // Ingresos netos de caja = cobrado − devuelto (la NC ajusta el período corriente).
            $totalCaja = bcsub($totalCaja, $totalDevoluciones, 2);
            $totalIngresos = bcsub($totalIngresos, $totalDevoluciones, 2);

            // Ventas brutas (devengado) = ingresos de caja/farmacia + cobertura de seguros.
            // Es la base imponible de IT e IUE; los "ingresos" de caja siguen siendo solo efectivo.
            $ventasBrutas = bcadd($totalIngresos, $ventasSeguro, 2);

            // Débito fiscal IVA de las ventas del período (caja + farmacia + seguros),
            // menos el débito revertido por las notas de crédito.
            $debitoCaja = $pagos->reduce(fn ($acc, $p) => bcadd($acc, $p->debito_fiscal, 2), '0');
            $debitoFarmacia = $ventasFarmacia->reduce(fn ($acc, $v) => bcadd($acc, $v->debito_fiscal, 2), '0');
            $totalDebitoFiscal = bcadd(bcadd($debitoCaja, $debitoFarmacia, 2), $debitoSeguro, 2);
            $totalDebitoFiscal = bcsub($totalDebitoFiscal, $debitoDevoluciones, 2);

            // Ingresos por método: combina caja + farmacia, neto de devoluciones
            // (el dinero devuelto salió por ese método y no debe contarse como ingresado).
            $ingresosPorMetodo = $pagos->groupBy('metodo_pago')->map(
                fn ($g) => $g->reduce(fn ($acc, $p) => bcadd($acc, $p->monto, 2), '0')
            )->toArray();
            foreach ($ventasFarmacia->groupBy('metodo_pago') as $metodo => $g) {
                $sumF = $g->reduce(fn ($acc, $v) => bcadd($acc, $v->total, 2), '0');
                $ingresosPorMetodo[$metodo] = bcadd($ingresosPorMetodo[$metodo] ?? '0', $sumF, 2);
            }
            foreach ($devolucionesVigentes->groupBy('metodo_devolucion') as $metodo => $g) {
                $sumD = $g->reduce(fn ($acc, $d) => bcadd($acc, $d->monto, 2), '0');
                $ingresosPorMetodo[$metodo] = bcsub($ingresosPorMetodo[$metodo] ?? '0', $sumD, 2);
            }

            // Egresos manuales — se listan todos (incl. anulados, marcados) pero solo
            // los vigentes suman al flujo de caja / crédito fiscal / serie.
            $egresos = Egreso::with('user', 'anuladoPor')->entreFechas($inicio->toDateString(), $fin->toDateString())
                ->orderBy('fecha', 'desc')->orderBy('id', 'desc')->get();
            $egresosVigentes = $egresos->whereNull('anulado_at');
            $totalEgresos = $egresosVigentes->reduce(fn ($acc, $e) => bcadd($acc, $e->monto, 2), '0');
            $totalCreditoFiscal = $egresosVigentes->reduce(fn ($acc, $e) => bcadd($acc, $e->importe_iva, 2), '0');
            $totalRetencionIue = $egresosVigentes->reduce(fn ($acc, $e) => bcadd($acc, $e->retencion_iue, 2), '0');
            $totalRetencionIt = $egresosVigentes->reduce(fn ($acc, $e) => bcadd($acc, $e->retencion_it, 2), '0');

            $egresosPorCategoria = $egresosVigentes->groupBy('categoria')->map(fn ($g) => [
                'label' => $g->first()->categoria_label,
                'total' => $g->reduce(fn ($acc, $e) => bcadd($acc, $e->monto, 2), '0'),
            ])->values();

            // Serie diaria para el gráfico (ingresos caja vs farmacia vs egresos por día).
            // La serie de caja se muestra NETA de devoluciones del día.
            $devolucionesPorDia = $devolucionesVigentes->groupBy(fn ($d) => $d->created_at->toDateString())
                ->map(fn ($g) => $g->reduce(fn ($acc, $d) => bcadd($acc, $d->monto, 2), '0'));
            $ingresosPorDia = $pagos->groupBy(fn ($p) => $p->created_at->toDateString())
                ->map(fn ($g) => $g->reduce(fn ($acc, $p) => bcadd($acc, $p->monto, 2), '0'))
                ->map(fn ($v, $dia) => bcsub($v, $devolucionesPorDia[$dia] ?? '0', 2));
            // Días con devolución pero sin cobros: la serie debe reflejar el neto negativo.
            foreach ($devolucionesPorDia as $dia => $monto) {
                if (! isset($ingresosPorDia[$dia])) {
                    $ingresosPorDia[$dia] = bcsub('0', $monto, 2);
                }
            }
            $farmaciaPorDia = $ventasFarmacia->groupBy(fn ($v) => $v->fecha_venta->toDateString())
                ->map(fn ($g) => $g->reduce(fn ($acc, $v) => bcadd($acc, $v->total, 2), '0'));
            $egresosPorDia = $egresosVigentes->groupBy(fn ($e) => $e->fecha->toDateString())
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
                    // Cobertura de seguros (devengado, por cobrar a la aseguradora) y
                    // ventas brutas (caja + farmacia + seguros) = base imponible IT/IUE.
                    'ventas_seguro' => $ventasSeguro,
                    'ventas_brutas' => $ventasBrutas,
                    // Devoluciones (NC) vigentes del período: ya restadas de ingresos,
                    // ventas brutas y débito fiscal; se exponen para la tarjeta propia.
                    'devoluciones' => $totalDevoluciones,
                    'debito_devoluciones' => $debitoDevoluciones,
                    // Ventas de farmacia anuladas en el período (informativo: ya
                    // excluidas de ingresos por estado, no se restan de nuevo).
                    'devoluciones_farmacia' => $totalDevolucionesFarmacia,
                    'egresos' => $totalEgresos,
                    'credito_fiscal' => $totalCreditoFiscal,
                    'debito_fiscal' => $totalDebitoFiscal,
                    // Posición IVA del período: débito (ventas) - crédito (compras).
                    // Positivo = IVA a pagar; negativo = saldo a favor.
                    'posicion_iva' => bcsub($totalDebitoFiscal, $totalCreditoFiscal, 2),
                    'retencion_iue' => $totalRetencionIue,
                    'retencion_it' => $totalRetencionIt,
                    'retencion_total' => bcadd($totalRetencionIue, $totalRetencionIt, 2),
                    // Bases IT / IUE. IT = 3% sobre ventas brutas (devengado, incl. seguros).
                    // IUE = 25% sobre la utilidad estimada (ventas brutas - egresos);
                    // referencial — el F-500 lo arma el contador con la contabilidad completa.
                    'it_3' => Impuestos::it($ventasBrutas),
                    'utilidad_estimada' => bcsub($ventasBrutas, $totalEgresos, 2),
                    'iue_estimado' => bccomp(bcsub($ventasBrutas, $totalEgresos, 2), '0', 2) > 0
                        ? Money::mul(bcsub($ventasBrutas, $totalEgresos, 2), Impuestos::IUE)
                        : '0.00',
                    // Saldo de CAJA del período = efectivo cobrado − egresos (no incluye
                    // ventas a seguro aún no cobradas).
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
                'cobertura_seguros' => $seguroCobros->map(fn ($s) => [
                    'id' => $s->id,
                    'fecha' => $s->created_at->format('d/m/Y H:i'),
                    'paciente' => $s->cuentaCobro?->paciente?->nombre ?? 'N/A',
                    'aseguradora' => $s->seguro?->nombre_empresa ?? 'N/A',
                    'cuenta_id' => $s->cuenta_cobro_id,
                    'monto' => $s->monto,
                    'debito_fiscal' => $s->debito_fiscal,
                    'estado' => $s->estado_label,
                ]),
                'devoluciones' => $devoluciones->map(fn ($d) => [
                    'id' => $d->id,
                    'origen' => 'Caja',
                    'fecha' => $d->created_at->format('d/m/Y H:i'),
                    'fecha_orden' => $d->created_at->toDateTimeString(),
                    'paciente' => $d->cuentaCobro?->paciente?->nombre ?? 'N/A',
                    'pago_id' => $d->pago_cuenta_id,
                    'cuenta_id' => $d->cuenta_cobro_id,
                    'monto' => $d->monto,
                    'debito_fiscal' => $d->debito_fiscal,
                    'metodo' => $d->metodo_devolucion_label,
                    'referencia' => $d->referencia,
                    'motivo' => $d->motivo,
                    'tipo_label' => $d->tipo_label,
                    'usuario' => $d->user->name ?? 'N/A',
                    'anulado' => $d->anulado,
                    'motivo_anulacion' => $d->motivo_anulacion,
                    'anulado_por' => $d->anuladoPor->name ?? null,
                    'anulado_at' => $d->anulado_at?->format('d/m/Y H:i'),
                ])->concat($devolucionesFarmacia->map(fn ($v) => [
                    'id' => $v->codigo_venta,
                    'origen' => 'Farmacia',
                    'fecha' => $v->anulado_at->format('d/m/Y H:i'),
                    'fecha_orden' => $v->anulado_at->toDateTimeString(),
                    'paciente' => $v->cliente ?: ($v->paciente?->nombre ?? 'Consumidor final'),
                    'pago_id' => $v->codigo_venta,
                    'cuenta_id' => 'Venta '.$v->fecha_venta->format('d/m/Y'),
                    'monto' => $v->total,
                    'debito_fiscal' => $v->debito_fiscal,
                    'metodo' => ucfirst($v->metodo_pago),
                    'referencia' => null,
                    'motivo' => $v->motivo_anulacion ?? 'Venta anulada',
                    'tipo_label' => 'Venta anulada (stock reingresado)',
                    'usuario' => $v->anuladoPor->name ?? 'N/A',
                    'anulado' => false,
                    'motivo_anulacion' => null,
                    'anulado_por' => null,
                    'anulado_at' => null,
                ]))->sortByDesc('fecha_orden')->values(),
                'egresos_por_categoria' => $egresosPorCategoria,
                'egresos' => $egresos->map(fn ($e) => [
                    'id' => $e->id,
                    'fecha' => $e->fecha->format('d/m/Y'),
                    'fecha_iso' => $e->fecha->toDateString(),
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
                    'aplica_retencion' => $e->aplica_retencion,
                    'retencion_tipo' => $e->retencion_tipo,
                    'retencion_iue' => $e->retencion_iue,
                    'retencion_it' => $e->retencion_it,
                    'retencion_total' => $e->retencion_total,
                    'neto_pagado' => $e->neto_pagado,
                    'usuario' => $e->user->name ?? 'N/A',
                    'anulado' => $e->anulado,
                    'motivo_anulacion' => $e->motivo_anulacion,
                    'anulado_por' => $e->anuladoPor->name ?? null,
                    'anulado_at' => $e->anulado_at?->format('d/m/Y H:i'),
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
                'aplica_retencion' => filter_var($request->input('aplica_retencion', false), FILTER_VALIDATE_BOOLEAN),
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
                'aplica_retencion' => 'boolean',
                'retencion_tipo' => 'nullable|in:'.implode(',', array_keys(Egreso::RETENCION_TASAS)),
            ];

            // Si declara crédito fiscal, los datos de la factura de compra son obligatorios.
            if ($request->boolean('con_credito_fiscal')) {
                $rules['nit_proveedor'] = 'required|string|max:20';
                $rules['nro_factura'] = 'required|string|max:50';
            }

            // Retención: identificar al beneficiario (nombre + documento) y el tipo de retención.
            if ($request->boolean('aplica_retencion')) {
                $rules['retencion_tipo'] = 'required|in:'.implode(',', array_keys(Egreso::RETENCION_TASAS));
                $rules['proveedor'] = 'required|string|max:255';
                $rules['nit_proveedor'] = 'required|string|max:20';
            }

            $data = $request->validate($rules);

            // Inmutabilidad: no se registran egresos en un período ya cerrado/declarado.
            if (CierreContable::estaCerrado($data['fecha'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'El período de esa fecha está cerrado; no se pueden registrar egresos en él.',
                ], 422);
            }

            $data['user_id'] = auth()->id();

            if ($data['aplica_retencion']) {
                // Pago sin factura con retención (p.ej. honorarios médicos): excluye crédito
                // fiscal y retiene IUE+IT sobre el monto bruto. El médico cobra el neto.
                $data['con_credito_fiscal'] = false;
                $data['importe_iva'] = '0';
                $data['nro_factura'] = null;
                $data['codigo_autorizacion'] = null;

                $ret = Egreso::calcularRetenciones($data['monto'], $data['retencion_tipo']);
                $data['retencion_iue'] = $ret['iue'];
                $data['retencion_it'] = $ret['it'];
            } else {
                $data['retencion_tipo'] = null;
                $data['retencion_iue'] = '0';
                $data['retencion_it'] = '0';

                // Crédito fiscal IVA = 13% del total de la factura. Sin factura válida no hay crédito.
                if ($data['con_credito_fiscal']) {
                    $data['importe_iva'] = Impuestos::iva($data['monto']);
                } else {
                    $data['importe_iva'] = '0';
                    $data['nit_proveedor'] = null;
                    $data['nro_factura'] = null;
                    $data['codigo_autorizacion'] = null;
                }
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

    /**
     * Anula un egreso (reversible + auditado). No se borra: el registro queda visible
     * para la auditoría y deja de sumar al flujo de caja.
     */
    public function anularEgreso(Request $request, int $id): JsonResponse
    {
        try {
            $data = $request->validate([
                'motivo' => 'required|string|max:255',
            ]);

            $egreso = Egreso::findOrFail($id);

            if ($egreso->anulado) {
                return response()->json(['success' => false, 'message' => 'El egreso ya está anulado'], 422);
            }
            if (CierreContable::estaCerrado($egreso->fecha)) {
                return response()->json(['success' => false, 'message' => 'No se puede anular un egreso de un período cerrado'], 422);
            }

            $egreso->anular(auth()->id(), $data['motivo']);

            $this->logActivity(
                'anular_egreso',
                'Egreso anulado - '.$egreso->categoria_label.': Bs. '.number_format($egreso->monto, 2).' - Motivo: '.$data['motivo'],
                $egreso
            );

            return response()->json(['success' => true, 'message' => 'Egreso anulado']);
        } catch (ValidationException $e) {
            return response()->json(['success' => false, 'message' => $e->validator->errors()->first()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al anular egreso: '.$e->getMessage()], 500);
        }
    }

    /** Revierte la anulación de un egreso (vuelve a contar para el flujo de caja). */
    public function revertirAnulacionEgreso(int $id): JsonResponse
    {
        try {
            $egreso = Egreso::findOrFail($id);

            if (! $egreso->anulado) {
                return response()->json(['success' => false, 'message' => 'El egreso no está anulado'], 422);
            }
            if (CierreContable::estaCerrado($egreso->fecha)) {
                return response()->json(['success' => false, 'message' => 'No se puede modificar un egreso de un período cerrado'], 422);
            }

            $egreso->revertirAnulacion();

            $this->logActivity(
                'revertir_anulacion_egreso',
                'Anulación revertida - '.$egreso->categoria_label.': Bs. '.number_format($egreso->monto, 2),
                $egreso
            );

            return response()->json(['success' => true, 'message' => 'Anulación revertida']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al revertir anulación: '.$e->getMessage()], 500);
        }
    }

    /** Lista de períodos contables cerrados (para mostrar y para bloquear el formulario). */
    public function cierres(): JsonResponse
    {
        $cierres = CierreContable::with('cerradoPor')
            ->orderByDesc('anio')->orderByDesc('mes')->get()
            ->map(fn ($c) => [
                'id' => $c->id,
                'anio' => $c->anio,
                'mes' => $c->mes,
                'etiqueta' => $c->etiqueta,
                'cerrado_at' => $c->cerrado_at->format('d/m/Y H:i'),
                'cerrado_por' => $c->cerradoPor->name ?? 'N/A',
                'observaciones' => $c->observaciones,
            ]);

        return response()->json(['success' => true, 'cierres' => $cierres]);
    }

    /** Cierra un período contable (mes). Restringido a admin|administrador. */
    public function cerrarPeriodo(Request $request): JsonResponse
    {
        try {
            $data = $request->validate([
                'anio' => 'required|integer|min:2020|max:2100',
                'mes' => 'required|integer|min:1|max:12',
                'observaciones' => 'nullable|string|max:255',
            ]);

            // No se cierra un mes que aún no terminó (evita declarar un período incompleto).
            $periodo = Carbon::create($data['anio'], $data['mes'], 1)->endOfMonth();
            if ($periodo->isAfter(now()->endOfMonth())) {
                return response()->json(['success' => false, 'message' => 'No se puede cerrar un período futuro o en curso'], 422);
            }

            if (CierreContable::where('anio', $data['anio'])->where('mes', $data['mes'])->exists()) {
                return response()->json(['success' => false, 'message' => 'El período ya está cerrado'], 422);
            }

            $cierre = CierreContable::create([
                'anio' => $data['anio'],
                'mes' => $data['mes'],
                'cerrado_at' => now(),
                'cerrado_por' => auth()->id(),
                'observaciones' => $data['observaciones'] ?? null,
            ]);

            $this->logActivity('cerrar_periodo_contable', 'Período contable cerrado: '.$cierre->etiqueta, $cierre);

            return response()->json(['success' => true, 'message' => 'Período '.$cierre->etiqueta.' cerrado']);
        } catch (ValidationException $e) {
            return response()->json(['success' => false, 'message' => $e->validator->errors()->first()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al cerrar período: '.$e->getMessage()], 500);
        }
    }

    /** Reabre un período cerrado (auditado). Restringido a admin|administrador. */
    public function reabrirPeriodo(int $id): JsonResponse
    {
        try {
            $cierre = CierreContable::findOrFail($id);
            $etiqueta = $cierre->etiqueta;

            $this->logActivity('reabrir_periodo_contable', 'Período contable reabierto: '.$etiqueta, $cierre);
            $cierre->delete();

            return response()->json(['success' => true, 'message' => 'Período '.$etiqueta.' reabierto']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al reabrir período: '.$e->getMessage()], 500);
        }
    }

    /** Comprobante de retención imprimible (certificado para el beneficiario / respaldo F-570). */
    public function comprobanteRetencion(int $id): View
    {
        $egreso = Egreso::with('user')->findOrFail($id);
        abort_unless($egreso->aplica_retencion, 404);

        return view('caja.contabilidad.comprobante-retencion', compact('egreso'));
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

    /** Pantalla de referencia: homologación codigoProductoSin + dosificación vigente (SFE-prep). */
    public function homologacionSin(): View
    {
        return view('caja.contabilidad.homologacion-sin', [
            'mapa' => \App\Support\CodigoSin::POR_FAMILIA,
            'etiquetas' => \App\Support\CodigoSin::ETIQUETA_FAMILIA,
            'dosificacion' => Dosificacion::activa(),
        ]);
    }

    /** Exporta el Registro de Compras y Ventas (RCV) homologable + resumen de impuestos. */
    public function exportarRcv(Request $request)
    {
        $request->validate([
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
        ]);

        [$inicio, $fin] = $this->rango($request);
        $nombre = 'rcv_'.$inicio->format('Ymd').'_'.$fin->format('Ymd').'.xlsx';

        return Excel::download(new RegistroComprasVentasExport($inicio, $fin), $nombre);
    }

    private function rango(Request $request): array
    {
        return [
            Carbon::parse($request->fecha_inicio)->startOfDay(),
            Carbon::parse($request->fecha_fin)->endOfDay(),
        ];
    }
}
