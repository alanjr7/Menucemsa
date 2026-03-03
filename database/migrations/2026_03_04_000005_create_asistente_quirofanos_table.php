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
            $table->string('id', 15);
            $table->integer('nro_quirofano');
            $table->string('descripcion', 80);
            $table->primary(['id', 'nro_quirofano']);
            $table->unique('id');
            $table->foreign('nro_quirofano')->references('nro')->on('quirofanos')->onDelete('cascade')->onUpdate('cascade');
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
