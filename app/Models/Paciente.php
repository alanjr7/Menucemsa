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
        'lugar_expedicion',
        'nacionalidad',
        'estado_civil',
        'direccion',
        'telefono',
        'correo',
        'profesion',
        'empresa_trabajo',
        'seguro_id',
        'triage_id',
        'registro_codigo',
        'id_garante_referencia',
    ];

    protected $casts = [
        'ci' => 'integer',
        'telefono' => 'string',
        'fecha_nacimiento' => 'date',
        'sexo' => 'string',
        'lugar_expedicion' => 'string',
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
        return $this->hasMany(HistorialMedico::class, 'ci_paciente', 'ci')
                    ->orderBy('fecha', 'desc');
    }

    public function historialReciente()
    {
        return $this->hasOne(HistorialMedico::class, 'ci_paciente', 'ci')
                    ->orderBy('fecha', 'desc');
    }

    public function emergencias()
    {
        return $this->hasMany(Emergency::class, 'patient_id', 'ci');
    }

    public function hospitalizaciones()
    {
        return $this->hasMany(Hospitalizacion::class, 'ci_paciente', 'ci');
    }

    public function cuentasCobro()
    {
        return $this->hasMany(\App\Models\CuentaCobro::class, 'paciente_ci', 'ci');
    }

    public function cuentasPendientes()
    {
        return $this->hasMany(\App\Models\CuentaCobro::class, 'paciente_ci', 'ci')
                    ->whereIn('estado', ['pendiente', 'parcial']);
    }

    public function altas()
    {
        return $this->hasMany(\App\Models\AltaPaciente::class, 'paciente_ci', 'ci');
    }

    public function estaDeAlta(): bool
    {
        return $this->altas()->exists();
    }

    public function garante()
    {
        return $this->belongsTo(Paciente::class, 'id_garante_referencia', 'ci');
    }

    public function pacientesComoGarante()
    {
        return $this->hasMany(Paciente::class, 'id_garante_referencia', 'ci');
    }

    public function esPaciente(): bool
    {
        return !is_null($this->seguro_id)
            || !is_null($this->triage_id)
            || !is_null($this->registro_codigo);
    }

    public function esGarante(): bool
    {
        return !$this->esPaciente();
    }
}
