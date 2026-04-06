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

    public function asistentes()
    {
        return $this->hasMany(AsistenteQuirofanos::class, 'quirofano_id');
    }

    public function citasQuirurgicas()
    {
        return $this->hasMany(CitaQuirurgica::class, 'quirofano_id');
    }
}
