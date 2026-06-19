<?php

namespace App\Support;

/**
 * Convierte un monto a su forma en letras al estilo de los comprobantes
 * bolivianos: "DOS MIL CUATROCIENTOS SESENTA Y UN 75/100 BOLIVIANOS".
 *
 * La parte decimal NO se escribe en palabras: se expresa como "NN/100"
 * (centavos), tal como aparece en facturas y recibos del país.
 *
 * Apócope: en contexto monetario el "uno" final precede siempre a un sustantivo
 * (mil, millones o la moneda), por lo que se apocopa a "un" / "veintiún" /
 * "...y un". Por eso `moneda()` siempre pide apócope a los grupos.
 *
 * Rango soportado: 0 .. 999.999.999,99 (más que suficiente para un recibo).
 */
final class NumeroALetras
{
    private const ESPECIALES = [
        0 => '', 1 => 'UNO', 2 => 'DOS', 3 => 'TRES', 4 => 'CUATRO', 5 => 'CINCO',
        6 => 'SEIS', 7 => 'SIETE', 8 => 'OCHO', 9 => 'NUEVE', 10 => 'DIEZ',
        11 => 'ONCE', 12 => 'DOCE', 13 => 'TRECE', 14 => 'CATORCE', 15 => 'QUINCE',
        16 => 'DIECISÉIS', 17 => 'DIECISIETE', 18 => 'DIECIOCHO', 19 => 'DIECINUEVE',
        20 => 'VEINTE', 21 => 'VEINTIUNO', 22 => 'VEINTIDÓS', 23 => 'VEINTITRÉS',
        24 => 'VEINTICUATRO', 25 => 'VEINTICINCO', 26 => 'VEINTISÉIS',
        27 => 'VEINTISIETE', 28 => 'VEINTIOCHO', 29 => 'VEINTINUEVE',
    ];

    private const DECENAS = [
        3 => 'TREINTA', 4 => 'CUARENTA', 5 => 'CINCUENTA', 6 => 'SESENTA',
        7 => 'SETENTA', 8 => 'OCHENTA', 9 => 'NOVENTA',
    ];

    private const CENTENAS = [
        1 => 'CIENTO', 2 => 'DOSCIENTOS', 3 => 'TRESCIENTOS', 4 => 'CUATROCIENTOS',
        5 => 'QUINIENTOS', 6 => 'SEISCIENTOS', 7 => 'SETECIENTOS',
        8 => 'OCHOCIENTOS', 9 => 'NOVECIENTOS',
    ];

    /** Ej: NumeroALetras::moneda('2461.75') => "DOS MIL CUATROCIENTOS SESENTA Y UN 75/100 BOLIVIANOS". */
    public static function moneda($monto, string $moneda = 'BOLIVIANOS'): string
    {
        $valor = Money::format($monto);                      // "2461.75"
        [$entero, $centavos] = array_pad(explode('.', $valor), 2, '00');
        $centavos = str_pad(substr($centavos, 0, 2), 2, '0');

        return self::entero((int) $entero) . ' ' . $centavos . '/100 ' . $moneda;
    }

    /** Convierte un entero (0..999.999.999) a palabras en mayúsculas. */
    private static function entero(int $n): string
    {
        if ($n === 0) {
            return 'CERO';
        }

        $millones = intdiv($n, 1_000_000);
        $miles    = intdiv($n % 1_000_000, 1_000);
        $cientos  = $n % 1_000;

        $partes = [];

        if ($millones > 0) {
            $partes[] = $millones === 1 ? 'UN MILLÓN' : self::centenas($millones, true) . ' MILLONES';
        }
        if ($miles > 0) {
            $partes[] = $miles === 1 ? 'MIL' : self::centenas($miles, true) . ' MIL';
        }
        if ($cientos > 0) {
            $partes[] = self::centenas($cientos, true);
        }

        return implode(' ', $partes);
    }

    /** Convierte 1..999 a palabras. $apocope -> "uno" final se vuelve "un". */
    private static function centenas(int $n, bool $apocope): string
    {
        if ($n === 100) {
            return 'CIEN';
        }

        $centena = intdiv($n, 100);
        $resto   = $n % 100;

        $texto = $centena > 0 ? self::CENTENAS[$centena] : '';
        if ($resto > 0) {
            $texto = trim($texto . ' ' . self::decenas($resto, $apocope));
        }

        return trim($texto);
    }

    /** Convierte 1..99 a palabras. $apocope -> "uno"->"un", "veintiuno"->"veintiún". */
    private static function decenas(int $n, bool $apocope): string
    {
        if ($n <= 29) {
            if ($apocope && $n === 1) {
                return 'UN';
            }
            if ($apocope && $n === 21) {
                return 'VEINTIÚN';
            }
            return self::ESPECIALES[$n];
        }

        $decena  = intdiv($n, 10);
        $unidad  = $n % 10;
        $texto   = self::DECENAS[$decena];

        if ($unidad > 0) {
            $unidadTxt = ($apocope && $unidad === 1) ? 'UN' : self::ESPECIALES[$unidad];
            $texto .= ' Y ' . $unidadTxt;
        }

        return $texto;
    }
}
