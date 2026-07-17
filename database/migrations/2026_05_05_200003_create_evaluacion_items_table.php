<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evaluacion_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluacion_id')->constrained('evaluaciones')->onDelete('cascade');
            $table->enum('tipo', ['medicamento', 'insumo', 'procedimiento']);
            $table->unsignedBigInteger('item_id');
            $table->string('nombre_snapshot');
            $table->integer('cantidad');
            $table->decimal('precio_snapshot', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluacion_items');
    }
};
