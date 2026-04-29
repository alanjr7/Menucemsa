<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IngresoPrecio extends Model
{
    use HasFactory;

    protected $fillable = [
        'tipo_ingreso',
        'precio',
        'activo',
        'user_id',
    ];

    protected $casts = [
        'precio' => 'decimal:2',
        'activo' => 'boolean',
    ];

    const TIPOS_INGRESO = [
        'consulta_externa' => 'Consulta Externa',
        'emergencia' => 'Emergencia',
        'internacion' => 'Internación',
    ];

    public static function getPrecio(string $tipoIngreso): ?float
    {
        $precio = self::where('tipo_ingreso', $tipoIngreso)
            ->where('activo', true)
            ->first();

        return $precio?->precio;
    }

    public function getTipoIngresoLabelAttribute(): string
    {
        return self::TIPOS_INGRESO[$this->tipo_ingreso] ?? $this->tipo_ingreso;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
