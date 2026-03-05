<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BasicCajaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Insertar especialidad
        DB::table('especialidades')->insert([
            'codigo' => 'GENERAL',
            'nombre' => 'Medicina General',
            'descripcion' => 'Especialidad de medicina general',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Insertar seguros básicos
        DB::table('seguros')->insert([
            [
                'codigo' => 1,
                'nombre_empresa' => 'SIS',
                'tipo' => 'CONSULTA',
                'cobertura' => 'Consulta Externa',
                'telefono' => null,
                'formulario' => 'ESTANDAR',
                'estado' => 'ACTIVO',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'codigo' => 2,
                'nombre_empresa' => 'ESSALUD',
                'tipo' => 'CONSULTA',
                'cobertura' => 'Consulta Externa',
                'telefono' => null,
                'formulario' => 'ESTANDAR',
                'estado' => 'ACTIVO',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);

        // Insertar triage y registro básicos
        DB::table('triages')->insert([
            'id' => 'TRIAGE-CONSULTA',
            'color' => 'green',
            'descripcion' => 'Consulta Externa - No Urgente',
            'prioridad' => 'baja',
            'id_usuario' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('registros')->insert([
            'codigo' => 'REG-2026-000001',
            'fecha' => now()->toDateString(),
            'hora' => now()->toTimeString(),
            'motivo' => 'Registro de Consulta Externa',
            'id_usuario' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Insertar pacientes
        DB::table('pacientes')->insert([
            [
                'ci' => '12345678',
                'nombre' => 'Juan Pérez García',
                'sexo' => 'Masculino',
                'direccion' => 'Av. Principal 123',
                'telefono' => 987654321,
                'correo' => 'juan.perez@email.com',
                'codigo_seguro' => 1,
                'id_triage' => 'TRIAGE-CONSULTA',
                'codigo_registro' => 'REG-2026-000001',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'ci' => '87654321',
                'nombre' => 'María López Rodríguez',
                'sexo' => 'Femenino',
                'direccion' => 'Calle Secundaria 456',
                'telefono' => 912345678,
                'correo' => 'maria.lopez@email.com',
                'codigo_seguro' => 2,
                'id_triage' => 'TRIAGE-CONSULTA',
                'codigo_registro' => 'REG-2026-000002',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);

        $this->commandInfo('Datos básicos creados exitosamente');
    }
}
