<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('uti_medications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('uti_admission_id');
            $table->string('codigo_medicamento', 20);
            $table->unsignedBigInteger('administered_by');
            $table->date('fecha');
            $table->time('hora');
            $table->decimal('dosis', 8, 2);
            $table->string('unidad', 20);
            $table->string('via_administracion', 50);
            $table->text('observaciones')->nullable();
            $table->boolean('cargo_generado')->default(false);
            $table->unsignedBigInteger('cuenta_cobro_detalle_id')->nullable();
            $table->timestamps();

            $table->foreign('uti_admission_id')->references('id')->on('uti_admissions')->onDelete('cascade');
            $table->foreign('codigo_medicamento')->references('codigo')->on('medicamentos');
            $table->foreign('administered_by')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('uti_medications');
    }
};
