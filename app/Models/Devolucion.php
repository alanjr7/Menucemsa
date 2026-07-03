<?php

namespace App\Models;

use App\Support\Money;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

/**
 * Devolución / Nota de Crédito interna (NC-AAAA-NNNNNN).
 *
 * Contra-ingreso: devuelve dinero de un pago YA cobrado sin tocar el pago
 * original (PagoCuenta es inmutable) y sin registrarlo como egreso (no es un
 * gasto, es una disminución de ingresos). Revierte el débito fiscal IVA del
 * monto devuelto y reduce el total_pagado de la cuenta, reabriendo el saldo.
 *
 * La NC vive en la fecha en que se EMITE (período corriente), no en la del
 * pago: si el mes del pago ya se declaró al SIN, no se reescribe el pasado —
 * el ajuste impacta el período actual, igual que una Nota de Crédito real.
 *
 * Montos en POSITIVO; los reportes los restan (mismo criterio que anulados).
 */
class Devolucion extends Model
{
    use HasFactory;

    /**
     * Parte del monto devuelto que NO pudo anularse en cargos (solo informativo,
     * no se persiste). Queda > 0 cuando los cargos vivos no alcanzan o el precio
     * unitario no divide exacto: ese resto permanece como saldo pendiente y se
     * gestiona a mano en Correcciones.
     */
    public ?string $residuoSinAnular = null;

    protected $table = 'devoluciones';

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'pago_cuenta_id',
        'cuenta_cobro_id',
        'monto',
        'base_imponible',
        'debito_fiscal',
        'metodo_devolucion',
        'referencia',
        'motivo',
        'anula_cargos',
        'observaciones',
        'user_id',
        'caja_session_id',
        'idempotency_key',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'base_imponible' => 'decimal:2',
        'debito_fiscal' => 'decimal:2',
        'anula_cargos' => 'boolean',
        'anulado_at' => 'datetime',
    ];

    // Relaciones
    public function pago(): BelongsTo
    {
        return $this->belongsTo(PagoCuenta::class, 'pago_cuenta_id');
    }

    public function cuentaCobro(): BelongsTo
    {
        return $this->belongsTo(CuentaCobro::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function cajaSession(): BelongsTo
    {
        return $this->belongsTo(CajaSession::class);
    }

    public function anuladoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'anulado_por');
    }

    // Scopes
    /** Devoluciones no anuladas — las únicas que restan de los ingresos. */
    public function scopeVigentes($query)
    {
        return $query->whereNull('anulado_at');
    }

    public function scopePorMetodo($query, string $metodo)
    {
        return $query->where('metodo_devolucion', $metodo);
    }

    public function scopeDelDia($query, $fecha = null)
    {
        return $query->whereDate('created_at', $fecha ?? now()->toDateString());
    }

    // Getters
    public function getAnuladoAttribute(): bool
    {
        return $this->anulado_at !== null;
    }

    public function getMetodoDevolucionLabelAttribute(): string
    {
        return match ($this->metodo_devolucion) {
            'efectivo' => 'Efectivo',
            'transferencia' => 'Transferencia',
            'tarjeta' => 'Tarjeta',
            'qr' => 'QR',
            default => ucfirst($this->metodo_devolucion),
        };
    }

    /** Etiqueta del caso de negocio de la devolución. */
    public function getTipoLabelAttribute(): string
    {
        return $this->anula_cargos
            ? 'Servicio no realizado (cargo anulado)'
            : 'Error de cobro (se vuelve a cobrar)';
    }

    /**
     * Registra una devolución sobre un pago (fuente única, ACID).
     *
     * En una sola transacción con lockForUpdate sobre la cuenta (mismo rigor
     * que el cobro): valida el tope devolvible del pago, crea la NC y reduce
     * total_pagado. El efecto sobre los CARGOS depende del caso de negocio
     * (datos['anula_cargos'], default true):
     *  - true  → "servicio no realizado": anula cargos por el monto devuelto
     *            (reversa la venta completa; la cuenta queda en 0 y no se recobra).
     *  - false → "error de cobro": el cargo se mantiene y la cuenta vuelve a
     *            pendiente/parcial para cobrarse correctamente.
     * El residuo no anulable (si lo hay) queda en $devolucion->residuoSinAnular.
     *
     * @param  array{monto: string|float, metodo_devolucion: string, motivo: string,
     *               referencia?: ?string, observaciones?: ?string, user_id: int,
     *               caja_session_id?: ?int, idempotency_key?: ?string}  $datos
     * @return self|null  null si fue un replay idempotente (token ya usado).
     *
     * @throws \RuntimeException si el monto excede lo devolvible del pago.
     */
    public static function registrarPara(PagoCuenta $pago, array $datos): ?self
    {
        return DB::transaction(function () use ($pago, $datos) {
            // Idempotencia: token ya usado = reintento; no se re-aplica nada.
            $token = $datos['idempotency_key'] ?? null;
            if ($token && static::where('idempotency_key', $token)->exists()) {
                return null;
            }

            $cuenta = CuentaCobro::whereKey($pago->cuenta_cobro_id)
                ->lockForUpdate()->firstOrFail();

            // Tope devolvible = monto del pago − devoluciones vigentes previas.
            // El lock de la cuenta serializa devoluciones concurrentes del mismo pago.
            $disponible = static::montoDisponible($pago);
            if (Money::cmp($datos['monto'], $disponible) > 0) {
                throw new \RuntimeException(sprintf(
                    'No se puede devolver Bs %s: el recibo %s tiene Bs %s disponibles para devolución.',
                    number_format((float) Money::format($datos['monto']), 2),
                    $pago->id,
                    number_format((float) $disponible, 2)
                ));
            }

            $devolucion = static::create([
                'pago_cuenta_id' => $pago->id,
                'cuenta_cobro_id' => $pago->cuenta_cobro_id,
                'monto' => Money::format($datos['monto']),
                'metodo_devolucion' => $datos['metodo_devolucion'],
                'referencia' => $datos['referencia'] ?? null,
                'motivo' => $datos['motivo'],
                'anula_cargos' => (bool) ($datos['anula_cargos'] ?? true),
                'observaciones' => $datos['observaciones'] ?? null,
                'user_id' => $datos['user_id'],
                'caja_session_id' => $datos['caja_session_id'] ?? null,
                'idempotency_key' => $token,
            ]);

            $devolucion->residuoSinAnular = $devolucion->aplicarACuenta($cuenta);

            // Arqueo: si el usuario tiene caja abierta, el dinero devuelto sale de
            // ese cajón y debe restar del efectivo esperado al cierre de la sesión.
            $devolucion->registrarMovimientoCaja('egreso', 'Devolución '.$devolucion->id);

            return $devolucion;
        });
    }

    /**
     * Registra el movimiento de arqueo de la devolución en la sesión de caja
     * asociada, solo si sigue ABIERTA (una sesión cerrada ya rindió cuentas y
     * no se reescribe). tipo: egreso al devolver, ingreso al anular la NC.
     */
    private function registrarMovimientoCaja(string $tipo, string $concepto): void
    {
        if (! $this->caja_session_id) {
            return;
        }

        $sesion = CajaSession::find($this->caja_session_id);
        if (! $sesion || $sesion->estado !== 'abierta') {
            return;
        }

        // movable apunta a la sesión (movable_id es entero; el id string de la
        // NC no cabe ahí): la trazabilidad de la NC va en referencia.
        MovimientoCaja::create([
            'caja_session_id' => $this->caja_session_id,
            'tipo' => $tipo,
            'concepto' => $concepto,
            'monto' => $this->monto,
            'referencia' => $this->id,
            'metodo_pago' => $this->metodo_devolucion,
            'observaciones' => 'Nota de crédito sobre el recibo '.$this->pago_cuenta_id,
            'movable_type' => CajaSession::class,
            'movable_id' => $this->caja_session_id,
        ]);
    }

    /** Monto aún devolvible de un pago = monto − devoluciones vigentes. */
    public static function montoDisponible(PagoCuenta $pago): string
    {
        $devuelto = static::where('pago_cuenta_id', $pago->id)->vigentes()->get()
            ->reduce(fn ($acc, $d) => Money::add($acc, $d->monto), '0');

        return Money::clampZero(Money::sub($pago->monto, $devuelto));
    }

    /**
     * Suma de devoluciones vigentes en un rango (opcional por método). Fuente
     * única para NETEAR ingresos en dashboards, cierres y reportes: todo lugar
     * que sume PagoCuenta debe restar esta suma del mismo rango.
     */
    public static function sumaVigente($inicio, $fin, ?string $metodo = null): string
    {
        $q = static::vigentes()->whereBetween('created_at', [$inicio, $fin]);
        if ($metodo !== null) {
            $q->where('metodo_devolucion', $metodo);
        }

        return Money::format($q->sum('monto'));
    }

    /** Suma de devoluciones vigentes de un día (opcional por método). */
    public static function sumaVigenteDelDia($fecha = null, ?string $metodo = null): string
    {
        $fecha = $fecha ?? now()->toDateString();
        $q = static::vigentes()->whereDate('created_at', $fecha);
        if ($metodo !== null) {
            $q->where('metodo_devolucion', $metodo);
        }

        return Money::format($q->sum('monto'));
    }

    /**
     * Anula la NC (reversible + auditado): el dinero "vuelve a estar cobrado".
     * Simetría exacta con el registro: primero RESTAURA los cargos que la NC
     * anuló (solo esos, vía devolucion_id en los eventos), luego re-aplica el
     * monto a total_pagado y, si la cuenta queda saldada, vuelve a liquidar
     * los cargos pendientes contra el pago original.
     */
    public function anular(int $userId, string $motivo): void
    {
        DB::transaction(function () use ($userId, $motivo) {
            $cuenta = CuentaCobro::whereKey($this->cuenta_cobro_id)
                ->lockForUpdate()->firstOrFail();

            $this->anulado_at = now();
            $this->anulado_por = $userId;
            $this->motivo_anulacion = $motivo;
            $this->save();

            // 1) Des-sellar los cargos liquidados contra el pago devuelto: la
            //    reversión de un cargo liquidado está bloqueada por diseño, y al
            //    final de este método se re-liquida todo si la cuenta queda saldada.
            $cuenta->detalles()->where('liquidado_pago_id', $this->pago_cuenta_id)->update([
                'liquidado_en' => null,
                'liquidado_pago_id' => null,
            ]);

            // 2) Restaurar los cargos que esta NC anuló (recompone total_calculado).
            $this->revertirCargosAnulados($userId);

            // 3) Restituir el pago sobre los totales ya recompuestos.
            $cuenta->refresh();
            $cuenta->total_pagado = Money::add($cuenta->total_pagado, $this->monto);
            $cuenta->load('detalles');
            $cuenta->recalcularTotales();

            if ($cuenta->estado === 'pagado') {
                $cuenta->detalles()->whereNull('liquidado_en')->update([
                    'liquidado_en' => now(),
                    'liquidado_pago_id' => $this->pago_cuenta_id,
                ]);
            }

            // Compensación de arqueo (solo si la sesión sigue abierta). El concepto
            // empieza con "Cobro" a propósito: las fórmulas de cierre suman los
            // ingresos con `concepto like 'Cobro%'` y esta reposición debe contar.
            $this->registrarMovimientoCaja('ingreso', 'Cobro restituido por anulación de devolución '.$this->id);
        });
    }

    /**
     * Revierte la anulación: la devolución vuelve a restar de la cuenta y a
     * anular los cargos correspondientes (eventos nuevos con el mismo id de NC;
     * los anteriores ya quedaron sellados como revertidos).
     */
    public function revertirAnulacion(): void
    {
        DB::transaction(function () {
            $cuenta = CuentaCobro::whereKey($this->cuenta_cobro_id)
                ->lockForUpdate()->firstOrFail();

            $this->anulado_at = null;
            $this->anulado_por = null;
            $this->motivo_anulacion = null;
            $this->save();

            $this->residuoSinAnular = $this->aplicarACuenta($cuenta);

            $this->registrarMovimientoCaja('egreso', 'Devolución '.$this->id.' (reactivada)');
        });
    }

    /**
     * Efecto de la devolución sobre la cuenta: resta total_pagado y des-liquida
     * los cargos del pago devuelto. Luego, según el caso de negocio:
     *  - anula_cargos = true ("servicio no realizado"): ANULA cargos por el
     *    hueco que abrió la devolución (reversa la venta: dinero + cargo). El
     *    hueco se mide como saldo_después − saldo_antes, así una devolución de
     *    SOBREPAGO (dinero cobrado de más, sin venta detrás) no toca cargos.
     *  - anula_cargos = false ("error de cobro"): los cargos quedan intactos y
     *    la cuenta vuelve a pendiente/parcial para volver a cobrarse bien.
     * Llamar SIEMPRE dentro de una transacción con la cuenta bloqueada.
     *
     * @return string  Residuo que no pudo anularse en cargos (queda como saldo).
     */
    private function aplicarACuenta(CuentaCobro $cuenta): string
    {
        $saldoAntes = Money::format($cuenta->saldo_pendiente);

        $cuenta->total_pagado = Money::clampZero(Money::sub($cuenta->total_pagado, $this->monto));
        $cuenta->load('detalles');
        $cuenta->recalcularTotales();

        if ($cuenta->estado !== 'pagado') {
            $cuenta->detalles()->where('liquidado_pago_id', $this->pago_cuenta_id)->update([
                'liquidado_en' => null,
                'liquidado_pago_id' => null,
            ]);
        }

        // "Error de cobro": el cargo se mantiene; el saldo reabierto es correcto
        // (se volverá a cobrar). No hay nada que anular ni residuo que reportar.
        if (! $this->anula_cargos) {
            return '0.00';
        }

        // "Servicio no realizado": anular cargos por el hueco abierto por ESTA
        // devolución (excluye saldo pendiente previo).
        $objetivo = Money::clampZero(Money::sub($cuenta->saldo_pendiente, $saldoAntes));
        $anulado = $this->anularCargosPorDevolucion($cuenta, $objetivo);

        // Si la cuenta quedó saldada, re-sellar los cargos restantes contra el
        // pago original (mismo ciclo): el recibo no re-lista lo ya pagado.
        $cuenta->refresh();
        if ($cuenta->estado === 'pagado') {
            $cuenta->detalles()->whereNull('liquidado_en')->update([
                'liquidado_en' => now(),
                'liquidado_pago_id' => $this->pago_cuenta_id,
            ]);
        }

        return Money::clampZero(Money::sub($objetivo, $anulado));
    }

    /**
     * Anula cargos vivos (no liquidados) hasta cubrir `objetivo`, de la línea
     * más reciente a la más antigua: líneas completas mientras alcancen y una
     * fracción de línea al final (cantidad truncada a 2 decimales para nunca
     * exceder el saldo). Cada evento queda marcado con el id de la NC para que
     * anular la NC revierta exactamente estas anulaciones.
     *
     * Best-effort deliberado: si un cargo no se puede anular (p. ej. cobertura
     * de seguro re-sincronizada a medio camino), se corta y el resto queda como
     * residuo/saldo en lugar de abortar la devolución completa.
     *
     * @return string  Total efectivamente anulado.
     */
    private function anularCargosPorDevolucion(CuentaCobro $cuenta, string $objetivo): string
    {
        $anulado = '0.00';
        if (Money::cmp($objetivo, '0') <= 0) {
            return $anulado;
        }

        $motivo = 'Devolución '.$this->id.': '.$this->motivo;

        $detalles = $cuenta->detalles()
            ->whereNull('liquidado_en')
            ->orderByDesc('id')
            ->get();

        foreach ($detalles as $detalle) {
            $restante = Money::sub($objetivo, $anulado);
            if (Money::cmp($restante, '0') <= 0) {
                break;
            }

            $subtotalLinea = Money::mul($detalle->cantidad, $detalle->precio_unitario);
            if (Money::cmp($subtotalLinea, '0') <= 0) {
                continue;
            }

            if (Money::cmp($subtotalLinea, $restante) <= 0) {
                $cant = (string) $detalle->cantidad; // línea completa
            } else {
                // Fracción: bcdiv escala 2 TRUNCA, garantiza subtotal ≤ restante.
                $cant = bcdiv($restante, (string) $detalle->precio_unitario, 2);
                if (Money::cmp($cant, '0') <= 0) {
                    continue;
                }
            }

            try {
                $evento = $detalle->anular($cant, $motivo, $this->user_id, $this->id);
            } catch (\RuntimeException) {
                break; // best-effort: el resto queda como residuo/saldo
            }

            $anulado = Money::add($anulado, $evento->subtotal);
        }

        return $anulado;
    }

    /**
     * Restaura los cargos que ESTA devolución anuló (y solo esos): revierte los
     * eventos de anulación vigentes marcados con su id. Se usa al anular la NC.
     */
    private function revertirCargosAnulados(int $userId): void
    {
        $eventos = CuentaCobroDetalleEliminado::with('detalle')
            ->where('devolucion_id', $this->id)
            ->vigentes()
            ->get();

        foreach ($eventos as $evento) {
            $evento->detalle?->revertirAnulacion($evento, $userId);
        }
    }

    // Correlativo + débito fiscal
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($devolucion) {
            if (empty($devolucion->id)) {
                $devolucion->id = static::generarNumero();
            }

            // Reversa del débito fiscal IVA: la NC descuenta 13% por dentro del
            // monto devuelto, espejo exacto del débito que generó el cobro.
            if (empty($devolucion->base_imponible) || (float) $devolucion->base_imponible === 0.0) {
                $devolucion->base_imponible = $devolucion->monto;
                $devolucion->debito_fiscal = \App\Support\Impuestos::iva((string) $devolucion->monto);
            }
        });
    }

    /**
     * Siguiente número de Nota de Crédito: NC-AAAA-NNNNNN (p. ej. NC-2026-000004).
     *
     * Correlativo incremental por gestión fiscal (año), sin saltos, mismo patrón
     * que CuentaCobro::generarNumero (fuente única + bucle anti-colisión; ante
     * colisión concurrente real el índice único del PK protege la integridad).
     */
    public static function generarNumero(): string
    {
        $prefijo = 'NC-' . now()->format('Y') . '-';

        do {
            $ultimo = static::where('id', 'REGEXP', '^NC-[0-9]{4}-[0-9]{6}$')
                ->where('id', 'like', $prefijo . '%')
                ->max(DB::raw("CAST(SUBSTRING_INDEX(id, '-', -1) AS UNSIGNED)")) ?? 0;

            $numero = $prefijo . str_pad((int) $ultimo + 1, 6, '0', STR_PAD_LEFT);
        } while (static::whereKey($numero)->exists());

        return $numero;
    }
}
