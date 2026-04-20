<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HospCatering extends Model
{
    use HasFactory;

    protected $table = 'hosp_catering';

    protected $fillable = [
        'hospitalizacion_id',
        'registered_by',
        'fecha',
        'tipo_comida',
        'estado',
        'hora_registro',
        'observaciones',
        'precio',
        'cargo_generado',
        'cuenta_cobro_detalle_id',
    ];

    protected $casts = [
        'fecha' => 'date',
        'hora_registro' => 'datetime',
        'precio' => 'decimal:2',
        'cargo_generado' => 'boolean',
    ];

    public function hospitalizacion()
    {
        return $this->belongsTo(Hospitalizacion::class, 'hospitalizacion_id');
    }

    public function registeredBy()
    {
        return $this->belongsTo(User::class, 'registered_by');
    }

    public function cuentaCobroDetalle()
    {
        return $this->belongsTo(CuentaCobroDetalle::class, 'cuenta_cobro_detalle_id');
    }

    public function getTipoComidaLabelAttribute()
    {
        return match($this->tipo_comida) {
            'desayuno' => 'Desayuno',
            'almuerzo' => 'Almuerzo',
            'merienda' => 'Merienda',
            'cena' => 'Cena',
            default => 'Desconocido',
        };
    }

    public function getEstadoLabelAttribute()
    {
        return match($this->estado) {
            'dado' => 'Dado',
            'no_dado' => 'No Dado',
            'no_aplica' => 'No Aplica',
            default => 'Desconocido',
        };
    }

    public function getEstadoColorAttribute()
    {
        return match($this->estado) {
            'dado' => 'green',
            'no_dado' => 'red',
            'no_aplica' => 'gray',
            default => 'gray',
        };
    }
}
