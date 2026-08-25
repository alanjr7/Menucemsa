<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('citas_quirurgicas', function (Blueprint $table) {
            if (!Schema::hasColumn('citas_quirurgicas', 'equipamientos_detalle')) {
                $table->json('equipamientos_detalle')->nullable()->after('equipamiento_precio');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('citas_quirurgicas', function (Blueprint $table) {
            if (Schema::hasColumn('citas_quirurgicas', 'equipamientos_detalle')) {
                $table->dropColumn('equipamientos_detalle');
            }
        });
    }
};
