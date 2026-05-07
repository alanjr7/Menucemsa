<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Procedimiento extends Model
{
    protected $fillable = ['nombre', 'descripcion', 'area', 'precio', 'activo'];

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
