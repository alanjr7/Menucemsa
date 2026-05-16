<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('almacen_entregas_paciente', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('paciente_id');
            $table->foreign('paciente_id')->references('id')->on('pacientes')->restrictOnDelete();
            $table->foreignId('entregado_por')->constrained('users')->restrictOnDelete();
            $table->enum('origen', ['emergencia', 'internacion', 'uti', 'cirugia', 'almacen', 'neonato'])->default('almacen');
            $table->unsignedBigInteger('referencia_id')->nullable();
            $table->foreignId('catalogo_id')->nullable()->constrained('almacen_catalogo')->nullOnDelete();
            $table->integer('cantidad')->default(1);
            $table->text('observaciones')->nullable();
            $table->timestamp('fecha_entrega')->useCurrent();
            $table->timestamps();

            $table->index(['paciente_id', 'fecha_entrega']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('almacen_entregas_paciente');
    }
};
