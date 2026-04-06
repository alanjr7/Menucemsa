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
        Schema::create('procesos_clinicos', function (Blueprint $table) {
            $table->id();
            $table->string('hospitalizacion_id', 30);
            $table->date('fecha')->nullable();
            $table->string('estado', 80);
            $table->text('descripcion');
            $table->foreign('hospitalizacion_id')->references('id')->on('hospitalizaciones')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('procesos_clinicos');
    }
};
