<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear usuario administrador
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@menucemsa.com',
            'password' => bcrypt('admin123'),
            'role' => 'admin',
        ]);

        // Crear usuario recepción
        User::create([
            'name' => 'Recepción',
            'email' => 'recepcion@menucemsa.com',
            'password' => bcrypt('recepcion123'),
            'role' => 'reception',
        ]);
    }
}
