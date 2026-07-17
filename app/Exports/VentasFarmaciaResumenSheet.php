<?php

namespace App\Exports;

use App\Models\VentaFarmacia;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class VentasFarmaciaResumenSheet implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithTitle
{
    public function __construct(
        private readonly ?Carbon $fechaInicio,
        private readonly ?Carbon $fechaFin,
    ) {}

    public function title(): string
    {
        return 'Ventas';
    }

    public function collection()
    {
        return VentaFarmacia::with(['usuario', 'detalles'])
            ->when($this->fechaInicio, fn($q) => $q->whereBetween('fecha_venta', [$this->fechaInicio, $this->fechaFin]))
            ->orderBy('fecha_venta', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Código Venta',
            'Fecha',
            'Hora',
            'Vendedor',
            'Cliente',
            'Items',
            'Cant. Total',
            'Método Pago',
            'Total (Bs.)',
            'Estado',
            'Receta',
        ];
    }

    public function map($v): array
    {
        return [
            $v->codigo_venta,
            Carbon::parse($v->fecha_venta)->format('d/m/Y'),
            Carbon::parse($v->fecha_venta)->format('H:i:s'),
            $v->usuario?->name ?? 'N/A',
            $v->cliente ?? 'Público en general',
            $v->detalles->map(fn($d) => $d->nombre_producto . ' x' . $d->cantidad)->join(' | '),
            $v->detalles->sum('cantidad'),
            ucfirst($v->metodo_pago),
            number_format((float) $v->total, 2),
            $v->estado,
            $v->requiere_receta ? 'Sí' : 'No',
        ];
    }

    public function styles(Worksheet $sheet): void
    {
        $sheet->getStyle('A1:K1')->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1D4ED8']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $lastRow = $sheet->getHighestRow();
        $sheet->getStyle("A2:K{$lastRow}")->applyFromArray([
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
        ]);

        // Fila de totales al final
        $totalRow = $lastRow + 2;
        $sheet->setCellValue("H{$totalRow}", 'TOTAL:');
        $sheet->setCellValue("I{$totalRow}", "=SUM(I2:I{$lastRow})");
        $sheet->getStyle("H{$totalRow}:I{$totalRow}")->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFDBEAFE']],
        ]);
        $sheet->getStyle("I{$totalRow}")->getNumberFormat()->setFormatCode('#,##0.00');
    }
}
