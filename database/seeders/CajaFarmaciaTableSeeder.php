<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CajaFarmaciaTableSeeder extends Seeder
{
    public function run(): void
    {
        $cajas = [
            [
                'CODIGO' => 'CF001',
                'DETALLE' => 'Caja Farmacia Principal - Turno Mañana',
                'TOTAL' => 1250.75,
                'ID_CAJA' => 'C001'
            ],
            [
                'CODIGO' => 'CF002',
                'DETALLE' => 'Caja Farmacia Principal - Turno Tarde',
                'TOTAL' => 980.50,
                'ID_CAJA' => 'C002'
            ],
            [
                'CODIGO' => 'CF003',
                'DETALLE' => 'Caja Farmacia Emergencia',
                'TOTAL' => 650.25,
                'ID_CAJA' => 'C003'
            ],
            [
                'CODIGO' => 'CF004',
                'DETALLE' => 'Caja Farmacia Piso 1',
                'TOTAL' => 450.80,
                'ID_CAJA' => 'C004'
            ],
            [
                'CODIGO' => 'CF005',
                'DETALLE' => 'Caja Farmacia Piso 2',
                'TOTAL' => 320.60,
                'ID_CAJA' => 'C005'
            ],
            [
                'CODIGO' => 'CF006',
                'DETALLE' => 'Caja Farmacia Quirófano',
                'TOTAL' => 890.40,
                'ID_CAJA' => 'C006'
            ],
        ];

        DB::table('CAJA_FARMACIA')->insert($cajas);
    }
}
