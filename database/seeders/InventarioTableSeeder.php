<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InventarioTableSeeder extends Seeder
{
    public function run(): void
    {
        $inventario = [
            [
                'ID' => 'MED001',
                'ID_FARMACIA' => 'F001',
                'TIPO_ITEM' => 'MEDICAMENTO',
                'STOCK_MINIMO' => '50',
                'STOCK_DISPONIBLE' => '150',
                'REPOSICION' => '100',
                'FECHA_INGRESO' => '2024-01-15'
            ],
            [
                'ID' => 'MED002',
                'ID_FARMACIA' => 'F001',
                'TIPO_ITEM' => 'MEDICAMENTO',
                'STOCK_MINIMO' => '30',
                'STOCK_DISPONIBLE' => '80',
                'REPOSICION' => '50',
                'FECHA_INGRESO' => '2024-01-20'
            ],
            [
                'ID' => 'MED003',
                'ID_FARMACIA' => 'F002',
                'TIPO_ITEM' => 'MEDICAMENTO',
                'STOCK_MINIMO' => '25',
                'STOCK_DISPONIBLE' => '60',
                'REPOSICION' => '40',
                'FECHA_INGRESO' => '2024-02-01'
            ],
            [
                'ID' => 'MED004',
                'ID_FARMACIA' => 'F002',
                'TIPO_ITEM' => 'MEDICAMENTO',
                'STOCK_MINIMO' => '40',
                'STOCK_DISPONIBLE' => '120',
                'REPOSICION' => '80',
                'FECHA_INGRESO' => '2024-02-10'
            ],
            [
                'ID' => 'MED005',
                'ID_FARMACIA' => 'F003',
                'TIPO_ITEM' => 'MEDICAMENTO',
                'STOCK_MINIMO' => '10',
                'STOCK_DISPONIBLE' => '25',
                'REPOSICION' => '15',
                'FECHA_INGRESO' => '2024-02-15'
            ],
            [
                'ID' => 'INS001',
                'ID_FARMACIA' => 'F001',
                'TIPO_ITEM' => 'INSUMO',
                'STOCK_MINIMO' => '200',
                'STOCK_DISPONIBLE' => '500',
                'REPOSICION' => '300',
                'FECHA_INGRESO' => '2024-01-10'
            ],
            [
                'ID' => 'INS002',
                'ID_FARMACIA' => 'F001',
                'TIPO_ITEM' => 'INSUMO',
                'STOCK_MINIMO' => '150',
                'STOCK_DISPONIBLE' => '400',
                'REPOSICION' => '250',
                'FECHA_INGRESO' => '2024-01-12'
            ],
            [
                'ID' => 'INS005',
                'ID_FARMACIA' => 'F003',
                'TIPO_ITEM' => 'INSUMO',
                'STOCK_MINIMO' => '100',
                'STOCK_DISPONIBLE' => '280',
                'REPOSICION' => '180',
                'FECHA_INGRESO' => '2024-02-05'
            ],
            [
                'ID' => 'INS007',
                'ID_FARMACIA' => 'F005',
                'TIPO_ITEM' => 'INSUMO',
                'STOCK_MINIMO' => '500',
                'STOCK_DISPONIBLE' => '1200',
                'REPOSICION' => '700',
                'FECHA_INGRESO' => '2024-01-25'
            ],
            [
                'ID' => 'INS009',
                'ID_FARMACIA' => 'F005',
                'TIPO_ITEM' => 'INSUMO',
                'STOCK_MINIMO' => '1000',
                'STOCK_DISPONIBLE' => '2500',
                'REPOSICION' => '1500',
                'FECHA_INGRESO' => '2024-01-30'
            ],
            [
                'ID' => 'INS010',
                'ID_FARMACIA' => 'F001',
                'TIPO_ITEM' => 'INSUMO',
                'STOCK_MINIMO' => '300',
                'STOCK_DISPONIBLE' => '800',
                'REPOSICION' => '500',
                'FECHA_INGRESO' => '2024-02-08'
            ],
            [
                'ID' => 'MED006',
                'ID_FARMACIA' => 'F005',
                'TIPO_ITEM' => 'MEDICAMENTO',
                'STOCK_MINIMO' => '20',
                'STOCK_DISPONIBLE' => '45',
                'REPOSICION' => '25',
                'FECHA_INGRESO' => '2024-02-20'
            ],
        ];

        DB::table('INVENTARIO')->insert($inventario);
    }
}
