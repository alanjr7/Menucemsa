<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pago_cuentas', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('cuenta_cobro_id');
            $table->decimal('monto', 10, 2);
            $table->enum('metodo_pago', ['efectivo', 'transferencia', 'tarjeta', 'qr']);
            $table->string('referencia')->nullable();
            $table->foreignId('usuario_id')->constrained('users');
            $table->foreignId('caja_session_id')->nullable()->constrained('caja_sessions');
            $table->text('observaciones')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('cuenta_cobro_id')->references('id')->on('cuenta_cobros')->onDelete('cascade');
            
            // Índices
            $table->index(['cuenta_cobro_id', 'created_at']);
            $table->index(['metodo_pago', 'created_at']);
            $table->index(['caja_session_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pago_cuentas');
    }
};
