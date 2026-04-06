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
        Schema::create('citas_quirurgicas', function (Blueprint $table) {
            $table->id();
            
            // Paciente y fecha/hora
            $table->integer('ci_paciente');
            $table->date('fecha');
            $table->time('hora_inicio_estimada');
            $table->time('hora_inicio_real')->nullable();
            $table->time('hora_fin_real')->nullable();
            
            // Equipo quirúrgico
            $table->integer('ci_cirujano');
            $table->integer('ci_instrumentista')->nullable();
            $table->integer('ci_anestesiologo')->nullable();
            
            // Tipo y duración de cirugía
            $table->enum('tipo_cirugia', ['menor', 'mediana', 'mayor', 'ambulatoria'])->default('menor');
            $table->enum('tipo_final', ['menor', 'mediana', 'mayor', 'ambulatoria'])->nullable();
            $table->text('descripcion_cirugia')->nullable();
            
            // Nombres de equipo quirúrgico (texto libre para casos externos)
            $table->string('nombre_instrumentista')->nullable();
            $table->string('nombre_anestesiologo')->nullable();
            
            // Quirófano
            $table->integer('nro_quirofano');
            
            // Estados y timestamps
            $table->enum('estado', ['programada', 'en_curso', 'finalizada', 'cancelada'])->default('programada');
            $table->timestamp('timestamp_inicio')->nullable();
            $table->timestamp('timestamp_fin')->nullable();
            
            // Costos y facturación
            $table->decimal('costo_base', 10, 2)->default(0);
            $table->decimal('costo_final', 10, 2)->nullable();
            $table->decimal('costo_minuto_extra', 10, 2)->default(0);
            
            // Observaciones
            $table->text('observaciones')->nullable();
            $table->text('motivo_cancelacion')->nullable();
            
            // Usuario que registra
            $table->unsignedBigInteger('id_usuario_registro');
            
            $table->timestamps();
            
            // Índices
            $table->index(['fecha', 'hora_inicio_estimada']);
            $table->index('ci_paciente');
            $table->index('nro_quirofano');
            $table->index('estado');
            $table->index('tipo_cirugia');
            
            // Claves foráneas (comentadas temporalmente)
            // $table->foreign('ci_paciente')->references('ci')->on('pacientes')->onDelete('cascade');
            // $table->foreign('ci_cirujano')->references('ci')->on('medicos')->onDelete('restrict');
            // $table->foreign('ci_instrumentista')->references('ci')->on('medicos')->onDelete('set null');
            // $table->foreign('ci_anestesiologo')->references('ci')->on('medicos')->onDelete('set null');
            // $table->foreign('nro_quirofano')->references('nro')->on('quirofanos')->onDelete('restrict');
            // $table->foreign('id_usuario_registro')->references('id')->on('users')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('citas_quirurgicas');
    }
};
