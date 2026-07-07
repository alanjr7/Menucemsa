<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quirofano extends Model
{
    use HasFactory;

    protected $table = 'quirofanos';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'tipo',
        'estado',
        'restriccion_externa',
    ];

    protected $casts = [
        'restriccion_externa' => 'array',
    ];

    public function citasQuirurgicas()
    {
        return $this->hasMany(CitaQuirurgica::class, 'quirofano_id');
    }

    public function cirugiasExternas()
    {
        return $this->hasMany(CirugiaExterna::class, 'quirofano_id');
    }

    /**
     * ¿Este quirófano acepta el tipo externo indicado? NULL en
     * `restriccion_externa` = acepta todos. Fuente única de la regla.
     */
    public function aceptaTipoExterno(string $clave): bool
    {
        return empty($this->restriccion_externa) || in_array($clave, $this->restriccion_externa, true);
    }

    /**
     * Etiqueta legible del quirófano. Fuente ÚNICA del formato "Q{id}" que antes
     * se construía a mano en cada vista. La tabla no tiene columna de nombre: el
     * identificador visible se deriva del id.
     */
    public function getNombreAttribute(): string
    {
        return 'Q'.$this->id;
    }
}
