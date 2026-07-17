<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Trazabilidad NC → anulación de cargo: cuando una devolución (Nota de
     * Crédito) anula cargos automáticamente, cada evento de anulación queda
     * marcado con el id de la NC. Así, anular la NC puede revertir EXACTAMENTE
     * las anulaciones que ella creó (y no otras hechas a mano en Correcciones).
     */
    public function up(): void
    {
        Schema::table('cuenta_cobro_detalle_eliminados', function (Blueprint $table) {
            $table->string('devolucion_id')->nullable()->after('motivo_eliminacion');

            $table->foreign('devolucion_id')->references('id')->on('devoluciones');
            $table->index('devolucion_id');
        });
    }

    public function down(): void
    {
        Schema::table('cuenta_cobro_detalle_eliminados', function (Blueprint $table) {
            $table->dropForeign(['devolucion_id']);
            $table->dropIndex(['devolucion_id']);
            $table->dropColumn('devolucion_id');
        });
    }
};
