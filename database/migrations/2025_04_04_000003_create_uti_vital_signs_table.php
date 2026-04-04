<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('uti_vital_signs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('uti_admission_id');
            $table->unsignedBigInteger('registered_by');
            $table->enum('turno', ['manana', 'tarde', 'noche']);
            $table->date('fecha');
            $table->time('hora');
            $table->decimal('presion_arterial_sistolica', 5, 1)->nullable();
            $table->decimal('presion_arterial_diastolica', 5, 1)->nullable();
            $table->decimal('frecuencia_cardiaca', 5, 1)->nullable();
            $table->decimal('frecuencia_respiratoria', 5, 1)->nullable();
            $table->decimal('temperatura', 4, 1)->nullable();
            $table->decimal('saturacion_o2', 5, 2)->nullable();
            $table->decimal('glicemia', 5, 1)->nullable();
            $table->decimal('peso', 6, 2)->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->foreign('uti_admission_id')->references('id')->on('uti_admissions')->onDelete('cascade');
            $table->foreign('registered_by')->references('id')->on('users');
            $table->index(['uti_admission_id', 'fecha', 'turno']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('uti_vital_signs');
    }
};
