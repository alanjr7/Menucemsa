<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TiposCirugiaExternaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ahora = now();

        DB::table('tipos_cirugia_externa')->upsert([
            [
                'clave' => 'mayor', 
                'nombre' => 'Cirugía mayor',
                'descripcion' => '',
                'precio' => 4600, 
                'duracion_minutos' => 150,
                'incluye' => json_encode(['Circulante + Instrumentista + 2 días de internación']),
                'no_incluye' => json_encode(['medicamentos', 'insumos de quirofano']),
                'activo' => true, 
                'created_at' => $ahora, 
                'updated_at' => $ahora,
            ],
            [
                'clave' => 'mediana', 
                'nombre' => 'Cirugía mediana',
                'descripcion' => '',
                'precio' => 3550, 
                'duracion_minutos' => 90,
                'incluye' => json_encode(['Circulante + Instrumentista + 1 días de internación']),
                'no_incluye' => json_encode(['medicamentos', 'insumos de quirofano']),
                'activo' => true, 
                'created_at' => $ahora, 
                'updated_at' => $ahora,
            ],
            [
                'clave' => 'menor', 
                'nombre' => 'Cirugía menor Ambulatoria',
                'descripcion' => '',
                'precio' => 2000, 
                'duracion_minutos' => 60,
                'incluye' => json_encode(['solo circulante, 2h de recuperacion']),
                'no_incluye' => json_encode(['medicamentos', 'insumos de quirofano']),
                'activo' => true, 
                'created_at' => $ahora, 
                'updated_at' => $ahora,
            ],
        ], 
        ['clave'], // Llave única para comparar
        [
            'nombre', 'descripcion', 'precio', 'duracion_minutos', 
            'incluye', 'no_incluye', 'activo', 'updated_at'
        ] // Campos a actualizar si ya existe la clave
        );
    }
}
