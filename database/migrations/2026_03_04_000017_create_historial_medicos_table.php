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
        Schema::create('historial_medicos', function (Blueprint $table) {
            $table->string('id', 80);
            $table->integer('ci_paciente');
            $table->date('fecha');
            $table->string('detalle', 80);
            $table->char('observaciones', 80)->nullable();
            $table->string('alergias', 80)->nullable();
            $table->foreignId('id_usuario_medico')->constrained('users')->onDelete('cascade')->onUpdate('cascade');
            $table->primary(['id', 'ci_paciente']);
            $table->foreign('ci_paciente')->references('ci')->on('pacientes')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historial_medicos');
    }
};
