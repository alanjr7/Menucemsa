<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('camilla_usos', function (Blueprint $table) {
            $table->string('paciente_ci', 30)->change();
        });
    }

    public function down(): void
    {
        Schema::table('camilla_usos', function (Blueprint $table) {
            $table->integer('paciente_ci')->change();
        });
    }
};
