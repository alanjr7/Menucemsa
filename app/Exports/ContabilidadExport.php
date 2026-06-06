<?php

namespace App\Exports;

use App\Models\Egreso;
use App\Models\PagoCuenta;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class ContabilidadExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithTitle
{
    public function __construct(
        protected Carbon $inicio,
        protected Carbon $fin
    ) {}

    public function title(): string
    {
        return 'Flujo de Caja';
    }

    public function collection()
    {
        $ingresos = PagoCuenta::with('cuentaCobro.paciente', 'user')
            ->whereBetween('created_at', [$this->inicio, $this->fin])
            ->get()
            ->map(fn ($p) => [
                'fecha' => $p->created_at,
                'tipo' => 'Ingreso',
                'categoria' => 'Cobro',
                'descripcion' => 'Pago cuenta '.$p->cuenta_cobro_id.' - '.($p->cuentaCobro?->paciente?->nombre ?? 'N/A'),
                'metodo_pago' => $p->metodo_pago_label,
                'comprobante' => $p->referencia,
                'usuario' => $p->user->name ?? 'Sistema',
                'monto' => $p->monto,
            ]);

        $egresos = Egreso::with('user')
            ->entreFechas($this->inicio->toDateString(), $this->fin->toDateString())
            ->get()
            ->map(fn ($e) => [
                'fecha' => $e->fecha,
                'tipo' => 'Egreso',
                'categoria' => $e->categoria_label,
                'descripcion' => $e->descripcion.($e->proveedor ? ' ('.$e->proveedor.')' : ''),
                'metodo_pago' => $e->metodo_pago_label,
                'comprobante' => $e->comprobante_nro,
                'usuario' => $e->user->name ?? 'N/A',
                'monto' => $e->monto,
            ]);

        return $ingresos->concat($egresos)->sortBy('fecha')->values();
    }

    public function headings(): array
    {
        return ['Fecha', 'Tipo', 'Categoría', 'Descripción', 'Método de Pago', 'Comprobante', 'Registrado por', 'Ingreso (Bs)', 'Egreso (Bs)'];
    }

    public function map($row): array
    {
        $esIngreso = $row['tipo'] === 'Ingreso';

        return [
            Carbon::parse($row['fecha'])->format('d/m/Y H:i'),
            $row['tipo'],
            $row['categoria'],
            $row['descripcion'],
            $row['metodo_pago'],
            $row['comprobante'] ?? '',
            $row['usuario'],
            $esIngreso ? $row['monto'] : '',
            $esIngreso ? '' : $row['monto'],
        ];
    }
}
