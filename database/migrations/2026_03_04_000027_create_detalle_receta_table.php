<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('detalle_receta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('receta_id')->constrained('recetas')->onDelete('cascade');
            $table->string('codigo_medicamento', 20);
            $table->string('dosis', 80)->nullable();
            $table->decimal('subtotal', 10, 2)->nullable();
            $table->foreign('codigo_medicamento')->references('codigo')->on('medicamentos');
        });
    }

    public function down()
    {
        Schema::dropIfExists('detalle_receta');
    }
};
