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
        Schema::create('cirugias', function (Blueprint $table) {
            $table->string('nro', 15)->primary();
            $table->date('fecha');
            $table->time('hora');
            $table->string('tipo', 80);
            $table->string('descripcion', 80);
            $table->string('nro_emergencia', 15);
            $table->integer('nro_quirofano');
            $table->foreign('nro_emergencia')->references('nro')->on('emergencias')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('nro_quirofano')->references('nro')->on('quirofanos')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cirugias');
    }
};
