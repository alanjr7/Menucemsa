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
            $table->id();
            $table->integer('ci_paciente');
            $table->date('fecha');
            $table->text('detalle');
            $table->text('observaciones')->nullable();
            $table->string('alergias', 255)->nullable();
            $table->foreignId('user_medico_id')->constrained('users')->onDelete('cascade');
            $table->foreign('ci_paciente')->references('ci')->on('pacientes')->onDelete('cascade');
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
