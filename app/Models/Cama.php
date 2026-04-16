<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cama extends Model
{
    use HasFactory;

    protected $table = 'camas';

    protected $fillable = [
        'nro',
        'habitacion_id',
        'disponibilidad',
        'tipo',
        'precio_por_dia',
    ];

    protected $casts = [
        'nro' => 'integer',
        'precio_por_dia' => 'decimal:2',
    ];

    /**
     * Habitación a la que pertenece
     */
    public function habitacion()
    {
        return $this->belongsTo(Habitacion::class, 'habitacion_id', 'id');
    }

    /**
     * Hospitalización activa en esta cama
     */
    public function hospitalizacionActiva()
    {
        return $this->hasOne(Hospitalizacion::class, 'cama_id', 'id')
            ->whereNull('fecha_alta');
    }

    /**
     * Verificar si está disponible
     */
    public function estaDisponible()
    {
        return $this->disponibilidad === 'disponible';
    }

    /**
     * Verificar si está ocupada
     */
    public function estaOcupada()
    {
        return $this->disponibilidad === 'ocupada';
    }
}
