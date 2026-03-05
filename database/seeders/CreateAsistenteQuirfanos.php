<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AsistenteQuirofanos;
use App\Models\Quirofano;

class CreateAsistenteQuirfanos extends Seeder
{
    public function run(): void
    {
        // Create a quirofano first
        $quirofano = Quirofano::firstOrCreate(
            ['nro' => 1],
            [
                'tipo' => 'General',
                'estado' => 'Activo'
            ]
        );
        
        // Create a basic asistente record
        AsistenteQuirofanos::firstOrCreate(
            ['id' => 'ASIST001', 'nro_quirofano' => 1],
            [
                'descripcion' => 'Asistente General de Quirofano'
            ]
        );
        
        $this->command->info('Asistente quirofanos record created successfully');
    }
}
