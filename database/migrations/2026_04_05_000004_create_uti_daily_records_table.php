<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('uti_daily_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('uti_admission_id');
            $table->date('fecha');
            $table->integer('medico_id');
            $table->text('evolucion_medica')->nullable();
            $table->text('indicaciones')->nullable();
            $table->text('plan_tratamiento')->nullable();
            $table->boolean('ronda_completada')->default(false);
            $table->timestamp('hora_ronda')->nullable();
            $table->boolean('dia_validado')->default(false);
            $table->timestamp('hora_validacion')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->foreign('uti_admission_id')->references('id')->on('uti_admissions')->onDelete('cascade');
            $table->foreign('medico_id')->references('ci')->on('medicos');
            $table->unique(['uti_admission_id', 'fecha']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('uti_daily_records');
    }
};
