<?php

namespace App\Models\Concerns;

use App\Support\CodigoProducto;

/**
 * Autollena la columna `codigo` de un catálogo facturable con el esquema
 * familia + id (ver App\Support\CodigoProducto).
 *
 * El modelo que lo usa debe declarar la familia:
 *   const FAMILIA_CODIGO = CodigoProducto::FAMILIA_PROCEDIMIENTO;
 *
 * El código depende del id (autoincrement), que recién existe tras el INSERT,
 * por eso se asigna en `created` con saveQuietly() (no re-dispara eventos).
 * Solo se asigna si vino vacío: un código curado a mano se respeta.
 *
 * Nota: filas insertadas con DB::table()->insert() (raw) o seeders que saltan
 * Eloquent NO pasan por aquí; para ésas está el comando codigos:backfill.
 */
trait GeneraCodigoCatalogo
{
    public static function bootGeneraCodigoCatalogo(): void
    {
        static::created(function ($modelo) {
            if (empty($modelo->codigo)) {
                $modelo->codigo = CodigoProducto::format(static::FAMILIA_CODIGO, (int) $modelo->id);
                $modelo->saveQuietly();
            }
        });
    }
}
