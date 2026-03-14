<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BitacorasTableSeeder extends Seeder
{
    public function run(): void
    {
        $bitacoras = [
            [
                'id' => 'BIT001',
                'accion_realizada' => 'Inicio de sesión',
                'fecha' => '2024-03-14',
                'hora' => '08:00:00',
                'id_usuario' => 1
            ],
            [
                'id' => 'BIT002',
                'accion_realizada' => 'Registro de paciente',
                'fecha' => '2024-03-14',
                'hora' => '08:30:00',
                'id_usuario' => 1
            ],
            [
                'id' => 'BIT003',
                'accion_realizada' => 'Apertura de caja',
                'fecha' => '2024-03-14',
                'hora' => '08:15:00',
                'id_usuario' => 2
            ],
            [
                'id' => 'BIT004',
                'accion_realizada' => 'Venta farmacia',
                'fecha' => '2024-03-14',
                'hora' => '10:30:00',
                'id_usuario' => 2
            ],
            [
                'id' => 'BIT005',
                'accion_realizada' => 'Consulta médica',
                'fecha' => '2024-03-14',
                'hora' => '09:15:00',
                'id_usuario' => 3
            ],
            [
                'id' => 'BIT006',
                'accion_realizada' => 'Programación cirugía',
                'fecha' => '2024-03-14',
                'hora' => '11:45:00',
                'id_usuario' => 1
            ],
            [
                'id' => 'BIT007',
                'accion_realizada' => 'Actualización inventario',
                'fecha' => '2024-03-14',
                'hora' => '14:20:00',
                'id_usuario' => 4
            ],
            [
                'id' => 'BIT008',
                'accion_realizada' => 'Cierre de caja',
                'fecha' => '2024-03-14',
                'hora' => '20:30:00',
                'id_usuario' => 2
            ],
            [
                'id' => 'BIT009',
                'accion_realizada' => 'Registro emergencia',
                'fecha' => '2024-03-13',
                'hora' => '22:15:00',
                'id_usuario' => 5
            ],
            [
                'id' => 'BIT010',
                'accion_realizada' => 'Hospitalización paciente',
                'fecha' => '2024-03-13',
                'hora' => '23:30:00',
                'id_usuario' => 5
            ],
            [
                'id' => 'BIT011',
                'accion_realizada' => 'Administración medicamento',
                'fecha' => '2024-03-14',
                'hora' => '12:00:00',
                'id_usuario' => 6
            ],
            [
                'id' => 'BIT012',
                'accion_realizada' => 'Resultado laboratorio',
                'fecha' => '2024-03-14',
                'hora' => '16:45:00',
                'id_usuario' => 7
            ],
            [
                'id' => 'BIT013',
                'accion_realizada' => 'Alta médica',
                'fecha' => '2024-03-14',
                'hora' => '17:30:00',
                'id_usuario' => 3
            ],
            [
                'id' => 'BIT014',
                'accion_realizada' => 'Facturación servicio',
                'fecha' => '2024-03-14',
                'hora' => '18:00:00',
                'id_usuario' => 8
            ],
            [
                'id' => 'BIT015',
                'accion_realizada' => 'Cierre del día',
                'fecha' => '2024-03-14',
                'hora' => '21:00:00',
                'id_usuario' => 1
            ],
        ];

        DB::table('bitacoras')->insert($bitacoras);
    }
}
