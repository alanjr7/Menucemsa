<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hospitalizacion extends Model
{
    use HasFactory;

    protected $table = 'hospitalizaciones';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'ci_paciente',
        'ci_medico',
        'habitacion_id',
        'cama_id',
        'fecha_ingreso',
        'fecha_alta',
        'diagnostico',
        'tratamiento',
        'estado',
        'motivo',
        'nro_emergencia',
    ];

    protected $casts = [
        'fecha_ingreso' => 'datetime',
        'fecha_alta' => 'datetime',
        'ci_paciente' => 'integer',
        'ci_medico' => 'integer',
    ];

    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'ci_paciente', 'ci');
    }

    public function medico()
    {
        return $this->belongsTo(Medico::class, 'ci_medico', 'ci');
    }

    public function habitacion()
    {
        return $this->belongsTo(Habitacion::class, 'habitacion_id');
    }

    public function cama()
    {
        return $this->belongsTo(Cama::class, 'cama_id');
    }
}
