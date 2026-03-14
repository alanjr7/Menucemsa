<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MedicosTableSeeder extends Seeder
{
    public function run(): void
    {
        $medicos = [
            [
                'id_usuario' => 3,
                'ci' => 12345678,
                'telefono' => 5551234,
                'estado' => 'Activo',
                'id_asistente' => 'ASIST001',
                'codigo_especialidad' => 'ESP001'
            ],
            [
                'id_usuario' => 9,
                'ci' => 23456789,
                'telefono' => 5555678,
                'estado' => 'Activo',
                'id_asistente' => 'ASIST004',
                'codigo_especialidad' => 'ESP002'
            ],
            [
                'id_usuario' => 13,
                'ci' => 34567890,
                'telefono' => 5559012,
                'estado' => 'Activo',
                'id_asistente' => 'ASIST007',
                'codigo_especialidad' => 'ESP003'
            ],
            [
                'id_usuario' => 16,
                'ci' => 45678901,
                'telefono' => 5553456,
                'estado' => 'Activo',
                'id_asistente' => 'ASIST010',
                'codigo_especialidad' => 'ESP004'
            ],
            [
                'id_usuario' => 17,
                'ci' => 56789012,
                'telefono' => 5557890,
                'estado' => 'Activo',
                'id_asistente' => 'ASIST013',
                'codigo_especialidad' => 'ESP005'
            ],
            [
                'id_usuario' => 18,
                'ci' => 67890123,
                'telefono' => 5551111,
                'estado' => 'Activo',
                'id_asistente' => null,
                'codigo_especialidad' => 'ESP006'
            ],
            [
                'id_usuario' => 19,
                'ci' => 78901234,
                'telefono' => 5552222,
                'estado' => 'Activo',
                'id_asistente' => null,
                'codigo_especialidad' => 'ESP007'
            ],
            [
                'id_usuario' => 20,
                'ci' => 89012345,
                'telefono' => 5553333,
                'estado' => 'Activo',
                'id_asistente' => 'ASIST002',
                'codigo_especialidad' => 'ESP008'
            ],
            [
                'id_usuario' => 21,
                'ci' => 90123456,
                'telefono' => 5554444,
                'estado' => 'Activo',
                'id_asistente' => null,
                'codigo_especialidad' => 'ESP009'
            ],
            [
                'id_usuario' => 22,
                'ci' => 11223344,
                'telefono' => 5555555,
                'estado' => 'Activo',
                'id_asistente' => null,
                'codigo_especialidad' => 'ESP010'
            ],
            [
                'id_usuario' => 23,
                'ci' => 22334455,
                'telefono' => 5556666,
                'estado' => 'Activo',
                'id_asistente' => null,
                'codigo_especialidad' => 'ESP011'
            ],
            [
                'id_usuario' => 24,
                'ci' => 33445566,
                'telefono' => 5557777,
                'estado' => 'Activo',
                'id_asistente' => null,
                'codigo_especialidad' => 'ESP012'
            ],
            [
                'id_usuario' => 25,
                'ci' => 44556677,
                'telefono' => 5558888,
                'estado' => 'Activo',
                'id_asistente' => null,
                'codigo_especialidad' => 'ESP013'
            ],
            [
                'id_usuario' => 26,
                'ci' => 55667788,
                'telefono' => 5559999,
                'estado' => 'Activo',
                'id_asistente' => null,
                'codigo_especialidad' => 'ESP014'
            ],
            [
                'id_usuario' => 27,
                'ci' => 66778899,
                'telefono' => 5550000,
                'estado' => 'Activo',
                'id_asistente' => null,
                'codigo_especialidad' => 'ESP015'
            ],
            [
                'id_usuario' => 28,
                'ci' => 77889900,
                'telefono' => 5551212,
                'estado' => 'Activo',
                'id_asistente' => null,
                'codigo_especialidad' => 'ESP016'
            ],
            [
                'id_usuario' => 29,
                'ci' => 88990011,
                'telefono' => 5553434,
                'estado' => 'Activo',
                'id_asistente' => null,
                'codigo_especialidad' => 'ESP017'
            ],
            [
                'id_usuario' => 30,
                'ci' => 99001122,
                'telefono' => 5555656,
                'estado' => 'Activo',
                'id_asistente' => null,
                'codigo_especialidad' => 'ESP018'
            ],
            [
                'id_usuario' => 31,
                'ci' => 00112233,
                'telefono' => 5557878,
                'estado' => 'Activo',
                'id_asistente' => null,
                'codigo_especialidad' => 'ESP019'
            ],
            [
                'id_usuario' => 32,
                'ci' => 12233445,
                'telefono' => 5559090,
                'estado' => 'Activo',
                'id_asistente' => null,
                'codigo_especialidad' => 'ESP020'
            ],
        ];

        DB::table('medicos')->insert($medicos);
    }
}
