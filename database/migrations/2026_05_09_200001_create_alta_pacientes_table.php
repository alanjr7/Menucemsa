<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alta_pacientes', function (Blueprint $table) {
            $table->id();
            $table->integer('paciente_ci');
            $table->unsignedBigInteger('dado_de_alta_por'); // user_id
            $table->string('motivo_alta')->default('alta_medica'); // alta_medica, voluntaria, fallecimiento, traslado
            $table->text('observaciones')->nullable();
            $table->timestamp('fecha_alta');
            $table->timestamps();

            $table->foreign('paciente_ci')->references('ci')->on('pacientes')->onDelete('cascade');
            $table->foreign('dado_de_alta_por')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alta_pacientes');
    }
};
