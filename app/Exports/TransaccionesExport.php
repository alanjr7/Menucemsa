<?php

namespace App\Exports;

use App\Models\CuentaCobro;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class TransaccionesExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $filtros;

    public function __construct($filtros)
    {
        $this->filtros = $filtros;
    }

    public function collection()
    {
        $query = CuentaCobro::with(['paciente', 'pagos', 'cajaSession.user']);

        if (!empty($this->filtros['fecha_inicio']) && !empty($this->filtros['fecha_fin'])) {
            $query->whereBetween('created_at', [$this->filtros['fecha_inicio'] . ' 00:00:00', $this->filtros['fecha_fin'] . ' 23:59:59']);
        }
        if (!empty($this->filtros['estado']) && $this->filtros['estado'] !== 'todos') {
            $query->where('estado', $this->filtros['estado']);
        }
        if (!empty($this->filtros['tipo_flujo']) && $this->filtros['tipo_flujo'] !== 'todos') {
            $query->where('es_emergencia', $this->filtros['tipo_flujo'] === 'emergencia');
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'ID Transacción',
            'Fecha',
            'Paciente',
            'CI Paciente',
            'Tipo Atención',
            'Flujo',
            'Total (Bs)',
            'Pagado (Bs)',
            'Saldo (Bs)',
            'Estado',
            'Cajero'
        ];
    }

    public function map($cuenta): array
    {
        return [
            $cuenta->id,
            $cuenta->created_at->format('d/m/Y H:i'),
            $cuenta->paciente->nombre ?? 'N/A',
            $cuenta->paciente_ci,
            $cuenta->tipo_atencion_label,
            $cuenta->es_emergencia ? 'Emergencia' : 'Normal',
            $cuenta->total_calculado,
            $cuenta->total_pagado,
            $cuenta->saldo_pendiente,
            $cuenta->estado_label,
            $cuenta->cajaSession->user->name ?? 'Sistema'
        ];
    }
}
