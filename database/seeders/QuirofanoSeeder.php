<?php

namespace Database\Seeders;

use App\Models\Quirofano;
use Illuminate\Database\Seeder;

class QuirofanoSeeder extends Seeder
{
    public function run(): void
    {
        $quirofanos = [
            ['tipo' => 'Quirófano General 1'],
            ['tipo' => 'Quirófano General 2'],
            ['tipo' => 'Quirófano General 3'],
            ['tipo' => 'Quirófano de Cirugía Cardiovascular'],
            ['tipo' => 'Quirófano de Neurocirugía'],
            ['tipo' => 'Quirófano de Traumatología'],
            ['tipo' => 'Quirófano de Cirugía Plástica'],
            ['tipo' => 'Quirófano de Cirugía Pediátrica'],
            ['tipo' => 'Quirófano de Cirugía Oncológica'],
            ['tipo' => 'Sala de Partos Quirúrgica'],
        ];

        foreach ($quirofanos as $quirofano) {
            Quirofano::create([
                'tipo'   => $quirofano['tipo'],
                'estado' => 'disponible',
            ]);
        }
    }
}
