<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AlmacenLote extends Model
{
    protected $table = 'almacen_lotes';

    protected $fillable = [
        'catalogo_id', 'codigo_lote', 'fecha_vencimiento',
        'precio_compra', 'porcentaje_ganancia', 'precio_venta', 'cantidad_inicial',
    ];

    protected $casts = [
        'fecha_vencimiento' => 'date',
        'precio_compra'     => 'decimal:2',
        'porcentaje_ganancia' => 'decimal:2',
        'precio_venta'      => 'decimal:2',
        'cantidad_inicial'  => 'integer',
    ];

    public function catalogo(): BelongsTo
    {
        return $this->belongsTo(AlmacenCatalogo::class, 'catalogo_id');
    }

    public function stocks(): HasMany
    {
        return $this->hasMany(AlmacenStock::class, 'lote_id');
    }

    public function dispensacionDetalles(): HasMany
    {
        return $this->hasMany(AlmacenDispensacionDetalle::class, 'lote_id');
    }

    public function scopeVencidos($query)
    {
        return $query->whereNotNull('fecha_vencimiento')->where('fecha_vencimiento', '<', now()->toDateString());
    }

    public function scopePorVencer($query, int $dias = 30)
    {
        return $query->whereNotNull('fecha_vencimiento')
            ->whereBetween('fecha_vencimiento', [now()->toDateString(), now()->addDays($dias)->toDateString()]);
    }

    public function scopeVigentes($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('fecha_vencimiento')
              ->orWhere('fecha_vencimiento', '>', now()->addDays(30)->toDateString());
        });
    }

    public function getStockCentralAttribute(): int
    {
        return $this->stocks()->where('ubicacion', 'central')->value('cantidad_actual') ?? 0;
    }

    public function getEstadoVencimientoAttribute(): string
    {
        if (!$this->fecha_vencimiento) {
            return 'sin_fecha';
        }

        if ($this->fecha_vencimiento->isPast()) {
            return 'vencido';
        }

        if ($this->fecha_vencimiento->diffInDays(now()) <= 30) {
            return 'por_vencer';
        }

        return 'vigente';
    }

    public function getDiasParaVencerAttribute(): ?int
    {
        if (!$this->fecha_vencimiento) {
            return null;
        }

        return (int) now()->diffInDays($this->fecha_vencimiento, false);
    }
}
