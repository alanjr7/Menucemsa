<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UtiVitalSign extends Model
{
    use HasFactory;

    protected $table = 'uti_vital_signs';

    protected $fillable = [
        'uti_admission_id',
        'registered_by',
        'turno',
        'fecha',
        'hora',
        'presion_arterial_sistolica',
        'presion_arterial_diastolica',
        'frecuencia_cardiaca',
        'frecuencia_respiratoria',
        'temperatura',
        'saturacion_o2',
        'glicemia',
        'peso',
        'observaciones',
    ];

    protected $casts = [
        'fecha' => 'date',
        'hora' => 'datetime',
        'presion_arterial_sistolica' => 'decimal:1',
        'presion_arterial_diastolica' => 'decimal:1',
        'frecuencia_cardiaca' => 'decimal:1',
        'frecuencia_respiratoria' => 'decimal:1',
        'temperatura' => 'decimal:1',
        'saturacion_o2' => 'decimal:2',
        'glicemia' => 'decimal:1',
        'peso' => 'decimal:2',
    ];

    public function admission()
    {
        return $this->belongsTo(UtiAdmission::class, 'uti_admission_id');
    }

    public function registeredBy()
    {
        return $this->belongsTo(User::class, 'registered_by');
    }

    public function getPresionArterialAttribute()
    {
        if ($this->presion_arterial_sistolica && $this->presion_arterial_diastolica) {
            return "{$this->presion_arterial_sistolica}/{$this->presion_arterial_diastolica}";
        }
        return null;
    }

    public function getTurnoLabelAttribute()
    {
        return match($this->turno) {
            'manana' => 'Mañana',
            'tarde' => 'Tarde',
            'noche' => 'Noche',
            default => 'Desconocido',
        };
    }
}
