<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // For SQLite, we need to recreate the table without the foreign key constraint
        // since SQLite doesn't support dropping foreign keys
        
        // Get existing data
        $existingData = DB::table('emergencies')->get();
        
        // Drop the table
        Schema::dropIfExists('emergencies');
        
        // Create new table without foreign key constraint
        Schema::create('emergencies', function (Blueprint $table) {
            $table->id();
            $table->string('patient_id');
            $table->boolean('is_temp_id')->default(false);
            $table->string('temp_id')->nullable();
            $table->foreignId('user_id')->constrained('users');
            $table->string('code')->unique();
            $table->enum('status', ['recibido', 'en_evaluacion', 'estabilizado', 'uti', 'cirugia', 'alta', 'fallecido'])->default('recibido');
            $table->enum('tipo_ingreso', ['soat', 'parto', 'general'])->nullable();
            $table->enum('destino_inicial', ['cirugia', 'camilla', 'uti', 'parto', 'observacion', 'hospitalizacion', 'alta'])->nullable();
            $table->string('ubicacion_actual')->default('emergencia');
            $table->text('symptoms');
            $table->text('initial_assessment')->nullable();
            $table->text('vital_signs')->nullable();
            $table->text('treatment')->nullable();
            $table->text('observations')->nullable();
            $table->enum('destination', ['observacion', 'uti', 'cirugia', 'consulta_externa', 'alta'])->nullable();
            $table->string('nro_cirugia')->nullable();
            $table->string('nro_hospitalizacion')->nullable();
            $table->string('nro_uti')->nullable();
            $table->decimal('cost', 10, 2)->default(0);
            $table->boolean('paid')->default(false);
            $table->decimal('deuda', 10, 2)->default(0);
            $table->decimal('total_pagado', 10, 2)->default(0);
            $table->json('detalle_costos')->nullable();
            $table->boolean('es_parto')->default(false);
            $table->string('estado_parto')->nullable();
            $table->json('flujo_historial')->nullable();
            $table->timestamp('admission_date')->nullable();
            $table->timestamp('discharge_date')->nullable();
            $table->timestamps();
        });
        
        // Restore data if any
        foreach ($existingData as $row) {
            $data = (array) $row;
            DB::table('emergencies')->insert($data);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Cannot easily reverse this
    }
};
