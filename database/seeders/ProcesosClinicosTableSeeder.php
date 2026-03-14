<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProcesosClinicosTableSeeder extends Seeder
{
    public function run(): void
    {
        $procesos = [
            [
                'id' => 'PC001',
                'id_hospitalizacion' => 'HOSP001',
                'fecha' => '2024-03-13',
                'estado' => 'En curso',
                'descripcion' => 'Estabilización inicial'
            ],
            [
                'id' => 'PC002',
                'id_hospitalizacion' => 'HOSP001',
                'fecha' => '2024-03-14',
                'estado' => 'Completado',
                'descripcion' => 'Cirugía de emergencia'
            ],
            [
                'id' => 'PC003',
                'id_hospitalizacion' => 'HOSP002',
                'fecha' => '2024-03-13',
                'estado' => 'Completado',
                'descripcion' => 'Parto normal'
            ],
            [
                'id' => 'PC004',
                'id_hospitalizacion' => 'HOSP003',
                'fecha' => '2024-03-14',
                'estado' => 'En curso',
                'descripcion' => 'Monitoreo cardíaco'
            ],
            [
                'id' => 'PC005',
                'id_hospitalizacion' => 'HOSP004',
                'fecha' => '2024-03-14',
                'estado' => 'En curso',
                'descripcion' => 'Endoscopia digestiva'
            ],
            [
                'id' => 'PC006',
                'id_hospitalizacion' => 'HOSP005',
                'fecha' => '2024-03-14',
                'estado' => 'En curso',
                'descripcion' => 'Reducción de fractura'
            ],
            [
                'id' => 'PC007',
                'id_hospitalizacion' => 'HOSP006',
                'fecha' => '2024-03-14',
                'estado' => 'Completado',
                'descripcion' => 'Tratamiento broncodilatador'
            ],
            [
                'id' => 'PC008',
                'id_hospitalizacion' => 'HOSP007',
                'fecha' => '2024-03-14',
                'estado' => 'En curso',
                'descripcion' => 'Curación de quemaduras'
            ],
            [
                'id' => 'PC009',
                'id_hospitalizacion' => 'HOSP008',
                'fecha' => '2024-03-14',
                'estado' => 'Completado',
                'descripcion' => 'Control de presión arterial'
            ],
            [
                'id' => 'PC010',
                'id_hospitalizacion' => 'HOSP009',
                'fecha' => '2024-03-14',
                'estado' => 'Completado',
                'descripcion' => 'Tratamiento antihistamínico'
            ],
            [
                'id' => 'PC011',
                'id_hospitalizacion' => 'HOSP010',
                'fecha' => '2024-03-14',
                'estado' => 'En curso',
                'descripcion' => 'Evaluación abdominal'
            ],
            [
                'id' => 'PC012',
                'id_hospitalizacion' => 'HOSP011',
                'fecha' => '2024-03-14',
                'estado' => 'Completado',
                'descripcion' => 'Control de fiebre'
            ],
            [
                'id' => 'PC013',
                'id_hospitalizacion' => 'HOSP012',
                'fecha' => '2024-03-14',
                'estado' => 'Completado',
                'descripcion' => 'Evaluación neurológica'
            ],
            [
                'id' => 'PC014',
                'id_hospitalizacion' => 'HOSP013',
                'fecha' => '2024-03-13',
                'estado' => 'Completado',
                'descripcion' => 'Reanimación cardiopulmonar'
            ],
            [
                'id' => 'PC015',
                'id_hospitalizacion' => 'HOSP014',
                'fecha' => '2024-03-13',
                'estado' => 'En curso',
                'descripcion' => 'Sesión de diálisis'
            ],
            [
                'id' => 'PC016',
                'id_hospitalizacion' => 'HOSP015',
                'fecha' => '2024-03-13',
                'estado' => 'En curso',
                'descripcion' => 'Control glucémico'
            ],
            [
                'id' => 'PC017',
                'id_hospitalizacion' => 'HOSP001',
                'fecha' => '2024-03-15',
                'estado' => 'Pendiente',
                'descripcion' => 'Terapia física'
            ],
            [
                'id' => 'PC018',
                'id_hospitalizacion' => 'HOSP003',
                'fecha' => '2024-03-15',
                'estado' => 'Pendiente',
                'descripcion' => 'Cateterismo cardíaco'
            ],
            [
                'id' => 'PC019',
                'id_hospitalizacion' => 'HOSP005',
                'fecha' => '2024-03-15',
                'estado' => 'Pendiente',
                'descripcion' => 'Revisión postquirúrgica'
            ],
            [
                'id' => 'PC020',
                'id_hospitalizacion' => 'HOSP007',
                'fecha' => '2024-03-15',
                'estado' => 'Pendiente',
                'descripcion' => 'Cambios de curación'
            ],
        ];

        DB::table('procesos_clinicos')->insert($procesos);
    }
}
