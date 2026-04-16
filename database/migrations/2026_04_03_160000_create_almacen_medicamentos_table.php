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
        Schema::create('almacen_medicamentos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 255);
            $table->text('descripcion')->nullable();
            $table->enum('area', ['emergencia', 'cirugia', 'hospitalizacion', 'uti', 'usi', 'neonato','internacion']);
            $table->decimal('precio', 10, 2)->nullable();
            $table->date('fecha_vencimiento')->nullable();
            $table->string('lote', 100)->nullable();
            $table->integer('cantidad')->default(0);
            $table->integer('stock_minimo')->default(0);
            $table->string('unidad_medida', 50)->default('unidades');
            $table->enum('tipo', ['medicamento', 'insumo'])->default('medicamento');
            $table->boolean('activo')->default(true);
            $table->text('observaciones')->nullable();
            $table->timestamps();
            
            $table->index(['area', 'activo']);
            $table->index('fecha_vencimiento');
            $table->index('nombre');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('almacen_medicamentos');
    }
};
