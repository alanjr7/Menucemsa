<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AlmacenEntregaPaciente extends Model
{
    protected $table = 'almacen_entregas_paciente';

    protected $fillable = [
        'paciente_id',
        'entregado_por',
        'origen',
        'referencia_id',
        'catalogo_id',
        'cantidad',
        'dispensacion_detalle_id',
        'observaciones',
        'fecha_entrega',
    ];

    protected $casts = [
        'fecha_entrega' => 'datetime',
    ];

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class, 'paciente_id');
    }

    public function entregadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'entregado_por');
    }

    public function catalogo(): BelongsTo
    {
        return $this->belongsTo(AlmacenCatalogo::class, 'catalogo_id');
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(AlmacenEntregaDetalle::class, 'entrega_id');
    }
}
