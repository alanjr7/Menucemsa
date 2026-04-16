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
        Schema::create('hospitalizaciones', function (Blueprint $table) {
            $table->string('id', 30)->primary();
            $table->integer('ci_paciente')->nullable();
            $table->integer('ci_medico')->nullable();
            $table->string('habitacion_id', 20)->nullable();
            $table->unsignedBigInteger('cama_id')->nullable();
            $table->decimal('precio_cama_dia', 10, 2)->default(0);
            $table->decimal('total_estancia', 10, 2)->default(0);
            $table->unsignedBigInteger('cuenta_cobro_detalle_id')->nullable();
            $table->dateTime('fecha_ingreso');
            $table->dateTime('fecha_alta')->nullable();
            $table->text('diagnostico')->nullable();
            $table->text('tratamiento')->nullable();
            $table->enum('estado', ['activo', 'alta', 'trasladado'])->default('activo');
            $table->string('motivo', 255)->nullable();
            $table->string('nro_emergencia', 30)->nullable();
            $table->foreign('ci_paciente')->references('ci')->on('pacientes')->onDelete('set null');
            $table->foreign('ci_medico')->references('ci')->on('medicos')->onDelete('set null');
            $table->foreign('habitacion_id')->references('id')->on('habitaciones')->onDelete('set null');
            $table->foreign('cama_id')->references('id')->on('camas')->onDelete('set null');
            $table->foreign('cuenta_cobro_detalle_id')->references('id')->on('cuenta_cobro_detalles')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hospitalizaciones');
    }
};
