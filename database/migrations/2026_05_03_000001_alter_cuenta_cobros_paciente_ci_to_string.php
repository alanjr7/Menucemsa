<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cuenta_cobros', function (Blueprint $table) {
            $table->string('paciente_ci', 50)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('cuenta_cobros', function (Blueprint $table) {
            $table->integer('paciente_ci')->unsigned()->nullable()->change();
        });
    }
};
