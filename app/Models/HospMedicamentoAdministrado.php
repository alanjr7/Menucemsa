<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HospMedicamentoAdministrado extends Model
{
    use HasFactory;

    protected $table = 'hosp_medicamentos_administrados';

    protected $fillable = [
        'hospitalizacion_id',
        'medicamento_id',
        'administered_by',
        'fecha',
        'hora',
        'cantidad',
        'unidad',
        'via_administracion',
        'observaciones',
        'cargo_generado',
        'cuenta_cobro_detalle_id',
    ];

    protected $casts = [
        'fecha' => 'date',
        'hora' => 'datetime',
        'cantidad' => 'decimal:2',
        'cargo_generado' => 'boolean',
    ];

    public function hospitalizacion()
    {
        return $this->belongsTo(Hospitalizacion::class, 'hospitalizacion_id');
    }

    public function medicamento()
    {
        return $this->belongsTo(AlmacenMedicamento::class, 'medicamento_id');
    }

    public function administeredBy()
    {
        return $this->belongsTo(User::class, 'administered_by');
    }

    public function cuentaCobroDetalle()
    {
        return $this->belongsTo(CuentaCobroDetalle::class, 'cuenta_cobro_detalle_id');
    }
}
