<?php

namespace App\Exports;

use App\Models\CitaQuirurgica;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CirugiasExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths, WithTitle
{
    protected $citas;
    protected $fechaDesde;
    protected $fechaHasta;

    public function __construct($citas, $fechaDesde = null, $fechaHasta = null)
    {
        $this->citas = $citas;
        $this->fechaDesde = $fechaDesde;
        $this->fechaHasta = $fechaHasta;
    }

    public function collection()
    {
        return $this->citas->map(function ($cita) {
            return [
                'Fecha' => $cita->fecha->format('d/m/Y'),
                'Hora' => $cita->hora_inicio_estimada->format('H:i'),
                'Paciente' => $cita->paciente->nombre,
                'CI' => $cita->paciente->ci,
                'Cirujano' => optional($cita->cirujano->user)->name ?? 'N/A',
                'Quirófano' => 'Q' . $cita->quirofano->id,
                'Tipo' => $cita->tipo_cirugia,
                'Estado' => ucfirst($cita->estado),
                'Duración' => $this->formatearDuracion($cita->duracion_real),
                'Costo' => $cita->costo_final ? '$' . number_format($cita->costo_final, 2) : '-',
            ];
        });
    }

    public function headings(): array
    {
        return [
            ['HISTORIAL DE CIRUGÍAS'],
            [''],
            ['Fecha', 'Hora', 'Paciente', 'CI', 'Cirujano', 'Quirófano', 'Tipo', 'Estado', 'Duración', 'Costo'],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Título principal
        $sheet->mergeCells('A1:J1');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 16],
            'alignment' => ['horizontal' => 'center'],
        ]);

        // Encabezados de columnas
        $sheet->getStyle('A3:J3')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => 'solid',
                'startColor' => ['rgb' => '3B82F6'],
            ],
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
        ]);

        return [
            1 => ['font' => ['bold' => true, 'size' => 16]],
            3 => ['font' => ['bold' => true]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 12,
            'B' => 8,
            'C' => 30,
            'D' => 12,
            'E' => 25,
            'F' => 12,
            'G' => 12,
            'H' => 12,
            'I' => 12,
            'J' => 15,
        ];
    }

    public function title(): string
    {
        return 'Cirugías';
    }

    private function formatearDuracion($minutos)
    {
        if (!$minutos) return '-';
        $total = round($minutos);
        $horas = floor($total / 60);
        $mins = $total % 60;
        if ($horas > 0) {
            return "{$horas}h {$mins}min";
        }
        return "{$mins}min";
    }
}
