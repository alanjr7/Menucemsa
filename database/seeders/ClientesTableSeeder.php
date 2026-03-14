<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClientesTableSeeder extends Seeder
{
    public function run(): void
    {
        $clientes = [
            [
                'nombre' => 'Juan Pérez González',
                'telefono' => '555-1234',
                'email' => 'juan.perez@email.com',
                'direccion' => 'Av. Principal #123, Ciudad'
            ],
            [
                'nombre' => 'María García López',
                'telefono' => '555-5678',
                'email' => 'maria.garcia@email.com',
                'direccion' => 'Calle Secundaria #456, Ciudad'
            ],
            [
                'nombre' => 'Carlos Rodríguez Martínez',
                'telefono' => '555-9012',
                'email' => 'carlos.rodriguez@email.com',
                'direccion' => 'Plaza Central #789, Ciudad'
            ],
            [
                'nombre' => 'Ana Martínez Sánchez',
                'telefono' => '555-3456',
                'email' => 'ana.martinez@email.com',
                'direccion' => 'Av. Universidad #101, Ciudad'
            ],
            [
                'nombre' => 'Luis Sánchez Hernández',
                'telefono' => '555-7890',
                'email' => 'luis.sanchez@email.com',
                'direccion' => 'Calle Comercio #202, Ciudad'
            ],
            [
                'nombre' => 'Hospital Central',
                'telefono' => '555-1111',
                'email' => 'compras@hospitalcentral.com',
                'direccion' => 'Av. de la Salud #100, Ciudad'
            ],
            [
                'nombre' => 'Clínica del Norte',
                'telefono' => '555-2222',
                'email' => 'suministros@clinicanorte.com',
                'direccion' => 'Av. Norte #500, Ciudad'
            ],
            [
                'nombre' => 'Centro Médico del Este',
                'telefono' => '555-3333',
                'email' => 'farmacia@centromedicoeste.com',
                'direccion' => 'Calle Este #303, Ciudad'
            ],
            [
                'nombre' => 'Distribuidora Farmacéutica SA',
                'telefono' => '555-4444',
                'email' => 'ventas@distfarmasa.com',
                'direccion' => 'Zona Industrial #404, Ciudad'
            ],
            [
                'nombre' => 'Consultorio Médico Dr. Ruiz',
                'telefono' => '555-5555',
                'email' => 'consultorio@drruiz.com',
                'direccion' => 'Calle Médicos #505, Ciudad'
            ],
            [
                'nombre' => 'Farmacia Vecinal',
                'telefono' => '555-6666',
                'email' => 'contacto@farmaciavecinal.com',
                'direccion' => 'Av. Vecinal #606, Ciudad'
            ],
            [
                'nombre' => 'Clínica Dental Sonrisa',
                'telefono' => '555-7777',
                'email' => 'suministros@clinicadental.com',
                'direccion' => 'Calle Dentistas #707, Ciudad'
            ],
            [
                'nombre' => 'Laboratorio Clínico Análisis',
                'telefono' => '555-8888',
                'email' => 'lab@analisisclinico.com',
                'direccion' => 'Av. Laboratorios #808, Ciudad'
            ],
            [
                'nombre' => 'Centro de Rehabilitación Fisioterapia',
                'telefono' => '555-9999',
                'email' => 'equipos@fisioterapia.com',
                'direccion' => 'Calle Rehabilitación #909, Ciudad'
            ],
            [
                'nombre' => 'Hospital Infantil Niño Feliz',
                'telefono' => '555-0000',
                'email' => 'farmacia@hospitalinfantil.com',
                'direccion' => 'Av. Niños #111, Ciudad'
            ],
        ];

        DB::table('clientes')->insert($clientes);
    }
}
