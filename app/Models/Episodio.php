<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Episodio extends Model
{
    protected $table = 'episodios';

    protected $fillable = [
        'paciente_id',
        'numero',
        'fecha_apertura',
        'fecha_cierre',
        'estado',
        'tipo_ingreso',
        'motivo_cierre',
        'created_by',
        'closed_by',
    ];

    protected $casts = [
        'fecha_apertura' => 'datetime',
        'fecha_cierre'   => 'datetime',
        'numero'         => 'integer',
    ];

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class, 'paciente_id');
    }

    public function creadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function cerradoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    public function evaluaciones(): HasMany
    {
        return $this->hasMany(Evaluacion::class);
    }

    public function historialMedico(): HasMany
    {
        return $this->hasMany(HistorialMedico::class);
    }

    public function emergencias(): HasMany
    {
        return $this->hasMany(Emergency::class);
    }

    public function hospitalizaciones(): HasMany
    {
        return $this->hasMany(Hospitalizacion::class);
    }

    public function cuentasCobro(): HasMany
    {
        return $this->hasMany(CuentaCobro::class);
    }

    public function isAbierto(): bool
    {
        return $this->estado === 'abierto';
    }

    public function getDuracionAttribute(): ?string
    {
        $fin = $this->fecha_cierre ?? now();
        $dias = (int) $this->fecha_apertura->diffInDays($fin);
        return $dias === 0 ? 'Mismo día' : $dias . ' día' . ($dias !== 1 ? 's' : '');
    }
}
