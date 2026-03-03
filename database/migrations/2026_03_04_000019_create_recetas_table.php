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
        Schema::create('recetas', function (Blueprint $table) {
            $table->string('nro', 15)->primary();
            $table->date('fecha');
            $table->string('indicaciones', 80);
            $table->foreignId('id_usuario_medico')->constrained('users')->onDelete('cascade')->onUpdate('cascade');
            $table->string('nro_consulta', 15);
            $table->foreign('nro_consulta')->references('nro')->on('consultas')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recetas');
    }
};
