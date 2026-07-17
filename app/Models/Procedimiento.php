<?php

namespace App\Models;

use App\Models\Concerns\GeneraCodigoCatalogo;
use App\Support\CodigoProducto;
use Illuminate\Database\Eloquent\Model;

class Procedimiento extends Model
{
    use GeneraCodigoCatalogo;

    /** Familia del código interno (PROCEDIMIENTOS). */
    public const FAMILIA_CODIGO = CodigoProducto::FAMILIA_PROCEDIMIENTO;

    protected $fillable = ['codigo', 'nombre', 'descripcion', 'area', 'precio', 'activo'];

    protected $casts = ['activo' => 'boolean', 'precio' => 'decimal:2'];

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopePorArea($query, $area)
    {
        return $query->where('area', $area);
    }
}
