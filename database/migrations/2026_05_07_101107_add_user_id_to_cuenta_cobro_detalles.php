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
        Schema::table('cuenta_cobro_detalles', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('area_origen')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('cuenta_cobro_detalles', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};
