<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TurnoInternosTableSeeder extends Seeder
{
    public function run(): void
    {
        $turnos = [
            [
                'nro' => 'TI001',
                'ci_interno' => 98765432,
                'hora_inicio' => '08:00:00',
                'hora_fin' => '16:00:00',
                'tipo' => 'Diurno'
            ],
            [
                'nro' => 'TI002',
                'ci_interno' => 87654321,
                'hora_inicio' => '09:00:00',
                'hora_fin' => '17:00:00',
                'tipo' => 'Diurno'
            ],
            [
                'nro' => 'TI003',
                'ci_interno' => 76543210,
                'hora_inicio' => '07:00:00',
                'hora_fin' => '15:00:00',
                'tipo' => 'Diurno'
            ],
            [
                'nro' => 'TI004',
                'ci_interno' => 65432109,
                'hora_inicio' => '08:30:00',
                'hora_fin' => '16:30:00',
                'tipo' => 'Diurno'
            ],
            [
                'nro' => 'TI005',
                'ci_interno' => 54321098,
                'hora_inicio' => '09:00:00',
                'hora_fin' => '17:00:00',
                'tipo' => 'Diurno'
            ],
            [
                'nro' => 'TI006',
                'ci_interno' => 43210987,
                'hora_inicio' => '16:00:00',
                'hora_fin' => '00:00:00',
                'tipo' => 'Nocturno'
            ],
            [
                'nro' => 'TI007',
                'ci_interno' => 32109876,
                'hora_inicio' => '08:00:00',
                'hora_fin' => '16:00:00',
                'tipo' => 'Diurno'
            ],
            [
                'nro' => 'TI008',
                'ci_interno' => 21098765,
                'hora_inicio' => '10:00:00',
                'hora_fin' => '18:00:00',
                'tipo' => 'Diurno'
            ],
            [
                'nro' => 'TI009',
                'ci_interno' => 10987654,
                'hora_inicio' => '08:00:00',
                'hora_fin' => '14:00:00',
                'tipo' => 'Medio turno'
            ],
            [
                'nro' => 'TI010',
                'ci_interno' => 99887766,
                'hora_inicio' => '14:00:00',
                'hora_fin' => '20:00:00',
                'tipo' => 'Medio turno'
            ],
            [
                'nro' => 'TI011',
                'ci_interno' => 98765432,
                'hora_inicio' => '00:00:00',
                'hora_fin' => '08:00:00',
                'tipo' => 'Nocturno'
            ],
            [
                'nro' => 'TI012',
                'ci_interno' => 87654321,
                'hora_inicio' => '16:00:00',
                'hora_fin' => '00:00:00',
                'tipo' => 'Nocturno'
            ],
            [
                'nro' => 'TI013',
                'ci_interno' => 76543210,
                'hora_inicio' => '00:00:00',
                'hora_fin' => '08:00:00',
                'tipo' => 'Guardia'
            ],
            [
                'nro' => 'TI014',
                'ci_interno' => 54321098,
                'hora_inicio' => '12:00:00',
                'hora_fin' => '20:00:00',
                'tipo' => 'Tarde'
            ],
            [
                'nro' => 'TI015',
                'ci_interno' => 43210987,
                'hora_inicio' => '08:00:00',
                'hora_fin' => '12:00:00',
                'tipo' => 'Mañana'
            ],
        ];

        DB::table('turno_internos')->insert($turnos);
    }
}
