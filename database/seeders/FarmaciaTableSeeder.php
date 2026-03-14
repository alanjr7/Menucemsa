<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FarmaciaTableSeeder extends Seeder
{
    public function run(): void
    {
        $farmacias = [
            ['ID' => 'F001', 'DETALLE' => 'Farmacia Principal'],
            ['ID' => 'F002', 'DETALLE' => 'Farmacia Emergencia'],
            ['ID' => 'F003', 'DETALLE' => 'Farmacia Piso 1'],
            ['ID' => 'F004', 'DETALLE' => 'Farmacia Piso 2'],
            ['ID' => 'F005', 'DETALLE' => 'Farmacia Quirófano'],
        ];

        DB::table('FARMACIA')->insert($farmacias);
    }
}
