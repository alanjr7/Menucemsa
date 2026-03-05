<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Paciente;
use App\Models\Consulta;
use App\Models\Caja;
use App\Models\Especialidad;
use App\Models\Seguro;
use App\Models\Triage;
use App\Models\Registro;
use App\Models\Medico;

class CreateMedicalTestData extends Seeder
{
    public function run(): void
    {
        // Create seguro
        $seguro = Seguro::firstOrCreate(
            ['codigo' => 1],
            [
                'nombre' => 'Seguro Básico',
                'descripcion' => 'Seguro médico básico'
            ]
        );

        // Create triage
        $triage = Triage::firstOrCreate(
            ['id' => 'TRIAGE-CONSULTA'],
            [
                'nivel' => 'Consulta Externa',
                'descripcion' => 'Paciente para consulta externa'
            ]
        );

        // Create registro
        $registro = Registro::firstOrCreate(
            ['codigo' => 'REG-2026-000001'],
            [
                'fecha' => now(),
                'id_triage' => 'TRIAGE-CONSULTA'
            ]
        );

        // Get the current doctor
        $doctor = Medico::where('ci', 87654321)->first(); // The doctor we created earlier

        // Create especialidad
        $especialidad = Especialidad::firstOrCreate(
            ['codigo' => 'GENERAL'],
            [
                'nombre' => 'Medicina General',
                'descripcion' => 'Especialidad de medicina general'
            ]
        );

        // Create pacientes
        $paciente1 = Paciente::firstOrCreate(
            ['ci' => 12345678],
            [
                'nombre' => 'Juan Pérez García',
                'sexo' => 'Masculino',
                'direccion' => 'Av. Principal 123',
                'telefono' => 987654321,
                'correo' => 'juan.perez@email.com',
                'codigo_seguro' => $seguro->codigo,
                'id_triage' => $triage->id,
                'codigo_registro' => $registro->codigo
            ]
        );

        $paciente2 = Paciente::firstOrCreate(
            ['ci' => 87654322],
            [
                'nombre' => 'María López Rodríguez',
                'sexo' => 'Femenino',
                'direccion' => 'Calle Secundaria 456',
                'telefono' => 912345678,
                'correo' => 'maria.lopez@email.com',
                'codigo_seguro' => $seguro->codigo,
                'id_triage' => $triage->id,
                'codigo_registro' => 'REG-2026-000002'
            ]
        );

        // Create paid cajas
        $caja1 = Caja::create([
            'id' => 'CAJA-2026030400001',
            'fecha' => now(),
            'total_dia' => 150.00,
            'tipo' => 'CONSULTA_EXTERNA',
            'nro_factura' => 1001,
            'id_farmacia' => null,
            'nro_pago_internos' => 'CONSULTA-' . date('YmdHis') . '001',
            'metodo_pago' => 'EFECTIVO',
            'estado' => 'pagado'
        ]);

        $caja2 = Caja::create([
            'id' => 'CAJA-2026030400002',
            'fecha' => now(),
            'total_dia' => 80.00,
            'tipo' => 'CONSULTA_EXTERNA',
            'nro_factura' => 1002,
            'id_farmacia' => null,
            'nro_pago_internos' => 'CONSULTA-' . date('YmdHis') . '002',
            'metodo_pago' => 'TARJETA',
            'estado' => 'pagado'
        ]);

        // Create consultas assigned to the current doctor and paid
        $consulta1 = Consulta::create([
            'nro' => 'CONS-2026-000001',
            'fecha' => today()->format('Y-m-d'),
            'hora' => '08:30:00',
            'motivo' => 'Dolor de cabeza',
            'observaciones' => 'Paciente refiere dolor desde hace 2 días',
            'codigo_especialidad' => $especialidad->codigo,
            'ci_paciente' => $paciente1->ci,
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
            'ci_paciente' => $paciente2->ci,
            'ci_medico' => $doctor->ci,
            'estado_pago' => true,
            'id_caja' => $caja2->id,
            'estado' => 'pendiente'
        ]);

        // Create a consultation in progress
        $caja3 = Caja::create([
            'id' => 'CAJA-2026030400003',
            'fecha' => now(),
            'total_dia' => 120.00,
            'tipo' => 'CONSULTA_EXTERNA',
            'nro_factura' => 1003,
            'id_farmacia' => null,
            'nro_pago_internos' => 'CONSULTA-' . date('YmdHis') . '003',
            'metodo_pago' => 'EFECTIVO',
            'estado' => 'pagado'
        ]);

        $consulta3 = Consulta::create([
            'nro' => 'CONS-2026-000003',
            'fecha' => today()->format('Y-m-d'),
            'hora' => '09:00:00',
            'motivo' => 'Dolor abdominal',
            'observaciones' => 'Paciente en atención actualmente',
            'codigo_especialidad' => $especialidad->codigo,
            'ci_paciente' => $paciente1->ci,
            'ci_medico' => $doctor->ci,
            'estado_pago' => true,
            'id_caja' => $caja3->id,
            'estado' => 'en_atencion'
        ]);

        // Create a completed consultation
        $caja4 = Caja::create([
            'id' => 'CAJA-2026030400004',
            'fecha' => now()->subHours(2),
            'total_dia' => 100.00,
            'tipo' => 'CONSULTA_EXTERNA',
            'nro_factura' => 1004,
            'id_farmacia' => null,
            'nro_pago_internos' => 'CONSULTA-' . date('YmdHis') . '004',
            'metodo_pago' => 'TARJETA',
            'estado' => 'pagado'
        ]);

        $consulta4 = Consulta::create([
            'nro' => 'CONS-2026-000004',
            'fecha' => today()->format('Y-m-d'),
            'hora' => '07:30:00',
            'motivo' => 'Control de presión',
            'observaciones' => 'Paciente con hipertensión controlada',
            'codigo_especialidad' => $especialidad->codigo,
            'ci_paciente' => $paciente2->ci,
            'ci_medico' => $doctor->ci,
            'estado_pago' => true,
            'id_caja' => $caja4->id,
            'estado' => 'completada'
        ]);

        $this->command->info('Medical test data created successfully');
        $this->command->info('Pacientes: ' . Paciente::count());
        $this->command->info('Consultas: ' . Consulta::count());
        $this->command->info('Cajas: ' . Caja::count());
        $this->command->info('Consultas for doctor ' . $doctor->ci . ': ' . Consulta::where('ci_medico', $doctor->ci)->count());
    }
}
