<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('almacen_lotes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('catalogo_id')->constrained('almacen_catalogo')->restrictOnDelete();
            $table->string('codigo_lote', 100)->nullable();
            $table->date('fecha_vencimiento')->nullable();
            $table->decimal('precio_compra', 10, 2)->nullable();
            $table->decimal('porcentaje_ganancia', 5, 2)->nullable();
            $table->decimal('precio_venta', 10, 2)->nullable();
            $table->integer('cantidad_inicial')->default(0);
            $table->timestamps();

            $table->index('catalogo_id');
            $table->index('fecha_vencimiento');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('almacen_lotes');
    }
};
