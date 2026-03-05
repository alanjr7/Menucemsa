<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UsuariosSeeder extends Seeder
{
    public function run()
    {
        // Crear usuarios de prueba para diferentes roles
        $usuarios = [
            [
                'name' => 'Juan Ramírez Pérez',
                'email' => 'jramirez@menucemsa.com',
                'password' => bcrypt('password'),
                'role' => 'dirmedico',
            ],
            [
                'name' => 'Ana Torres López',
                'email' => 'atorres@menucemsa.com',
                'password' => bcrypt('password'),
                'role' => 'dirmedico',
            ],
            [
                'name' => 'María García Silva',
                'email' => 'mgarcia@menucemsa.com',
                'password' => bcrypt('password'),
                'role' => 'reception',
            ],
            [
                'name' => 'Carlos López Mendoza',
                'email' => 'clopez@menucemsa.com',
                'password' => bcrypt('password'),
                'role' => 'caja',
            ],
            [
                'name' => 'Pedro Silva Castro',
                'email' => 'psilva@menucemsa.com',
                'password' => bcrypt('password'),
                'role' => 'emergencia',
            ],
            [
                'name' => 'Laura Martínez Díaz',
                'email' => 'lmartinez@menucemsa.com',
                'password' => bcrypt('password'),
                'role' => 'reception',
            ],
            [
                'name' => 'Roberto Sánchez Ruiz',
                'email' => 'rsanchez@menucemsa.com',
                'password' => bcrypt('password'),
                'role' => 'caja',
            ],
            [
                'name' => 'Carmen Vargas Morales',
                'email' => 'cvargas@menucemsa.com',
                'password' => bcrypt('password'),
                'role' => 'emergencia',
            ],
        ];

        foreach ($usuarios as $usuario) {
            User::updateOrCreate(
                ['email' => $usuario['email']],
                $usuario
            );
        }
    }
}
