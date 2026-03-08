<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Especialidad;

class EspecialidadSeeder extends Seeder
{
    public function run(): void
    {
        $especialidades = [
            ['codigo' => 'GENERAL', 'nombre' => 'Medicina General', 'descripcion' => 'Atención primaria y medicina general'],
            ['codigo' => 'CARDIO', 'nombre' => 'Cardiología', 'descripcion' => 'Enfermedades del corazón y sistema circulatorio'],
            ['codigo' => 'PEDIAT', 'nombre' => 'Pediatría', 'descripcion' => 'Atención médica infantil y adolescentes'],
            ['codigo' => 'GINE', 'nombre' => 'Ginecología', 'descripcion' => 'Salud reproductiva femenina'],
            ['codigo' => 'OBSTE', 'nombre' => 'Obstetricia', 'descripcion' => 'Atención del embarazo y parto'],
            ['codigo' => 'DERMA', 'nombre' => 'Dermatología', 'descripcion' => 'Enfermedades de la piel'],
            ['codigo' => 'NEURO', 'nombre' => 'Neurología', 'descripcion' => 'Enfermedades del sistema nervioso'],
            ['codigo' => 'ORTOP', 'nombre' => 'Ortopedia', 'descripcion' => 'Huesos, articulaciones y traumatología'],
            ['codigo' => 'OFTAL', 'nombre' => 'Oftalmología', 'descripcion' => 'Enfermedades de los ojos'],
            ['codigo' => 'OTORR', 'nombre' => 'Otorrinolaringología', 'descripcion' => 'Oídos, nariz y garganta'],
            ['codigo' => 'UROLO', 'nombre' => 'Urología', 'descripcion' => 'Sistema urinario y masculino'],
            ['codigo' => 'ENDOC', 'nombre' => 'Endocrinología', 'descripcion' => 'Hormonas y glándulas'],
            ['codigo' => 'GASTRO', 'nombre' => 'Gastroenterología', 'descripcion' => 'Sistema digestivo'],
            ['codigo' => 'NEUMO', 'nombre' => 'Neumología', 'descripcion' => 'Enfermedades respiratorias'],
            ['codigo' => 'REUMA', 'nombre' => 'Reumatología', 'descripcion' => 'Enfermedades autoinmunes y articulares'],
            ['codigo' => 'PSIQUI', 'nombre' => 'Psiquiatría', 'descripcion' => 'Salud mental y trastornos psiquiátricos'],
        ];

        foreach ($especialidades as $especialidad) {
            Especialidad::firstOrCreate(
                ['codigo' => $especialidad['codigo']],
                [
                    'nombre' => $especialidad['nombre'],
                    'descripcion' => $especialidad['descripcion']
                ]
            );
        }
    }
}
