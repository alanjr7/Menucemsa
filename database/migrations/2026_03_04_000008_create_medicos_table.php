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
        Schema::create('medicos', function (Blueprint $table) {
            $table->foreignId('id_usuario')->constrained('users')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('ci');
            $table->integer('telefono')->nullable();
            $table->string('estado', 80);
            $table->string('id_asistente', 15)->nullable();
            $table->string('codigo_especialidad', 15)->nullable();
            $table->primary(['id_usuario', 'ci']);
            $table->foreign('id_asistente')->references('id')->on('asistente_quirofanos')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('codigo_especialidad')->references('codigo')->on('especialidades')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicos');
    }
};
