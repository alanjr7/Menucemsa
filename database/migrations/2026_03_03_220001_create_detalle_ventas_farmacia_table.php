<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detalle_ventas_farmacia', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_venta', 20);
            $table->string('codigo_producto', 20);
            $table->enum('tipo_producto', ['medicamento', 'insumo']);
            $table->string('nombre_producto', 200);
            $table->integer('cantidad');
            $table->decimal('precio_unitario', 10, 2);
            $table->decimal('subtotal', 10, 2);
            
            $table->foreign('codigo_venta')->references('codigo_venta')->on('ventas_farmacia')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detalle_ventas_farmacia');
    }
};
