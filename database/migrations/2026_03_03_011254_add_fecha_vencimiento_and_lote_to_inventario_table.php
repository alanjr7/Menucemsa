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
        Schema::table('INVENTARIO', function (Blueprint $table) {
            $table->date('FECHA_VENCIMIENTO')->nullable()->after('FECHA_INGRESO');
            $table->string('LOTE', 50)->nullable()->after('FECHA_VENCIMIENTO');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('INVENTARIO', function (Blueprint $table) {
            $table->dropColumn(['FECHA_VENCIMIENTO', 'LOTE']);
        });
    }
};
