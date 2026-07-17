<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Sprint 4 — Datos reales del seguro (modelarlo como relación de seguro, no como descuento):
 *   - seguros.nit: NIT de la aseguradora (receptor fiscal real de la venta cubierta / RCV).
 *   - pacientes.seguro_poliza + vigencia: identidad de la póliza del afiliado y su validez.
 *   - cuenta_cobros.seguro_nro_autorizacion: código de autorización emitido por la
 *     aseguradora (distinto del usuario interno que aprobó), clave para la conciliación.
 *
 * Todo aditivo/nullable: no rompe datos existentes.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('seguros', function (Blueprint $table) {
            $table->string('nit', 20)->nullable()->after('telefono');
        });

        Schema::table('pacientes', function (Blueprint $table) {
            $table->string('seguro_poliza', 60)->nullable()->after('seguro_id');
            $table->date('seguro_vigencia_desde')->nullable()->after('seguro_poliza');
            $table->date('seguro_vigencia_hasta')->nullable()->after('seguro_vigencia_desde');
        });

        Schema::table('cuenta_cobros', function (Blueprint $table) {
            $table->string('seguro_nro_autorizacion', 60)->nullable()->after('seguro_observaciones');
        });
    }

    public function down(): void
    {
        Schema::table('seguros', fn (Blueprint $table) => $table->dropColumn('nit'));
        Schema::table('pacientes', fn (Blueprint $table) => $table->dropColumn(['seguro_poliza', 'seguro_vigencia_desde', 'seguro_vigencia_hasta']));
        Schema::table('cuenta_cobros', fn (Blueprint $table) => $table->dropColumn('seguro_nro_autorizacion'));
    }
};
