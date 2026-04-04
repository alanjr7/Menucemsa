<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('uti_tarifario', function (Blueprint $table) {
            $table->id();
            $table->string('concepto', 100);
            $table->enum('tipo', ['estadia', 'alimentacion', 'procedimiento', 'insumo', 'medicamento']);
            $table->decimal('precio', 10, 2);
            $table->string('unidad', 20)->default('unidad');
            $table->boolean('activo')->default(true);
            $table->text('descripcion')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('uti_tarifario');
    }
};
