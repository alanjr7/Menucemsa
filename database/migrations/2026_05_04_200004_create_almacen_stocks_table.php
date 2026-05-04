<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('almacen_stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lote_id')->constrained('almacen_lotes')->restrictOnDelete();
            $table->enum('ubicacion', [
                'central', 'emergencia', 'cirugia', 'hospitalizacion',
                'uti', 'usi', 'neonato', 'internacion',
            ]);
            $table->integer('cantidad_actual')->default(0);
            $table->integer('stock_minimo')->default(0);
            $table->timestamps();

            $table->unique(['lote_id', 'ubicacion']);
            $table->index(['ubicacion', 'cantidad_actual']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('almacen_stocks');
    }
};
