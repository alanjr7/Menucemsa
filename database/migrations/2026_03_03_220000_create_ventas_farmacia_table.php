<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ventas_farmacia', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_venta', 20)->unique();
            $table->string('farmacia_id', 20)->nullable();
            $table->string('cliente', 100)->default('Cliente General');
            $table->decimal('total', 10, 2);
            $table->enum('metodo_pago', ['efectivo', 'transferencia', 'tarjeta', 'qr', 'credito']);
            $table->boolean('requiere_receta')->default(false);
            $table->timestamp('fecha_venta')->useCurrent();
            $table->enum('estado', ['COMPLETADA', 'ANULADA', 'PENDIENTE'])->default('COMPLETADA');
            $table->foreignId('caja_diaria_id')->nullable()->constrained('caja_diarias');
            $table->text('observaciones')->nullable();
            
            $table->foreign('farmacia_id')->references('id')->on('farmacias')->onDelete('set null');
            $table->index('fecha_venta');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ventas_farmacia');
    }
};
