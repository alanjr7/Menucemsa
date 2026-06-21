<?php

namespace App\Exports;

use App\Models\PagoCuenta;
use App\Models\VentaFarmacia;
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
 * Libro de Ventas IVA (RCV) — débito fiscal de cobros de caja (PagoCuenta) +
 * ventas de farmacia (VentaFarmacia). Columnas homologables al formato del SIN.
 */
class RcvVentasSheet implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithStyles, WithTitle
{
    public function __construct(
        private readonly Carbon $inicio,
        private readonly Carbon $fin,
    ) {}

    public function title(): string
    {
        return 'Ventas IVA';
    }

    public function collection()
    {
        $rows = collect();
        $n = 0;

        $pagos = PagoCuenta::with('cuentaCobro')
            ->whereBetween('created_at', [$this->inicio, $this->fin])
            ->orderBy('created_at')->get();
        foreach ($pagos as $p) {
            $r = $p->cuentaCobro?->receptorFiscal() ?? [
                'razon_social' => 'S/N', 'tipo_documento_label' => 'NIT', 'numero_documento' => '0', 'complemento' => null,
            ];
            $rows->push([
                'n' => ++$n,
                'fecha' => $p->created_at->format('d/m/Y'),
                'origen' => 'Caja',
                'recibo' => $p->id,
                'autorizacion' => '',
                'tipo_doc' => $r['tipo_documento_label'],
                'documento' => $r['numero_documento'].($r['complemento'] ? '-'.$r['complemento'] : ''),
                'razon' => $r['razon_social'],
                'total' => (float) $p->monto,
                'base' => (float) $p->base_imponible,
                'debito' => (float) $p->debito_fiscal,
            ]);
        }

        $ventas = VentaFarmacia::where('estado', 'COMPLETADA')
            ->whereBetween('fecha_venta', [$this->inicio, $this->fin])
            ->orderBy('fecha_venta')->get();
        foreach ($ventas as $v) {
            $rows->push([
                'n' => ++$n,
                'fecha' => Carbon::parse($v->fecha_venta)->format('d/m/Y'),
                'origen' => 'Farmacia',
                'recibo' => $v->codigo_venta,
                'autorizacion' => '',
                'tipo_doc' => $v->con_credito_fiscal ? $v->factura_tipo_documento_label : 'NIT',
                'documento' => $v->con_credito_fiscal
                    ? $v->factura_numero_documento.($v->factura_complemento ? '-'.$v->factura_complemento : '')
                    : '0',
                'razon' => $v->con_credito_fiscal ? $v->factura_razon_social : 'S/N',
                'total' => (float) $v->total,
                'base' => (float) $v->base_imponible,
                'debito' => (float) $v->debito_fiscal,
            ]);
        }

        return $rows;
    }

    public function headings(): array
    {
        return [
            'N°', 'Fecha', 'Origen', 'N° Recibo', 'N° Autorización/CUF',
            'Tipo Doc', 'NIT/CI', 'Razón Social',
            'Importe Total (Bs)', 'Base Débito Fiscal (Bs)', 'Débito Fiscal IVA (Bs)',
        ];
    }

    public function map($row): array
    {
        return [
            $row['n'], $row['fecha'], $row['origen'], $row['recibo'], $row['autorizacion'],
            $row['tipo_doc'], $row['documento'], $row['razon'],
            number_format($row['total'], 2, '.', ''),
            number_format($row['base'], 2, '.', ''),
            number_format($row['debito'], 2, '.', ''),
        ];
    }

    public function styles(Worksheet $sheet): void
    {
        $sheet->getStyle('A1:K1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF15803D']],
        ]);
    }
}
