<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class AlmacenPlantillaExport implements FromArray, ShouldAutoSize, WithHeadings, WithTitle
{
    public function title(): string
    {
        return 'Plantilla Importacion';
    }

    public function headings(): array
    {
        return [
            'nombre', 'cantidad', 'tipo', 'unidad', 'stock_minimo',
            'proveedor', 'laboratorio', 'codigo_lote', 'fecha_vencimiento',
            'precio_compra', 'precio_venta', 'descripcion',
        ];
    }

    public function array(): array
    {
        return [
            ['Omeprazol 20mg cápsula', 100, 'medicamento', 'unidades', 20, 'Distribuidora ABC', 'Genérico', 'L-2026-001', '2027-12-31', 0.50, 1.00, 'Antiácido'],
            ['Omeprazol 20mg cápsula', 50, 'medicamento', 'unidades', 20, 'Farma SRL', 'Bagó', 'L-2026-002', '2026-10-31', 0.80, 1.50, ''],
            ['Omeprazol jarabe 40mg/5ml', 30, 'medicamento', 'frascos', 5, 'Farma SRL', 'Bagó', 'JBE-2026-01', '2026-09-30', 8.00, 14.00, 'Presentación líquida'],
            ['Gasa estéril', 50, 'insumo', 'unidades', 10, 'Insumos Médicos SA', '', '', '', 1.20, 2.00, ''],
        ];
    }
}
