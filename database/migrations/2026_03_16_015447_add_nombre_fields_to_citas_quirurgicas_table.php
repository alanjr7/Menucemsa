<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('citas_quirurgicas', function (Blueprint $table) {
            $table->string('nombre_instrumentista')->nullable();
            $table->string('nombre_anestesiologo')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('citas_quirurgicas', function (Blueprint $table) {
            $table->dropColumn('nombre_instrumentista');
            $table->dropColumn('nombre_anestesiologo');
        });
    }
};
