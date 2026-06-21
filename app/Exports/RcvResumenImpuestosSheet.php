<?php

namespace App\Exports;

use App\Models\Egreso;
use App\Models\PagoCuenta;
use App\Models\VentaFarmacia;
use App\Support\Impuestos;
use App\Support\Money;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Resumen de impuestos del período: posición IVA (débito − crédito), base IT 3%
 * y base IUE 25% (referencial). Insumo para que el contador arme F-200 / F-400 / F-500.
 */
class RcvResumenImpuestosSheet implements FromArray, ShouldAutoSize, WithHeadings, WithStyles, WithTitle
{
    public function __construct(
        private readonly Carbon $inicio,
        private readonly Carbon $fin,
    ) {}

    public function title(): string
    {
        return 'Resumen Impuestos';
    }

    public function array(): array
    {
        $pagos = PagoCuenta::whereBetween('created_at', [$this->inicio, $this->fin])->get();
        $ventas = VentaFarmacia::where('estado', 'COMPLETADA')
            ->whereBetween('fecha_venta', [$this->inicio, $this->fin])->get();
        $egresos = Egreso::vigentes()
            ->whereBetween('fecha', [$this->inicio->toDateString(), $this->fin->toDateString()])->get();

        $ventasCaja = $pagos->reduce(fn ($a, $p) => Money::add($a, $p->monto), '0');
        $ventasFarmacia = $ventas->reduce(fn ($a, $v) => Money::add($a, $v->total), '0');
        $totalVentas = Money::add($ventasCaja, $ventasFarmacia);

        $debito = $pagos->reduce(fn ($a, $p) => Money::add($a, $p->debito_fiscal), '0');
        $debito = Money::add($debito, $ventas->reduce(fn ($a, $v) => Money::add($a, $v->debito_fiscal), '0'));

        $comprasCf = $egresos->where('con_credito_fiscal', true);
        $totalCompras = $comprasCf->reduce(fn ($a, $e) => Money::add($a, $e->monto), '0');
        $credito = $comprasCf->reduce(fn ($a, $e) => Money::add($a, $e->importe_iva), '0');

        $posicionIva = Money::sub($debito, $credito);

        $totalEgresos = $egresos->reduce(fn ($a, $e) => Money::add($a, $e->monto), '0');
        $retIue = $egresos->reduce(fn ($a, $e) => Money::add($a, $e->retencion_iue), '0');
        $retIt = $egresos->reduce(fn ($a, $e) => Money::add($a, $e->retencion_it), '0');

        $utilidad = Money::sub($totalVentas, $totalEgresos);
        $iue = Money::cmp($utilidad, '0') > 0 ? Money::mul($utilidad, Impuestos::IUE) : '0.00';

        $f = fn ($v) => number_format((float) $v, 2, '.', '');

        return [
            ['IVA — DÉBITO Y CRÉDITO', ''],
            ['Total ventas (ingresos brutos)', $f($totalVentas)],
            ['Débito fiscal IVA (13%)', $f($debito)],
            ['Total compras con crédito fiscal', $f($totalCompras)],
            ['Crédito fiscal IVA', $f($credito)],
            [Money::cmp($posicionIva, '0') >= 0 ? 'IVA a pagar (F-200)' : 'Saldo a favor IVA', $f(abs((float) $posicionIva))],
            ['', ''],
            ['IT — IMPUESTO A LAS TRANSACCIONES', ''],
            ['Base IT (ventas brutas)', $f($totalVentas)],
            ['IT (3%) (F-400)', $f(Impuestos::it($totalVentas))],
            ['', ''],
            ['RETENCIONES (F-570)', ''],
            ['Retención IUE del período', $f($retIue)],
            ['Retención IT del período', $f($retIt)],
            ['', ''],
            ['IUE — REFERENCIAL (el F-500 lo arma el contador)', ''],
            ['Total egresos del período', $f($totalEgresos)],
            ['Utilidad estimada (ventas − egresos)', $f($utilidad)],
            ['IUE estimado (25%)', $f($iue)],
        ];
    }

    public function headings(): array
    {
        return ['Concepto', 'Importe (Bs)'];
    }

    public function styles(Worksheet $sheet): void
    {
        $sheet->getStyle('A1:B1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF374151']],
        ]);
        $sheet->getStyle('B2:B100')->getNumberFormat()->setFormatCode('#,##0.00');
    }
}
