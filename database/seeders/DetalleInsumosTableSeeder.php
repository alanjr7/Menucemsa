<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DetalleInsumosTableSeeder extends Seeder
{
    public function run(): void
    {
        $detalles = [
            [
                'ID_FARMACIA' => 'F001',
                'CODIGO_INSUMOS' => 'INS001',
                'LABORATORIO' => 'Becton Dickinson',
                'FECHA_VENCIMIENTO' => '2028-12-31',
                'DESCRIPCION' => 'Jeringa estéril unidosis'
            ],
            [
                'ID_FARMACIA' => 'F001',
                'CODIGO_INSUMOS' => 'INS002',
                'LABORATORIO' => 'Becton Dickinson',
                'FECHA_VENCIMIENTO' => '2028-12-31',
                'DESCRIPCION' => 'Jeringa estéril unidosis'
            ],
            [
                'ID_FARMACIA' => 'F002',
                'CODIGO_INSUMOS' => 'INS003',
                'LABORATORIO' => 'Terumo',
                'FECHA_VENCIMIENTO' => '2027-06-30',
                'DESCRIPCION' => 'Aguja para inyección IM'
            ],
            [
                'ID_FARMACIA' => 'F002',
                'CODIGO_INSUMOS' => 'INS004',
                'LABORATORIO' => 'Terumo',
                'FECHA_VENCIMIENTO' => '2027-06-30',
                'DESCRIPCION' => 'Aguja para inyección SC'
            ],
            [
                'ID_FARMACIA' => 'F003',
                'CODIGO_INSUMOS' => 'INS005',
                'LABORATORIO' => 'B Braun',
                'FECHA_VENCIMIENTO' => '2029-03-15',
                'DESCRIPCION' => 'Catéter para venopunción'
            ],
            [
                'ID_FARMACIA' => 'F003',
                'CODIGO_INSUMOS' => 'INS006',
                'LABORATORIO' => 'B Braun',
                'FECHA_VENCIMIENTO' => '2029-03-15',
                'DESCRIPCION' => 'Catéter para venopunción'
            ],
            [
                'ID_FARMACIA' => 'F005',
                'CODIGO_INSUMOS' => 'INS007',
                'LABORATORIO' => 'Semperit',
                'FECHA_VENCIMIENTO' => '2026-08-20',
                'DESCRIPCION' => 'Guantes de examen estériles'
            ],
            [
                'ID_FARMACIA' => 'F005',
                'CODIGO_INSUMOS' => 'INS008',
                'LABORATORIO' => 'Semperit',
                'FECHA_VENCIMIENTO' => '2026-08-20',
                'DESCRIPCION' => 'Guantes de examen estériles'
            ],
            [
                'ID_FARMACIA' => 'F005',
                'CODIGO_INSUMOS' => 'INS009',
                'LABORATORIO' => '3M',
                'FECHA_VENCIMIENTO' => '2025-12-15',
                'DESCRIPCION' => 'Mascarilla quirúrgica desechable'
            ],
            [
                'ID_FARMACIA' => 'F001',
                'CODIGO_INSUMOS' => 'INS010',
                'LABORATORIO' => 'Johnson & Johnson',
                'FECHA_VENCIMIENTO' => '2027-11-30',
                'DESCRIPCION' => 'Gasa estéril para curación'
            ],
            [
                'ID_FARMACIA' => 'F001',
                'CODIGO_INSUMOS' => 'INS012',
                'LABORATORIO' => 'Industrias Químicas',
                'FECHA_VENCIMIENTO' => '2028-02-28',
                'DESCRIPCION' => 'Solución antiséptica'
            ],
            [
                'ID_FARMACIA' => 'F005',
                'CODIGO_INSUMOS' => 'INS014',
                'LABORATORIO' => 'Kimberly-Clark',
                'FECHA_VENCIMIENTO' => '2026-05-15',
                'DESCRIPCION' => 'Bata quirúrgica estéril'
            ],
        ];

        DB::table('DETALLE_INSUMOS')->insert($detalles);
    }
}
