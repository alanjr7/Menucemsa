<?php

namespace App\Support;

/**
 * Catálogo oficial de "Documento de Identidad" del SIN (SFE Bolivia).
 * Los códigos numéricos son los que exige el SIN, por lo que se persisten
 * tal cual para que el día de la facturación electrónica no haya traducción.
 *
 * @see project_contabilidad_credito_fiscal — roadmap "defenderse ante el SIN"
 */
enum TipoDocumento: int
{
    case CI = 1;        // Cédula de Identidad
    case CEX = 2;       // Cédula de Identidad de Extranjero
    case PASAPORTE = 3; // Pasaporte
    case OTRO = 4;      // Otro documento
    case NIT = 5;       // Número de Identificación Tributaria

    /** Valores por defecto para una venta "sin nombre" (cliente sin datos). */
    public const SIN_NOMBRE_TIPO = self::NIT;
    public const SIN_NOMBRE_DOC = '0';
    public const SIN_NOMBRE_RAZON = 'S/N';

    public function label(): string
    {
        return match ($this) {
            self::CI => 'CI',
            self::CEX => 'Carnet de Extranjería',
            self::PASAPORTE => 'Pasaporte',
            self::OTRO => 'Otro documento',
            self::NIT => 'NIT',
        };
    }

    /** Etiqueta para un código suelto (p.ej. desde un accesor de modelo). */
    public static function labelFor(?int $code): string
    {
        return ($code !== null ? self::tryFrom($code) : null)?->label() ?? '—';
    }

    /** Opciones para selects/JS: [['code' => 1, 'label' => 'CI'], ...]. */
    public static function options(): array
    {
        return array_map(
            fn (self $c) => ['code' => $c->value, 'label' => $c->label()],
            self::cases()
        );
    }

    /** Lista de códigos válidos, para reglas de validación in:. */
    public static function codigos(): array
    {
        return array_map(fn (self $c) => $c->value, self::cases());
    }
}
