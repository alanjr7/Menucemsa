<?php

namespace App\Support;

/**
 * Tasas tributarias bolivianas (SIN) — fuente única.
 *
 * IVA "por dentro": el precio final ya incluye el impuesto; el débito/crédito fiscal
 * se calcula como 13% del importe total (no se suma por fuera). Mismo criterio para
 * compras (crédito fiscal) y ventas (débito fiscal).
 */
final class Impuestos
{
    /** IVA — Impuesto al Valor Agregado (tasa nominal). */
    public const IVA = '0.13';

    /** IT — Impuesto a las Transacciones. */
    public const IT = '0.03';

    /** IUE — Impuesto sobre las Utilidades de las Empresas. */
    public const IUE = '0.25';

    /** Débito/crédito fiscal IVA = 13% del importe total (IVA incluido). */
    public static function iva(string $importe): string
    {
        return Money::mul($importe, self::IVA);
    }

    /** Impuesto a las Transacciones = 3% del importe. */
    public static function it(string $importe): string
    {
        return Money::mul($importe, self::IT);
    }
}
