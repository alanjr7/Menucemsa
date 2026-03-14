<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EspecialidadesTableSeeder extends Seeder
{
    public function run(): void
    {
        $especialidades = [
            [
                'codigo' => 'ESP001',
                'nombre' => 'Medicina General',
                'descripcion' => 'Atención médica primaria y general'
            ],
            [
                'codigo' => 'ESP002',
                'nombre' => 'Cirugía General',
                'descripcion' => 'Procedimientos quirúrgicos generales'
            ],
            [
                'codigo' => 'ESP003',
                'nombre' => 'Pediatría',
                'descripcion' => 'Atención médica infantil'
            ],
            [
                'codigo' => 'ESP004',
                'nombre' => 'Ginecología y Obstetricia',
                'descripcion' => 'Salud de la mujer y atención del parto'
            ],
            [
                'codigo' => 'ESP005',
                'nombre' => 'Cardiología',
                'descripcion' => 'Enfermedades del corazón y sistema circulatorio'
            ],
            [
                'codigo' => 'ESP006',
                'nombre' => 'Neurología',
                'descripcion' => 'Enfermedades del sistema nervioso'
            ],
            [
                'codigo' => 'ESP007',
                'nombre' => 'Ortopedia y Traumatología',
                'descripcion' => 'Huesos, articulaciones y lesiones traumáticas'
            ],
            [
                'codigo' => 'ESP008',
                'nombre' => 'Anestesiología',
                'descripcion' => 'Anestesia y cuidados intensivos'
            ],
            [
                'codigo' => 'ESP009',
                'nombre' => 'Medicina Interna',
                'descripcion' => 'Enfermedades de adultos'
            ],
            [
                'codigo' => 'ESP010',
                'nombre' => 'Urgencias y Emergencias',
                'descripcion' => 'Atención médica de emergencia'
            ],
            [
                'codigo' => 'ESP011',
                'nombre' => 'Dermatología',
                'descripcion' => 'Enfermedades de la piel'
            ],
            [
                'codigo' => 'ESP012',
                'nombre' => 'Oftalmología',
                'descripcion' => 'Enfermedades de los ojos'
            ],
            [
                'codigo' => 'ESP013',
                'nombre' => 'Otorrinolaringología',
                'descripcion' => 'Oídos, nariz y garganta'
            ],
            [
                'codigo' => 'ESP014',
                'nombre' => 'Psiquiatría',
                'descripcion' => 'Salud mental y trastornos psiquiátricos'
            ],
            [
                'codigo' => 'ESP015',
                'nombre' => 'Endocrinología',
                'descripcion' => 'Sistema endocrino y metabólico'
            ],
            [
                'codigo' => 'ESP016',
                'nombre' => 'Nefrología',
                'descripcion' => 'Enfermedades renales'
            ],
            [
                'codigo' => 'ESP017',
                'nombre' => 'Neumología',
                'descripcion' => 'Enfermedades respiratorias'
            ],
            [
                'codigo' => 'ESP018',
                'nombre' => 'Gastroenterología',
                'descripcion' => 'Sistema digestivo'
            ],
            [
                'codigo' => 'ESP019',
                'nombre' => 'Reumatología',
                'descripcion' => 'Enfermedades reumáticas'
            ],
            [
                'codigo' => 'ESP020',
                'nombre' => 'Medicina Física y Rehabilitación',
                'descripcion' => 'Terapia física y rehabilitación'
            ],
        ];

        DB::table('especialidades')->insert($especialidades);
    }
}
