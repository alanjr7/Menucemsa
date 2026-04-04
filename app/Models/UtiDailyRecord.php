<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UtiDailyRecord extends Model
{
    use HasFactory;

    protected $table = 'uti_daily_records';

    protected $fillable = [
        'uti_admission_id',
        'fecha',
        'medico_id',
        'evolucion_medica',
        'indicaciones',
        'plan_tratamiento',
        'ronda_completada',
        'hora_ronda',
        'dia_validado',
        'hora_validacion',
        'observaciones',
    ];

    protected $casts = [
        'fecha' => 'date',
        'ronda_completada' => 'boolean',
        'dia_validado' => 'boolean',
        'hora_ronda' => 'datetime',
        'hora_validacion' => 'datetime',
    ];

    public function admission()
    {
        return $this->belongsTo(UtiAdmission::class, 'uti_admission_id');
    }

    public function medico()
    {
        return $this->belongsTo(Medico::class, 'medico_id');
    }

    public function getEstadoDiaAttribute()
    {
        if ($this->dia_validado) {
            return 'validado';
        }
        if ($this->ronda_completada) {
            return 'ronda_completada';
        }
        return 'incompleto';
    }

    public function getEstadoDiaColorAttribute()
    {
        return match($this->estado_dia) {
            'validado' => 'green',
            'ronda_completada' => 'yellow',
            'incompleto' => 'red',
            default => 'gray',
        };
    }

    public function getEstadoDiaLabelAttribute()
    {
        return match($this->estado_dia) {
            'validado' => 'Día Validado',
            'ronda_completada' => 'Ronda Completada',
            'incompleto' => 'Día Incompleto',
            default => 'Desconocido',
        };
    }
}
