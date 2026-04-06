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
        Schema::create('pacientes', function (Blueprint $table) {
            $table->integer('ci')->primary();
            $table->string('nombre', 120);
            $table->enum('sexo', ['M', 'F', 'otro']);
            $table->date('fecha_nacimiento')->nullable();
            $table->string('direccion', 120)->nullable();
            $table->string('telefono', 20);
            $table->string('correo', 100)->nullable();
            $table->unsignedBigInteger('seguro_id')->nullable();
            $table->string('triage_id', 50)->nullable();
            $table->string('registro_codigo', 80)->nullable();
            $table->foreign('seguro_id')->references('id')->on('seguros')->onDelete('set null');
            $table->foreign('triage_id')->references('id')->on('triages')->onDelete('set null');
            $table->foreign('registro_codigo')->references('codigo')->on('registros')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pacientes');
    }
};
