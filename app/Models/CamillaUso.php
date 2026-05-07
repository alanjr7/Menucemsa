<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CamillaUso extends Model
{
    protected $table = 'camilla_usos';

    protected $fillable = [
        'camilla_id',
        'paciente_ci',
        'fecha_inicio',
        'fecha_fin',
        'costo_calculado',
        'cuenta_cobro_detalle_id',
        'registrado_por',
    ];

    protected $casts = [
        'fecha_inicio'    => 'datetime',
        'fecha_fin'       => 'datetime',
        'costo_calculado' => 'decimal:2',
    ];

    public function camilla(): BelongsTo
    {
        return $this->belongsTo(Camilla::class);
    }

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class, 'paciente_ci', 'ci');
    }

    public function registradoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }

    public function cuentaCobroDetalle(): BelongsTo
    {
        return $this->belongsTo(CuentaCobroDetalle::class);
    }

    public function calcularHoras(): float
    {
        $fin = $this->fecha_fin ?? now();
        return max(0.5, round($this->fecha_inicio->diffInMinutes($fin) / 60, 2));
    }
}
