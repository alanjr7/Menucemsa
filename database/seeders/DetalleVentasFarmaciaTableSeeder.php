<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DetalleVentasFarmaciaTableSeeder extends Seeder
{
    public function run(): void
    {
        $detalles = [
            // V001 - Juan Pérez
            [
                'CODIGO_VENTA' => 'V001',
                'CODIGO_PRODUCTO' => 'MED001',
                'TIPO_PRODUCTO' => 'medicamento',
                'NOMBRE_PRODUCTO' => 'Paracetamol 500mg',
                'CANTIDAD' => 3,
                'PRECIO_UNITARIO' => 5.50,
                'SUBTOTAL' => 16.50
            ],
            [
                'CODIGO_VENTA' => 'V001',
                'CODIGO_PRODUCTO' => 'MED004',
                'TIPO_PRODUCTO' => 'medicamento',
                'NOMBRE_PRODUCTO' => 'Omeprazol 20mg',
                'CANTIDAD' => 1,
                'PRECIO_UNITARIO' => 15.60,
                'SUBTOTAL' => 15.60
            ],
            [
                'CODIGO_VENTA' => 'V001',
                'CODIGO_PRODUCTO' => 'INS010',
                'TIPO_PRODUCTO' => 'insumo',
                'NOMBRE_PRODUCTO' => 'Gasa estéril 10x10',
                'CANTIDAD' => 2,
                'PRECIO_UNITARIO' => 6.70,
                'SUBTOTAL' => 13.40
            ],
            // V002 - María García
            [
                'CODIGO_VENTA' => 'V002',
                'CODIGO_PRODUCTO' => 'INS001',
                'TIPO_PRODUCTO' => 'insumo',
                'NOMBRE_PRODUCTO' => 'Jeringa 5ml',
                'CANTIDAD' => 5,
                'PRECIO_UNITARIO' => 2.50,
                'SUBTOTAL' => 12.50
            ],
            [
                'CODIGO_VENTA' => 'V002',
                'CODIGO_PRODUCTO' => 'INS003',
                'TIPO_PRODUCTO' => 'insumo',
                'NOMBRE_PRODUCTO' => 'Aguja 21G',
                'CANTIDAD' => 5,
                'PRECIO_UNITARIO' => 1.25,
                'SUBTOTAL' => 6.25
            ],
            [
                'CODIGO_VENTA' => 'V002',
                'CODIGO_PRODUCTO' => 'INS012',
                'TIPO_PRODUCTO' => 'insumo',
                'NOMBRE_PRODUCTO' => 'Alcohol 70%',
                'CANTIDAD' => 1,
                'PRECIO_UNITARIO' => 10.00,
                'SUBTOTAL' => 10.00
            ],
            // V003 - Carlos Rodríguez
            [
                'CODIGO_VENTA' => 'V003',
                'CODIGO_PRODUCTO' => 'MED003',
                'TIPO_PRODUCTO' => 'medicamento',
                'NOMBRE_PRODUCTO' => 'Amoxicilina 500mg',
                'CANTIDAD' => 2,
                'PRECIO_UNITARIO' => 12.30,
                'SUBTOTAL' => 24.60
            ],
            [
                'CODIGO_VENTA' => 'V003',
                'CODIGO_PRODUCTO' => 'MED011',
                'TIPO_PRODUCTO' => 'medicamento',
                'NOMBRE_PRODUCTO' => 'Metronidazol 500mg',
                'CANTIDAD' => 3,
                'PRECIO_UNITARIO' => 14.30,
                'SUBTOTAL' => 42.90
            ],
            // V004 - Ana Martínez
            [
                'CODIGO_VENTA' => 'V004',
                'CODIGO_PRODUCTO' => 'MED005',
                'TIPO_PRODUCTO' => 'medicamento',
                'NOMBRE_PRODUCTO' => 'Diazepam 10mg',
                'CANTIDAD' => 2,
                'PRECIO_UNITARIO' => 22.40,
                'SUBTOTAL' => 44.80
            ],
            [
                'CODIGO_VENTA' => 'V004',
                'CODIGO_PRODUCTO' => 'MED012',
                'TIPO_PRODUCTO' => 'medicamento',
                'NOMBRE_PRODUCTO' => 'Heparina 5000UI',
                'CANTIDAD' => 1,
                'PRECIO_UNITARIO' => 25.60,
                'SUBTOTAL' => 25.60
            ],
            [
                'CODIGO_VENTA' => 'V004',
                'CODIGO_PRODUCTO' => 'MED013',
                'TIPO_PRODUCTO' => 'medicamento',
                'NOMBRE_PRODUCTO' => 'Atropina 0.5mg',
                'CANTIDAD' => 1,
                'PRECIO_UNITARIO' => 12.80,
                'SUBTOTAL' => 12.80
            ],
            [
                'CODIGO_VENTA' => 'V004',
                'CODIGO_PRODUCTO' => 'MED015',
                'TIPO_PRODUCTO' => 'medicamento',
                'NOMBRE_PRODUCTO' => 'Cloruro de Sodio 0.9%',
                'CANTIDAD' => 1,
                'PRECIO_UNITARIO' => 8.20,
                'SUBTOTAL' => 6.40
            ],
            // V005 - Hospital Central
            [
                'CODIGO_VENTA' => 'V005',
                'CODIGO_PRODUCTO' => 'INS005',
                'TIPO_PRODUCTO' => 'insumo',
                'NOMBRE_PRODUCTO' => 'Catéter IV 18G',
                'CANTIDAD' => 10,
                'PRECIO_UNITARIO' => 8.50,
                'SUBTOTAL' => 85.00
            ],
            [
                'CODIGO_VENTA' => 'V005',
                'CODIGO_PRODUCTO' => 'INS007',
                'TIPO_PRODUCTO' => 'insumo',
                'NOMBRE_PRODUCTO' => 'Guantes latex M',
                'CANTIDAD' => 20,
                'PRECIO_UNITARIO' => 2.50,
                'SUBTOTAL' => 50.00
            ],
            [
                'CODIGO_VENTA' => 'V005',
                'CODIGO_PRODUCTO' => 'INS014',
                'TIPO_PRODUCTO' => 'insumo',
                'NOMBRE_PRODUCTO' => 'Bata quirúrgica',
                'CANTIDAD' => 5,
                'PRECIO_UNITARIO' => 4.26,
                'SUBTOTAL' => 21.30
            ],
            // V006 - Cliente General
            [
                'CODIGO_VENTA' => 'V006',
                'CODIGO_PRODUCTO' => 'INS009',
                'TIPO_PRODUCTO' => 'insumo',
                'NOMBRE_PRODUCTO' => 'Mascarilla quirúrgica',
                'CANTIDAD' => 10,
                'PRECIO_UNITARIO' => 1.24,
                'SUBTOTAL' => 12.40
            ],
            // V007 - Luis Sánchez
            [
                'CODIGO_VENTA' => 'V007',
                'CODIGO_PRODUCTO' => 'MED002',
                'TIPO_PRODUCTO' => 'medicamento',
                'NOMBRE_PRODUCTO' => 'Ibuprofeno 400mg',
                'CANTIDAD' => 2,
                'PRECIO_UNITARIO' => 8.75,
                'SUBTOTAL' => 17.50
            ],
            [
                'CODIGO_VENTA' => 'V007',
                'CODIGO_PRODUCTO' => 'MED010',
                'TIPO_PRODUCTO' => 'medicamento',
                'NOMBRE_PRODUCTO' => 'Ceftriaxona 1g',
                'CANTIDAD' => 1,
                'PRECIO_UNITARIO' => 18.75,
                'SUBTOTAL' => 18.75
            ],
            // V008 - Clínica del Norte
            [
                'CODIGO_VENTA' => 'V008',
                'CODIGO_PRODUCTO' => 'MED006',
                'TIPO_PRODUCTO' => 'medicamento',
                'NOMBRE_PRODUCTO' => 'Lidocaina 2%',
                'CANTIDAD' => 5,
                'PRECIO_UNITARIO' => 18.90,
                'SUBTOTAL' => 94.50
            ],
            [
                'CODIGO_VENTA' => 'V008',
                'CODIGO_PRODUCTO' => 'MED007',
                'TIPO_PRODUCTO' => 'medicamento',
                'NOMBRE_PRODUCTO' => 'Epinefrina 1mg',
                'CANTIDAD' => 3,
                'PRECIO_UNITARIO' => 35.20,
                'SUBTOTAL' => 105.60
            ],
            [
                'CODIGO_VENTA' => 'V008',
                'CODIGO_PRODUCTO' => 'MED008',
                'TIPO_PRODUCTO' => 'medicamento',
                'NOMBRE_PRODUCTO' => 'Fentanilo 50mcg',
                'CANTIDAD' => 2,
                'PRECIO_UNITARIO' => 45.80,
                'SUBTOTAL' => 91.60
            ],
        ];

        DB::table('DETALLE_VENTAS_FARMACIA')->insert($detalles);
    }
}
