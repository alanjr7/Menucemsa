<?php

namespace App\Exports;

use App\Models\Egreso;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Libro de Compras IVA (RCV) — crédito fiscal de los egresos con factura de
 * compra válida (vigentes). Columnas homologables al formato del SIN.
 */
class RcvComprasSheet implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithStyles, WithTitle
{
    public function __construct(
        private readonly Carbon $inicio,
        private readonly Carbon $fin,
    ) {}

    public function title(): string
    {
        return 'Compras IVA';
    }

    public function collection()
    {
        return Egreso::vigentes()
            ->where('con_credito_fiscal', true)
            ->whereBetween('fecha', [$this->inicio->toDateString(), $this->fin->toDateString()])
            ->orderBy('fecha')->orderBy('id')
            ->get()
            ->values()
            ->map(fn ($e, $i) => [
                'n' => $i + 1,
                'fecha' => $e->fecha->format('d/m/Y'),
                'nit' => $e->nit_proveedor,
                'razon' => $e->proveedor,
                'factura' => $e->nro_factura,
                'autorizacion' => $e->codigo_autorizacion,
                'total' => (float) $e->monto,
                'base' => (float) $e->monto,
                'credito' => (float) $e->importe_iva,
            ]);
    }

    public function headings(): array
    {
        return [
            'N°', 'Fecha', 'NIT Proveedor', 'Razón Social', 'N° Factura', 'N° Autorización',
            'Importe Total (Bs)', 'Base Crédito Fiscal (Bs)', 'Crédito Fiscal IVA (Bs)',
        ];
    }

    public function map($row): array
    {
        return [
            $row['n'], $row['fecha'], $row['nit'], $row['razon'], $row['factura'], $row['autorizacion'],
            number_format($row['total'], 2, '.', ''),
            number_format($row['base'], 2, '.', ''),
            number_format($row['credito'], 2, '.', ''),
        ];
    }

    public function styles(Worksheet $sheet): void
    {
        $sheet->getStyle('A1:I1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1D4ED8']],
        ]);
    }
}
