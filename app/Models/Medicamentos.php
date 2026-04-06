<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medicamentos extends Model
{
    use HasFactory;

    protected $table = 'medicamentos';
    protected $primaryKey = 'codigo';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'codigo',
        'descripcion',
        'precio'
    ];

    public function detalleRecetas()
    {
        return $this->hasMany(DetalleReceta::class, 'codigo_medicamento', 'codigo');
    }
}
