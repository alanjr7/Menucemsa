<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paciente extends Model
{
    use HasFactory;

    protected $table = 'pacientes';
    protected $primaryKey = 'ci';
    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = [
        'ci',
        'nombre',
        'sexo',
        'fecha_nacimiento',
        'direccion',
        'telefono',
        'correo',
        'seguro_id',
        'triage_id',
        'registro_codigo',
    ];

    protected $casts = [
        'ci' => 'integer',
        'telefono' => 'string',
        'fecha_nacimiento' => 'date',
        'sexo' => 'string',
    ];

    public function seguro()
    {
        return $this->belongsTo(Seguro::class, 'seguro_id');
    }

    public function triage()
    {
        return $this->belongsTo(Triage::class, 'triage_id');
    }

    public function registro()
    {
        return $this->belongsTo(Registro::class, 'registro_codigo', 'codigo');
    }

    public function consultas()
    {
        return $this->hasMany(Consulta::class, 'ci_paciente', 'ci');
    }

    public function historialMedico()
    {
        return $this->hasOne(HistorialMedico::class, 'ci_paciente', 'ci');
    }

    public function emergencies()
    {
        return $this->hasMany(Emergency::class, 'patient_id', 'ci');
    }

    public function hospitalizaciones()
    {
        return $this->hasMany(Hospitalizacion::class, 'ci_paciente', 'ci');
    }
}
