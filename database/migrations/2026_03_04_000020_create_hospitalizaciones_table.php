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
        Schema::create('hospitalizaciones', function (Blueprint $table) {
            $table->string('id', 250)->primary();
            $table->integer('ci_paciente')->nullable();
            $table->integer('ci_medico')->nullable();
            $table->timestamp('fecha_ingreso');
            $table->date('fecha_alta')->nullable();
            $table->time('hora_ingreso')->nullable();
            $table->time('hora_alta')->nullable();
            $table->string('diagnostico', 500)->nullable();
            $table->string('tratamiento', 500)->nullable();
            $table->string('estado', 20)->default('Activo');
            $table->string('cama', 250)->nullable();
            $table->string('motivo', 80)->nullable();
            $table->string('nro_emergencia', 250);
            $table->foreign('nro_emergencia')->references('nro')->on('emergencias')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hospitalizaciones');
    }
};
