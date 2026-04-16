<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Habitacion extends Model
{
    use HasFactory;

    protected $table = 'habitaciones';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'estado',
        'detalle',
        'capacidad',
    ];

    protected $casts = [
        'capacidad' => 'integer',
    ];

    /**
     * Camas de la habitación
     */
    public function camas()
    {
        return $this->hasMany(Cama::class, 'habitacion_id', 'id');
    }

    /**
     * Hospitalizaciones activas en esta habitación
     */
    public function hospitalizacionesActivas()
    {
        return $this->hasMany(Hospitalizacion::class, 'habitacion_id', 'id')
            ->whereNull('fecha_alta');
    }

    /**
     * Contar camas disponibles
     */
    public function camasDisponibles()
    {
        return $this->camas()->where('disponibilidad', 'disponible')->count();
    }

    /**
     * Contar camas ocupadas
     */
    public function camasOcupadas()
    {
        return $this->camas()->where('disponibilidad', 'ocupada')->count();
    }

    /**
     * Verificar si habitación está llena
     */
    public function estaLlena()
    {
        return $this->camasDisponibles() === 0;
    }

    /**
     * Obtener primer cama disponible
     */
    public function primeraCamaDisponible()
    {
        return $this->camas()->where('disponibilidad', 'disponible')->first();
    }
}
