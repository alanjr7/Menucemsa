<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Catálogo de precios de "alquiler de quirófano" para cirugías externas.
 *
 * Separado a propósito de `tipos_cirugia` (tarifario clínico interno): el precio
 * externo es un producto comercial distinto (el cirujano externo paga por el uso
 * del quirófano) y sus montos no deben mezclarse con el cobro interno. El admin
 * lo edita desde el panel de "Precios Externos".
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tipos_cirugia_externa', function (Blueprint $table) {
            $table->id();
            $table->string('clave', 30)->unique();      // mayor|mediana|menor|parto|sala
            $table->string('nombre', 120);
            $table->text('descripcion')->nullable();
            $table->decimal('precio', 10, 2)->default(0);
            $table->unsignedInteger('duracion_minutos')->default(60);
            $table->json('incluye')->nullable();
            $table->json('no_incluye')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        // Semilla inicial: los 5 tipos del programa CEMSA (tarifa en Bs),
        // con nombres/taglines/incluye idénticos al diseño original.
        $ahora = now();
        DB::table('tipos_cirugia_externa')->insert([
            [
                'clave' => 'mayor', 'nombre' => 'Cirugía mayor',
                'descripcion' => 'Equipo completo + 1 día de internación',
                'precio' => 4600, 'duracion_minutos' => 150,
                'incluye' => json_encode(['Cirujano', '2.º cirujano', 'Instrumentista', 'Circulante', 'Anestesiólogo', '1 día de internación', 'Desayuno']),
                'no_incluye' => json_encode(['Medicamentos']),
                'activo' => true, 'created_at' => $ahora, 'updated_at' => $ahora,
            ],
            [
                'clave' => 'mediana', 'nombre' => 'Cirugía mediana',
                'descripcion' => 'Equipo completo + 1 día de internación',
                'precio' => 3550, 'duracion_minutos' => 90,
                'incluye' => json_encode(['Cirujano', '2.º cirujano', 'Instrumentista', 'Circulante', 'Anestesiólogo', '1 día de internación', 'Desayuno']),
                'no_incluye' => json_encode(['Medicamentos']),
                'activo' => true, 'created_at' => $ahora, 'updated_at' => $ahora,
            ],
            [
                'clave' => 'menor', 'nombre' => 'Cirugía menor',
                'descripcion' => 'Ambulatoria · 2 h de observación',
                'precio' => 2000, 'duracion_minutos' => 60,
                'incluye' => json_encode(['Cirujano', 'Circulante', '2 h de observación']),
                'no_incluye' => json_encode(['Medicamentos']),
                'activo' => true, 'created_at' => $ahora, 'updated_at' => $ahora,
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('tipos_cirugia_externa');
    }
};
