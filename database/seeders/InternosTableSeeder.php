<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InternosTableSeeder extends Seeder
{
    public function run(): void
    {
        $internos = [
            [
                'ci' => 98765432,
                'nombre' => 'Roberto Sánchez López',
                'fecha_inicio' => '2024-01-15',
                'fecha_fin' => '2024-06-15',
                'telefono' => 5554321,
                'lugar_asignado' => 'Servicio de Medicina Interna',
                'detalle' => 'Rotación clínica general',
                'estado_formulario' => 'Aprobado',
                'id_usuario_medico' => 21
            ],
            [
                'ci' => 87654321,
                'nombre' => 'Ana Martínez Gómez',
                'fecha_inicio' => '2024-02-01',
                'fecha_fin' => '2024-07-01',
                'telefono' => 5555432,
                'lugar_asignado' => 'Servicio de Pediatría',
                'detalle' => 'Rotación pediátrica',
                'estado_formulario' => 'Aprobado',
                'id_usuario_medico' => 13
            ],
            [
                'ci' => 76543210,
                'nombre' => 'Carlos Rodríguez Díaz',
                'fecha_inicio' => '2024-01-20',
                'fecha_fin' => '2024-04-20',
                'telefono' => 5556543,
                'lugar_asignado' => 'Servicio de Cirugía',
                'detalle' => 'Rotación quirúrgica',
                'estado_formulario' => 'Aprobado',
                'id_usuario_medico' => 9
            ],
            [
                'ci' => 65432109,
                'nombre' => 'María González Pérez',
                'fecha_inicio' => '2024-03-01',
                'fecha_fin' => '2024-08-01',
                'telefono' => 5557654,
                'lugar_asignado' => 'Servicio de Ginecología',
                'detalle' => 'Rotación ginecológica',
                'estado_formulario' => 'Pendiente',
                'id_usuario_medico' => 16
            ],
            [
                'ci' => 54321098,
                'nombre' => 'Luis Hernández Castro',
                'fecha_inicio' => '2024-02-15',
                'fecha_fin' => '2024-05-15',
                'telefono' => 5558765,
                'lugar_asignado' => 'Servicio de Cardiología',
                'detalle' => 'Rotación cardiológica',
                'estado_formulario' => 'Aprobado',
                'id_usuario_medico' => 17
            ],
            [
                'ci' => 43210987,
                'nombre' => 'Sofía Vargas Ruiz',
                'fecha_inicio' => '2024-01-10',
                'fecha_fin' => '2024-07-10',
                'telefono' => 5559876,
                'lugar_asignado' => 'Servicio de Urgencias',
                'detalle' => 'Rotación de emergencias',
                'estado_formulario' => 'Aprobado',
                'id_usuario_medico' => 22
            ],
            [
                'ci' => 32109876,
                'nombre' => 'Diego Morales Silva',
                'fecha_inicio' => '2024-03-15',
                'fecha_fin' => '2024-09-15',
                'telefono' => 5550987,
                'lugar_asignado' => 'Servicio de Ortopedia',
                'detalle' => 'Rotación traumatológica',
                'estado_formulario' => 'En proceso',
                'id_usuario_medico' => 19
            ],
            [
                'ci' => 21098765,
                'nombre' => 'Laura Torres Mendoza',
                'fecha_inicio' => '2024-02-20',
                'fecha_fin' => '2024-06-20',
                'telefono' => 5551234,
                'lugar_asignado' => 'Servicio de Anestesiología',
                'detalle' => 'Rotación anestésica',
                'estado_formulario' => 'Aprobado',
                'id_usuario_medico' => 20
            ],
            [
                'ci' => 10987654,
                'nombre' => 'Pedro Ramírez Guerrero',
                'fecha_inicio' => '2024-01-25',
                'fecha_fin' => '2024-04-25',
                'telefono' => 5552345,
                'lugar_asignado' => 'Servicio de Neurología',
                'detalle' => 'Rotación neurológica',
                'estado_formulario' => 'Aprobado',
                'id_usuario_medico' => 18
            ],
            [
                'ci' => 99887766,
                'nombre' => 'Carmen Ortiz López',
                'fecha_inicio' => '2024-03-10',
                'fecha_fin' => '2024-08-10',
                'telefono' => 5553456,
                'lugar_asignado' => 'Servicio de Dermatología',
                'detalle' => 'Rotación dermatológica',
                'estado_formulario' => 'Pendiente',
                'id_usuario_medico' => 23
            ],
        ];

        DB::table('internos')->insert($internos);
    }
}
