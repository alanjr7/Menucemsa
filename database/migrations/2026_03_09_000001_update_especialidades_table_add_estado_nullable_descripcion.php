<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('especialidades', function (Blueprint $table) {
            if (! Schema::hasColumn('especialidades', 'estado')) {
                $table->enum('estado', ['activo', 'inactivo'])->default('activo')->after('descripcion');
            }

            $table->string('descripcion', 255)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('especialidades', function (Blueprint $table) {
            if (Schema::hasColumn('especialidades', 'estado')) {
                $table->dropColumn('estado');
            }

            $table->string('descripcion', 80)->nullable(false)->change();
        });
    }
};
