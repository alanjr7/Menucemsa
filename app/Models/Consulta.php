<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consulta extends Model
{
    use HasFactory;

    protected $table = 'consultas';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'codigo',
        'fecha',
        'hora',
        'motivo',
        'observaciones',
        'codigo_especialidad',
        'ci_paciente',
        'ci_medico',
        'estado_pago',
        'caja_id',
        'estado',
    ];

    protected $casts = [
        'fecha' => 'date',
        'estado_pago' => 'boolean',
    ];

    public function especialidad()
    {
        return $this->belongsTo(Especialidad::class, 'codigo_especialidad', 'codigo');
    }

    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'ci_paciente', 'ci');
    }

    public function medico()
    {
        return $this->belongsTo(Medico::class, 'ci_medico', 'ci');
    }

    public function caja()
    {
        return $this->belongsTo(Caja::class, 'caja_id');
    }

    public function recetas()
    {
        return $this->hasMany(Receta::class, 'consulta_id');
    }
}
