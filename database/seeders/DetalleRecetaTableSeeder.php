<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DetalleRecetaTableSeeder extends Seeder
{
    public function run(): void
    {
        $detalles = [
            [
                'ID_FARMACIA' => 'F001',
                'CODIGO_MEDICAMENTOS' => 'MED001',
                'DOSIS' => '1 tableta cada 8 horas',
                'SUBTOTAL' => 16.50
            ],
            [
                'ID_FARMACIA' => 'F001',
                'CODIGO_MEDICAMENTOS' => 'MED002',
                'DOSIS' => '1 tableta cada 6 horas',
                'SUBTOTAL' => 35.00
            ],
            [
                'ID_FARMACIA' => 'F002',
                'CODIGO_MEDICAMENTOS' => 'MED003',
                'DOSIS' => '1 cápsula cada 8 horas',
                'SUBTOTAL' => 36.90
            ],
            [
                'ID_FARMACIA' => 'F002',
                'CODIGO_MEDICAMENTOS' => 'MED004',
                'DOSIS' => '1 tableta cada 12 horas',
                'SUBTOTAL' => 31.20
            ],
            [
                'ID_FARMACIA' => 'F003',
                'CODIGO_MEDICAMENTOS' => 'MED005',
                'DOSIS' => '1/2 tableta cada 12 horas',
                'SUBTOTAL' => 44.80
            ],
            [
                'ID_FARMACIA' => 'F001',
                'CODIGO_MEDICAMENTOS' => 'MED010',
                'DOSIS' => '1 ampolla cada 12 horas',
                'SUBTOTAL' => 37.50
            ],
            [
                'ID_FARMACIA' => 'F001',
                'CODIGO_MEDICAMENTOS' => 'MED011',
                'DOSIS' => '1 tableta cada 8 horas',
                'SUBTOTAL' => 42.90
            ],
            [
                'ID_FARMACIA' => 'F002',
                'CODIGO_MEDICAMENTOS' => 'MED012',
                'DOSIS' => '5000 UI cada 8 horas',
                'SUBTOTAL' => 76.80
            ],
            [
                'ID_FARMACIA' => 'F003',
                'CODIGO_MEDICAMENTOS' => 'MED013',
                'DOSIS' => '0.5 mg según necesidad',
                'SUBTOTAL' => 25.60
            ],
            [
                'ID_FARMACIA' => 'F001',
                'CODIGO_MEDICAMENTOS' => 'MED015',
                'DOSIS' => '1 litro según necesidad',
                'SUBTOTAL' => 8.20
            ],
        ];

        DB::table('DETALLE_RECETA')->insert($detalles);
    }
}
