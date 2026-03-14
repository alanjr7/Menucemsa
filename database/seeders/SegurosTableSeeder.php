<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SegurosTableSeeder extends Seeder
{
    public function run(): void
    {
        $seguros = [
            [
                'codigo' => 1001,
                'nombre_empresa' => 'Seguros Médicos del Estado',
                'tipo' => 'Público',
                'cobertura' => 'Completa',
                'telefono' => 800123456,
                'formulario' => 'Formulario SMP-001',
                'estado' => 'Activo'
            ],
            [
                'codigo' => 1002,
                'nombre_empresa' => 'SaludSeguro Privada',
                'tipo' => 'Privado',
                'cobertura' => 'Premium',
                'telefono' => 800987654,
                'formulario' => 'Formulario SSP-002',
                'estado' => 'Activo'
            ],
            [
                'codigo' => 1003,
                'nombre_empresa' => 'Cruz Blanca Seguros',
                'tipo' => 'Privado',
                'cobertura' => 'Básica',
                'telefono' => 800555123,
                'formulario' => 'Formulario CBS-003',
                'estado' => 'Activo'
            ],
            [
                'codigo' => 1004,
                'nombre_empresa' => 'Mapfre Salud',
                'tipo' => 'Privado',
                'cobertura' => 'Completa',
                'telefono' => 800777888,
                'formulario' => 'Formulario MS-004',
                'estado' => 'Activo'
            ],
            [
                'codigo' => 1005,
                'nombre_empresa' => 'Seguros La Vitalicia',
                'tipo' => 'Privado',
                'cobertura' => 'Premium',
                'telefono' => 800999000,
                'formulario' => 'Formulario SLV-005',
                'estado' => 'Activo'
            ],
            [
                'codigo' => 1006,
                'nombre_empresa' => 'Sanitas Seguros',
                'tipo' => 'Privado',
                'cobertura' => 'Básica',
                'telefono' => 800111222,
                'formulario' => 'Formulario SS-006',
                'estado' => 'Activo'
            ],
            [
                'codigo' => 1007,
                'nombre_empresa' => 'Seguros Bolívar',
                'tipo' => 'Privado',
                'cobertura' => 'Completa',
                'telefono' => 800333444,
                'formulario' => 'Formulario SB-007',
                'estado' => 'Activo'
            ],
            [
                'codigo' => 1008,
                'nombre_empresa' => 'SURA Seguros',
                'tipo' => 'Privado',
                'cobertura' => 'Premium',
                'telefono' => 800555666,
                'formulario' => 'Formulario SURA-008',
                'estado' => 'Activo'
            ],
            [
                'codigo' => 1009,
                'nombre_empresa' => 'Seguros Solidaria',
                'tipo' => 'Cooperativa',
                'cobertura' => 'Básica',
                'telefono' => 800777999,
                'formulario' => 'Formulario SSO-009',
                'estado' => 'Activo'
            ],
            [
                'codigo' => 1010,
                'nombre_empresa' => 'Previsión Médica',
                'tipo' => 'Privado',
                'cobertura' => 'Completa',
                'telefono' => 800888777,
                'formulario' => 'Formulario PM-010',
                'estado' => 'Activo'
            ],
            [
                'codigo' => 1011,
                'nombre_empresa' => 'Seguros Médicos Universales',
                'tipo' => 'Privado',
                'cobertura' => 'Premium',
                'telefono' => 800999888,
                'formulario' => 'Formulario SMU-011',
                'estado' => 'Activo'
            ],
            [
                'codigo' => 1012,
                'nombre_empresa' => 'Salud Total Seguros',
                'tipo' => 'Privado',
                'cobertura' => 'Básica',
                'telefono' => 800666555,
                'formulario' => 'Formulario STS-012',
                'estado' => 'Activo'
            ],
            [
                'codigo' => 1013,
                'nombre_empresa' => 'Protección Médica Familiar',
                'tipo' => 'Familiar',
                'cobertura' => 'Completa',
                'telefono' => 800444333,
                'formulario' => 'Formulario PMF-013',
                'estado' => 'Activo'
            ],
            [
                'codigo' => 1014,
                'nombre_empresa' => 'Seguros de Salud del Ejército',
                'tipo' => 'Militar',
                'cobertura' => 'Completa',
                'telefono' => 800222111,
                'formulario' => 'Formulario SDE-014',
                'estado' => 'Activo'
            ],
            [
                'codigo' => 1015,
                'nombre_empresa' => 'Caja Nacional de Salud',
                'tipo' => 'Público',
                'cobertura' => 'Básica',
                'telefono' => 800123789,
                'formulario' => 'Formulario CNS-015',
                'estado' => 'Activo'
            ],
        ];

        DB::table('seguros')->insert($seguros);
    }
}
