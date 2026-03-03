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
        Schema::create('hospitalizaciones', function (Blueprint $table) {
            $table->string('id', 15)->primary();
            $table->timestamp('fecha_ingreso');
            $table->string('motivo', 80)->nullable();
            $table->string('nro_emergencia', 15);
            $table->foreign('nro_emergencia')->references('nro')->on('emergencias')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hospitalizaciones');
    }
};
