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
        Schema::create('pago_internos', function (Blueprint $table) {
            $table->string('nro', 15)->primary();
            $table->string('detalle', 80);
            $table->float('saldo_a_pagar');
            $table->float('total_cancelado');
            $table->timestamp('fecha');
            $table->integer('ci_interno');
            $table->foreign('ci_interno')->references('ci')->on('internos')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pago_internos');
    }
};
