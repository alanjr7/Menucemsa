<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Medico;
use App\Models\Especialidad;

class CreateMedicalUser extends Seeder
{
    public function run(): void
    {
        // Create a medical user (dirmedico)
        $medicalUser = User::firstOrCreate(
            ['email' => 'doctor@menucemsa.com'],
            [
                'name' => 'Doctor Principal',
                'password' => bcrypt('password'),
                'role' => 'dirmedico'
            ]
        );

        // Create or find especialidad
        $especialidad = Especialidad::firstOrCreate(
            ['nombre' => 'Medicina General'],
            [
                'codigo' => 'MED-001',
                'descripcion' => 'Especialidad de Medicina General'
            ]
        );
        
        // Create medico record for the medical user
        try {
            Medico::firstOrCreate(
                ['id_usuario' => $medicalUser->id],
                [
                    'ci' => 87654321, // Different CI from admin
                    'telefono' => 123456789,
                    'estado' => 'Activo',
                    'codigo_especialidad' => $especialidad->codigo,
                    'id_asistente' => 'ASIST001',
                ]
            );
            
            $this->command->info('Medical user and medico record created successfully');
            $this->command->info('Email: doctor@menucemsa.com');
            $this->command->info('Password: password');
            $this->command->info('Role: dirmedico');
        } catch (\Exception $e) {
            $this->command->error('Error creating medico: ' . $e->getMessage());
        }
    }
}
