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
    ];

    public function citasQuirurgicas()
    {
        return $this->hasMany(CitaQuirurgica::class, 'quirofano_id');
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
