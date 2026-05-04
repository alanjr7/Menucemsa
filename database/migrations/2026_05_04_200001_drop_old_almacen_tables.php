<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Quitar FK antes de dropear la tabla referenciada
        Schema::table('hosp_medicamentos_administrados', function (Blueprint $table) {
            $table->dropForeign(['medicamento_id']);
        });

        Schema::dropIfExists('dispensaciones_almacen');
        Schema::dropIfExists('almacen_medicamentos');
    }

    public function down(): void
    {
        // No se restauran las tablas viejas
    }
};
