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
        Schema::table('cajas', function (Blueprint $table) {
            // Hacer que nro_factura sea nullable para permitir consultas pendientes de pago
            $table->integer('nro_factura')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cajas', function (Blueprint $table) {
            // Revertir a NOT NULL (esto podría causar problemas si hay registros nulos)
            $table->integer('nro_factura')->nullable(false)->change();
        });
    }
};
