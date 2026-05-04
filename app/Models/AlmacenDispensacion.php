<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AlmacenDispensacion extends Model
{
    protected $table = 'almacen_dispensaciones';

    protected $fillable = [
        'ubicacion_origen', 'ubicacion_destino',
        'dispensado_por', 'recibido_por', 'observaciones', 'fecha_dispensacion',
    ];

    protected $casts = [
        'fecha_dispensacion' => 'datetime',
    ];

    public function detalles(): HasMany
    {
        return $this->hasMany(AlmacenDispensacionDetalle::class, 'dispensacion_id');
    }

    public function dispensadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dispensado_por');
    }

    public function scopeRecientes($query)
    {
        return $query->orderBy('fecha_dispensacion', 'desc');
    }

    public function scopePorDestino($query, string $destino)
    {
        return $query->where('ubicacion_destino', $destino);
    }

    public function scopeEntreFechas($query, $desde, $hasta)
    {
        return $query->whereBetween('fecha_dispensacion', [$desde, $hasta]);
    }

    public function getUbicacionDestinoLabelAttribute(): string
    {
        return match ($this->ubicacion_destino) {
            'emergencia'      => 'Emergencia',
            'cirugia'         => 'Cirugía',
            'hospitalizacion' => 'Hospitalización',
            'uti'             => 'UTI',
            'usi'             => 'USI',
            'neonato'         => 'Neonato',
            'internacion'     => 'Internación',
            default           => ucfirst($this->ubicacion_destino),
        };
    }
}
