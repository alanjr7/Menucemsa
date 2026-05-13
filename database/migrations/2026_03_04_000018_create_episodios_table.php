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
            $table->integer('paciente_ci');
            $table->unsignedInteger('numero')->default(1);
            $table->timestamp('fecha_apertura');
            $table->timestamp('fecha_cierre')->nullable();
            $table->enum('estado', ['abierto', 'cerrado'])->default('abierto');
            $table->string('tipo_ingreso', 50)->nullable(); // emergencia|internacion|uti|consulta
            $table->string('motivo_cierre', 255)->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('closed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreign('paciente_ci')->references('ci')->on('pacientes')->onDelete('cascade');
            $table->timestamps();

            $table->index(['paciente_ci', 'estado']);
            $table->unique(['paciente_ci', 'numero']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('episodios');
    }
};
