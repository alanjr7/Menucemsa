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
        Schema::create('camas', function (Blueprint $table) {
            $table->integer('nro');
            $table->string('id_habitacion', 15);
            $table->string('disponibilidad', 15);
            $table->string('tipo', 80);
            $table->primary(['nro', 'id_habitacion']);
            $table->foreign('id_habitacion')->references('id')->on('habitaciones')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('camas');
    }
};
