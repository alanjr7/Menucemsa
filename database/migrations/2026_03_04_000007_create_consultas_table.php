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
            $table->string('nro', 250)->primary();
            $table->date('fecha');
            $table->time('hora');
            $table->string('motivo', 80);
            $table->string('observaciones', 80);
            $table->string('codigo_especialidad', 150);
            $table->foreign('codigo_especialidad')->references('codigo')->on('especialidades')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('ci_paciente')->nullable();
            $table->integer('ci_medico')->nullable();
            $table->boolean('estado_pago')->default(false);
            $table->string('id_caja', 250)->nullable();
            $table->string('estado')->default('pendiente');
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
