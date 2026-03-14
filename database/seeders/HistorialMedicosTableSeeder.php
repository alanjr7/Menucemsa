<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HistorialMedicosTableSeeder extends Seeder
{
    public function run(): void
    {
        $historial = [
            [
                'id' => 'HM001',
                'ci_paciente' => 123456789,
                'fecha' => '2024-03-14',
                'detalle' => 'Control general de rutina',
                'observaciones' => 'Paciente estable',
                'alergias' => 'Penicilina',
                'id_usuario_medico' => 3
            ],
            [
                'id' => 'HM002',
                'ci_paciente' => 234567890,
                'fecha' => '2024-03-14',
                'detalle' => 'Control prenatal',
                'observaciones' => 'Embarazo saludable',
                'alergias' => 'Ninguna',
                'id_usuario_medico' => 16
            ],
            [
                'id' => 'HM003',
                'ci_paciente' => 345678901,
                'fecha' => '2024-03-14',
                'detalle' => 'Dolor abdominal agudo',
                'observaciones' => 'Requiere estudios',
                'alergias' => 'Sulfonamidas',
                'id_usuario_medico' => 21
            ],
            [
                'id' => 'HM004',
                'ci_paciente' => 456789012,
                'fecha' => '2024-03-14',
                'detalle' => 'Control pediátrico',
                'observaciones' => 'Vacunas al día',
                'alergias' => 'Látex',
                'id_usuario_medico' => 13
            ],
            [
                'id' => 'HM005',
                'ci_paciente' => 567890123,
                'fecha' => '2024-03-14',
                'detalle' => 'Dolor torácico',
                'observaciones' => 'Urgencia cardiológica',
                'alergias' => 'Ninguna',
                'id_usuario_medico' => 17
            ],
            [
                'id' => 'HM006',
                'ci_paciente' => 678901234,
                'fecha' => '2024-03-14',
                'detalle' => 'Cefalea intensa',
                'observaciones' => 'Migraña probable',
                'alergias' => 'AINEs',
                'id_usuario_medico' => 18
            ],
            [
                'id' => 'HM007',
                'ci_paciente' => 789012345,
                'fecha' => '2024-03-14',
                'detalle' => 'Fractura de muñeca',
                'observaciones' => 'Inmovilización requerida',
                'alergias' => 'Ninguna',
                'id_usuario_medico' => 19
            ],
            [
                'id' => 'HM008',
                'ci_paciente' => 890123456,
                'fecha' => '2024-03-14',
                'detalle' => 'Evaluación preoperatoria',
                'observaciones' => 'Apto para cirugía',
                'alergias' => 'Anestésicos locales',
                'id_usuario_medico' => 20
            ],
            [
                'id' => 'HM009',
                'ci_paciente' => 901234567,
                'fecha' => '2024-03-14',
                'detalle' => 'Diabetes tipo 2',
                'observaciones' => 'Glucemia controlada',
                'alergias' => 'Metformina',
                'id_usuario_medico' => 21
            ],
            [
                'id' => 'HM010',
                'ci_paciente' => 112233445,
                'fecha' => '2024-03-14',
                'detalle' => 'Herida cortante en mano',
                'observaciones' => 'Requiere sutura',
                'alergias' => 'Ninguna',
                'id_usuario_medico' => 22
            ],
            [
                'id' => 'HM011',
                'ci_paciente' => 223344556,
                'fecha' => '2024-03-14',
                'detalle' => 'Erupción cutánea',
                'observaciones' => 'Alergia probable',
                'alergias' => 'Níquel',
                'id_usuario_medico' => 23
            ],
            [
                'id' => 'HM012',
                'ci_paciente' => 334455667,
                'fecha' => '2024-03-14',
                'detalle' => 'Visión borrosa',
                'observaciones' => 'Requiere corrección',
                'alergias' => 'Ninguna',
                'id_usuario_medico' => 24
            ],
            [
                'id' => 'HM013',
                'ci_paciente' => 445566778,
                'fecha' => '2024-03-14',
                'detalle' => 'Dolor de garganta',
                'observaciones' => 'Faringitis aguda',
                'alergias' => 'Penicilina',
                'id_usuario_medico' => 25
            ],
            [
                'id' => 'HM014',
                'ci_paciente' => 556677889,
                'fecha' => '2024-03-14',
                'detalle' => 'Ansiedad generalizada',
                'observaciones' => 'Requiere terapia',
                'alergias' => 'Benzodiacepinas',
                'id_usuario_medico' => 26
            ],
            [
                'id' => 'HM015',
                'ci_paciente' => 667788900,
                'fecha' => '2024-03-14',
                'detalle' => 'Hipotiroidismo',
                'observaciones' => 'Medicación ajustada',
                'alergias' => 'Levotiroxina',
                'id_usuario_medico' => 27
            ],
            [
                'id' => 'HM016',
                'ci_paciente' => 778899011,
                'fecha' => '2024-03-13',
                'detalle' => 'Insuficiencia renal crónica',
                'observaciones' => 'Diálisis programada',
                'alergias' => 'Heparina',
                'id_usuario_medico' => 28
            ],
            [
                'id' => 'HM017',
                'ci_paciente' => 889900122,
                'fecha' => '2024-03-13',
                'detalle' => 'Asma bronquial',
                'observaciones' => 'Crisis controlada',
                'alergias' => 'Polen',
                'id_usuario_medico' => 29
            ],
            [
                'id' => 'HM018',
                'ci_paciente' => 990011233,
                'fecha' => '2024-03-13',
                'detalle' => 'Gastritis crónica',
                'observaciones' => 'Dieta recomendada',
                'alergias' => 'AINEs',
                'id_usuario_medico' => 30
            ],
            [
                'id' => 'HM019',
                'ci_paciente' => 001122334,
                'fecha' => '2024-03-13',
                'detalle' => 'Artritis reumatoide',
                'observaciones' => 'Tratamiento crónico',
                'alergias' => 'Metotrexato',
                'id_usuario_medico' => 31
            ],
            [
                'id' => 'HM020',
                'ci_paciente' => 122334455,
                'fecha' => '2024-03-13',
                'detalle' => 'Rehabilitación post-ACV',
                'observaciones' => 'Progreso favorable',
                'alergias' => 'Ninguna',
                'id_usuario_medico' => 32
            ],
        ];

        DB::table('historial_medicos')->insert($historial);
    }
}
