<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Especialidad;
use App\Models\Medico;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class EspecialidadMedicoSeeder extends Seeder
{
    public function run(): void
    {
        // 3 Especialidades
        $especialidades = [
            [
                'codigo' => 'MED-001',
                'nombre' => 'Medicina General',
                'descripcion' => 'Atención médica general y consulta externa',
                'estado' => 'activo',
            ],
            [
                'codigo' => 'CAR-002',
                'nombre' => 'Cardiología',
                'descripcion' => 'Especialidad en enfermedades del corazón y sistema cardiovascular',
                'estado' => 'activo',
            ],
            [
                'codigo' => 'PED-003',
                'nombre' => 'Pediatría',
                'descripcion' => 'Especialidad en salud infantil y del adolescente',
                'estado' => 'activo',
            ],
        ];

        foreach ($especialidades as $esp) {
            Especialidad::firstOrCreate(['codigo' => $esp['codigo']], $esp);
        }

        // 3 Doctores (usuarios + médicos)
        $doctores = [
            [
                'name' => 'Dr. Juan Pérez',
                'email' => 'juan.perez@cemsa.com',
                'ci' => 1234567,
                'telefono' => 70012345,
                'codigo_especialidad' => 'MED-001',
            ],
            [
                'name' => 'Dra. María García',
                'email' => 'maria.garcia@cemsa.com',
                'ci' => 2345678,
                'telefono' => 70023456,
                'codigo_especialidad' => 'CAR-002',
            ],
            [
                'name' => 'Dr. Carlos López',
                'email' => 'carlos.lopez@cemsa.com',
                'ci' => 3456789,
                'telefono' => 70034567,
                'codigo_especialidad' => 'PED-003',
            ],
        ];

        foreach ($doctores as $doc) {
            // Crear usuario con rol doctor (evitar duplicados)
            $user = User::firstOrCreate(
                ['email' => $doc['email']],
                [
                    'name' => $doc['name'],
                    'password' => Hash::make('password'),
                    'role' => 'doctor',
                    'is_active' => true,
                ]
            );

            // Crear médico asociado (evitar duplicados)
            Medico::firstOrCreate(
                ['ci' => $doc['ci']],
                [
                    'user_id' => $user->id,
                    'telefono' => $doc['telefono'],
                    'estado' => 'activo',
                    'codigo_especialidad' => $doc['codigo_especialidad'],
                ]
            );
        }
    }
}
