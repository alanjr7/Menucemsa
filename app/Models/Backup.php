<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

/**
 * Registro de un respaldo generado por el sistema.
 *
 * @property string $filename
 * @property string $disk
 * @property string $path
 * @property int $size
 * @property string $type        manual|automatico|pre_restauracion
 * @property string $estado      en_proceso|completado|fallido
 */
class Backup extends Model
{
    protected $guarded = [];

    protected $casts = [
        'size' => 'integer',
        'incluye_archivos' => 'boolean',
    ];

    public function autor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /** Tamaño legible (KB / MB / GB). */
    public function getTamanoLegibleAttribute(): string
    {
        $bytes = (int) $this->size;
        if ($bytes <= 0) {
            return '0 KB';
        }
        $unidades = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = (int) floor(log($bytes, 1024));
        $i = min($i, count($unidades) - 1);

        return round($bytes / (1024 ** $i), 2).' '.$unidades[$i];
    }

    /** Etiqueta amigable para el tipo de respaldo. */
    public function getTipoLegibleAttribute(): string
    {
        return match ($this->type) {
            'automatico' => 'Automático',
            'pre_restauracion' => 'Previo a restauración',
            default => 'Manual',
        };
    }

    /** ¿El archivo físico todavía existe en el disco? */
    public function existeArchivo(): bool
    {
        return Storage::disk($this->disk)->exists($this->path);
    }
}
