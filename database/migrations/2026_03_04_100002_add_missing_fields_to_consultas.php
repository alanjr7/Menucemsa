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
        Schema::table('consultas', function (Blueprint $table) {
            // Agregar campos que faltan para el flujo de consulta externa
            $table->integer('ci_paciente')->nullable()->after('codigo_especialidad');
            $table->integer('ci_medico')->nullable()->after('ci_paciente');
            $table->boolean('estado_pago')->default(false)->after('ci_medico');
            $table->string('id_caja', 15)->nullable()->after('estado_pago');
            
            // Agregar claves foráneas (sin restricciones por ahora)
            // $table->foreign('ci_paciente')->references('ci')->on('pacientes');
            // $table->foreign('ci_medico')->references('ci')->on('medicos');
            // $table->foreign('id_caja')->references('id')->on('cajas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('consultas', function (Blueprint $table) {
            $table->dropColumn(['ci_paciente', 'ci_medico', 'estado_pago', 'id_caja']);
        });
    }
};
