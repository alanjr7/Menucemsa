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
        Schema::table('emergencias', function (Blueprint $table) {
            $table->integer('ci_paciente')->nullable()->after('id_triage');
            $table->integer('ci_medico')->nullable()->after('ci_paciente');
            $table->date('fecha')->nullable()->after('ci_medico');
            $table->time('hora')->nullable()->after('fecha');
            $table->string('motivo', 200)->nullable()->after('hora');
            
            $table->foreign('ci_paciente')->references('ci')->on('pacientes')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('ci_medico')->references('ci')->on('medicos')->onDelete('set null')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('emergencias', function (Blueprint $table) {
            $table->dropForeign(['ci_paciente']);
            $table->dropForeign(['ci_medico']);
            $table->dropColumn(['ci_paciente', 'ci_medico', 'fecha', 'hora', 'motivo']);
        });
    }
};
