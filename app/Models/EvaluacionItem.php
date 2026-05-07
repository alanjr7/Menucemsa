<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EvaluacionItem extends Model
{
    protected $fillable = ['evaluacion_id', 'tipo', 'item_id', 'nombre_snapshot', 'cantidad', 'precio_snapshot'];

    protected $casts = ['precio_snapshot' => 'decimal:2'];
}
