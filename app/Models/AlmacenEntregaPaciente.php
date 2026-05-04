<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AlmacenEntregaPaciente extends Model
{
    protected $table = 'almacen_entregas_paciente';

    protected $fillable = [
        'paciente_ci', 'entregado_por', 'observaciones', 'fecha_entrega',
    ];

    protected $casts = [
        'paciente_ci'   => 'integer',
        'fecha_entrega' => 'datetime',
    ];

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class, 'paciente_ci', 'ci');
    }

    public function entregadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'entregado_por');
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(AlmacenEntregaDetalle::class, 'entrega_id');
    }
}
