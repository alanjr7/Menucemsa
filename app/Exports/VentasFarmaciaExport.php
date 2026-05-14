<?php

namespace App\Exports;

use App\Models\VentaFarmacia;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class VentasFarmaciaExport implements WithMultipleSheets
{
    public function __construct(
        private readonly ?Carbon $fechaInicio,
        private readonly ?Carbon $fechaFin,
    ) {}

    public function sheets(): array
    {
        return [
            new VentasFarmaciaResumenSheet($this->fechaInicio, $this->fechaFin),
            new VentasFarmaciaDetalleSheet($this->fechaInicio, $this->fechaFin),
        ];
    }
}
