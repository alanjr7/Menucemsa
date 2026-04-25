<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cuenta_cobro_detalles', function (Blueprint $table) {
            $table->string('area_origen', 50)->nullable()->after('observaciones')
                ->comment('emergencia|quirofano|internacion|uti|farmacia|consulta_externa');
        });
    }

    public function down(): void
    {
        Schema::table('cuenta_cobro_detalles', function (Blueprint $table) {
            $table->dropColumn('area_origen');
        });
    }
};
