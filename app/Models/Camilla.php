<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Camilla extends Model
{
    protected $table = 'camillas';

    protected $fillable = ['nombre', 'codigo', 'precio_por_hora', 'area', 'activa'];

    protected $casts = [
        'precio_por_hora' => 'decimal:2',
        'activa'          => 'boolean',
    ];

    public function usos(): HasMany
    {
        return $this->hasMany(CamillaUso::class);
    }

    public function getAreaLabelAttribute(): string
    {
        return match ($this->area) {
            'uti'        => 'UTI',
            'emergencia' => 'Emergencia',
            default      => ucfirst($this->area),
        };
    }
}
