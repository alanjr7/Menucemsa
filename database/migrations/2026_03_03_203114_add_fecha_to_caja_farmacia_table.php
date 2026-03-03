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
        Schema::table('CAJA_FARMACIA', function (Blueprint $table) {
            $table->date('FECHA')->nullable()->after('TOTAL');
            $table->index('FECHA');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('CAJA_FARMACIA', function (Blueprint $table) {
            $table->dropIndex(['FECHA']);
            $table->dropColumn('FECHA');
        });
    }
};
