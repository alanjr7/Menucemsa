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
        Schema::create('pagos', function (Blueprint $table) {
            $table->integer('nro')->autoIncrement();
            $table->timestamp('fecha')->nullable();
            $table->decimal('monto', 10, 2);
            $table->string('concepto', 120);
            $table->enum('estado', ['pendiente', 'pagado', 'anulado'])->default('pendiente');
            $table->string('hospitalizacion_id', 30);
            $table->foreign('hospitalizacion_id')->references('id')->on('hospitalizaciones')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};
