<?php

namespace Database\Seeders;

use App\Models\Quirofano;
use Illuminate\Database\Seeder;

class QuirofanoSeeder extends Seeder
{
    public function run(): void
    {
        $quirofanos = [
            [
                'id' => 1,
                'tipo' => 'Quirófano General 1',
                'estado' => 'disponible',
                'restriccion_externa' => null,
            ],
            [
                'id' => 2,
                'tipo' => 'Quirófano General 2',
                'estado' => 'disponible',
                'restriccion_externa' => null,
            ],
            [
                'id' => 3,
                'tipo' => 'Sala de Partos',
                'estado' => 'disponible',
                'restriccion_externa' => ['parto'],
            ],
        ];

        foreach ($quirofanos as $q) {
            Quirofano::updateOrCreate(
                ['id' => $q['id']],
                [
                    'tipo' => $q['tipo'],
                    'estado' => $q['estado'],
                    'restriccion_externa' => $q['restriccion_externa'],
                ]
            );
        }
    }
}
