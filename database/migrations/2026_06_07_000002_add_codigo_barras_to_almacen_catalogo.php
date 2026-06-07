<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('almacen_catalogo', function (Blueprint $table) {
            if (! Schema::hasColumn('almacen_catalogo', 'codigo_barras')) {
                $table->string('codigo_barras', 50)->nullable()->after('nombre')->unique();
            }
        });
    }

    public function down(): void
    {
        Schema::table('almacen_catalogo', function (Blueprint $table) {
            if (Schema::hasColumn('almacen_catalogo', 'codigo_barras')) {
                $table->dropUnique(['codigo_barras']);
                $table->dropColumn('codigo_barras');
            }
        });
    }
};
