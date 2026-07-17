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
        'paciente_id',
        'ci_medico',
        'estado_pago',
        'caja_id',
        'estado',
        'tipo',
    ];

    protected $casts = [
        'fecha' => 'date',
        'estado_pago' => 'boolean',
    ];

    /**
     * Genera el código de un episodio de la tabla `consultas`: PREFIJO-Ymd-NNNN
     * (p. ej. CONS-20260610-5001 para consulta externa, ENF-20260610-5001 para
     * enfermería). Lleva la fecha, pero el correlativo final es global e incremental
     * por prefijo: arranca en 5001 y NO se reinicia al cambiar de día. Cada prefijo
     * lleva su propio contador (el REGEXP filtra por prefijo). Para crear se debe usar
     * {@see crearConCodigo()}, que además resuelve colisiones concurrentes. Mismo patrón
     * que Emergency/Hospitalizacion.
     */
    public static function generarCodigo(string $prefijo = 'CONS'): string
    {
        $last = static::where('codigo', 'REGEXP', '^' . $prefijo . '-[0-9]{8}-[0-9]+$')
            ->max(\DB::raw("CAST(SUBSTRING_INDEX(codigo, '-', -1) AS UNSIGNED)")) ?? 0;
        $siguiente = max((int) $last, 5000) + 1;

        return $prefijo . '-' . now()->format('Ymd') . '-' . str_pad($siguiente, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Crea una consulta asignando un `codigo` único e incremental. Si dos procesos
     * concurrentes calculan el mismo correlativo, el unique index hace fallar al
     * segundo y aquí se reintenta con el siguiente número, en vez de un 500. Es la
     * única vía que se debe usar para crear consultas.
     *
     * @param array  $attributes Datos de la consulta. No incluir `codigo`.
     * @param string $prefijo    Prefijo según el tipo: 'CONS' (consulta externa) o 'ENF' (enfermería).
     */
    public static function crearConCodigo(array $attributes, string $prefijo = 'CONS'): self
    {
        for ($intento = 0; $intento < 5; $intento++) {
            $attributes['codigo'] = static::generarCodigo($prefijo);

            try {
                return static::create($attributes);
            } catch (\Illuminate\Database\UniqueConstraintViolationException $e) {
                // Colisión por concurrencia: reintentar con el siguiente correlativo.
            }
        }

        throw new \RuntimeException('No se pudo generar un código de consulta único tras varios intentos.');
    }

    public function especialidad()
    {
        return $this->belongsTo(Especialidad::class, 'codigo_especialidad', 'codigo');
    }

    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'paciente_id');
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
