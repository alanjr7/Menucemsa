<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('almacen_entregas_paciente', function (Blueprint $table) {
            if (!Schema::hasColumn('almacen_entregas_paciente', 'origen')) {
                $table->enum('origen', ['emergencia', 'internacion', 'uti', 'cirugia', 'almacen'])
                      ->default('almacen')
                      ->after('entregado_por');
            }
            if (!Schema::hasColumn('almacen_entregas_paciente', 'referencia_id')) {
                $table->unsignedBigInteger('referencia_id')->nullable()->after('origen');
            }
            if (!Schema::hasColumn('almacen_entregas_paciente', 'catalogo_id')) {
                $table->foreignId('catalogo_id')
                      ->nullable()
                      ->constrained('almacen_catalogo')
                      ->nullOnDelete()
                      ->after('referencia_id');
            }
            if (!Schema::hasColumn('almacen_entregas_paciente', 'cantidad')) {
                $table->integer('cantidad')->default(1)->after('catalogo_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('almacen_entregas_paciente', function (Blueprint $table) {
            $table->dropColumn(['origen', 'referencia_id', 'catalogo_id', 'cantidad']);
        });
    }
};
