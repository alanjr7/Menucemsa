<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Paciente;
use App\Models\Seguro;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class PacienteSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('es_ES');
        // Obtenemos IDs de seguros existentes para asociar algunos
        $segurosIds = Seguro::pluck('id')->toArray();
        $expediciones = ['LP', 'SC', 'CB', 'OR', 'PT', 'CH', 'TJ', 'BE', 'PD'];

        for ($i = 0; $i < 30; $i++) {
            Paciente::create([
                'ci' => $faker->unique()->numberBetween(1000000, 9999999),
                'nombre' => $faker->name,
                'sexo' => $faker->randomElement(['M', 'F']),
                'fecha_nacimiento' => $faker->date('Y-m-d', '2005-01-01'),
                'lugar_expedicion' => $faker->randomElement($expediciones),
                'nacionalidad' => 'Boliviana',
                'estado_civil' => $faker->randomElement(['Soltero(a)', 'Casado(a)', 'Divorciado(a)', 'Viudo(a)']),
                'direccion' => $faker->address,
                'telefono' => $faker->phoneNumber,
                'correo' => $faker->safeEmail,
                'profesion' => $faker->jobTitle,
                'empresa_trabajo' => $faker->company,
                // 70% de probabilidad de tener seguro
                'seguro_id' => $faker->boolean(70) ? $faker->randomElement($segurosIds) : null,
                'registro_codigo' => null, // Normalmente se llena en la atención
            ]);
        }
    }
}
