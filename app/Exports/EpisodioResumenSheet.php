<?php

namespace App\Exports;

use App\Models\Episodio;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class EpisodioResumenSheet implements FromArray, WithTitle, WithStyles, WithColumnWidths
{
    public function __construct(protected Episodio $episodio) {}

    public function title(): string
    {
        return 'Resumen';
    }

    public function array(): array
    {
        $ep = $this->episodio;
        $rows = [];

        $rows[] = ['EPISODIO #' . $ep->numero . ' — ' . strtoupper($ep->paciente?->nombre ?? '—')];
        $rows[] = [];
        $rows[] = ['PACIENTE', ''];
        $rows[] = ['Nombre', $ep->paciente?->nombre ?? '—'];
        $rows[] = ['CI', $ep->paciente?->ci ?? $ep->paciente?->temp_code ?? '—'];
        $rows[] = ['Tipo ingreso', ucfirst($ep->tipo_ingreso ?? '—')];
        $rows[] = ['Estado', ucfirst($ep->estado)];
        $rows[] = ['Apertura', $ep->fecha_apertura->format('d/m/Y H:i')];
        $rows[] = ['Cierre', $ep->fecha_cierre?->format('d/m/Y H:i') ?? 'En curso'];
        $rows[] = ['Duración', $ep->fecha_cierre ? $ep->duracion : 'En curso'];
        if ($ep->motivo_cierre) {
            $rows[] = ['Motivo cierre', $ep->motivo_cierre];
        }

        if ($ep->emergencias->isNotEmpty()) {
            $rows[] = [];
            $rows[] = ['EMERGENCIAS', ''];
            $rows[] = ['Código', 'Fecha admisión', 'Ubicación'];
            foreach ($ep->emergencias as $em) {
                $rows[] = [
                    $em->code,
                    ($em->admission_date ?? $em->created_at)?->format('d/m/Y H:i') ?? '—',
                    $em->ubicacion_label ?? '—',
                ];
            }
        }

        if ($ep->hospitalizaciones->isNotEmpty()) {
            $rows[] = [];
            $rows[] = ['HOSPITALIZACIONES', ''];
            $rows[] = ['Ingreso', 'Alta', 'Médico'];
            foreach ($ep->hospitalizaciones as $hosp) {
                $rows[] = [
                    $hosp->fecha_ingreso->format('d/m/Y'),
                    $hosp->fecha_alta?->format('d/m/Y') ?? 'En curso',
                    $hosp->medico?->user?->name ?? 'Sin médico',
                ];
            }
        }

        return $rows;
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 13]],
        ];
    }

    public function columnWidths(): array
    {
        return ['A' => 22, 'B' => 30, 'C' => 25];
    }
}
