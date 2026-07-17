<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Dosificación / numeración autorizada por el SIN. Define el rango de números de
 * factura habilitado, su llave/autorización y la fecha límite de emisión.
 *
 * Scaffold preparatorio: la facturación (SFE) está diferida, por lo que aún no se
 * consume numeración. Listo para cuando se construya el emisor de facturas.
 */
class Dosificacion extends Model
{
    use HasFactory;

    protected $table = 'dosificaciones';

    protected $fillable = [
        'modalidad',
        'numero_autorizacion',
        'llave_dosificacion',
        'rango_desde',
        'rango_hasta',
        'fecha_limite_emision',
        'activa',
        'observaciones',
    ];

    protected $casts = [
        'rango_desde' => 'integer',
        'rango_hasta' => 'integer',
        'fecha_limite_emision' => 'date',
        'activa' => 'boolean',
    ];

    public const MODALIDADES = [
        'computarizada_en_linea' => 'Computarizada en línea',
        'electronica_en_linea' => 'Electrónica en línea',
        'manual' => 'Manual',
    ];

    /** Dosificación vigente: activa y dentro de la fecha límite de emisión. */
    public static function activa(): ?self
    {
        return static::where('activa', true)
            ->where(function ($q) {
                $q->whereNull('fecha_limite_emision')
                    ->orWhereDate('fecha_limite_emision', '>=', Carbon::today());
            })
            ->orderByDesc('id')
            ->first();
    }

    public function getVigenteAttribute(): bool
    {
        return $this->activa
            && ($this->fecha_limite_emision === null || $this->fecha_limite_emision->gte(Carbon::today()));
    }

    public function getModalidadLabelAttribute(): string
    {
        return self::MODALIDADES[$this->modalidad] ?? ucfirst($this->modalidad);
    }

    /**
     * Siguiente número autorizado dado el último emitido. Devuelve null si la
     * dosificación está agotada (excede `rango_hasta`).
     */
    public function siguienteNumero(int $ultimoEmitido = 0): ?int
    {
        $siguiente = max($ultimoEmitido + 1, $this->rango_desde);

        if ($this->rango_hasta !== null && $siguiente > $this->rango_hasta) {
            return null;
        }

        return $siguiente;
    }
}
