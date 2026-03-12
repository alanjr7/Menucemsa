<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Test the controller logic
$productos = App\Models\Medicamentos::with('detalleMedicamentos')
    ->get()
    ->map(function ($medicamento) {
        $detalle = $medicamento->detalleMedicamentos->first();
        return [
            'id' => $medicamento->CODIGO,
            'nombre' => $medicamento->DESCRIPCION,
            'precio' => $medicamento->PRECIO,
            'categoria' => $detalle?->TIPO ?? 'Medicamento',
            'laboratorio' => $detalle?->LABORATORIO ?? 'N/A',
            'fecha_vencimiento' => $detalle?->FECHA_VENCIMIENTO ?? 'N/A',
            'stock' => 100,
            'codigo_barras' => $medicamento->CODIGO,
            'lote' => 'LOT-' . $medicamento->CODIGO,
            'requerimiento' => $detalle?->REQUERIMIENTO ?? 'Normal'
        ];
    });

echo "Productos count: " . $productos->count() . "\n";
echo "Productos data:\n";
print_r($productos->toArray());
