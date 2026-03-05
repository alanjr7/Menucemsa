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
        Schema::create('movimientos_caja', function (Blueprint $table) {
            $table->id();
            $table->foreignId('caja_session_id')->constrained('caja_sessions')->onDelete('cascade');
            $table->enum('tipo', ['ingreso', 'egreso']);
            $table->string('concepto', 255);
            $table->decimal('monto', 10, 2);
            $table->string('referencia')->nullable(); // Nro factura, recibo, etc.
            $table->string('metodo_pago')->nullable(); // EFECTIVO, TARJETA, etc.
            $table->morphs('movable'); // Polimórfico: puede ser Consulta, VentaFarmacia, etc.
            $table->text('observaciones')->nullable();
            $table->timestamps();
            
            $table->index(['caja_session_id', 'tipo']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimientos_caja');
    }
};
