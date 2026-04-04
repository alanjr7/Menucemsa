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
            $table->unsignedBigInteger('medico_id');
            $table->string('nro_receta', 20)->unique();
            $table->date('fecha');
            $table->text('indicaciones_generales');
            $table->enum('estado', ['activa', 'suspendida', 'completada'])->default('activa');
            $table->timestamps();

            $table->foreign('uti_admission_id')->references('id')->on('uti_admissions')->onDelete('cascade');
            $table->foreign('medico_id')->references('id')->on('medicos');
        });

        Schema::create('uti_recipe_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('uti_recipe_id');
            $table->unsignedBigInteger('medicamento_id');
            $table->decimal('dosis', 8, 2);
            $table->string('unidad', 20);
            $table->string('frecuencia', 50);
            $table->string('via_administracion', 50);
            $table->text('indicaciones')->nullable();
            $table->timestamps();

            $table->foreign('uti_recipe_id')->references('id')->on('uti_recipes')->onDelete('cascade');
            $table->foreign('medicamento_id')->references('id')->on('medicamentos');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('uti_recipe_details');
        Schema::dropIfExists('uti_recipes');
    }
};
