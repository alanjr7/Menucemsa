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
        Schema::create('estado_cuentas', function (Blueprint $table) {
            $table->integer('id');
            $table->string('id_hospitalizacion', 15);
            $table->date('fecha_apertura')->nullable();
            $table->date('fecha_cierre')->nullable();
            $table->float('total')->nullable();
            $table->string('estado', 15);
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
        Schema::dropIfExists('estado_cuentas');
    }
};
