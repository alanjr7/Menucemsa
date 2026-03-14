<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AsistenteQuirofanosTableSeeder extends Seeder
{
    public function run(): void
    {
        $asistentes = [
            [
                'id' => 'ASIST001',
                'nro_quirofano' => 1,
                'descripcion' => 'Monitor de signos vitales'
            ],
            [
                'id' => 'ASIST002',
                'nro_quirofano' => 1,
                'descripcion' => 'Bomba de infusión'
            ],
            [
                'id' => 'ASIST003',
                'nro_quirofano' => 1,
                'descripcion' => 'Electrobisturí'
            ],
            [
                'id' => 'ASIST004',
                'nro_quirofano' => 2,
                'descripcion' => 'Monitor de signos vitales'
            ],
            [
                'id' => 'ASIST005',
                'nro_quirofano' => 2,
                'descripcion' => 'Lámpara quirúrgica'
            ],
            [
                'id' => 'ASIST006',
                'nro_quirofano' => 2,
                'descripcion' => 'Aspirador quirúrgico'
            ],
            [
                'id' => 'ASIST007',
                'nro_quirofano' => 3,
                'descripcion' => 'Monitor anestésico'
            ],
            [
                'id' => 'ASIST008',
                'nro_quirofano' => 3,
                'descripcion' => 'Ventilador mecánico'
            ],
            [
                'id' => 'ASIST009',
                'nro_quirofano' => 3,
                'descripcion' => 'Bomba de infusión'
            ],
            [
                'id' => 'ASIST010',
                'nro_quirofano' => 4,
                'descripcion' => 'Monitor de signos vitales'
            ],
            [
                'id' => 'ASIST011',
                'nro_quirofano' => 4,
                'descripcion' => 'Electrobisturí'
            ],
            [
                'id' => 'ASIST012',
                'nro_quirofano' => 4,
                'descripcion' => 'Lámpara quirúrgica'
            ],
            [
                'id' => 'ASIST013',
                'nro_quirofano' => 5,
                'descripcion' => 'Monitor neonatal'
            ],
            [
                'id' => 'ASIST014',
                'nro_quirofano' => 5,
                'descripcion' => 'Incubadora transportadora'
            ],
            [
                'id' => 'ASIST015',
                'nro_quirofano' => 5,
                'descripcion' => 'Bomba de infusión pediátrica'
            ],
        ];

        DB::table('asistente_quirofanos')->insert($asistentes);
    }
}
