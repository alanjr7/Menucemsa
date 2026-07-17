<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Los dos casos de negocio de una devolución (diagrama del cliente):
     *  - anula_cargos = true  → "servicio no realizado/cancelado": la NC reversa
     *    la venta completa (dinero + cargo); la cuenta queda en 0 y no se recobra.
     *  - anula_cargos = false → "error de cobro": solo se devuelve el dinero; el
     *    cargo se mantiene y la cuenta vuelve a pendiente para cobrarse bien.
     * Se persiste en la NC para que anular/reactivar re-apliquen el mismo modo.
     */
    public function up(): void
    {
        Schema::table('devoluciones', function (Blueprint $table) {
            $table->boolean('anula_cargos')->default(true)->after('motivo');
        });
    }

    public function down(): void
    {
        Schema::table('devoluciones', function (Blueprint $table) {
            $table->dropColumn('anula_cargos');
        });
    }
};
