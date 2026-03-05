<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Servicio extends Model
{
    use HasFactory;

    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'precio',
        'tipo',
        'activo',
    ];

    protected $casts = [
        'precio' => 'decimal:2',
        'activo' => 'boolean',
    ];

    public static function getPrecioPorTipo($tipo)
    {
        return self::where('tipo', $tipo)
                   ->where('activo', true)
                   ->first()
                   ?->precio ?? 0;
    }

    public static function getServicioPorTipo($tipo)
    {
        return self::where('tipo', $tipo)
                   ->where('activo', true)
                   ->first();
    }
}
