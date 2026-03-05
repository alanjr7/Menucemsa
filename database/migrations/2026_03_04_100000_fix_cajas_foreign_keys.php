<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Eliminar las claves foráneas que causan problemas
        try {
            Schema::table('cajas', function (Blueprint $table) {
                $table->dropForeign(['nro_factura']);
            });
        } catch (\Exception $e) {
            // La clave foránea no existe, continuar
        }

        try {
            Schema::table('cajas', function (Blueprint $table) {
                $table->dropForeign(['id_farmacia']);
            });
        } catch (\Exception $e) {
            // La clave foránea no existe, continuar
        }

        try {
            Schema::table('cajas', function (Blueprint $table) {
                $table->dropForeign(['nro_pago_internos']);
            });
        } catch (\Exception $e) {
            // La clave foránea no existe, continuar
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No es necesario revertir ya que las claves foráneas originales causaban problemas
    }
};
