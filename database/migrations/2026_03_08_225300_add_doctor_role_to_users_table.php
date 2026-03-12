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
        Schema::table('users', function (Blueprint $table) {
            // Cambiar el enum para incluir 'doctor'
            $table->enum('role', ['admin', 'reception', 'dirmedico', 'emergencia', 'caja', 'gerente', 'doctor'])->default('reception')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Revertir al enum original sin 'doctor'
            $table->enum('role', ['admin', 'reception', 'dirmedico', 'emergencia', 'caja', 'gerente'])->default('reception')->change();
        });
    }
};
