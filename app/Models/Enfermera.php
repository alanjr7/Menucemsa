<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'turno',
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

    public function getTurnoLabelAttribute(): string
    {
        return match($this->turno) {
            'mañana' => 'Turno Mañana',
            'tarde' => 'Turno Tarde',
            'noche' => 'Turno Noche',
            default => $this->turno,
        };
    }

    /**
     * Relationship to permissions
     */
    public function permissions(): HasMany
    {
        return $this->hasMany(EnfermeraPermission::class, 'enfermera_id', 'user_id');
    }

    /**
     * Check if nurse has a specific permission
     */
    public function hasPermission(string $permissionKey): bool
    {
        return $this->permissions()
            ->where('permission_key', $permissionKey)
            ->exists();
    }

    /**
     * Get all permission keys for this nurse
     */
    public function getPermissionKeys(): array
    {
        return $this->permissions()
            ->pluck('permission_key')
            ->toArray();
    }

    /**
     * Assign default permissions to new nurse
     */
    public function assignDefaultPermissions(int $grantedBy = null): void
    {
        $defaultPermissions = EnfermeraPermission::getDefaultPermissions();

        foreach ($defaultPermissions as $permission) {
            $this->permissions()->create([
                'permission_key' => $permission,
                'granted_by' => $grantedBy,
            ]);
        }
    }
}
