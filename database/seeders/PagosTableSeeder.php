<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PagosTableSeeder extends Seeder
{
    public function run(): void
    {
        $pagos = [
            [
                'nro' => 1,
                'fecha' => '2024-03-14 10:00:00',
                'monto' => 2000.00,
                'concepto' => 'Depósito inicial',
                'estado' => 'Pagado',
                'id_hospitalizacion' => 'HOSP001'
            ],
            [
                'nro' => 2,
                'fecha' => '2024-03-13 23:45:00',
                'monto' => 4500.00,
                'concepto' => 'Pago total parto',
                'estado' => 'Pagado',
                'id_hospitalizacion' => 'HOSP002'
            ],
            [
                'nro' => 3,
                'fecha' => '2024-03-14 11:00:00',
                'monto' => 3000.00,
                'concepto' => 'Depósito UCI',
                'estado' => 'Pagado',
                'id_hospitalizacion' => 'HOSP003'
            ],
            [
                'nro' => 4,
                'fecha' => '2024-03-14 12:00:00',
                'monto' => 1500.00,
                'concepto' => 'Depósito cirugía',
                'estado' => 'Pagado',
                'id_hospitalizacion' => 'HOSP004'
            ],
            [
                'nro' => 5,
                'fecha' => '2024-03-14 15:00:00',
                'monto' => 2500.00,
                'concepto' => 'Depósito traumatología',
                'estado' => 'Pagado',
                'id_hospitalizacion' => 'HOSP005'
            ],
            [
                'nro' => 6,
                'fecha' => '2024-03-14 16:00:00',
                'monto' => 3200.00,
                'concepto' => 'Pago completo neumología',
                'estado' => 'Pagado',
                'id_hospitalizacion' => 'HOSP006'
            ],
            [
                'nro' => 7,
                'fecha' => '2024-03-14 17:00:00',
                'monto' => 3000.00,
                'concepto' => 'Depósito quemados',
                'estado' => 'Pagado',
                'id_hospitalizacion' => 'HOSP007'
            ],
            [
                'nro' => 8,
                'fecha' => '2024-03-14 18:00:00',
                'monto' => 2800.00,
                'concepto' => 'Pago completo cardiología',
                'estado' => 'Pagado',
                'id_hospitalizacion' => 'HOSP008'
            ],
            [
                'nro' => 9,
                'fecha' => '2024-03-14 19:00:00',
                'monto' => 1500.00,
                'concepto' => 'Pago completo alergia',
                'estado' => 'Pagado',
                'id_hospitalizacion' => 'HOSP009'
            ],
            [
                'nro' => 10,
                'fecha' => '2024-03-14 20:00:00',
                'monto' => 1000.00,
                'concepto' => 'Depósito gastroenterología',
                'estado' => 'Pagado',
                'id_hospitalizacion' => 'HOSP010'
            ],
            [
                'nro' => 11,
                'fecha' => '2024-03-14 21:00:00',
                'monto' => 1800.00,
                'concepto' => 'Pago completo pediatría',
                'estado' => 'Pagado',
                'id_hospitalizacion' => 'HOSP011'
            ],
            [
                'nro' => 12,
                'fecha' => '2024-03-14 22:00:00',
                'monto' => 2200.00,
                'concepto' => 'Pago completo neurología',
                'estado' => 'Pagado',
                'id_hospitalizacion' => 'HOSP012'
            ],
            [
                'nro' => 13,
                'fecha' => '2024-03-13 19:00:00',
                'monto' => 5000.00,
                'concepto' => 'Depósito emergencia',
                'estado' => 'Pagado',
                'id_hospitalizacion' => 'HOSP013'
            ],
            [
                'nro' => 14,
                'fecha' => '2024-03-13 20:00:00',
                'monto' => 2000.00,
                'concepto' => 'Depósito nefrología',
                'estado' => 'Pagado',
                'id_hospitalizacion' => 'HOSP014'
            ],
            [
                'nro' => 15,
                'fecha' => '2024-03-13 21:00:00',
                'monto' => 1500.00,
                'concepto' => 'Depósito endocrinología',
                'estado' => 'Pagado',
                'id_hospitalizacion' => 'HOSP015'
            ],
            [
                'nro' => 16,
                'fecha' => '2024-03-15 09:00:00',
                'monto' => 1500.00,
                'concepto' => 'Adicional hospitalización',
                'estado' => 'Pendiente',
                'id_hospitalizacion' => 'HOSP001'
            ],
            [
                'nro' => 17,
                'fecha' => '2024-03-15 10:00:00',
                'monto' => 2000.00,
                'concepto' => 'Adicional UCI',
                'estado' => 'Pendiente',
                'id_hospitalizacion' => 'HOSP003'
            ],
            [
                'nro' => 18,
                'fecha' => '2024-03-15 11:00:00',
                'monto' => 1000.00,
                'concepto' => 'Adicional cirugía',
                'estado' => 'Pendiente',
                'id_hospitalizacion' => 'HOSP004'
            ],
            [
                'nro' => 19,
                'fecha' => '2024-03-15 12:00:00',
                'monto' => 2000.00,
                'concepto' => 'Adicional traumatología',
                'estado' => 'Pendiente',
                'id_hospitalizacion' => 'HOSP005'
            ],
            [
                'nro' => 20,
                'fecha' => '2024-03-15 13:00:00',
                'monto' => 1200.00,
                'concepto' => 'Adicional quemados',
                'estado' => 'Pendiente',
                'id_hospitalizacion' => 'HOSP007'
            ],
        ];

        DB::table('pagos')->insert($pagos);
    }
}
