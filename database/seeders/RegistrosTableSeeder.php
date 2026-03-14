<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RegistrosTableSeeder extends Seeder
{
    public function run(): void
    {
        $registros = [
            [
                'codigo' => 'REG001',
                'fecha' => '2024-03-14',
                'hora' => '08:00:00',
                'motivo' => 'Primera consulta',
                'id_usuario' => 1
            ],
            [
                'codigo' => 'REG002',
                'fecha' => '2024-03-14',
                'hora' => '08:30:00',
                'motivo' => 'Control médico',
                'id_usuario' => 1
            ],
            [
                'codigo' => 'REG003',
                'fecha' => '2024-03-14',
                'hora' => '09:00:00',
                'motivo' => 'Emergencia',
                'id_usuario' => 5
            ],
            [
                'codigo' => 'REG004',
                'fecha' => '2024-03-14',
                'hora' => '09:30:00',
                'motivo' => 'Consulta pediátrica',
                'id_usuario' => 3
            ],
            [
                'codigo' => 'REG005',
                'fecha' => '2024-03-14',
                'hora' => '10:00:00',
                'motivo' => 'Control prenatal',
                'id_usuario' => 2
            ],
            [
                'codigo' => 'REG006',
                'fecha' => '2024-03-14',
                'hora' => '10:30:00',
                'motivo' => 'Urgencia cardiológica',
                'id_usuario' => 5
            ],
            [
                'codigo' => 'REG007',
                'fecha' => '2024-03-14',
                'hora' => '11:00:00',
                'motivo' => 'Consulta neurológica',
                'id_usuario' => 4
            ],
            [
                'codigo' => 'REG008',
                'fecha' => '2024-03-14',
                'hora' => '11:30:00',
                'motivo' => 'Traumatología',
                'id_usuario' => 6
            ],
            [
                'codigo' => 'REG009',
                'fecha' => '2024-03-14',
                'hora' => '12:00:00',
                'motivo' => 'Evaluación preoperatoria',
                'id_usuario' => 7
            ],
            [
                'codigo' => 'REG010',
                'fecha' => '2024-03-14',
                'hora' => '14:00:00',
                'motivo' => 'Control diabetes',
                'id_usuario' => 8
            ],
            [
                'codigo' => 'REG011',
                'fecha' => '2024-03-14',
                'hora' => '14:30:00',
                'motivo' => 'Herida cortante',
                'id_usuario' => 5
            ],
            [
                'codigo' => 'REG012',
                'fecha' => '2024-03-14',
                'hora' => '15:00:00',
                'motivo' => 'Consulta dermatológica',
                'id_usuario' => 9
            ],
            [
                'codigo' => 'REG013',
                'fecha' => '2024-03-14',
                'hora' => '15:30:00',
                'motivo' => 'Oftalmología',
                'id_usuario' => 10
            ],
            [
                'codigo' => 'REG014',
                'fecha' => '2024-03-14',
                'hora' => '16:00:00',
                'motivo' => 'Otorrinolaringología',
                'id_usuario' => 11
            ],
            [
                'codigo' => 'REG015',
                'fecha' => '2024-03-14',
                'hora' => '16:30:00',
                'motivo' => 'Consulta psiquiátrica',
                'id_usuario' => 12
            ],
            [
                'codigo' => 'REG016',
                'fecha' => '2024-03-13',
                'hora' => '18:00:00',
                'motivo' => 'Emergencia nocturna',
                'id_usuario' => 5
            ],
            [
                'codigo' => 'REG017',
                'fecha' => '2024-03-13',
                'hora' => '18:30:00',
                'motivo' => 'Hospitalización',
                'id_usuario' => 6
            ],
            [
                'codigo' => 'REG018',
                'fecha' => '2024-03-13',
                'hora' => '19:00:00',
                'motivo' => 'Parto',
                'id_usuario' => 7
            ],
            [
                'codigo' => 'REG019',
                'fecha' => '2024-03-13',
                'hora' => '19:30:00',
                'motivo' => 'Cirugía de emergencia',
                'id_usuario' => 8
            ],
            [
                'codigo' => 'REG020',
                'fecha' => '2024-03-13',
                'hora' => '20:00:00',
                'motivo' => 'UCI',
                'id_usuario' => 9
            ],
        ];

        DB::table('registros')->insert($registros);
    }
}
