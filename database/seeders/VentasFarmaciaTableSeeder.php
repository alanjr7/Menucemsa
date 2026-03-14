<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VentasFarmaciaTableSeeder extends Seeder
{
    public function run(): void
    {
        $ventas = [
            [
                'CODIGO_VENTA' => 'V001',
                'ID_FARMACIA' => 'F001',
                'CLIENTE' => 'Juan Pérez',
                'TOTAL' => 45.50,
                'METODO_PAGO' => 'EFECTIVO',
                'REQUIERE_RECETA' => true,
                'FECHA_VENTA' => '2024-03-14 10:30:00',
                'ESTADO' => 'COMPLETADA',
                'OBSERVACIONES' => 'Cliente con receta médica'
            ],
            [
                'CODIGO_VENTA' => 'V002',
                'ID_FARMACIA' => 'F001',
                'CLIENTE' => 'María García',
                'TOTAL' => 28.75,
                'METODO_PAGO' => 'TARJETA',
                'REQUIERE_RECETA' => false,
                'FECHA_VENTA' => '2024-03-14 11:15:00',
                'ESTADO' => 'COMPLETADA',
                'OBSERVACIONES' => 'Venta de productos sin receta'
            ],
            [
                'CODIGO_VENTA' => 'V003',
                'ID_FARMACIA' => 'F002',
                'CLIENTE' => 'Carlos Rodríguez',
                'TOTAL' => 67.80,
                'METODO_PAGO' => 'EFECTIVO',
                'REQUIERE_RECETA' => true,
                'FECHA_VENTA' => '2024-03-14 14:20:00',
                'ESTADO' => 'COMPLETADA',
                'OBSERVACIONES' => 'Antibióticos con receta'
            ],
            [
                'CODIGO_VENTA' => 'V004',
                'ID_FARMACIA' => 'F003',
                'CLIENTE' => 'Ana Martínez',
                'TOTAL' => 89.60,
                'METODO_PAGO' => 'TRANSFERENCIA',
                'REQUIERE_RECETA' => true,
                'FECHA_VENTA' => '2024-03-14 15:45:00',
                'ESTADO' => 'COMPLETADA',
                'OBSERVACIONES' => 'Medicamentos controlados'
            ],
            [
                'CODIGO_VENTA' => 'V005',
                'ID_FARMACIA' => 'F005',
                'CLIENTE' => 'Hospital Central',
                'TOTAL' => 156.30,
                'METODO_PAGO' => 'CUENTA_CORRIENTE',
                'REQUIERE_RECETA' => false,
                'FECHA_VENTA' => '2024-03-14 16:30:00',
                'ESTADO' => 'COMPLETADA',
                'OBSERVACIONES' => 'Insumos quirúrgicos para hospital'
            ],
            [
                'CODIGO_VENTA' => 'V006',
                'ID_FARMACIA' => 'F001',
                'CLIENTE' => 'Cliente General',
                'TOTAL' => 12.40,
                'METODO_PAGO' => 'EFECTIVO',
                'REQUIERE_RECETA' => false,
                'FECHA_VENTA' => '2024-03-14 17:10:00',
                'ESTADO' => 'COMPLETADA',
                'OBSERVACIONES' => 'Venta menor de productos básicos'
            ],
            [
                'CODIGO_VENTA' => 'V007',
                'ID_FARMACIA' => 'F002',
                'CLIENTE' => 'Luis Sánchez',
                'TOTAL' => 34.90,
                'METODO_PAGO' => 'TARJETA',
                'REQUIERE_RECETA' => true,
                'FECHA_VENTA' => '2024-03-14 18:25:00',
                'ESTADO' => 'COMPLETADA',
                'OBSERVACIONES' => 'Venta con receta médica válida'
            ],
            [
                'CODIGO_VENTA' => 'V008',
                'ID_FARMACIA' => 'F005',
                'CLIENTE' => 'Clínica del Norte',
                'TOTAL' => 245.70,
                'METODO_PAGO' => 'CUENTA_CORRIENTE',
                'REQUIERE_RECETA' => false,
                'FECHA_VENTA' => '2024-03-14 19:15:00',
                'ESTADO' => 'COMPLETADA',
                'OBSERVACIONES' => 'Suministro mensual para clínica'
            ],
        ];

        DB::table('VENTAS_FARMACIA')->insert($ventas);
    }
}
