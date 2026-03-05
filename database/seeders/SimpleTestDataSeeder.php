<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SimpleTestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Insertar datos de prueba directamente con SQL
        DB::table('consultas')->insert([
            [
                'nro' => 'CONS-2026-000001',
                'fecha' => '2026-03-04',
                'hora' => '08:30:00',
                'motivo' => 'Dolor de cabeza',
                'observaciones' => 'Paciente refiere dolor desde hace 2 días',
                'codigo_especialidad' => 'GENERAL',
                'ci_paciente' => '12345678',
                'ci_medico' => null,
                'estado_pago' => false,
                'id_caja' => null,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nro' => 'CONS-2026-000002',
                'fecha' => '2026-03-04',
                'hora' => '10:15:00',
                'motivo' => 'Fiebre y tos',
                'observaciones' => 'Paciente con síntomas gripales',
                'codigo_especialidad' => 'GENERAL',
                'ci_paciente' => '87654321',
                'ci_medico' => null,
                'estado_pago' => false,
                'id_caja' => null,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);

        // Insertar cajas pendientes
        DB::table('cajas')->insert([
            [
                'id' => 'CAJA-2026030400001',
                'fecha' => now(),
                'total_dia' => 50.00,
                'tipo' => 'CONSULTA_EXTERNA',
                'nro_factura' => null,
                'id_farmacia' => null,
                'nro_pago_internos' => 'CONSULTA-' . date('YmdHis') . '001',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 'CAJA-2026030400002',
                'fecha' => now(),
                'total_dia' => 50.00,
                'tipo' => 'CONSULTA_EXTERNA',
                'nro_factura' => null,
                'id_farmacia' => null,
                'nro_pago_internos' => 'CONSULTA-' . date('YmdHis') . '002',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);

        // Actualizar consultas con sus cajas
        DB::table('consultas')
            ->where('nro', 'CONS-2026-000001')
            ->update(['id_caja' => 'CAJA-2026030400001']);

        DB::table('consultas')
            ->where('nro', 'CONS-2026-000002')
            ->update(['id_caja' => 'CAJA-2026030400002']);

        // Insertar algunos pagos ya procesados
        DB::table('cajas')->insert([
            [
                'id' => 'CAJA-2026030400003',
                'fecha' => now()->subHours(2),
                'total_dia' => 150.00,
                'tipo' => 'EMERGENCIA',
                'nro_factura' => 1001,
                'id_farmacia' => null,
                'nro_pago_internos' => 'EMER-' . date('YmdHis') . '001',
                'metodo_pago' => 'EFECTIVO',
                'estado' => 'pagado',
                'created_at' => now()->subHours(2),
                'updated_at' => now()->subHours(2)
            ],
            [
                'id' => 'CAJA-2026030400004',
                'fecha' => now()->subHours(1),
                'total_dia' => 80.00,
                'tipo' => 'LABORATORIO',
                'nro_factura' => 1002,
                'id_farmacia' => null,
                'nro_pago_internos' => 'LAB-' . date('YmdHis') . '001',
                'metodo_pago' => 'TARJETA',
                'estado' => 'pagado',
                'created_at' => now()->subHours(1),
                'updated_at' => now()->subHours(1)
            ]
        ]);

        // Insertar consultas pagadas
        DB::table('consultas')->insert([
            [
                'nro' => 'CONS-2026-000003',
                'fecha' => '2026-03-04',
                'hora' => '06:45:00',
                'motivo' => 'Dolor abdominal agudo',
                'observaciones' => 'Atención de emergencia',
                'codigo_especialidad' => 'GENERAL',
                'ci_paciente' => '12345678',
                'ci_medico' => null,
                'estado_pago' => true,
                'id_caja' => 'CAJA-2026030400003',
                'created_at' => now()->subHours(2),
                'updated_at' => now()->subHours(2)
            ],
            [
                'nro' => 'CONS-2026-000004',
                'fecha' => '2026-03-04',
                'hora' => '09:30:00',
                'motivo' => 'Análisis de laboratorio',
                'observaciones' => 'Hemograma completo',
                'codigo_especialidad' => 'GENERAL',
                'ci_paciente' => '87654321',
                'ci_medico' => null,
                'estado_pago' => true,
                'id_caja' => 'CAJA-2026030400004',
                'created_at' => now()->subHours(1),
                'updated_at' => now()->subHours(1)
            ]
        ]);

        $this->commandInfo('Datos de prueba creados exitosamente');
    }
}
