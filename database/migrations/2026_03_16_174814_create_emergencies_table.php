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
            $table->integer('ci_paciente');
            $table->foreign('ci_paciente')->references('ci')->on('pacientes')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('user_id')->constrained('users')->comment('Personal de emergencias que atiende');
            $table->string('code')->unique()->comment('Código único de emergencia');
            $table->enum('status', ['recibido', 'en_evaluacion', 'estabilizado', 'uti', 'cirugia', 'alta', 'fallecido'])->default('recibido');
            $table->text('symptoms')->comment('Síntomas iniciales');
            $table->text('initial_assessment')->nullable()->comment('Valoración inicial');
            $table->text('vital_signs')->nullable()->comment('Signos vitales');
            $table->text('treatment')->nullable()->comment('Tratamiento aplicado');
            $table->text('observations')->nullable()->comment('Observaciones médicas');
            $table->enum('destination', ['observacion', 'uti', 'cirugia', 'consulta_externa', 'alta'])->nullable();
            $table->decimal('cost', 10, 2)->default(0)->comment('Costo de la emergencia');
            $table->boolean('paid')->default(false)->comment('Pagado en caja');
            $table->timestamp('admission_date')->nullable();
            $table->timestamp('discharge_date')->nullable();
            $table->timestamps();
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
