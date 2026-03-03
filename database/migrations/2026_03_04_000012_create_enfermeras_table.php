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
            $table->foreignId('id_usuario')->constrained('users')->onDelete('cascade')->onUpdate('cascade');
            $table->integer('ci');
            $table->integer('telefono')->nullable();
            $table->string('tipo', 80);
            $table->string('estado', 80);
            $table->string('id_asistente', 15);
            $table->primary(['id_usuario', 'ci']);
            $table->foreign('id_asistente')->references('id')->on('asistente_quirofanos')->onDelete('cascade')->onUpdate('cascade');
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
