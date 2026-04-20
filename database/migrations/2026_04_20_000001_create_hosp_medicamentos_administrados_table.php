<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hosp_medicamentos_administrados', function (Blueprint $table) {
            $table->id();
            $table->string('hospitalizacion_id', 30);
            $table->unsignedBigInteger('medicamento_id');
            $table->unsignedBigInteger('administered_by');
            $table->date('fecha');
            $table->time('hora');
            $table->decimal('cantidad', 8, 2);
            $table->string('unidad', 20);
            $table->string('via_administracion', 50)->nullable();
            $table->text('observaciones')->nullable();
            $table->boolean('cargo_generado')->default(false);
            $table->unsignedBigInteger('cuenta_cobro_detalle_id')->nullable();
            $table->timestamps();

            $table->foreign('hospitalizacion_id')->references('id')->on('hospitalizaciones')->onDelete('cascade');
            $table->foreign('medicamento_id')->references('id')->on('almacen_medicamentos');
            $table->foreign('administered_by')->references('id')->on('users');
            $table->foreign('cuenta_cobro_detalle_id')->references('id')->on('cuenta_cobro_detalles')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hosp_medicamentos_administrados');
    }
};
