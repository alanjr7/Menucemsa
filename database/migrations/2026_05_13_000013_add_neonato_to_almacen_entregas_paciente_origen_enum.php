<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE almacen_entregas_paciente MODIFY COLUMN origen ENUM('emergencia','internacion','uti','cirugia','almacen','neonato') NOT NULL DEFAULT 'almacen'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE almacen_entregas_paciente MODIFY COLUMN origen ENUM('emergencia','internacion','uti','cirugia','almacen') NOT NULL DEFAULT 'almacen'");
    }
};
