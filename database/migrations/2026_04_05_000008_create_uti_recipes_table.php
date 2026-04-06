<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('uti_recipes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('uti_admission_id');
            $table->integer('medico_ci');
            $table->string('nro_receta', 20)->unique();
            $table->date('fecha');
            $table->text('indicaciones_generales');
            $table->enum('estado', ['activa', 'suspendida', 'completada'])->default('activa');
            $table->timestamps();

            $table->foreign('uti_admission_id')->references('id')->on('uti_admissions')->onDelete('cascade');
            $table->foreign('medico_ci')->references('ci')->on('medicos');
        });

        Schema::create('uti_recipe_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('uti_recipe_id');
            $table->string('codigo_medicamento', 20);
            $table->decimal('dosis', 8, 2);
            $table->string('unidad', 20);
            $table->string('frecuencia', 50);
            $table->string('via_administracion', 50);
            $table->text('indicaciones')->nullable();
            $table->timestamps();

            $table->foreign('uti_recipe_id')->references('id')->on('uti_recipes')->onDelete('cascade');
            $table->foreign('codigo_medicamento')->references('codigo')->on('medicamentos');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('uti_recipe_details');
        Schema::dropIfExists('uti_recipes');
    }
};
