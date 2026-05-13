<?php

namespace App\Exports;

use App\Models\Episodio;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class EpisodioContableSheet implements FromArray, WithTitle, WithStyles, WithColumnWidths
{
    public function __construct(protected Episodio $episodio) {}

    public function title(): string
    {
        return 'Cuentas';
    }

    public function array(): array
    {
        $rows = [];
        $rows[] = ['Tipo atención', 'Estado', 'Total (Bs.)', 'Pagado (Bs.)', 'Saldo (Bs.)'];

        foreach ($this->episodio->cuentasCobro as $cuenta) {
            $rows[] = [
                $cuenta->tipo_atencion_label,
                $cuenta->estado_label,
                number_format($cuenta->total_calculado, 2),
                number_format($cuenta->total_pagado, 2),
                number_format($cuenta->saldo_pendiente, 2),
            ];

            foreach ($cuenta->detalles as $det) {
                $rows[] = [
                    '  ' . $det->descripcion,
                    '',
                    number_format($det->subtotal, 2),
                    '',
                    '',
                ];
            }

            $rows[] = [];
        }

        if (count($rows) === 1) {
            $rows[] = ['Sin cuentas en este episodio.'];
        }

        return $rows;
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'DBEAFE']]],
        ];
    }

    public function columnWidths(): array
    {
        return ['A' => 35, 'B' => 14, 'C' => 14, 'D' => 14, 'E' => 14];
    }
}
