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
            $table->string('metodo_pago', 20)->nullable()->after('nro_pago_internos')->comment('EFECTIVO, TARJETA, QR, TRANSFERENCIA');
            $table->string('referencia', 50)->nullable()->after('metodo_pago')->comment('Número de referencia para QR y transferencias');
            $table->string('estado', 20)->default('pendiente')->after('referencia')->comment('pendiente, pagado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cajas', function (Blueprint $table) {
            $table->dropColumn(['metodo_pago', 'referencia', 'estado']);
        });
    }
};
