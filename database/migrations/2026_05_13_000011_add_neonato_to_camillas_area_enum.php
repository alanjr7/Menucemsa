<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE camillas MODIFY COLUMN area ENUM('uti','emergencia','neonato') NOT NULL DEFAULT 'emergencia'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE camillas MODIFY COLUMN area ENUM('uti','emergencia') NOT NULL DEFAULT 'emergencia'");
    }
};
