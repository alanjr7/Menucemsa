<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evaluaciones', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('paciente_id')->nullable();
            $table->foreign('paciente_id')->references('id')->on('pacientes')->onDelete('cascade');
            $table->string('area', 50);
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('observaciones')->nullable();
            $table->json('signos_vitales')->nullable();
            $table->unsignedBigInteger('episodio_id')->nullable();
            $table->foreign('episodio_id')->references('id')->on('episodios')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluaciones');
    }
};
