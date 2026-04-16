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
        'ci_paciente',
        'ci_medico',
        'habitacion_id',
        'cama_id',
        'precio_cama_dia',
        'total_estancia',
        'cuenta_cobro_detalle_id',
        'fecha_ingreso',
        'fecha_alta',
        'diagnostico',
        'tratamiento',
        'estado',
        'motivo',
        'nro_emergencia',
    ];

    protected $casts = [
        'fecha_ingreso' => 'datetime',
        'fecha_alta' => 'datetime',
        'ci_paciente' => 'integer',
        'ci_medico' => 'integer',
        'precio_cama_dia' => 'decimal:2',
        'total_estancia' => 'decimal:2',
    ];

    public function paciente()
    {
        return $this->belongsTo(Paciente::class, 'ci_paciente', 'ci');
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

    public function cuentaCobroDetalle()
    {
        return $this->belongsTo(CuentaCobroDetalle::class, 'cuenta_cobro_detalle_id');
    }

    /**
     * Calcular días de estancia hasta ahora
     * Mínimo 1 día (aunque esté pocas horas)
     */
    public function getDiasEstancia(): int
    {
        $fechaInicio = $this->fecha_ingreso;
        $fechaFin = $this->fecha_alta ?? now();

        return max(1, $fechaInicio->diffInDays($fechaFin) + 1);
    }

    /**
     * Calcular costo actual de estancia
     */
    public function getCostoEstancia(): float
    {
        $precio = $this->precio_cama_dia ?? 0;
        $dias = $this->getDiasEstancia();

        return $dias * $precio;
    }
}
