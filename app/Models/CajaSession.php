<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CajaSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'fecha_apertura',
        'fecha_cierre',
        'monto_inicial',
        'monto_final',
        'estado',
        'observaciones',
    ];

    protected $casts = [
        'fecha_apertura' => 'datetime',
        'fecha_cierre' => 'datetime',
        'monto_inicial' => 'decimal:2',
        'monto_final' => 'decimal:2',
    ];

    // Relaciones
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function movimientos(): HasMany
    {
        return $this->hasMany(MovimientoCaja::class);
    }

    public function ingresos(): HasMany
    {
        return $this->hasMany(MovimientoCaja::class)->where('tipo', 'ingreso');
    }

    public function egresos(): HasMany
    {
        return $this->hasMany(MovimientoCaja::class)->where('tipo', 'egreso');
    }

    // Scopes
    public function scopeAbierta($query)
    {
        return $query->where('estado', 'abierta');
    }

    public function scopeCerrada($query)
    {
        return $query->where('estado', 'cerrada');
    }

    public function scopeDelUsuario($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Métodos de estado
    public function estaAbierta(): bool
    {
        return $this->estado === 'abierta';
    }

    public function estaCerrada(): bool
    {
        return $this->estado === 'cerrada';
    }

    public function getDuracionAttribute(): string
    {
        if (!$this->fecha_cierre) {
            return $this->fecha_apertura->diffForHumans(now());
        }

        return $this->fecha_apertura->diffForHumans($this->fecha_cierre);
    }
}
