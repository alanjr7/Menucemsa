<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventario_farmacia', function (Blueprint $table) {
            $table->id();
            $table->string('farmacia_id', 20);
            $table->enum('tipo_item', ['medicamento', 'insumo']);
            $table->string('codigo_item', 20);
            $table->string('laboratorio', 80)->nullable();
            $table->date('fecha_vencimiento')->nullable();
            $table->string('tipo', 80)->nullable();
            $table->string('requerimiento', 80)->nullable();
            $table->integer('stock_minimo')->default(0);
            $table->integer('stock_disponible')->default(0);
            $table->integer('reposicion')->default(0);
            $table->string('lote', 50)->nullable();
            $table->date('fecha_ingreso')->nullable();
            $table->foreign('farmacia_id')->references('id')->on('farmacias')->onDelete('cascade');
            $table->index(['tipo_item', 'codigo_item']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventario_farmacia');
    }
};
