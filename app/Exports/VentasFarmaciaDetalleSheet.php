<?php

namespace App\Exports;

use App\Models\DetalleVentaFarmacia;
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

class VentasFarmaciaDetalleSheet implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithTitle
{
    public function __construct(
        private readonly ?Carbon $fechaInicio,
        private readonly ?Carbon $fechaFin,
    ) {}

    public function title(): string
    {
        return 'Detalle Productos';
    }

    public function collection()
    {
        return DetalleVentaFarmacia::with(['venta.usuario'])
            ->when($this->fechaInicio, fn($q) => $q->whereHas('venta', fn($q2) =>
                $q2->whereBetween('fecha_venta', [$this->fechaInicio, $this->fechaFin])
            ))
            ->orderByDesc(
                DetalleVentaFarmacia::select('fecha_venta')
                    ->from('ventas_farmacia')
                    ->whereColumn('ventas_farmacia.codigo_venta', 'detalle_ventas_farmacia.codigo_venta')
                    ->limit(1)
            )
            ->get();
    }

    public function headings(): array
    {
        return [
            'Código Venta',
            'Fecha',
            'Hora',
            'Vendedor',
            'Producto',
            'Tipo',
            'Cantidad',
            'Precio Unit. (Bs.)',
            'Subtotal (Bs.)',
            'Estado Venta',
        ];
    }

    public function map($d): array
    {
        return [
            $d->codigo_venta,
            $d->venta ? Carbon::parse($d->venta->fecha_venta)->format('d/m/Y') : '',
            $d->venta ? Carbon::parse($d->venta->fecha_venta)->format('H:i:s') : '',
            $d->venta?->usuario?->name ?? 'N/A',
            $d->nombre_producto,
            ucfirst($d->tipo_producto ?? ''),
            $d->cantidad,
            number_format((float) $d->precio_unitario, 2),
            number_format((float) $d->subtotal, 2),
            $d->venta?->estado ?? '',
        ];
    }

    public function styles(Worksheet $sheet): void
    {
        $sheet->getStyle('A1:J1')->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF059669']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $lastRow = $sheet->getHighestRow();

        $totalRow = $lastRow + 2;
        $sheet->setCellValue("H{$totalRow}", 'TOTAL:');
        $sheet->setCellValue("I{$totalRow}", "=SUM(I2:I{$lastRow})");
        $sheet->getStyle("H{$totalRow}:I{$totalRow}")->applyFromArray([
            'font' => ['bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFD1FAE5']],
        ]);
        $sheet->getStyle("I{$totalRow}")->getNumberFormat()->setFormatCode('#,##0.00');
    }
}
