<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Cierre de período contable: una vez cerrado un mes (declarado al SIN), no se
        // pueden registrar ni anular egresos con fecha en ese período. Inmutabilidad fiscal.
        Schema::create('cierres_contables', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('anio');
            $table->unsignedTinyInteger('mes');
            $table->timestamp('cerrado_at');
            $table->foreignId('cerrado_por')->constrained('users');
            $table->string('observaciones')->nullable();
            $table->timestamps();

            $table->unique(['anio', 'mes']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cierres_contables');
    }
};
