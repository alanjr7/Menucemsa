<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServicioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $servicios = [
            [
                'codigo' => 'CONS-EXT-001',
                'nombre' => 'Consulta Externa General',
                'descripcion' => 'Consulta médica general con especialista',
                'precio' => 50.00,
                'tipo' => 'CONSULTA_EXTERNA',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'codigo' => 'EMER-001',
                'nombre' => 'Emergencia General',
                'descripcion' => 'Atención de emergencia no traumática',
                'precio' => 150.00,
                'tipo' => 'EMERGENCIA',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'codigo' => 'LAB-001',
                'nombre' => 'Análisis de Laboratorio Básico',
                'descripcion' => 'Hemograma completo, perfil bioquímico',
                'precio' => 80.00,
                'tipo' => 'LABORATORIO',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'codigo' => 'RAYX-001',
                'nombre' => 'Radiografía Simple',
                'descripcion' => 'Radiografía de una región anatómica',
                'precio' => 120.00,
                'tipo' => 'RAYOS_X',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'codigo' => 'ECO-001',
                'nombre' => 'Ecografía Abdominal',
                'descripcion' => 'Ecografía completa de abdomen',
                'precio' => 100.00,
                'tipo' => 'ECOGRAFIA',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'codigo' => 'CONS-EXT-002',
                'nombre' => 'Consulta Externa Especializada',
                'descripcion' => 'Consulta con médico especialista',
                'precio' => 80.00,
                'tipo' => 'CONSULTA_EXTERNA',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('servicios')->insert($servicios);
    }
}
