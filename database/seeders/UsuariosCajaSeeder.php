<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsuariosCajaSeeder extends Seeder
{
    public function run(): void
    {
        $usuarios = [
            [
                'name' => 'Cajero Principal',
                'email' => 'caja@cemsa.com',
                'password' => 'password123',
                'role' => 'caja',
                'is_active' => true,
            ],
            [
                'name' => 'Administrador Caja',
                'email' => 'admincaja@cemsa.com',
                'password' => 'password123',
                'role' => 'admin',
                'is_active' => true,
            ],
            [
                'name' => 'Supervisor Caja',
                'email' => 'supervisor@cemsa.com',
                'password' => 'password123',
                'role' => 'gerente',
                'is_active' => true,
            ],
        ];

        foreach ($usuarios as $usuario) {
            User::updateOrCreate(
                ['email' => $usuario['email']],
                [
                    'name' => $usuario['name'],
                    'password' => Hash::make($usuario['password']),
                    'role' => $usuario['role'],
                    'is_active' => $usuario['is_active'],
                ]
            );
            $this->command->info("Usuario creado/actualizado: {$usuario['email']} ({$usuario['role']})");
        }

        $this->command->info('');
        $this->command->info('=== Usuarios de Caja Creados ===');
        $this->command->info('Email: caja@cemsa.com | Rol: caja | Password: password123');
        $this->command->info('Email: admincaja@cemsa.com | Rol: admin | Password: password123');
        $this->command->info('Email: supervisor@cemsa.com | Rol: gerente | Password: password123');
    }
}
