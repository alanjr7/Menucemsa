<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UtiAdmission extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'uti_admissions';

    protected $fillable = [
        'patient_id',
        'bed_id',
        'nro_ingreso',
        'emergency_id',
        'quirofano_id',
        'hospitalization_id',
        'estado_clinico',
        'diagnostico_principal',
        'diagnostico_secundario',
        'tipo_ingreso',
        'tipo_pago',
        'seguro_id',
        'nro_autorizacion',
        'fecha_ingreso',
        'fecha_alta_clinica',
        'fecha_alta_administrativa',
        'estado',
        'destino_alta',
        'medico_responsable_ci',
        'observaciones',
    ];

    protected $casts = [
        'fecha_ingreso' => 'datetime',
        'fecha_alta_clinica' => 'datetime',
        'fecha_alta_administrativa' => 'datetime',
    ];

    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'patient_id', 'ci');
    }

    public function bed()
    {
        return $this->belongsTo(UtiBed::class, 'bed_id');
    }

    public function seguro()
    {
        return $this->belongsTo(Seguro::class, 'seguro_id');
    }

    public function medico()
    {
        return $this->belongsTo(Medico::class, 'medico_responsable_ci', 'ci');
    }

    public function vitalSigns()
    {
        return $this->hasMany(UtiVitalSign::class, 'uti_admission_id');
    }

    public function dailyRecords()
    {
        return $this->hasMany(UtiDailyRecord::class, 'uti_admission_id');
    }

    public function medications()
    {
        return $this->hasMany(UtiMedication::class, 'uti_admission_id');
    }

    public function supplies()
    {
        return $this->hasMany(UtiSupply::class, 'uti_admission_id');
    }

    public function catering()
    {
        return $this->hasMany(UtiCatering::class, 'uti_admission_id');
    }

    public function recipes()
    {
        return $this->hasMany(UtiRecipe::class, 'uti_admission_id');
    }

    public function getDiasEnUtiAttribute()
    {
        $fechaFin = $this->fecha_alta_clinica ?? now();
        return $this->fecha_ingreso->diffInDays($fechaFin) + 1;
    }

    public function getEstadoClinicoColorAttribute()
    {
        return match($this->estado_clinico) {
            'estable' => 'green',
            'critico' => 'yellow',
            'muy_critico' => 'red',
            default => 'gray',
        };
    }

    public function getTiempoEnUtiTextoAttribute()
    {
        $dias = $this->dias_en_uti;
        if ($dias == 1) return '1 día';
        return "$dias días";
    }

    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }

    public function scopeConCama($query)
    {
        return $query->whereNotNull('bed_id');
    }
}
