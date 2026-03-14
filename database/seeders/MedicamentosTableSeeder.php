<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MedicamentosTableSeeder extends Seeder
{
    public function run(): void
    {
        $medicamentos = [
            ['CODIGO' => 'MED001', 'DESCRIPCION' => 'Paracetamol 500mg', 'PRECIO' => 5.50],
            ['CODIGO' => 'MED002', 'DESCRIPCION' => 'Ibuprofeno 400mg', 'PRECIO' => 8.75],
            ['CODIGO' => 'MED003', 'DESCRIPCION' => 'Amoxicilina 500mg', 'PRECIO' => 12.30],
            ['CODIGO' => 'MED004', 'DESCRIPCION' => 'Omeprazol 20mg', 'PRECIO' => 15.60],
            ['CODIGO' => 'MED005', 'DESCRIPCION' => 'Diazepam 10mg', 'PRECIO' => 22.40],
            ['CODIGO' => 'MED006', 'DESCRIPCION' => 'Lidocaina 2%', 'PRECIO' => 18.90],
            ['CODIGO' => 'MED007', 'DESCRIPCION' => 'Epinefrina 1mg', 'PRECIO' => 35.20],
            ['CODIGO' => 'MED008', 'DESCRIPCION' => 'Fentanilo 50mcg', 'PRECIO' => 45.80],
            ['CODIGO' => 'MED009', 'DESCRIPCION' => 'Propofol 10mg', 'PRECIO' => 28.50],
            ['CODIGO' => 'MED010', 'DESCRIPCION' => 'Ceftriaxona 1g', 'PRECIO' => 18.75],
            ['CODIGO' => 'MED011', 'DESCRIPCION' => 'Metronidazol 500mg', 'PRECIO' => 14.30],
            ['CODIGO' => 'MED012', 'DESCRIPCION' => 'Heparina 5000UI', 'PRECIO' => 25.60],
            ['CODIGO' => 'MED013', 'DESCRIPCION' => 'Atropina 0.5mg', 'PRECIO' => 12.80],
            ['CODIGO' => 'MED014', 'DESCRIPCION' => 'Adrenalina 1mg', 'PRECIO' => 30.40],
            ['CODIGO' => 'MED015', 'DESCRIPCION' => 'Cloruro de Sodio 0.9%', 'PRECIO' => 8.20],
        ];

        DB::table('MEDICAMENTOS')->insert($medicamentos);
    }
}
