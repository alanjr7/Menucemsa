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
        Schema::table('hospitalizaciones', function (Blueprint $table) {
            $table->integer('ci_paciente')->nullable()->after('id');
            $table->integer('ci_medico')->nullable()->after('ci_paciente');
            $table->date('fecha_alta')->nullable()->after('fecha_ingreso');
            $table->time('hora_ingreso')->nullable()->after('fecha_alta');
            $table->time('hora_alta')->nullable()->after('hora_ingreso');
            $table->string('diagnostico', 500)->nullable()->after('hora_alta');
            $table->string('tratamiento', 500)->nullable()->after('diagnostico');
            $table->string('estado', 20)->default('Activo')->after('tratamiento');
            $table->string('cama', 10)->nullable()->after('estado');
            
            // $table->foreign('ci_paciente')->references('ci')->on('pacientes')->onDelete('cascade')->onUpdate('cascade');
            // $table->foreign('ci_medico')->references('ci')->on('medicos')->onDelete('set null')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hospitalizaciones', function (Blueprint $table) {
            $table->dropForeign(['ci_paciente']);
            $table->dropForeign(['ci_medico']);
            $table->dropColumn(['ci_paciente', 'ci_medico', 'fecha_alta', 'hora_ingreso', 'hora_alta', 'diagnostico', 'tratamiento', 'estado', 'cama']);
        });
    }
};
