<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EstadoCuentasTableSeeder extends Seeder
{
    public function run(): void
    {
        $estado_cuentas = [
            [
                'id' => 1,
                'id_hospitalizacion' => 'HOSP001',
                'fecha_apertura' => '2024-03-13',
                'fecha_cierre' => null,
                'total' => 8500.00,
                'estado' => 'Abierta'
            ],
            [
                'id' => 2,
                'id_hospitalizacion' => 'HOSP002',
                'fecha_apertura' => '2024-03-13',
                'fecha_cierre' => '2024-03-14',
                'total' => 4500.00,
                'estado' => 'Cerrada'
            ],
            [
                'id' => 3,
                'id_hospitalizacion' => 'HOSP003',
                'fecha_apertura' => '2024-03-14',
                'fecha_cierre' => null,
                'total' => 12000.00,
                'estado' => 'Abierta'
            ],
            [
                'id' => 4,
                'id_hospitalizacion' => 'HOSP004',
                'fecha_apertura' => '2024-03-14',
                'fecha_cierre' => null,
                'total' => 6800.00,
                'estado' => 'Abierta'
            ],
            [
                'id' => 5,
                'id_hospitalizacion' => 'HOSP005',
                'fecha_apertura' => '2024-03-14',
                'fecha_cierre' => null,
                'total' => 7500.00,
                'estado' => 'Abierta'
            ],
            [
                'id' => 6,
                'id_hospitalizacion' => 'HOSP006',
                'fecha_apertura' => '2024-03-14',
                'fecha_cierre' => '2024-03-15',
                'total' => 3200.00,
                'estado' => 'Cerrada'
            ],
            [
                'id' => 7,
                'id_hospitalizacion' => 'HOSP007',
                'fecha_apertura' => '2024-03-14',
                'fecha_cierre' => null,
                'total' => 9200.00,
                'estado' => 'Abierta'
            ],
            [
                'id' => 8,
                'id_hospitalizacion' => 'HOSP008',
                'fecha_apertura' => '2024-03-14',
                'fecha_cierre' => '2024-03-15',
                'total' => 2800.00,
                'estado' => 'Cerrada'
            ],
            [
                'id' => 9,
                'id_hospitalizacion' => 'HOSP009',
                'fecha_apertura' => '2024-03-14',
                'fecha_cierre' => '2024-03-15',
                'total' => 1500.00,
                'estado' => 'Cerrada'
            ],
            [
                'id' => 10,
                'id_hospitalizacion' => 'HOSP010',
                'fecha_apertura' => '2024-03-14',
                'fecha_cierre' => null,
                'total' => 4200.00,
                'estado' => 'Abierta'
            ],
            [
                'id' => 11,
                'id_hospitalizacion' => 'HOSP011',
                'fecha_apertura' => '2024-03-14',
                'fecha_cierre' => '2024-03-15',
                'total' => 1800.00,
                'estado' => 'Cerrada'
            ],
            [
                'id' => 12,
                'id_hospitalizacion' => 'HOSP012',
                'fecha_apertura' => '2024-03-14',
                'fecha_cierre' => '2024-03-15',
                'total' => 2200.00,
                'estado' => 'Cerrada'
            ],
            [
                'id' => 13,
                'id_hospitalizacion' => 'HOSP013',
                'fecha_apertura' => '2024-03-13',
                'fecha_cierre' => null,
                'total' => 15000.00,
                'estado' => 'Abierta'
            ],
            [
                'id' => 14,
                'id_hospitalizacion' => 'HOSP014',
                'fecha_apertura' => '2024-03-13',
                'fecha_cierre' => null,
                'total' => 5600.00,
                'estado' => 'Abierta'
            ],
            [
                'id' => 15,
                'id_hospitalizacion' => 'HOSP015',
                'fecha_apertura' => '2024-03-13',
                'fecha_cierre' => null,
                'total' => 3800.00,
                'estado' => 'Abierta'
            ],
        ];

        DB::table('estado_cuentas')->insert($estado_cuentas);
    }
}
