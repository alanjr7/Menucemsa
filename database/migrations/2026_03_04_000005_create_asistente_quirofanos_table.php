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
            $table->string('id', 20)->primary();
            $table->unsignedBigInteger('quirofano_id');
            $table->string('descripcion', 120);
            $table->foreign('quirofano_id')->references('id')->on('quirofanos')->onDelete('cascade');
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
