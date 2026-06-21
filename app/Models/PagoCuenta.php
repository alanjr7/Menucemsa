<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PagoCuenta extends Model
{
    use HasFactory;

    protected $table = 'pago_cuentas';
    
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    
    protected $fillable = [
        'cuenta_cobro_id',
        'monto',
        'base_imponible',
        'debito_fiscal',
        'metodo_pago',
        'referencia',
        'user_id',
        'caja_session_id',
        'observaciones',
        'idempotency_key',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'base_imponible' => 'decimal:2',
        'debito_fiscal' => 'decimal:2',
    ];

    // Relaciones
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

    // Scopes
    public function scopePorMetodo($query, $metodo)
    {
        return $query->where('metodo_pago', $metodo);
    }

    public function scopeDelDia($query, $fecha = null)
    {
        $fecha = $fecha ?? now()->toDateString();
        return $query->whereDate('created_at', $fecha);
    }

    public function scopeEfectivo($query)
    {
        return $query->where('metodo_pago', 'efectivo');
    }

    public function scopeTransferencia($query)
    {
        return $query->where('metodo_pago', 'transferencia');
    }

    public function scopeTarjeta($query)
    {
        return $query->where('metodo_pago', 'tarjeta');
    }

    public function scopeQr($query)
    {
        return $query->where('metodo_pago', 'qr');
    }

    /**
     * Filtros del historial de pagos (fuente única, compartida por el listado JSON y
     * el export Excel). Búsqueda libre por nº de recibo (PAGO-), nº de cuenta (REC-),
     * referencia o paciente (nombre/CI). La fecha es OPCIONAL: sin rango devuelve TODOS
     * los pagos, sin importar la caja en que se cobraron.
     *
     * @param  array<string,mixed>  $f
     */
    public function scopeFiltrarHistorial($query, array $f)
    {
        $q = trim((string) ($f['q'] ?? ''));
        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('id', 'like', "%{$q}%")
                    ->orWhere('cuenta_cobro_id', 'like', "%{$q}%")
                    ->orWhere('referencia', 'like', "%{$q}%")
                    ->orWhereHas('cuentaCobro.paciente', function ($p) use ($q) {
                        $p->where('nombre', 'like', "%{$q}%")
                            ->orWhere('ci', 'like', "%{$q}%")
                            ->orWhere('temp_code', 'like', "%{$q}%");
                    });
            });
        }

        if (!empty($f['fecha_inicio']) && !empty($f['fecha_fin'])) {
            $query->whereBetween('created_at', [
                \Carbon\Carbon::parse($f['fecha_inicio'])->startOfDay(),
                \Carbon\Carbon::parse($f['fecha_fin'])->endOfDay(),
            ]);
        } elseif (!empty($f['fecha_inicio'])) {
            $query->whereDate('created_at', $f['fecha_inicio']);
        }

        if (!empty($f['metodo_pago']) && $f['metodo_pago'] !== 'todos') {
            $query->where('metodo_pago', $f['metodo_pago']);
        }

        return $query;
    }

    // Getters
    public function getMetodoPagoLabelAttribute(): string
    {
        return match($this->metodo_pago) {
            'efectivo' => 'Efectivo',
            'transferencia' => 'Transferencia',
            'tarjeta' => 'Tarjeta',
            'qr' => 'QR',
            default => ucfirst($this->metodo_pago),
        };
    }

    public function getMetodoPagoIconAttribute(): string
    {
        return match($this->metodo_pago) {
            'efectivo' => 'banknote',
            'transferencia' => 'arrow-left-right',
            'tarjeta' => 'credit-card',
            'qr' => 'qr-code',
            default => 'circle-dollar-sign',
        };
    }

    public function getMontoFormateadoAttribute(): string
    {
        return 'Bs ' . number_format($this->monto, 2);
    }

    // Generar recibo correlativo
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($pago) {
            if (empty($pago->id)) {
                $pago->id = static::generarNumero();
            }

            // Débito fiscal IVA (Libro de Ventas): el monto cobrado incluye IVA 13% (por
            // dentro). base imponible = monto; débito fiscal = monto × 13%. Aditivo.
            if (empty($pago->base_imponible) || (float) $pago->base_imponible === 0.0) {
                $pago->base_imponible = $pago->monto;
                $pago->debito_fiscal = \App\Support\Impuestos::iva((string) $pago->monto);
            }
        });
    }

    /**
     * Siguiente número de recibo: PAGO-AAAAMMDD-NNN (p. ej. PAGO-20260621-003).
     *
     * Correlativo incremental que reinicia cada día (la fecha va en el código).
     * Reemplaza al antiguo PAGO-{YmdHis}-{random}, ilegible y no secuencial; un
     * recibo numerado y sin saltos permite ubicarlos y auditar faltantes. Mismo
     * patrón que CuentaCobro::generarNumero (fuente única + bucle anti-colisión;
     * ante colisión concurrente real el índice único del PK protege la integridad).
     */
    public static function generarNumero(): string
    {
        $prefijo = 'PAGO-' . now()->format('Ymd') . '-';

        do {
            $ultimo = static::where('id', 'REGEXP', '^PAGO-[0-9]{8}-[0-9]+$')
                ->where('id', 'like', $prefijo . '%')
                ->max(\DB::raw("CAST(SUBSTRING_INDEX(id, '-', -1) AS UNSIGNED)")) ?? 0;

            $numero = $prefijo . str_pad((int) $ultimo + 1, 3, '0', STR_PAD_LEFT);
        } while (static::whereKey($numero)->exists());

        return $numero;
    }
}
