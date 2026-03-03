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
        Schema::table('INSUMOS', function (Blueprint $table) {
            $table->decimal('PRECIO', 10, 2)->default(0)->after('DESCRIPCION');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('INSUMOS', function (Blueprint $table) {
            $table->dropColumn('PRECIO');
        });
    }
};
