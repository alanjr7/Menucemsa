<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HabitacionesTableSeeder extends Seeder
{
    public function run(): void
    {
        $habitaciones = [
            [
                'id' => 'HAB001',
                'estado' => 'Ocupada',
                'detalle' => 'Habitación individual',
                'capacidad' => 1,
                'id_hospitalizacion' => 'HOSP001'
            ],
            [
                'id' => 'HAB002',
                'estado' => 'Ocupada',
                'detalle' => 'Habitación maternity',
                'capacidad' => 2,
                'id_hospitalizacion' => 'HOSP002'
            ],
            [
                'id' => 'HAB003',
                'estado' => 'Ocupada',
                'detalle' => 'Habitación UCI',
                'capacidad' => 1,
                'id_hospitalizacion' => 'HOSP003'
            ],
            [
                'id' => 'HAB004',
                'estado' => 'Ocupada',
                'detalle' => 'Habitación quirúrgica',
                'capacidad' => 1,
                'id_hospitalizacion' => 'HOSP004'
            ],
            [
                'id' => 'HAB005',
                'estado' => 'Ocupada',
                'detalle' => 'Habitación traumatología',
                'capacidad' => 2,
                'id_hospitalizacion' => 'HOSP005'
            ],
            [
                'id' => 'HAB006',
                'estado' => 'Ocupada',
                'detalle' => 'Habitación neumología',
                'capacidad' => 1,
                'id_hospitalizacion' => 'HOSP006'
            ],
            [
                'id' => 'HAB007',
                'estado' => 'Ocupada',
                'detalle' => 'Habitación quemados',
                'capacidad' => 1,
                'id_hospitalizacion' => 'HOSP007'
            ],
            [
                'id' => 'HAB008',
                'estado' => 'Ocupada',
                'detalle' => 'Habitación cardiología',
                'capacidad' => 1,
                'id_hospitalizacion' => 'HOSP008'
            ],
            [
                'id' => 'HAB009',
                'estado' => 'Ocupada',
                'detalle' => 'Habitación observación',
                'capacidad' => 2,
                'id_hospitalizacion' => 'HOSP009'
            ],
            [
                'id' => 'HAB010',
                'estado' => 'Ocupada',
                'detalle' => 'Habitación gastroenterología',
                'capacidad' => 1,
                'id_hospitalizacion' => 'HOSP010'
            ],
            [
                'id' => 'HAB011',
                'estado' => 'Ocupada',
                'detalle' => 'Habitación pediatría',
                'capacidad' => 2,
                'id_hospitalizacion' => 'HOSP011'
            ],
            [
                'id' => 'HAB012',
                'estado' => 'Ocupada',
                'detalle' => 'Habitación neurología',
                'capacidad' => 1,
                'id_hospitalizacion' => 'HOSP012'
            ],
            [
                'id' => 'HAB013',
                'estado' => 'Disponible',
                'detalle' => 'Habitación individual',
                'capacidad' => 1,
                'id_hospitalizacion' => null
            ],
            [
                'id' => 'HAB014',
                'estado' => 'Disponible',
                'detalle' => 'Habitación doble',
                'capacidad' => 2,
                'id_hospitalizacion' => null
            ],
            [
                'id' => 'HAB015',
                'estado' => 'Mantenimiento',
                'detalle' => 'Habitación suite',
                'capacidad' => 2,
                'id_hospitalizacion' => null
            ],
            [
                'id' => 'HAB016',
                'estado' => 'Disponible',
                'detalle' => 'Habitación individual',
                'capacidad' => 1,
                'id_hospitalizacion' => null
            ],
            [
                'id' => 'HAB017',
                'estado' => 'Disponible',
                'detalle' => 'Habitación doble',
                'capacidad' => 2,
                'id_hospitalizacion' => null
            ],
            [
                'id' => 'HAB018',
                'estado' => 'Ocupada',
                'detalle' => 'Habitación UCI',
                'capacidad' => 1,
                'id_hospitalizacion' => 'HOSP013'
            ],
            [
                'id' => 'HAB019',
                'estado' => 'Ocupada',
                'detalle' => 'Habitación nefrología',
                'capacidad' => 1,
                'id_hospitalizacion' => 'HOSP014'
            ],
            [
                'id' => 'HAB020',
                'estado' => 'Ocupada',
                'detalle' => 'Habitación endocrinología',
                'capacidad' => 1,
                'id_hospitalizacion' => 'HOSP015'
            ],
        ];

        DB::table('habitaciones')->insert($habitaciones);
    }
}
