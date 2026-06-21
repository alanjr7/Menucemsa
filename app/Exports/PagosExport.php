<?php

namespace App\Exports;

use App\Models\PagoCuenta;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

/**
 * Historial de pagos (recibos PAGO-) homologable al listado de la pantalla.
 *
 * Sin filtros exporta TODOS los pagos (todas las cajas, todas las fechas); con
 * filtros exporta exactamente la vista dinámica que ve el usuario. Reusa el scope
 * `filtrarHistorial` como fuente única de filtrado.
 */
class PagosExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithTitle
{
    /** @param array<string,mixed> $filtros */
    public function __construct(protected array $filtros) {}

    public function title(): string
    {
        return 'Historial de Pagos';
    }

    public function collection()
    {
        return PagoCuenta::with(['cuentaCobro.paciente', 'user'])
            ->filtrarHistorial($this->filtros)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return ['Recibo', 'Cuenta', 'Fecha', 'Paciente', 'CI/Doc', 'Método', 'Referencia', 'Monto (Bs)', 'Cajero', 'Caja N°'];
    }

    public function map($pago): array
    {
        $paciente = $pago->cuentaCobro?->paciente;

        return [
            $pago->id,
            $pago->cuenta_cobro_id,
            $pago->created_at->format('d/m/Y H:i'),
            $paciente?->nombre ?? 'N/A',
            $paciente?->ci ?? $paciente?->temp_code ?? 'N/A',
            $pago->metodo_pago_label,
            $pago->referencia ?? '',
            $pago->monto,
            $pago->user?->name ?? 'Sistema',
            $pago->caja_session_id ?? '',
        ];
    }
}
