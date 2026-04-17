<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Enfermera extends Model
{
    protected $table = 'enfermeras';
    
    protected $primaryKey = 'user_id';
    
    public $incrementing = false;
    
    protected $fillable = [
        'user_id',
        'ci',
        'telefono',
        'tipo',
        'estado',
        'area',
        'asistente_id',
    ];

    protected $casts = [
        'ci' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function getNombreCompletoAttribute(): string
    {
        return $this->user?->name ?? 'Enfermera sin nombre';
    }

    public function getEmailAttribute(): ?string
    {
        return $this->user?->email;
    }

    public function scopeActivas($query)
    {
        return $query->where('estado', 'activo');
    }

    public function scopePorArea($query, string $area)
    {
        return $query->where('area', $area);
    }

    public function scopeEmergencia($query)
    {
        return $query->where('area', 'emergencia');
    }

    public function scopeUti($query)
    {
        return $query->where('area', 'uti');
    }

    public function isActiva(): bool
    {
        return $this->estado === 'activo';
    }

    public function isEmergencia(): bool
    {
        return $this->area === 'emergencia';
    }

    public function isUti(): bool
    {
        return $this->area === 'uti';
    }
}
