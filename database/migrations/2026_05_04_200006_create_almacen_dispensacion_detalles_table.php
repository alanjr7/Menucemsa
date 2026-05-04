<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('almacen_dispensacion_detalles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dispensacion_id')->constrained('almacen_dispensaciones')->cascadeOnDelete();
            $table->foreignId('lote_id')->constrained('almacen_lotes')->restrictOnDelete();
            $table->integer('cantidad');
            $table->timestamps();

            $table->index(['dispensacion_id', 'lote_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('almacen_dispensacion_detalles');
    }
};
