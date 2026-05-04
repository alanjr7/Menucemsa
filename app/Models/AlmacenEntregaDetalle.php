<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AlmacenEntregaDetalle extends Model
{
    protected $table = 'almacen_entrega_detalles';

    protected $fillable = [
        'entrega_id', 'dispensacion_detalle_id', 'cantidad',
    ];

    protected $casts = [
        'cantidad' => 'integer',
    ];

    public function entrega(): BelongsTo
    {
        return $this->belongsTo(AlmacenEntregaPaciente::class, 'entrega_id');
    }

    public function dispensacionDetalle(): BelongsTo
    {
        return $this->belongsTo(AlmacenDispensacionDetalle::class, 'dispensacion_detalle_id');
    }
}
