<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Anulación segura de ventas de farmacia (devolución con reingreso de stock):
     * la venta NO se borra — pasa a estado ANULADA (ya existía en el enum) y estas
     * columnas registran quién/cuándo/por qué. Reemplaza al hard-delete del
     * endpoint destroy, que dejaba los ingresos sin rastro de auditoría.
     */
    public function up(): void
    {
        Schema::table('ventas_farmacia', function (Blueprint $table) {
            $table->timestamp('anulado_at')->nullable()->after('observaciones');
            $table->foreignId('anulado_por')->nullable()->after('anulado_at')->constrained('users');
            $table->string('motivo_anulacion')->nullable()->after('anulado_por');
        });
    }

    public function down(): void
    {
        Schema::table('ventas_farmacia', function (Blueprint $table) {
            $table->dropForeign(['anulado_por']);
            $table->dropColumn(['anulado_at', 'anulado_por', 'motivo_anulacion']);
        });
    }
};
