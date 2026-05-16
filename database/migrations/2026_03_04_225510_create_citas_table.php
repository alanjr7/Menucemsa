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
        Schema::create('citas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('paciente_id');
            $table->integer('ci_medico');
            $table->string('codigo_especialidad', 20);
            $table->date('fecha');
            $table->time('hora');
            $table->text('motivo')->nullable();
            $table->enum('estado', ['programado', 'confirmado', 'en_atencion', 'atendido', 'cancelado', 'no_asistio'])->default('programado');
            $table->text('observaciones')->nullable();
            $table->string('tipo_ingreso')->nullable();
            $table->boolean('confirmado')->default(false);
            $table->timestamp('fecha_confirmacion')->nullable();
            $table->boolean('llamado')->default(false);
            $table->timestamp('fecha_llamada')->nullable();
            $table->text('notas_llamada')->nullable();
            $table->unsignedBigInteger('user_registro_id')->nullable();
            $table->unsignedBigInteger('user_confirmacion_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Índices
            $table->index(['fecha', 'hora']);
            $table->index('paciente_id');
            $table->index('ci_medico');
            $table->index('codigo_especialidad');
            $table->index('estado');

            // Claves foráneas
            $table->foreign('paciente_id')->references('id')->on('pacientes')->onDelete('cascade');
            $table->foreign('ci_medico')->references('ci')->on('medicos')->onDelete('cascade');
            $table->foreign('codigo_especialidad')->references('codigo')->on('especialidades');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('citas');
    }
};
