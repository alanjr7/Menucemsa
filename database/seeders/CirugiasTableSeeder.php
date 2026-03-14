<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CirugiasTableSeeder extends Seeder
{
    public function run(): void
    {
        $cirugias = [
            [
                'nro' => 'CIR001',
                'fecha' => '2024-03-14',
                'hora' => '08:00:00',
                'tipo' => 'Emergencia',
                'descripcion' => 'Reducción de fractura expuesta',
                'nro_emergencia' => 'EM004',
                'nro_quirofano' => 1
            ],
            [
                'nro' => 'CIR002',
                'fecha' => '2024-03-13',
                'hora' => '23:45:00',
                'tipo' => 'Emergencia',
                'descripcion' => 'Cesárea de emergencia',
                'nro_emergencia' => 'EM019',
                'nro_quirofano' => 2
            ],
            [
                'nro' => 'CIR003',
                'fecha' => '2024-03-14',
                'hora' => '10:30:00',
                'tipo' => 'Urgente',
                'descripcion' => 'Laparotomía exploratoria',
                'nro_emergencia' => 'EM005',
                'nro_quirofano' => 3
            ],
            [
                'nro' => 'CIR004',
                'fecha' => '2024-03-14',
                'hora' => '14:00:00',
                'tipo' => 'Programada',
                'descripcion' => 'Colecistectomía laparoscópica',
                'nro_emergencia' => null,
                'nro_quirofano' => 4
            ],
            [
                'nro' => 'CIR005',
                'fecha' => '2024-03-14',
                'hora' => '16:30:00',
                'tipo' => 'Programada',
                'descripcion' => 'Herniorrafia inguinal',
                'nro_emergencia' => null,
                'nro_quirofano' => 5
            ],
            [
                'nro' => 'CIR006',
                'fecha' => '2024-03-14',
                'hora' => '09:00:00',
                'tipo' => 'Emergencia',
                'descripcion' => 'Desbridamiento de quemaduras',
                'nro_emergencia' => 'EM009',
                'nro_quirofano' => 1
            ],
            [
                'nro' => 'CIR007',
                'fecha' => '2024-03-14',
                'hora' => '11:15:00',
                'tipo' => 'Urgente',
                'descripcion' => 'Apendicectomía',
                'nro_emergencia' => 'EM007',
                'nro_quirofano' => 2
            ],
            [
                'nro' => 'CIR008',
                'fecha' => '2024-03-14',
                'hora' => '13:45:00',
                'tipo' => 'Programada',
                'descripcion' => 'Artroscopia de rodilla',
                'nro_emergencia' => null,
                'nro_quirofano' => 3
            ],
            [
                'nro' => 'CIR009',
                'fecha' => '2024-03-14',
                'hora' => '15:30:00',
                'tipo' => 'Programada',
                'descripcion' => 'Mastectomía parcial',
                'nro_emergencia' => null,
                'nro_quirofano' => 4
            ],
            [
                'nro' => 'CIR010',
                'fecha' => '2024-03-14',
                'hora' => '17:45:00',
                'tipo' => 'Programada',
                'descripcion' => 'Sinuscopy endoscópica',
                'nro_emergencia' => null,
                'nro_quirofano' => 5
            ],
            [
                'nro' => 'CIR011',
                'fecha' => '2024-03-13',
                'hora' => '18:30:00',
                'tipo' => 'Emergencia',
                'descripcion' => 'Toracotomía de emergencia',
                'nro_emergencia' => 'EM001',
                'nro_quirofano' => 1
            ],
            [
                'nro' => 'CIR012',
                'fecha' => '2024-03-13',
                'hora' => '20:15:00',
                'tipo' => 'Urgente',
                'descripcion' => 'Craniotomía descompresiva',
                'nro_emergencia' => 'EM002',
                'nro_quirofano' => 2
            ],
            [
                'nro' => 'CIR013',
                'fecha' => '2024-03-15',
                'hora' => '08:30:00',
                'tipo' => 'Programada',
                'descripcion' => 'Reparación de hernia diafragmática',
                'nro_emergencia' => null,
                'nro_quirofano' => 3
            ],
            [
                'nro' => 'CIR014',
                'fecha' => '2024-03-15',
                'hora' => '10:00:00',
                'tipo' => 'Programada',
                'descripcion' => 'Ligadura de trompas',
                'nro_emergencia' => null,
                'nro_quirofano' => 4
            ],
            [
                'nro' => 'CIR015',
                'fecha' => '2024-03-15',
                'hora' => '14:15:00',
                'tipo' => 'Programada',
                'descripcion' => 'Resección meniscal',
                'nro_emergencia' => null,
                'nro_quirofano' => 5
            ],
        ];

        DB::table('cirugias')->insert($cirugias);
    }
}
