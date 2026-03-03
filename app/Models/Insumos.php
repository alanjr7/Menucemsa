<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Insumos extends Model
{
    use HasFactory;

    protected $table = 'INSUMOS';
    protected $primaryKey = 'CODIGO';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'CODIGO',
        'NOMBRE',
        'DESCRIPCION',
        'PRECIO'
    ];

    protected $hidden = [
        'detalleInsumos'
    ];

    public $timestamps = false;

    public function detalleInsumos()
    {
        return $this->hasMany(DetalleInsumos::class, 'CODIGO_INSUMOS', 'CODIGO');
    }
}
