<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * La "ganancia" del lote pasa de ser un PORCENTAJE a un MONTO ABSOLUTO en Bs.
 * (compra 5, venta 6 => ganancia 1 Bs). Antes: precio_venta = compra * (1 + %/100).
 * Ahora: precio_venta = compra + ganancia.
 *
 * - Se renombra la columna para que el nombre no mienta (porcentaje_ganancia -> ganancia).
 * - Se ensancha decimal(5,2) -> decimal(10,2): una ganancia en Bs puede superar 999,99
 *   en ítems caros (debe poder igualar el rango de precio_compra/precio_venta).
 * - Se RECALCULAN los datos existentes: precio_venta fue siempre el valor autoritativo
 *   (el cobro lo usa), así que ganancia se deriva de él y el cobro no cambia de monto.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('almacen_lotes', function (Blueprint $table) {
            $table->renameColumn('porcentaje_ganancia', 'ganancia');
        });

        Schema::table('almacen_lotes', function (Blueprint $table) {
            $table->decimal('ganancia', 10, 2)->nullable()->change();
        });

        // Backfill: reinterpretar el dato viejo (que aún contiene el porcentaje) como Bs.
        // 1) Sin precio de venta pero con el % viejo: convertir % a Bs sobre el costo.
        DB::statement('UPDATE almacen_lotes SET ganancia = ROUND(precio_compra * ganancia / 100, 2)
                       WHERE precio_venta IS NULL AND precio_compra IS NOT NULL');

        // 2) Con precio de venta (autoritativo): ganancia = venta - compra.
        DB::statement('UPDATE almacen_lotes SET ganancia = ROUND(precio_venta - precio_compra, 2)
                       WHERE precio_venta IS NOT NULL AND precio_compra IS NOT NULL');

        // 3) Sin costo: la ganancia en Bs no tiene base; se limpia.
        DB::statement('UPDATE almacen_lotes SET ganancia = NULL WHERE precio_compra IS NULL');
    }

    public function down(): void
    {
        // Best-effort: reconstruir el porcentaje desde la ganancia en Bs y el costo.
        DB::statement('UPDATE almacen_lotes SET ganancia = ROUND(ganancia / precio_compra * 100, 2)
                       WHERE precio_compra IS NOT NULL AND precio_compra > 0');

        Schema::table('almacen_lotes', function (Blueprint $table) {
            $table->decimal('ganancia', 5, 2)->nullable()->change();
        });

        Schema::table('almacen_lotes', function (Blueprint $table) {
            $table->renameColumn('ganancia', 'porcentaje_ganancia');
        });
    }
};
