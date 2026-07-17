<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hospitalizacion extends Model
{
    use HasFactory;

    protected $table = 'hospitalizaciones';
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'paciente_id',
        'ci_medico',
        'habitacion_id',
        'cama_id',
        'fecha_ingreso',
        'fecha_alta',
        'diagnostico',
        'tratamiento',
        'estado',
        'motivo',
        'nro_emergencia',
        'contacto_nombre',
        'contacto_telefono',
        'equipos_medicos',
        'episodio_id',
    ];

    protected $casts = [
        'fecha_ingreso' => 'datetime',
        'fecha_alta' => 'datetime',
        'ci_medico' => 'integer',
        'equipos_medicos' => 'array',
    ];

    /**
     * Genera el código de hospitalización: INT-Ymd-NNNN (p. ej. INT-20260610-5001).
     * Lleva la fecha del ingreso, pero el correlativo final es global e incremental:
     * arranca en 5001 y NO se reinicia al cambiar de día. Solo considera los códigos
     * con este formato (INT-fecha-numero), ignorando registros antiguos con otro
     * esquema. Para crear hospitalizaciones se debe usar {@see crearConCodigo()},
     * que además resuelve colisiones concurrentes.
     */
    public static function generarCodigo(): string
    {
        $last = static::where('id', 'REGEXP', '^INT-[0-9]{8}-[0-9]+$')
            ->max(\DB::raw("CAST(SUBSTRING_INDEX(id, '-', -1) AS UNSIGNED)")) ?? 0;
        $siguiente = max((int) $last, 5000) + 1;

        return 'INT-' . now()->format('Ymd') . '-' . str_pad($siguiente, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Crea una hospitalización asignando un `id` (código) único e incremental.
     * Si dos procesos concurrentes calculan el mismo correlativo, la clave
     * primaria hace fallar al segundo y aquí se reintenta con el siguiente
     * número, en vez de un 500. Es la única vía que se debe usar para crear
     * hospitalizaciones.
     *
     * @param array $attributes Datos de la hospitalización. No incluir `id`.
     */
    public static function crearConCodigo(array $attributes): self
    {
        for ($intento = 0; $intento < 5; $intento++) {
            $attributes['id'] = static::generarCodigo();

            try {
                return static::create($attributes);
            } catch (\Illuminate\Database\UniqueConstraintViolationException $e) {
                // Colisión por concurrencia: reintentar con el siguiente correlativo.
            }
        }

        throw new \RuntimeException('No se pudo generar un código de hospitalización único tras varios intentos.');
    }

    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'paciente_id');
    }

    public function medico()
    {
        return $this->belongsTo(Medico::class, 'ci_medico', 'ci');
    }

    public function habitacion()
    {
        return $this->belongsTo(Habitacion::class, 'habitacion_id');
    }

    public function cama()
    {
        return $this->belongsTo(Cama::class, 'cama_id');
    }

    public function episodio()
    {
        return $this->belongsTo(\App\Models\Episodio::class);
    }

    /**
     * Calcular días de estancia hasta ahora.
     * Se cobra el día de ingreso + días completos adicionales.
     * Mínimo 1 día aunque el alta sea el mismo día (regla de negocio: cobrar día de ingreso).
     */
    public function getDiasEstancia(): int
    {
        $fechaInicio = $this->fecha_ingreso;
        $fechaFin = $this->fecha_alta ?? now();

        return max(1, $fechaInicio->diffInDays($fechaFin) + 1);
    }

    /**
     * Costo de estancia real: suma de los cargos de estadía (CuentaCobroDetalle
     * tipo 'estadia') del paciente. La estadía se cobra vía registro-uso, no sobre
     * la hospitalización, por lo que el costo se deriva de la cuenta del paciente
     * (los cargos deshabilitados quedan fuera por el global scope de CuentaCobroDetalle).
     */
    public function getCostoEstancia(): float
    {
        if (!$this->paciente_id) {
            return 0.0;
        }

        return (float) CuentaCobroDetalle::estadia()
            ->whereHas('cuentaCobro', fn ($q) => $q->where('paciente_id', $this->paciente_id))
            ->sum('subtotal');
    }
}
