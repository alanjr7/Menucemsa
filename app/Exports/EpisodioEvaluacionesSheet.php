<?php

namespace App\Exports;

use App\Models\Episodio;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class EpisodioEvaluacionesSheet implements FromArray, WithTitle, WithStyles, WithColumnWidths
{
    public function __construct(protected Episodio $episodio) {}

    public function title(): string
    {
        return 'Evaluaciones';
    }

    public function array(): array
    {
        $rows = [];
        $rows[] = ['Área', 'Fecha', 'Personal', 'Observaciones', 'P.A.', 'F.C.', 'F.R.', 'Temp.', 'Sat.O2', 'Glucosa', 'Peso', 'Talla', 'IMC'];

        foreach ($this->episodio->evaluaciones as $eval) {
            $sv = $eval->signos_vitales ?? [];
            $rows[] = [
                strtoupper($eval->area),
                $eval->created_at->format('d/m/Y H:i'),
                $eval->user?->name ?? '—',
                $eval->observaciones ?? '',
                $sv['presion_arterial'] ?? '',
                $sv['frecuencia_cardiaca'] ?? '',
                $sv['frecuencia_respiratoria'] ?? '',
                $sv['temperatura'] ?? '',
                $sv['saturacion_o2'] ?? '',
                $sv['glucosa'] ?? '',
                $sv['peso'] ?? '',
                $sv['altura'] ?? '',
                $sv['imc'] ?? '',
            ];

            foreach ($eval->items as $item) {
                $rows[] = [
                    '  → ' . ucfirst($item->tipo),
                    '',
                    '',
                    $item->nombre_snapshot . ' x' . $item->cantidad,
                ];
            }
        }

        if (count($rows) === 1) {
            $rows[] = ['Sin evaluaciones en este episodio.'];
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
        return [
            'A' => 16, 'B' => 16, 'C' => 22, 'D' => 40,
            'E' => 10, 'F' => 8, 'G' => 8, 'H' => 8,
            'I' => 9, 'J' => 10, 'K' => 8, 'L' => 8, 'M' => 8,
        ];
    }
}
