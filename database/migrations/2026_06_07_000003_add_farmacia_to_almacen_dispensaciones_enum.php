<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE `almacen_dispensaciones` MODIFY `ubicacion_destino` ENUM('farmacia','emergencia','cirugia','hospitalizacion','uti','usi','neonato','internacion') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE `almacen_dispensaciones` MODIFY `ubicacion_destino` ENUM('emergencia','cirugia','hospitalizacion','uti','usi','neonato','internacion') NOT NULL");
    }
};
