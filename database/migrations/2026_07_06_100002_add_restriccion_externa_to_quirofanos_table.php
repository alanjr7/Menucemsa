<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Restricción opcional de tipos permitidos por quirófano para reservas EXTERNAS.
 * NULL = sin restricción (acepta todos los tipos). Ej.: ["menor","parto"] limita
 * ese quirófano a cirugías menores y partos (regla "Q3" del programa original).
 * Migración aditiva y no destructiva (no toca datos existentes).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quirofanos', function (Blueprint $table) {
            $table->json('restriccion_externa')->nullable()->after('estado');
        });
    }

    public function down(): void
    {
        Schema::table('quirofanos', function (Blueprint $table) {
            $table->dropColumn('restriccion_externa');
        });
    }
};
