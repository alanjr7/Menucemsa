<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HospDrenaje extends Model
{
    use HasFactory;

    protected $table = 'hosp_drenajes';

    protected $fillable = [
        'hospitalizacion_id',
        'registered_by',
        'fecha',
        'hora',
        'tipo_drenaje',
        'realizado',
        'observaciones',
        'precio',
        'cargo_generado',
        'cuenta_cobro_detalle_id',
    ];

    protected $casts = [
        'fecha' => 'date',
        'hora' => 'datetime',
        'realizado' => 'boolean',
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
}
