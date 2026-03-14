<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RecetasTableSeeder extends Seeder
{
    public function run(): void
    {
        $recetas = [
            [
                'nro' => 'REC001',
                'fecha' => '2024-03-14',
                'indicaciones' => 'Tomar con alimentos',
                'id_usuario_medico' => 3,
                'nro_consulta' => 'C001'
            ],
            [
                'nro' => 'REC002',
                'fecha' => '2024-03-14',
                'indicaciones' => 'Tomar cada 8 horas',
                'id_usuario_medico' => 21,
                'nro_consulta' => 'C002'
            ],
            [
                'nro' => 'REC003',
                'fecha' => '2024-03-14',
                'indicaciones' => 'Suspender si hay reacción',
                'id_usuario_medico' => 13,
                'nro_consulta' => 'C003'
            ],
            [
                'nro' => 'REC004',
                'fecha' => '2024-03-14',
                'indicaciones' => 'Tomar con agua',
                'id_usuario_medico' => 16,
                'nro_consulta' => 'C004'
            ],
            [
                'nro' => 'REC005',
                'fecha' => '2024-03-14',
                'indicaciones' => 'Urgente - completar tratamiento',
                'id_usuario_medico' => 17,
                'nro_consulta' => 'C005'
            ],
            [
                'nro' => 'REC006',
                'fecha' => '2024-03-14',
                'indicaciones' => 'Tomar al inicio del dolor',
                'id_usuario_medico' => 18,
                'nro_consulta' => 'C006'
            ],
            [
                'nro' => 'REC007',
                'fecha' => '2024-03-14',
                'indicaciones' => 'Aplicar frío local',
                'id_usuario_medico' => 19,
                'nro_consulta' => 'C007'
            ],
            [
                'nro' => 'REC008',
                'fecha' => '2024-03-14',
                'indicaciones' => 'En ayunas',
                'id_usuario_medico' => 20,
                'nro_consulta' => 'C008'
            ],
            [
                'nro' => 'REC009',
                'fecha' => '2024-03-14',
                'indicaciones' => 'Controlar glucemia',
                'id_usuario_medico' => 21,
                'nro_consulta' => 'C009'
            ],
            [
                'nro' => 'REC010',
                'fecha' => '2024-03-14',
                'indicaciones' => 'Uso tópico',
                'id_usuario_medico' => 22,
                'nro_consulta' => 'C010'
            ],
            [
                'nro' => 'REC011',
                'fecha' => '2024-03-14',
                'indicaciones' => 'Evitar exposición solar',
                'id_usuario_medico' => 23,
                'nro_consulta' => 'C011'
            ],
            [
                'nro' => 'REC012',
                'fecha' => '2024-03-14',
                'indicaciones' => 'Usar anteojos',
                'id_usuario_medico' => 24,
                'nro_consulta' => 'C012'
            ],
            [
                'nro' => 'REC013',
                'fecha' => '2024-03-14',
                'indicaciones' => 'Hacer gárgaras',
                'id_usuario_medico' => 25,
                'nro_consulta' => 'C013'
            ],
            [
                'nro' => 'REC014',
                'fecha' => '2024-03-14',
                'indicaciones' => 'Tomar antes de dormir',
                'id_usuario_medico' => 26,
                'nro_consulta' => 'C014'
            ],
            [
                'nro' => 'REC015',
                'fecha' => '2024-03-14',
                'indicaciones' => 'Tomar en ayunas',
                'id_usuario_medico' => 27,
                'nro_consulta' => 'C015'
            ],
            [
                'nro' => 'REC016',
                'fecha' => '2024-03-13',
                'indicaciones' => 'Controlar función renal',
                'id_usuario_medico' => 28,
                'nro_consulta' => 'C016'
            ],
            [
                'nro' => 'REC017',
                'fecha' => '2024-03-13',
                'indicaciones' => 'Usar inhalador según necesidad',
                'id_usuario_medico' => 29,
                'nro_consulta' => 'C017'
            ],
            [
                'nro' => 'REC018',
                'fecha' => '2024-03-13',
                'indicaciones' => 'Dieta blanda',
                'id_usuario_medico' => 30,
                'nro_consulta' => 'C018'
            ],
            [
                'nro' => 'REC019',
                'fecha' => '2024-03-13',
                'indicaciones' => 'Tomar con antiácidos',
                'id_usuario_medico' => 31,
                'nro_consulta' => 'C019'
            ],
            [
                'nro' => 'REC020',
                'fecha' => '2024-03-13',
                'indicaciones' => 'Continuar terapia física',
                'id_usuario_medico' => 32,
                'nro_consulta' => 'C020'
            ],
        ];

        DB::table('recetas')->insert($recetas);
    }
}
