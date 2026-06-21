<?php

namespace App\Support;

/**
 * Homologación al `codigoProductoSin` del Catálogo de Productos y Servicios del SIN.
 *
 * El sistema ya emite el `codigoProducto` INTERNO (familia + correlativo, ver
 * {@see CodigoProducto}). La factura electrónica (SFE) exige ADEMÁS el
 * `codigoProductoSin` homologado del catálogo oficial. Aquí se mapea cada familia
 * interna a su código homologado.
 *
 * ⚠️ Los códigos de abajo son un PUNTO DE PARTIDA y deben CONFIRMARSE con el
 * Catálogo de Productos y Servicios del SIN según la actividad económica (CAEB)
 * registrada de la clínica. Editar SOLO este archivo para ajustarlos (fuente única).
 *
 * SFE diferido: hoy esto no se imprime (el comprobante interno no lleva campos
 * fiscales); queda listo para cuando se construya el emisor de facturas.
 */
final class CodigoSin
{
    /** Mapeo familia interna ({@see CodigoProducto}) → codigoProductoSin homologado. */
    public const POR_FAMILIA = [
        CodigoProducto::FAMILIA_BIENES        => '99100', // Medicamentos / insumos (confirmar con SIN)
        CodigoProducto::FAMILIA_ADMISION      => '86101', // Consulta / admisión médica (confirmar)
        CodigoProducto::FAMILIA_PROCEDIMIENTO => '86101', // Procedimientos médicos (confirmar)
        CodigoProducto::FAMILIA_CIRUGIA       => '86101', // Cirugías (confirmar)
        CodigoProducto::FAMILIA_DICCIONARIO   => '99100', // Genérico no catalogado (confirmar)
    ];

    /** Etiqueta legible por familia (para la pantalla de verificación). */
    public const ETIQUETA_FAMILIA = [
        CodigoProducto::FAMILIA_BIENES        => 'Medicamentos / insumos',
        CodigoProducto::FAMILIA_ADMISION      => 'Consultas / admisiones',
        CodigoProducto::FAMILIA_PROCEDIMIENTO => 'Procedimientos',
        CodigoProducto::FAMILIA_CIRUGIA       => 'Cirugías',
        CodigoProducto::FAMILIA_DICCIONARIO   => 'Otros (diccionario)',
    ];

    /** Código homologado por defecto cuando la familia no está mapeada. */
    public const DEFECTO = '99100';

    /** Resuelve el codigoProductoSin a partir del código interno (familia = 1er dígito). */
    public static function paraCodigoItem(?string $codigoItem): string
    {
        $familia = ($codigoItem !== null && $codigoItem !== '') ? $codigoItem[0] : null;

        return self::POR_FAMILIA[$familia] ?? self::DEFECTO;
    }
}
