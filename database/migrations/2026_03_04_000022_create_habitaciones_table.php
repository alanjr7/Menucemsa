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
        Schema::create('habitaciones', function (Blueprint $table) {
            $table->string('id', 15)->primary();
            $table->string('estado', 80);
            $table->string('detalle', 80)->nullable();
            $table->integer('capacidad');
            $table->string('id_hospitalizacion', 15)->nullable();
            $table->foreign('id_hospitalizacion')->references('id')->on('hospitalizaciones')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('habitaciones');
    }
};
