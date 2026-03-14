<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConsultasTableSeeder extends Seeder
{
    public function run(): void
    {
        $consultas = [
            [
                'nro' => 'C001',
                'fecha' => '2024-03-14',
                'hora' => '09:00:00',
                'motivo' => 'Control general',
                'observaciones' => 'Paciente estable',
                'codigo_especialidad' => 'ESP001'
            ],
            [
                'nro' => 'C002',
                'fecha' => '2024-03-14',
                'hora' => '09:30:00',
                'motivo' => 'Dolor abdominal',
                'observaciones' => 'Requiere estudios',
                'codigo_especialidad' => 'ESP018'
            ],
            [
                'nro' => 'C003',
                'fecha' => '2024-03-14',
                'hora' => '10:00:00',
                'motivo' => 'Control pediátrico',
                'observaciones' => 'Vacunas al día',
                'codigo_especialidad' => 'ESP003'
            ],
            [
                'nro' => 'C004',
                'fecha' => '2024-03-14',
                'hora' => '10:30:00',
                'motivo' => 'Control prenatal',
                'observaciones' => 'Embarazo saludable',
                'codigo_especialidad' => 'ESP004'
            ],
            [
                'nro' => 'C005',
                'fecha' => '2024-03-14',
                'hora' => '11:00:00',
                'motivo' => 'Dolor torácico',
                'observaciones' => 'Urgencia cardiológica',
                'codigo_especialidad' => 'ESP005'
            ],
            [
                'nro' => 'C006',
                'fecha' => '2024-03-14',
                'hora' => '11:30:00',
                'motivo' => 'Cefalea',
                'observaciones' => 'Migraña',
                'codigo_especialidad' => 'ESP006'
            ],
            [
                'nro' => 'C007',
                'fecha' => '2024-03-14',
                'hora' => '12:00:00',
                'motivo' => 'Fractura de muñeca',
                'observaciones' => 'Inmovilización requerida',
                'codigo_especialidad' => 'ESP007'
            ],
            [
                'nro' => 'C008',
                'fecha' => '2024-03-14',
                'hora' => '14:00:00',
                'motivo' => 'Evaluación preoperatoria',
                'observaciones' => 'Apto para cirugía',
                'codigo_especialidad' => 'ESP008'
            ],
            [
                'nro' => 'C009',
                'fecha' => '2024-03-14',
                'hora' => '14:30:00',
                'motivo' => 'Diabetes control',
                'observaciones' => 'Glucemia estable',
                'codigo_especialidad' => 'ESP009'
            ],
            [
                'nro' => 'C010',
                'fecha' => '2024-03-14',
                'hora' => '15:00:00',
                'motivo' => 'Herida cortante',
                'observaciones' => 'Requiere sutura',
                'codigo_especialidad' => 'ESP010'
            ],
            [
                'nro' => 'C011',
                'fecha' => '2024-03-14',
                'hora' => '15:30:00',
                'motivo' => 'Erupción cutánea',
                'observaciones' => 'Alergia probable',
                'codigo_especialidad' => 'ESP011'
            ],
            [
                'nro' => 'C012',
                'fecha' => '2024-03-14',
                'hora' => '16:00:00',
                'motivo' => 'Visión borrosa',
                'observaciones' => 'Requiere lentes',
                'codigo_especialidad' => 'ESP012'
            ],
            [
                'nro' => 'C013',
                'fecha' => '2024-03-14',
                'hora' => '16:30:00',
                'motivo' => 'Dolor de garganta',
                'observaciones' => 'Faringitis aguda',
                'codigo_especialidad' => 'ESP013'
            ],
            [
                'nro' => 'C014',
                'fecha' => '2024-03-14',
                'hora' => '17:00:00',
                'motivo' => 'Ansiedad',
                'observaciones' => 'Requiere terapia',
                'codigo_especialidad' => 'ESP014'
            ],
            [
                'nro' => 'C015',
                'fecha' => '2024-03-14',
                'hora' => '17:30:00',
                'motivo' => 'Hipotiroidismo',
                'observaciones' => 'Medicación ajustada',
                'codigo_especialidad' => 'ESP015'
            ],
            [
                'nro' => 'C016',
                'fecha' => '2024-03-13',
                'hora' => '18:00:00',
                'motivo' => 'Insuficiencia renal',
                'observaciones' => 'Diálisis programada',
                'codigo_especialidad' => 'ESP016'
            ],
            [
                'nro' => 'C017',
                'fecha' => '2024-03-13',
                'hora' => '18:30:00',
                'motivo' => 'Asma',
                'observaciones' => 'Crisis controlada',
                'codigo_especialidad' => 'ESP017'
            ],
            [
                'nro' => 'C018',
                'fecha' => '2024-03-13',
                'hora' => '19:00:00',
                'motivo' => 'Gastritis',
                'observaciones' => 'Dieta recomendada',
                'codigo_especialidad' => 'ESP018'
            ],
            [
                'nro' => 'C019',
                'fecha' => '2024-03-13',
                'hora' => '19:30:00',
                'motivo' => 'Artritis',
                'observaciones' => 'Tratamiento crónico',
                'codigo_especialidad' => 'ESP019'
            ],
            [
                'nro' => 'C020',
                'fecha' => '2024-03-13',
                'hora' => '20:00:00',
                'motivo' => 'Rehabilitación',
                'observaciones' => 'Progreso favorable',
                'codigo_especialidad' => 'ESP020'
            ],
        ];

        DB::table('consultas')->insert($consultas);
    }
}
