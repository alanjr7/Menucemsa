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
            // First run user-related seeders
            RoleSeeder::class,
            ServicioSeeder::class,
            AdminSeeder::class,
            FarmaciaRoleSeeder::class,
            UsuariosSeeder::class,
            QuirofanosSeeder::class,
            
            // Farmacia tables
            FarmaciaTableSeeder::class,
            MedicamentosTableSeeder::class,
            DetalleMedicamentosTableSeeder::class,
            InsumosTableSeeder::class,
            DetalleInsumosTableSeeder::class,
            DetalleRecetaTableSeeder::class,
            InventarioTableSeeder::class,
            CajaFarmaciaTableSeeder::class,
            VentasFarmaciaTableSeeder::class,
            DetalleVentasFarmaciaTableSeeder::class,
            ClientesTableSeeder::class,
            CajaDiariasTableSeeder::class,
            
            // Medical tables (except MedicosTableSeeder for now)
            BitacorasTableSeeder::class,
            TurnosTableSeeder::class,
            AsistenteQuirofanosTableSeeder::class,
            EspecialidadesTableSeeder::class,
            ConsultasTableSeeder::class,
            // MedicosTableSeeder::class, // Temporarily commented
            // InternosTableSeeder::class, // Temporarily commented
            // TurnoInternosTableSeeder::class, // Temporarily commented
            // PagoInternosTableSeeder::class, // Temporarily commented
            // EnfermerasTableSeeder::class, // Temporarily commented
            // TriagesTableSeeder::class, // Temporarily commented
            SegurosTableSeeder::class,
            RegistrosTableSeeder::class,
            // PacientesTableSeeder::class, // Temporarily commented
            // HistorialMedicosTableSeeder::class, // Temporarily commented
            // EmergenciasTableSeeder::class, // Temporarily commented
            // RecetasTableSeeder::class, // Temporarily commented
            
            // Hospital management tables
            // HospitalizacionesTableSeeder::class, // Temporarily commented
            // ProcesosClinicosTableSeeder::class, // Temporarily commented
            // HabitacionesTableSeeder::class, // Temporarily commented
            // CamasTableSeeder::class, // Temporarily commented
            // CirugiasTableSeeder::class, // Temporarily commented
            // PartosTableSeeder::class, // Temporarily commented
            // EstadoCuentasTableSeeder::class, // Temporarily commented
            // PagosTableSeeder::class, // Temporarily commented
            // MetodoPagosTableSeeder::class, // Temporarily commented
            
            // New surgical tables
            // CitasQuirurgicasTableSeeder::class, // Temporarily commented
            // TiposCirugiaTableSeeder::class, // Temporarily commented
        ]);
    }
}
