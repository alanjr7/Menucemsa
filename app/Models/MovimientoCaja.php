<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class MovimientoCaja extends Model
{
    use HasFactory;

    protected $table = 'movimientos_caja';

    protected $fillable = [
        'caja_session_id',
        'tipo',
        'concepto',
        'monto',
        'referencia',
        'metodo_pago',
        'movable_type',
        'movable_id',
        'observaciones',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
    ];

    // Relaciones
    public function cajaSession(): BelongsTo
    {
        return $this->belongsTo(CajaSession::class);
    }

    public function movable(): MorphTo
    {
        return $this->morphTo();
    }

    // Scopes
    public function scopeIngreso($query)
    {
        return $query->where('tipo', 'ingreso');
    }

    public function scopeEgreso($query)
    {
        return $query->where('tipo', 'egreso');
    }

    public function scopeDelDia($query, $date = null)
    {
        $date = $date ?? now()->toDateString();
        return $query->whereDate('created_at', $date);
    }

    // Métodos de formato
    public function getTipoFormateadoAttribute(): string
    {
        return $this->tipo === 'ingreso' ? 'Ingreso' : 'Egreso';
    }

    public function getMontoFormateadoAttribute(): string
    {
        $signo = $this->tipo === 'ingreso' ? '+' : '-';
        return "{$signo}S/ " . number_format($this->monto, 2);
    }

    public function getMontoConSignoAttribute(): float
    {
        return $this->tipo === 'ingreso' ? $this->monto : -$this->monto;
    }
}
