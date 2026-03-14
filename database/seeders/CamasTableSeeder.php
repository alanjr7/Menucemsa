<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CamasTableSeeder extends Seeder
{
    public function run(): void
    {
        $camas = [
            // HAB001 - Habitación individual
            [
                'nro' => 1,
                'id_habitacion' => 'HAB001',
                'disponibilidad' => 'Ocupada',
                'tipo' => 'Cama individual'
            ],
            // HAB002 - Habitación maternity
            [
                'nro' => 1,
                'id_habitacion' => 'HAB002',
                'disponibilidad' => 'Ocupada',
                'tipo' => 'Cama maternity'
            ],
            [
                'nro' => 2,
                'id_habitacion' => 'HAB002',
                'disponibilidad' => 'Disponible',
                'tipo' => 'Cama maternity'
            ],
            // HAB003 - Habitación UCI
            [
                'nro' => 1,
                'id_habitacion' => 'HAB003',
                'disponibilidad' => 'Ocupada',
                'tipo' => 'Cama UCI'
            ],
            // HAB004 - Habitación quirúrgica
            [
                'nro' => 1,
                'id_habitacion' => 'HAB004',
                'disponibilidad' => 'Ocupada',
                'tipo' => 'Cama quirúrgica'
            ],
            // HAB005 - Habitación traumatología
            [
                'nro' => 1,
                'id_habitacion' => 'HAB005',
                'disponibilidad' => 'Ocupada',
                'tipo' => 'Cama traumatología'
            ],
            [
                'nro' => 2,
                'id_habitacion' => 'HAB005',
                'disponibilidad' => 'Disponible',
                'tipo' => 'Cama traumatología'
            ],
            // HAB006 - Habitación neumología
            [
                'nro' => 1,
                'id_habitacion' => 'HAB006',
                'disponibilidad' => 'Ocupada',
                'tipo' => 'Cama neumología'
            ],
            // HAB007 - Habitación quemados
            [
                'nro' => 1,
                'id_habitacion' => 'HAB007',
                'disponibilidad' => 'Ocupada',
                'tipo' => 'Cama quemados'
            ],
            // HAB008 - Habitación cardiología
            [
                'nro' => 1,
                'id_habitacion' => 'HAB008',
                'disponibilidad' => 'Ocupada',
                'tipo' => 'Cama cardiología'
            ],
            // HAB009 - Habitación observación
            [
                'nro' => 1,
                'id_habitacion' => 'HAB009',
                'disponibilidad' => 'Ocupada',
                'tipo' => 'Cama observación'
            ],
            [
                'nro' => 2,
                'id_habitacion' => 'HAB009',
                'disponibilidad' => 'Disponible',
                'tipo' => 'Cama observación'
            ],
            // HAB010 - Habitación gastroenterología
            [
                'nro' => 1,
                'id_habitacion' => 'HAB010',
                'disponibilidad' => 'Ocupada',
                'tipo' => 'Cama gastroenterología'
            ],
            // HAB011 - Habitación pediatría
            [
                'nro' => 1,
                'id_habitacion' => 'HAB011',
                'disponibilidad' => 'Ocupada',
                'tipo' => 'Cama pediátrica'
            ],
            [
                'nro' => 2,
                'id_habitacion' => 'HAB011',
                'disponibilidad' => 'Disponible',
                'tipo' => 'Cama pediátrica'
            ],
            // HAB012 - Habitación neurología
            [
                'nro' => 1,
                'id_habitacion' => 'HAB012',
                'disponibilidad' => 'Ocupada',
                'tipo' => 'Cama neurología'
            ],
            // HAB013 - Habitación individual disponible
            [
                'nro' => 1,
                'id_habitacion' => 'HAB013',
                'disponibilidad' => 'Disponible',
                'tipo' => 'Cama individual'
            ],
            // HAB014 - Habitación doble disponible
            [
                'nro' => 1,
                'id_habitacion' => 'HAB014',
                'disponibilidad' => 'Disponible',
                'tipo' => 'Cama doble'
            ],
            [
                'nro' => 2,
                'id_habitacion' => 'HAB014',
                'disponibilidad' => 'Disponible',
                'tipo' => 'Cama doble'
            ],
            // HAB015 - Habitación suite en mantenimiento
            [
                'nro' => 1,
                'id_habitacion' => 'HAB015',
                'disponibilidad' => 'Mantenimiento',
                'tipo' => 'Cama suite'
            ],
            [
                'nro' => 2,
                'id_habitacion' => 'HAB015',
                'disponibilidad' => 'Mantenimiento',
                'tipo' => 'Cama suite'
            ],
            // HAB016 - Habitación individual disponible
            [
                'nro' => 1,
                'id_habitacion' => 'HAB016',
                'disponibilidad' => 'Disponible',
                'tipo' => 'Cama individual'
            ],
            // HAB017 - Habitación doble disponible
            [
                'nro' => 1,
                'id_habitacion' => 'HAB017',
                'disponibilidad' => 'Disponible',
                'tipo' => 'Cama doble'
            ],
            [
                'nro' => 2,
                'id_habitacion' => 'HAB017',
                'disponibilidad' => 'Disponible',
                'tipo' => 'Cama doble'
            ],
            // HAB018 - Habitación UCI ocupada
            [
                'nro' => 1,
                'id_habitacion' => 'HAB018',
                'disponibilidad' => 'Ocupada',
                'tipo' => 'Cama UCI'
            ],
            // HAB019 - Habitación nefrología ocupada
            [
                'nro' => 1,
                'id_habitacion' => 'HAB019',
                'disponibilidad' => 'Ocupada',
                'tipo' => 'Cama nefrología'
            ],
            // HAB020 - Habitación endocrinología ocupada
            [
                'nro' => 1,
                'id_habitacion' => 'HAB020',
                'disponibilidad' => 'Ocupada',
                'tipo' => 'Cama endocrinología'
            ],
        ];

        DB::table('camas')->insert($camas);
    }
}
