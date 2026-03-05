<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tarifa extends Model
{
    use HasFactory;

    protected $fillable = [
        'codigo',
        'descripcion',
        'categoria',
        'precio_particular',
        'precio_sis',
        'precio_eps',
        'tipo_convenio_sis',
        'tipo_convenio_eps',
        'activo',
    ];

    protected $casts = [
        'precio_particular' => 'decimal:2',
        'precio_sis' => 'decimal:2',
        'precio_eps' => 'decimal:2',
        'activo' => 'boolean',
    ];

    // Scope para filtrar por categoría
    public function scopePorCategoria($query, $categoria)
    {
        return $query->where('categoria', $categoria)->where('activo', true);
    }

    // Scope para buscar por código o descripción
    public function scopeBuscar($query, $termino)
    {
        return $query->where(function($q) use ($termino) {
            $q->where('codigo', 'like', "%{$termino}%")
              ->orWhere('descripcion', 'like', "%{$termino}%");
        });
    }

    // Obtener precio según tipo de seguro
    public function getPrecioPorSeguro($tipoSeguro)
    {
        return match($tipoSeguro) {
            'PARTICULAR' => $this->precio_particular,
            'SIS' => $this->precio_sis,
            'EPS' => $this->precio_eps,
            default => $this->precio_particular,
        };
    }

    // Verificar si tiene convenio
    public function tieneConvenio($tipoSeguro)
    {
        return match($tipoSeguro) {
            'SIS' => $this->tipo_convenio_sis === 'CONVENIO',
            'EPS' => $this->tipo_convenio_eps === 'CONVENIO',
            default => false,
        };
    }
}
