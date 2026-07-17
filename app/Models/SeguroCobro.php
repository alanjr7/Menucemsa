<?php

namespace App\Models;

use App\Support\Impuestos;
use App\Support\Money;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Cuenta por cobrar a una aseguradora (porción de una cuenta cubierta por seguro).
 *
 * Es a la vez:
 *   - una VENTA devengada con débito fiscal IVA (se declara en el RCV / Libro de Ventas),
 *   - una CUENTA POR COBRAR (la aseguradora debe ese monto hasta que se liquida).
 *
 * No es caja: el copago del paciente vive en {@see PagoCuenta}. Este modelo es lo que
 * paga el seguro. Fuente única de creación: {@see CuentaCobro::autorizarSeguro()}.
 */
class SeguroCobro extends Model
{
    use HasFactory;

    protected $table = 'seguro_cobros';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'cuenta_cobro_id',
        'seguro_id',
        'monto',
        'base_imponible',
        'debito_fiscal',
        'estado',
        'autorizado_por',
        'observaciones',
        'liquidado_en',
        'liquidado_por',
        'liquidado_referencia',
        'anulado_en',
        'anulado_por',
        'anulado_motivo',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'base_imponible' => 'decimal:2',
        'debito_fiscal' => 'decimal:2',
        'liquidado_en' => 'datetime',
        'anulado_en' => 'datetime',
    ];

    // Relaciones
    public function cuentaCobro(): BelongsTo
    {
        return $this->belongsTo(CuentaCobro::class);
    }

    public function seguro(): BelongsTo
    {
        return $this->belongsTo(Seguro::class);
    }

    public function autorizadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'autorizado_por');
    }

    public function liquidadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'liquidado_por');
    }

    // Scopes
    public function scopePendiente($query)
    {
        return $query->where('estado', 'pendiente');
    }

    public function scopeCobrado($query)
    {
        return $query->where('estado', 'cobrado');
    }

    /** Registros vigentes (no anulados): los que cuentan para venta/débito fiscal. */
    public function scopeVigentes($query)
    {
        return $query->where('estado', '!=', 'anulado');
    }

    /**
     * Cobertura ya consumida por un paciente bajo un seguro en un período (año/gestión).
     * Es la base para aplicar el tope como límite AGREGADO: el tope se reparte entre todas
     * las cuentas del paciente en el período, no se reinicia por cuenta.
     */
    public static function consumoPaciente(int $pacienteId, int $seguroId, int $anio, ?string $exceptoCuentaId = null): string
    {
        $total = static::vigentes()
            ->where('seguro_id', $seguroId)
            ->whereYear('created_at', $anio)
            ->when($exceptoCuentaId !== null, fn ($q) => $q->where('cuenta_cobro_id', '!=', $exceptoCuentaId))
            ->whereHas('cuentaCobro', fn ($q) => $q->where('paciente_id', $pacienteId))
            ->sum('monto');

        return Money::format($total);
    }

    // Estado
    public function estaPendiente(): bool
    {
        return $this->estado === 'pendiente';
    }

    public function estaCobrado(): bool
    {
        return $this->estado === 'cobrado';
    }

    public function estaAnulado(): bool
    {
        return $this->estado === 'anulado';
    }

    public function getEstadoLabelAttribute(): string
    {
        return match ($this->estado) {
            'pendiente' => 'Por cobrar',
            'cobrado' => 'Cobrado',
            'anulado' => 'Anulado',
            default => ucfirst((string) $this->estado),
        };
    }

    /**
     * Datos fiscales del receptor de esta venta = la ASEGURADORA (es a quien se factura
     * la porción cubierta). Usa el NIT de la aseguradora; si aún no está cargado, emite
     * con documento '0' (sin nombre) para no bloquear el RCV.
     */
    public function receptorFiscal(): array
    {
        $this->loadMissing('seguro');

        return [
            'razon_social' => $this->seguro?->nombre_empresa ?? 'Aseguradora',
            'tipo_documento_label' => 'NIT',
            'numero_documento' => $this->seguro?->nit ?: '0',
            'complemento' => null,
        ];
    }

    /**
     * Crea (idempotente) la venta devengada a la aseguradora para una cuenta.
     * Si ya existe un registro vigente para esa cuenta, no duplica.
     */
    public static function registrarPara(CuentaCobro $cuenta, Seguro $seguro, string|float $monto, ?int $usuarioId = null): ?self
    {
        if (Money::cmp($monto, '0') <= 0) {
            return null;
        }

        $existente = static::where('cuenta_cobro_id', $cuenta->id)->vigentes()->first();
        if ($existente) {
            return $existente;
        }

        return static::create([
            'cuenta_cobro_id' => $cuenta->id,
            'seguro_id' => $seguro->id,
            'monto' => Money::round($monto),
            'estado' => 'pendiente',
            'autorizado_por' => $usuarioId ?? auth()->id(),
        ]);
    }

    /**
     * Sincroniza el monto de la venta con el total cubierto en vivo (autorización abierta).
     * Sólo mientras está PENDIENTE: tras liquidar (o anular) queda congelada y no se toca.
     * Recalcula base imponible y débito fiscal IVA en consecuencia.
     */
    public function actualizarMonto(string|float $monto): void
    {
        if (! $this->estaPendiente()) {
            return;
        }

        $nuevo = Money::round($monto);
        if (Money::cmp($nuevo, (string) $this->monto) === 0) {
            return; // sin cambios
        }

        $this->monto = $nuevo;
        $this->base_imponible = $nuevo;
        $this->debito_fiscal = Impuestos::iva((string) $nuevo);
        $this->save();
    }

    /** Marca la venta como cobrada a la aseguradora (liquidación). Lo usa el Sprint 2. */
    public function liquidar(?int $usuarioId = null, ?string $referencia = null): void
    {
        if (! $this->estaPendiente()) {
            return;
        }
        $this->update([
            'estado' => 'cobrado',
            'liquidado_en' => now(),
            'liquidado_por' => $usuarioId ?? auth()->id(),
            'liquidado_referencia' => $referencia,
        ]);
    }

    /** Anula la venta (reversible + auditada): la cobertura deja de devengarse/cobrarse. */
    public function anular(?int $usuarioId = null, ?string $motivo = null): void
    {
        if ($this->estaAnulado()) {
            return;
        }
        $this->update([
            'estado' => 'anulado',
            'anulado_en' => now(),
            'anulado_por' => $usuarioId ?? auth()->id(),
            'anulado_motivo' => $motivo,
        ]);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($cobro) {
            if (empty($cobro->id)) {
                $cobro->id = static::generarNumero();
            }

            // Débito fiscal IVA (Libro de Ventas): la cobertura es una venta con IVA por
            // dentro. base imponible = monto; débito fiscal = monto * 13%. Mismo criterio
            // que PagoCuenta y VentaFarmacia (fuente única Impuestos::iva).
            if (empty($cobro->base_imponible) || (float) $cobro->base_imponible === 0.0) {
                $cobro->base_imponible = $cobro->monto;
                $cobro->debito_fiscal = Impuestos::iva((string) $cobro->monto);
            }
        });
    }

    /**
     * Siguiente número: SEG-AAAA-NNNNNN (correlativo anual sin saltos). Mismo patrón
     * que CuentaCobro::generarNumero (fuente única + bucle anti-colisión; el índice
     * único del PK protege ante colisión concurrente).
     */
    public static function generarNumero(): string
    {
        $prefijo = 'SEG-' . now()->format('Y') . '-';

        do {
            $ultimo = static::where('id', 'REGEXP', '^SEG-[0-9]{4}-[0-9]{6}$')
                ->where('id', 'like', $prefijo . '%')
                ->max(\DB::raw("CAST(SUBSTRING_INDEX(id, '-', -1) AS UNSIGNED)")) ?? 0;

            $numero = $prefijo . str_pad((int) $ultimo + 1, 6, '0', STR_PAD_LEFT);
        } while (static::whereKey($numero)->exists());

        return $numero;
    }
}
