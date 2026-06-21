<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Cierre de período contable. Mientras un mes esté cerrado (ya declarado al SIN),
 * no se pueden registrar ni anular egresos con fecha en ese período.
 */
class CierreContable extends Model
{
    use HasFactory;

    protected $table = 'cierres_contables';

    protected $fillable = [
        'anio',
        'mes',
        'cerrado_at',
        'cerrado_por',
        'observaciones',
    ];

    protected $casts = [
        'anio' => 'integer',
        'mes' => 'integer',
        'cerrado_at' => 'datetime',
    ];

    public function cerradoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cerrado_por');
    }

    /** ¿La fecha cae en un período contable cerrado? */
    public static function estaCerrado($fecha): bool
    {
        $f = Carbon::parse($fecha);

        return static::query()
            ->where('anio', $f->year)
            ->where('mes', $f->month)
            ->exists();
    }

    public function getEtiquetaAttribute(): string
    {
        return str_pad((string) $this->mes, 2, '0', STR_PAD_LEFT).'/'.$this->anio;
    }
}
