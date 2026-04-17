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
        Schema::create('enfermeras', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->integer('ci');
            $table->string('telefono', 20)->nullable();
            $table->string('tipo', 80);
            $table->enum('estado', ['activo', 'inactivo'])->default('activo');
            $table->string('area', 50)->default('general');
            $table->string('asistente_id', 20)->nullable();
            $table->primary('user_id');
            $table->unique('ci');
            $table->foreign('asistente_id')->references('id')->on('asistente_quirofanos')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enfermeras');
    }
};
