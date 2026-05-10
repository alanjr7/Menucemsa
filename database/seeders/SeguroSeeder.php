<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SeguroSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $seguros = [
            // 1. SEGUROS OBLIGATORIOS (POR LEY)
            [
                'nombre_empresa' => 'UNIVida - SOAT',
                'tipo' => 'Obligatorio de Accidentes de Tránsito',
                'telefono' => '800108484',
                'formulario' => 'FORM-SOAT-01',
                'estado' => 'inactivo',
                'tipo_cobertura' => 'tope_monto',
                'cobertura_porcentaje' => null,
                'tope_monto' => 24000.00,
                'copago_porcentaje' => 0.00,
            ],
            [
                'nombre_empresa' => 'UNIVida - SOATC',
                'tipo' => 'Obligatorio de Accidentes del Trabajador Construcción',
                'telefono' => '800108484',
                'formulario' => 'FORM-SOATC-02',
                'estado' => 'inactivo',
                'tipo_cobertura' => 'tope_monto',
                'cobertura_porcentaje' => null,
                'tope_monto' => 7000.00,
                'copago_porcentaje' => 0.00,
            ],
            [
                'nombre_empresa' => 'Instituto del Seguro Agrario (INSA)',
                'tipo' => 'Seguro Agrario Universal Misk’y T’ika',
                'telefono' => '2147110',
                'formulario' => 'FORM-AGRO-01',
                'estado' => 'inactivo',
                'tipo_cobertura' => 'tope_monto',
                'cobertura_porcentaje' => null,
                'tope_monto' => 1000.00, // Por hectárea
                'copago_porcentaje' => 0.00,
            ],

            // 2. SEGUROS DE PERSONAS (VOLUNTARIOS Y PRIVADOS)
            [
                'nombre_empresa' => 'BISA Seguros',
                'tipo' => 'Salud Privada - Red Max / Infinity Green',
                'telefono' => '800102472',
                'formulario' => 'FORM-SALUD-BISA',
                'estado' => 'inactivo',
                'tipo_cobertura' => 'porcentaje',
                'cobertura_porcentaje' => 80.00,
                'tope_monto' => null,
                'copago_porcentaje' => 20.00,
            ],
            [
                'nombre_empresa' => 'La Boliviana Ciacruz',
                'tipo' => 'Seguro de Salud - Plan Advance',
                'telefono' => '800103010',
                'formulario' => 'FORM-LBC-SALUD',
                'estado' => 'inactivo',
                'tipo_cobertura' => 'porcentaje',
                'cobertura_porcentaje' => 90.00,
                'tope_monto' => null,
                'copago_porcentaje' => 10.00,
            ],
            [
                'nombre_empresa' => 'Alianza Seguros',
                'tipo' => 'Seguro de Vida y Salud Combinado',
                'telefono' => '800103003',
                'formulario' => 'FORM-AL-05',
                'estado' => 'inactivo',
                'tipo_cobertura' => 'porcentaje',
                'cobertura_porcentaje' => 70.00,
                'tope_monto' => null,
                'copago_porcentaje' => 30.00,
            ],
            [
                'nombre_empresa' => 'Banco FIE (Seguro Vida XS)',
                'tipo' => 'Vida y Accidentes Personales',
                'telefono' => '800101112',
                'formulario' => 'FORM-FIE-LIFE',
                'estado' => 'inactivo',
                'tipo_cobertura' => 'solo_consulta',
                'cobertura_porcentaje' => 100.00,
                'tope_monto' => null,
                'copago_porcentaje' => 0.00,
            ],

            // 3. SEGUROS GENERALES Y PATRIMONIALES
            [
                'nombre_empresa' => 'Fortaleza Seguros',
                'tipo' => 'Seguro Vehicular Voluntario',
                'telefono' => '800101010',
                'formulario' => 'FORM-VEH-FT',
                'estado' => 'inactivo',
                'tipo_cobertura' => 'porcentaje',
                'cobertura_porcentaje' => 100.00,
                'tope_monto' => null,
                'copago_porcentaje' => 0.00,
            ],
            [
                'nombre_empresa' => 'Crediseguro',
                'tipo' => 'Seguro de Desgravamen (Hipotecario)',
                'telefono' => '800102733',
                'formulario' => 'FORM-CRED-01',
                'estado' => 'inactivo',
                'tipo_cobertura' => 'tope_monto',
                'cobertura_porcentaje' => null,
                'tope_monto' => 500000.00,
                'copago_porcentaje' => 0.00,
            ],
            [
                'nombre_empresa' => 'Mercantil Santa Cruz Seguros',
                'tipo' => 'Seguro de Hogar e Incendio',
                'telefono' => '800105454',
                'formulario' => 'FORM-MSC-HOGAR',
                'estado' => 'inactivo',
                'tipo_cobertura' => 'porcentaje',
                'cobertura_porcentaje' => 100.00,
                'tope_monto' => null,
                'copago_porcentaje' => 0.00,
            ],
        ];

        foreach ($seguros as $seguro) {
            DB::table('seguros')->insert(array_merge($seguro, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }
}
