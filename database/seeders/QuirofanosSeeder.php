<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Quirofano;

class QuirofanosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $quirofanos = [
            ['nro' => 1, 'tipo' => 'General', 'estado' => 'Activo'],
            ['nro' => 2, 'tipo' => 'Especializado', 'estado' => 'Activo'],
            ['nro' => 3, 'tipo' => 'Urgencias', 'estado' => 'Activo'],
            ['nro' => 4, 'tipo' => 'Pediatrico', 'estado' => 'Activo'],
            ['nro' => 5, 'tipo' => 'General', 'estado' => 'Activo'],
        ];

        foreach ($quirofanos as $quirofano) {
            Quirofano::updateOrCreate(
                ['nro' => $quirofano['nro']],
                $quirofano
            );
        }

        $this->command->info('Quirófanos creados exitosamente');
    }
}
