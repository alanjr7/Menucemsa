<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class EmergencyRoleSeeder extends Seeder
{
    public function run(): void
    {
        // Crear usuario de ejemplo para emergencias con el rol existente
        $user = User::firstOrCreate(
            ['email' => 'emergencia@menucemsa.com'],
            [
                'name' => 'Personal Emergencias',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role' => 'emergencia', // Usar el rol existente
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $this->command->info('Usuario de emergencias creado exitosamente.');
        $this->command->info('Email: emergencia@menucemsa.com');
        $this->command->info('Password: password');
        $this->command->info('Role: emergencia');
    }
}
