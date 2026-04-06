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
        Schema::create('cirugias', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 30)->unique();
            $table->date('fecha');
            $table->time('hora');
            $table->string('tipo', 80);
            $table->text('descripcion');
            $table->unsignedBigInteger('emergencia_id')->nullable();
            $table->unsignedBigInteger('quirofano_id');
            $table->foreign('quirofano_id')->references('id')->on('quirofanos')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cirugias');
    }
};
