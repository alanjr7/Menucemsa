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
        Schema::create('emergencies', function (Blueprint $table) {
            $table->id();
            $table->integer('patient_id')->nullable();
            $table->boolean('is_temp_id')->default(false);
            $table->string('temp_id')->nullable();
            $table->foreignId('user_id')->constrained('users');
            $table->string('code')->unique();
            $table->enum('status', ['recibido', 'en_evaluacion', 'estabilizado', 'uti', 'cirugia', 'alta', 'fallecido'])->default('recibido');
            $table->enum('tipo_ingreso', ['soat', 'parto', 'general'])->nullable();
            $table->enum('destino_inicial', ['cirugia', 'camilla', 'uti', 'parto', 'observacion', 'hospitalizacion', 'alta'])->nullable();
            $table->string('ubicacion_actual', 100)->default('emergencia');
            $table->text('symptoms');
            $table->text('initial_assessment')->nullable();
            $table->text('vital_signs')->nullable();
            $table->text('treatment')->nullable();
            $table->text('observations')->nullable();
            $table->enum('destination', ['observacion', 'uti', 'cirugia', 'consulta_externa', 'alta'])->nullable();
            $table->unsignedBigInteger('cirugia_id')->nullable();
            $table->string('hospitalizacion_id', 30)->nullable();
            $table->string('nro_uti', 30)->nullable();
            $table->decimal('cost', 10, 2)->default(0);
            $table->boolean('paid')->default(false);
            $table->decimal('deuda', 10, 2)->default(0);
            $table->decimal('total_pagado', 10, 2)->default(0);
            $table->json('detalle_costos')->nullable();
            $table->json('flujo_historial')->nullable();
            $table->boolean('es_parto')->default(false);
            $table->string('estado_parto', 60)->nullable();
            $table->timestamp('admission_date')->nullable();
            $table->timestamp('discharge_date')->nullable();
            $table->timestamps();
            
            $table->foreign('patient_id')->references('ci')->on('pacientes')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('emergencies');
    }
};
