<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AlmacenDispensacionDetalle extends Model
{
    protected $table = 'almacen_dispensacion_detalles';

    protected $fillable = [
        'dispensacion_id', 'lote_id', 'cantidad',
    ];

    protected $casts = [
        'cantidad' => 'integer',
    ];

    public function dispensacion(): BelongsTo
    {
        return $this->belongsTo(AlmacenDispensacion::class, 'dispensacion_id');
    }

    public function lote(): BelongsTo
    {
        return $this->belongsTo(AlmacenLote::class, 'lote_id');
    }

    public function entregaDetalles(): HasMany
    {
        return $this->hasMany(AlmacenEntregaDetalle::class, 'dispensacion_detalle_id');
    }

    public function getCantidadEntregadaAttribute(): int
    {
        return $this->entregaDetalles()->sum('cantidad');
    }

    public function getCantidadPendienteAttribute(): int
    {
        return max(0, $this->cantidad - $this->getCantidadEntregadaAttribute());
    }
}
