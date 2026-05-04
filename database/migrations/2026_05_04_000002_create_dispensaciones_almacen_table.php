<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dispensaciones_almacen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('almacen_medicamento_id')->constrained('almacen_medicamentos')->onDelete('restrict');
            $table->integer('cantidad');
            $table->enum('area_destino', ['emergencia','cirugia','hospitalizacion','uti','usi','neonato','internacion']);
            $table->foreignId('dispensado_por')->constrained('users')->onDelete('restrict');
            $table->string('recibido_por', 150)->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamp('fecha_dispensacion')->useCurrent();
            $table->timestamps();

            $table->index(['almacen_medicamento_id', 'fecha_dispensacion'], 'idx_disp_alm_med_fecha');
            $table->index(['area_destino', 'fecha_dispensacion'], 'idx_disp_area_fecha');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dispensaciones_almacen');
    }
};
