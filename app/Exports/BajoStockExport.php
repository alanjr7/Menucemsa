<?php

namespace App\Exports;

use App\Models\AlmacenStock;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class BajoStockExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    public function collection()
    {
        return AlmacenStock::with(['lote.catalogo'])
            ->bajoStock()
            ->whereHas('lote.catalogo', fn ($q) => $q->where('activo', true))
            ->orderBy('cantidad_actual')
            ->get();
    }

    public function headings(): array
    {
        return ['Medicamento/Insumo', 'Lote', 'Ubicación', 'Stock Actual', 'Mínimo', 'Estado'];
    }

    public function map($stock): array
    {
        return [
            $stock->lote->catalogo->nombre ?? 'N/A',
            $stock->lote->codigo_lote ?? '-',
            $stock->ubicacion_label,
            $stock->cantidad_actual,
            $stock->stock_minimo,
            $stock->cantidad_actual <= 0 ? 'Agotado' : 'Bajo Stock',
        ];
    }
}
