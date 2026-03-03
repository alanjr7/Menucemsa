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
            $table->string('id', 15)->primary();
            $table->timestamp('fecha');
            $table->float('total_dia');
            $table->string('tipo', 80);
            $table->integer('nro_factura');
            $table->string('id_farmacia', 15)->nullable();
            $table->string('nro_pago_internos', 15);
            $table->foreign('nro_factura')->references('nro')->on('facturas')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('id_farmacia')->references('id')->on('farmacias')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('nro_pago_internos')->references('nro')->on('pago_internos')->onDelete('cascade')->onUpdate('cascade');
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
