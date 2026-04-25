<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cuenta_cobros', function (Blueprint $table) {
            $table->unsignedInteger('episodio_numero')->default(1)->after('es_post_pago')
                ->comment('Número de episodio del paciente, se incrementa en cada nuevo ingreso');
        });
    }

    public function down(): void
    {
        Schema::table('cuenta_cobros', function (Blueprint $table) {
            $table->dropColumn('episodio_numero');
        });
    }
};
