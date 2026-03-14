<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HospitalizacionesTableSeeder extends Seeder
{
    public function run(): void
    {
        $hospitalizaciones = [
            [
                'id' => 'HOSP001',
                'fecha_ingreso' => '2024-03-13 22:15:00',
                'motivo' => 'Trauma severo por accidente',
                'nro_emergencia' => 'EM002'
            ],
            [
                'id' => 'HOSP002',
                'fecha_ingreso' => '2024-03-13 23:30:00',
                'motivo' => 'Parto de emergencia',
                'nro_emergencia' => 'EM019'
            ],
            [
                'id' => 'HOSP003',
                'fecha_ingreso' => '2024-03-14 10:30:00',
                'motivo' => 'Dolor torácico agudo',
                'nro_emergencia' => 'EM003'
            ],
            [
                'id' => 'HOSP004',
                'fecha_ingreso' => '2024-03-14 11:45:00',
                'motivo' => 'Hemorragia digestiva',
                'nro_emergencia' => 'EM005'
            ],
            [
                'id' => 'HOSP005',
                'fecha_ingreso' => '2024-03-14 14:20:00',
                'motivo' => 'Fractura expuesta',
                'nro_emergencia' => 'EM004'
            ],
            [
                'id' => 'HOSP006',
                'fecha_ingreso' => '2024-03-14 15:30:00',
                'motivo' => 'Crisis asmática severa',
                'nro_emergencia' => 'EM006'
            ],
            [
                'id' => 'HOSP007',
                'fecha_ingreso' => '2024-03-14 16:45:00',
                'motivo' => 'Quemaduras de segundo grado',
                'nro_emergencia' => 'EM009'
            ],
            [
                'id' => 'HOSP008',
                'fecha_ingreso' => '2024-03-14 17:00:00',
                'motivo' => 'Crisis hipertensiva',
                'nro_emergencia' => 'EM013'
            ],
            [
                'id' => 'HOSP009',
                'fecha_ingreso' => '2024-03-14 18:15:00',
                'motivo' => 'Reacción alérgica aguda',
                'nro_emergencia' => 'EM017'
            ],
            [
                'id' => 'HOSP010',
                'fecha_ingreso' => '2024-03-14 19:30:00',
                'motivo' => 'Dolor abdominal agudo',
                'nro_emergencia' => 'EM007'
            ],
            [
                'id' => 'HOSP011',
                'fecha_ingreso' => '2024-03-14 20:45:00',
                'motivo' => 'Convulsión febril',
                'nro_emergencia' => 'EM014'
            ],
            [
                'id' => 'HOSP012',
                'fecha_ingreso' => '2024-03-14 21:00:00',
                'motivo' => 'Desmayo síncope',
                'nro_emergencia' => 'EM016'
            ],
            [
                'id' => 'HOSP013',
                'fecha_ingreso' => '2024-03-13 18:00:00',
                'motivo' => 'Paro cardiorrespiratorio',
                'nro_emergencia' => 'EM001'
            ],
            [
                'id' => 'HOSP014',
                'fecha_ingreso' => '2024-03-13 19:15:00',
                'motivo' => 'Insuficiencia renal crónica',
                'nro_emergencia' => null
            ],
            [
                'id' => 'HOSP015',
                'fecha_ingreso' => '2024-03-13 20:30:00',
                'motivo' => 'Diabetes descompensada',
                'nro_emergencia' => null
            ],
        ];

        DB::table('hospitalizaciones')->insert($hospitalizaciones);
    }
}
