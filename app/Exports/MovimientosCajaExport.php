<?php

namespace App\Exports;

use App\Models\MovimientoCaja;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class MovimientosCajaExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $filtros;

    public function __construct($filtros)
    {
        $this->filtros = $filtros;
    }

    public function collection()
    {
        $query = MovimientoCaja::with(['cajaSession.user']);

        // Filtros dinámicos que vienen desde tus inputs de JS
        if (!empty($this->filtros['fecha_inicio'])) {
            $query->whereDate('created_at', '>=', $this->filtros['fecha_inicio']);
        }
        if (!empty($this->filtros['fecha_fin'])) {
            $query->whereDate('created_at', '<=', $this->filtros['fecha_fin']);
        }
        if (!empty($this->filtros['tipo_accion']) && $this->filtros['tipo_accion'] !== 'todos') {
            $query->where('tipo', $this->filtros['tipo_accion']);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function headings(): array
    {
        return ['ID', 'Fecha', 'Usuario', 'Tipo', 'Concepto', 'Monto (Bs)', 'Método Pago', 'Referencia'];
    }

    public function map($mov): array
    {
        return [
            $mov->id,
            $mov->created_at->format('d/m/Y H:i'),
            $mov->cajaSession->user->name ?? 'N/A',
            ucfirst($mov->tipo),
            $mov->concepto,
            $mov->monto,
            $mov->metodo_pago,
            $mov->referencia,
        ];
    }
}
