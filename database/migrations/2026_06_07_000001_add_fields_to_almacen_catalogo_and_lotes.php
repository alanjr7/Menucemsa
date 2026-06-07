<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('almacen_catalogo', function (Blueprint $table) {
            if (!Schema::hasColumn('almacen_catalogo', 'nombre_generico')) {
                $table->string('nombre_generico')->nullable()->after('nombre');
            }
            if (!Schema::hasColumn('almacen_catalogo', 'concentracion')) {
                $table->string('concentracion')->nullable()->after('nombre_generico');
            }
            if (!Schema::hasColumn('almacen_catalogo', 'forma_farmaceutica')) {
                $table->string('forma_farmaceutica')->nullable()->after('concentracion');
            }
            if (!Schema::hasColumn('almacen_catalogo', 'requiere_receta')) {
                $table->boolean('requiere_receta')->default(false)->after('forma_farmaceutica');
            }
            if (!Schema::hasColumn('almacen_catalogo', 'categoria')) {
                $table->string('categoria')->nullable()->after('requiere_receta');
            }
        });

        Schema::table('almacen_lotes', function (Blueprint $table) {
            if (!Schema::hasColumn('almacen_lotes', 'numero_lote_fabricante')) {
                $table->string('numero_lote_fabricante')->nullable()->after('codigo_lote');
            }
            if (!Schema::hasColumn('almacen_lotes', 'cantidad_recibida')) {
                $table->integer('cantidad_recibida')->nullable()->after('cantidad_inicial');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('almacen_catalogo', function (Blueprint $table) {
            if (Schema::hasColumn('almacen_catalogo', 'categoria')) {
                $table->dropColumn('categoria');
            }
            if (Schema::hasColumn('almacen_catalogo', 'requiere_receta')) {
                $table->dropColumn('requiere_receta');
            }
            if (Schema::hasColumn('almacen_catalogo', 'forma_farmaceutica')) {
                $table->dropColumn('forma_farmaceutica');
            }
            if (Schema::hasColumn('almacen_catalogo', 'concentracion')) {
                $table->dropColumn('concentracion');
            }
            if (Schema::hasColumn('almacen_catalogo', 'nombre_generico')) {
                $table->dropColumn('nombre_generico');
            }
        });

        Schema::table('almacen_lotes', function (Blueprint $table) {
            if (Schema::hasColumn('almacen_lotes', 'cantidad_recibida')) {
                $table->dropColumn('cantidad_recibida');
            }
            if (Schema::hasColumn('almacen_lotes', 'numero_lote_fabricante')) {
                $table->dropColumn('numero_lote_fabricante');
            }
        });
    }
};
