<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UtiSupply extends Model
{
    use HasFactory;

    protected $table = 'uti_supplies';

    protected $fillable = [
        'uti_admission_id',
        'insumo_id',
        'used_by',
        'fecha',
        'hora',
        'cantidad',
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

    public function admission()
    {
        return $this->belongsTo(UtiAdmission::class, 'uti_admission_id');
    }

    public function insumo()
    {
        return $this->belongsTo(Insumos::class, 'insumo_id');
    }

    public function usedBy()
    {
        return $this->belongsTo(User::class, 'used_by');
    }
}
