<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class AlmacenCatalogo extends Model
{
    protected $table = 'almacen_catalogo';

    protected $fillable = [
        'nombre', 'descripcion', 'unidad_medida', 'tipo', 'activo', 'observaciones',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function lotes(): HasMany
    {
        return $this->hasMany(AlmacenLote::class, 'catalogo_id');
    }

    public function stocks(): HasManyThrough
    {
        return $this->hasManyThrough(AlmacenStock::class, AlmacenLote::class, 'catalogo_id', 'lote_id');
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopeMedicamentos($query)
    {
        return $query->where('tipo', 'medicamento');
    }

    public function scopeInsumos($query)
    {
        return $query->where('tipo', 'insumo');
    }

    public function getStockTotalCentralAttribute(): int
    {
        return $this->stocks()->where('ubicacion', 'central')->sum('cantidad_actual');
    }

    public function getEstadoStockAttribute(): string
    {
        $stock = $this->stocks()->where('ubicacion', 'central')->first();

        if (!$stock || $stock->cantidad_actual <= 0) {
            return 'agotado';
        }

        if ($stock->cantidad_actual <= $stock->stock_minimo) {
            return 'bajo';
        }

        return 'normal';
    }

    public function getTipoLabelAttribute(): string
    {
        return ucfirst($this->tipo);
    }
}
