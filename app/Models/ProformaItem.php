<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProformaItem extends Model
{
    protected $fillable = [
        'proforma_id',
        'codigo_item',
        'tipo_item',
        'descripcion',
        'cantidad',
        'precio_unitario',
        'subtotal',
        'orden',
    ];

    protected $casts = [
        'cantidad'        => 'decimal:2',
        'precio_unitario' => 'decimal:2',
        'subtotal'        => 'decimal:2',
        'orden'           => 'integer',
    ];

    /** Bienes vs servicios, para la columna UNIDAD DE MEDIDA del documento impreso. */
    public const TIPOS_BIENES = ['medicamento', 'material', 'equipo_medico', 'farmacia'];

    public function proforma(): BelongsTo
    {
        return $this->belongsTo(Proforma::class);
    }

    public function esBien(): bool
    {
        return in_array($this->tipo_item, self::TIPOS_BIENES, true);
    }
}
