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
            $table->id();
            $table->string('nombre_empresa', 120);
            $table->string('tipo', 80);
            $table->string('telefono', 20)->nullable();
            $table->string('formulario', 80);
            $table->enum('estado', ['activo', 'inactivo'])->default('activo');
            $table->enum('tipo_cobertura', ['porcentaje', 'solo_consulta', 'tope_monto'])->default('porcentaje');
            $table->decimal('cobertura_porcentaje', 5, 2)->nullable();
            $table->decimal('tope_monto', 10, 2)->nullable();
            $table->decimal('copago_porcentaje', 5, 2)->nullable();
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
