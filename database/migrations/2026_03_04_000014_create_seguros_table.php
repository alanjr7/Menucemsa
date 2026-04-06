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
        Schema::create('seguros', function (Blueprint $table) {
            $table->integer('codigo')->primary();
            $table->string('nombre_empresa', 80);
            $table->string('tipo', 80);
            $table->string('cobertura', 80);
            $table->integer('telefono')->nullable();
            $table->string('formulario', 80);
            $table->string('estado', 80);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seguros');
    }
};
