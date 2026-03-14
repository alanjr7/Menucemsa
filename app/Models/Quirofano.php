<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quirofano extends Model
{
    use HasFactory;

    protected $table = 'quirofanos';
    
    protected $primaryKey = 'nro';
    
    public $incrementing = false;
    
    protected $keyType = 'int';
    
    protected $fillable = [
        'nro',
        'tipo',
        'estado',
    ];

    public function asistentes()
    {
        return $this->hasMany(AsistenteQuirofanos::class, 'nro_quirofano', 'nro');
    }

    public function citasQuirurgicas()
    {
        return $this->hasMany(CitaQuirurgica::class, 'nro_quirofano', 'nro');
    }
}
