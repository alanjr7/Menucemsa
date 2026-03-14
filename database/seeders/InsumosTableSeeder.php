<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InsumosTableSeeder extends Seeder
{
    public function run(): void
    {
        $insumos = [
            ['CODIGO' => 'INS001', 'NOMBRE' => 'Jeringa 5ml', 'DESCRIPCION' => 'Jeringa desechable 5ml'],
            ['CODIGO' => 'INS002', 'NOMBRE' => 'Jeringa 10ml', 'DESCRIPCION' => 'Jeringa desechable 10ml'],
            ['CODIGO' => 'INS003', 'NOMBRE' => 'Aguja 21G', 'DESCRIPCION' => 'Aguja hipodérmica 21G'],
            ['CODIGO' => 'INS004', 'NOMBRE' => 'Aguja 25G', 'DESCRIPCION' => 'Aguja hipodérmica 25G'],
            ['CODIGO' => 'INS005', 'NOMBRE' => 'Catéter IV 18G', 'DESCRIPCION' => 'Catéter intravenoso 18G'],
            ['CODIGO' => 'INS006', 'NOMBRE' => 'Catéter IV 20G', 'DESCRIPCION' => 'Catéter intravenoso 20G'],
            ['CODIGO' => 'INS007', 'NOMBRE' => 'Guantes latex M', 'DESCRIPCION' => 'Guantes de látex talla M'],
            ['CODIGO' => 'INS008', 'NOMBRE' => 'Guantes latex L', 'DESCRIPCION' => 'Guantes de látex talla L'],
            ['CODIGO' => 'INS009', 'NOMBRE' => 'Mascarilla quirúrgica', 'DESCRIPCION' => 'Mascarilla facial desechable'],
            ['CODIGO' => 'INS010', 'NOMBRE' => 'Gasa estéril 10x10', 'DESCRIPCION' => 'Gasa estéril 10x10 cm'],
            ['CODIGO' => 'INS011', 'NOMBRE' => 'Esparadrapo 5cm', 'DESCRIPCION' => 'Esparadrapo adhesivo 5cm'],
            ['CODIGO' => 'INS012', 'NOMBRE' => 'Alcohol 70%', 'DESCRIPCION' => 'Solución antiséptica alcohol 70%'],
            ['CODIGO' => 'INS013', 'NOMBRE' => 'Povidona yodo', 'DESCRIPCION' => 'Solución antiséptica povidona yodo'],
            ['CODIGO' => 'INS014', 'NOMBRE' => 'Bata quirúrgica', 'DESCRIPCION' => 'Bata estéril desechable'],
            ['CODIGO' => 'INS015', 'NOMBRE' => 'Gorro quirúrgico', 'DESCRIPCION' => 'Gorro desechable'],
            ['CODIGO' => 'INS016', 'NOMBRE' => 'Cubrebocas', 'DESCRIPCION' => 'Cubrebocas médico'],
            ['CODIGO' => 'INS017', 'NOMBRE' => 'Campo quirúrgico', 'DESCRIPCION' => 'Campo estéril fenestrado'],
            ['CODIGO' => 'INS018', 'NOMBRE' => 'Sonda vesical', 'DESCRIPCION' => 'Sonda Foley 14Fr'],
            ['CODIGO' => 'INS019', 'NOMBRE' => 'Tubo endotraqueal', 'DESCRIPCION' => 'Tubo endotraqueal 7.5mm'],
            ['CODIGO' => 'INS020', 'NOMBRE' => 'Bolsa de suero', 'DESCRIPCION' => 'Bolsa de solución salina 500ml'],
        ];

        DB::table('INSUMOS')->insert($insumos);
    }
}
