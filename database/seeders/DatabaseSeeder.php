<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            SeguroSeeder::class,
            EspecialidadMedicoSeeder::class,
            MenuSeeder::class,
            //AlmacenInventarioSeeder::class,
            IpAccessSeeder::class,
            LinameSeeder::class, // precarga catálogo de almacén con la LINAME (sin stock)
            CamillaSeeder::class,
            HabitacionSeeder::class,
            //QuirofanoSeeder::class,
            // PacienteSeeder::class,
            // CuentaCobroSeeder::class,
            //ProcedimientosClinicosSeeder::class,
        ]);
    }
}
