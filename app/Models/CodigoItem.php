<?php

namespace App\Models;

use App\Models\Concerns\GeneraCodigoCatalogo;
use App\Support\CodigoProducto;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Diccionario de códigos para ítems facturables SIN catálogo propio
 * (laboratorio, imagenología, roles de quirófano, estadía, cargos manuales…).
 *
 * Familia 9. Auto-registra cada (tipo_item + descripción normalizada) la
 * primera vez que se cobra y le asigna un código estable. Red de seguridad de
 * la Opción B; ver [[App\Support\ResolverCodigoItem]].
 */
class CodigoItem extends Model
{
    use GeneraCodigoCatalogo;

    /** Familia del código interno (NO catalogado). */
    public const FAMILIA_CODIGO = CodigoProducto::FAMILIA_DICCIONARIO;

    protected $table = 'codigo_items';

    protected $fillable = [
        'codigo',
        'tipo_item',
        'descripcion_normalizada',
        'descripcion_original',
    ];

    /** Normaliza una descripción para agrupar variantes triviales (espacios/caso/acentos). */
    public static function normalizar(string $descripcion): string
    {
        return Str::squish(mb_strtoupper($descripcion, 'UTF-8'));
    }

    /**
     * Devuelve (creando si hace falta) el código estable para este ítem.
     * Idempotente: la misma descripción siempre cae en la misma fila/código.
     */
    public static function resolver(string $tipoItem, string $descripcion): string
    {
        $normalizada = self::normalizar($descripcion);

        $item = self::firstOrCreate(
            ['tipo_item' => $tipoItem, 'descripcion_normalizada' => $normalizada],
            ['descripcion_original' => trim($descripcion)],
        );

        // Backfill defensivo: filas legacy sin código (o si el hook no corrió).
        if (empty($item->codigo)) {
            $item->codigo = CodigoProducto::format(self::FAMILIA_CODIGO, (int) $item->id);
            $item->saveQuietly();
        }

        return $item->codigo;
    }
}
