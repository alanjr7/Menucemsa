<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EnfermerasTableSeeder extends Seeder
{
    public function run(): void
    {
        $enfermeras = [
            [
                'id_usuario' => 4,
                'ci' => 11223344,
                'telefono' => 5551111,
                'tipo' => 'Enfermera General',
                'estado' => 'Activo',
                'id_asistente' => 'ASIST003'
            ],
            [
                'id_usuario' => 6,
                'ci' => 22334455,
                'telefono' => 5552222,
                'tipo' => 'Enfermera Jefe',
                'estado' => 'Activo',
                'id_asistente' => 'ASIST005'
            ],
            [
                'id_usuario' => 33,
                'ci' => 33445566,
                'telefono' => 5553333,
                'tipo' => 'Enfermera Quirúrgica',
                'estado' => 'Activo',
                'id_asistente' => 'ASIST008'
            ],
            [
                'id_usuario' => 34,
                'ci' => 44556677,
                'telefono' => 5554444,
                'tipo' => 'Enfermera Pediátrica',
                'estado' => 'Activo',
                'id_asistente' => 'ASIST011'
            ],
            [
                'id_usuario' => 35,
                'ci' => 55667788,
                'telefono' => 5555555,
                'tipo' => 'Enfermera de Urgencias',
                'estado' => 'Activo',
                'id_asistente' => 'ASIST014'
            ],
            [
                'id_usuario' => 36,
                'ci' => 66778899,
                'telefono' => 5556666,
                'tipo' => 'Enfermera Obstétrica',
                'estado' => 'Activo',
                'id_asistente' => 'ASIST002'
            ],
            [
                'id_usuario' => 37,
                'ci' => 77889900,
                'telefono' => 5557777,
                'tipo' => 'Enfermera de Terapia Intensiva',
                'estado' => 'Activo',
                'id_asistente' => 'ASIST006'
            ],
            [
                'id_usuario' => 38,
                'ci' => 88990011,
                'telefono' => 5558888,
                'tipo' => 'Enfermera General',
                'estado' => 'Activo',
                'id_asistente' => 'ASIST009'
            ],
            [
                'id_usuario' => 39,
                'ci' => 99001122,
                'telefono' => 5559999,
                'tipo' => 'Enfermera Quirúrgica',
                'estado' => 'Activo',
                'id_asistente' => 'ASIST012'
            ],
            [
                'id_usuario' => 40,
                'ci' => 00112233,
                'telefono' => 5550000,
                'tipo' => 'Enfermera Neonatal',
                'estado' => 'Activo',
                'id_asistente' => 'ASIST015'
            ],
            [
                'id_usuario' => 41,
                'ci' => 12233445,
                'telefono' => 5551212,
                'tipo' => 'Enfermera General',
                'estado' => 'Activo',
                'id_asistente' => 'ASIST001'
            ],
            [
                'id_usuario' => 42,
                'ci' => 23344556,
                'telefono' => 5553434,
                'tipo' => 'Enfermera de Sala',
                'estado' => 'Activo',
                'id_asistente' => 'ASIST004'
            ],
            [
                'id_usuario' => 43,
                'ci' => 34455667,
                'telefono' => 5555656,
                'tipo' => 'Enfermera de Turno Nocturno',
                'estado' => 'Activo',
                'id_asistente' => 'ASIST007'
            ],
            [
                'id_usuario' => 44,
                'ci' => 45566778,
                'telefono' => 5557878,
                'tipo' => 'Enfermera General',
                'estado' => 'Activo',
                'id_asistente' => 'ASIST010'
            ],
            [
                'id_usuario' => 45,
                'ci' => 56677889,
                'telefono' => 5559090,
                'tipo' => 'Enfermera Jefe de Turno',
                'estado' => 'Activo',
                'id_asistente' => 'ASIST013'
            ],
        ];

        DB::table('enfermeras')->insert($enfermeras);
    }
}
