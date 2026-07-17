<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * El ENUM 'origen' no incluía 'central' (ni farmacia/usi/hospitalizacion), así que
     * una entrega registrada desde una evaluación de admin/administrador (área = central)
     * fallaba con "Data truncated for column 'origen'". Se alinea el ENUM con las
     * ubicaciones reales de AlmacenStock. Amplía el conjunto (no destructivo): los
     * valores existentes siguen siendo válidos.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE almacen_entregas_paciente MODIFY COLUMN origen ENUM('almacen','central','farmacia','emergencia','cirugia','hospitalizacion','uti','usi','neonato','internacion') NOT NULL DEFAULT 'almacen'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE almacen_entregas_paciente MODIFY COLUMN origen ENUM('emergencia','internacion','uti','cirugia','almacen','neonato') NOT NULL DEFAULT 'almacen'");
    }
};
