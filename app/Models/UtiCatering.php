<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UtiCatering extends Model
{
    use HasFactory;

    protected $table = 'uti_catering';

    protected $fillable = [
        'uti_admission_id',
        'registered_by',
        'fecha',
        'tipo_comida',
        'estado',
        'hora_registro',
        'observaciones',
        'cargo_generado',
        'cuenta_cobro_detalle_id',
    ];

    protected $casts = [
        'fecha' => 'date',
        'hora_registro' => 'datetime',
        'cargo_generado' => 'boolean',
    ];

    public function admission()
    {
        return $this->belongsTo(UtiAdmission::class, 'uti_admission_id');
    }

    public function registeredBy()
    {
        return $this->belongsTo(User::class, 'registered_by');
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
