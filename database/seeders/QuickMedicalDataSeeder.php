<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Paciente;
use App\Models\Consulta;
use App\Models\Caja;
use App\Models\Especialidad;
use App\Models\Medico;

class QuickMedicalDataSeeder extends Seeder
{
    public function run(): void
    {
        // Get the current doctor
        $doctor = Medico::where('ci', 87654321)->first();
        
        if (!$doctor) {
            $this->command->error('Doctor not found!');
            return;
        }

        // Create especialidad
        $especialidad = Especialidad::firstOrCreate(
            ['codigo' => 'GENERAL'],
            [
                'nombre' => 'Medicina General',
                'descripcion' => 'Especialidad de medicina general'
            ]
        );

        // Create or get cajas
        $caja1 = Caja::firstOrCreate(
            ['id' => 'CAJA-TEST-001'],
            [
                'fecha' => now(),
                'total_dia' => 150.00,
                'tipo' => 'CONSULTA_EXTERNA',
                'nro_factura' => 1001,
                'id_farmacia' => null,
                'nro_pago_internos' => 'TEST-001'
            ]
        );

        $caja2 = Caja::firstOrCreate(
            ['id' => 'CAJA-TEST-002'],
            [
                'fecha' => now(),
                'total_dia' => 80.00,
                'tipo' => 'CONSULTA_EXTERNA',
                'nro_factura' => 1002,
                'id_farmacia' => null,
                'nro_pago_internos' => 'TEST-002'
            ]
        );

        $caja3 = Caja::firstOrCreate(
            ['id' => 'CAJA-TEST-003'],
            [
                'fecha' => now(),
                'total_dia' => 120.00,
                'tipo' => 'CONSULTA_EXTERNA',
                'nro_factura' => 1003,
                'id_farmacia' => null,
                'nro_pago_internos' => 'TEST-003'
            ]
        );

        // Create consultas assigned to the current doctor
        $consulta1 = Consulta::create([
            'nro' => 'CONS-2026-000001',
            'fecha' => today()->format('Y-m-d'),
            'hora' => '08:30:00',
            'motivo' => 'Dolor de cabeza',
            'observaciones' => 'Paciente refiere dolor desde hace 2 días',
            'codigo_especialidad' => $especialidad->codigo,
            'ci_paciente' => 12345678,
            'ci_medico' => $doctor->ci,
            'estado_pago' => true,
            'id_caja' => $caja1->id,
            'estado' => 'pendiente'
        ]);

        $consulta2 = Consulta::create([
            'nro' => 'CONS-2026-000002',
            'fecha' => today()->format('Y-m-d'),
            'hora' => '10:15:00',
            'motivo' => 'Fiebre y tos',
            'observaciones' => 'Paciente con síntomas gripales',
            'codigo_especialidad' => $especialidad->codigo,
            'ci_paciente' => 12345678,
            'ci_medico' => $doctor->ci,
            'estado_pago' => true,
            'id_caja' => $caja2->id,
            'estado' => 'en_atencion'
        ]);

        $consulta3 = Consulta::create([
            'nro' => 'CONS-2026-000003',
            'fecha' => today()->format('Y-m-d'),
            'hora' => '09:00:00',
            'motivo' => 'Control de presión',
            'observaciones' => 'Paciente con hipertensión controlada',
            'codigo_especialidad' => $especialidad->codigo,
            'ci_paciente' => 12345678,
            'ci_medico' => $doctor->ci,
            'estado_pago' => true,
            'id_caja' => $caja3->id,
            'estado' => 'completada'
        ]);

        $this->command->info('Medical test data created successfully');
        $this->command->info('Doctor CI: ' . $doctor->ci);
        $this->command->info('Consultas for doctor: ' . Consulta::where('ci_medico', $doctor->ci)->count());
        $this->command->info('Pendientes: ' . Consulta::where('ci_medico', $doctor->ci)->where('estado', 'pendiente')->count());
        $this->command->info('En atención: ' . Consulta::where('ci_medico', $doctor->ci)->where('estado', 'en_atencion')->count());
        $this->command->info('Completadas: ' . Consulta::where('ci_medico', $doctor->ci)->where('estado', 'completada')->count());
    }
}
