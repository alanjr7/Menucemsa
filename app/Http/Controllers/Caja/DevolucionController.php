<?php

namespace App\Http\Controllers\Caja;

use App\Http\Controllers\Controller;
use App\Models\CierreContable;
use App\Models\Devolucion;
use App\Models\PagoCuenta;
use App\Models\VentaFarmacia;
use App\Support\Money;
use App\Traits\AuditLoggable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * Devoluciones / Notas de Crédito sobre pagos (contra-ingreso).
 *
 * El pago original es inmutable; la devolución es un documento nuevo (NC-)
 * que resta de los ingresos del período CORRIENTE (no del período del pago).
 * Restringido a admin|administrador (grupo de rutas caja-gestion).
 */
class DevolucionController extends Controller
{
    use AuditLoggable;

    /** Página propia del módulo Devoluciones / Notas de Crédito. */
    public function index(): \Illuminate\View\View
    {
        return view('caja.devoluciones.index');
    }

    /**
     * Listado unificado de devoluciones con filtros + KPIs (página propia):
     * NC de caja (origen "Caja") + ventas de farmacia anuladas con reingreso de
     * stock (origen "Farmacia", fechadas por anulado_at, solo lectura aquí).
     */
    public function listar(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'q' => 'nullable|string|max:100',
                'fecha_inicio' => 'nullable|date',
                'fecha_fin' => 'nullable|date',
                'estado' => 'nullable|in:todas,vigentes,anuladas',
            ]);

            $q = trim((string) $request->input('q', ''));
            $estado = $request->input('estado', 'todas');
            [$desde, $hasta] = [null, null];
            if ($request->filled('fecha_inicio') && $request->filled('fecha_fin')) {
                $desde = \Carbon\Carbon::parse($request->fecha_inicio)->startOfDay();
                $hasta = \Carbon\Carbon::parse($request->fecha_fin)->endOfDay();
            } elseif ($request->filled('fecha_inicio')) {
                $desde = \Carbon\Carbon::parse($request->fecha_inicio)->startOfDay();
                $hasta = \Carbon\Carbon::parse($request->fecha_inicio)->endOfDay();
            }

            // ── NC de caja ──
            $ncQuery = Devolucion::with(['user', 'anuladoPor', 'cuentaCobro.paciente']);
            if ($q !== '') {
                $ncQuery->where(function ($sub) use ($q) {
                    $sub->where('id', 'like', "%{$q}%")
                        ->orWhere('pago_cuenta_id', 'like', "%{$q}%")
                        ->orWhere('cuenta_cobro_id', 'like', "%{$q}%")
                        ->orWhereHas('cuentaCobro.paciente', function ($p) use ($q) {
                            $p->where('nombre', 'like', "%{$q}%")
                                ->orWhere('ci', 'like', "%{$q}%")
                                ->orWhere('temp_code', 'like', "%{$q}%");
                        });
                });
            }
            if ($desde) {
                $ncQuery->whereBetween('created_at', [$desde, $hasta]);
            }
            match ($estado) {
                'vigentes' => $ncQuery->whereNull('anulado_at'),
                'anuladas' => $ncQuery->whereNotNull('anulado_at'),
                default => null,
            };

            $ncs = $ncQuery->get()->map(fn ($d) => [
                'id' => $d->id,
                'origen' => 'Caja',
                'fecha' => $d->created_at->format('d/m/Y H:i'),
                'fecha_orden' => $d->created_at->toDateTimeString(),
                'paciente' => $d->cuentaCobro?->paciente?->nombre ?? 'N/A',
                'pago_id' => $d->pago_cuenta_id,
                'cuenta_id' => $d->cuenta_cobro_id,
                'monto' => $d->monto,
                'metodo' => $d->metodo_devolucion_label,
                'referencia' => $d->referencia,
                'motivo' => $d->motivo,
                'tipo_label' => $d->tipo_label,
                'anula_cargos' => $d->anula_cargos,
                'usuario' => $d->user->name ?? 'N/A',
                'anulado' => $d->anulado,
                'motivo_anulacion' => $d->motivo_anulacion,
                'anulado_por' => $d->anuladoPor->name ?? null,
                'anulado_at' => $d->anulado_at?->format('d/m/Y H:i'),
            ]);

            // ── Farmacia: ventas anuladas (devolución con reingreso de stock) ──
            // Son devoluciones VIGENTES (no reversibles desde aquí); se excluyen
            // solo cuando se filtra "anuladas" (ese estado es de las NC).
            $farmacia = collect();
            if ($estado !== 'anuladas') {
                $fQuery = VentaFarmacia::with(['anuladoPor', 'paciente'])
                    ->where('estado', 'ANULADA')
                    ->whereNotNull('anulado_at');
                if ($q !== '') {
                    $fQuery->where(function ($sub) use ($q) {
                        $sub->where('codigo_venta', 'like', "%{$q}%")
                            ->orWhere('cliente', 'like', "%{$q}%")
                            ->orWhereHas('paciente', function ($p) use ($q) {
                                $p->where('nombre', 'like', "%{$q}%")
                                    ->orWhere('ci', 'like', "%{$q}%");
                            });
                    });
                }
                if ($desde) {
                    $fQuery->whereBetween('anulado_at', [$desde, $hasta]);
                }

                $farmacia = $fQuery->get()->map(fn ($v) => [
                    'id' => $v->codigo_venta,
                    'origen' => 'Farmacia',
                    'fecha' => $v->anulado_at->format('d/m/Y H:i'),
                    'fecha_orden' => $v->anulado_at->toDateTimeString(),
                    'paciente' => $v->cliente ?: ($v->paciente?->nombre ?? 'Consumidor final'),
                    'pago_id' => $v->codigo_venta,
                    'cuenta_id' => 'Venta '.$v->fecha_venta->format('d/m/Y'),
                    'monto' => $v->total,
                    'metodo' => ucfirst($v->metodo_pago),
                    'referencia' => null,
                    'motivo' => $v->motivo_anulacion ?? 'Venta anulada',
                    'tipo_label' => 'Venta anulada (stock reingresado)',
                    'anula_cargos' => true,
                    'usuario' => $v->anuladoPor->name ?? 'N/A',
                    'anulado' => false,
                    'motivo_anulacion' => null,
                    'anulado_por' => null,
                    'anulado_at' => null,
                ]);
            }

            $todas = $ncs->concat($farmacia)->sortByDesc('fecha_orden')->values();

            // KPIs sobre el MISMO filtro: lo vigente incluye farmacia (devoluciones
            // efectivas); "anuladas" cuenta solo NC anuladas (farmacia no se anula aquí).
            $stats = [
                'total_vigente' => $todas->where('anulado', false)
                    ->reduce(fn ($acc, $d) => Money::add($acc, $d['monto']), '0'),
                'cantidad' => $todas->count(),
                'anuladas' => $todas->where('anulado', true)->count(),
            ];

            // Paginación manual sobre la colección fusionada (mismo shape que paginate()).
            $porPagina = 25;
            $pagina = max(1, (int) $request->input('page', 1));
            $devoluciones = new \Illuminate\Pagination\LengthAwarePaginator(
                $todas->forPage($pagina, $porPagina)->values(),
                $todas->count(),
                $porPagina,
                $pagina
            );

            return response()->json(['success' => true, 'devoluciones' => $devoluciones, 'stats' => $stats]);
        } catch (ValidationException $e) {
            return response()->json(['success' => false, 'message' => $e->validator->errors()->first()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al listar devoluciones: '.$e->getMessage()], 500);
        }
    }

    /** Estado de devoluciones de un pago: NC emitidas + monto disponible (para el modal). */
    public function porPago(string $pagoId): JsonResponse
    {
        $pago = PagoCuenta::with(['devoluciones.user', 'devoluciones.anuladoPor', 'cuentaCobro.paciente'])
            ->findOrFail($pagoId);

        return response()->json([
            'success' => true,
            'pago' => [
                'id' => $pago->id,
                'cuenta_cobro_id' => $pago->cuenta_cobro_id,
                'paciente' => $pago->cuentaCobro?->paciente?->nombre ?? 'N/A',
                'fecha' => $pago->created_at->format('d/m/Y H:i'),
                'monto' => $pago->monto,
                'metodo_pago' => $pago->metodo_pago_label,
                'monto_devuelto' => $pago->monto_devuelto,
                'monto_disponible' => Devolucion::montoDisponible($pago),
            ],
            'devoluciones' => $pago->devoluciones->sortByDesc('created_at')->values()->map(fn ($d) => [
                'id' => $d->id,
                'fecha' => $d->created_at->format('d/m/Y H:i'),
                'monto' => $d->monto,
                'metodo' => $d->metodo_devolucion_label,
                'referencia' => $d->referencia,
                'motivo' => $d->motivo,
                'tipo_label' => $d->tipo_label,
                'usuario' => $d->user->name ?? 'N/A',
                'anulado' => $d->anulado,
                'motivo_anulacion' => $d->motivo_anulacion,
                'anulado_por' => $d->anuladoPor->name ?? null,
                'anulado_at' => $d->anulado_at?->format('d/m/Y H:i'),
            ]),
        ]);
    }

    /** Registra una devolución (NC) sobre un pago. */
    public function store(Request $request, string $pagoId): JsonResponse
    {
        try {
            $request->merge([
                'monto' => str_replace(',', '.', (string) $request->monto),
                'anula_cargos' => filter_var($request->input('anula_cargos', true), FILTER_VALIDATE_BOOLEAN),
            ]);

            $data = $request->validate([
                'monto' => Money::rules(min: '0.01'),
                'metodo_devolucion' => 'required|in:efectivo,transferencia,tarjeta,qr',
                'motivo' => 'required|string|max:255',
                // Caso de negocio: true = servicio no realizado (anula el cargo);
                // false = error de cobro (el cargo se mantiene y se vuelve a cobrar).
                'anula_cargos' => 'required|boolean',
                'referencia' => 'nullable|string|max:255',
                'observaciones' => 'nullable|string|max:1000',
                'idempotency_key' => 'nullable|string|max:100',
            ]);

            // La NC se emite HOY: si el período corriente está cerrado/declarado,
            // no se puede ajustar (mismo bloqueo que egresos).
            if (CierreContable::estaCerrado(now()->toDateString())) {
                return response()->json([
                    'success' => false,
                    'message' => 'El período contable actual está cerrado; no se pueden registrar devoluciones.',
                ], 422);
            }

            $pago = PagoCuenta::findOrFail($pagoId);

            $data['user_id'] = auth()->id();
            // Arqueo: si quien devuelve tiene caja abierta, el dinero sale de ese
            // cajón (queda el movimiento de egreso y resta del esperado al cierre).
            $data['caja_session_id'] = \App\Models\CajaSession::delUsuario(auth()->id())
                ->abierta()->value('id');
            // Clave compuesta {token}:{pagoId}: un mismo token de formulario no
            // bloquea devoluciones legítimas sobre otros pagos (patrón de caja).
            if (!empty($data['idempotency_key'])) {
                $data['idempotency_key'] = $data['idempotency_key'].':'.$pago->id;
            }

            $devolucion = Devolucion::registrarPara($pago, $data);

            if ($devolucion === null) {
                // Replay idempotente: la devolución ya se registró en un intento previo.
                return response()->json([
                    'success' => true,
                    'message' => 'La devolución ya fue registrada (reintento ignorado).',
                ]);
            }

            $this->logActivity(
                'registrar_devolucion',
                'Devolución registrada '.$devolucion->id.' - Bs. '.number_format((float) $devolucion->monto, 2)
                    .' del recibo '.$pago->id.' - Motivo: '.$devolucion->motivo,
                $devolucion
            );

            $mensaje = $devolucion->anula_cargos
                ? 'Devolución '.$devolucion->id.' registrada; los cargos equivalentes fueron anulados.'
                : 'Devolución '.$devolucion->id.' registrada; el cargo se mantiene y la cuenta queda pendiente para volver a cobrarse.';
            // Residuo: parte del monto que no pudo anularse en cargos (precio que no
            // divide exacto o cargos de otro ciclo). Queda como saldo y se avisa.
            if ($devolucion->residuoSinAnular !== null && Money::cmp($devolucion->residuoSinAnular, '0') > 0) {
                $mensaje .= ' Atención: Bs '.number_format((float) $devolucion->residuoSinAnular, 2)
                    .' no pudieron anularse automáticamente y quedan como saldo pendiente de la cuenta (gestionar en Ajustes de Paciente).';
            }

            return response()->json([
                'success' => true,
                'message' => $mensaje,
                'devolucion_id' => $devolucion->id,
                'residuo_sin_anular' => $devolucion->residuoSinAnular,
            ]);
        } catch (ValidationException $e) {
            return response()->json(['success' => false, 'message' => $e->validator->errors()->first()], 422);
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al registrar devolución: '.$e->getMessage()], 500);
        }
    }

    /** Anula una NC (reversible + auditado): el dinero vuelve a contar como cobrado. */
    public function anular(Request $request, string $id): JsonResponse
    {
        try {
            $data = $request->validate(['motivo' => 'required|string|max:255']);

            $devolucion = Devolucion::findOrFail($id);

            if ($devolucion->anulado) {
                return response()->json(['success' => false, 'message' => 'La devolución ya está anulada'], 422);
            }
            if (CierreContable::estaCerrado($devolucion->created_at->toDateString())) {
                return response()->json(['success' => false, 'message' => 'No se puede anular una devolución de un período cerrado'], 422);
            }

            $devolucion->anular(auth()->id(), $data['motivo']);

            $this->logActivity(
                'anular_devolucion',
                'Devolución anulada '.$devolucion->id.' - Bs. '.number_format((float) $devolucion->monto, 2).' - Motivo: '.$data['motivo'],
                $devolucion
            );

            return response()->json(['success' => true, 'message' => 'Devolución anulada']);
        } catch (ValidationException $e) {
            return response()->json(['success' => false, 'message' => $e->validator->errors()->first()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al anular devolución: '.$e->getMessage()], 500);
        }
    }

    /** Revierte la anulación de una NC (vuelve a restar de los ingresos). */
    public function revertir(string $id): JsonResponse
    {
        try {
            $devolucion = Devolucion::findOrFail($id);

            if (! $devolucion->anulado) {
                return response()->json(['success' => false, 'message' => 'La devolución no está anulada'], 422);
            }
            if (CierreContable::estaCerrado($devolucion->created_at->toDateString())) {
                return response()->json(['success' => false, 'message' => 'No se puede modificar una devolución de un período cerrado'], 422);
            }

            $devolucion->revertirAnulacion();

            $this->logActivity(
                'revertir_anulacion_devolucion',
                'Anulación revertida de devolución '.$devolucion->id.' - Bs. '.number_format((float) $devolucion->monto, 2),
                $devolucion
            );

            return response()->json(['success' => true, 'message' => 'Anulación revertida']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al revertir anulación: '.$e->getMessage()], 500);
        }
    }

    /** Comprobante de la Nota de Crédito imprimible. */
    public function comprobante(string $id): View
    {
        $devolucion = Devolucion::with(['pago', 'cuentaCobro.paciente', 'user'])->findOrFail($id);

        return view('caja.devoluciones.comprobante', compact('devolucion'));
    }
}
