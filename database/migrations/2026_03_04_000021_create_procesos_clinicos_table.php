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
            $table->string('id', 15);
            $table->string('id_hospitalizacion', 15);
            $table->date('fecha')->nullable();
            $table->string('estado', 80);
            $table->string('descripcion', 80);
            $table->primary(['id', 'id_hospitalizacion']);
            $table->foreign('id_hospitalizacion')->references('id')->on('hospitalizaciones')->onDelete('cascade')->onUpdate('cascade');
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
