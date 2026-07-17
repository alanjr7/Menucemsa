<?php

namespace Database\Seeders;

use App\Models\Cama;
use App\Models\Habitacion;
use Illuminate\Database\Seeder;

class HabitacionSeeder extends Seeder
{
    public function run(): void
    {
        $habitaciones = [
            [
                'id'        => 'HAB-101',
                'detalle'   => 'Habitación Individual - Planta Baja',
                'capacidad' => 1,
                'camas'     => [
                    ['nro' => 1, 'tipo' => 'Cama Standard', 'precio_por_dia' => 180.00],
                ],
            ],
            [
                'id'        => 'HAB-102',
                'detalle'   => 'Habitación Individual - Planta Baja',
                'capacidad' => 1,
                'camas'     => [
                    ['nro' => 1, 'tipo' => 'Cama Standard', 'precio_por_dia' => 180.00],
                ],
            ],
            [
                'id'        => 'HAB-103',
                'detalle'   => 'Habitación Doble - Planta Baja',
                'capacidad' => 2,
                'camas'     => [
                    ['nro' => 1, 'tipo' => 'Cama Standard', 'precio_por_dia' => 150.00],
                    ['nro' => 2, 'tipo' => 'Cama Standard', 'precio_por_dia' => 150.00],
                ],
            ],
            [
                'id'        => 'HAB-201',
                'detalle'   => 'Habitación Individual - Segundo Piso',
                'capacidad' => 1,
                'camas'     => [
                    ['nro' => 1, 'tipo' => 'Cama Premium', 'precio_por_dia' => 250.00],
                ],
            ],
            [
                'id'        => 'HAB-202',
                'detalle'   => 'Habitación Individual - Segundo Piso',
                'capacidad' => 1,
                'camas'     => [
                    ['nro' => 1, 'tipo' => 'Cama Premium', 'precio_por_dia' => 250.00],
                ],
            ],
            [
                'id'        => 'HAB-203',
                'detalle'   => 'Habitación Doble - Segundo Piso',
                'capacidad' => 2,
                'camas'     => [
                    ['nro' => 1, 'tipo' => 'Cama Standard', 'precio_por_dia' => 160.00],
                    ['nro' => 2, 'tipo' => 'Cama Standard', 'precio_por_dia' => 160.00],
                ],
            ],
            [
                'id'        => 'HAB-301',
                'detalle'   => 'Suite Familiar - Tercer Piso',
                'capacidad' => 3,
                'camas'     => [
                    ['nro' => 1, 'tipo' => 'Cama Matrimonial', 'precio_por_dia' => 300.00],
                    ['nro' => 2, 'tipo' => 'Cama Standard', 'precio_por_dia' => 120.00],
                    ['nro' => 3, 'tipo' => 'Cama Standard', 'precio_por_dia' => 120.00],
                ],
            ],
            [
                'id'        => 'HAB-302',
                'detalle'   => 'Habitación Triple - Tercer Piso',
                'capacidad' => 3,
                'camas'     => [
                    ['nro' => 1, 'tipo' => 'Cama Standard', 'precio_por_dia' => 140.00],
                    ['nro' => 2, 'tipo' => 'Cama Standard', 'precio_por_dia' => 140.00],
                    ['nro' => 3, 'tipo' => 'Cama Standard', 'precio_por_dia' => 140.00],
                ],
            ],
            [
                'id'        => 'HAB-401',
                'detalle'   => 'Habitación Individual VIP - Cuarto Piso',
                'capacidad' => 1,
                'camas'     => [
                    ['nro' => 1, 'tipo' => 'Cama Eléctrica', 'precio_por_dia' => 450.00],
                ],
            ],
            [
                'id'        => 'HAB-402',
                'detalle'   => 'Habitación Doble VIP - Cuarto Piso',
                'capacidad' => 2,
                'camas'     => [
                    ['nro' => 1, 'tipo' => 'Cama Eléctrica', 'precio_por_dia' => 400.00],
                    ['nro' => 2, 'tipo' => 'Cama Standard', 'precio_por_dia' => 200.00],
                ],
            ],
        ];

        foreach ($habitaciones as $habitacionData) {
            $camas = $habitacionData['camas'];
            unset($habitacionData['camas']);

            $habitacion = Habitacion::firstOrCreate(
                ['id' => $habitacionData['id']],
                [
                    'detalle'   => $habitacionData['detalle'],
                    'capacidad' => $habitacionData['capacidad'],
                    'estado'    => 'disponible',
                ]
            );

            foreach ($camas as $cama) {
                Cama::firstOrCreate(
                    ['habitacion_id' => $habitacion->id, 'nro' => $cama['nro']],
                    [
                        'tipo'           => $cama['tipo'],
                        'precio_por_dia' => $cama['precio_por_dia'],
                        'disponibilidad' => 'disponible',
                    ]
                );
            }
        }
    }
}
