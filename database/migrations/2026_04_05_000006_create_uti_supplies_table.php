<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('uti_supplies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('uti_admission_id');
            $table->string('codigo_insumo', 20);
            $table->unsignedBigInteger('used_by');
            $table->date('fecha');
            $table->time('hora');
            $table->decimal('cantidad', 8, 2);
            $table->text('observaciones')->nullable();
            $table->boolean('cargo_generado')->default(false);
            $table->unsignedBigInteger('cuenta_cobro_detalle_id')->nullable();
            $table->timestamps();

            $table->foreign('uti_admission_id')->references('id')->on('uti_admissions')->onDelete('cascade');
            $table->foreign('codigo_insumo')->references('codigo')->on('insumos');
            $table->foreign('used_by')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('uti_supplies');
    }
};
