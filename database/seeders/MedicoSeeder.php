<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Medico;
use App\Models\Especialidad;
use Illuminate\Support\Facades\DB;

class MedicoSeeder extends Seeder
{
    public function run(): void
    {
        // Find the dirmedico user
        $dirmedicoUser = User::where('role', 'dirmedico')->first();
        
        if ($dirmedicoUser) {
            // Create or find especialidad
            $especialidad = Especialidad::firstOrCreate(
                ['nombre' => 'Medicina General'],
                [
                    'codigo' => 'MED-001',
                    'descripcion' => 'Especialidad de Medicina General'
                ]
            );
            
            // Create medico record for the dirmedico user without asistente for now
            try {
                Medico::firstOrCreate(
                    ['id_usuario' => $dirmedicoUser->id],
                    [
                        'ci' => 12345678, // Example CI
                        'telefono' => 987654321,
                        'estado' => 'Activo',
                        'codigo_especialidad' => $especialidad->codigo,
                        'id_asistente' => null, // Explicitly set to null
                    ]
                );
                
                $this->command->info('Medico record created for user: ' . $dirmedicoUser->name);
            } catch (\Exception $e) {
                $this->command->error('Error creating medico: ' . $e->getMessage());
                
                // Try to get existing medico record
                $medico = Medico::where('id_usuario', $dirmedicoUser->id)->first();
                if ($medico) {
                    $this->command->info('Found existing medico record for user: ' . $dirmedicoUser->name);
                }
            }
        } else {
            $this->command->error('No dirmedico user found');
        }
    }
}
