<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE procedimientos MODIFY COLUMN area ENUM('emergencia','uti','internacion','cirugia','hospitalizacion','neonato') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE procedimientos MODIFY COLUMN area ENUM('emergencia','uti','internacion','cirugia','hospitalizacion') NOT NULL");
    }
};
