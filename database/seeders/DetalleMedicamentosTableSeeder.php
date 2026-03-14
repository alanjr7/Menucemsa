<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DetalleMedicamentosTableSeeder extends Seeder
{
    public function run(): void
    {
        $detalles = [
            [
                'ID_FARMACIA' => 'F001',
                'CODIGO_MEDICAMENTOS' => 'MED001',
                'LABORATORIO' => 'Bayer',
                'FECHA_VENCIMIENTO' => '2025-12-31',
                'TIPO' => 'Analgésico',
                'REQUERIMIENTO' => 'Receta médica'
            ],
            [
                'ID_FARMACIA' => 'F001',
                'CODIGO_MEDICAMENTOS' => 'MED002',
                'LABORATORIO' => 'Pfizer',
                'FECHA_VENCIMIENTO' => '2026-06-30',
                'TIPO' => 'Antiinflamatorio',
                'REQUERIMIENTO' => 'Receta médica'
            ],
            [
                'ID_FARMACIA' => 'F002',
                'CODIGO_MEDICAMENTOS' => 'MED003',
                'LABORATORIO' => 'GSK',
                'FECHA_VENCIMIENTO' => '2025-08-15',
                'TIPO' => 'Antibiótico',
                'REQUERIMIENTO' => 'Receta médica'
            ],
            [
                'ID_FARMACIA' => 'F002',
                'CODIGO_MEDICAMENTOS' => 'MED004',
                'LABORATORIO' => 'AstraZeneca',
                'FECHA_VENCIMIENTO' => '2026-03-20',
                'TIPO' => 'Antiulceroso',
                'REQUERIMIENTO' => 'Receta médica'
            ],
            [
                'ID_FARMACIA' => 'F003',
                'CODIGO_MEDICAMENTOS' => 'MED005',
                'LABORATORIO' => 'Roche',
                'FECHA_VENCIMIENTO' => '2025-11-10',
                'TIPO' => 'Ansiolítico',
                'REQUERIMIENTO' => 'Controlado'
            ],
            [
                'ID_FARMACIA' => 'F005',
                'CODIGO_MEDICAMENTOS' => 'MED006',
                'LABORATORIO' => 'Hospira',
                'FECHA_VENCIMIENTO' => '2026-01-15',
                'TIPO' => 'Anestésico local',
                'REQUERIMIENTO' => 'Uso quirúrgico'
            ],
            [
                'ID_FARMACIA' => 'F005',
                'CODIGO_MEDICAMENTOS' => 'MED007',
                'LABORATORIO' => 'Abbott',
                'FECHA_VENCIMIENTO' => '2025-09-30',
                'TIPO' => 'Vasoconstrictor',
                'REQUERIMIENTO' => 'Uso emergencia'
            ],
            [
                'ID_FARMACIA' => 'F005',
                'CODIGO_MEDICAMENTOS' => 'MED008',
                'LABORATORIO' => 'Janssen',
                'FECHA_VENCIMIENTO' => '2026-02-28',
                'TIPO' => 'Opioide',
                'REQUERIMIENTO' => 'Controlado'
            ],
            [
                'ID_FARMACIA' => 'F005',
                'CODIGO_MEDICAMENTOS' => 'MED009',
                'LABORATORIO' => 'Fresenius',
                'FECHA_VENCIMIENTO' => '2025-10-20',
                'TIPO' => 'Anestésico IV',
                'REQUERIMIENTO' => 'Uso quirúrgico'
            ],
            [
                'ID_FARMACIA' => 'F001',
                'CODIGO_MEDICAMENTOS' => 'MED010',
                'LABORATORIO' => 'Roche',
                'FECHA_VENCIMIENTO' => '2026-04-15',
                'TIPO' => 'Antibiótico',
                'REQUERIMIENTO' => 'Receta médica'
            ],
        ];

        DB::table('DETALLE_MEDICAMENTOS')->insert($detalles);
    }
}
