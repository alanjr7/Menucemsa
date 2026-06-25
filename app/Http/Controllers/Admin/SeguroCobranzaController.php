<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CierreContable;
use App\Models\Seguro;
use App\Models\SeguroCobro;
use App\Support\Money;
use App\Traits\AuditLoggable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * Sub-mayor de cuentas por cobrar a aseguradoras (Sprint 2).
 *
 * Cada {@see SeguroCobro} es una venta cubierta por un seguro (devengada en el Sprint 1).
 * Acá se gestiona su COBRANZA: ver lo pendiente por aseguradora, la antigüedad (aging) de
 * la deuda, y registrar el cobro (liquidación) cuando la aseguradora paga. La anulación
 * revierte la venta (auditada, bloqueada en períodos contables cerrados).
 */
class SeguroCobranzaController extends Controller
{
    use AuditLoggable;

    /** Tramos de antigüedad de la deuda (días desde que se devengó). */
    private const AGING_BUCKETS = ['0-30', '31-60', '61-90', '90+'];

    public function index(Request $request): View
    {
        $query = SeguroCobro::with(['cuentaCobro.paciente', 'seguro', 'autorizadoPor', 'liquidadoPor'])
            ->when($request->filled('seguro_id'), fn ($q) => $q->where('seguro_id', $request->seguro_id))
            ->when($request->filled('estado'), fn ($q) => $q->where('estado', $request->estado))
            ->when($request->filled('fecha_inicio'), fn ($q) => $q->whereDate('created_at', '>=', $request->fecha_inicio))
            ->when($request->filled('fecha_fin'), fn ($q) => $q->whereDate('created_at', '<=', $request->fecha_fin));

        $cobros = (clone $query)->orderByDesc('created_at')->paginate(20)->withQueryString();

        // Posición de cobranza GLOBAL (no depende de los filtros de la tabla): lo que las
        // aseguradoras deben hoy. La antigüedad se mide desde la fecha de devengo.
        $pendientes = SeguroCobro::with('seguro')->pendiente()->get();

        $stats = [
            'total_por_cobrar' => $pendientes->reduce(fn ($a, $s) => Money::add($a, $s->monto), '0'),
            'cantidad_pendientes' => $pendientes->count(),
            'total_cobrado' => SeguroCobro::cobrado()
                ->when($request->filled('fecha_inicio'), fn ($q) => $q->whereDate('liquidado_en', '>=', $request->fecha_inicio))
                ->when($request->filled('fecha_fin'), fn ($q) => $q->whereDate('liquidado_en', '<=', $request->fecha_fin))
                ->sum('monto'),
            'aseguradoras_con_deuda' => $pendientes->pluck('seguro_id')->unique()->count(),
        ];

        // Aging global + desglose por aseguradora.
        $aging = array_fill_keys(self::AGING_BUCKETS, '0');
        $porAseguradora = [];
        foreach ($pendientes as $s) {
            $bucket = $this->bucket($s->created_at->diffInDays(now()));
            $aging[$bucket] = Money::add($aging[$bucket], $s->monto);

            $sid = $s->seguro_id;
            if (! isset($porAseguradora[$sid])) {
                $porAseguradora[$sid] = [
                    'seguro_id' => $sid,
                    'nombre' => $s->seguro?->nombre_empresa ?? 'N/A',
                    'total' => '0',
                    'cantidad' => 0,
                    'dias_max' => 0,
                ];
            }
            $porAseguradora[$sid]['total'] = Money::add($porAseguradora[$sid]['total'], $s->monto);
            $porAseguradora[$sid]['cantidad']++;
            $porAseguradora[$sid]['dias_max'] = max($porAseguradora[$sid]['dias_max'], (int) $s->created_at->diffInDays(now()));
        }
        $porAseguradora = collect($porAseguradora)->sortByDesc('total')->values();

        $seguros = Seguro::orderBy('nombre_empresa')->get(['id', 'nombre_empresa']);

        return view('admin.seguros-cobranza', compact('cobros', 'stats', 'aging', 'porAseguradora', 'seguros'));
    }

    /** Registra el cobro de una venta a la aseguradora (liquidación). */
    public function liquidar(Request $request, SeguroCobro $seguroCobro): JsonResponse
    {
        try {
            $data = $request->validate([
                'referencia' => 'nullable|string|max:120',
            ]);

            if (! $seguroCobro->estaPendiente()) {
                return response()->json(['success' => false, 'message' => 'Esta cobertura no está pendiente de cobro.'], 422);
            }

            $seguroCobro->liquidar(auth()->id(), $data['referencia'] ?? null);

            $this->logActivity(
                'liquidar_cobro_seguro',
                'Cobro de seguro liquidado - ' . $seguroCobro->id . ' (' . ($seguroCobro->seguro?->nombre_empresa ?? '') . '): Bs. ' . number_format($seguroCobro->monto, 2),
                $seguroCobro
            );

            return response()->json(['success' => true, 'message' => 'Cobro registrado correctamente.']);
        } catch (ValidationException $e) {
            return response()->json(['success' => false, 'message' => $e->validator->errors()->first()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al liquidar: ' . $e->getMessage()], 500);
        }
    }

    /** Liquida en lote todo lo pendiente de una aseguradora (las aseguradoras pagan por lote). */
    public function liquidarLote(Request $request, Seguro $seguro): JsonResponse
    {
        try {
            $data = $request->validate([
                'referencia' => 'nullable|string|max:120',
            ]);

            $pendientes = SeguroCobro::where('seguro_id', $seguro->id)->pendiente()->get();
            if ($pendientes->isEmpty()) {
                return response()->json(['success' => false, 'message' => 'No hay coberturas pendientes para esta aseguradora.'], 422);
            }

            $total = '0';
            foreach ($pendientes as $s) {
                $s->liquidar(auth()->id(), $data['referencia'] ?? null);
                $total = Money::add($total, $s->monto);
            }

            $this->logActivity(
                'liquidar_lote_seguro',
                'Liquidación en lote ' . $seguro->nombre_empresa . ': ' . $pendientes->count() . ' coberturas, Bs. ' . number_format((float) $total, 2),
                $seguro
            );

            return response()->json([
                'success' => true,
                'message' => 'Se liquidaron ' . $pendientes->count() . ' coberturas (Bs. ' . number_format((float) $total, 2) . ').',
            ]);
        } catch (ValidationException $e) {
            return response()->json(['success' => false, 'message' => $e->validator->errors()->first()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al liquidar en lote: ' . $e->getMessage()], 500);
        }
    }

    /** Anula una venta a seguro (reversible + auditada). Bloqueada si el período fiscal está cerrado. */
    public function anular(Request $request, SeguroCobro $seguroCobro): JsonResponse
    {
        try {
            $data = $request->validate([
                'motivo' => 'required|string|max:255',
            ]);

            if ($seguroCobro->estaAnulado()) {
                return response()->json(['success' => false, 'message' => 'La cobertura ya está anulada.'], 422);
            }

            // Anular retira una venta del débito fiscal del período en que se devengó: no
            // se permite si ese mes ya fue declarado/cerrado.
            if (CierreContable::estaCerrado($seguroCobro->created_at)) {
                return response()->json(['success' => false, 'message' => 'No se puede anular: el período contable de esa venta está cerrado.'], 422);
            }

            $seguroCobro->anular(auth()->id(), $data['motivo']);

            $this->logActivity(
                'anular_cobro_seguro',
                'Cobertura de seguro anulada - ' . $seguroCobro->id . ': Bs. ' . number_format($seguroCobro->monto, 2) . ' - Motivo: ' . $data['motivo'],
                $seguroCobro
            );

            return response()->json(['success' => true, 'message' => 'Cobertura anulada.']);
        } catch (ValidationException $e) {
            return response()->json(['success' => false, 'message' => $e->validator->errors()->first()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al anular: ' . $e->getMessage()], 500);
        }
    }

    private function bucket(int $dias): string
    {
        return match (true) {
            $dias <= 30 => '0-30',
            $dias <= 60 => '31-60',
            $dias <= 90 => '61-90',
            default => '90+',
        };
    }
}
