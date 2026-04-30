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
        Schema::create('pacientes', function (Blueprint $table) {
            $table->integer('ci')->primary();
            $table->string('nombre_completo', 255)->nullable();
            $table->string('nombre', 120)->nullable();
            $table->enum('sexo', ['M', 'F'])->nullable();
            $table->date('fecha_nacimiento')->nullable();
            $table->char('lugar_expedicion', 2)->nullable();
            $table->string('nacionalidad', 100)->nullable();
            $table->string('estado_civil', 50)->nullable();
            $table->text('direccion_residencia')->nullable();
            $table->string('direccion', 120)->nullable();
            $table->string('telefono', 20)->nullable();
            $table->string('correo', 100)->nullable();
            $table->string('profesion', 150)->nullable();
            $table->string('empresa_trabajo', 150)->nullable();
            $table->unsignedBigInteger('seguro_id')->nullable();
            $table->string('triage_id', 50)->nullable();
            $table->string('registro_codigo', 80)->nullable();
            $table->integer('id_garante_referencia')->nullable();
            $table->foreign('seguro_id')->references('id')->on('seguros')->onDelete('set null');
            $table->foreign('triage_id')->references('id')->on('triages')->onDelete('set null');
            $table->foreign('registro_codigo')->references('codigo')->on('registros')->onDelete('set null');
            $table->foreign('id_garante_referencia')->references('ci')->on('pacientes')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pacientes');
    }
};
