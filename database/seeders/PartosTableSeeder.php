<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PartosTableSeeder extends Seeder
{
    public function run(): void
    {
        $partos = [
            [
                'nro' => 'PAR001',
                'tipo' => 'Cesárea',
                'observaciones' => 'Cesárea de emergencia por sufrimiento fetal',
                'id_hospitalizacion' => 'HOSP002',
                'nro_cirugia' => 'CIR002'
            ],
            [
                'nro' => 'PAR002',
                'tipo' => 'Natural',
                'observaciones' => 'Parto vaginal sin complicaciones',
                'id_hospitalizacion' => 'HOSP002',
                'nro_cirugia' => null
            ],
            [
                'nro' => 'PAR003',
                'tipo' => 'Cesárea',
                'observaciones' => 'Cesárea programada por presentación pélvica',
                'id_hospitalizacion' => 'HOSP002',
                'nro_cirugia' => 'CIR014'
            ],
            [
                'nro' => 'PAR004',
                'tipo' => 'Natural',
                'observaciones' => 'Parto con episiotomía',
                'id_hospitalizacion' => 'HOSP002',
                'nro_cirugia' => null
            ],
            [
                'nro' => 'PAR005',
                'tipo' => 'Cesárea',
                'observaciones' => 'Cesárea por distocia de presentación',
                'id_hospitalizacion' => 'HOSP002',
                'nro_cirugia' => 'CIR002'
            ],
            [
                'nro' => 'PAR006',
                'tipo' => 'Natural',
                'observaciones' => 'Parto pretérmino - 36 semanas',
                'id_hospitalizacion' => 'HOSP002',
                'nro_cirugia' => null
            ],
            [
                'nro' => 'PAR007',
                'tipo' => 'Natural',
                'observaciones' => 'Parto gemelar - ambos vivos',
                'id_hospitalizacion' => 'HOSP002',
                'nro_cirugia' => null
            ],
            [
                'nro' => 'PAR008',
                'tipo' => 'Cesárea',
                'observaciones' => 'Cesárea por placenta previa',
                'id_hospitalizacion' => 'HOSP002',
                'nro_cirugia' => 'CIR002'
            ],
            [
                'nro' => 'PAR009',
                'tipo' => 'Natural',
                'observaciones' => 'Parto en casa - trasladado al hospital',
                'id_hospitalizacion' => 'HOSP002',
                'nro_cirugia' => null
            ],
            [
                'nro' => 'PAR010',
                'tipo' => 'Cesárea',
                'observaciones' => 'Cesárea por hipertensión arterial severa',
                'id_hospitalizacion' => 'HOSP002',
                'nro_cirugia' => 'CIR002'
            ],
        ];

        DB::table('partos')->insert($partos);
    }
}
