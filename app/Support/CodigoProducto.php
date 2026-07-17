<?php

namespace App\Support;

/**
 * Esquema de códigos internos de producto/servicio (estilo factura Incor).
 *
 * Formato: familia (1 dígito) + correlativo (8 dígitos) = 9 dígitos.
 * El dígito de familia particiona el espacio, garantizando unicidad global
 * sin coordinar contadores entre catálogos. El correlativo = id de la fila del
 * catálogo (o del diccionario), así el código es estable y derivable.
 *
 *   Bienes (medicamentos/insumos)  AlmacenCatalogo   1 + id  -> 100000027
 *   Admisiones                     IngresoPrecio     2 + id  -> 200000003
 *   Procedimientos                 Procedimiento     5 + id  -> 500000012
 *   Cirugías                       TipoCirugia       6 + id  -> 600000004
 *   No catalogado (diccionario)    CodigoItem        9 + id  -> 900000045
 *
 * Este es el `codigoProducto` INTERNO del contribuyente (libre). NO es el
 * `codigoProductoSin` homologado: ese es un módulo futuro del SFE.
 */
final class CodigoProducto
{
    public const FAMILIA_BIENES        = '1';
    public const FAMILIA_ADMISION      = '2';
    public const FAMILIA_PROCEDIMIENTO = '5';
    public const FAMILIA_CIRUGIA       = '6';
    public const FAMILIA_DICCIONARIO   = '9';

    public static function format(string $familia, int $id): string
    {
        return $familia . str_pad((string) $id, 8, '0', STR_PAD_LEFT);
    }
}
