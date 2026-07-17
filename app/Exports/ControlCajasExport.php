<?php

namespace App\Exports;

use App\Models\CajaSession;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ControlCajasExport implements FromCollection, WithHeadings, WithMapping
{
    protected $filtros;

    public function __construct($filtros)
    {
        $this->filtros = $filtros;
    }

    public function collection()
    {
        $query = CajaSession::with(['user', 'movimientos']);
        // Aplicar filtros similares al controlador...
        return $query->get();
    }

    public function headings(): array
    {
        return ['ID', 'Cajero', 'Estado', 'Apertura', 'Cierre', 'M. Inicial', 'M. Final', 'Ingresos', 'Egresos', 'Esperado'];
    }

    public function map($caja): array
    {
        $ingresos = $caja->movimientos()->where('tipo', 'ingreso')->where('concepto', 'like', 'Cobro%')->sum('monto');
        $egresos = $caja->movimientos()->where('tipo', 'egreso')->where('concepto', '!=', 'Cierre de caja')->sum('monto');
        $esperado = $caja->monto_inicial + $ingresos - $egresos;

        return [
            $caja->id,
            $caja->user->name,
            ucfirst($caja->estado),
            $caja->fecha_apertura->format('d/m/Y H:i'),
            $caja->fecha_cierre ? $caja->fecha_cierre->format('d/m/Y H:i') : '-',
            $caja->monto_inicial,
            $caja->monto_final ?? 0,
            $ingresos,
            $egresos,
            $esperado
        ];
    }
}
