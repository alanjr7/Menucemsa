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
        Schema::create('cajas', function (Blueprint $table) {
            $table->string('id', 250)->primary();
            $table->timestamp('fecha');
            $table->decimal('total_dia', 10, 2);
            $table->string('tipo', 80);
            $table->enum('metodo_pago', ['efectivo', 'transferencia', 'tarjeta', 'qr'])->nullable();
            $table->string('referencia', 255)->nullable();
            $table->enum('estado', ['pendiente', 'pagado', 'anulado'])->default('pendiente');
            $table->integer('nro_factura')->nullable();
            $table->string('farmacia_id', 20)->nullable();
            $table->decimal('monto_pagado', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cajas');
    }
};
