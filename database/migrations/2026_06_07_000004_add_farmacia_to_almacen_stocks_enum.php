<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE `almacen_stocks` MODIFY `ubicacion` ENUM('central','farmacia','emergencia','cirugia','hospitalizacion','uti','usi','neonato','internacion') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE `almacen_stocks` MODIFY `ubicacion` ENUM('central','emergencia','cirugia','hospitalizacion','uti','usi','neonato','internacion') NOT NULL");
    }
};
