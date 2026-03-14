<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TiposCirugiaTableSeeder extends Seeder
{
    public function run(): void
    {
        $tipos_cirugia = [
            [
                'id' => 1,
                'nombre' => 'menor',
                'descripcion' => 'Cirugía menor - 60 minutos',
                'duracion_minutos' => 60,
                'costo_base' => 500.00,
                'costo_minuto_extra' => 10.00,
                'activo' => true
            ],
            [
                'id' => 2,
                'nombre' => 'mediana',
                'descripcion' => 'Cirugía mediana - 90 minutos',
                'duracion_minutos' => 90,
                'costo_base' => 800.00,
                'costo_minuto_extra' => 15.00,
                'activo' => true
            ],
            [
                'id' => 3,
                'nombre' => 'mayor',
                'descripcion' => 'Cirugía mayor - 120 minutos',
                'duracion_minutos' => 120,
                'costo_base' => 1200.00,
                'costo_minuto_extra' => 20.00,
                'activo' => true
            ],
            [
                'id' => 4,
                'nombre' => 'ambulatoria',
                'descripcion' => 'Cirugía ambulatoria - 45 minutos',
                'duracion_minutos' => 45,
                'costo_base' => 300.00,
                'costo_minuto_extra' => 8.00,
                'activo' => true
            ],
            [
                'id' => 5,
                'nombre' => 'pediatrica',
                'descripcion' => 'Cirugía pediátrica - 75 minutos',
                'duracion_minutos' => 75,
                'costo_base' => 600.00,
                'costo_minuto_extra' => 12.00,
                'activo' => true
            ],
            [
                'id' => 6,
                'nombre' => 'urgencia',
                'descripcion' => 'Cirugía de urgencia - variable',
                'duracion_minutos' => 90,
                'costo_base' => 1000.00,
                'costo_minuto_extra' => 25.00,
                'activo' => true
            ],
            [
                'id' => 7,
                'nombre' => 'laparoscopica',
                'descripcion' => 'Cirugía laparoscópica - 120 minutos',
                'duracion_minutos' => 120,
                'costo_base' => 1500.00,
                'costo_minuto_extra' => 30.00,
                'activo' => true
            ],
            [
                'id' => 8,
                'nombre' => 'robotica',
                'descripcion' => 'Cirugía robótica - 150 minutos',
                'duracion_minutos' => 150,
                'costo_base' => 2500.00,
                'costo_minuto_extra' => 40.00,
                'activo' => false
            ],
            [
                'id' => 9,
                'nombre' => 'endovascular',
                'descripcion' => 'Cirugía endovascular - 180 minutos',
                'duracion_minutos' => 180,
                'costo_base' => 3000.00,
                'costo_minuto_extra' => 50.00,
                'activo' => true
            ],
            [
                'id' => 10,
                'nombre' => 'microcirugia',
                'descripcion' => 'Microcirugía - 240 minutos',
                'duracion_minutos' => 240,
                'costo_base' => 4000.00,
                'costo_minuto_extra' => 60.00,
                'activo' => true
            ],
        ];

        DB::table('tipos_cirugia')->insert($tipos_cirugia);
    }
}
