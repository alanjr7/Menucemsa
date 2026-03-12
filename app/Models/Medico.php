<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medico extends Model
{
    use HasFactory;

    protected $table = 'medicos';
    protected $primaryKey = 'id_usuario';
    public $incrementing = true;
    protected $keyType = 'integer';

    protected $fillable = [
        'id_usuario',
        'ci',
        'telefono',
        'estado',
        'id_asistente',
        'codigo_especialidad',
    ];

    protected $casts = [
        'id_usuario' => 'integer',
        'ci' => 'integer',
        'telefono' => 'integer',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id');
    }

    public function asistenteQuirofano()
    {
        return $this->belongsTo(AsistenteQuirofano::class, 'id_asistente', 'id');
    }

    public function especialidad()
    {
        return $this->belongsTo(Especialidad::class, 'codigo_especialidad', 'codigo');
    }

    public function consultas()
    {
        return $this->hasMany(Consulta::class, 'ci_medico', 'ci');
    }

    public function cirugias()
    {
        return $this->hasMany(Cirugia::class, 'ci_medico', 'ci');
    }

    public function getNombreCompletoAttribute()
    {
        return $this->usuario ? $this->usuario->name : 'N/A';
    }

    public function getEstadoColorAttribute()
    {
        return match($this->estado) {
            'Activo' => 'green',
            'Inactivo' => 'red',
            'En Espera' => 'yellow',
            default => 'gray'
        };
    }
}
