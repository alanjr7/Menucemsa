<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CuentaCobro;
use App\Models\Paciente;
use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Support\Str;

class CuentaCobroSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('es_ES');
        $pacientes = Paciente::all();
        $users = User::pluck('id')->toArray(); // Para autorizaciones y caja

        if ($pacientes->isEmpty()) {
            $this->command->error("Primero debes ejecutar el PacienteSeeder");
            return;
        }

        for ($i = 0; $i < 100; $i++) {
            $paciente = $pacientes->random();
            $total = $faker->randomFloat(2, 50, 5000);
            $estado = $faker->randomElement(['pendiente', 'parcial', 'pagado']);

            // Lógica de seguro basada en si el paciente tiene seguro_id
            $tieneSeguro = !is_null($paciente->seguro_id);
            $seguroEstado = null;
            $montoCobertura = 0;
            $montoPaciente = $total;

            if ($tieneSeguro) {
                $seguroEstado = $faker->randomElement(['pendiente_autorizacion', 'autorizado', 'rechazado']);

                if ($seguroEstado === 'autorizado') {
                    $porcentajeCobertura = $faker->randomElement([0.7, 0.8, 1.0]); // 70%, 80% o 100%
                    $montoCobertura = $total * $porcentajeCobertura;
                    $montoPaciente = $total - $montoCobertura;
                }
            }

            CuentaCobro::create([
                'id' => 'CC-' . date('Ymd') . '-' . Str::upper(Str::random(6)),
                'paciente_ci' => $paciente->ci,
                'tipo_atencion' => $faker->randomElement(['Consulta Externa', 'Emergencia', 'Laboratorio', 'Rayos X']),
                'estado' => $estado,
                'total_calculado' => $total,
                'total_pagado' => $estado === 'pagado' ? $montoPaciente : ($estado === 'parcial' ? $montoPaciente / 2 : 0),
                'es_emergencia' => $faker->boolean(20),
                'es_post_pago' => $faker->boolean(10),
                'episodio_numero' => $faker->numberBetween(1, 5),
                'razon_social' => $paciente->nombre,
                'ci_nit_facturacion' => $paciente->ci,
                // Datos de Seguro
                'seguro_id' => $paciente->seguro_id,
                'seguro_estado' => $seguroEstado,
                'seguro_autorizado_por' => ($seguroEstado === 'autorizado' || $seguroEstado === 'rechazado') ? $faker->randomElement($users) : null,
                'seguro_fecha_autorizacion' => $seguroEstado ? $faker->dateTimeBetween('-1 month', 'now') : null,
                'seguro_monto_cobertura' => $montoCobertura,
                'seguro_monto_paciente' => $montoPaciente,
                'seguro_observaciones' => $seguroEstado === 'rechazado' ? 'Falta orden médica original o documentos incompletos.' : null,
                'created_at' => $faker->dateTimeBetween('-3 months', 'now'),
            ]);
        }
    }
}
