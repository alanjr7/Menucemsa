<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Tipo/tarifa de "alquiler de quirófano" para cirugías externas. Catálogo
 * editable por el admin (panel "Precios Externos"), independiente del tarifario
 * clínico interno {@see TipoCirugia}.
 */
class TipoCirugiaExterna extends Model
{
    use HasFactory;

    protected $table = 'tipos_cirugia_externa';

    protected $fillable = [
        'clave',
        'nombre',
        'descripcion',
        'precio',
        'duracion_minutos',
        'incluye',
        'no_incluye',
        'activo',
    ];

    protected $casts = [
        'precio' => 'decimal:2',
        'duracion_minutos' => 'integer',
        'incluye' => 'array',
        'no_incluye' => 'array',
        'activo' => 'boolean',
    ];

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function getDuracionFormateadaAttribute(): string
    {
        $horas = intdiv($this->duracion_minutos, 60);
        $minutos = $this->duracion_minutos % 60;

        return $horas > 0 ? "{$horas}h {$minutos}min" : "{$minutos}min";
    }
}
