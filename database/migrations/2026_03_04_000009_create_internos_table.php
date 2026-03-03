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
        Schema::create('internos', function (Blueprint $table) {
            $table->integer('ci')->primary();
            $table->string('nombre', 80);
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->integer('telefono');
            $table->string('lugar_asignado', 80)->nullable();
            $table->string('detalle', 80)->nullable();
            $table->string('estado_formulario', 20);
            $table->foreignId('id_usuario_medico')->constrained('users')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('internos');
    }
};
