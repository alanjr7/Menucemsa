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
        Schema::table('almacen_medicamentos', function (Blueprint $table) {
            $table->decimal('precio_compra', 10, 2)->nullable()->after('precio');
            $table->decimal('porcentaje_ganancia', 5, 2)->nullable()->after('precio_compra');
            $table->decimal('precio_venta', 10, 2)->nullable()->after('porcentaje_ganancia');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('almacen_medicamentos', function (Blueprint $table) {
            $table->dropColumn(['precio_compra', 'porcentaje_ganancia', 'precio_venta']);
        });
    }
};
