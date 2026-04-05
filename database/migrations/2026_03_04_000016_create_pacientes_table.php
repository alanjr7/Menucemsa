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
            $table->string('nombre', 80);
            $table->char('sexo', 10);
            $table->string('direccion', 80)->nullable();
            $table->integer('telefono');
            $table->string('correo', 80)->nullable();
            $table->integer('codigo_seguro');
            $table->string('id_triage', 15);
            $table->string('codigo_registro', 80);
            $table->foreign('codigo_seguro')->references('codigo')->on('seguros')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('id_triage')->references('id')->on('triages')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('codigo_registro')->references('codigo')->on('registros')->onDelete('cascade')->onUpdate('cascade');
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
