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
    protected $keyType = 'integer';

    protected $fillable = [
        'ci',
        'nombre',
        'sexo',
        'direccion',
        'telefono',
        'correo',
        'codigo_seguro',
        'id_triage',
        'codigo_registro',
    ];

    protected $casts = [
        'ci' => 'integer',
        'telefono' => 'integer',
        'codigo_seguro' => 'integer',
    ];

    public function seguro()
    {
        return $this->belongsTo(Seguro::class, 'codigo_seguro', 'codigo');
    }

    public function triage()
    {
        return $this->belongsTo(Triage::class, 'id_triage', 'id');
    }

    public function registro()
    {
        return $this->belongsTo(Registro::class, 'codigo_registro', 'codigo');
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
