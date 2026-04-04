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
        Schema::create('movimiento_cajas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('caja_session_id')->constrained('caja_sessions')->onDelete('cascade');
            $table->enum('tipo', ['ingreso', 'egreso']);
            $table->string('concepto');
            $table->decimal('monto', 10, 2);
            $table->string('referencia')->nullable();
            $table->enum('metodo_pago', ['efectivo', 'transferencia', 'tarjeta', 'qr'])->default('efectivo');
            $table->string('movable_type')->nullable();
            $table->unsignedBigInteger('movable_id')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimiento_cajas');
    }
};
