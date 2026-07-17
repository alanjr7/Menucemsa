<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('camilla_usos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('camilla_id')->constrained('camillas')->cascadeOnDelete();
            $table->unsignedBigInteger('paciente_id')->index();
            $table->foreign('paciente_id')->references('id')->on('pacientes')->restrictOnDelete();
            $table->dateTime('fecha_inicio');
            $table->dateTime('fecha_fin');
            $table->decimal('costo_calculado', 10, 2)->default(0);
            $table->foreignId('cuenta_cobro_detalle_id')->nullable()->constrained('cuenta_cobro_detalles')->nullOnDelete();
            $table->foreignId('registrado_por')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('camilla_usos');
    }
};
