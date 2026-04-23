<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\AlmacenMedicamento;

class UtiMedication extends Model
{
    use HasFactory;

    protected $table = 'uti_medications';

    protected $fillable = [
        'uti_admission_id',
        'codigo_medicamento',
        'administered_by',
        'fecha',
        'hora',
        'dosis',
        'unidad',
        'via_administracion',
        'observaciones',
        'cargo_generado',
        'cuenta_cobro_detalle_id',
    ];

    protected $casts = [
        'fecha' => 'date',
        'hora' => 'datetime',
        'dosis' => 'decimal:2',
        'cargo_generado' => 'boolean',
    ];

    public function admission()
    {
        return $this->belongsTo(UtiAdmission::class, 'uti_admission_id');
    }

    public function medicamento()
    {
        return $this->belongsTo(Medicamentos::class, 'codigo_medicamento', 'codigo');
    }

    public function administeredBy()
    {
        return $this->belongsTo(User::class, 'administered_by');
    }
}
