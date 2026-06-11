<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('almacen_catalogo', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('codigo_barras', 50)->nullable()->unique();
            $table->string('nombre_generico')->nullable();
            $table->string('concentracion')->nullable();
            $table->string('forma_farmaceutica')->nullable();
            $table->boolean('requiere_receta')->default(false);
            $table->string('categoria')->nullable();
            $table->text('descripcion')->nullable();
            $table->string('unidad_medida', 50)->default('unidades');
            $table->enum('tipo', ['medicamento', 'insumo'])->default('medicamento');
            $table->boolean('activo')->default(true);
            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->index('nombre');
            $table->index(['tipo', 'activo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('almacen_catalogo');
    }
};
