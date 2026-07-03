<?php

namespace App\Exports;

use App\Models\Devolucion;
use App\Models\Egreso;
use App\Models\PagoCuenta;
use App\Models\VentaFarmacia;
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
                // El comprobante del cobro es su recibo correlativo (PAGO-AAAAMMDD-NNN).
                // Si el pago trae referencia externa (transferencia/tarjeta), se conserva
                // entre paréntesis para conciliar el nro de operación.
                'comprobante' => $p->referencia ? $p->id.' (Ref. '.$p->referencia.')' : $p->id,
                'usuario' => $p->user->name ?? 'Sistema',
                'monto' => $p->monto,
            ]);

        $farmacia = VentaFarmacia::with('usuario')
            ->where('estado', 'COMPLETADA')
            ->whereBetween('fecha_venta', [$this->inicio, $this->fin])
            ->get()
            ->map(fn ($v) => [
                'fecha' => $v->fecha_venta,
                'tipo' => 'Ingreso',
                'categoria' => 'Venta farmacia',
                'descripcion' => 'Venta '.$v->codigo_venta.' - '.($v->cliente ?: 'Consumidor final'),
                'metodo_pago' => ucfirst($v->metodo_pago),
                'comprobante' => $v->codigo_venta,
                'usuario' => $v->usuario->name ?? 'N/A',
                'monto' => $v->total,
            ]);

        // Devoluciones (NC): contra-ingreso. Van en la columna Ingreso con signo
        // NEGATIVO para que la suma de la columna dé el ingreso neto del período
        // (no son egresos/gastos). Solo las vigentes: una NC anulada no movió caja.
        $devoluciones = Devolucion::with('cuentaCobro.paciente', 'user')
            ->vigentes()
            ->whereBetween('created_at', [$this->inicio, $this->fin])
            ->get()
            ->map(fn ($d) => [
                'fecha' => $d->created_at,
                'tipo' => 'Ingreso',
                'categoria' => 'Devolución (NC)',
                'descripcion' => 'Devolución del recibo '.$d->pago_cuenta_id.' - '
                    .($d->cuentaCobro?->paciente?->nombre ?? 'N/A').' - '.$d->motivo,
                'metodo_pago' => $d->metodo_devolucion_label,
                'comprobante' => $d->referencia ? $d->id.' (Ref. '.$d->referencia.')' : $d->id,
                'usuario' => $d->user->name ?? 'N/A',
                'monto' => '-'.$d->monto,
            ]);

        // Devoluciones de FARMACIA (ventas anuladas por anulado_at): fila de RASTRO
        // sin monto en las columnas — la venta anulada ya quedó excluida de la
        // sección de ingresos (filtro COMPLETADA), así que un monto negativo aquí
        // descontaría dos veces. El importe devuelto va en la descripción.
        $anuladasFarmacia = VentaFarmacia::with('anuladoPor')
            ->where('estado', 'ANULADA')
            ->whereBetween('anulado_at', [$this->inicio, $this->fin])
            ->get()
            ->map(fn ($v) => [
                'fecha' => $v->anulado_at,
                'tipo' => 'Ingreso',
                'categoria' => 'Devolución farmacia',
                'descripcion' => 'Venta '.$v->codigo_venta.' ANULADA — Bs '.number_format((float) $v->total, 2)
                    .' devueltos, stock reingresado ('.($v->motivo_anulacion ?? 'sin motivo').'). '
                    .'La venta ya no suma en los ingresos de este libro.',
                'metodo_pago' => ucfirst($v->metodo_pago),
                'comprobante' => $v->codigo_venta,
                'usuario' => $v->anuladoPor->name ?? 'N/A',
                'monto' => '',
            ]);

        // Los anulados no movieron caja: se excluyen del libro de flujo de efectivo.
        $egresos = Egreso::with('user')
            ->vigentes()
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

        return $ingresos->concat($farmacia)->concat($devoluciones)->concat($anuladasFarmacia)->concat($egresos)->sortBy('fecha')->values();
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
