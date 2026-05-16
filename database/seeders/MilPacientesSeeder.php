<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Paciente;
use App\Models\Emergency;
use App\Models\Hospitalizacion;
use App\Models\Consulta;
use App\Models\Episodio;
use App\Models\Seguro;
use App\Models\User;
use App\Models\Registro;
use Faker\Factory as Faker;

class MilPacientesSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('es_ES');

        $segurosIds      = Seguro::pluck('id')->toArray();
        $expediciones    = ['LP', 'SC', 'CB', 'OR', 'PT', 'CH', 'TJ', 'BE', 'PD'];
        $userId          = User::first()?->id ?? 1;
        $especialidades  = DB::table('especialidades')->pluck('codigo')->toArray();

        // Camas agrupadas por habitacion para asignarlas consistentemente
        $camasPorHabitacion = DB::table('camas')
            ->get(['id', 'habitacion_id'])
            ->groupBy('habitacion_id')
            ->map(fn($grupo) => $grupo->pluck('id')->toArray())
            ->toArray();
        $habitacionIds = array_keys($camasPorHabitacion);

        $diagnosticos = [
            'Hipertensión arterial', 'Diabetes mellitus tipo 2', 'Fractura de fémur',
            'Apendicitis aguda', 'Neumonía bacteriana', 'Insuficiencia cardíaca',
            'Gastroenteritis aguda', 'Traumatismo craneoencefálico', 'Pancreatitis aguda',
            'Infección urinaria', 'Pielonefritis', 'Colecistitis aguda', 'Hernia inguinal',
            'Obstrucción intestinal', 'Sepsis', 'ACV isquémico', 'Eclampsia',
            'Parto normal', 'Cesárea de emergencia', 'Quemaduras de 2do grado',
        ];

        $sintomas = [
            'Dolor abdominal intenso', 'Fiebre alta y escalofríos', 'Dificultad respiratoria',
            'Pérdida de conciencia', 'Sangrado abundante', 'Convulsiones',
            'Dolor torácico', 'Vómitos y diarrea persistente', 'Traumatismo por accidente',
            'Cefalea intensa', 'Edema generalizado', 'Hipotensión severa',
        ];

        // Tipos de ingreso con pesos: emergencia 35%, internacion 25%, consulta 30%, uti 10%
        $tiposIngreso = ['emergencia', 'internacion', 'consulta', 'uti'];
        $pesos        = [35, 25, 30, 10];

        $counters      = ['emergencia' => 0, 'internacion' => 0, 'consulta' => 0, 'uti' => 0];
        $creados       = 0;
        $errores       = 0;
        $codigosUsados = [];

        for ($i = 0; $i < 1000; $i++) {
            $ci           = $faker->unique()->numberBetween(1000000, 9999999);
            $fechaIngreso = $faker->dateTimeBetween('-2 years', 'now');
            $tipoIngreso  = $this->weightedRandom($tiposIngreso, $pesos, $faker);
            $nombre       = $faker->name;
            $sexo         = $faker->randomElement(['M', 'F']);
            $fechaNac     = $faker->dateTimeBetween('-80 years', '-1 year')->format('Y-m-d');

            try {
                DB::transaction(function () use (
                    $faker, $ci, $fechaIngreso, $tipoIngreso,
                    $segurosIds, $expediciones, $userId, $especialidades,
                    $camasPorHabitacion, $habitacionIds,
                    $diagnosticos, $sintomas,
                    $nombre, $sexo, $fechaNac,
                    &$counters, &$codigosUsados
                ) {
                    // Generar código de registro único
                    $base   = Registro::generarCodigo(['nombre' => $nombre, 'fecha_nacimiento' => $fechaNac, 'sexo' => $sexo]);
                    $codigo = $base;
                    $sufijo = 1;
                    while (in_array($codigo, $codigosUsados)) {
                        $codigo = $base . '-' . $sufijo++;
                    }
                    $codigosUsados[] = $codigo;

                    Registro::create([
                        'codigo'  => $codigo,
                        'fecha'   => $fechaIngreso->format('Y-m-d'),
                        'hora'    => $fechaIngreso->format('H:i:s'),
                        'motivo'  => 'Registro inicial',
                        'user_id' => $userId,
                    ]);

                    $paciente = Paciente::create([
                        'ci'               => $ci,
                        'nombre'           => $nombre,
                        'sexo'             => $sexo,
                        'fecha_nacimiento' => $fechaNac,
                        'lugar_expedicion' => $faker->randomElement($expediciones),
                        'nacionalidad'     => 'Boliviana',
                        'estado_civil'     => $faker->randomElement(['Soltero(a)', 'Casado(a)', 'Divorciado(a)', 'Viudo(a)']),
                        'direccion'        => $faker->streetAddress,
                        'telefono'         => $faker->numerify('7#######'),
                        'correo'           => $faker->boolean(60) ? $faker->safeEmail : null,
                        'profesion'        => $faker->jobTitle,
                        'empresa_trabajo'  => $faker->boolean(50) ? $faker->company : null,
                        'seguro_id'        => $segurosIds && $faker->boolean(55)
                                                ? $faker->randomElement($segurosIds)
                                                : null,
                        'registro_codigo'  => $codigo,
                    ]);

                    // Todos los ingresos tienen episodio abierto para aparecer en /patients
                    $episodioEstado = $tipoIngreso === 'consulta'
                        ? 'cerrado'
                        : 'abierto';

                    $episodio = Episodio::create([
                        'paciente_ci'   => $ci,
                        'numero'        => 1,
                        'fecha_apertura'=> $fechaIngreso->format('Y-m-d H:i:s'),
                        'fecha_cierre'  => $episodioEstado === 'cerrado'
                                            ? $faker->dateTimeBetween($fechaIngreso, 'now')->format('Y-m-d H:i:s')
                                            : null,
                        'estado'        => $episodioEstado,
                        'tipo_ingreso'  => $tipoIngreso,
                        'created_by'    => $userId,
                        'closed_by'     => $episodioEstado === 'cerrado' ? $userId : null,
                    ]);

                    $counters[$tipoIngreso]++;
                    $n = $counters[$tipoIngreso];

                    match ($tipoIngreso) {
                        'emergencia' => $this->crearEmergencia(
                            $faker, $ci, $userId, $n, $fechaIngreso, $episodio->id,
                            $segurosIds, $diagnosticos, $sintomas
                        ),
                        'internacion' => $this->crearInternacion(
                            $faker, $ci, $n, $fechaIngreso, $episodio->id,
                            $habitacionIds, $camasPorHabitacion, $diagnosticos
                        ),
                        'consulta' => $this->crearConsulta(
                            $faker, $ci, $n, $fechaIngreso, $especialidades
                        ),
                        'uti' => $this->crearUti(
                            $faker, $ci, $n, $fechaIngreso, $episodio->id, $segurosIds
                        ),
                    };
                });

                $creados++;
            } catch (\Throwable $e) {
                $errores++;
            }
        }

        $this->command->info(sprintf(
            'Seeder completado: %d pacientes creados, %d errores.',
            $creados, $errores
        ));
        $this->command->table(
            ['Tipo', 'Cantidad'],
            collect($counters)->map(fn($v, $k) => [$k, $v])->values()->toArray()
        );
    }

    private function crearEmergencia(
        $faker, int $ci, int $userId, int $n,
        \DateTime $fechaIngreso, int $episodioId,
        array $segurosIds, array $diagnosticos, array $sintomas
    ): void {
        $tipoEmg      = $faker->randomElement(['soat', 'parto', 'general', 'general', 'general']);
        $destinoInicial = match ($tipoEmg) {
            'parto' => 'parto',
            'soat'  => $faker->randomElement(['camilla', 'cirugia', 'observacion']),
            default => $faker->randomElement(['camilla', 'observacion', 'uti', 'hospitalizacion']),
        };

        $estaAlta      = $faker->boolean(75);
        $dischargeDate = $estaAlta
            ? $faker->dateTimeBetween($fechaIngreso, 'now')->format('Y-m-d H:i:s')
            : null;
        $status        = $dischargeDate
            ? $faker->randomElement(['alta', 'estabilizado'])
            : $faker->randomElement(['recibido', 'en_evaluacion']);

        Emergency::create([
            'patient_id'        => $ci,
            'user_id'           => $userId,
            'code'              => 'EMG-' . $fechaIngreso->format('Ymd') . '-' . str_pad($n, 5, '0', STR_PAD_LEFT),
            'status'            => $status,
            'tipo_ingreso'      => $tipoEmg,
            'destino_inicial'   => $destinoInicial,
            'is_temp_id'        => false,
            'symptoms'          => $faker->randomElement($sintomas),
            'initial_assessment'=> $faker->randomElement($diagnosticos),
            'observations'      => $faker->boolean(50) ? $faker->sentence(6) : null,
            'ubicacion_actual'  => $dischargeDate ? 'alta' : 'emergencia',
            'cost'              => $faker->randomFloat(2, 80, 4000),
            'paid'              => $faker->boolean(60),
            'deuda'             => 0,
            'total_pagado'      => 0,
            'admission_date'    => $fechaIngreso->format('Y-m-d H:i:s'),
            'discharge_date'    => $dischargeDate,
            'episodio_id'       => $episodioId,
        ]);
    }

    private function crearInternacion(
        $faker, int $ci, int $n,
        \DateTime $fechaIngreso, int $episodioId,
        array $habitacionIds, array $camasPorHabitacion, array $diagnosticos
    ): void {
        $habitacionId = $habitacionIds ? $faker->randomElement($habitacionIds) : null;
        $camaId       = $habitacionId && !empty($camasPorHabitacion[$habitacionId])
            ? $faker->randomElement($camasPorHabitacion[$habitacionId])
            : null;

        $estaAlta     = $faker->boolean(70);
        $fechaAlta    = $estaAlta
            ? $faker->dateTimeBetween($fechaIngreso, 'now')->format('Y-m-d H:i:s')
            : null;

        Hospitalizacion::create([
            'id'                  => 'HOSP-' . $fechaIngreso->format('Ymd') . '-' . str_pad($n, 5, '0', STR_PAD_LEFT),
            'ci_paciente'         => $ci,
            'habitacion_id'       => $habitacionId,
            'cama_id'             => $camaId,
            'precio_cama_dia'     => $faker->randomElement([150, 200, 250, 300, 400, 500]),
            'total_estancia'      => 0,
            'fecha_ingreso'       => $fechaIngreso->format('Y-m-d H:i:s'),
            'fecha_alta'          => $fechaAlta,
            'diagnostico'         => $faker->randomElement($diagnosticos),
            'tratamiento'         => $faker->sentence(8),
            'estado'              => $fechaAlta ? 'alta' : 'activo',
            'motivo'              => $faker->sentence(5),
            'contacto_nombre'     => $faker->name,
            'contacto_telefono'   => $faker->numerify('7#######'),
            'contacto_parentesco' => $faker->randomElement(['Madre', 'Padre', 'Hijo/a', 'Cónyuge', 'Hermano/a', 'Tutor']),
            'episodio_id'         => $episodioId,
        ]);
    }

    private function crearConsulta(
        $faker, int $ci, int $n,
        \DateTime $fechaIngreso, array $especialidades
    ): void {
        Consulta::create([
            'codigo'             => 'CONS-' . $fechaIngreso->format('Y') . '-' . str_pad($n, 6, '0', STR_PAD_LEFT),
            'fecha'              => $fechaIngreso->format('Y-m-d'),
            'hora'               => $fechaIngreso->format('H:i:s'),
            'motivo'             => $faker->sentence(5),
            'observaciones'      => $faker->boolean(45) ? $faker->sentence(7) : null,
            'codigo_especialidad'=> $especialidades ? $faker->randomElement($especialidades) : null,
            'ci_paciente'        => $ci,
            'estado_pago'        => $faker->boolean(72),
            'estado'             => $faker->randomElement(['atendido', 'atendido', 'atendido', 'cancelado', 'pendiente']),
            'tipo'               => $faker->randomElement(['consulta_externa', 'consulta_externa', 'enfermeria']),
        ]);
    }

    private function crearUti(
        $faker, int $ci, int $n,
        \DateTime $fechaIngreso, int $episodioId, array $segurosIds
    ): void {
        $tieneSeguro = $segurosIds && $faker->boolean(40);
        $estadoUti   = $faker->randomElement(['activo', 'alta_clinica', 'alta_administrativa', 'trasladado']);

        DB::table('uti_admissions')->insert([
            'patient_id'         => $ci,
            'nro_ingreso'        => 'UTI-' . $fechaIngreso->format('Ymd') . '-' . str_pad($n, 4, '0', STR_PAD_LEFT),
            'estado_clinico'     => $faker->randomElement(['estable', 'estable', 'critico', 'muy_critico']),
            'diagnostico_principal' => $faker->randomElement([
                'Sepsis severa', 'Fallo multiorgánico', 'Traumatismo grave',
                'Insuficiencia respiratoria aguda', 'Shock cardiogénico', 'ACV hemorrágico',
                'Politraumatismo', 'Intoxicación grave',
            ]),
            'tipo_ingreso'       => $faker->randomElement(['emergencia', 'emergencia', 'quirofano', 'derivacion_interna']),
            'tipo_pago'          => $tieneSeguro ? 'seguro' : 'particular',
            'seguro_id'          => $tieneSeguro ? $faker->randomElement($segurosIds) : null,
            'fecha_ingreso'      => $fechaIngreso->format('Y-m-d H:i:s'),
            'fecha_alta_clinica' => in_array($estadoUti, ['alta_clinica', 'alta_administrativa', 'trasladado'])
                ? $faker->dateTimeBetween($fechaIngreso, 'now')->format('Y-m-d H:i:s')
                : null,
            'estado'             => $estadoUti,
            'destino_alta'       => in_array($estadoUti, ['alta_clinica', 'alta_administrativa'])
                ? $faker->randomElement(['hospitalizacion', 'domicilio', 'otro_hospital'])
                : null,
            'observaciones'      => $faker->boolean(40) ? $faker->sentence(6) : null,
            'episodio_id'        => $episodioId,
            'created_at'         => now(),
            'updated_at'         => now(),
        ]);
    }

    private function weightedRandom(array $items, array $pesos, $faker): string
    {
        $total      = array_sum($pesos);
        $rand       = $faker->numberBetween(1, $total);
        $acumulado  = 0;
        foreach ($items as $i => $item) {
            $acumulado += $pesos[$i];
            if ($rand <= $acumulado) {
                return $item;
            }
        }
        return $items[0];
    }
}
