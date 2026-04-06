<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medico extends Model
{
    use HasFactory;

    protected $table = 'medicos';
    protected $primaryKey = 'ci';
    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = [
        'ci',
        'user_id',
        'telefono',
        'estado',
        'asistente_id',
        'codigo_especialidad',
    ];

    protected $casts = [
        'ci' => 'integer',
        'user_id' => 'integer',
        'telefono' => 'string',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function asistenteQuirofano()
    {
        return $this->belongsTo(AsistenteQuirofanos::class, 'asistente_id');
    }

    public function especialidad()
    {
        return $this->belongsTo(Especialidad::class, 'codigo_especialidad', 'codigo');
    }

    public function consultas()
    {
        return $this->hasMany(Consulta::class, 'ci_medico', 'ci');
    }

    public function citas()
    {
        return $this->hasMany(Cita::class, 'ci_medico', 'ci');
    }

    public function getNombreCompletoAttribute()
    {
        return $this->user ? $this->user->name : 'N/A';
    }

    public function getEstadoColorAttribute()
    {
        return match($this->estado) {
            'activo' => 'green',
            'inactivo' => 'red',
            'vacaciones' => 'yellow',
            default => 'gray'
        };
    }
}
