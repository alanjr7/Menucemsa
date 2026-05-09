<?php

namespace Database\Seeders;

use App\Models\Camilla;
use Illuminate\Database\Seeder;

class CamillaSeeder extends Seeder
{
    public function run(): void
    {
        $camillasEmergencia = [
            ['nombre' => 'Camilla Emergencia 1', 'codigo' => 'EMER-CAM-001', 'precio_por_hora' => 25.00],
            ['nombre' => 'Camilla Emergencia 2', 'codigo' => 'EMER-CAM-002', 'precio_por_hora' => 25.00],
            ['nombre' => 'Camilla Emergencia 3', 'codigo' => 'EMER-CAM-003', 'precio_por_hora' => 25.00],
            ['nombre' => 'Camilla Emergencia 4', 'codigo' => 'EMER-CAM-004', 'precio_por_hora' => 25.00],
            ['nombre' => 'Camilla Emergencia 5', 'codigo' => 'EMER-CAM-005', 'precio_por_hora' => 25.00],
            ['nombre' => 'Camilla Emergencia 6', 'codigo' => 'EMER-CAM-006', 'precio_por_hora' => 25.00],
            ['nombre' => 'Camilla Emergencia 7', 'codigo' => 'EMER-CAM-007', 'precio_por_hora' => 25.00],
            ['nombre' => 'Camilla Emergencia 8', 'codigo' => 'EMER-CAM-008', 'precio_por_hora' => 25.00],
            ['nombre' => 'Camilla Emergencia 9', 'codigo' => 'EMER-CAM-009', 'precio_por_hora' => 25.00],
            ['nombre' => 'Camilla Emergencia 10', 'codigo' => 'EMER-CAM-010', 'precio_por_hora' => 25.00],
        ];

        $camillasUti = [
            ['nombre' => 'Camilla UTI 1', 'codigo' => 'UTI-CAM-001', 'precio_por_hora' => 75.00],
            ['nombre' => 'Camilla UTI 2', 'codigo' => 'UTI-CAM-002', 'precio_por_hora' => 75.00],
            ['nombre' => 'Camilla UTI 3', 'codigo' => 'UTI-CAM-003', 'precio_por_hora' => 75.00],
            ['nombre' => 'Camilla UTI 4', 'codigo' => 'UTI-CAM-004', 'precio_por_hora' => 75.00],
            ['nombre' => 'Camilla UTI 5', 'codigo' => 'UTI-CAM-005', 'precio_por_hora' => 75.00],
        ];

        foreach ($camillasEmergencia as $camilla) {
            Camilla::create([
                'nombre'          => $camilla['nombre'],
                'codigo'          => $camilla['codigo'],
                'precio_por_hora' => $camilla['precio_por_hora'],
                'area'            => 'emergencia',
                'activa'          => true,
            ]);
        }

        foreach ($camillasUti as $camilla) {
            Camilla::create([
                'nombre'          => $camilla['nombre'],
                'codigo'          => $camilla['codigo'],
                'precio_por_hora' => $camilla['precio_por_hora'],
                'area'            => 'uti',
                'activa'          => true,
            ]);
        }
    }
}
