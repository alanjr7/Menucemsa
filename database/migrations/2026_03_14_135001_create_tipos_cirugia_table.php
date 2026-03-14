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
        Schema::create('tipos_cirugia', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 50); // menor, mediana, mayor, ambulatoria
            $table->text('descripcion')->nullable();
            $table->integer('duracion_minutos'); // duración estimada en minutos
            $table->decimal('costo_base', 10, 2)->default(0);
            $table->decimal('costo_minuto_extra', 10, 2)->default(0);
            $table->boolean('activo')->default(true);
            $table->timestamps();
            
            $table->unique('nombre');
        });
        
        // Insertar tipos de cirugía por defecto
        DB::table('tipos_cirugia')->insert([
            [
                'nombre' => 'menor',
                'descripcion' => 'Cirugía menor - 60 minutos',
                'duracion_minutos' => 60,
                'costo_base' => 500.00,
                'costo_minuto_extra' => 10.00,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'mediana',
                'descripcion' => 'Cirugía mediana - 90 minutos',
                'duracion_minutos' => 90,
                'costo_base' => 800.00,
                'costo_minuto_extra' => 15.00,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'mayor',
                'descripcion' => 'Cirugía mayor - 80 minutos',
                'duracion_minutos' => 80,
                'costo_base' => 1200.00,
                'costo_minuto_extra' => 20.00,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'ambulatoria',
                'descripcion' => 'Cirugía ambulatoria - menos de 60 minutos',
                'duracion_minutos' => 45,
                'costo_base' => 300.00,
                'costo_minuto_extra' => 8.00,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tipos_cirugia');
    }
};
