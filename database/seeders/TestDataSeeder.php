<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Paciente;
use App\Models\Consulta;
use App\Models\Caja;
use App\Models\Especialidad;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear especialidad general
        $especialidad = Especialidad::firstOrCreate(
            ['codigo' => 'GENERAL'],
            [
                'nombre' => 'Medicina General',
                'descripcion' => 'Especialidad de medicina general'
            ]
        );

        // Obtener pacientes existentes o crear nuevos
        $paciente1 = Paciente::firstOrCreate(
            ['ci' => '12345678'],
            [
                'nombre' => 'Juan Pérez García',
                'sexo' => 'Masculino',
                'direccion' => 'Av. Principal 123',
                'telefono' => '987654321',
                'correo' => 'juan.perez@email.com',
                'codigo_seguro' => 1,
                'id_triage' => 'TRIAGE-CONSULTA',
                'codigo_registro' => 'REG-2026-000001'
            ]
        );

        $paciente2 = Paciente::firstOrCreate(
            ['ci' => '87654321'],
            [
                'nombre' => 'María López Rodríguez',
                'sexo' => 'Femenino',
                'direccion' => 'Calle Secundaria 456',
                'telefono' => '912345678',
                'correo' => 'maria.lopez@email.com',
                'codigo_seguro' => 2,
                'id_triage' => 'TRIAGE-CONSULTA',
                'codigo_registro' => 'REG-2026-000002'
            ]
        );

        // Crear consultas pendientes de pago
        $consulta1 = Consulta::create([
            'nro' => 'CONS-2026-000001',
            'fecha' => '2026-03-04',
            'hora' => '08:30:00',
            'motivo' => 'Dolor de cabeza',
            'observaciones' => 'Paciente refiere dolor desde hace 2 días',
            'codigo_especialidad' => $especialidad->codigo,
            'ci_paciente' => $paciente1->ci,
            'ci_medico' => null,
            'estado_pago' => false,
            'id_caja' => null
        ]);

        $consulta2 = Consulta::create([
            'nro' => 'CONS-2026-000002',
            'fecha' => '2026-03-04',
            'hora' => '10:15:00',
            'motivo' => 'Fiebre y tos',
            'observaciones' => 'Paciente con síntomas gripales',
            'codigo_especialidad' => $especialidad->codigo,
            'ci_paciente' => $paciente2->ci,
            'ci_medico' => null,
            'estado_pago' => false,
            'id_caja' => null
        ]);

        // Crear registros de caja pendientes
        $caja1 = Caja::create([
            'id' => 'CAJA-2026030400001',
            'fecha' => now(),
            'total_dia' => 50.00,
            'tipo' => 'CONSULTA_EXTERNA',
            'nro_factura' => null, // Pendiente de pago
            'id_farmacia' => null,
            'nro_pago_internos' => 'CONSULTA-' . date('YmdHis') . '001'
        ]);

        $caja2 = Caja::create([
            'id' => 'CAJA-2026030400002',
            'fecha' => now(),
            'total_dia' => 50.00,
            'tipo' => 'CONSULTA_EXTERNA',
            'nro_factura' => null, // Pendiente de pago
            'id_farmacia' => null,
            'nro_pago_internos' => 'CONSULTA-' . date('YmdHis') . '002'
        ]);

        // Actualizar consultas con sus cajas
        $consulta1->id_caja = $caja1->id;
        $consulta1->save();

        $consulta2->id_caja = $caja2->id;
        $consulta2->save();

        // Crear algunos pagos ya procesados
        $cajaPagada1 = Caja::create([
            'id' => 'CAJA-2026030400003',
            'fecha' => now()->subHours(2),
            'total_dia' => 150.00,
            'tipo' => 'EMERGENCIA',
            'nro_factura' => 1001, // Ya pagada
            'id_farmacia' => null,
            'nro_pago_internos' => 'EMER-' . date('YmdHis') . '001',
            'metodo_pago' => 'EFECTIVO',
            'estado' => 'pagado'
        ]);

        $consultaPagada1 = Consulta::create([
            'nro' => 'CONS-2026-000003',
            'fecha' => '2026-03-04',
            'hora' => '06:45:00',
            'motivo' => 'Dolor abdominal agudo',
            'observaciones' => 'Atención de emergencia',
            'codigo_especialidad' => $especialidad->codigo,
            'ci_paciente' => '12345678',
            'ci_medico' => null,
            'estado_pago' => true, // Ya pagada
            'id_caja' => $cajaPagada1->id
        ]);

        $cajaPagada2 = Caja::create([
            'id' => 'CAJA-2026030400004',
            'fecha' => now()->subHours(1),
            'total_dia' => 80.00,
            'tipo' => 'LABORATORIO',
            'nro_factura' => 1002, // Ya pagada
            'id_farmacia' => null,
            'nro_pago_internos' => 'LAB-' . date('YmdHis') . '001',
            'metodo_pago' => 'TARJETA',
            'estado' => 'pagado'
        ]);

        $consultaPagada2 = Consulta::create([
            'nro' => 'CONS-2026-000004',
            'fecha' => '2026-03-04',
            'hora' => '09:30:00',
            'motivo' => 'Análisis de laboratorio',
            'observaciones' => 'Hemograma completo',
            'codigo_especialidad' => $especialidad->codigo,
            'ci_paciente' => '87654321',
            'ci_medico' => null,
            'estado_pago' => true, // Ya pagada
            'id_caja' => $cajaPagada2->id
        ]);

        $this->commandInfo('Datos de prueba creados exitosamente');
        $this->commandInfo('Pacientes: ' . Paciente::count());
        $this->commandInfo('Consultas: ' . Consulta::count());
        $this->commandInfo('Cajas: ' . Caja::count());
    }
}
