<?php

namespace App\Exports;

use App\Models\CuentaCobro;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class HistorialSegurosExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $filtros;

    public function __construct($filtros)
    {
        $this->filtros = $filtros;
    }

    public function collection()
    {
        $query = CuentaCobro::with(['paciente', 'seguro', 'seguroAutorizadoPor'])
            ->whereNotNull('seguro_id')
            ->whereIn('seguro_estado', ['autorizado', 'rechazado']);

        // Aplicar los mismos filtros que en el controlador
        if (!empty($this->filtros['fecha_inicio'])) {
            $query->whereDate('created_at', '>=', $this->filtros['fecha_inicio']);
        }
        if (!empty($this->filtros['fecha_fin'])) {
            $query->whereDate('created_at', '<=', $this->filtros['fecha_fin']);
        }
        if (!empty($this->filtros['paciente'])) {
            $query->whereHas('paciente', function ($q) {
                $q->where('nombre', 'like', '%' . $this->filtros['paciente'] . '%')
                  ->orWhere('ci', 'like', '%' . $this->filtros['paciente'] . '%');
            });
        }
        if (!empty($this->filtros['seguro_id'])) {
            $query->where('seguro_id', $this->filtros['seguro_id']);
        }
        if (!empty($this->filtros['estado'])) {
            $query->where('seguro_estado', $this->filtros['estado']);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'Fecha',
            'Paciente',
            'CI Paciente',
            'Seguro',
            'Tipo de Atención',
            'Monto Total (Bs)',
            'Cobertura Seguro (Bs)',
            'Copago Paciente (Bs)',
            'Estado Seguro',
            'Autorizado Por',
            'Observaciones'
        ];
    }

    public function map($registro): array
    {
        return [
            $registro->created_at->format('d/m/Y H:i'),
            $registro->paciente?->nombre ?? 'N/A',
            $registro->paciente?->ci ?? $registro->paciente?->temp_code ?? 'N/A',
            $registro->seguro?->nombre_empresa ?? 'N/A',
            $registro->tipo_atencion_label,
            number_format($registro->total_calculado, 2, '.', ''),
            number_format($registro->seguro_monto_cobertura ?? 0, 2, '.', ''),
            number_format($registro->seguro_monto_paciente ?? 0, 2, '.', ''),
            ucfirst($registro->seguro_estado),
            $registro->seguroAutorizadoPor?->name ?? 'N/A',
            $registro->seguro_observaciones ?? ''
        ];
    }
}
