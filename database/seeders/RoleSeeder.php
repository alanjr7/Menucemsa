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
        User::firstOrCreate(
            ['email' => 'admin@menucemsa.com'],
            [
                'name' => 'Administrador',
                'password' => bcrypt('admin123'),
                'role' => 'admin',
            ]
        );

        // Crear usuario recepción
        User::firstOrCreate(
            ['email' => 'recepcion@menucemsa.com'],
            [
                'name' => 'Recepción',
                'password' => bcrypt('recepcion123'),
                'role' => 'reception',
            ]
        );

        // Crear usuario Director Médico
        User::firstOrCreate(
            ['email' => 'dirmedico@menucemsa.com'],
            [
                'name' => 'Director Médico',
                'password' => bcrypt('dirmedico123'),
                'role' => 'dirmedico',
            ]
        );

        // Crear usuario Emergencia
        User::firstOrCreate(
            ['email' => 'emergencia@menucemsa.com'],
            [
                'name' => 'Emergencia',
                'password' => bcrypt('emergencia123'),
                'role' => 'emergencia',
            ]
        );

        // Crear usuario Caja
        User::firstOrCreate(
            ['email' => 'caja@menucemsa.com'],
            [
                'name' => 'Caja',
                'password' => bcrypt('caja123'),
                'role' => 'caja',
            ]
        );
    }
}//hol
