<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TurnosTableSeeder extends Seeder
{
    public function run(): void
    {
        $turnos = [
            [
                'nro' => 'T001',
                'id_usuario' => 1,
                'hora_inicio' => '08:00:00',
                'hora_fin' => '16:00:00',
                'tipo' => 'Administrativo'
            ],
            [
                'nro' => 'T002',
                'id_usuario' => 2,
                'hora_inicio' => '08:00:00',
                'hora_fin' => '16:00:00',
                'tipo' => 'Farmacia'
            ],
            [
                'nro' => 'T003',
                'id_usuario' => 3,
                'hora_inicio' => '09:00:00',
                'hora_fin' => '17:00:00',
                'tipo' => 'Médico'
            ],
            [
                'nro' => 'T004',
                'id_usuario' => 4,
                'hora_inicio' => '08:00:00',
                'hora_fin' => '16:00:00',
                'tipo' => 'Enfermería'
            ],
            [
                'nro' => 'T005',
                'id_usuario' => 5,
                'hora_inicio' => '16:00:00',
                'hora_fin' => '00:00:00',
                'tipo' => 'Emergencia'
            ],
            [
                'nro' => 'T006',
                'id_usuario' => 6,
                'hora_inicio' => '07:00:00',
                'hora_fin' => '15:00:00',
                'tipo' => 'Enfermería'
            ],
            [
                'nro' => 'T007',
                'id_usuario' => 7,
                'hora_inicio' => '08:30:00',
                'hora_fin' => '16:30:00',
                'tipo' => 'Laboratorio'
            ],
            [
                'nro' => 'T008',
                'id_usuario' => 8,
                'hora_inicio' => '08:00:00',
                'hora_fin' => '16:00:00',
                'tipo' => 'Administrativo'
            ],
            [
                'nro' => 'T009',
                'id_usuario' => 9,
                'hora_inicio' => '10:00:00',
                'hora_fin' => '18:00:00',
                'tipo' => 'Médico'
            ],
            [
                'nro' => 'T010',
                'id_usuario' => 10,
                'hora_inicio' => '00:00:00',
                'hora_fin' => '08:00:00',
                'tipo' => 'Guardia'
            ],
            [
                'nro' => 'T011',
                'id_usuario' => 11,
                'hora_inicio' => '08:00:00',
                'hora_fin' => '16:00:00',
                'tipo' => 'Quirófano'
            ],
            [
                'nro' => 'T012',
                'id_usuario' => 12,
                'hora_inicio' => '12:00:00',
                'hora_fin' => '20:00:00',
                'tipo' => 'Farmacia'
            ],
            [
                'nro' => 'T013',
                'id_usuario' => 13,
                'hora_inicio' => '09:00:00',
                'hora_fin' => '17:00:00',
                'tipo' => 'Médico'
            ],
            [
                'nro' => 'T014',
                'id_usuario' => 14,
                'hora_inicio' => '08:00:00',
                'hora_fin' => '16:00:00',
                'tipo' => 'Administrativo'
            ],
            [
                'nro' => 'T015',
                'id_usuario' => 15,
                'hora_inicio' => '16:00:00',
                'hora_fin' => '00:00:00',
                'tipo' => 'Emergencia'
            ],
        ];

        DB::table('turnos')->insert($turnos);
    }
}
