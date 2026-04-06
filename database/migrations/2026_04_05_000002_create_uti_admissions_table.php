<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('uti_admissions', function (Blueprint $table) {
            $table->id();
            $table->integer('patient_id');
            $table->unsignedBigInteger('bed_id')->nullable();
            $table->string('nro_ingreso', 20)->unique();
            $table->unsignedBigInteger('emergency_id')->nullable();
            $table->unsignedBigInteger('quirofano_id')->nullable();
            $table->string('hospitalization_id', 30)->nullable();
            $table->enum('estado_clinico', ['estable', 'critico', 'muy_critico'])->default('estable');
            $table->text('diagnostico_principal')->nullable();
            $table->text('diagnostico_secundario')->nullable();
            $table->enum('tipo_ingreso', ['emergencia', 'quirofano', 'derivacion_interna'])->default('emergencia');
            $table->enum('tipo_pago', ['particular', 'seguro'])->default('particular');
            $table->unsignedBigInteger('seguro_id')->nullable();
            $table->string('nro_autorizacion', 50)->nullable();
            $table->timestamp('fecha_ingreso');
            $table->timestamp('fecha_alta_clinica')->nullable();
            $table->timestamp('fecha_alta_administrativa')->nullable();
            $table->enum('estado', ['activo', 'alta_clinica', 'alta_administrativa', 'trasladado', 'fallecido'])->default('activo');
            $table->enum('destino_alta', ['hospitalizacion', 'domicilio', 'otro_hospital'])->nullable();
            $table->integer('medico_responsable_ci')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('patient_id')->references('ci')->on('pacientes');
            $table->foreign('bed_id')->references('id')->on('uti_beds');
            $table->foreign('seguro_id')->references('id')->on('seguros');
            $table->foreign('medico_responsable_ci')->references('ci')->on('medicos');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('uti_admissions');
    }
};
