<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MetodoPagosTableSeeder extends Seeder
{
    public function run(): void
    {
        $metodo_pagos = [
            [
                'id' => 'MP001',
                'nro_pago' => 1,
                'tipo' => 'Efectivo'
            ],
            [
                'id' => 'MP002',
                'nro_pago' => 2,
                'tipo' => 'Tarjeta de crédito'
            ],
            [
                'id' => 'MP003',
                'nro_pago' => 3,
                'tipo' => 'Transferencia bancaria'
            ],
            [
                'id' => 'MP004',
                'nro_pago' => 4,
                'tipo' => 'Efectivo'
            ],
            [
                'id' => 'MP005',
                'nro_pago' => 5,
                'tipo' => 'Tarjeta de débito'
            ],
            [
                'id' => 'MP006',
                'nro_pago' => 6,
                'tipo' => 'Seguro médico'
            ],
            [
                'id' => 'MP007',
                'nro_pago' => 7,
                'tipo' => 'Efectivo'
            ],
            [
                'id' => 'MP008',
                'nro_pago' => 8,
                'tipo' => 'Tarjeta de crédito'
            ],
            [
                'id' => 'MP009',
                'nro_pago' => 9,
                'tipo' => 'Efectivo'
            ],
            [
                'id' => 'MP010',
                'nro_pago' => 10,
                'tipo' => 'Transferencia bancaria'
            ],
            [
                'id' => 'MP011',
                'nro_pago' => 11,
                'tipo' => 'Seguro médico'
            ],
            [
                'id' => 'MP012',
                'nro_pago' => 12,
                'tipo' => 'Tarjeta de débito'
            ],
            [
                'id' => 'MP013',
                'nro_pago' => 13,
                'tipo' => 'Efectivo'
            ],
            [
                'id' => 'MP014',
                'nro_pago' => 14,
                'tipo' => 'Transferencia bancaria'
            ],
            [
                'id' => 'MP015',
                'nro_pago' => 15,
                'tipo' => 'Seguro médico'
            ],
            [
                'id' => 'MP016',
                'nro_pago' => 16,
                'tipo' => 'Pendiente'
            ],
            [
                'id' => 'MP017',
                'nro_pago' => 17,
                'tipo' => 'Pendiente'
            ],
            [
                'id' => 'MP018',
                'nro_pago' => 18,
                'tipo' => 'Pendiente'
            ],
            [
                'id' => 'MP019',
                'nro_pago' => 19,
                'tipo' => 'Pendiente'
            ],
            [
                'id' => 'MP020',
                'nro_pago' => 20,
                'tipo' => 'Pendiente'
            ],
        ];

        DB::table('metodo_pagos')->insert($metodo_pagos);
    }
}
