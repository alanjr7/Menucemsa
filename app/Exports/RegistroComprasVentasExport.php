<?php

namespace App\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

/**
 * Registro de Compras y Ventas (RCV) boliviano: Libro de Ventas IVA + Libro de
 * Compras IVA + Resumen de impuestos. Formato homologable para que el contador
 * lo cargue al SIAT / Da Vinci. (No emite CUF/autorización — SFE diferido.)
 */
class RegistroComprasVentasExport implements WithMultipleSheets
{
    public function __construct(
        private readonly Carbon $inicio,
        private readonly Carbon $fin,
    ) {}

    public function sheets(): array
    {
        return [
            new RcvVentasSheet($this->inicio, $this->fin),
            new RcvComprasSheet($this->inicio, $this->fin),
            new RcvResumenImpuestosSheet($this->inicio, $this->fin),
        ];
    }
}
