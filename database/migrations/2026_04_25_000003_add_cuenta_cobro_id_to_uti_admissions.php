<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('uti_admissions', function (Blueprint $table) {
            $table->string('cuenta_cobro_id')->nullable()->after('seguro_id')
                ->comment('ID de la cuenta maestra del paciente');
        });
    }

    public function down(): void
    {
        Schema::table('uti_admissions', function (Blueprint $table) {
            $table->dropColumn('cuenta_cobro_id');
        });
    }
};
