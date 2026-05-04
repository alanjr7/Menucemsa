<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hosp_medicamentos_administrados', function (Blueprint $table) {
            $table->renameColumn('medicamento_id', 'catalogo_id');
        });

        Schema::table('hosp_medicamentos_administrados', function (Blueprint $table) {
            $table->foreign('catalogo_id')->references('id')->on('almacen_catalogo')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('hosp_medicamentos_administrados', function (Blueprint $table) {
            $table->dropForeign(['catalogo_id']);
            $table->renameColumn('catalogo_id', 'medicamento_id');
        });
    }
};
