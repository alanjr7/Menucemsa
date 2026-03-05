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
        Schema::create('asistente_quirofanos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->string('especialidad', 100);
            $table->integer('telefono')->nullable();
            $table->string('estado', 50)->default('Activo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asistente_quirofanos');
    }
};
