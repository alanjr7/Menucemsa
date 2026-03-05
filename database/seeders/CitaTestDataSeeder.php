<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Especialidad;
use App\Models\Medico;
use App\Models\Cita;
use App\Models\Paciente;
use Carbon\Carbon;

class CitaTestDataSeeder extends Seeder
{
    public function run()
    {
        // Crear especialidades si no existen
        $especialidades = [
            ['nombre' => 'Medicina General', 'descripcion' => 'Medicina general y consultas de rutina'],
            ['nombre' => 'Cardiología', 'descripcion' => 'Especialidad del corazón y sistema circulatorio'],
            ['nombre' => 'Pediatría', 'descripcion' => 'Atención médica infantil'],
            ['nombre' => 'Ginecología', 'descripcion' => 'Salud de la mujer'],
            ['nombre' => 'Cirugía General', 'descripcion' => 'Procedimientos quirúrgicos generales'],
        ];

        foreach ($especialidades as $esp) {
            Especialidad::firstOrCreate(['nombre' => $esp['nombre']], $esp);
        }

        // Crear algunas citas de prueba para hoy con datos existentes
        $pacientes = Paciente::limit(3)->get();
        $medicos = Medico::limit(3)->get();
        $especialidades = Especialidad::limit(3)->get();

        if ($pacientes->count() >= 2 && $medicos->count() >= 1) {
            $citasData = [
                [
                    'ci_paciente' => $pacientes[0]->ci,
                    'ci_medico' => $medicos[0]->ci,
                    'codigo_especialidad' => $especialidades[0]->codigo,
                    'fecha' => Carbon::today(),
                    'hora' => '08:00:00',
                    'motivo' => 'Control de presión arterial',
                    'estado' => 'programado',
                    'confirmado' => true,
                    'id_usuario_registro' => 1
                ],
                [
                    'ci_paciente' => $pacientes[1]->ci ?? $pacientes[0]->ci,
                    'ci_medico' => $medicos[0]->ci,
                    'codigo_especialidad' => $especialidades[1]->codigo,
                    'fecha' => Carbon::today(),
                    'hora' => '09:30:00',
                    'motivo' => 'Dolor de cabeza persistente',
                    'estado' => 'programado',
                    'confirmado' => false,
                    'id_usuario_registro' => 1
                ],
                [
                    'ci_paciente' => $pacientes[0]->ci,
                    'ci_medico' => $medicos[0]->ci,
                    'codigo_especialidad' => $especialidades[2]->codigo,
                    'fecha' => Carbon::today()->addDay(),
                    'hora' => '10:00:00',
                    'motivo' => 'Revisión pediátrica',
                    'estado' => 'programado',
                    'confirmado' => false,
                    'id_usuario_registro' => 1
                ]
            ];

            foreach ($citasData as $citaData) {
                Cita::firstOrCreate([
                    'ci_paciente' => $citaData['ci_paciente'],
                    'fecha' => $citaData['fecha'],
                    'hora' => $citaData['hora']
                ], $citaData);
            }
        }

        $this->command->info('Datos de prueba para citas creados exitosamente');
    }
}
