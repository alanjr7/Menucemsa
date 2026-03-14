<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CajaDiariasTableSeeder extends Seeder
{
    public function run(): void
    {
        $cajas = [
            [
                'fecha' => '2024-03-14',
                'monto_inicial' => 500.00,
                'monto_final' => 1890.40,
                'ventas_efectivo' => 650.25,
                'ventas_qr' => 125.50,
                'ventas_transferencia' => 402.00,
                'ventas_tarjeta' => 312.65,
                'total_ventas' => 1490.40,
                'estado' => 'cerrada',
                'usuario_id' => 1,
                'observaciones' => 'Caja cerrada correctamente - día normal',
                'hora_apertura' => '2024-03-14 08:00:00',
                'hora_cierre' => '2024-03-14 20:30:00'
            ],
            [
                'fecha' => '2024-03-13',
                'monto_inicial' => 500.00,
                'monto_final' => 1650.80,
                'ventas_efectivo' => 580.30,
                'ventas_qr' => 95.20,
                'ventas_transferencia' => 375.00,
                'ventas_tarjeta' => 250.30,
                'total_ventas' => 1300.80,
                'estado' => 'cerrada',
                'usuario_id' => 2,
                'observaciones' => 'Caja cerrada - día tranquilo',
                'hora_apertura' => '2024-03-13 08:15:00',
                'hora_cierre' => '2024-03-13 19:45:00'
            ],
            [
                'fecha' => '2024-03-12',
                'monto_inicial' => 500.00,
                'monto_final' => 2100.60,
                'ventas_efectivo' => 890.45,
                'ventas_qr' => 180.75,
                'ventas_transferencia' => 520.20,
                'ventas_tarjeta' => 409.20,
                'total_ventas' => 2000.60,
                'estado' => 'cerrada',
                'usuario_id' => 1,
                'observaciones' => 'Día con alta demanda - stock agotado en varios productos',
                'hora_apertura' => '2024-03-12 07:45:00',
                'hora_cierre' => '2024-03-12 21:00:00'
            ],
            [
                'fecha' => '2024-03-11',
                'monto_inicial' => 500.00,
                'monto_final' => 1450.30,
                'ventas_efectivo' => 420.15,
                'ventas_qr' => 110.50,
                'ventas_transferencia' => 280.40,
                'ventas_tarjeta' => 239.25,
                'total_ventas' => 1050.30,
                'estado' => 'cerrada',
                'usuario_id' => 3,
                'observaciones' => 'Caja cerrada - día festivo',
                'hora_apertura' => '2024-03-11 09:00:00',
                'hora_cierre' => '2024-03-11 18:00:00'
            ],
            [
                'fecha' => '2024-03-15',
                'monto_inicial' => 500.00,
                'monto_final' => null,
                'ventas_efectivo' => 320.50,
                'ventas_qr' => 85.25,
                'ventas_transferencia' => 150.00,
                'ventas_tarjeta' => 125.75,
                'total_ventas' => 681.50,
                'estado' => 'abierta',
                'usuario_id' => 1,
                'observaciones' => 'Caja abierta - en curso',
                'hora_apertura' => '2024-03-15 08:00:00',
                'hora_cierre' => null
            ],
        ];

        DB::table('caja_diarias')->insert($cajas);
    }
}
