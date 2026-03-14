<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmergenciasTableSeeder extends Seeder
{
    public function run(): void
    {
        $emergencias = [
            [
                'nro' => 'EM001',
                'descripcion' => 'Paro cardiorrespiratorio',
                'estado' => 'Atendido',
                'tipo' => 'Crítica',
                'id_triage' => 'TRI001'
            ],
            [
                'nro' => 'EM002',
                'descripcion' => 'Trauma severo por accidente',
                'estado' => 'En tratamiento',
                'tipo' => 'Grave',
                'id_triage' => 'TRI002'
            ],
            [
                'nro' => 'EM003',
                'descripcion' => 'Dolor torácico agudo',
                'estado' => 'En observación',
                'tipo' => 'Urgente',
                'id_triage' => 'TRI002'
            ],
            [
                'nro' => 'EM004',
                'descripcion' => 'Fractura expuesta',
                'estado' => 'Atendido',
                'tipo' => 'Grave',
                'id_triage' => 'TRI002'
            ],
            [
                'nro' => 'EM005',
                'descripcion' => 'Hemorragia digestiva',
                'estado' => 'En tratamiento',
                'tipo' => 'Grave',
                'id_triage' => 'TRI002'
            ],
            [
                'nro' => 'EM006',
                'descripcion' => 'Crisis asmática severa',
                'estado' => 'Estabilizado',
                'tipo' => 'Urgente',
                'id_triage' => 'TRI002'
            ],
            [
                'nro' => 'EM007',
                'descripcion' => 'Dolor abdominal agudo',
                'estado' => 'En evaluación',
                'tipo' => 'Urgente',
                'id_triage' => 'TRI003'
            ],
            [
                'nro' => 'EM008',
                'descripcion' => 'Herida por arma blanca',
                'estado' => 'Atendido',
                'tipo' => 'Moderado',
                'id_triage' => 'TRI003'
            ],
            [
                'nro' => 'EM009',
                'descripcion' => 'Quemaduras de segundo grado',
                'estado' => 'En tratamiento',
                'tipo' => 'Moderado',
                'id_triage' => 'TRI003'
            ],
            [
                'nro' => 'EM010',
                'descripcion' => 'Intoxicación alimentaria',
                'estado' => 'En observación',
                'tipo' => 'Leve',
                'id_triage' => 'TRI004'
            ],
            [
                'nro' => 'EM011',
                'descripcion' => 'Migraña severa',
                'estado' => 'Atendido',
                'tipo' => 'Leve',
                'id_triage' => 'TRI004'
            ],
            [
                'nro' => 'EM012',
                'descripcion' => 'Esguince de tobillo',
                'estado' => 'Atendido',
                'tipo' => 'Leve',
                'id_triage' => 'TRI004'
            ],
            [
                'nro' => 'EM013',
                'descripcion' => 'Crisis hipertensiva',
                'estado' => 'Estabilizado',
                'tipo' => 'Urgente',
                'id_triage' => 'TRI003'
            ],
            [
                'nro' => 'EM014',
                'descripcion' => 'Convulsión febril',
                'estado' => 'Atendido',
                'tipo' => 'Moderado',
                'id_triage' => 'TRI003'
            ],
            [
                'nro' => 'EM015',
                'descripcion' => 'Cuerpo extraño en ojo',
                'estado' => 'Atendido',
                'tipo' => 'Leve',
                'id_triage' => 'TRI004'
            ],
            [
                'nro' => 'EM016',
                'descripcion' => 'Desmayo síncope',
                'estado' => 'En evaluación',
                'tipo' => 'Moderado',
                'id_triage' => 'TRI003'
            ],
            [
                'nro' => 'EM017',
                'descripcion' => 'Reacción alérgica aguda',
                'estado' => 'Atendido',
                'tipo' => 'Urgente',
                'id_triage' => 'TRI002'
            ],
            [
                'nro' => 'EM018',
                'descripcion' => 'Dolor lumbar agudo',
                'estado' => 'En tratamiento',
                'tipo' => 'Leve',
                'id_triage' => 'TRI004'
            ],
            [
                'nro' => 'EM019',
                'descripcion' => 'Parto de emergencia',
                'estado' => 'Atendido',
                'tipo' => 'Grave',
                'id_triage' => 'TRI002'
            ],
            [
                'nro' => 'EM020',
                'descripcion' => 'Ataque de pánico',
                'estado' => 'Atendido',
                'tipo' => 'Leve',
                'id_triage' => 'TRI004'
            ],
        ];

        DB::table('emergencias')->insert($emergencias);
    }
}
