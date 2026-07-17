<?php

namespace App\Support;

use App\Models\AlmacenCatalogo;
use App\Models\AlmacenLote;
use App\Models\CodigoItem;
use App\Models\CuentaCobroDetalle;
use App\Models\IngresoPrecio;
use App\Models\Procedimiento;
use Illuminate\Support\Facades\Log;

/**
 * Resuelve el código interno (familia + correlativo) de una línea de cobro.
 *
 * Orden de resolución:
 *   1. Código explícito ya seteado en el detalle → se respeta.
 *   2. Medicamento con origen AlmacenLote → código del AlmacenCatalogo (familia 1).
 *   3. Coincidencia exacta de descripción contra el catálogo según tipo_item
 *      (medicamento→AlmacenCatalogo, procedimiento→Procedimiento).
 *   3.5 Admisiones (familia 2): cargo "Admisión de {tipo}" → código de IngresoPrecio.
 *   4. Diccionario CodigoItem (familia 9): auto-registra y devuelve código estable.
 *
 * DEFENSIVO: corre dentro de CuentaCobroDetalle::creating (núcleo de cobro).
 * Ante cualquier fallo devuelve null y loguea — NUNCA lanza, para no romper un cobro.
 */
final class ResolverCodigoItem
{
    /** Caché por request: "tipo|descripcionNormalizada" => codigo. */
    private static array $cache = [];

    private const BIENES = ['medicamento', 'farmacia', 'material', 'equipo_medico'];

    public static function paraDetalle(CuentaCobroDetalle $detalle): ?string
    {
        // 1. Respetar un código ya asignado (p. ej. pasado explícito desde un call site).
        if (!empty($detalle->codigo_item)) {
            return $detalle->codigo_item;
        }

        try {
            // 2. Medicamento dispensado: origen = lote → catálogo de almacén (familia 1).
            if ($detalle->origen_type === AlmacenLote::class && $detalle->origen_id) {
                $codigo = AlmacenLote::with('catalogo')->find($detalle->origen_id)?->catalogo?->codigo;
                if ($codigo) {
                    return $codigo;
                }
            }

            $descripcion = (string) $detalle->descripcion;
            $tipo        = (string) $detalle->tipo_item;
            $clave       = $tipo . '|' . CodigoItem::normalizar($descripcion);

            if (isset(self::$cache[$clave])) {
                return self::$cache[$clave];
            }

            // 3. Coincidencia exacta contra el catálogo correspondiente.
            $codigo = self::buscarEnCatalogo($tipo, $descripcion);

            // 3.5 Admisiones (familia 2): "Admisión de {tipo}" → código de IngresoPrecio.
            $codigo ??= IngresoPrecio::codigoPorDescripcion($descripcion);

            // 4. Diccionario (familia 9) como red de seguridad.
            $codigo ??= CodigoItem::resolver($tipo, $descripcion);

            return self::$cache[$clave] = $codigo;
        } catch (\Throwable $e) {
            Log::warning('[ResolverCodigoItem] No se pudo resolver código de ítem', [
                'detalle_id' => $detalle->id,
                'tipo_item'  => $detalle->tipo_item,
                'error'      => $e->getMessage(),
            ]);
            return null;
        }
    }

    /** Match exacto por nombre normalizado contra el catálogo mapeado al tipo_item. */
    private static function buscarEnCatalogo(string $tipo, string $descripcion): ?string
    {
        $normalizada = CodigoItem::normalizar($descripcion);

        if (in_array($tipo, self::BIENES, true)) {
            return self::matchUnico(
                AlmacenCatalogo::query()->whereRaw('UPPER(TRIM(nombre)) = ?', [$normalizada])
            );
        }

        if ($tipo === 'procedimiento') {
            return self::matchUnico(
                Procedimiento::query()->whereRaw('UPPER(TRIM(nombre)) = ?', [$normalizada])
            );
        }

        return null;
    }

    /** Devuelve el código solo si la coincidencia es ÚNICA (evita asignar mal por ambigüedad). */
    private static function matchUnico($query): ?string
    {
        $codigos = $query->whereNotNull('codigo')->limit(2)->pluck('codigo');

        return $codigos->count() === 1 ? $codigos->first() : null;
    }
}
