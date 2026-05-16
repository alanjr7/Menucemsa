<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paciente extends Model
{
    use HasFactory;

    protected $table = 'pacientes';

    protected $fillable = [
        'ci',
        'temp_code',
        'is_temp',
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
        'garante_id',
    ];

    protected $casts = [
        'ci' => 'integer',
        'is_temp' => 'boolean',
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
        return $this->hasMany(Consulta::class, 'paciente_id');
    }

    public function historialMedico()
    {
        return $this->hasMany(HistorialMedico::class, 'paciente_id')
                    ->orderBy('fecha', 'desc');
    }

    public function historialReciente()
    {
        return $this->hasOne(HistorialMedico::class, 'paciente_id')
                    ->orderBy('fecha', 'desc');
    }

    public function emergencias()
    {
        return $this->hasMany(Emergency::class, 'paciente_id');
    }

    public function hospitalizaciones()
    {
        return $this->hasMany(Hospitalizacion::class, 'paciente_id');
    }

    public function cuentasCobro()
    {
        return $this->hasMany(\App\Models\CuentaCobro::class, 'paciente_id');
    }

    public function cuentasPendientes()
    {
        return $this->hasMany(\App\Models\CuentaCobro::class, 'paciente_id')
                    ->whereIn('estado', ['pendiente', 'parcial']);
    }

    public function altas()
    {
        return $this->hasMany(\App\Models\AltaPaciente::class, 'paciente_id');
    }

    public function episodios()
    {
        return $this->hasMany(\App\Models\Episodio::class, 'paciente_id')
                    ->orderBy('numero', 'desc');
    }

    public function episodioAbierto()
    {
        return $this->hasOne(\App\Models\Episodio::class, 'paciente_id')
                    ->where('estado', 'abierto');
    }

    public function estaDeAlta(): bool
    {
        return $this->altas()->exists();
    }

    public function garante()
    {
        return $this->belongsTo(Paciente::class, 'garante_id');
    }

    public function pacientesComoGarante()
    {
        return $this->hasMany(Paciente::class, 'garante_id');
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
