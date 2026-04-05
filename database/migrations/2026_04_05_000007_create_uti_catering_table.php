<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('uti_catering', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('uti_admission_id');
            $table->unsignedBigInteger('registered_by');
            $table->date('fecha');
            $table->enum('tipo_comida', ['desayuno', 'almuerzo', 'merienda', 'cena']);
            $table->enum('estado', ['dado', 'no_dado', 'no_aplica'])->default('no_dado');
            $table->timestamp('hora_registro')->nullable();
            $table->text('observaciones')->nullable();
            $table->boolean('cargo_generado')->default(false);
            $table->unsignedBigInteger('cuenta_cobro_detalle_id')->nullable();
            $table->timestamps();

            $table->foreign('uti_admission_id')->references('id')->on('uti_admissions')->onDelete('cascade');
            $table->foreign('registered_by')->references('id')->on('users');
            $table->unique(['uti_admission_id', 'fecha', 'tipo_comida']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('uti_catering');
    }
};
