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
        'metodo_pago',
        'referencia',
        'usuario_id',
        'caja_session_id',
        'observaciones',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
    ];

    // Relaciones
    public function cuentaCobro(): BelongsTo
    {
        return $this->belongsTo(CuentaCobro::class);
    }

    public function usuario(): BelongsTo
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
        return 'S/ ' . number_format($this->monto, 2);
    }

    // Generar código único
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($pago) {
            if (empty($pago->id)) {
                $pago->id = 'PAGO-' . date('YmdHis') . '-' . rand(100, 999);
            }
        });
    }
}
