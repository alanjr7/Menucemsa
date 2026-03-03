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
        Schema::create('altas', function (Blueprint $table) {
            $table->string('id', 15)->primary();
            $table->date('fecha')->nullable();
            $table->time('hora')->nullable();
            $table->string('estado', 80);
            $table->string('observaciones', 80)->nullable();
            $table->string('id_hospitalizacion', 15);
            $table->integer('nro_factura');
            $table->foreign('id_hospitalizacion')->references('id')->on('hospitalizaciones')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('nro_factura')->references('nro')->on('facturas')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('altas');
    }
};
