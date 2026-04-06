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
            $table->integer('ci')->primary();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('telefono', 20)->nullable();
            $table->enum('estado', ['activo', 'inactivo', 'vacaciones'])->default('activo');
            $table->string('asistente_id', 20)->nullable();
            $table->string('codigo_especialidad', 20)->nullable();
            $table->foreign('asistente_id')->references('id')->on('asistente_quirofanos')->onDelete('set null');
            $table->foreign('codigo_especialidad')->references('codigo')->on('especialidades');
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
