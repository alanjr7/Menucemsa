<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UtiTarifario extends Model
{
    use HasFactory;

    protected $table = 'uti_tarifario';

    protected $fillable = [
        'concepto',
        'tipo',
        'precio',
        'unidad',
        'activo',
        'descripcion',
    ];

    protected $casts = [
        'precio' => 'decimal:2',
        'activo' => 'boolean',
    ];

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    public function getTipoLabelAttribute()
    {
        return match($this->tipo) {
            'estadia' => 'Estadía',
            'alimentacion' => 'Alimentación',
            'procedimiento' => 'Procedimiento',
            'insumo' => 'Insumo',
            'medicamento' => 'Medicamento',
            default => 'Otro',
        };
    }
}
