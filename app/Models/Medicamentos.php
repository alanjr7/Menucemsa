<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medicamentos extends Model
{
    use HasFactory;

    protected $table = 'MEDICAMENTOS';
    protected $primaryKey = 'CODIGO';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'CODIGO',
        'DESCRIPCION',
        'PRECIO'
    ];

    protected $hidden = [
        'detalleMedicamentos',
        'detalleRecetas'
    ];

    public $timestamps = false;

    public function detalleMedicamentos()
    {
        return $this->hasMany(DetalleMedicamentos::class, 'CODIGO_MEDICAMENTOS', 'CODIGO');
    }

    public function detalleRecetas()
    {
        return $this->hasMany(DetalleReceta::class, 'CODIGO_MEDICAMENTOS', 'CODIGO');
    }
}
