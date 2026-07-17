<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Medicamentos externos en evaluaciones: el paciente los compra/trae por su
     * cuenta. Se registran para el historial (no repetir dosis) pero NO descuentan
     * stock ni generan cargo. Un ítem externo = facturable false.
     */
    public function up(): void
    {
        Schema::table('evaluacion_items', function (Blueprint $table) {
            $table->boolean('facturable')->default(true)->after('precio_snapshot');
            $table->string('observacion', 255)->nullable()->after('facturable');
        });
    }

    public function down(): void
    {
        Schema::table('evaluacion_items', function (Blueprint $table) {
            $table->dropColumn(['facturable', 'observacion']);
        });
    }
};
