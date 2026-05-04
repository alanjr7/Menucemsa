<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AlmacenStock extends Model
{
    protected $table = 'almacen_stocks';

    protected $fillable = [
        'lote_id', 'ubicacion', 'cantidad_actual', 'stock_minimo',
    ];

    protected $casts = [
        'cantidad_actual' => 'integer',
        'stock_minimo'    => 'integer',
    ];

    public function lote(): BelongsTo
    {
        return $this->belongsTo(AlmacenLote::class, 'lote_id');
    }

    public function scopePorUbicacion($query, string $ubicacion)
    {
        return $query->where('ubicacion', $ubicacion);
    }

    public function scopeBajoStock($query)
    {
        return $query->whereColumn('cantidad_actual', '<=', 'stock_minimo');
    }

    public function scopeAgotado($query)
    {
        return $query->where('cantidad_actual', '<=', 0);
    }

    public function getEstadoStockAttribute(): string
    {
        if ($this->cantidad_actual <= 0) {
            return 'agotado';
        }

        if ($this->cantidad_actual <= $this->stock_minimo) {
            return 'bajo';
        }

        return 'normal';
    }

    public function getUbicacionLabelAttribute(): string
    {
        return match ($this->ubicacion) {
            'central'         => 'Central',
            'emergencia'      => 'Emergencia',
            'cirugia'         => 'Cirugía',
            'hospitalizacion' => 'Hospitalización',
            'uti'             => 'UTI',
            'usi'             => 'USI',
            'neonato'         => 'Neonato',
            'internacion'     => 'Internación',
            default           => ucfirst($this->ubicacion),
        };
    }
}
