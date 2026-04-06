<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('VENTAS_FARMACIA', function (Blueprint $table) {
            $table->id();
            $table->string('CODIGO_VENTA', 20)->unique();
            $table->string('ID_FARMACIA', 15)->nullable();
            $table->string('CLIENTE', 100)->default('Cliente General');
            $table->decimal('TOTAL', 10, 2);
            $table->string('METODO_PAGO', 20);
            $table->boolean('REQUIERE_RECETA')->default(false);
            $table->timestamp('FECHA_VENTA')->useCurrent();
            $table->string('ESTADO', 20)->default('COMPLETADA');
            $table->foreignId('caja_diaria_id')->nullable();
            $table->text('OBSERVACIONES')->nullable();
            
            $table->foreign('ID_FARMACIA')->references('ID')->on('FARMACIA')->onDelete('set null');
            $table->index(['CODIGO_VENTA', 'FECHA_VENTA']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('VENTAS_FARMACIA');
    }
};
