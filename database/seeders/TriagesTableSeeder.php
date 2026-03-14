<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TriagesTableSeeder extends Seeder
{
    public function run(): void
    {
        $triages = [
            [
                'id' => 'TRI001',
                'color' => 'Rojo',
                'descripcion' => 'Reanimación inmediata',
                'prioridad' => 'Alta',
                'id_usuario' => 22
            ],
            [
                'id' => 'TRI002',
                'color' => 'Naranja',
                'descripcion' => 'Emergencia urgente',
                'prioridad' => 'Alta',
                'id_usuario' => 22
            ],
            [
                'id' => 'TRI003',
                'color' => 'Amarillo',
                'descripcion' => 'Urgencia',
                'prioridad' => 'Media',
                'id_usuario' => 5
            ],
            [
                'id' => 'TRI004',
                'color' => 'Verde',
                'descripcion' => 'Urgencia menor',
                'prioridad' => 'Baja',
                'id_usuario' => 5
            ],
            [
                'id' => 'TRI005',
                'color' => 'Azul',
                'descripcion' => 'No urgente',
                'prioridad' => 'Baja',
                'id_usuario' => 4
            ],
            [
                'id' => 'TRI006',
                'color' => 'Rojo',
                'descripcion' => 'Paro cardiorrespiratorio',
                'prioridad' => 'Alta',
                'id_usuario' => 22
            ],
            [
                'id' => 'TRI007',
                'color' => 'Naranja',
                'descripcion' => 'Trauma severo',
                'prioridad' => 'Alta',
                'id_usuario' => 22
            ],
            [
                'id' => 'TRI008',
                'color' => 'Amarillo',
                'descripcion' => 'Dolor torácico',
                'prioridad' => 'Media',
                'id_usuario' => 5
            ],
            [
                'id' => 'TRI009',
                'color' => 'Verde',
                'descripcion' => 'Fractura simple',
                'prioridad' => 'Baja',
                'id_usuario' => 5
            ],
            [
                'id' => 'TRI010',
                'color' => 'Azul',
                'descripcion' => 'Consulta general',
                'prioridad' => 'Baja',
                'id_usuario' => 4
            ],
            [
                'id' => 'TRI011',
                'color' => 'Rojo',
                'descripcion' => 'Hemorragia severa',
                'prioridad' => 'Alta',
                'id_usuario' => 22
            ],
            [
                'id' => 'TRI012',
                'color' => 'Naranja',
                'descripcion' => 'Dificultad respiratoria',
                'prioridad' => 'Alta',
                'id_usuario' => 22
            ],
            [
                'id' => 'TRI013',
                'color' => 'Amarillo',
                'descripcion' => 'Dolor abdominal agudo',
                'prioridad' => 'Media',
                'id_usuario' => 5
            ],
            [
                'id' => 'TRI014',
                'color' => 'Verde',
                'descripcion' => 'Cefalea moderada',
                'prioridad' => 'Baja',
                'id_usuario' => 5
            ],
            [
                'id' => 'TRI015',
                'color' => 'Azul',
                'descripcion' => 'Control de presión',
                'prioridad' => 'Baja',
                'id_usuario' => 4
            ],
        ];

        DB::table('triages')->insert($triages);
    }
}
