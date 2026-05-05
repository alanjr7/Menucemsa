<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('almacen_dispensaciones', function (Blueprint $table) {
            $table->id();
            $table->enum('ubicacion_origen', [
                'central', 'emergencia', 'cirugia', 'hospitalizacion',
                'uti', 'usi', 'neonato', 'internacion',
            ])->default('central');
            $table->enum('ubicacion_destino', [
                'emergencia', 'cirugia', 'hospitalizacion',
                'uti', 'usi', 'neonato', 'internacion',
            ]);
            $table->foreignId('dispensado_por')->constrained('users')->restrictOnDelete();
            $table->string('recibido_por', 150)->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamp('fecha_dispensacion')->useCurrent();
            $table->timestamps();

            $table->index(['ubicacion_destino', 'fecha_dispensacion'], 'idx_dest_fecha');
            $table->index(['dispensado_por', 'fecha_dispensacion'], 'idx_dispensado_fecha');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('almacen_dispensaciones');
    }
};
