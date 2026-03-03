<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('DETALLE_VENTAS_FARMACIA', function (Blueprint $table) {
            $table->id();
            $table->string('CODIGO_VENTA', 20);
            $table->string('CODIGO_PRODUCTO', 15);
            $table->string('TIPO_PRODUCTO', 20); // 'medicamento' o 'insumo'
            $table->string('NOMBRE_PRODUCTO', 200);
            $table->integer('CANTIDAD');
            $table->decimal('PRECIO_UNITARIO', 10, 2);
            $table->decimal('SUBTOTAL', 10, 2);
            
            $table->foreign('CODIGO_VENTA')->references('CODIGO_VENTA')->on('VENTAS_FARMACIA')->onDelete('cascade');
            $table->index(['CODIGO_VENTA', 'CODIGO_PRODUCTO']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('DETALLE_VENTAS_FARMACIA');
    }
};
