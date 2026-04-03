<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class FarmaciaRoleSeeder extends Seeder
{
    public function run()
    {
        User::updateOrCreate(
            ['email' => 'farmacia@example.com'],
            [
                'name' => 'Farmacia User',
                'password' => bcrypt('password'),
                'role' => 'farmacia',
                'is_active' => true
            ]
        );
    }
}
