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
            $table->id();
            $table->date('fecha')->nullable();
            $table->time('hora')->nullable();
            $table->string('estado', 60);
            $table->text('observaciones')->nullable();
            $table->string('hospitalizacion_id', 30);
            $table->integer('factura_nro');
            $table->foreign('hospitalizacion_id')->references('id')->on('hospitalizaciones')->onDelete('cascade');
            $table->foreign('factura_nro')->references('nro')->on('facturas')->onDelete('cascade');
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
