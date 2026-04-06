<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleReceta extends Model
{
    use HasFactory;

    protected $table = 'detalle_receta';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'receta_id',
        'codigo_medicamento',
        'dosis',
        'subtotal',
    ];

    public function receta()
    {
        return $this->belongsTo(Receta::class, 'receta_id');
    }

    public function medicamento()
    {
        return $this->belongsTo(Medicamentos::class, 'codigo_medicamento', 'codigo');
    }
}
