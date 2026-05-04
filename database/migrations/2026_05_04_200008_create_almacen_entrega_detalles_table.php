<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('almacen_entrega_detalles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entrega_id')->constrained('almacen_entregas_paciente')->cascadeOnDelete();
            $table->foreignId('dispensacion_detalle_id')->constrained('almacen_dispensacion_detalles')->restrictOnDelete();
            $table->integer('cantidad');
            $table->timestamps();

            $table->index('entrega_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('almacen_entrega_detalles');
    }
};
