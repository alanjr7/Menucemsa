<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('episodios', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('paciente_id');
            $table->unsignedInteger('numero')->default(1);
            $table->timestamp('fecha_apertura');
            $table->timestamp('fecha_cierre')->nullable();
            $table->enum('estado', ['abierto', 'cerrado'])->default('abierto');
            $table->string('tipo_ingreso', 50)->nullable();
            $table->string('motivo_cierre', 255)->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('closed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreign('paciente_id')->references('id')->on('pacientes')->onDelete('cascade');
            $table->timestamps();

            $table->index(['paciente_id', 'estado']);
            $table->unique(['paciente_id', 'numero']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('episodios');
    }
};
