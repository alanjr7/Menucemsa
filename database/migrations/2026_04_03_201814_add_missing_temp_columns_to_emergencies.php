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
        Schema::table('emergencies', function (Blueprint $table) {
            // Check if columns don't exist before adding
            if (!Schema::hasColumn('emergencies', 'is_temp_id')) {
                $table->boolean('is_temp_id')->default(false)->after('ci_paciente');
            }
            if (!Schema::hasColumn('emergencies', 'temp_id')) {
                $table->string('temp_id')->nullable()->after('ci_paciente');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('emergencies', function (Blueprint $table) {
            if (Schema::hasColumn('emergencies', 'is_temp_id')) {
                $table->dropColumn('is_temp_id');
            }
            if (Schema::hasColumn('emergencies', 'temp_id')) {
                $table->dropColumn('temp_id');
            }
        });
    }
};
