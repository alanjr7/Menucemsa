<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FarmaciaSeeder extends Seeder
{
    public function run()
    {
        // Insertar datos en FARMACIA
        DB::table('FARMACIA')->insert([
            ['ID' => 'F1', 'DETALLE' => 'Farmacia Central'],
            ['ID' => 'F2', 'DETALLE' => 'Farmacia Norte'],
        ]);

        // Insertar datos en MEDICAMENTOS
        DB::table('MEDICAMENTOS')->insert([
            ['CODIGO' => 'M1', 'DESCRIPCION' => 'Paracetamol', 'PRECIO' => 10.00],
            ['CODIGO' => 'M2', 'DESCRIPCION' => 'Ibuprofeno', 'PRECIO' => 15.00],
        ]);

        // Insertar datos en INSUMOS
        DB::table('INSUMOS')->insert([
            ['CODIGO' => 'I1', 'NOMBRE' => 'Guantes', 'DESCRIPCION' => 'Guantes desechables'],
            ['CODIGO' => 'I2', 'NOMBRE' => 'Jeringa', 'DESCRIPCION' => 'Jeringa estéril'],
        ]);

        // Insertar datos en DETALLE_RECETA
        DB::table('DETALLE_RECETA')->insert([
            ['ID_FARMACIA' => 'F1', 'CODIGO_MEDICAMENTOS' => 'M1', 'DOSIS' => '1 cada 8h', 'SUBTOTAL' => 50.00],
            ['ID_FARMACIA' => 'F2', 'CODIGO_MEDICAMENTOS' => 'M2', 'DOSIS' => '1 cada 12h', 'SUBTOTAL' => 30.00],
        ]);

        // Insertar datos en INVENTARIO
        DB::table('INVENTARIO')->insert([
            ['ID' => '1', 'ID_FARMACIA' => 'F1', 'TIPO_ITEM' => 'Medicamento', 'STOCK_MINIMO' => '10', 'STOCK_DISPONIBLE' => '100', 'REPOSICION' => 'No', 'FECHA_INGRESO' => '2026-01-10'],
            ['ID' => '2', 'ID_FARMACIA' => 'F2', 'TIPO_ITEM' => 'Medicamento', 'STOCK_MINIMO' => '15', 'STOCK_DISPONIBLE' => '200', 'REPOSICION' => 'No', 'FECHA_INGRESO' => '2026-01-10'],
        ]);

        // Insertar datos en CAJA_FARMACIA sin referencia a CAJA
        DB::table('CAJA_FARMACIA')->insert([
            ['CODIGO' => 'CF1', 'DETALLE' => 'Caja día 1', 'TOTAL' => 1000.00, 'ID_CAJA' => null],
            ['CODIGO' => 'CF2', 'DETALLE' => 'Caja día 2', 'TOTAL' => 1500.00, 'ID_CAJA' => null],
        ]);
    }
}
