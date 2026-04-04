<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cajas', function (Blueprint $table) {
            $table->string('metodo_pago')->nullable()->after('tipo');
            $table->string('referencia')->nullable()->after('metodo_pago');
            $table->string('estado')->nullable()->default('pendiente')->after('referencia');
        });
    }

    public function down(): void
    {
        Schema::table('cajas', function (Blueprint $table) {
            $table->dropColumn(['metodo_pago', 'referencia', 'estado']);
        });
    }
};
