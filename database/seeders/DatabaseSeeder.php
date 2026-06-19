<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

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

        // Asigna los códigos internos de producto/servicio que los seeders no
        // pudieron llenar: los inserts raw saltan el trait GeneraCodigoCatalogo,
        // y WithoutModelEvents (arriba) silencia el evento `created` del que
        // cuelga el trait incluso en los seeders Eloquent. El backfill es
        // idempotente y es el chokepoint único de asignación de códigos.
        Artisan::call('codigos:backfill');
    }
}
