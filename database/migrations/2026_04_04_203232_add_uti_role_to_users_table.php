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
            // SQLite no soporta modificar enum directamente, necesitamos recrear la columna
            $table->dropColumn('role');
        });
        
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'reception', 'dirmedico', 'emergencia', 'caja', 'gerente', 'doctor', 'farmacia', 'uti'])->default('reception')->after('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
        
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'reception', 'dirmedico', 'emergencia', 'caja', 'gerente', 'doctor', 'farmacia'])->default('reception')->after('email');
        });
    }
};
