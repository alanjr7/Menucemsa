<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PagoInternosTableSeeder extends Seeder
{
    public function run(): void
    {
        $pagos = [
            [
                'nro' => 'PAG001',
                'detalle' => 'Matrícula interna',
                'saldo_a_pagar' => 5000.00,
                'total_cancelado' => 5000.00,
                'fecha' => '2024-01-15 10:00:00',
                'ci_interno' => 98765432
            ],
            [
                'nro' => 'PAG002',
                'detalle' => 'Matrícula interna',
                'saldo_a_pagar' => 5000.00,
                'total_cancelado' => 2500.00,
                'fecha' => '2024-02-01 14:30:00',
                'ci_interno' => 87654321
            ],
            [
                'nro' => 'PAG003',
                'detalle' => 'Matrícula interna',
                'saldo_a_pagar' => 5000.00,
                'total_cancelado' => 5000.00,
                'fecha' => '2024-01-20 09:15:00',
                'ci_interno' => 76543210
            ],
            [
                'nro' => 'PAG004',
                'detalle' => 'Matrícula interna',
                'saldo_a_pagar' => 5000.00,
                'total_cancelado' => 0.00,
                'fecha' => '2024-03-01 11:00:00',
                'ci_interno' => 65432109
            ],
            [
                'nro' => 'PAG005',
                'detalle' => 'Matrícula interna',
                'saldo_a_pagar' => 5000.00,
                'total_cancelado' => 5000.00,
                'fecha' => '2024-02-15 16:45:00',
                'ci_interno' => 54321098
            ],
            [
                'nro' => 'PAG006',
                'detalle' => 'Matrícula interna',
                'saldo_a_pagar' => 5000.00,
                'total_cancelado' => 5000.00,
                'fecha' => '2024-01-10 08:30:00',
                'ci_interno' => 43210987
            ],
            [
                'nro' => 'PAG007',
                'detalle' => 'Matrícula interna',
                'saldo_a_pagar' => 5000.00,
                'total_cancelado' => 3000.00,
                'fecha' => '2024-03-15 13:20:00',
                'ci_interno' => 32109876
            ],
            [
                'nro' => 'PAG008',
                'detalle' => 'Matrícula interna',
                'saldo_a_pagar' => 5000.00,
                'total_cancelado' => 5000.00,
                'fecha' => '2024-02-20 10:45:00',
                'ci_interno' => 21098765
            ],
            [
                'nro' => 'PAG009',
                'detalle' => 'Matrícula interna',
                'saldo_a_pagar' => 5000.00,
                'total_cancelado' => 5000.00,
                'fecha' => '2024-01-25 15:30:00',
                'ci_interno' => 10987654
            ],
            [
                'nro' => 'PAG010',
                'detalle' => 'Matrícula interna',
                'saldo_a_pagar' => 5000.00,
                'total_cancelado' => 1000.00,
                'fecha' => '2024-03-10 12:15:00',
                'ci_interno' => 99887766
            ],
            [
                'nro' => 'PAG011',
                'detalle' => 'Cuota mensual',
                'saldo_a_pagar' => 500.00,
                'total_cancelado' => 500.00,
                'fecha' => '2024-03-01 09:00:00',
                'ci_interno' => 98765432
            ],
            [
                'nro' => 'PAG012',
                'detalle' => 'Cuota mensual',
                'saldo_a_pagar' => 500.00,
                'total_cancelado' => 250.00,
                'fecha' => '2024-03-05 14:20:00',
                'ci_interno' => 87654321
            ],
            [
                'nro' => 'PAG013',
                'detalle' => 'Cuota mensual',
                'saldo_a_pagar' => 500.00,
                'total_cancelado' => 500.00,
                'fecha' => '2024-03-01 11:30:00',
                'ci_interno' => 76543210
            ],
            [
                'nro' => 'PAG014',
                'detalle' => 'Cuota mensual',
                'saldo_a_pagar' => 500.00,
                'total_cancelado' => 0.00,
                'fecha' => '2024-03-10 16:00:00',
                'ci_interno' => 65432109
            ],
            [
                'nro' => 'PAG015',
                'detalle' => 'Cuota mensual',
                'saldo_a_pagar' => 500.00,
                'total_cancelado' => 500.00,
                'fecha' => '2024-03-01 08:45:00',
                'ci_interno' => 54321098
            ],
        ];

        DB::table('pago_internos')->insert($pagos);
    }
}
