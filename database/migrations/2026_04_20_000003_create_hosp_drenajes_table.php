<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hosp_drenajes', function (Blueprint $table) {
            $table->id();
            $table->string('hospitalizacion_id', 30);
            $table->unsignedBigInteger('registered_by');
            $table->date('fecha');
            $table->time('hora')->nullable();
            $table->string('tipo_drenaje', 50)->nullable();
            $table->boolean('realizado')->default(false);
            $table->text('observaciones')->nullable();
            $table->decimal('precio', 10, 2)->default(0);
            $table->boolean('cargo_generado')->default(false);
            $table->unsignedBigInteger('cuenta_cobro_detalle_id')->nullable();
            $table->timestamps();

            $table->foreign('hospitalizacion_id')->references('id')->on('hospitalizaciones')->onDelete('cascade');
            $table->foreign('registered_by')->references('id')->on('users');
            $table->foreign('cuenta_cobro_detalle_id')->references('id')->on('cuenta_cobro_detalles')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hosp_drenajes');
    }
};
