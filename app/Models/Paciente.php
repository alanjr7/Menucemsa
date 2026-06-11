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

    /**
     * Calcula el siguiente código temporal correlativo del día (TEMP-Ymd-NNN).
     * Es solo el cálculo: la unicidad real la garantiza el unique index +
     * el reintento de {@see crearTemporal()}. Para preview usar este método;
     * para persistir usar crearTemporal().
     */
    public static function generarTempCode(): string
    {
        $prefix = 'TEMP-' . now()->format('Ymd');
        $last   = static::where('temp_code', 'like', $prefix . '-%')
            ->orderBy('temp_code', 'desc')
            ->value('temp_code');
        $seq = $last ? ((int) substr($last, -3)) + 1 : 1;

        return $prefix . '-' . str_pad($seq, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Crea un paciente temporal asignando un temp_code único. Si dos procesos
     * concurrentes calculan el mismo correlativo, el unique index hace fallar
     * al segundo y aquí se reintenta con el siguiente número, en vez de un 500.
     *
     * @param array $attributes Datos del paciente (nombre, sexo, etc.). No es
     *                          necesario incluir temp_code ni is_temp.
     */
    public static function crearTemporal(array $attributes): self
    {
        $attributes['is_temp'] = true;

        for ($intento = 0; $intento < 5; $intento++) {
            $attributes['temp_code'] = static::generarTempCode();

            try {
                return static::create($attributes);
            } catch (\Illuminate\Database\UniqueConstraintViolationException $e) {
                // Colisión por concurrencia: reintentar con el siguiente correlativo.
            }
        }

        throw new \RuntimeException('No se pudo generar un código temporal único tras varios intentos.');
    }

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
