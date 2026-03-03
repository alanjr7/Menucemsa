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
        Schema::create('partos', function (Blueprint $table) {
            $table->string('nro', 15)->primary();
            $table->string('tipo', 15);
            $table->string('observaciones', 80)->nullable();
            $table->string('id_hospitalizacion', 15);
            $table->string('nro_cirugia', 15);
            $table->foreign('id_hospitalizacion')->references('id')->on('hospitalizaciones')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('nro_cirugia')->references('nro')->on('cirugias')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partos');
    }
};
