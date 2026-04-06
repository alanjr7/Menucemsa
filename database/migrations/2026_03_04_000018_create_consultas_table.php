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
        Schema::create('consultas', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 30)->unique();
            $table->date('fecha');
            $table->time('hora');
            $table->string('motivo', 255);
            $table->text('observaciones')->nullable();
            $table->string('codigo_especialidad', 20);
            $table->integer('ci_paciente')->nullable();
            $table->integer('ci_medico')->nullable();
            $table->boolean('estado_pago')->default(false);
            $table->string('caja_id', 250)->nullable();
            $table->enum('estado', ['pendiente', 'en_atencion', 'atendido', 'cancelado'])->default('pendiente');
            $table->foreign('codigo_especialidad')->references('codigo')->on('especialidades');
            $table->foreign('ci_paciente')->references('ci')->on('pacientes')->onDelete('set null');
            $table->foreign('ci_medico')->references('ci')->on('medicos')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consultas');
    }
};
