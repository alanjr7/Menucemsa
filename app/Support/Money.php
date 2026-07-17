<?php

namespace App\Support;

/**
 * Aritmética monetaria de precisión fija (2 decimales) sobre BCMath.
 *
 * Fuente ÚNICA para todo cálculo de dinero del sistema. Reemplaza los
 * operadores nativos de PHP (+, -, *, /), que arrastran error de punto
 * flotante (0.1 + 0.2 !== 0.3) y hacen perder/ganar centavos al guardar o
 * mostrar montos.
 *
 * Por qué un helper y no bc* sueltas: el error más común con BCMath es pasarle
 * un float de Eloquent/Request; BCMath espera strings. Aquí todo se normaliza
 * en un solo lugar (self::s) antes de operar, con escala de trabajo amplia y
 * un único redondeo half-up al final.
 *
 * Regla del proyecto: NUNCA usar aritmética nativa sobre dinero. Usar Money::*.
 */
final class Money
{
    /** Decimales con los que se opera y se persiste el dinero (decimal(10,2)). */
    public const SCALE = 2;

    /** Escala interna mayor para multiplicar/dividir antes del redondeo final. */
    private const WORK_SCALE = 6;

    /** Normaliza float|int|string|null a string apto para BCMath (sin notación científica). */
    private static function s($value): string
    {
        if ($value === null || $value === '') {
            return '0';
        }
        if (is_float($value) || is_int($value)) {
            return number_format((float) $value, self::WORK_SCALE, '.', '');
        }
        // Soporta locales con coma decimal ("1234,56").
        return str_replace(',', '.', trim((string) $value));
    }

    public static function add($a, $b): string
    {
        return self::round(bcadd(self::s($a), self::s($b), self::WORK_SCALE));
    }

    public static function sub($a, $b): string
    {
        return self::round(bcsub(self::s($a), self::s($b), self::WORK_SCALE));
    }

    public static function mul($a, $b): string
    {
        return self::round(bcmul(self::s($a), self::s($b), self::WORK_SCALE));
    }

    public static function div($a, $b): string
    {
        $divisor = self::s($b);
        if (bccomp($divisor, '0', self::WORK_SCALE) === 0) {
            return self::format('0');
        }
        return self::round(bcdiv(self::s($a), $divisor, self::WORK_SCALE));
    }

    /**
     * Redondeo half-up (away from zero) a 2 decimales, portable a cualquier
     * PHP 8.2+ (no depende de bcround). Devuelve string con 2 decimales.
     */
    public static function round($value): string
    {
        $v = self::s($value);
        $factor = '100'; // 10 ^ SCALE
        $scaled = bcmul($v, $factor, self::WORK_SCALE);
        $half   = bccomp($v, '0', self::WORK_SCALE) >= 0 ? '0.5' : '-0.5';
        $entero = bcadd($scaled, $half, 0); // bcadd con escala 0 trunca → half-up
        return bcdiv($entero, $factor, self::SCALE);
    }

    /** Compara dos montos a escala de dinero: -1, 0 o 1. */
    public static function cmp($a, $b): int
    {
        return bccomp(self::s($a), self::s($b), self::SCALE);
    }

    /** El menor de dos montos (redondeado). */
    public static function min($a, $b): string
    {
        return self::cmp($a, $b) <= 0 ? self::round($a) : self::round($b);
    }

    /** max(0, value) — útil para saldos pendientes. */
    public static function clampZero($a): string
    {
        return self::cmp($a, 0) < 0 ? self::format('0') : self::round($a);
    }

    /** Normaliza a string con exactamente 2 decimales (sin operar). */
    public static function format($value): string
    {
        return self::round($value);
    }

    /**
     * Reglas de validación para un campo de dinero: numérico, hasta 2 decimales.
     * Uso: 'monto' => Money::rules(), o Money::rules(required: false, min: '0.01').
     */
    public static function rules(bool $required = true, string $min = '0'): string
    {
        $base = $required ? 'required' : 'nullable';
        return "{$base}|numeric|decimal:0,2|min:{$min}";
    }
}
